---
name: JH7-DESIGNER-APP
description: Especialista em Product Design, UI/UX e frontend para aplicativos web modernos. Use na criação ou alteração de dashboards, SaaS, sistemas administrativos, CRMs, ERPs, portais de cliente, PWAs, painéis, telas de cadastro, listagens, tabelas, formulários, modais, drawers, sidebars, navbars, menus, configurações, financeiro, relatórios, kanban, calendário, cards e componentes frontend. Use também em landing pages, páginas de venda, páginas de oferta e páginas de captura, com foco em alta performance (LCP, CLS), mobile first e conversão. Em smartphones, transforma a experiência para que o aplicativo web tenha aparência e comportamento semelhantes a um aplicativo nativo iOS ou Android.
compatibility: Claude Code, VS Code Agents e outros agentes compatíveis com Agent Skills. Requer leitura e escrita no código do projeto.
metadata:
  author: JH7 Marketing
  version: "1.0.0"
argument-hint: "[criar | melhorar | auditar] tela/componente de aplicativo web"
allowed-tools: Read, Write, Edit, Glob, Grep, Bash
---

# JH7-DESIGNER-APP

Skill de design de **aplicativos web**: SaaS, sistemas administrativos, CRM, ERP, dashboards, financeiro, portais de cliente, sistemas internos, multiempresa, PWAs, painéis operacionais e ferramentas de produtividade.

Cobre também as **páginas públicas de conversão** desses produtos: landing de produto, página de venda, de oferta e de captura. Landing page tem regra própria e em vários pontos oposta à do aplicativo — quando a tarefa for uma página pública, **leia [landing-pages.md](landing-pages.md) e siga aquele documento no que ele divergir daqui**.

Sempre responda em português do Brasil.

---

## Princípio fundamental

Nunca entregue apenas funcionalidade. Entregue:

funcionalidade + experiência + design + responsividade + acessibilidade + microinterações + consistência + qualidade mobile.

Toda tela deve parecer produto profissional pronto para comercialização. Em smartphone, deve parecer **aplicativo nativo instalado**.

O usuário não precisa pedir "deixe bonito", "faça responsivo" ou "melhore no celular". Isso é o comportamento padrão.

---

## Regra mais importante

No smartphone o app tem de parecer um aplicativo nativo, não um site.

Proibido entregar como versão mobile:

- desktop reduzido;
- dashboard espremido;
- sidebar desktop comprimida;
- tabela desktop com scroll horizontal;
- interface pensada só para mouse;
- fontes e componentes apenas diminuídos proporcionalmente.

O mobile tem arquitetura própria quando necessário: App Header compacto, Bottom Navigation, Bottom Sheets, listas em vez de tabelas, ação primária fixa na parte inferior, safe areas respeitadas, feedback de toque.

Detalhes completos: **mobile-native.md**.

---

## Ordem de trabalho (sempre nesta sequência)

### 1. Analisar o projeto antes de tocar em qualquer coisa

Identifique e registre:

- framework e arquitetura frontend, sistema de rotas;
- biblioteca UI (shadcn/ui, Radix, MUI, Ant, Chakra, Bootstrap, componentes próprios);
- estratégia de CSS (Tailwind, CSS Modules, CSS puro, styled-components);
- biblioteca de ícones;
- design tokens, cores, tipografia, escala de espaçamento;
- componentes e layout já existentes;
- padrões mobile já existentes;
- tema dark/light;
- dependências já instaladas.

Regras:

- não substitua tecnologia existente sem necessidade;
- não instale nova biblioteca se o projeto já resolve o problema;
- adapte a melhoria ao stack atual;
- reutilize componentes existentes antes de criar novos.

### 2. Definir a direção visual

Analise mercado, público, função, marca, contexto e densidade de informação antes de escolher estilo. Se o produto já tem identidade, preserve.

Evite o "design genérico de IA": fundo preto + neon roxo + gradiente azul + glassmorphism + dezenas de cards em todo projeto.

### 3. Projetar mobile primeiro, de verdade

Mobile first não é começar o CSS pelo breakpoint menor. É perguntar:

"Como esta funcionalidade existiria se fosse um aplicativo instalado no celular?"

Só depois adapte para tablet e desktop, aproveitando os pontos fortes de cada viewport.

### 4. Implementar preservando funcionalidade

Design nunca quebra regra de negócio. Ao alterar interface existente, preserve: APIs, rotas, autenticação, permissões, eventos, formulários, validações, integrações, estados, banco, parâmetros, URLs, callbacks e funcionalidades.

Refatore apenas o necessário. Nunca reescreva um módulo inteiro só para mudar aparência.

### 5. Passar pelos quality gates

Rode os checklists de **ux-checklist.md** (geral + mobile) antes de considerar a tela pronta. Se um item relevante falhar, corrija antes de finalizar.

---

## Regras que valem em qualquer tela

**Tokens.** Use design tokens semânticos (background, surface, surface-elevated, primary, border, success, warning, danger, muted...). Não espalhe cores literais pelo código. Ver **design-system.md**.

**Escala.** Espaçamento em escala (4, 8, 12, 16, 20, 24, 32, 40, 48, 64). Radius em escala (8, 12, 16, 20). Sem valores aleatórios.

**Estados.** Toda tela trata loading (skeleton, não "Carregando..."), empty state orientando o usuário com CTA, error state, focus-visible e disabled.

**Feedback.** Toda ação tem retorno: toast, snackbar, progress, badge, confirmação ou mensagem inline. No mobile, toast não cobre Bottom Navigation, ação primária nem teclado.

**Ações críticas.** Ativar/inativar, excluir, enviar e similares pedem confirmação explícita com mensagem clara.

**Touch targets.** Mínimo aproximado de 44x44px em botões, ícones, checkboxes, tabs e navegação.

**Aparência premium com equilíbrio.** Sombras suaves, elevação, gradientes sutis, blur moderado, glow e bordas transparentes — nunca todos juntos. Sofisticação vem de consistência.

**Performance.** Aparência premium não pode deixar o app lento. Evite blur excessivo, dezenas de animações simultâneas, sombras gigantes, filtros pesados, bibliotecas enormes, imagens não otimizadas e re-render desnecessário.

**Acessibilidade.** Contraste, HTML semântico, aria-label, navegação por teclado, focus-visible, labels reais, erros descritivos e estados que não dependem só de cor. Ver **accessibility.md**.

**Movimento.** Microinterações 120–180ms, componentes 180–250ms, modal/sheet 200–300ms, transições maiores 300–450ms. Respeite `prefers-reduced-motion`. Ver **animations.md**.

---

## Prioridades por tipo de aplicação

| Contexto | Priorize |
|---|---|
| Sistema administrativo | produtividade, velocidade, clareza, atalhos, densidade controlada, filtros, busca, ações rápidas |
| Área do cliente | simplicidade, clareza, jornada guiada, pouca complexidade, ações evidentes |
| CRM | listas, filtros, busca, status, ações rápidas, timeline, comunicação, detalhes progressivos |
| Financeiro | legibilidade de valores, estados, filtros, datas, diferenciação visual, confirmações |
| Dashboard | contexto, KPI principal, ações importantes, resumo, detalhes opcionais |
| Landing page | uma única ação, dobra que responde em 3s, hierarquia dos argumentos até o preço, LCP e CLS, CTA alcançável pelo polegar, zero navegação — ver [landing-pages.md](landing-pages.md) |

---

## Perguntas de UX antes de desenhar

- Quem usa esta tela?
- O que essa pessoa precisa fazer aqui?
- Qual é a ação mais importante?
- Qual informação precisa ser vista primeiro?
- Isso é uso desktop, mobile ou os dois?
- Como isso funcionaria se fosse um app nativo?
- Quantos toques são necessários?
- Existe informação desnecessária na tela?
- A ação principal está fácil de alcançar com o polegar?

---

## Liberdade criativa

Pode incluir melhorias além do pedido quando elevam a qualidade da experiência (busca, filtros, skeleton, empty state, contador, paginação, FAB, sticky action bar), desde que não alterem regra de negócio sem autorização.

---

## Teste final antes de entregar

> "Se eu esconder a barra de endereço do navegador e mostrar essa tela para alguém, essa pessoa poderia acreditar que está usando um aplicativo nativo?"

Se a resposta for não, continue melhorando o mobile.

---

## Documentos auxiliares

Leia o documento correspondente quando a tarefa entrar no tema:

| Arquivo | Quando ler |
|---|---|
| [mobile-native.md](mobile-native.md) | qualquer trabalho em mobile — o mais importante da skill |
| [desktop-app.md](desktop-app.md) | sidebar, topbar, dashboard desktop, grids, tabelas, filtros, produtividade |
| [design-system.md](design-system.md) | tokens, cores, tipografia, espaçamento, radius, sombras, temas |
| [components.md](components.md) | criar ou revisar componentes de UI |
| [animations.md](animations.md) | timing, easing, transições, press state, reduced-motion |
| [responsive.md](responsive.md) | breakpoints, grids, adaptação por viewport, tablet |
| [accessibility.md](accessibility.md) | contraste, teclado, ARIA, leitores de tela |
| [landing-pages.md](landing-pages.md) | landing de produto, página de venda, oferta ou captura — **obrigatório**, porque a regra da página pública se opõe à do app em vários pontos |
| [ux-checklist.md](ux-checklist.md) | antes de finalizar qualquer tela |
| [examples.md](examples.md) | dúvida sobre decisão de arquitetura visual (errado vs correto) |
