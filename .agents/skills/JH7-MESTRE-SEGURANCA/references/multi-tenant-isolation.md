# Isolamento multi-tenant

A verificação mais importante em SaaS. Um vazamento cross-tenant é sempre **P0**.

> **UMA EMPRESA JAMAIS PODE ACESSAR DADOS DE OUTRA EMPRESA.**

---

## 1. A regra de ouro

O escopo do tenant **nunca** vem do cliente.

```text
❌ company_id = req.body.company_id
❌ company_id = req.query.company_id
❌ company_id = req.headers['x-company-id']
❌ company_id = localStorage.getItem('company')
❌ company_id = claim editável do JWT sem revalidação

✅ company_id = resolvido no servidor a partir da identidade autenticada
```

Em Supabase/Postgres, isso normalmente significa derivar de `auth.uid()` dentro da policy ou de uma função `security definer` que consulta a tabela de vínculo.

Se o usuário pertence a mais de uma empresa, a seleção de empresa é um **pedido** do cliente que precisa ser **autorizado** no servidor:

```sql
-- vínculo obrigatório antes de aceitar o company_id solicitado
select 1 from company_users
 where user_id = auth.uid() and company_id = $1 and active;
```

---

## 2. Vetores de ataque a testar

| Vetor | Teste |
| --- | --- |
| URL / path | `GET /produtos/<id-de-outra-empresa>` |
| Query string | `?company_id=<outro>` |
| Body | `{"company_id": "<outro>"}` em create e update |
| Header | `X-Company-Id`, `X-Tenant`, header customizado |
| Cookie | valor de empresa em cookie editável |
| Relacionamento | criar filho apontando para pai de outro tenant (`product_id` alheio) |
| Update de FK | mover registro próprio para empresa alheia |
| Listagem | filtro ausente devolvendo tudo |
| Agregado | `count`, `sum`, dashboard somando linhas de outros tenants |
| Busca | full-text que ignora o filtro de empresa |
| Ordenação/paginação | cursor que atravessa a fronteira |
| Arquivo | download por caminho/ID de storage de outro tenant |
| Realtime | assinar canal/tópico de outra empresa |
| RPC | função `security definer` sem filtro interno |
| Exportação | CSV/relatório gerado sem escopo |
| Webhook | evento externo trazendo `company_id` e sendo obedecido |
| Convite | convidar-se para empresa alheia; aceitar convite de outro e-mail |
| Slug público | página pública revelando dado privado por slug adivinhável |
| Enumeração | ID sequencial permite varredura (`/produtos/1..9999`) |

---

## 3. Defesa em camadas

```text
1. autenticação            → quem é
2. resolução do tenant     → a qual empresa pertence (servidor)
3. autorização da operação → papel/permissão
4. filtro na query         → where company_id = <resolvido>
5. RLS no banco            → o banco recusa mesmo se a camada 4 falhar
6. permissões de role      → menor privilégio no Postgres
7. log + auditoria         → rastro de acesso e de negativa
8. teste automatizado      → prova de que A não vê B
```

O item 5 é o que salva quando alguém esquece o item 4. **Não abrir mão de nenhum dos dois.**

---

## 4. RLS — padrões

### Ativar e forçar

```sql
alter table public.produtos enable row level security;
-- opcional e mais forte: aplica também ao dono da tabela
alter table public.produtos force row level security;
```

Sem policy e com RLS ativo, o resultado é "nega tudo" — que é o comportamento correto de partida (deny by default).

### Policy por operação, nunca uma policy "para tudo"

```sql
-- leitura
create policy produtos_select on public.produtos
  for select to authenticated
  using (company_id = public.current_company_id());

-- inserção: WITH CHECK impede gravar em empresa alheia
create policy produtos_insert on public.produtos
  for insert to authenticated
  with check (company_id = public.current_company_id()
              and public.has_permission('produtos.criar'));

-- atualização: USING (linha atual) + WITH CHECK (linha resultante)
create policy produtos_update on public.produtos
  for update to authenticated
  using (company_id = public.current_company_id())
  with check (company_id = public.current_company_id());

-- exclusão
create policy produtos_delete on public.produtos
  for delete to authenticated
  using (company_id = public.current_company_id()
         and public.has_permission('produtos.excluir'));
```

**`USING` sem `WITH CHECK` em `UPDATE` permite mover a linha para outro tenant.** Erro clássico.

### Função de contexto

```sql
create or replace function public.current_company_id()
returns uuid
language sql
stable
security definer
set search_path = public, pg_temp   -- obrigatório em definer
as $$
  select cu.company_id
    from public.company_users cu
   where cu.user_id = auth.uid() and cu.active
   limit 1;
$$;

revoke all on function public.current_company_id() from public;
grant execute on function public.current_company_id() to authenticated;
```

Cuidados com `security definer`:

- `set search_path` sempre (senão o chamador pode injetar objeto homônimo);
- `stable` permite cache dentro da query e melhora o plano;
- a função **é** a fronteira de confiança: ela não aceita parâmetro do cliente para decidir o tenant;
- `revoke` do público e `grant` só para o papel necessário.

### Performance

Policy que consulta tabela em cada linha custa caro. Padrões que ajudam:

- índice em `company_id` (e índices compostos começando por ele);
- função `stable` em vez de subquery repetida;
- envolver função em `(select fn())` para o planner avaliar uma vez;
- evitar `in (select ...)` gigante na policy.

Segurança primeiro; depois otimizar sem abrir mão do filtro.

---

## 5. Storage

- caminho com o `company_id` como primeiro segmento (`{company_id}/{produto_id}/{arquivo}`);
- policy de storage validando o primeiro segmento contra o tenant do usuário;
- bucket privado por padrão; URL assinada com validade curta;
- não usar nome de arquivo previsível para conteúdo privado;
- verificar se o bucket "público" contém apenas ativo realmente público.

---

## 6. Chaves e credenciais em SaaS

- `anon key` é pública por natureza: ela só é segura porque a RLS existe. Sem RLS, `anon key` é acesso total;
- `service_role` **ignora RLS**. Só no servidor, só dentro de função que já validou a identidade, e sempre com filtro de tenant explícito no código;
- API key de tenant (para integração) precisa de escopo, prefixo identificável, hash no banco, rotação e revogação.

---

## 7. Testes obrigatórios

Dois testes que não podem faltar na suíte:

```text
✅ Usuário A NÃO acessa recurso do Usuário B
✅ Empresa A NÃO acessa recurso da Empresa B
```

### Receita de teste (integração)

```text
setup: criar empresa A com usuário A e registro RA
       criar empresa B com usuário B e registro RB

para cada rota que recebe id:
  autenticar como A
  GET    /recurso/RB   → 404 (ou 403)
  PATCH  /recurso/RB   → 404/403 e RB inalterado
  DELETE /recurso/RB   → 404/403 e RB existente
  POST   /recurso {company_id: B} → criado em A, não em B
  PATCH  /recurso/RA {company_id: B} → recusado ou ignorado
  GET    /recursos     → lista contém RA e NÃO contém RB
  GET    /dashboard    → números não incluem dados de B
```

### Teste de RLS direto no banco

```sql
-- simula o usuário A via PostgREST/authenticated
set local role authenticated;
set local request.jwt.claims = '{"sub":"<uuid-do-usuario-A>","role":"authenticated"}';

select count(*) from public.produtos;                 -- só os de A
select * from public.produtos where id = '<id-de-B>'; -- 0 linhas
insert into public.produtos (company_id, name) values ('<empresa-B>','x'); -- deve falhar
update public.produtos set company_id = '<empresa-B>' where id = '<id-de-A>'; -- deve falhar
reset role;
```

Rodar como `postgres`/`service_role` invalida o teste — `auth.uid()` fica nulo e a policy não é exercitada.

---

## 8. Revisão rápida

- [ ] toda tabela com dado de tenant tem `company_id` (ou vínculo indireto claro)
- [ ] RLS ativa em todas elas
- [ ] policy separada por SELECT/INSERT/UPDATE/DELETE
- [ ] `WITH CHECK` presente em INSERT e UPDATE
- [ ] tenant resolvido no servidor, nunca no payload
- [ ] filtro de tenant também na aplicação (defense in depth)
- [ ] RPC/`security definer` com `search_path` fixo e filtro interno
- [ ] storage particionado por tenant e com policy
- [ ] `service_role` só no servidor e após validar identidade
- [ ] agregados, buscas, exportações e realtime escopados
- [ ] testes automatizados de cross-tenant existem e passam
- [ ] negativa de acesso registrada em log
