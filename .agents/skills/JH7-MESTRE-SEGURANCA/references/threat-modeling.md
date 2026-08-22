# Threat modeling

Pensar como atacante para decidir o que defender primeiro.

---

## 1. Quatro perguntas

```text
1. O que estamos construindo?     → arquitetura e fluxo de dados
2. O que pode dar errado?         → ameaças
3. O que vamos fazer sobre isso?  → mitigações
4. Fizemos um bom trabalho?       → validação e testes
```

---

## 2. Mapa da superfície de ataque

Antes de listar ameaças, levantar:

| Item | Perguntas |
| --- | --- |
| **Pontos de entrada** | rotas HTTP, RPC, webhook, upload, e-mail, fila, cron, WebSocket, formulário público, link com slug |
| **Fronteiras de confiança** | navegador → API, API → banco, API → serviço externo, tenant A → tenant B, usuário → admin |
| **Ativos** | credenciais, dados pessoais, dados financeiros, catálogo, mensagens, arquivos, configuração, chaves de integração |
| **Atores** | anônimo, usuário autenticado, usuário de outro tenant, admin do tenant, super admin, serviço interno, integração externa, bot |
| **Dados em trânsito e repouso** | onde trafegam, onde ficam, como são cifrados, quem lê |
| **Dependências** | bibliotecas, APIs externas, storage, provedor de e-mail, WhatsApp, Meta, IA |

Um diagrama simples de fluxo de dados com as fronteiras marcadas vale mais que uma lista longa.

---

## 3. STRIDE

| Sigla | Ameaça | Pergunta | Mitigação típica |
| --- | --- | --- | --- |
| **S** | Spoofing | alguém se passa por outro? | autenticação forte, MFA, assinatura |
| **T** | Tampering | dado pode ser alterado? | validação no backend, integridade, HMAC, constraint |
| **R** | Repudiation | dá para negar que fez? | log e auditoria imutável |
| **I** | Information disclosure | dado vaza? | autorização, projeção de campos, criptografia, erro genérico |
| **D** | Denial of service | dá para derrubar? | rate limit, cota, timeout, limite de recurso, fila |
| **E** | Elevation of privilege | dá para virar admin? | autorização por recurso, allowlist de campos, deny by default |

Aplicar STRIDE por fronteira de confiança, não por arquivo.

---

## 4. Perguntas de abuso (rápidas e eficazes)

Para cada funcionalidade nova:

```text
Como alguém abusaria disso?
Como acessaria dados de outra pessoa? E de outra empresa?
Como escalaria privilégio?
Como automatizaria o abuso em escala?
Como manipularia a requisição? Como contornaria o frontend?
Como exploraria os IDs? Dá para enumerar?
Como causaria indisponibilidade ou custo?
Como extrairia dados em massa?
Como modificaria dados que não são dele?
O que acontece se a requisição for repetida 1.000 vezes?
O que acontece se dois pedidos chegarem no mesmo milissegundo?
O que o usuário vê quando dá erro?
```

### Abuse case por papel

Rodar a mesma rota mentalmente como: **anônimo**, **usuário comum**, **usuário de outro tenant**, **usuário desativado**, **token expirado**, **admin do tenant**, **super admin**. O comportamento esperado de cada um está implementado?

---

## 5. Priorização

```text
RISCO = IMPACTO × PROBABILIDADE
DECISÃO = RISCO × COMPLEXIDADE DA MITIGAÇÃO
```

| Impacto | Exemplos |
| --- | --- |
| Crítico | comprometimento cross-tenant, RCE, bypass de autenticação, vazamento massivo |
| Alto | acesso a dado de outro usuário, escalonamento de privilégio, exposição de credencial |
| Médio | vazamento parcial, DoS localizado, informação técnica exposta |
| Baixo | hardening, redução de superfície |

Mitigação barata para risco médio se implementa junto. Mitigação caríssima para risco improvável se registra como decisão consciente, com o motivo escrito.

Ver classificação P0–P3 em [security-checklist.md](security-checklist.md).

---

## 6. Exemplo aplicado — página pública de produto com link de WhatsApp

**Ativos:** dados da empresa, número de WhatsApp, catálogo, métricas de visita.

**Atores:** visitante anônimo, bot, concorrente, usuário do painel.

| Ameaça | STRIDE | Risco | Mitigação |
| --- | --- | --- | --- |
| Slug adivinhável expõe página não publicada | I | Alto | slug com entropia; só publicar o que está marcado como publicado; filtro no servidor |
| Inflar contagem de visitas/cliques por laço | T/D | Médio | contagem só por função `security definer`; anônimo sem `insert`; rate limit por IP |
| XSS via descrição do produto cadastrada no painel | I/E | Alto | renderizar como texto; sanitizar se HTML for requisito; CSP |
| Enumeração de produtos por ID sequencial | I | Médio | UUID ou slug aleatório |
| Open redirect no link de saída | S | Médio | destino montado no servidor a partir do registro |
| Scraping do catálogo inteiro | I | Baixo | rate limit; sem endpoint de listagem total |
| Página pública devolvendo campo interno | I | Alto | view pública com projeção explícita; nunca `select *` |

**Validação:** teste que confirma que produto não publicado responde 404, que a contagem não sobe em requisição repetida além do limite, e que a resposta pública não contém campos internos.

---

## 7. Quando refazer o threat model

- nova fronteira de confiança (nova integração, novo tipo de usuário);
- mudança em autenticação, autorização ou modelo de tenant;
- nova entrada de dado externo (upload, webhook, importação);
- exposição pública de algo que era interno;
- incidente (o post-mortem alimenta o modelo).

O modelo é documento vivo. Ver [templates/threat-model.md](../templates/threat-model.md).
