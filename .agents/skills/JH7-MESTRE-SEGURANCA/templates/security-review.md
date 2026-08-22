# Security Review — [funcionalidade / PR]

> Revisão de segurança · `AAAA-MM-DD`
> Escopo: `[arquivos, rotas, migrations]`

---

## O que foi analisado

Descrição curta da funcionalidade e do que ela toca (autenticação, permissões, usuários, empresas, banco, arquivos, APIs, integrações, pagamentos, dados pessoais, infraestrutura).

**Arquivos revisados**

- `caminho/arquivo.ts`
- `supabase/sql/NNN_descricao.sql`

---

## SECURITY GATE

| # | Item | Status | Observação |
| --- | --- | --- | --- |
| 1 | Autenticação | 🟢 / 🟡 / 🔴 / n/a | |
| 2 | Autorização | | |
| 3 | Tenant | | |
| 4 | Input | | |
| 5 | Injection | | |
| 6 | XSS | | |
| 7 | CSRF | | |
| 8 | Secrets | | |
| 9 | API | | |
| 10 | Rate limit | | |
| 11 | Logs | | |
| 12 | Erros | | |
| 13 | Arquivos | | |
| 14 | Banco | | |

---

## Achados

### 🔴 / 🟠 / 🟡 / 🔵 · [título]

| Campo | Conteúdo |
| --- | --- |
| **Severidade** | P0 / P1 / P2 / P3 |
| **Arquivo** | `arquivo.ts:linha` |

**Problema**

**Risco**

**Exploração**

**Correção**

```typescript
// código corrigido
```

**Status** — corrigido nesta revisão / pendente / aceito com justificativa

---

## Correções aplicadas

| Arquivo | Alteração | Motivo |
| --- | --- | --- |
| | | |

---

## Revalidação

O que foi conferido depois da correção:

- [ ] build/typecheck passou
- [ ] testes existentes continuam verdes
- [ ] teste novo cobrindo a falha
- [ ] requisição de prova executada (descrever)
- [ ] nenhuma funcionalidade legítima quebrou

---

## Veredito

Uma das duas opções:

### 🔴 SECURITY BLOCKER

**O que está bloqueado:**
**Causa:**
**Risco concreto:**
**Caminho seguro para seguir:**

### 🟢 SECURITY REVIEW APROVADO

Nenhuma vulnerabilidade relevante conhecida foi identificada no escopo analisado. Não constitui garantia de segurança absoluta.

**Risco residual aceito** (se houver): o quê, por quê, e o que reduziria.

---

## Pendências para depois

| Item | Prioridade | Motivo do adiamento |
| --- | --- | --- |
| | | |
