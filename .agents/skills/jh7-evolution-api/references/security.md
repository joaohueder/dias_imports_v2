# Arquitetura e segurança

## Arquitetura recomendada

```text
[React]
   |
   | JWT/cookie do próprio sistema
   v
[API backend / Supabase Edge Function]
   |
   | apikey da Evolution no servidor
   v
[Evolution API 2.3.x]
   |
   v
[WhatsApp]
```

Webhooks seguem o caminho inverso:

```text
[Evolution API] → [Webhook backend] → [fila/banco] → [processador] → [React em tempo real]
```

## Fronteiras de confiança

### Navegador

Não é confiável para segredos. O usuário pode inspecionar bundles, rede e armazenamento.

### Backend

É responsável por:

- autenticar usuário;
- autorizar tenant e instância;
- validar payload;
- acessar segredos;
- aplicar rate limit;
- chamar Evolution;
- sanitizar erros;
- registrar auditoria.

### Evolution API

É serviço externo ao domínio da aplicação. Trate respostas e webhooks como dados externos.

## Autorização multiempresa

Nunca faça:

```ts
const instance = request.body.instanceName;
await evolution.sendText(instance, ...);
```

Faça:

```ts
const tenantId = session.tenantId;
const integration = await repository.findAuthorizedEvolutionInstance(tenantId);
await evolution.sendText(integration.instanceName, ...);
```

## Segredos

Use:

- variáveis de ambiente do servidor;
- secret manager;
- secrets de Supabase Edge Functions;
- credenciais do n8n.

Evite:

- `.env` commitado;
- `VITE_*` para apikey secreta;
- tabela acessível pelo frontend;
- logs;
- prints;
- respostas de erro.

## SSRF e mídia

Quando o sistema aceita uma URL de mídia e a Evolution fará download:

- permita apenas HTTPS;
- bloqueie localhost, IPs privados e metadata endpoints;
- use allowlist de domínios quando possível;
- verifique MIME e tamanho;
- prefira storage controlado pelo sistema;
- use URLs assinadas de curta duração.

## Rate limit

Aplique limites por:

- usuário;
- tenant;
- instância;
- número de destino;
- endpoint;
- janela de tempo.

Envio em massa exige regras específicas, consentimento, opt-out e conformidade aplicável. Não implemente mecanismos para burlar limites ou políticas do WhatsApp.

## Auditoria

Registre ações administrativas:

```text
create_instance
connect_instance
restart_instance
logout_instance
delete_instance
set_webhook
send_message
```

Campos úteis:

```text
actor_id
tenant_id
instance_id
action
request_id
result
created_at
```

Não salve segredos na auditoria.
