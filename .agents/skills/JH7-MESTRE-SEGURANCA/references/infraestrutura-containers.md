# Infraestrutura, containers e rede

Segurança não termina no código da aplicação.

---

## 1. Docker

### Imagem

```dockerfile
# ✅ base oficial, versão fixada por digest quando possível
FROM node:22.11.0-alpine@sha256:...

# dependências primeiro, para cache
WORKDIR /app
COPY package*.json ./
RUN npm ci --omit=dev && npm cache clean --force

COPY . .

# usuário sem privilégio
RUN addgroup -S app && adduser -S app -G app
USER app

EXPOSE 3000
CMD ["node", "server.js"]
```

Regras:

- **não rodar como root** (`USER` explícito). Container root + volume montado = escrita no host com privilégio;
- imagem base oficial ou verificada; tag fixa, nunca `latest` em produção;
- multi-stage build para não levar toolchain, `.git`, testes e devDependencies para a imagem final;
- `.dockerignore` com `.env`, `.git`, `node_modules`, dumps, chaves;
- **nunca `ARG`/`ENV` com secret** — fica no histórico de camadas (`docker history` revela). Usar secret em runtime ou BuildKit secrets;
- menos pacotes = menos CVE. Não instalar `curl`, compiladores e utilitários de debug na imagem de produção;
- escanear imagem (Trivy, Grype, `docker scout`) no pipeline;
- `HEALTHCHECK` para orquestrador detectar falha.

### Runtime

```yaml
services:
  api:
    read_only: true                 # filesystem imutável
    tmpfs: [/tmp]
    cap_drop: [ALL]                 # remove capabilities
    security_opt: [no-new-privileges:true]
    user: "1000:1000"
    mem_limit: 512m
    pids_limit: 200
    restart: unless-stopped
```

- nunca `privileged: true`;
- nunca montar `/var/run/docker.sock` em container acessível pela aplicação (equivale a root no host);
- volume com o mínimo necessário e, quando possível, `:ro`;
- **não publicar porta que não precisa sair do host**: `5432:5432` expõe o banco; use rede interna do compose e publique só o proxy;
- limites de memória/CPU/PIDs contra DoS por consumo;
- rede segmentada: banco e serviços internos em rede sem acesso externo.

### Supabase self-hosted

- trocar **todos** os defaults do `.env`: `POSTGRES_PASSWORD`, `JWT_SECRET`, `ANON_KEY`, `SERVICE_ROLE_KEY`, `DASHBOARD_USERNAME/PASSWORD`, `SECRET_KEY_BASE`, `VAULT_ENC_KEY`;
- Studio nunca exposto publicamente sem autenticação forte + IP restrito;
- porta do Postgres fechada para a internet;
- Kong é a borda: revisar rotas expostas, e não deixar rota administrativa acessível;
- `JWT_SECRET` compartilhado entre serviços — vazamento permite forjar qualquer identidade, inclusive `service_role`. Rotação invalida todos os tokens;
- backup do volume do Postgres criptografado e fora do host.

---

## 2. Servidor Linux

- SSH: só chave (`PasswordAuthentication no`), sem login de root (`PermitRootLogin no`), porta com fail2ban/rate limit, acesso por bastion quando houver;
- firewall deny by default: liberar 22 (restrito), 80, 443. Todo o resto fechado;
- atualizações de segurança automáticas para o SO;
- usuário de aplicação sem sudo; serviço com `systemd` usando `NoNewPrivileges`, `ProtectSystem=strict`, `PrivateTmp`;
- nada de painel administrativo (phpMyAdmin, pgAdmin, Adminer, Portainer) aberto na internet;
- log centralizado com retenção, e monitoramento de disco (log cheio = indisponibilidade);
- timezone e NTP corretos (auditoria e validação de token dependem de hora).

---

## 3. TLS e proxy reverso

- certificado válido com renovação automática (ACME);
- TLS 1.2 e 1.3 apenas; ciphers modernos; sem SSLv3/TLS 1.0/1.1;
- HSTS com `max-age` longo (preload só após confirmar todos os subdomínios em HTTPS);
- redirect 301 de HTTP para HTTPS;
- proxy define os headers de segurança e remove os que revelam stack;
- limitar tamanho de corpo (`client_max_body_size`) e conexões por IP;
- **não confiar em `X-Forwarded-For` recebido do cliente** — sobrescrever na borda e configurar `trust proxy` só para o IP do próprio proxy;
- timeout de leitura/escrita para conter slowloris;
- WAF/CDN à frente quando o serviço for público, com proteção de bot e DDoS.

---

## 4. Variáveis de ambiente e configuração

- separar ambiente: dev, staging, produção com credenciais distintas;
- produção com `NODE_ENV=production` (ou equivalente) — modo dev expõe stack trace e desabilita otimizações de segurança;
- desativar debug, source map público, endpoint de introspecção e página de erro detalhada em produção;
- nenhuma credencial de produção em máquina de desenvolvimento;
- ver [secrets-credenciais.md](secrets-credenciais.md).

---

## 5. Disponibilidade e recuperação

- backup automatizado, criptografado, externo, com retenção e **restauração testada**;
- monitoramento com alerta: erro 5xx, latência, disco, memória, falha de login em massa, fila travada;
- plano de DR com RPO/RTO declarados;
- limites de recurso para que um tenant não derrube os outros;
- rollback de deploy possível e ensaiado.

---

## 6. Revisão rápida

- [ ] container não roda como root, sem `privileged`, sem docker.sock
- [ ] imagem oficial com tag fixa, escaneada, sem secret nas camadas
- [ ] portas internas não publicadas; banco fora da internet
- [ ] limites de memória/CPU/PIDs definidos
- [ ] `.dockerignore` cobrindo `.env`, `.git`, dumps, chaves
- [ ] SSH só por chave, root desabilitado, firewall deny by default
- [ ] painéis administrativos fechados ou restritos por IP
- [ ] TLS 1.2+/1.3, HSTS, headers de segurança no proxy
- [ ] `X-Forwarded-For` confiável só a partir do proxy
- [ ] defaults do Supabase self-hosted todos trocados
- [ ] backup criptografado, externo e restauração testada
- [ ] monitoramento com alerta ativo
