# Webhooks Evolution API 2.3.x

## Princípios

- Trate o webhook como entrada não confiável.
- Confirme os eventos suportados pela instalação.
- Responda rápido.
- Processe de modo idempotente.
- Não dependa de um único formato de JID.
- Preserve o payload original apenas quando necessário e com retenção controlada.

## Eventos comuns

Na linha v2, são frequentes eventos relacionados a:

```text
APPLICATION_STARTUP
QRCODE_UPDATED
CONNECTION_UPDATE
MESSAGES_SET
MESSAGES_UPSERT
MESSAGES_UPDATE
MESSAGES_DELETE
SEND_MESSAGE
CONTACTS_SET
CONTACTS_UPSERT
CONTACTS_UPDATE
PRESENCE_UPDATE
CHATS_SET
CHATS_UPSERT
CHATS_UPDATE
CHATS_DELETE
GROUPS_UPSERT
GROUP_UPDATE
GROUP_PARTICIPANTS_UPDATE
CALL
```

A lista e a grafia podem variar. Não envie eventos não reconhecidos ao configurar sem verificar o schema.

## Estrutura comum

Exemplo reduzido:

```json
{
  "event": "messages.upsert",
  "instance": "empresa-42-atendimento",
  "data": {
    "key": {
      "remoteJid": "5517999999999@s.whatsapp.net",
      "remoteJidAlt": "12345678901234@lid",
      "fromMe": false,
      "id": "MESSAGE_ID"
    },
    "pushName": "Cliente",
    "message": {
      "conversation": "Olá"
    }
  }
}
```

Não use esse exemplo como schema rígido. Mensagens de imagem, áudio, documento, reação, protocolo, grupo e anúncios possuem estruturas diferentes.

## JIDs

Trate pelo menos:

- `@s.whatsapp.net`: usuário por número;
- `@lid`: Linked Identity, nem sempre convertível diretamente em telefone;
- `@g.us`: grupo;
- `@broadcast`: broadcast/status.

Regras:

1. não use `split('@')[0]` como única fonte de telefone;
2. prefira `remoteJidAlt` quando ele oferecer a identidade adequada;
3. mantenha o JID completo como identificador técnico;
4. armazene telefone separadamente apenas quando validado;
5. não responda para `@lid` como se fosse sempre um telefone.

## Deduplicação

Chave recomendada para mensagens:

```text
instance:event:data.key.id
```

Exemplo de tabela:

```sql
create table evolution_webhook_events (
  id uuid primary key default gen_random_uuid(),
  dedupe_key text not null unique,
  tenant_id uuid,
  instance_name text not null,
  event_name text not null,
  message_id text,
  payload jsonb not null,
  status text not null default 'received',
  attempts integer not null default 0,
  received_at timestamptz not null default now(),
  processed_at timestamptz
);
```

Ajuste nomes e RLS ao projeto. Não aplique migration destrutiva sem autorização.

## Evitar loops

Para um bot que responde apenas mensagens recebidas:

```ts
if (payload.data?.key?.fromMe === true) {
  return;
}
```

Também:

- filtre eventos que não sejam nova mensagem;
- ignore mensagens geradas pelo próprio sistema;
- use cooldown;
- limite tentativas;
- não responda atualização de status;
- associe resposta ao evento processado.

## Validação mínima

Valide:

- método HTTP;
- tamanho máximo do body;
- `Content-Type`;
- presença de `event` e `instance`;
- instância conhecida e autorizada;
- segredo compartilhado ou proteção equivalente disponível no ambiente;
- JSON válido.

Não assuma assinatura criptográfica se ela não foi confirmada na versão e configuração implantadas. Quando não houver assinatura verificável, use camadas como URL secreta, autenticação no proxy, allowlist de rede, rate limit e correlação com instâncias conhecidas.

## Resposta rápida

Fluxo recomendado:

```text
Webhook → validar → persistir/deduplicar → responder 200 → processar
```

Não espere IA, envio de mídia, Chatwoot ou chamadas lentas antes de responder.

## Observabilidade

Registre:

- request ID;
- evento;
- instância mascarada quando necessário;
- message ID;
- status de processamento;
- duração;
- erro sanitizado.

Não registre:

- `apikey`;
- tokens;
- conteúdo integral de mensagens sem necessidade;
- mídia em base64;
- dados pessoais em logs públicos.
