# Components — JH7-DESIGNER-APP

Especificação dos componentes de um aplicativo web. Antes de criar: verifique se o projeto já tem o componente e estenda o existente. Evite abstração prematura — só crie componente quando houver reuso real ou complexidade que justifique.

Todo componente precisa contemplar: variantes, tamanhos, estados (default, hover, focus-visible, active/press, disabled, loading, error), comportamento mobile e acessibilidade.

---

## AppShell

Casca da aplicação, decide a arquitetura por viewport.

- Mobile: `MobileHeader` + conteúdo + `BottomNavigation`, `height: 100dvh`, grid 3 linhas.
- Tablet: topbar + sidebar colapsável (drawer).
- Desktop: `DesktopSidebar` fixa + topbar + conteúdo.

Responsabilidades: rolagem apenas na área de conteúdo, safe areas, provider de tema, região de toast, controle de overlays abertos.

---

## MobileHeader

Props: `title`, `showBack`, `onBack`, `actions` (máx. 2), `overflowItems`, `transparentOnTop`.

Altura 56px + safe-area-top. Título com ellipsis. Botão de voltar com alvo 44x44. Sticky com blur quando o conteúdo rola sob ele. `<header>` semântico.

---

## DesktopSidebar

Props: `items` (com `icon`, `label`, `to`, `badge`, `group`, `permission`), `collapsed`, `onToggle`, `footer`.

Item ativo: background sutil + cor primária + barra indicadora de 3px. Estado colapsado com tooltip e persistência. Itens sem permissão não são renderizados. `<nav aria-label="Navegação principal">`, `aria-current="page"` no ativo.

---

## BottomNavigation

Props: `items` (3–5, com `icon`, `iconActive`, `label`, `to`, `badge`).

Altura 56–64px + safe-area-bottom, fixo, z-index de sidebar. Ícone 24px + label 11–12px. Ativo por cor + peso + ícone preenchido. Tocar no item ativo rola ao topo. Só renderiza abaixo de 768px.

---

## PageHeader

Props: `title`, `subtitle`, `meta` (contador), `breadcrumb`, `primaryAction`, `secondaryActions`, `tabs`.

Desktop: título à esquerda, ações à direita. Mobile: absorvido pelo `MobileHeader` e a ação primária migra para FAB ou sticky bar.

---

## Button

Variantes: `primary`, `secondary`, `outline`, `ghost`, `danger`, `link`.
Tamanhos: `sm` (32px), `md` (40px), `lg` (48px). No mobile, mínimo `md`; ação primária em `lg`.

Props: `loading`, `disabled`, `iconLeft`, `iconRight`, `fullWidth`.

Requisitos: radius 12px, transição 150ms, `focus-visible` com ring de 2px e offset, press `scale(0.97)`, loading com spinner mantendo a largura e `aria-busy`, disabled com `cursor: not-allowed` e sem perda total de contraste. `danger` sempre acompanhado de confirmação.

---

## IconButton

Quadrado 40x40 (mobile 44x44), ícone 20px, radius 12px. `aria-label` obrigatório. Tooltip no desktop. Variante `ghost` como padrão em headers e linhas de tabela.

---

## Card

Props: `padding`, `interactive`, `elevated`, `header`, `footer`.

`surface` + `border` + radius 16px. Padding 20–24px no desktop, 16px no mobile. Quando `interactive`, renderiza como `<button>`/`<a>` com hover no desktop e press state no mobile, e tem chevron ou ação evidente.

---

## StatCard (KPI)

Estrutura: label (`muted`, 13px) → valor (`display`, `tabular-nums`) → variação (sinal + cor + período) → sparkline opcional → ícone contextual.

Variação positiva não é sempre verde: em despesa, alta é ruim. Use prop `invertTrend`. Skeleton mantém a altura exata. Clicável quando existe drill-down.

---

## Input

Estrutura: label acima (obrigatória), campo, hint, mensagem de erro.

Altura 40px desktop / 44–48px mobile. `font-size: 16px` no mobile. Radius 12px. Borda `border`, foco com `ring`. Erro: borda `danger` + ícone + texto abaixo, com `aria-invalid` e `aria-describedby`. Suporta `prefix`/`suffix` (R$, %, ícone de busca) e `clearable`.

Placeholder nunca substitui label.

---

## FormField

Wrapper que conecta label, controle, hint e erro por id, marca obrigatoriedade (`*` + `aria-required`) e mantém o espaçamento consistente. Todo campo do app passa por ele.

---

## Select / Combobox

Desktop: popover com lista, navegação por setas, busca quando há mais de ~10 opções, Enter seleciona, Esc fecha.
Mobile: Bottom Sheet com itens de 48px, busca no topo quando a lista é longa.

Multi-seleção mostra chips removíveis. Estados: loading das opções, vazio ("Nenhum resultado"), criação de novo item quando aplicável.

---

## Table (desktop)

Props: `columns`, `rows`, `sort`, `onSort`, `selection`, `rowActions`, `density`, `loading`, `empty`, `pagination`, `onRowClick`.

Header sticky, hover na linha, checkbox de seleção com barra de ações contextual, ações à direita, números com `tabular-nums` alinhados à direita, truncamento com tooltip, skeleton de linhas, estado vazio dentro do container. `<table>` semântica com `<th scope="col">` e `aria-sort`.

---

## MobileList / MobileListItem

Props do item: `leading` (avatar/ícone), `title`, `subtitle`, `meta`, `status`, `trailing` (chevron ou ação), `onPress`, `swipeActions`.

Altura mínima 64px, padding 16px, divisor recuado alinhado ao texto, máximo 3 linhas de texto, press state obrigatório, item é elemento interativo real. Agrupamento por seção com header sticky.

É o substituto de `Table` no mobile.

---

## Modal / Dialog

Props: `size` (`sm` 400 / `md` 560 / `lg` 720), `title`, `description`, `footer`, `mobileBehavior` (`sheet` | `fullscreen` | `dialog`).

Overlay com fade, conteúdo com scale+fade, foco preso, retorno do foco ao fechar, Esc fecha, scroll do body travado. `role="dialog"`, `aria-modal`, `aria-labelledby`, `aria-describedby`.

Confirmação de ação crítica: ícone contextual animado, título direto, texto explicando a consequência, botões "Cancelar" e ação nomeada (não "OK"), variante `danger` quando destrutivo.

---

## BottomSheet

Props: `open`, `onClose`, `title`, `snapPoints`, `dismissible`, `footerAction`.

Radius 24px no topo, grabber, `max-height: 90dvh`, scroll interno, `translateY` 240–280ms, swipe-down para fechar, safe-area no rodapé, botão do Android fecha o sheet antes de navegar.

---

## Drawer

Lateral (desktop, 420–560px) para detalhes e filtros avançados; inferior no mobile (vira `BottomSheet`).

Slide-in 250ms, overlay opcional, navegação entre registros com ←/→ quando abre um item de lista, header com título e fechar, ações fixas no rodapé.

---

## Tabs

Desktop: underline com indicador animado. Mobile: tabs roláveis horizontalmente com snap, ou segmented control quando são 2–3 opções.

`role="tablist"`, setas navegam, indicador nunca só por cor, conteúdo com `role="tabpanel"`. Aba ativa na URL quando representa uma visão.

---

## Badge

Variantes: `neutral`, `success`, `warning`, `danger`, `info`, `primary`.
Estilo: fundo `-surface`, texto `-foreground`, radius full, 12px, padding 2/8, opcional dot à esquerda.

Status precisa de texto — cor sozinha não comunica. Contagem em navegação usa badge circular com `aria-label` descritivo.

---

## Toast

Props: `variant`, `title`, `description`, `action` (ex.: Desfazer), `duration`.

Desktop: canto superior direito ou inferior direito. Mobile: acima da Bottom Nav e safe area. Sucesso 3–4s; erro persistente com fechar. Fila de um por vez. `role="status"` / `role="alert"`. Nunca cobre ação primária ou teclado.

---

## Skeleton

Formas: `text` (linhas com larguras variadas), `circle` (avatar), `rect` (card, gráfico), `list` (n itens), `table` (n linhas).

Deve reproduzir a geometria do conteúdo final para não haver salto de layout. Shimmer suave; com `prefers-reduced-motion`, fundo estático.

---

## EmptyState

Props: `icon`, `title`, `description`, `action`, `secondaryAction`.

Título curto, uma frase de orientação, CTA claro ("Adicionar cliente"). Variantes: sem dados (com CTA), sem resultado de busca (com "Limpar filtros"), sem permissão (explicativo, sem CTA), erro (com "Tentar novamente").

---

## SearchBar

Ícone de lupa, `type="search"`, `inputmode="search"`, `enterkeyhint="search"`, debounce 300ms, botão de limpar, indicador de carregamento no campo, resultado com contagem. Desktop: `⌘K`/`/` foca. Mobile: pode expandir a partir do ícone no header.

---

## FilterBar

Desktop: busca + selects + chips de filtros aplicados + "Limpar". Mobile: botão "Filtros" com contador que abre Bottom Sheet, e chips roláveis mostrando o que está aplicado.

Estado dos filtros na URL. Sempre existe caminho para limpar tudo.

---

## FloatingActionButton

56x56, radius full, elevação, ícone 24px, `aria-label`. Posicionado acima da Bottom Nav + safe area. Só mobile, só uma ação primária de criação, nunca destrutiva, nunca junto de `StickyActionBar`.

---

## StickyActionBar

Barra fixa inferior com a ação primária da tela. Background `surface`, borda superior, `padding-bottom` com safe area, ajuste por `--kb-inset` quando o teclado abre, botão em largura total com loading interno.

---

## Avatar

Tamanhos 24/32/40/48/64. Imagem com fallback de iniciais em cor derivada do nome. `AvatarGroup` com sobreposição e "+N". `alt` com o nome ou `aria-hidden` quando o nome já está ao lado.

---

## Tooltip

Só desktop (`@media (hover: hover)`). Delay 400ms, 12px, `surface-elevated`, seta opcional. Nunca contém informação essencial nem ação. No mobile, a informação vira hint visível ou sheet.

---

## Pagination

Desktop: total de registros, itens por página, navegação numérica com primeira/última. Mobile: "Carregar mais" ou scroll infinito com sentinela; evite paginação numérica no toque.

---

## ConfirmDialog

Wrapper de `Modal` para ações críticas (ativar/inativar, excluir, enviar). Ícone animado contextual, mensagem explicando consequência e reversibilidade, botão com o verbo da ação, loading no confirmar, foco inicial no botão seguro. Em ação irreversível de alto impacto, exija digitar o nome do registro.
