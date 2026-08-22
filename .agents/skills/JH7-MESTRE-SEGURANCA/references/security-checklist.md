# Security gate e checklists

---

## 1. SECURITY GATE — nenhuma funcionalidade passa sem responder

| # | Item | Pergunta |
| --- | --- | --- |
| 1 | **Autenticação** | a operação exige identidade? está protegida? |
| 2 | **Autorização** | o usuário pode executar **esta** operação **neste** registro? validado no backend? |
| 3 | **Tenant** | existe qualquer caminho para acessar outra empresa? |
| 4 | **Input** | validado no backend por schema, com allowlist de campos? |
| 5 | **Injection** | SQL/NoSQL/comando/template com dado do usuário? parametrizado? |
| 6 | **XSS** | conteúdo do usuário é renderizado? como texto ou sanitizado? |
| 7 | **CSRF** | a operação altera estado e depende de cookie? tem proteção? |
| 8 | **Secrets** | nenhuma credencial no código, bundle, log ou repositório? |
| 9 | **API** | resposta expõe só o necessário? abuso é possível? |
| 10 | **Rate limit** | endpoint sensível ou custoso está limitado? |
| 11 | **Logs** | operação crítica registra quem, quando, o quê? |
| 12 | **Erros** | mensagem genérica, sem stack trace nem detalhe interno? |
| 13 | **Arquivos** | upload/download validado e servido com segurança? |
| 14 | **Banco** | query segura, RLS ativa, migration preserva dados, rollback existe? |

Resposta "não sei" conta como "não". Volta e verifica.

---

## 2. CHECKLIST FINAL antes de declarar pronto

- [ ] autenticação revisada
- [ ] autorização revisada (por papel **e** por propriedade do recurso)
- [ ] isolamento entre tenants revisado
- [ ] validação backend revisada
- [ ] SQL Injection revisado
- [ ] XSS revisado
- [ ] CSRF revisado
- [ ] SSRF revisado
- [ ] IDOR/BOLA revisado
- [ ] mass assignment revisado
- [ ] file upload revisado
- [ ] secrets revisados
- [ ] logs revisados
- [ ] tratamento de erros revisado
- [ ] rate limiting avaliado
- [ ] dependências avaliadas
- [ ] permissões avaliadas
- [ ] migration avaliada
- [ ] backup/rollback avaliado quando necessário

---

## 3. Checklists por tipo de alteração

### Nova rota / endpoint

- [ ] nasce protegida (deny by default no roteador)
- [ ] papel e permissão verificados no handler
- [ ] tenant resolvido no servidor
- [ ] recurso buscado com filtro de propriedade/tenant
- [ ] schema de entrada com `.strict()`
- [ ] projeção explícita na resposta
- [ ] limite de payload e timeout
- [ ] rate limit se sensível
- [ ] erro genérico + log com requestId
- [ ] auditoria se crítica
- [ ] teste de acesso negado (outro tenant, sem permissão, anônimo)

### Nova tela / componente

- [ ] área oculta quando sem permissão **e** bloqueada no backend
- [ ] nenhum secret em variável exposta ao bundle
- [ ] conteúdo do usuário renderizado como texto
- [ ] nenhuma decisão de segurança tomada só no cliente
- [ ] link externo com `rel="noopener noreferrer"`
- [ ] nada sensível em `localStorage` sem justificativa
- [ ] ação crítica com confirmação (regra do projeto) **e** revalidação no servidor

### Nova tabela / migration

- [ ] `company_id`/vínculo de tenant quando aplicável, com índice
- [ ] RLS ativa
- [ ] policy separada por SELECT/INSERT/UPDATE/DELETE
- [ ] `WITH CHECK` em INSERT e UPDATE
- [ ] nenhuma coluna sensível exposta em view pública
- [ ] migration incremental, idempotente, sem perda de dados
- [ ] rollback descrito (ou declarado irreversível)
- [ ] `000-completo.sql` atualizado (convenção do projeto)
- [ ] soft delete avaliado para dado crítico

### Nova integração externa

- [ ] credencial em variável de ambiente, escopo mínimo
- [ ] resposta validada por schema (fronteira de confiança)
- [ ] timeout, retry com backoff e circuit breaker
- [ ] webhook com assinatura, timestamp e idempotência
- [ ] URL de destino validada contra SSRF quando configurável
- [ ] erro do terceiro não vaza para o cliente
- [ ] log sem secret e sem PII desnecessária

### Novo upload

- [ ] tamanho máximo aplicado antes de bufferizar
- [ ] allowlist de extensão + tipo real por magic number
- [ ] nome gerado no servidor
- [ ] storage privado, sem execução, caminho com tenant
- [ ] policy de storage isolando por empresa
- [ ] download via URL assinada de validade curta
- [ ] quota e rate limit

### Autenticação / permissões

- [ ] hash de senha com custo atual
- [ ] rate limit e antienumeração
- [ ] sessão regenerada no login, revogável no logout
- [ ] JWT com algoritmo fixado e claims validadas
- [ ] usuário não altera o próprio papel nem o tenant
- [ ] remoção de permissão revoga sessão/efeito imediato
- [ ] evento em log de auditoria

### Operação financeira

- [ ] valor recalculado no backend
- [ ] operação atômica / transação contra race condition
- [ ] idempotência em cobrança e crédito
- [ ] auditoria com valor anterior e novo
- [ ] permissão específica, não papel genérico
- [ ] nenhuma alteração silenciosa

---

## 4. Severidade

| Nível | Critério | Prazo |
| --- | --- | --- |
| **P0 — CRÍTICO** | invasão, acesso administrativo, vazamento massivo, RCE, perda de dados, bypass de autenticação, cross-tenant | imediato; bloquear release |
| **P1 — ALTO** | comprometimento relevante sob algumas condições | dias |
| **P2 — MÉDIO** | impacto limitado ou exploração difícil | próxima iteração |
| **P3 — BAIXO** | hardening, redução de superfície | backlog |

### Ordem de correção

```text
 1. Remote Code Execution      7. SSRF
 2. Authentication bypass      8. File upload crítico
 3. Broken authorization       9. XSS crítico
 4. Cross-tenant access       10. CSRF
 5. SQL Injection             11. Rate limiting
 6. Credential exposure       12. Hardening
```

---

## 5. Quando bloquear

🔴 **SECURITY BLOCKER** obrigatório ao encontrar risco sério de:

```text
perda de dados · acesso não autorizado · exposição de credenciais
SQL Injection · execução de código · bypass de autenticação
cross-tenant access · privilege escalation
senha armazenada de forma insegura · secret no frontend
```

## 6. Quando aprovar

🟢 **SECURITY REVIEW APROVADO** somente quando não houver vulnerabilidade relevante conhecida no escopo analisado — e sempre com a ressalva de que segurança absoluta não existe.

> "Nenhuma vulnerabilidade conhecida foi identificada nesta análise, dentro do escopo analisado."
