# Mobile Native — JH7-DESIGNER-APP

Documento central da skill. Objetivo: no smartphone, o aplicativo web deve parecer um app nativo instalado.

Pergunta que guia tudo: **"como esta funcionalidade existiria se fosse um app na App Store / Play Store?"**

---

## 1. App Shell mobile

Estrutura padrão de um app com navegação principal:

```
┌─────────────────────┐
│  safe-area-top      │
│  App Header (56px)  │  ← compacto, sticky
├─────────────────────┤
│                     │
│                     │
│      CONTEÚDO       │  ← única área com scroll
│                     │
│                     │
├─────────────────────┤
│ Bottom Nav (56-64)  │  ← fixo
│  safe-area-bottom   │
└─────────────────────┘
```

```css
.app-shell {
  display: grid;
  grid-template-rows: auto 1fr auto;
  height: 100dvh;              /* dvh, não vh */
  overflow: hidden;
}
.app-shell__content {
  overflow-y: auto;
  overscroll-behavior-y: contain;
  -webkit-overflow-scrolling: touch;
}
```

Use `100dvh`. `100vh` no iOS Safari conta a barra de endereço e corta o rodapé.

Alternativa aceitável: shell com header e nav em `position: fixed` e `padding-top/bottom` no conteúdo. Escolha uma abordagem e mantenha em todo o app.

---

## 2. Safe area

Aparelhos com notch, Dynamic Island e barra de gestos exigem respeito às áreas seguras. Elemento fixo nunca fica atrás delas.

```html
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
```

`viewport-fit=cover` é obrigatório, senão `env(safe-area-inset-*)` retorna 0.

```css
:root {
  --safe-top: env(safe-area-inset-top, 0px);
  --safe-bottom: env(safe-area-inset-bottom, 0px);
  --safe-left: env(safe-area-inset-left, 0px);
  --safe-right: env(safe-area-inset-right, 0px);
  --nav-height: 60px;
}

.app-header  { padding-top: var(--safe-top); }
.bottom-nav  { padding-bottom: var(--safe-bottom); }
.sticky-action-bar { padding-bottom: calc(12px + var(--safe-bottom)); }
.fab { bottom: calc(var(--nav-height) + var(--safe-bottom) + 16px); }
.toast-mobile { bottom: calc(var(--nav-height) + var(--safe-bottom) + 12px); }
```

Landscape também importa: `--safe-left` / `--safe-right` em telas com notch lateral.

---

## 3. Viewport e comportamentos base

```html
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#070B14" media="(prefers-color-scheme: dark)">
<meta name="theme-color" content="#F8FAFC" media="(prefers-color-scheme: light)">
```

Não use `maximum-scale=1` nem `user-scalable=no` — quebra acessibilidade. Para evitar o zoom de input no iOS, use `font-size: 16px` no campo.

```css
html { -webkit-text-size-adjust: 100%; }

body {
  overflow-x: hidden;
  overscroll-behavior-y: none;      /* evita bounce/refresh acidental */
}

/* remove destaque cinza do tap no Android/iOS */
* { -webkit-tap-highlight-color: transparent; }

/* elementos interativos não devem ser selecionáveis por long-press */
button, a, [role='button'], .nav-item { user-select: none; touch-action: manipulation; }
```

Checagem obrigatória: nenhuma tela pode ter scroll horizontal. Se aparecer, algo tem largura fixa maior que a tela.

---

## 4. App Header (Top Bar)

Altura 56px de conteúdo + safe area. Compacto e contextual.

Pode conter, no máximo 3 elementos além do título:

- voltar (quando não é tela raiz);
- título curto do contexto;
- 1–2 ações (busca, notificações, ação primária);
- menu de opções (`⋯`) para o resto.

```
┌──────────────────────────────┐
│  ←   Clientes         🔍  ⋯  │
└──────────────────────────────┘
```

Regras:

- não empilhe todas as ações no header — jogue no menu `⋯` ou em Bottom Sheet;
- título com ellipsis, nunca quebrando em duas linhas;
- em tela raiz, troque o "voltar" por logo ou avatar;
- header sticky com `backdrop-filter: blur(12px)` + background translúcido dá sensação nativa quando o conteúdo rola sob ele;
- em tela de detalhe, o header pode ser transparente no topo e ganhar background ao rolar.

Botão de voltar deve ter área de toque de 44x44 mesmo que o ícone tenha 20px.

---

## 5. Bottom Navigation

Use quando o app tem **3 a 5 áreas principais**. Não use indiscriminadamente — app de fluxo único (wizard, checkout) não precisa.

```
┌──────┬──────┬──────┬──────┬──────┐
│  🏠  │  👥  │  📁  │  💰  │  ⋯   │
│Início│Client│Projet│Financ│ Mais │
└──────┴──────┴──────┴──────┴──────┘
```

Requisitos:

- ícone + label curto (label melhora reconhecimento e acessibilidade);
- rota ativa destacada por cor + peso + ícone preenchido — nunca só por cor;
- altura 56–64px + safe-area-bottom;
- cada item ocupa fração igual da largura, mínimo 44px de altura tocável;
- fica fixo; o conteúdo tem `padding-bottom` equivalente para não ficar coberto;
- badge de contagem quando houver pendências;
- `aria-current="page"` no item ativo;
- ao tocar no item já ativo, volte ao topo da lista (comportamento nativo esperado).

Mais de 5 áreas: use 4 + item "Mais", que abre um Bottom Sheet ou uma página de menu com o restante.

Esconder a nav ao rolar é opcional; se fizer, restaure ao rolar para cima e nunca esconda quando houver ação primária dependente dela.

---

## 6. Bottom Sheet

No mobile, prefira Bottom Sheet onde o desktop usaria popover, dropdown ou modal pequeno.

Casos: filtros, seleção de item, ações de um registro, detalhes rápidos, escolha de status, menu contextual, seletor de data simplificado, compartilhar.

Anatomia:

```
┌──────────────────────────────┐
│            ▁▁▁▁              │  ← grabber (4px, muted, centralizado)
│  Filtrar clientes            │  ← título
│                              │
│  [ conteúdo ]                │
│                              │
│  ┌────────────────────────┐  │
│  │   Aplicar filtros      │  │  ← ação primária, largura total
│  └────────────────────────┘  │
│         safe-area            │
└──────────────────────────────┘
```

Regras:

- radius 24px apenas no topo;
- overlay escuro com fade; toque no overlay fecha;
- entrada por `translateY(100%) → 0`, 240–280ms, easing de saída;
- altura pelo conteúdo, com máximo de 90dvh e scroll interno;
- sheet alto pode ter snap points (ex.: 50% e 90%);
- swipe-down para fechar quando houver suporte (com fallback em botão);
- foco vai para o sheet ao abrir e volta ao disparador ao fechar;
- `role="dialog"` + `aria-modal="true"` + `aria-labelledby`;
- travar o scroll do body enquanto aberto, restaurando a posição depois.

O mesmo recurso no desktop vira dropdown, popover ou modal centralizado — mesmo estado, apresentação diferente.

---

## 7. Modais responsivos

Não force o mesmo modal em todas as telas.

| Complexidade | Desktop | Mobile |
|---|---|---|
| Confirmação curta | dialog centralizado ~400px | dialog centralizado compacto ou sheet |
| Seleção/filtro | popover ou modal médio | Bottom Sheet |
| Formulário médio | modal 560–640px | sheet alto (90dvh) ou full-screen |
| Formulário complexo / multi-etapas | modal grande ou página | **página full-screen** com header próprio e sticky action |

Full-screen mobile: header com "Cancelar" à esquerda e título no centro, ação primária no rodapé fixo ou como "Salvar" à direita. Isso é o padrão iOS/Android e passa sensação nativa.

Regra: o dialog nunca pode ser maior que o viewport nem ter conteúdo inacessível por causa do teclado.

---

## 8. Tabelas → listas

Proibido reduzir tabela desktop até caber no celular.

Desktop:

```
Cliente        | Telefone        | Plano  | Status | Última compra | Ações
```

Mobile — item de lista com hierarquia:

```
┌──────────────────────────────┐
│ ┌──┐  João Silva          ›  │
│ │JS│  Plano Pro               │
│ └──┘  ● Ativo · 07 Ago        │
└──────────────────────────────┘
```

Como decidir o que mostra:

1. **identificador** (nome/razão social) — sempre;
2. **qualificador** (plano, categoria, responsável) — 1 linha;
3. **status** — badge ou dot + texto;
4. **meta relevante** (data, valor) — 1 informação;
5. o resto vai para a tela de detalhes.

Máximo de 3 linhas de texto por item. Toque no item abre detalhes; ações secundárias em menu `⋯`, long-press ou swipe.

Scroll horizontal só quando comparar colunas é o objetivo real (ex.: extrato contábil). Nesse caso: primeira coluna sticky, indicador visual de que há mais conteúdo, e ofereça também uma visão em lista.

Selecionar múltiplos itens no mobile: entre em "modo seleção" (long-press ou botão), com barra de ações no rodapé e contador no header.

---

## 9. Cards mobile

- área de toque generosa (mínimo 64px de altura útil);
- padding 16px, radius 16px, gap 12px entre cards;
- uma informação principal em destaque, no máximo 4 secundárias;
- ação evidente: chevron, botão ou card inteiro clicável;
- se o card inteiro é clicável, ele é um `<button>` ou `<a>` — não uma `div` com onClick;
- press state obrigatório (ver animations.md).

Não empilhe 15 informações em um card pequeno. Se precisar, é tela de detalhes.

---

## 10. Listas nativas

Padrões que aproximam de app nativo:

**Lista simples com divisor:** avatar/ícone à esquerda, título + subtítulo, chevron à direita, divisor recuado alinhado ao texto (não à borda da tela).

**Lista agrupada por seção:** header de seção sticky, pequeno, em caixa alta suave ou `muted`. Ótimo para agenda, extrato e histórico.

**Lista de configurações:** grupos de itens em cartão com radius, label à esquerda, valor/controle à direita.

Listas longas: virtualize acima de ~200 itens; use scroll infinito com sentinela em vez de paginação numérica; mantenha a posição do scroll ao voltar de um detalhe.

---

## 11. FAB (Floating Action Button)

Use quando a tela tem **uma** ação primária de criação e a lista ocupa a tela toda.

- 56x56px, radius full, elevação, ícone 24px;
- posição: canto inferior direito, acima da Bottom Nav e da safe area;
- `aria-label` obrigatório (só ícone);
- pode recolher para ícone ao rolar para baixo e expandir com texto ao subir;
- não use FAB junto de sticky action bar na mesma tela;
- não use FAB para ação destrutiva.

Quando há 2+ ações primárias, prefira sticky action bar ou menu no header.

---

## 12. Sticky action bar

Tela com ação principal (Salvar, Criar, Continuar, Enviar, Finalizar, Adicionar) ganha barra fixa inferior no mobile:

```
──────────────────────────────
│  [    Salvar alterações   ] │
──────────────────────────────
```

- background `surface` + borda superior ou sombra suave para separar do conteúdo;
- `padding-bottom: calc(12px + safe-area-bottom)`;
- ação primária ocupa largura total; se houver secundária, proporção 2:1 ou secundária como link acima;
- estado de loading no próprio botão, desabilitando dupla submissão;
- quando existe Bottom Nav na mesma tela, a action bar fica acima dela — ou, melhor, a tela é full-screen sem nav.

Nunca deixe o botão salvar apenas no fim de um formulário longo sem alternativa fixa.

---

## 13. Teclado virtual

Problemas a evitar: botão salvar escondido, campo ativo fora da área visível, modal inacessível, footer sobreposto, scroll travado.

```css
/* altura real disponível quando o teclado abre */
.app-shell { height: 100dvh; }
```

```js
// ajusta a área de ação com base no viewport visual (iOS/Android modernos)
const vv = window.visualViewport;
if (vv) {
  const sync = () => {
    const inset = Math.max(0, window.innerHeight - vv.height - vv.offsetTop);
    document.documentElement.style.setProperty('--kb-inset', `${inset}px`);
  };
  vv.addEventListener('resize', sync);
  vv.addEventListener('scroll', sync);
  sync();
}
```

```css
.sticky-action-bar {
  bottom: var(--kb-inset, 0px);
  padding-bottom: calc(12px + var(--safe-bottom));
}
```

Boas práticas adicionais:

- `scroll-margin-bottom` nos campos para o foco não colar no teclado;
- em formulário dentro de sheet, considere abrir full-screen no mobile;
- esconda a Bottom Nav enquanto o teclado está aberto em telas de formulário;
- `enterkeyhint` no último campo (`enterkeyhint="done"` ou `"send"`).

---

## 14. Inputs mobile

```html
<input type="email"  inputmode="email"    autocomplete="email">
<input type="tel"    inputmode="tel"      autocomplete="tel">
<input type="text"   inputmode="numeric"  autocomplete="postal-code">
<input type="text"   inputmode="decimal">                      <!-- valores -->
<input type="search" inputmode="search"   enterkeyhint="search">
<input type="password" autocomplete="current-password">
<input type="text"   autocomplete="name">
<input type="url"    inputmode="url">
```

- `font-size: 16px` mínimo para não haver zoom no iOS;
- altura mínima 44px, ideal 48px;
- label sempre visível — placeholder não substitui label;
- erro inline abaixo do campo, com ícone e texto (não só borda vermelha);
- máscara aplicada sem impedir colar valor;
- `type="number"` só quando faz sentido incremento; para telefone/documento use `text` + `inputmode`;
- select nativo é aceitável em listas curtas; em listas longas ou com busca, use Bottom Sheet com campo de busca.

---

## 15. Formulários longos

Não jogue 30 campos em uma tela mobile. Opções:

- **wizard por etapas** com indicador de progresso e resumo final;
- **seções** com títulos e divisores claros;
- **accordions** quando as partes são independentes;
- **progressive disclosure**: campos avançados escondidos atrás de "Mais opções".

Sempre: salvar rascunho ou preservar estado ao navegar; validar por etapa, não só no fim; ao errar, levar o foco para o primeiro campo inválido.

---

## 16. Navegação e continuidade

```
Lista → Detalhe → Editar → volta ao Detalhe atualizado
```

- "voltar" volta um nível, não para a home;
- título do header informa onde o usuário está;
- transição de entrada slide-in da direita, saída slide-out para a direita reforça hierarquia (respeitando reduced-motion);
- preserve scroll e filtros ao voltar para a lista;
- não abra tela nova para uma ação de um toque — use sheet;
- intercepte o gesto/botão "voltar" do Android para fechar sheet ou modal aberto antes de sair da rota.

---

## 17. Gestures

Use quando somam. Nunca esconda ação crítica só atrás de gesto.

| Gesto | Uso adequado |
|---|---|
| swipe horizontal em item | revelar Editar / Arquivar (com equivalente em menu `⋯`) |
| swipe down em sheet | fechar |
| swipe entre tabs | trocar aba, quando as abas são irmãs |
| long-press | menu contextual / modo seleção |
| pull-to-refresh | atualizar lista |

Requisitos: `touch-action` correto para não competir com o scroll; sempre existir caminho alternativo por botão; feedback visual durante o gesto (item acompanhando o dedo).

---

## 18. Pull to refresh

Só quando a tela mostra dados que mudam e o usuário espera atualizar.

- indicador aparece proporcional ao arraste;
- spinner curto + feedback de conclusão;
- `overscroll-behavior-y: contain` no container para controlar o comportamento;
- em PWA standalone é onde faz mais sentido;
- nunca como enfeite: se não há o que atualizar, não implemente.

---

## 19. Press state e feedback de toque

Sem hover no mobile. O toque precisa de resposta imediata (ver animations.md):

- `scale(0.97)` em cards e botões, 120ms;
- ou redução de opacidade / mudança de background;
- ripple quando o app segue linguagem Android;
- vibração leve (`navigator.vibrate(10)`) só em ações de confirmação, e nunca em excesso.

Resposta deve começar em menos de 100ms do toque, antes de qualquer resposta de rede.

---

## 20. Loading, empty e error no mobile

**Loading:** skeleton com a forma do conteúdo final (avatar circular, duas linhas, badge). Nunca só "Carregando...". Skeleton com shimmer suave; se `prefers-reduced-motion`, use fade estático.

**Empty state:** ícone/ilustração contida, título curto, uma frase de orientação, CTA confortável para toque.

```
        ⊕
Nenhum cliente cadastrado

Cadastre seu primeiro cliente
para começar.

[   Adicionar cliente   ]
```

**Error state:** mensagem em linguagem humana, causa provável e botão "Tentar novamente". Erro de rede não pode virar tela branca.

**Ação em andamento:** loading no próprio botão, bloqueando redisparo.

---

## 21. Toast e feedback global

- posição no mobile: acima da Bottom Nav e da safe area, ou no topo abaixo do header quando há action bar;
- nunca cobre a ação primária nem o teclado;
- 3–4s para sucesso, persistente com botão de fechar para erro;
- um por vez, com fila;
- `role="status"` para sucesso, `role="alert"` para erro;
- swipe para descartar é um plus nativo.

---

## 22. Dashboards mobile

Dashboard desktop não vira sequência infinita de cards. Ordem no mobile:

1. contexto (período, empresa selecionada);
2. KPI principal em destaque;
3. ações importantes;
4. resumo dos demais indicadores (grid 2 colunas de cards compactos);
5. informações prioritárias (últimos registros, pendências);
6. detalhes por drill-down.

Recursos: tabs para separar visões, carrossel horizontal moderado com snap para KPIs, seções colapsáveis, "ver tudo" levando à lista completa.

```css
.kpi-carousel {
  display: flex; gap: 12px;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  scroll-padding-left: 16px;
}
.kpi-carousel > * { scroll-snap-align: start; flex: 0 0 78%; }
```

---

## 23. Gráficos mobile

- proporção mais alta que larga não funciona; use altura 180–240px;
- reduza labels de eixo (mostre 3–5 marcas);
- tooltip por toque, com área de toque ampliada;
- destaque a métrica principal em texto acima do gráfico — o número importa mais que a curva;
- barras > linhas quando há poucos pontos; evite pizza com muitas fatias;
- gráfico ilegível é pior que nenhum: substitua por lista de valores quando necessário;
- cores dos gráficos precisam funcionar no dark e no light.

---

## 24. PWA

Quando o projeto tem suporte PWA:

```json
{
  "display": "standalone",
  "background_color": "#070B14",
  "theme_color": "#070B14",
  "orientation": "portrait",
  "icons": [
    { "src": "/icons/192.png", "sizes": "192x192", "type": "image/png" },
    { "src": "/icons/512.png", "sizes": "512x512", "type": "image/png" },
    { "src": "/icons/maskable.png", "sizes": "512x512", "purpose": "maskable" }
  ]
}
```

- ícone maskable evita corte no Android;
- `apple-mobile-web-app-capable` e ícones apple-touch para iOS;
- em standalone não existe barra de endereço: a navegação interna precisa ser autossuficiente (voltar sempre disponível);
- teste o app instalado, não só no navegador;
- estado offline com mensagem clara quando houver service worker;
- prompt de instalação discreto, dispensável e que não reaparece toda visita.

---

## 25. iOS — pontos de atenção

- `100vh` incorreto → use `100dvh`;
- input com fonte < 16px causa zoom;
- `position: fixed` com teclado aberto se comporta mal → use `visualViewport`;
- `-webkit-overflow-scrolling: touch` para inércia natural;
- bounce do body: `overscroll-behavior-y: none`;
- `backdrop-filter` é caro — use com parcimônia;
- data/hora nativos têm aparência própria; aceite ou construa componente próprio, sem meio termo;
- swipe-back do sistema pode conflitar com swipe horizontal na borda;
- `env(safe-area-inset-*)` exige `viewport-fit=cover`.

---

## 26. Android — pontos de atenção

- teclado pode redimensionar o viewport (resize) em vez de sobrepor;
- botão físico/gesto "voltar" precisa fechar sheet/modal antes de sair da rota;
- `theme-color` pinta a barra de status — mantenha sincronizado com o tema;
- barra de gestos inferior exige `safe-area-inset-bottom`;
- ripple é esperado como feedback de toque;
- variedade de densidades: teste em 360px de largura;
- fontes do sistema variam; não dependa de métrica exata de fonte.

---

## 27. Acessibilidade mobile

- touch target mínimo 44x44px, com 8px de separação entre alvos;
- todo ícone-botão tem `aria-label`;
- ordem de foco segue a ordem visual;
- sheet e modal com foco preso dentro e retorno ao disparador;
- respeite `prefers-reduced-motion` e `prefers-contrast`;
- suporte a zoom de texto do sistema (evite altura fixa em containers de texto);
- contraste mínimo 4.5:1 em texto, 3:1 em ícone e borda relevante;
- estado ativo da navegação não pode ser comunicado só por cor;
- não bloqueie rotação da tela sem motivo real.

---

## 28. Quality gate mobile (resumo)

Antes de finalizar, ver checklist completo em ux-checklist.md. O essencial:

- [ ] parece app, não site
- [ ] mobile foi projetado, não adaptado
- [ ] sem scroll horizontal indevido
- [ ] sidebar desktop eliminada no mobile
- [ ] tabela virou lista/cards
- [ ] Bottom Nav ou Sheet usados quando fazem sentido
- [ ] safe areas respeitadas
- [ ] teclado não quebra a tela
- [ ] elementos fixos não cobrem conteúdo
- [ ] uso com uma mão possível
- [ ] feedback de toque presente
- [ ] loading / empty / error tratados
