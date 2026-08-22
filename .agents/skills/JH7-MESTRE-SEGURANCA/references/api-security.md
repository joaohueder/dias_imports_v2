# Segurança de APIs

Aplica-se a REST, RPC, GraphQL, WebSocket, Edge Functions e webhooks.

---

## 1. Checklist por endpoint

Todo endpoint novo responde a estas perguntas antes de subir:

| # | Item | Pergunta |
| --- | --- | --- |
| 1 | Autenticação | exige identidade? qual mecanismo? |
| 2 | Autorização | papel, permissão e **propriedade do recurso** conferidos no servidor? |
| 3 | Tenant | o escopo da empresa vem da sessão, nunca do payload? |
| 4 | Validação | schema de entrada com tipo, tamanho, formato e allowlist de campos? |
| 5 | Método | só os verbos necessários? `GET` sem efeito colateral? |
| 6 | Payload | limite de tamanho definido? |
| 7 | Resposta | devolve só os campos necessários? |
| 8 | Erros | mensagem genérica, sem stack trace nem SQL? |
| 9 | Rate limit | protegido contra abuso e força bruta? |
| 10 | Timeout | consulta e chamada externa com prazo máximo? |
| 11 | Log | operação crítica auditada (quem, quando, o quê)? |
| 12 | Idempotência | repetição acidental duplica efeito? |

---

## 2. Exposição excessiva de dados

O erro mais comum e mais silencioso: o backend devolve o registro inteiro e o frontend só mostra parte.

```javascript
// ❌ devolve hash de senha, tokens, flags internas, company_id de terceiros
return res.json(user);

// ✅ projeção explícita
return res.json({ id: user.id, name: user.name, email: user.email });
```

Aplicar também em:

- `select *` em query e em RPC;
- relacionamento aninhado (`include`/`select` de ORM traz o objeto pai completo);
- erro de validação que ecoa o payload recebido;
- listagem que devolve campos usados só na tela de detalhe;
- `count` e agregados que revelam volume de outros tenants.

Em PostgREST/Supabase: preferir **view** com as colunas públicas a expor a tabela inteira, e revisar o que a policy de `select` realmente libera.

---

## 3. Validação de entrada

Schema declarado (zod, yup, valibot, JSON Schema, pydantic) na borda, antes de qualquer lógica:

```typescript
const schema = z.object({
  name: z.string().trim().min(1).max(120),
  price: z.number().positive().max(1_000_000),
  status: z.enum(['ativo', 'inativo']),        // allowlist
}).strict();                                   // rejeita campo desconhecido
```

`.strict()` (ou equivalente) mata mass assignment na origem.

Regras adicionais:

- coagir tipo explicitamente — `"1"` não é `1`, `"true"` não é `true`;
- normalizar antes de validar (trim, lowercase de e-mail, NFC em unicode);
- validar UUID/ID por formato antes de ir ao banco;
- limitar tamanho de array e profundidade de objeto;
- validar de novo no serviço quando ele for chamado por mais de uma porta (HTTP, fila, cron).

---

## 4. Métodos, cabeçalhos e CORS

### Métodos

`GET` e `HEAD` são seguros e não alteram estado. Operação com efeito usa `POST`/`PUT`/`PATCH`/`DELETE`. Método não previsto responde `405`.

### CORS

```text
❌ Access-Control-Allow-Origin: *  em API privada
❌ Allow-Origin refletindo o header Origin recebido
❌ Allow-Credentials: true com Allow-Origin dinâmico e sem allowlist
✅ allowlist explícita de origens, por ambiente
```

Wildcard só em API realmente pública, sem credencial e sem dado privado. Preflight deve limitar `Allow-Methods` e `Allow-Headers` ao necessário.

### Headers de resposta

| Header | Valor de partida |
| --- | --- |
| `Content-Security-Policy` | `default-src 'self'` + ajustes mínimos; evitar `unsafe-inline` |
| `Strict-Transport-Security` | `max-age=63072000; includeSubDomains` (preload após validar) |
| `X-Content-Type-Options` | `nosniff` |
| `Referrer-Policy` | `strict-origin-when-cross-origin` |
| `Permissions-Policy` | negar o que não usa: `camera=(), microphone=(), geolocation=()` |
| `Cache-Control` | `no-store` em resposta autenticada |
| `Content-Type` | sempre explícito; JSON como `application/json` |

Remover header que revela stack (`X-Powered-By`, versão do servidor).

---

## 5. Rate limiting

| Endpoint | Sugestão de partida |
| --- | --- |
| login | 5–10 tentativas / 15 min por conta **e** por IP |
| reset de senha | 3–5 / hora por e-mail e por IP |
| cadastro | limitar por IP + verificação de e-mail |
| OTP / envio de código | 3 / 10 min, com cooldown crescente |
| busca custosa / relatório | limite por usuário + fila |
| upload | limite por usuário + tamanho máximo |
| endpoint de IA | limite por usuário e orçamento por tenant |
| API pública | por API key, com cota diária |

Chave de contagem: usuário autenticado quando houver; senão IP normalizado (cuidado com `X-Forwarded-For` — confiar apenas no valor injetado pelo próprio proxy da borda).

Resposta `429` com `Retry-After`. Contagem em store compartilhado (Redis/banco) — contador em memória não funciona com múltiplas instâncias.

---

## 6. Webhooks

### Recebendo

```text
1. verificar assinatura HMAC do corpo BRUTO (antes de qualquer parse)
2. comparar em tempo constante
3. validar timestamp (janela de ~5 min) → anti-replay
4. registrar o event id e descartar duplicata → idempotência
5. validar o schema do payload (é dado externo, não é verdade)
6. responder rápido (2xx) e processar em fila
7. nunca confiar em valores de negócio do payload sem reconsultar a origem
```

O corpo bruto importa: reserializar JSON quebra a assinatura.

```javascript
const esperado = crypto.createHmac('sha256', secret).update(rawBody).digest('hex');
if (!crypto.timingSafeEqual(Buffer.from(esperado), Buffer.from(recebido))) return res.status(401).end();
```

Quando o provedor não assina: restringir por IP de origem se ele publicar a faixa, usar URL com segredo de alta entropia, e **sempre** reconsultar o recurso na API do provedor antes de agir sobre valor financeiro.

### Enviando

Assinar com HMAC, incluir timestamp e ID do evento, usar HTTPS, validar a URL de destino contra SSRF (o cliente escolhe o destino), timeout curto, retry com backoff, e nunca enviar dado sensível desnecessário no payload.

---

## 7. GraphQL

- desativar introspecção em produção quando o schema não for público;
- limitar profundidade e complexidade da query;
- limite de custo por operação e por usuário;
- autorização por **campo/resolver**, não só na raiz — o grafo permite chegar ao dado por outro caminho;
- desativar batching ilimitado (aliases repetidos viram amplificação de brute force);
- erro sem stack trace; `formatError` filtrado.

---

## 8. WebSocket / Realtime

- autenticar no handshake e revalidar em reconexão;
- autorizar **por canal/tópico**: assinar `company:123` exige provar vínculo com 123 no servidor;
- não confiar em mensagem que diz de quem é — derivar do socket autenticado;
- limitar taxa de mensagens e tamanho do frame;
- expirar conexão quando o token expira ou a sessão é revogada;
- validar `Origin` no upgrade.

Em Supabase Realtime, o que protege o canal de tabela é a **RLS** da tabela publicada — conferir se `replica identity` e policies não expõem linha de outro tenant.

---

## 9. Edge Functions / serverless

- secret por variável de ambiente da plataforma, nunca no arquivo;
- `service_role` só dentro da função, jamais devolvido ao cliente nem embutido no bundle do frontend;
- validar JWT do chamador dentro da função quando ela usa credencial privilegiada — função com `service_role` e sem verificação de identidade é bypass total de RLS;
- limitar tempo de execução e tamanho do payload;
- CORS explícito por função;
- log sem secret e sem PII desnecessária.

```typescript
// ✅ padrão seguro para função com service_role
const jwt = req.headers.get('Authorization')?.replace('Bearer ', '');
const { data: { user }, error } = await anonClient.auth.getUser(jwt);
if (error || !user) return json({ error: 'não autorizado' }, 401);
// somente aqui usar o client com service_role, já sabendo quem é o usuário
// e sempre filtrando pelo tenant obtido no servidor
```

---

## 10. Erros

```javascript
// ❌
res.status(500).json({ error: err.message, stack: err.stack });

// ✅
logger.error({ err, requestId, userId });               // detalhe no log
res.status(500).json({ error: 'Erro interno', requestId }); // genérico para o cliente
```

Padronizar códigos: `400` validação, `401` sem identidade, `403` sem permissão, `404` inexistente ou fora do escopo, `409` conflito, `422` semântica, `429` limite, `500` interno. Nunca devolver mensagem do banco.
