# Examples — JH7-DESIGNER-APP

Decisões conceituais. Foco em arquitetura de tela, não em código.

---

## 1. Listagem de clientes

**Errado**

```
Desktop: sidebar + tabela de 8 colunas
Mobile:  sidebar de 180px + a mesma tabela com scroll horizontal,
         fonte 11px, ações em ícones de 16px
```

Isso é um site desktop dentro de um celular.

**Correto**

```
Desktop
┌────────┬──────────────────────────────────────────┐
│Sidebar │ Clientes · 248            [+ Novo]       │
│        │ [🔍] [Status ▾] [Plano ▾] · Limpar       │
│        │ ┌──────────────────────────────────────┐ │
│        │ │Cliente │Tel│Plano│Status│Compra│ ⋯  │ │
│        │ └──────────────────────────────────────┘ │
│        │ 1–20 de 248                     ‹ 1 2 › │
└────────┴──────────────────────────────────────────┘
Clicar na linha abre drawer lateral com o detalhe, sem sair da lista.

Mobile
┌─────────────────────┐
│ Clientes      🔍 ⋯  │  App Header
│ [Todos][Ativos][…]  │  chips roláveis + "Filtros (2)"
│ ┌─────────────────┐ │
│ │JS João Silva  › │ │  lista: nome, plano, status, data
│ │   Pro · ● Ativo │ │
│ └─────────────────┘ │
│ ┌─────────────────┐ │
│ │MA Maria A.    › │ │
│ └─────────────────┘ │
│               ( + ) │  FAB
├─────────────────────┤
│ 🏠  👥  📁  💰  ⋯   │  Bottom Nav
└─────────────────────┘
Filtros abrem em Bottom Sheet. Toque no item abre página full-screen de detalhe.
```

A arquitetura muda: tabela → lista, filtros à vista → sheet, botão no header → FAB, sidebar → Bottom Nav.

---

## 2. Cadastro de cliente

**Errado**

Modal de 640px com 22 campos, usado igual em desktop e mobile. No celular o modal fica maior que a tela, o botão salvar some atrás do teclado e o usuário perde o contexto.

**Correto**

```
Desktop: modal 640px ou página com 2 colunas
         Dados principais | Endereço | Comercial (seções)
         Ações no rodapé do modal: [Cancelar] [Salvar]

Mobile:  página full-screen
         Header: [Cancelar]  Novo cliente
         Etapa 1 de 3 ──────○──○
         Campos em 1 coluna, teclado correto por tipo
         Rodapé fixo: [ Continuar ]  (respeitando safe area e --kb-inset)
```

Formulário longo no mobile vira wizard. Salvar rascunho ao sair.

---

## 3. Dashboard financeiro

**Errado**

Mobile recebendo os 9 widgets do desktop empilhados em coluna única, com gráficos de 400px de largura comprimidos e legendas ilegíveis. Usuário rola por 6 telas para achar o saldo.

**Correto**

```
Desktop
┌──────┬──────┬──────┬──────┐  4 KPIs
├──────┴───┬──┴──────┴──────┤
│ Fluxo    │  A receber     │  gráfico 8col + lista 4col
├──────────┴────────────────┤
│ Últimos lançamentos       │  tabela
└───────────────────────────┘

Mobile
[ Agosto ▾ ]                  contexto primeiro
┌─────────────────────┐
│ Saldo atual         │       KPI principal em destaque
│ R$ 128.450,00       │
│ ▲ 12,4% vs jul      │
└─────────────────────┘
┌─────────┬─────────┐         KPIs secundários 2 col
│A receber│ A pagar │
└─────────┴─────────┘
Fluxo de caixa            gráfico 200px, 4 marcas de eixo,
[gráfico]                 valor destacado em texto acima
Últimos lançamentos       lista de 5 itens
[ Ver todos ]             drill-down
```

Prioridade, não empilhamento.

---

## 4. Ações de um registro

**Errado**

Seis ícones de 16px lado a lado no fim da linha da tabela, replicados no card mobile. No celular ninguém acerta o botão certo.

**Correto**

```
Desktop: 2 ações frequentes visíveis (Editar, Ver) + menu ⋯ com o resto
         Ações extras podem aparecer no hover da linha,
         mas o menu ⋯ fica sempre visível para acesso por teclado.

Mobile:  toque no item abre o detalhe
         menu ⋯ no item abre Bottom Sheet:
         ┌──────────────────────┐
         │        ▁▁▁▁          │
         │ João Silva           │
         │ ✏️  Editar           │
         │ 📄  Ver histórico    │
         │ 📤  Enviar mensagem  │
         │ 🚫  Inativar         │
         │ 🗑️  Excluir (danger) │
         └──────────────────────┘
         Swipe no item pode revelar Editar/Arquivar — sempre com equivalente no menu.
```

---

## 5. Filtros

**Errado**

Barra de filtros do desktop com 5 selects mantida no mobile, quebrando em 3 linhas e ocupando 40% da tela antes do primeiro registro.

**Correto**

```
Desktop: [🔍 Buscar] [Status ▾] [Plano ▾] [Período ▾] [Mais filtros] · Limpar
         Chips do que está aplicado abaixo.

Mobile:  header com 🔍   +   linha de chips rolável
         [ Filtros (2) ] abre Bottom Sheet com o conjunto completo
         e botão "Aplicar filtros" fixo no rodapé do sheet.
```

Estado dos filtros na URL nos dois casos.

---

## 6. Detalhe do registro

**Errado**

Mesma página de detalhe com 5 abas e grid de 3 colunas, apenas empilhada no mobile — 12 telas de rolagem.

**Correto**

```
Desktop
┌────────┬──────────────────────────────────┐
│Sidebar │ ← Clientes / João Silva  [Editar]│
│        │ ┌────────────┬──────────────────┐│
│        │ │ Resumo     │ Timeline         ││
│        │ │ Dados      │ (atividades)     ││
│        │ │ Financeiro │                  ││
│        │ └────────────┴──────────────────┘│
└────────┴──────────────────────────────────┘

Mobile
┌─────────────────────┐
│ ←  João Silva    ⋯  │
│  ┌───┐              │
│  │JS │ Plano Pro    │  cabeçalho de identidade
│  └───┘ ● Ativo      │
│  [Ligar][WhatsApp]  │  ações rápidas em linha
│ ─────────────────── │
│ Resumo │ Dados │ …  │  tabs roláveis
│                     │
│ conteúdo da aba     │
└─────────────────────┘
```

Ações de contato ficam ao alcance do polegar. Informação secundária atrás de tabs, não empilhada.

---

## 7. Navegação com muitos módulos

**Errado**

Bottom Navigation com 8 itens de 11px, ilegível e impossível de acertar. Ou sidebar desktop reduzida a 60px no celular, roubando espaço do conteúdo.

**Correto**

```
Mobile: Bottom Nav com 4 destinos mais usados + "Mais"
        🏠 Início · 👥 Clientes · 📋 Pedidos · 💰 Financeiro · ⋯ Mais

        "Mais" abre uma página de menu (não um sheet apertado)
        com os módulos agrupados por área, busca no topo,
        acesso a Configurações e Perfil.

Desktop: sidebar completa com grupos (Operação, Financeiro, Cadastros, Admin)
         + command palette (⌘K) para acesso rápido a qualquer módulo.
```

---

## 8. Confirmação de ação crítica

**Errado**

`window.confirm('Tem certeza?')` — sem contexto, sem identidade visual, sem informar consequência.

**Correto**

```
┌──────────────────────────────┐
│            ⚠️                 │  ícone contextual com animação sutil
│  Inativar João Silva?        │
│                              │
│  O cliente deixará de        │
│  aparecer nas listagens e    │
│  não receberá novas          │
│  cobranças. Você pode        │
│  reativar depois.            │
│                              │
│  [ Cancelar ] [ Inativar ]   │  foco inicial em Cancelar
└──────────────────────────────┘

Mobile: mesmo conteúdo como Bottom Sheet, botões em largura total,
        ação destrutiva abaixo, respeitando safe area.
```

Exclusão definitiva: exigir digitar o nome do registro. Ação reversível: toast com "Desfazer".

---

## 9. Estados de uma listagem

```
Primeira carga     → skeleton de 6 itens com a forma do item real
Sem nenhum dado    → "Nenhum cliente cadastrado" + [Adicionar cliente]
Busca sem resultado→ "Nenhum cliente para 'joão silva'" + [Limpar filtros]
Erro de rede       → "Não foi possível carregar" + [Tentar novamente]
Sem permissão      → mensagem explicativa, sem CTA (e o módulo nem deveria aparecer no menu)
Carregando mais    → skeleton de 2 itens no fim da lista
Salvando           → loading no próprio botão, tela não trava
```

Cada estado é diferente. "Nenhum resultado" com botão "Adicionar cliente" confunde: o cliente pode existir e estar fora do filtro.

---

## 10. Tabela que precisa continuar tabela

Nem todo caso vira lista. Extrato contábil onde o usuário compara colunas:

```
Desktop: tabela completa, totais no rodapé, exportação

Mobile:  duas visões com alternância explícita
         [ Lista ] [ Tabela ]

         Lista  → itens com data, descrição, valor e saldo (padrão)
         Tabela → scroll horizontal com primeira coluna sticky,
                  indicador de que há mais conteúdo à direita,
                  totais fixos no rodapé
```

Scroll horizontal como escolha consciente do usuário, não como falha de adaptação.

---

## 11. Multiempresa

```
Desktop: seletor de empresa no topo da sidebar, com busca quando há muitas.
         Empresa ativa visível em todo momento.

Mobile:  empresa ativa no App Header (nome curto + chevron),
         toque abre Bottom Sheet com a lista e busca.
         Trocar de empresa mostra feedback claro e recarrega os dados
         sem deixar o usuário em dúvida sobre qual contexto está vendo.
```

Nunca deixe o usuário sem saber em qual empresa está operando — é a principal fonte de erro grave nesse tipo de sistema.

---

## 12. Resumo das transformações

| Desktop | Mobile |
|---|---|
| Sidebar fixa | Bottom Navigation + "Mais" |
| Topbar completa | App Header 56px compacto |
| Tabela de N colunas | Lista/cards com 3–4 informações |
| Barra de filtros | Chips + Bottom Sheet de filtros |
| Botão no page header | FAB ou sticky action bar |
| Modal centralizado | Bottom Sheet ou página full-screen |
| Dropdown / popover | Bottom Sheet |
| Drawer lateral de detalhe | Página full-screen de detalhe |
| Grid 3–4 colunas | 1 coluna (KPIs em 2) |
| Paginação numérica | Carregar mais / scroll infinito |
| Ações no hover | Menu ⋯ / long-press / swipe |
| Tooltip | Hint visível ou sheet |
| Atalhos de teclado | Gestos com alternativa por botão |
