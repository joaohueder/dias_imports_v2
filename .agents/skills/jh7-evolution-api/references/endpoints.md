# Evolution API 2.3.x — Referência operacional

Esta referência lista rotas comuns da linha v2.3.x. A instalação real é a fonte de verdade. Confirme cada rota e schema no Swagger/OpenAPI da mesma versão antes de alterar código de produção.

## Convenções

```text
BASE_URL=https://evolution.exemplo.com
INSTANCE=empresa-42-atendimento
```

Headers comuns:

```http
Content-Type: application/json
apikey: <EVOLUTION_API_KEY>
```

Não acrescente `/api` à URL base sem confirmar que o proxy da instalação exige esse prefixo.

## Instâncias

### Criar

```http
POST /instance/create
```

Payload mínimo comum:

```json
{
  "instanceName": "empresa-42-atendimento",
  "integration": "WHATSAPP-BAILEYS",
  "qrcode": true
}
```

Campos adicionais variam pela integração e patch. Não envie campos desconhecidos por tentativa.

### Listar

```http
GET /instance/fetchInstances
```

Use a resposta para descobrir o nome exato e o estado armazenado. Não use o UUID no lugar do nome quando a rota exige `{instance}`.

### Conectar ou obter QR

```http
GET /instance/connect/{instance}
```

A resposta pode conter QR Code, pairing code ou estado, conforme configuração e patch.

### Estado da conexão

```http
GET /instance/connectionState/{instance}
```

Estados devem ser tratados como valores externos. Não quebre o sistema caso apareça um estado novo.

### Reiniciar

```http
PUT /instance/restart/{instance}
```

Confirme se a rota está disponível no patch implantado.

### Logout

```http
DELETE /instance/logout/{instance}
```

É operação destrutiva de sessão. Exija autorização explícita e confirmação na interface.

### Excluir

```http
DELETE /instance/delete/{instance}
```

É operação destrutiva. Verifique dependências, auditoria e autorização antes de executar.

## Mensagens

### Texto

```http
POST /message/sendText/{instance}
```

Payload comum em v2.3.x:

```json
{
  "number": "5517999999999",
  "text": "Olá! Como podemos ajudar?"
}
```

Opções como delay, link preview, menções e quoted podem variar. Confirme o schema local.

### Mídia e documento

```http
POST /message/sendMedia/{instance}
```

Modelo comum:

```json
{
  "number": "5517999999999",
  "mediatype": "image",
  "mimetype": "image/jpeg",
  "caption": "Legenda opcional",
  "media": "https://exemplo.com/imagem.jpg",
  "fileName": "imagem.jpg"
}
```

`media` pode aceitar URL ou base64 conforme endpoint, configuração e versão. Não assuma. Valide tamanho, MIME e origem.

### Áudio de WhatsApp

```http
POST /message/sendWhatsAppAudio/{instance}
```

Modelo comum:

```json
{
  "number": "5517999999999",
  "audio": "https://exemplo.com/audio.ogg",
  "encoding": true
}
```

Confirme formatos aceitos e limite de tamanho na instalação.

### Outros envios

Rotas comuns da família `/message` incluem localização, contato, reação, enquete, status e presença. Use somente após conferir o contrato da versão implantada.

## Webhook

### Configurar

```http
POST /webhook/set/{instance}
```

Modelo comum:

```json
{
  "webhook": {
    "enabled": true,
    "url": "https://api.seusistema.com/webhooks/evolution",
    "webhookByEvents": false,
    "webhookBase64": false,
    "events": [
      "CONNECTION_UPDATE",
      "QRCODE_UPDATED",
      "MESSAGES_UPSERT",
      "MESSAGES_UPDATE"
    ]
  }
}
```

Confirme nomes, caixa e disponibilidade de eventos.

### Consultar

```http
GET /webhook/find/{instance}
```

Use para verificar se a configuração persistiu.

## Exemplo cURL — estado

```bash
curl --request GET \
  --url "$EVOLUTION_API_URL/instance/connectionState/$INSTANCE" \
  --header "apikey: $EVOLUTION_API_KEY"
```

## Exemplo cURL — enviar texto

```bash
curl --request POST \
  --url "$EVOLUTION_API_URL/message/sendText/$INSTANCE" \
  --header "Content-Type: application/json" \
  --header "apikey: $EVOLUTION_API_KEY" \
  --data '{
    "number": "5517999999999",
    "text": "Mensagem de teste"
  }'
```

## Checklist para qualquer endpoint

1. A URL base está correta?
2. O proxy exige algum prefixo?
3. O header `apikey` chegou ao serviço?
4. O nome da instância está exato e URL-encoded?
5. A instância está conectada?
6. O payload corresponde ao patch 2.3.x implantado?
7. O número está em dígitos com país e DDD?
8. Existe timeout?
9. O erro foi mascarado antes de retornar ao frontend?
10. Há risco de duplicar a operação após retry?
