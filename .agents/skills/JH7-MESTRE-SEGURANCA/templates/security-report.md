# JH7 SECURITY REPORT

> Auditoria de segurança — **[nome do sistema]**
> Data: `AAAA-MM-DD` · Escopo analisado: `[repositório / módulos / ambiente]`
> Responsável: JH7-MESTRE-SEGURANÇA

---

## RESUMO

Situação geral em 3 a 6 linhas: o que foi analisado, qual é a postura de segurança encontrada, o que é mais urgente.

| Severidade | Quantidade |
| --- | --- |
| 🔴 Críticos | 0 |
| 🟠 Altos | 0 |
| 🟡 Médios | 0 |
| 🔵 Baixos | 0 |

**Escopo não coberto:** o que ficou fora (ex.: infraestrutura sem acesso, código de terceiros, teste dinâmico não executado).

---

## SUPERFÍCIE DE ATAQUE MAPEADA

| Item | Encontrado |
| --- | --- |
| Pontos de entrada | |
| Fronteiras de confiança | |
| Ativos sensíveis | |
| Atores / papéis | |
| Integrações externas | |
| Mecanismo de autenticação | |
| Mecanismo de autorização | |
| Modelo de multi-tenancy | |

---

## 🔴 CRÍTICOS

### C-01 · [título curto]

| Campo | Conteúdo |
| --- | --- |
| **Severidade** | P0 |
| **Categoria** | ex.: Broken Access Control (OWASP A01) / CWE-639 |
| **Arquivo** | `caminho/arquivo.ts:linha` |

**Problema**
O que está errado, objetivamente.

**Risco**
O que um atacante consegue obter.

**Exploração**
Passo a passo plausível, com exemplo de requisição quando útil.

```http
PATCH /api/produtos/<id-de-outra-empresa>
Authorization: Bearer <token-do-tenant-A>
```

**Correção recomendada**
O caminho seguro.

```typescript
// código corrigido
```

**Validação**
Como confirmar que fechou (teste, query, requisição).

---

## 🟠 ALTOS

### A-01 · [título]

*(mesma estrutura)*

---

## 🟡 MÉDIOS

### M-01 · [título]

*(mesma estrutura, pode ser mais enxuta)*

---

## 🔵 BAIXOS

### B-01 · [título]

Achado, risco residual e melhoria sugerida em poucas linhas.

---

## 🟢 PONTOS POSITIVOS

Controles já implementados que devem ser preservados. Importante: evita que uma refatoração futura remova proteção existente sem perceber.

- ex.: RLS ativa em todas as tabelas de negócio
- ex.: senha com bcrypt cost 12
- ex.: `service_role` restrita a Edge Functions

---

## PLANO DE CORREÇÃO

| Prioridade | ID | Ação | Esforço | Dependência |
| --- | --- | --- | --- | --- |
| **P0** | C-01 | | | |
| **P1** | A-01 | | | |
| **P2** | M-01 | | | |
| **P3** | B-01 | | | |

**Ordem sugerida de execução e por quê:** justificar quando a ordem divergir da severidade pura (ex.: correção de P1 desbloqueia a de P0).

---

## TESTES RECOMENDADOS

- [ ] usuário A não acessa recurso do usuário B
- [ ] empresa A não acessa recurso da empresa B
- [ ] rota protegida recusa anônimo e token expirado
- [ ] campo sensível não é atualizável pelo cliente
- [ ] upload recusa tipo não permitido
- [ ] rate limit responde 429 no limiar

---

## OBSERVAÇÕES

Segurança absoluta não existe. Este relatório afirma apenas que **nenhuma vulnerabilidade conhecida foi identificada além das listadas, dentro do escopo analisado e no estado atual do código**.

Recomenda-se reavaliação após: mudança em autenticação/autorização, nova integração, nova entrada de dado externo ou incidente.
