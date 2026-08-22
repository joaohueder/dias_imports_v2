# Segurança de banco de dados

Complementa a skill **JH7-MESTRE-BD** — ela é a autoridade sobre integridade e estrutura; este arquivo trata de acesso, privilégio e vazamento.

---

## 1. Injection

Query parametrizada é a única defesa aceitável. Detalhes em [owasp-vulnerabilidades.md](owasp-vulnerabilidades.md#sql-injection).

Pontos que escapam da revisão:

- `order by`/`limit` dinâmico (não aceita parâmetro → allowlist);
- nome de tabela/coluna dinâmico (`%I` no `format()`);
- `EXECUTE` em função PL/pgSQL;
- filtro construído a partir de query string em PostgREST (`?or=(...)`) — a proteção real é a RLS, não a sintaxe;
- migration gerada com dado do usuário;
- `LIKE` com `%` do usuário (não é injection, mas é DoS: `%a%a%a%`).

---

## 2. Roles e privilégios

Menor privilégio por papel:

| Papel | Deve poder |
| --- | --- |
| aplicação (runtime) | `SELECT/INSERT/UPDATE/DELETE` nas tabelas que usa. **Sem** DDL, sem `SUPERUSER` |
| migration | DDL, em execução separada e credencial distinta |
| leitura/BI | `SELECT` em views específicas |
| backup | somente leitura |

```sql
-- ponto de partida em Postgres
revoke all on schema public from public;
revoke create on schema public from public;
grant usage on schema public to app_role;
grant select, insert, update, delete on public.produtos to app_role;
```

Nunca compartilhar uma credencial única entre aplicação, migration, BI e pessoas. Cada consumidor com credencial própria = revogação cirúrgica e rastro real.

### Supabase

| Chave/role | Regra |
| --- | --- |
| `anon` | pública; segura **somente** porque a RLS existe |
| `authenticated` | usuário logado; sujeito à RLS |
| `service_role` | **ignora RLS**. Só no servidor, nunca no frontend, nunca em bundle, nunca em repositório |
| `postgres` | administrativo; não usar como credencial de aplicação |

Se `service_role` aparecer em código de frontend, variável `VITE_*`/`NEXT_PUBLIC_*` ou log: 🔴 SECURITY BLOCKER + rotação imediata da chave.

---

## 3. RLS

Ver [multi-tenant-isolation.md](multi-tenant-isolation.md#4-rls--padrões) para os padrões completos. Regras que importam aqui:

- RLS ativa é deny by default: sem policy, nada passa. Esse é o estado inicial desejado;
- policy por operação; `WITH CHECK` obrigatório em `INSERT` e `UPDATE`;
- **nunca** desativar RLS nem criar policy `using (true)` para calar um erro. Se a policy recusa, descobrir a regra de negócio correta;
- `force row level security` quando o dono da tabela também precisa obedecer;
- views: por padrão executam com privilégio do dono e podem furar a RLS da tabela base. Em Postgres 15+, usar `security_invoker = on`;
- funções `security definer` são fronteira de confiança: `set search_path`, `revoke` do público, filtro de tenant interno, e nenhum parâmetro do cliente decidindo escopo.

---

## 4. Vazamento por consulta

Situações em que a policy está certa e o dado escapa mesmo assim:

- `select *` em API que devolve o objeto inteiro (ver [api-security.md](api-security.md#2-exposição-excessiva-de-dados));
- view sem `security_invoker` juntando tabelas de tenants diferentes;
- função `definer` que devolve agregado global;
- mensagem de erro do banco chegando ao cliente (`duplicate key value violates unique constraint "users_email_key"` confirma existência de e-mail → enumeração);
- `count` exato em recurso compartilhado;
- coluna técnica exposta (`created_by`, `internal_notes`, `stripe_customer_id`);
- log de query com parâmetro sensível.

---

## 5. Dados sensíveis

| Dado | Tratamento |
| --- | --- |
| senha | hash Argon2id/bcrypt. Nunca reversível |
| token / API key | armazenar hash; exibir o valor uma única vez na criação |
| documento (CPF/CNPJ) | avaliar necessidade real; criptografar em repouso quando exigido; mascarar na interface e no log |
| cartão | **não armazenar**. Tokenização pelo provedor (PCI-DSS) |
| dado bancário | acesso restrito por permissão + auditoria |
| dado pessoal | minimização, retenção definida, exclusão/anonimização quando aplicável |

Criptografia em repouso: coluna criptografada com chave fora do banco (KMS/secret manager). Criptografar com chave guardada na mesma base não protege contra dump do banco.

Hash com salt para dado que precisa de busca por igualdade; HMAC com chave secreta quando o valor tem baixo espaço de busca (CPF em SHA-256 puro é quebrável por força bruta).

### LGPD (requisito técnico, não parecer jurídico)

- base de tratamento por finalidade e coleta mínima;
- controle de acesso por necessidade;
- rastreabilidade de quem acessou dado pessoal;
- retenção e descarte;
- atendimento a titular: acesso, correção, portabilidade, eliminação;
- registro de incidente e capacidade de notificar;
- contrato e avaliação de risco com operadores (serviços de terceiros).

---

## 6. Exposição de rede

- banco **nunca** acessível diretamente pela internet. Firewall/security group liberando só a rede da aplicação;
- em Docker, não publicar `5432:5432` no host sem necessidade — usar rede interna do compose;
- TLS na conexão, com verificação de certificado (`sslmode=verify-full` quando disponível). `sslmode=disable` ou `rejectUnauthorized: false` é achado de segurança;
- painéis administrativos (pgAdmin, Adminer, Studio) atrás de autenticação forte e rede restrita, nunca abertos;
- porta do pooler tratada com o mesmo rigor da porta do banco.

---

## 7. Backup e alterações estruturais

```text
BACKUP CRIADO ≠ BACKUP TESTADO
BACKUP NÃO TESTADO = BACKUP NÃO CONFIÁVEL
```

- backup criptografado, com acesso restrito e retenção definida;
- backup fora do mesmo host/provedor (regra 3-2-1);
- dump nunca versionado no Git nem deixado em pasta pública;
- restauração testada periodicamente;
- antes de migration de risco: identificar risco → verificar backup → avaliar compatibilidade → migration incremental → rollback pronto.

Nenhuma alteração pode apagar ou alterar registro existente sem migração que preserve os dados. Operação destrutiva (`DROP`, `TRUNCATE`, `DELETE` sem `WHERE`) exige confirmação explícita — acionar a **JH7-MESTRE-BD**.

---

## 8. Auditoria no banco

Registrar em tabela própria (ou trigger) para operações críticas: quem, quando, tabela, registro, ação, valor anterior, valor novo, origem/IP, resultado.

Cuidados:

- a tabela de auditoria não deve ser editável pela aplicação (`insert` apenas, sem `update`/`delete`);
- não gravar senha, token completo nem secret no valor anterior/novo;
- RLS na tabela de auditoria também (auditoria de A não é visível para B);
- volume: definir retenção e particionamento antes de virar problema.

---

## 9. Revisão rápida

- [ ] toda query parametrizada; nenhum `order by` dinâmico sem allowlist
- [ ] credenciais separadas por consumidor, com menor privilégio
- [ ] `service_role` ausente do frontend, do bundle e do repositório
- [ ] RLS ativa nas tabelas com dado de usuário/tenant
- [ ] `WITH CHECK` em INSERT/UPDATE
- [ ] views com `security_invoker` quando aplicável
- [ ] funções `definer` com `search_path` fixo e sem escopo vindo do cliente
- [ ] respostas com projeção explícita de colunas
- [ ] erro do banco não chega ao cliente
- [ ] senha com hash forte; token com hash; cartão não armazenado
- [ ] banco fora da internet, TLS com verificação
- [ ] backup criptografado, externo, testado
- [ ] auditoria de operação crítica, sem secret nos valores
