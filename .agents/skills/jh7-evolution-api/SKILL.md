---
name: jh7-evolution-api
description: Especialista em integrar, desenvolver, revisar e depurar sistemas com Evolution API v2.3.x, especialmente React, TypeScript, Node.js, Supabase, n8n, Chatwoot e WhatsApp. Use quando a tarefa mencionar Evolution API, instâncias, QR Code, pairing code, conexão WhatsApp, envio de mensagens, mídia, áudio, webhooks, messages.upsert, connection.update, apikey, Baileys, WhatsApp Business, Chatwoot ou automações de WhatsApp.
compatibility: Projetada para Claude Code, VS Code Agents e outros agentes compatíveis com Agent Skills. Requer acesso ao código do projeto e, para testes reais, acesso autorizado a uma instalação Evolution API v2.3.x.
metadata:
  author: JH7 Marketing
  version: "1.0.0"
  target: "Evolution API 2.3.x"
argument-hint: "[tarefa da Evolution API, instância ou integração]"
---

# JH7 Evolution API

Atue como especialista em Evolution API v2.3.x e integração segura com aplicações React e TypeScript.

## Objetivo

Implementar, revisar e depurar integrações com Evolution API sem expor credenciais, sem quebrar fluxos existentes e sem assumir contratos de API que não foram confirmados na versão implantada.

## Antes de alterar o projeto

1. Leia `AGENTS.md`, `CLAUDE.md`, `package.json` e os arquivos relacionados à tarefa.
2. Identifique onde o código executa: navegador, backend Node.js, serverless function, Supabase Edge Function ou n8n.
3. Localize as variáveis de ambiente existentes e nunca revele seus valores.
4. Descubra a versão real da Evolution API implantada.
5. Confirme a URL base, sem acrescentar `/api` automaticamente.
6. Confirme o nome da instância. Não confunda nome, UUID e token da instância.
7. Antes de usar um endpoint ou payload, confira o Swagger/OpenAPI ou a documentação da mesma versão implantada.
8. Em mudanças com vários arquivos, apresente um plano curto antes de editar.

## Regra crítica de arquitetura

Nunca chame a Evolution API diretamente de um frontend React usando a chave global `apikey`.

Fluxo obrigatório:

```text
React → backend próprio/Edge Function → Evolution API
```

A chave da Evolution API deve permanecer apenas no servidor.

Use no backend:

```env
EVOLUTION_API_URL=https://evolution.exemplo.com
EVOLUTION_API_KEY=chave-secreta
EVOLUTION_API_VERSION=2.3
EVOLUTION_DEFAULT_INSTANCE=instancia-opcional
```

No frontend, exponha apenas a URL da API do próprio sistema. Nunca use `VITE_EVOLUTION_API_KEY`.

## Cabeçalhos padrão

Nas requisições servidor → Evolution API, normalmente use:

```http
Content-Type: application/json
apikey: <EVOLUTION_API_KEY>
```

Não registre a chave completa em logs, respostas, prints, commits ou mensagens de erro.

## Contratos da versão

Esta skill tem como alvo a linha `2.3.x`. Patch releases podem alterar validações, respostas e comportamento.

Antes de implementar:

1. consulte a versão em execução;
2. confira o contrato do endpoint;
3. faça uma requisição mínima de teste;
4. só então tipifique a resposta no projeto.

Não copie payloads de v1, v2.1, v2.2, v2.4 RC ou tutoriais antigos sem validação.

Consulte [referência de endpoints](references/endpoints.md) e [webhooks](references/webhooks.md).

## Fluxos suportados

Use esta skill para:

- criar, listar, conectar, reiniciar, desconectar e excluir instâncias;
- obter QR Code ou pairing code;
- consultar estado da conexão;
- enviar texto, mídia, documento, áudio, localização, contato, reação e enquete;
- configurar e receber webhooks;
- integrar com React, Node.js, Supabase, n8n e Chatwoot;
- tratar eventos de mensagens e conexão;
- criar clientes TypeScript e serviços reutilizáveis;
- investigar erros 400, 401, 404, 409, 422, 429 e 500;
- revisar segurança, retries, idempotência e logs.

## Procedimento para implementar uma integração

### 1. Descobrir o ambiente

Identifique:

- versão exata da Evolution API;
- integração da instância, como `WHATSAPP-BAILEYS`;
- URL base real;
- forma de autenticação;
- nome exato da instância;
- backend disponível no projeto;
- destino dos webhooks;
- necessidade de multiempresa ou múltiplas instâncias.

### 2. Criar uma camada de serviço

Centralize chamadas em um serviço servidor, por exemplo:

```text
src/server/evolution/
├── evolution.client.ts
├── evolution.types.ts
├── evolution.errors.ts
└── evolution.service.ts
```

Não espalhe chamadas `fetch` da Evolution API por componentes React.

Use o modelo em [cliente TypeScript](assets/evolution-server-client.ts).

### 3. Validar entradas

Antes de enviar:

- remova espaços, `+`, parênteses e hífens do telefone;
- mantenha código do país e DDD;
- rejeite número vazio ou claramente inválido;
- normalize e valide o nome da instância;
- limite tamanho de texto e arquivos conforme o ambiente;
- valide URLs de mídia;
- não aceite URLs internas ou privadas fornecidas livremente pelo usuário sem proteção contra SSRF.

Não invente o nono dígito nem altere números silenciosamente.

### 4. Executar a chamada

- aplique timeout com `AbortController`;
- envie `apikey` somente no servidor;
- trate respostas que não sejam JSON;
- capture status HTTP e uma mensagem segura;
- não exponha corpo interno completo ao frontend;
- use retries somente para falhas transitórias e operações seguras.

### 5. Verificar o resultado

Não considere sucesso apenas porque a chamada retornou HTTP 200.

Quando aplicável, verifique:

- estado da instância;
- presença de identificador da mensagem;
- evento posterior de atualização;
- logs do backend;
- recebimento do webhook;
- duplicidade.

### 6. Testar

Teste pelo menos:

1. credencial inválida;
2. instância inexistente;
3. instância desconectada;
4. número inválido;
5. envio válido;
6. timeout;
7. webhook duplicado;
8. payload inesperado;
9. evento com `remoteJid` em formato `@lid`;
10. mensagem `fromMe: true` e `fromMe: false`.

## Regras de instância

- Use o nome da instância exigido no path, não o UUID retornado pelo banco.
- Faça `trim` do nome antes de criar ou usar.
- Prefira nomes previsíveis, minúsculos e sem espaços, por exemplo `empresa-42-atendimento`.
- Não exclua ou desconecte uma instância sem autorização explícita.
- Antes de recriar, verifique se ela já existe.
- Trate criação como operação potencialmente não idempotente.

## Regras de envio

Antes de enviar uma mensagem:

1. confirme que a instância existe;
2. confirme que o estado está aberto/conectado;
3. normalize o número;
4. confirme o formato do payload na versão implantada;
5. registre um identificador interno da tentativa;
6. evite duplicar envios após timeout sem reconciliação.

Para texto na linha 2.3.x, o formato comum é:

```json
{
  "number": "5517999999999",
  "text": "Olá!"
}
```

Use esse formato apenas depois de confirmar o schema da instalação.

## Regras de webhook

Ao criar um receptor de webhook:

- responda rapidamente com HTTP 200 após validação mínima;
- processe tarefas demoradas de forma assíncrona;
- valide JSON e limite o tamanho do body;
- trate o campo `event` sem assumir caixa ou formato imutável;
- deduplique usando instância, evento e ID da mensagem;
- registre o payload original com dados sensíveis minimizados;
- ignore eventos desconhecidos sem derrubar o endpoint;
- nunca assuma que todo `remoteJid` contém um telefone legível;
- use `remoteJidAlt` quando disponível e apropriado;
- trate identificadores `@lid`, `@s.whatsapp.net`, `@g.us` e broadcast separadamente;
- filtre `fromMe` conforme o fluxo para evitar loops de resposta;
- não confie em `pushName` como identificador estável;
- não execute comandos, URLs ou conteúdo recebido sem validação.

Leia [webhooks e eventos](references/webhooks.md) antes de implementar.

## Idempotência

Webhooks podem chegar repetidos ou fora de ordem.

Crie uma chave de deduplicação, por exemplo:

```text
instance + event + data.key.id
```

Quando `data.key.id` não existir, gere uma estratégia específica para aquele evento.

No banco:

- use índice único para a chave de evento;
- grave `received_at` e `processed_at`;
- diferencie recebido, processando, concluído e falhou;
- permita reprocessamento controlado.

## Prevenção de loops

Em automações de resposta:

- ignore `fromMe: true` quando o fluxo só responde mensagens recebidas;
- marque mensagens geradas pelo sistema;
- aplique cooldown por contato;
- limite respostas por janela de tempo;
- não responda eventos de atualização como se fossem novas mensagens;
- não use apenas `pushName` ou texto para detectar duplicidade.

## Tratamento de erros

Mapeie erros para mensagens internas estáveis:

- `400`: payload incompatível ou entrada inválida;
- `401`: `apikey` ausente ou inválida;
- `404`: rota, instância ou recurso inexistente;
- `409`: conflito de estado ou recurso já existente;
- `422`: validação semântica;
- `429`: limite temporário;
- `500+`: falha interna ou indisponibilidade.

Ao encontrar 404:

1. confirme a URL base;
2. verifique se foi acrescentado `/api` indevidamente;
3. confirme o nome exato da instância;
4. consulte `fetchInstances`;
5. confirme se o endpoint existe na versão implantada.

Ao encontrar 401:

1. confirme o header `apikey`;
2. confirme se a chave é global ou específica da instância;
3. verifique se proxy ou gateway remove o header;
4. nunca mostre a chave nos logs.

Ao encontrar 500:

1. preserve request ID, endpoint, status e horário;
2. remova segredos do log;
3. consulte logs do container;
4. reproduza com payload mínimo;
5. compare com o schema da versão;
6. não faça retries ilimitados.

## Segurança

Consulte [arquitetura e segurança](references/security.md).

Regras obrigatórias:

- nunca expor `apikey` no React;
- nunca salvar chave no `localStorage`;
- nunca inserir chave em repositório;
- nunca retornar segredo em resposta ao navegador;
- usar HTTPS;
- restringir CORS no backend;
- aplicar autenticação e autorização antes de enviar mensagens;
- validar que o usuário pode operar a instância solicitada;
- em SaaS, associar cada instância a uma empresa/tenant;
- aplicar rate limit por usuário, empresa e instância;
- mascarar telefones e conteúdo sensível nos logs;
- registrar auditoria de criação, envio, logout e exclusão.

## Multiempresa

Em um SaaS, nunca aceite `instanceName` livre do frontend e o repasse diretamente.

Fluxo correto:

1. usuário autenticado solicita uma ação;
2. backend identifica `tenant_id` pela sessão;
3. backend busca a instância autorizada no banco;
4. backend usa o nome real e a credencial segura;
5. backend registra auditoria.

Cada registro de integração deve conter, no mínimo:

```text
id
tenant_id
instance_name
provider
status
created_at
updated_at
```

Não grave a chave global da Evolution por tenant se todos usam o mesmo servidor. Guarde-a no cofre de segredos do backend.

## React

No React:

- crie hooks que chamem apenas a API interna;
- exiba estados de carregamento, sucesso e erro;
- desabilite botões durante envio;
- evite clique duplo;
- não mostre detalhes internos da Evolution;
- atualize o status por polling moderado ou eventos do backend;
- não faça polling agressivo do QR Code;
- revogue URLs temporárias e timers ao desmontar componentes.

## Supabase

Quando usar Supabase:

- prefira Edge Functions ou backend próprio para chamar a Evolution API;
- guarde segredos no ambiente da função, não em tabelas públicas;
- ative RLS nas tabelas de integrações e mensagens;
- valide JWT no servidor;
- use `tenant_id` derivado da sessão;
- não use `service_role` no React;
- assine atualizações do banco apenas para dados autorizados.

## n8n

Ao integrar com n8n:

- armazene credenciais no Credentials do n8n;
- não fixe `apikey` em vários nós;
- use um nó HTTP centralizado ou subworkflow;
- configure timeout e tratamento de erro;
- filtre `fromMe`;
- deduplique `data.key.id`;
- responda ao webhook rapidamente;
- evite loops entre n8n, Chatwoot e Evolution.

## Chatwoot

Antes de alterar integração Chatwoot:

- confirme se a mensagem será enviada pelo Chatwoot ou diretamente pela Evolution;
- evite dois caminhos enviando a mesma resposta;
- preserve identificadores de inbox, conta e contato;
- trate mensagens ecoadas como `fromMe`;
- confirme webhooks e status após mudanças;
- não apague integração existente sem backup da configuração.

## Saída esperada do agente

Após concluir uma tarefa, informe:

1. o que foi alterado;
2. os arquivos modificados;
3. endpoints utilizados;
4. variáveis de ambiente necessárias, sem valores;
5. como testar;
6. comandos executados;
7. limitações ou diferenças de versão;
8. riscos pendentes.

Não declare que a integração funciona sem ter executado os testes possíveis ou sem indicar claramente o que não pôde ser validado.
