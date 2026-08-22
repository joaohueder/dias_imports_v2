# Secrets e credenciais

---

## 1. Proibido no código

```text
senha · token · API key · secret · chave privada · service role key
credencial de banco · senha de SMTP · chave OAuth · webhook secret
chave de assinatura de JWT · chave de criptografia
```

```javascript
// 🔴 SECURITY BLOCKER
const API_KEY = 'sk_live_51H...';
const supabase = createClient(url, 'eyJhbGciOi...service_role...');
```

Inclui também: comentário no código, arquivo de exemplo com valor real, teste, seed, migration, README, documentação, print no chat e mensagem de commit.

---

## 2. Onde guardar

| Contexto | Solução |
| --- | --- |
| Desenvolvimento local | `.env` local, fora do Git |
| CI/CD | store de secrets da plataforma |
| Servidor/VPS | variável de ambiente do serviço (`systemd` `EnvironmentFile` com permissão 600) |
| Cloud | Secrets Manager / Key Vault / Parameter Store, com IAM |
| Kubernetes | Secret + provider externo; nunca em ConfigMap |
| Edge Function | variável de ambiente da plataforma |
| Frontend | **nenhum secret**. O bundle é público |

### A regra do frontend

Tudo que chega ao navegador é público. `VITE_*`, `NEXT_PUBLIC_*`, `REACT_APP_*` são embutidos no bundle e legíveis por qualquer visitante.

```text
✅ VITE_SUPABASE_URL, VITE_SUPABASE_ANON_KEY   (públicas por design; RLS é o que protege)
🔴 VITE_SERVICE_ROLE_KEY, VITE_OPENAI_KEY, VITE_META_TOKEN, VITE_SMTP_PASS
```

Quando o frontend precisa de um serviço que exige chave secreta, a chave fica em **proxy no backend**, que autentica o usuário, autoriza a operação, aplica rate limit e só então chama o terceiro.

---

## 3. `.env`

- `.env` no `.gitignore` desde o primeiro commit;
- `.env.example` versionado com **nomes** e valores vazios ou fictícios óbvios;
- permissão restrita no servidor (`chmod 600`);
- não copiar `.env` de produção para máquina local;
- ao inspecionar `.env` durante auditoria: verificar nomes, uso no código e defaults perigosos, **sem imprimir os valores reais**.

```bash
# ✅ auditoria sem expor valor
grep -o '^[A-Z_]*' .env
```

Sinais de problema em `.env`: senha padrão do template, `JWT_SECRET` curto ou previsível, chave de exemplo da documentação, `POSTGRES_PASSWORD=postgres`, `DASHBOARD_PASSWORD=this_password_is_insecure_and_should_be_updated`.

---

## 4. Geração

Sempre CSPRNG, nunca `Math.random()` nem timestamp.

```bash
openssl rand -base64 48
openssl rand -hex 32
```

```javascript
crypto.randomBytes(32).toString('base64url');
crypto.randomUUID();               // identificador, não segredo de alta entropia
```

Tamanho mínimo: 256 bits para chave de assinatura, 128 bits para token de uso único.

---

## 5. API keys emitidas pelo sistema

Quando o próprio sistema entrega chave para o cliente/integração:

```text
prefixo identificável   jh7_live_ / jh7_test_   → facilita secret scanning e revogação
valor aleatório          ≥ 256 bits
armazenar HASH           (SHA-256 é aceitável aqui: entropia alta dispensa hash lento)
exibir uma única vez     na criação
metadados                nome, escopo, tenant, criada por, criada em, último uso, expiração
escopo mínimo            permissões explícitas por chave
revogação imediata       e rotação sem downtime (permitir duas chaves ativas)
rate limit por chave
auditoria de uso
```

Comparação em tempo constante. Lookup por prefixo indexado + comparação do hash.

---

## 6. Rotação

Rotina: chave de longa duração tem prazo. Rotação obrigatória quando:

- alguém com acesso sai do time;
- fornecedor/terceiro é descontinuado;
- suspeita de exposição;
- chave apareceu em log, print, chat, ticket ou repositório;
- incidente de segurança em qualquer camada.

Rotação sem downtime: aceitar chave nova e antiga por uma janela → migrar consumidores → revogar a antiga → confirmar que nada quebrou.

`JWT_SECRET` é caso especial: rotação invalida todos os tokens emitidos. Planejar (dupla verificação com `kid`, ou aceitar o logout geral).

---

## 7. Secret exposto — resposta

```text
1. CONTER      → revogar/rotacionar a credencial IMEDIATAMENTE
2. SUBSTITUIR  → gerar nova e publicar no store correto
3. REMOVER     → tirar do código e do que estiver ao alcance
4. INVESTIGAR  → onde vazou, desde quando, quem teve acesso, o que foi acessado com ela
5. MONITORAR   → log de uso da credencial antiga, acesso anômalo
6. PREVENIR    → secret scanning no pipeline e no pre-commit
7. REGISTRAR   → incidente documentado (ver resposta-incidentes.md)
```

> **Remover do Git NÃO torna a credencial segura.** Ela está no histórico, em forks, em clones locais, em cache de plataformas e possivelmente já foi coletada por bots que varrem commits públicos em segundos.

Ordem correta: **rotacionar primeiro, limpar depois.** Reescrever histórico (`filter-repo`, BFG) é operação destrutiva que quebra clones existentes — só com confirmação explícita, e sempre depois da rotação.

---

## 8. Secret scanning

- pre-commit: gitleaks, trufflehog, detect-secrets;
- CI: bloquear merge em achado;
- push protection do provedor quando disponível;
- varredura do histórico ao adotar a prática (achado antigo = rotação).

---

## 9. Revisão rápida

- [ ] nenhum secret literal no código, teste, seed, doc ou comentário
- [ ] `.env` no `.gitignore`; `.env.example` sem valores reais
- [ ] nenhuma chave secreta em variável exposta ao frontend
- [ ] `service_role`/admin key só no servidor
- [ ] defaults de template todos trocados
- [ ] secrets gerados por CSPRNG com tamanho adequado
- [ ] API keys do sistema armazenadas como hash, com escopo e revogação
- [ ] secrets ausentes de logs (aplicação e pipeline)
- [ ] rotação prevista e possível sem downtime
- [ ] secret scanning ativo no pipeline
- [ ] achado histórico já rotacionado
