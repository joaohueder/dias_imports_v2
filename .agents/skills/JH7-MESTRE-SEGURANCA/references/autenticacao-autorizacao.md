# Autenticação e autorização

---

## 1. Senhas

### Armazenamento

Nunca texto puro. Nunca MD5, SHA1, SHA256 "puro" — hash rápido é o problema, não a solução.

Algoritmos aceitáveis, em ordem de preferência:

| Algoritmo | Parâmetros de partida |
| --- | --- |
| **Argon2id** | memória ≥ 19 MiB, iterações ≥ 2, paralelismo 1 (recomendação OWASP) |
| **scrypt** | N ≥ 2^17, r = 8, p = 1 |
| **bcrypt** | cost ≥ 12; senha truncada em 72 bytes (pré-hash com SHA-256 + base64 se aceitar senha longa) |
| **PBKDF2-HMAC-SHA256** | ≥ 600.000 iterações (usar só quando exigido por compliance/FIPS) |

Parâmetros envelhecem. **Confirmar na OWASP Password Storage Cheat Sheet atual antes de fixar valores.**

Salt é por senha e vem do próprio algoritmo. Pepper é opcional e mora fora do banco (secret manager).

### Política

- comprimento mínimo 8, recomendável 12; máximo alto (64+) para não impedir passphrase;
- permitir todos os caracteres, inclusive espaço e unicode;
- verificar contra lista de senhas vazadas (k-anonymity da API do Pwned Passwords) em vez de exigir regra de complexidade decorativa;
- não forçar rotação periódica sem indício de comprometimento;
- rehash transparente no login quando o custo do hash armazenado estiver abaixo do atual.

### Comparação

Sempre com a função de verificação da biblioteca (tempo constante). Nunca `==` em hash ou token.

---

## 2. Fluxo de login

```text
1. rate limit por IP + por conta (janela deslizante)
2. buscar usuário
3. verificar hash SEMPRE — inclusive quando o usuário não existe
   (hash falso, para o tempo de resposta não revelar a existência)
4. verificar estado da conta (ativa, bloqueada, e-mail confirmado)
5. registrar tentativa (sucesso/falha) em log de auditoria
6. criar sessão / emitir token
7. resposta de erro GENÉRICA e uniforme
```

### Antienumeração

Mesma mensagem, mesmo código HTTP e tempo de resposta semelhante para "usuário não existe" e "senha errada": *"E-mail ou senha inválidos."*

Cuidado com os vetores esquecidos: cadastro ("e-mail já cadastrado"), recuperação de senha ("enviamos, se existir conta"), convite e endpoint de verificação de disponibilidade.

### Antibrute force

- limite por conta e por IP, com incremento progressivo;
- bloqueio temporário após N falhas, sem revelar que a conta existe;
- CAPTCHA após limiar, não no primeiro acesso;
- alerta ao usuário em falha repetida e em login de novo dispositivo/local;
- monitorar padrão distribuído (muitas contas, uma senha = password spraying).

---

## 3. Sessões

### Cookie de sessão

```text
HttpOnly           impede leitura por JavaScript
Secure             só em HTTPS
SameSite=Lax       padrão razoável; Strict para painel administrativo
Path=/
sem Domain amplo   não compartilhar cookie com subdomínios sem necessidade
```

Nome sem prefixo previsível quando quiser reduzir fingerprint; `__Host-` quando aplicável.

### Regras

- ID de sessão com ≥ 128 bits de entropia de CSPRNG (nunca `Math.random()`);
- **regerar o ID no login** e após elevação de privilégio (evita session fixation);
- expiração absoluta e por inatividade;
- logout invalida no servidor, não só apaga o cookie;
- listar e revogar sessões ativas ao alterar senha, ao remover permissão e ao desativar usuário;
- vincular sessão a sinais estáveis quando fizer sentido (não ao IP puro, que quebra em mobile).

---

## 4. JWT

### Riscos

| Ataque | Mitigação |
| --- | --- |
| `alg: none` | fixar o algoritmo esperado na verificação |
| Confusão RS256 → HS256 | nunca aceitar algoritmo do token; validar contra lista fixa |
| `kid` malicioso / `jku` | resolver chave só por fonte confiável e cacheada |
| Token sem verificação | `jwt.decode()` **não valida**; usar `jwt.verify()` |
| Replay depois de logout | JWT é stateless: manter denylist/versão de token ou usar sessão no servidor |
| Payload sensível | JWT assinado é legível; não colocar dado confidencial |
| Expiração longa | access token curto (5–15 min) + refresh token com rotação |

Validar sempre: assinatura, `alg`, `exp`, `nbf`, `iss`, `aud` e o `sub`.

### Refresh token

Rotação com detecção de reuso: cada refresh emite um novo e invalida o anterior; se um token já usado reaparecer, revogar a família inteira. Armazenar hash do refresh token, não o valor.

---

## 5. OAuth 2 / OpenID Connect

- **Authorization Code + PKCE** para app web e mobile. Implicit flow está obsoleto;
- `state` obrigatório e verificado (anti-CSRF); `nonce` em OIDC;
- `redirect_uri` por correspondência exata em allowlist — nunca por prefixo ou wildcard;
- validar ID token: assinatura, `iss`, `aud`, `exp`, `nonce`;
- não confiar em `email_verified` ausente para vincular conta existente (account takeover por provedor);
- escopo mínimo; client secret nunca em app público.

---

## 6. MFA

Justificável em: painel administrativo, super admin, acesso a dado financeiro, exportação de dados, alteração de credencial.

Ordem de robustez: WebAuthn/passkey > TOTP > push > SMS (suscetível a SIM swap).

Cuidados: rate limit no código, código de uso único com janela curta, códigos de recuperação com hash, e reautenticação para desativar MFA.

---

## 7. Recuperação de senha

```text
token aleatório (≥256 bits) → armazenar HASH → expiração curta (15–60 min)
→ uso único → invalidar demais sessões ao concluir
→ resposta idêntica exista ou não a conta
→ rate limit por e-mail e por IP
→ notificar o titular após a troca
```

Nunca enviar a senha por e-mail. Nunca gerar senha previsível. Nunca aceitar o e-mail de destino vindo do corpo da requisição para uma conta existente.

---

## 8. Autorização

### Onde ela mora

```text
❌ no componente que decide mostrar o botão
✅ no handler que executa a operação, antes de qualquer efeito
```

Interface esconde; backend impede. As duas coisas juntas (a skill exige ocultar o que o usuário não pode ver **e** bloquear no servidor).

### Modelos

| Modelo | Uso |
| --- | --- |
| **RBAC** | papéis fixos (admin, gestor, operador). Simples e auditável |
| **ABAC** | decisão por atributos (dono do registro, empresa, horário, status) |
| **ReBAC** | relação entre sujeito e objeto (membro do grupo X) |

Na prática, a maioria dos SaaS precisa de RBAC + verificação de propriedade/tenant. Papel sozinho não autoriza acesso a um registro específico.

### Checklist por operação sensível

```text
1. identidade autenticada?              (quem)
2. tenant/empresa confere?              (onde)
3. papel/permissão permite a ação?      (o quê)
4. o recurso pertence ao escopo?        (qual registro)
5. o estado permite a operação?         (contexto: já pago? já cancelado?)
6. a ação foi registrada em auditoria?  (rastro)
```

### Padrões que ajudam

- função única de autorização por recurso (`canEditProduct(user, product)`), testável e reutilizada em todas as rotas;
- deny by default no middleware: rota nova nasce protegida, e liberar exige declaração explícita;
- separar rotas administrativas em prefixo próprio, com guarda no roteador **e** na função;
- 404 em vez de 403 quando o próprio conhecimento da existência do recurso for informação sensível;
- nunca autorizar por dado do JWT que o usuário possa influenciar sem revalidar no servidor (ex.: `company_id` em claim editável por auto-cadastro).

### Escalonamento de privilégio

Verificar sempre:

- usuário pode alterar o próprio papel? (não)
- usuário pode se convidar para outra empresa? (não)
- usuário pode criar outro usuário com papel maior que o seu? (não)
- remoção do último admin deixa a empresa órfã? (bloquear)
- endpoint de update de perfil aceita `role`? (mass assignment)
- token de convite prevê papel? (assinado no servidor, não escolhido pelo cliente)

---

## 9. Verificações rápidas de revisão

- [ ] senha com hash moderno e custo atual
- [ ] mensagem e tempo de erro uniformes no login
- [ ] rate limit em login, cadastro, reset, OTP
- [ ] ID de sessão regenerado no login
- [ ] logout invalida no servidor
- [ ] cookie HttpOnly + Secure + SameSite
- [ ] JWT com algoritmo fixado e claims validadas
- [ ] refresh token com rotação e detecção de reuso
- [ ] toda rota protegida por padrão
- [ ] autorização por recurso, não só por papel
- [ ] nenhum campo de papel/tenant atualizável pelo usuário
- [ ] sessões revogadas em troca de senha e remoção de permissão
- [ ] eventos de autenticação em log de auditoria
