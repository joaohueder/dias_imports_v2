# Instalação da skill jh7-evolution-api

Copie a pasta completa `jh7-evolution-api` para uma das localizações de skills do projeto.

## Claude Code e VS Code Agents

Recomendado para uso compartilhado:

```text
.claude/skills/jh7-evolution-api/
```

Alternativas suportadas pelo VS Code:

```text
.github/skills/jh7-evolution-api/
.agents/skills/jh7-evolution-api/
```

Mantenha o nome da pasta exatamente igual ao campo `name` do `SKILL.md`.

## Estrutura

```text
jh7-evolution-api/
├── SKILL.md
├── README.md
├── references/
│   ├── endpoints.md
│   ├── webhooks.md
│   └── security.md
└── assets/
    ├── .env.example
    ├── evolution-server-client.ts
    └── evolution-webhook-handler.ts
```

## Como ativar

No chat do agente, peça uma tarefa relacionada, por exemplo:

```text
Use a skill jh7-evolution-api para criar no backend um serviço de envio de mensagens de texto.
```

No VS Code, ela também pode aparecer como:

```text
/jh7-evolution-api
```

## Primeiro teste

Peça ao agente:

```text
Use jh7-evolution-api. Analise a arquitetura do projeto e diga onde a integração deve ficar sem fazer alterações.
```

O agente deve impedir a exposição da `apikey` no frontend e sugerir backend ou Edge Function.
