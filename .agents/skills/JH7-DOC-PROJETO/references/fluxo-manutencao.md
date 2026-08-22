# Fluxo de manutenção do documento

O documento só serve se acompanhar o código. Este é o mapa de "o que eu mudei" → "o que preciso atualizar".

---

## Mapa de impacto

| Alteração no sistema | Seções a atualizar |
| --- | --- |
| Nova tabela, coluna, índice ou constraint | 3 Banco (tabela + diagrama + índice de migrações), 10 Changelog |
| Nova política RLS ou mudança de permissão no banco | 3 Banco, 5 Segurança, 10 Changelog |
| Nova função/view no banco | 3 Banco, 10 Changelog |
| Novo módulo ou nova rota | 2 Arquitetura, 5 Segurança (matriz), 6 Módulos, 10 Changelog |
| Nova regra de negócio | 1 Negócio (nova `RN-xx`), 6 Módulo afetado, 10 Changelog |
| Mudança de regra existente | 1 Negócio (marca a antiga como descontinuada com data e motivo), 6, 10 |
| Novo token de cor/tipografia/espaçamento | 4 Layout, 10 Changelog |
| Novo componente reutilizável | 4 Layout (padrões de componente), 10 Changelog |
| Nova integração externa | 2 Arquitetura (variáveis), 5 Segurança (segredo e proxy), 7 Integrações, 10 Changelog |
| Nova variável de ambiente | 2 Arquitetura, 5 Segurança se for segredo, 10 Changelog |
| Novo bucket ou mudança de acesso a storage | 3 Banco (storage), 5 Segurança, 10 Changelog |
| Nova Edge Function | 2 Arquitetura, 5 Segurança, 7 Integrações, 10 Changelog |
| Correção de bug sem mudança de contrato | 10 Changelog |
| Refatoração sem mudança de comportamento | 8 Convenções se o padrão mudou, senão 10 Changelog |
| Item do roadmap entregue | Move de 9 Roadmap para a seção definitiva, 10 Changelog |

Se a alteração não encaixa em nenhuma linha, ela ainda entra no changelog.

---

## Versionamento

Formato `ano.mes.sequencia` — ex: `2026.08.3` é a terceira entrega de agosto de 2026.

- A sequência reinicia a cada mês.
- O número no cabeçalho do documento, na linha do changelog e no rodapé da aplicação são **o mesmo**. Divergência entre eles é bug.
- O arquivo de versão da aplicação (ex: `src/version.ts`) e o documento são atualizados na mesma tarefa.

---

## Ordem de trabalho em uma alteração

1. Ler `docs/PROJETO.md` (modo Consultar) e conferir se o pedido conflita com algo definido.
2. Se conflita, levantar o conflito com o usuário antes de codificar.
3. Implementar a alteração no código e no SQL.
4. Atualizar as seções do documento conforme o mapa acima.
5. Bater a versão no documento e no rodapé.
6. Rodar o checklist de atualização em `checklists.md`.

---

## Edição segura

- Substitua trechos, não o arquivo. Reescrever o documento inteiro apaga decisões que você não conhece.
- Antes de editar uma seção, leia a seção inteira. Editar sem ler produz duplicidade e contradição.
- Regra revogada vira nota de descontinuação com data e motivo. Histórico não se apaga.
- Se o documento e o código discordarem, investigue qual está certo antes de escrever. Não presuma que o código venceu — pode ser regressão.
- Se a seção passar de ~400 linhas, mova o detalhe para `docs/projeto/<secao>.md` e deixe resumo e link no documento mestre.
