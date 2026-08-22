# Desktop App — JH7-DESIGNER-APP

No desktop o app aproveita espaço horizontal, densidade e precisão do mouse/teclado. Desktop não é mobile esticado.

---

## 1. Layout base

```
┌──────────┬─────────────────────────────────────────┐
│          │  Topbar: breadcrumb · busca · user      │
│ Sidebar  ├─────────────────────────────────────────┤
│  240px   │  Page header: título · meta · ações     │
│          ├─────────────────────────────────────────┤
│          │                                         │
│          │  CONTEÚDO (max-width controlado)        │
│          │                                         │
└──────────┴─────────────────────────────────────────┘
```

```css
.app-desktop {
  display: grid;
  grid-template-columns: var(--sidebar-w, 240px) 1fr;
  height: 100vh;
}
.app-desktop[data-collapsed='true'] { --sidebar-w: 72px; }
.app-desktop__main { display: grid; grid-template-rows: auto 1fr; overflow: hidden; }
.app-desktop__content { overflow-y: auto; }
```

Largura de conteúdo: listas e tabelas podem usar 100%; formulários e leitura ficam melhores com `max-width: 720–880px`. Dashboards podem ir até 1440–1600px com centralização.

---

## 2. Sidebar

Três estados: **fixa** (240–280px), **compacta** (64–72px, só ícones com tooltip), **recolhível** (drawer sobre o conteúdo em telas menores).

Estrutura:

```
┌──────────────────┐
│ Logo / Empresa   │  ← seletor de empresa em multiempresa
├──────────────────┤
│ ▸ Início         │
│                  │
│ OPERAÇÃO         │  ← label de grupo, 12px, muted
│ ▸ Clientes       │
│ ▸ Pedidos        │
│ ▸ Produtos       │
│                  │
│ FINANCEIRO       │
│ ▸ Contas         │
│ ▸ Relatórios     │
├──────────────────┤
│ Configurações    │
│ [avatar] Usuário │  ← menu de conta
└──────────────────┘
```

Regras:

- item ativo com background sutil + cor primária + indicador lateral (barra 3px) — não só cor de texto;
- ícone 18–20px alinhado, label 14px;
- altura de item 40px, gap 2–4px;
- grupos com label quando há mais de ~7 itens;
- submenu expansível com animação de altura; mantenha aberto o grupo da rota atual;
- estado colapsado persistido (localStorage) e com tooltip em cada ícone;
- rolagem interna própria quando a lista é longa, com header e footer fixos;
- **itens sem permissão não aparecem** — ocultar, não desabilitar;
- badge de contagem alinhado à direita.

---

## 3. Topbar

Complementa a sidebar. Deve conter no máximo:

- breadcrumb ou título do contexto;
- busca global (`⌘K` / `Ctrl+K`);
- seletor de empresa/período quando global;
- notificações;
- tema;
- menu do usuário.

Altura 56–64px, sticky, borda inferior discreta. Não duplique navegação que já está na sidebar.

---

## 4. Page header

Padrão para toda página de módulo:

```
Clientes                                    [Exportar] [+ Novo cliente]
248 registros · 12 inativos
```

- título 24–30px, contador/meta em `muted` 14px;
- ação primária à direita, secundárias como botão outline ou menu `⋯`;
- tabs de visão logo abaixo quando existirem ("Todos · Ativos · Inativos");
- breadcrumb acima em hierarquias profundas.

---

## 5. Dashboards desktop

Grade de referência em 12 colunas:

```
┌──────┬──────┬──────┬──────┐
│ KPI  │ KPI  │ KPI  │ KPI  │   4 cards × 3 col
├──────┴──────┴──┬───┴──────┤
│  Gráfico       │  Ranking │   8 col + 4 col
├────────────────┴──────────┤
│  Tabela de últimos itens  │   12 col
└───────────────────────────┘
```

- KPI card: label, valor grande com `tabular-nums`, variação com sinal e cor, sparkline opcional;
- gráficos com legenda, tooltip no hover e seletor de período;
- filtros globais no topo, aplicados a todos os widgets;
- estados vazios por widget, não uma tela vazia inteira;
- evite mais de 8 widgets em uma tela; agrupe por abas quando passar disso.

---

## 6. Tabelas desktop

É aqui que o desktop ganha do mobile. Recursos esperados:

- header sticky ao rolar;
- ordenação por coluna com indicador;
- seleção múltipla com checkbox + barra de ações contextual;
- densidade ajustável (compacta / normal);
- coluna de ações à direita (ícones + menu `⋯`);
- primeira coluna sticky em tabelas largas;
- paginação com total e itens por página, ou scroll infinito com contador;
- alinhamento: texto à esquerda, números e valores à direita com `tabular-nums`;
- linha inteira clicável abrindo detalhe/drawer, com `cursor: pointer` e hover na linha;
- truncamento com tooltip no conteúdo longo;
- skeleton de linhas no loading, preservando a estrutura;
- coluna de status como badge, nunca texto solto colorido;
- opção de escolher colunas visíveis quando há muitas.

Não use tabela quando o dado é hierárquico ou visual — considere lista rica, kanban ou cards.

---

## 7. Filtros

Desktop tem espaço para filtros à vista:

```
[🔍 Buscar]  [Status ▾]  [Plano ▾]  [Período ▾]  [Mais filtros]  ·  Limpar
```

- filtros aplicados aparecem como chips removíveis;
- resultado atualiza sem recarregar a página;
- filtros na URL (query string) para permitir link e voltar;
- "Mais filtros" abre painel lateral (drawer) com o conjunto completo;
- combo de busca com debounce ~300ms e indicador de carregamento no campo;
- salvar visão/filtro favorito é um diferencial em CRM e ERP.

---

## 8. Detalhe: página, drawer ou modal

| Situação | Escolha |
|---|---|
| consulta rápida sem sair da lista | drawer lateral (420–560px) |
| edição curta | modal médio |
| registro com muitas seções e abas | página dedicada |
| comparar itens da lista | drawer, mantendo a lista visível |

Drawer lateral é o padrão mais produtivo em CRM: navega entre registros com ←/→ sem perder o contexto da lista.

---

## 9. Padrões de produtividade

- **Command palette** (`⌘K`) para navegar e executar ações;
- atalhos: `/` foca busca, `n` novo registro, `Esc` fecha overlay, `⌘S` salva, `?` lista atalhos;
- ações em massa com confirmação e contagem ("Inativar 12 clientes?");
- edição inline em campos simples da tabela;
- auto-save com indicador de estado ("Salvo às 14:32") em telas de configuração;
- undo em ações reversíveis via toast ("Cliente arquivado · Desfazer");
- copiar para clipboard com feedback em IDs e códigos;
- dados que atualizam em tempo real sem refresh, com indicação discreta de atualização;
- foco visível em toda navegação por teclado; ordem de tab coerente.

---

## 10. Hover e cursor

Recursos exclusivos do desktop — use:

- hover em linha, card e item de menu;
- tooltip em ícone sem label (delay ~400ms);
- cursor coerente: `pointer` em clicável, `not-allowed` em disabled, `grab` em arrastável;
- área de ações que aparece no hover da linha (mas mantenha o menu `⋯` sempre visível para acesso por teclado).

Nunca esconda uma ação exclusivamente no hover sem alternativa acessível por teclado.

---

## 11. Multi-painel e workspaces

Telas de trabalho intensivo (kanban, inbox, editor) podem usar 2–3 painéis:

```
┌────────┬──────────────┬──────────┐
│ Lista  │  Conteúdo    │ Detalhes │
└────────┴──────────────┴──────────┘
```

- painéis redimensionáveis com persistência da largura;
- painel de detalhes colapsável;
- abaixo de 1280px, reduza para 2 painéis; abaixo de 1024px, um painel com navegação.

Kanban: colunas com scroll próprio, header de coluna com contagem e total, drag com placeholder e alternativa por menu ("Mover para...").

---

## 12. O que não fazer no desktop

- transformar o desktop em coluna única gigante de cards mobile;
- deixar largura de leitura acima de ~90 caracteres;
- usar Bottom Navigation no desktop;
- FAB flutuante no desktop quando existe botão no page header;
- modal em tela cheia para uma confirmação simples;
- desperdiçar a lateral inteira e concentrar tudo em uma coluna estreita;
- densidade tão alta que a tela vira planilha ilegível.
