---
name: JH7-DOC-PROJETO
description: Cria e mantém a documentação mestre do projeto (docs/PROJETO.md) — negócio, regras, arquitetura, layout, banco de dados, segurança, integrações, módulos e histórico de versões. Use SEMPRE antes de desenvolver qualquer coisa neste sistema (para consultar o que já está definido e não divergir) e SEMPRE depois de qualquer alteração (para atualizar o documento). Use também quando o usuário pedir documentar o projeto, gerar/atualizar PROJETO.md, revisar a documentação, entender o sistema, mapear módulos, tabelas, permissões ou fluxos.
compatibility: Claude Code, VS Code Agents e outros agentes compatíveis com Agent Skills. Requer acesso de leitura e escrita ao código do projeto.
metadata:
  author: JH7 Marketing
  version: "1.0.0"
argument-hint: "[criar | consultar | atualizar | auditar] documentação do projeto"
---

# JH7-DOC-PROJETO

Skill responsável pela **fonte única de verdade** do sistema: o arquivo `docs/PROJETO.md`.

Sempre responda em português.

---

## Por que esta skill existe

Sem um documento mestre, cada alteração reinventa nomes, cores, regras de negócio e estruturas de tabela. O resultado é um sistema incoerente. Este documento existe para que **qualquer desenvolvimento comece lendo o que já foi decidido** e termine registrando o que mudou.

Duas regras absolutas:

1. **Antes de escrever código** — leia `docs/PROJETO.md` e siga o que está lá.
2. **Depois de alterar o sistema** — atualize `docs/PROJETO.md` no mesmo trabalho. Alteração sem documentação atualizada é trabalho incompleto.

---

## Localização do documento

```text
docs/PROJETO.md          ← documento mestre (obrigatório)
docs/projeto/            ← anexos opcionais quando uma seção crescer demais
  banco-de-dados.md
  modulos.md
  integracoes.md
```

Em monorepo, o documento fica na raiz da aplicação (ex: `JH7-MKT-DIASIMPORTS/docs/PROJETO.md`). Cada aplicação tem o seu.

Divida em anexos **só quando** uma seção passar de ~400 linhas. `docs/PROJETO.md` continua sendo o índice e mantém o resumo de cada seção com link para o anexo.

---

## Modo de operação

Identifique qual dos quatro modos a tarefa pede. Se não estiver claro, pergunte.

| Modo | Quando | O que fazer |
| --- | --- | --- |
| **Criar** | `docs/PROJETO.md` não existe | Levantamento completo do código e escrita do documento inteiro |
| **Consultar** | Antes de desenvolver qualquer coisa | Ler o documento, extrair as regras aplicáveis, apontar conflitos com o pedido |
| **Atualizar** | Depois de qualquer alteração no sistema | Editar cirurgicamente as seções afetadas + changelog |
| **Auditar** | Suspeita de documento desatualizado | Comparar documento × código e listar divergências |

---

## Modo Criar

Não escreva o documento de memória nem por suposição. Cada afirmação precisa vir do código.

### 1. Levantamento

Leia, nesta ordem:

1. `AGENTS.md`, `CLAUDE.md`, `README.md` — regras impostas pelo dono do projeto. **Elas têm prioridade sobre qualquer padrão seu.**
2. `package.json` — stack, scripts, dependências, versão.
3. Configuração de build e ambiente (`vite.config.ts`, `tsconfig*.json`, `.env.example`, `supabase/config.toml`).
4. Migrações SQL em ordem numérica — é ali que está a verdade do banco, incluindo RLS e funções.
5. Rotas e layout principal (ex: `App.tsx`, `*Layout.tsx`) — mapeia os módulos reais.
6. `src/contexts/**` — estado global, autenticação, tema, configurações.
7. `src/lib/**` — camada de acesso a dados e integrações externas.
8. `src/pages/**` e `src/components/**` — telas e comportamento de interface.
9. `supabase/functions/**` — o que roda no servidor e por quê (normalmente por causa de segredo).
10. Tokens de estilo (`styles/theme.css` ou equivalente) — paleta, tipografia, espaçamento.

Para levantamento amplo, delegue a leitura a subagentes em paralelo e consolide o resultado. Não encha o contexto principal com arquivos inteiros.

### 2. Escrita

Use `assets/PROJETO-TEMPLATE.md` como base. A estrutura obrigatória das seções e o nível de detalhe esperado em cada uma estão em `references/estrutura-documento.md`.

### 3. Fechamento

- Marque com `⚠️ A CONFIRMAR` tudo que você não conseguiu deduzir do código (regra de negócio implícita, decisão de produto, meta comercial). Nunca invente.
- Liste no fim da resposta as perguntas abertas para o usuário responder.

---

## Modo Consultar

Este é o modo mais usado. Antes de implementar qualquer coisa:

1. Leia `docs/PROJETO.md`. Se não existir, avise e ofereça criá-lo antes de seguir.
2. Extraia apenas o que se aplica à tarefa: módulo envolvido, tabelas, permissões, tokens de estilo, padrões de UI, integrações.
3. **Se o pedido do usuário contradiz o documento, diga isso antes de codificar.** Ex: "o documento define que preço promocional é opcional e nunca maior que o preço cheio; o pedido inverte essa regra. Confirma a mudança de regra?"
4. Implemente seguindo o documento.

Nunca reescreva o documento no modo Consultar.

---

## Modo Atualizar

Toda alteração no sistema exige atualização proporcional do documento. O mapa de "o que mudei → o que atualizar" está em `references/fluxo-manutencao.md`.

Regras da edição:

- **Edite cirurgicamente.** Substitua só os trechos afetados. Não reescreva o arquivo inteiro — isso destrói decisões documentadas que você não conhece.
- **Nunca apague histórico.** Regra que deixou de valer é marcada como descontinuada com a data e o motivo, não removida.
- **Sempre acrescente uma linha ao changelog**, na mesma versão que foi para o rodapé da aplicação.
- **Versão segue `ano.mes.sequencia`** (ex: `2026.08.3`), igual à do rodapé do sistema. Documento e rodapé sempre com o mesmo número.

Checklist antes de encerrar a tarefa: `references/checklists.md`.

---

## Modo Auditar

1. Para cada seção do documento, confirme no código se ainda é verdade.
2. Produza uma tabela: seção · o que o documento diz · o que o código faz · gravidade.
3. Corrija o documento onde o código está certo. Onde o **código** está errado (divergiu de uma decisão de produto), reporte ao usuário em vez de silenciar a regra no documento.

Priorize auditar, nesta ordem: segurança e permissões, banco de dados, integrações, módulos, layout.

---

## Princípios de escrita

- Escreva para alguém que assume o projeto amanhã sem falar com ninguém.
- Descreva **o que é e por quê**, não passo a passo de implementação. Código muda mais rápido que intenção.
- Uma afirmação por linha, verificável. Sem "robusto", "moderno", "de ponta".
- Tabela para tudo enumerável: tabelas do banco, permissões, rotas, variáveis de ambiente, tokens.
- Nome real de arquivo, tabela, coluna, rota e função. Nada genérico.
- **Nunca escreva segredo no documento.** Chave, token, senha e string de conexão entram como nome da variável e onde ela vive. Nada de valor.
- Não documente o que não existe. Ideia futura vai para a seção "Roadmap", claramente separada do que está implementado.

---

## Arquivos de apoio

| Arquivo | Conteúdo |
| --- | --- |
| `references/estrutura-documento.md` | Seções obrigatórias e o que cada uma precisa conter |
| `references/fluxo-manutencao.md` | Mapa tipo de alteração → seções a atualizar |
| `references/checklists.md` | Checklists de criação, atualização e auditoria |
| `assets/PROJETO-TEMPLATE.md` | Esqueleto pronto do `docs/PROJETO.md` |
