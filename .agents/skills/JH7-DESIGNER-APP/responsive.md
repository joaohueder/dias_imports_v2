# Responsive — JH7-DESIGNER-APP

Cada viewport aproveita seus pontos fortes. Não é o mesmo layout escalado.

---

## 1. Larguras de teste

Toda tela deve ser verificada conceitualmente em:

| Largura | Representa |
|---|---|
| 320px | limite inferior (iPhone SE 1ª geração, Android antigo) |
| 360px | Android mais comum |
| 375px | iPhone SE / mini |
| 390px | iPhone 13/14/15 |
| 414px | iPhone Plus |
| 430px | iPhone Pro Max |
| 768px | tablet portrait |
| 1024px | tablet landscape / notebook pequeno |
| 1280px | notebook |
| 1440px | desktop comum |
| 1920px | desktop grande |

320px é o teste mais duro: se não quebra em 320, não quebra em nada.

---

## 2. Breakpoints

```css
/* mobile: base, sem media query */
@media (min-width: 640px)  { /* sm  — mobile largo / portrait grande */ }
@media (min-width: 768px)  { /* md  — tablet: navegação colapsável */ }
@media (min-width: 1024px) { /* lg  — desktop: sidebar fixa */ }
@media (min-width: 1280px) { /* xl  — mais colunas, painéis */ }
@media (min-width: 1536px) { /* 2xl — largura máxima de conteúdo */ }
```

O breakpoint principal de arquitetura é **1024px**: abaixo dele, navegação mobile/tablet; acima, sidebar fixa desktop.

Consultas úteis além da largura:

```css
@media (hover: hover) and (pointer: fine)  { /* só mouse: aplique hover aqui */ }
@media (pointer: coarse)                   { /* toque: alvos maiores */ }
@media (orientation: landscape) and (max-height: 500px) { /* celular deitado */ }
@media (display-mode: standalone)          { /* PWA instalado */ }
@media (prefers-reduced-motion: reduce)    { /* movimento reduzido */ }
```

Regra: `:hover` que altera layout ou revela conteúdo deve estar dentro de `@media (hover: hover)`. No toque, o hover fica "preso" após o tap.

---

## 3. Prefira responsividade sem breakpoint

Quando possível, deixe o layout responder ao espaço:

```css
/* grid que se adapta sem media query */
.cards { display: grid; gap: 16px; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); }

/* tipografia fluida controlada */
.page-title { font-size: clamp(1.5rem, 1.1rem + 1.6vw, 1.875rem); }

/* padding fluido de página */
.page { padding-inline: clamp(16px, 4vw, 32px); }
```

Container queries quando o componente aparece em contextos de largura diferente:

```css
.widget { container-type: inline-size; }
@container (min-width: 420px) {
  .widget__body { grid-template-columns: 1fr auto; }
}
```

Isso resolve o caso do mesmo card aparecer no dashboard largo e no drawer estreito.

---

## 4. Arquitetura por viewport

| Elemento | Mobile (<768) | Tablet (768–1023) | Desktop (≥1024) |
|---|---|---|---|
| Navegação principal | Bottom Nav / Drawer | sidebar compacta ou drawer | sidebar fixa |
| Header | App Header 56px | topbar + toggle de menu | topbar completa |
| Ação primária | sticky bottom / FAB | page header | page header |
| Lista de dados | cards / lista | tabela reduzida ou cards em 2 col | tabela completa |
| Detalhe | página full-screen | drawer ou página | drawer ou página |
| Filtros | Bottom Sheet | drawer / barra reduzida | barra de filtros à vista |
| Seleção em lista | modo seleção | checkbox + barra | checkbox + barra |
| Modal médio | Bottom Sheet | modal | modal |
| Dashboard | 1 col + carrossel de KPIs | 2 col | 3–4 col |
| Formulário | 1 coluna | 1–2 colunas | 2 colunas + coluna lateral |

---

## 5. Grid por viewport

```css
.grid-kpi   { display: grid; gap: 12px; grid-template-columns: repeat(2, 1fr); }
.grid-cards { display: grid; gap: 12px; grid-template-columns: 1fr; }

@media (min-width: 768px) {
  .grid-kpi   { grid-template-columns: repeat(2, 1fr); gap: 16px; }
  .grid-cards { grid-template-columns: repeat(2, 1fr); gap: 16px; }
}
@media (min-width: 1280px) {
  .grid-kpi   { grid-template-columns: repeat(4, 1fr); }
  .grid-cards { grid-template-columns: repeat(3, 1fr); }
}
```

KPI em 2 colunas no mobile funciona melhor que 1 — mostra mais contexto sem rolagem. Cards com conteúdo rico ficam em 1 coluna.

---

## 6. Tabelas por viewport

- **Desktop:** tabela completa, todos os recursos (ver desktop-app.md).
- **Tablet:** reduza para 4–5 colunas essenciais e mova o resto para o detalhe. Ou mantenha a tabela com scroll horizontal e primeira coluna sticky.
- **Mobile:** lista/cards (ver mobile-native.md). Scroll horizontal só em caso justificado.

Implementação recomendada: um componente de dados que decide a apresentação, e não duas telas duplicadas.

```tsx
const isMobile = useMediaQuery('(max-width: 767px)');
return isMobile ? <MobileList items={items} /> : <DataTable items={items} />;
```

Cuidado: renderizar as duas e esconder com CSS duplica DOM e custo. Use quando a lista é pequena; acima disso, condicione a renderização.

---

## 7. Formulários por viewport

Mobile: uma coluna, campos empilhados, ação fixa inferior.

Desktop: agrupe campos relacionados em 2 colunas, mas mantenha campos longos (endereço, descrição, observação) em largura total.

```css
.form-grid { display: grid; gap: 16px; grid-template-columns: 1fr; }
@media (min-width: 768px) {
  .form-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; }
  .form-grid > .col-full { grid-column: 1 / -1; }
}
```

Nunca coloque dois campos lado a lado no mobile, exceto pares muito curtos (CEP + número, mês + ano).

---

## 8. Imagens e mídia

```css
img, video, canvas, svg { max-width: 100%; height: auto; display: block; }
```

- `aspect-ratio` para reservar espaço e evitar layout shift;
- `loading="lazy"` fora da primeira dobra, `fetchpriority="high"` na imagem principal;
- `srcset`/`sizes` para não baixar imagem de desktop no celular;
- avatar sempre com fallback de iniciais.

---

## 9. Overflow e quebra de texto

```css
.truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.break    { overflow-wrap: anywhere; }   /* e-mail, URL, código */
```

Causas comuns de scroll horizontal indesejado: largura fixa em px, `min-width` em grid item (`minmax(0, 1fr)` resolve), tabela sem container `overflow-x`, texto longo sem quebra, `100vw` com scrollbar visível, elemento posicionado fora da viewport.

---

## 10. Landscape em celular

Altura útil pode cair para ~380px. Nessa situação:

- reduza a altura do header;
- considere esconder a Bottom Nav e mostrar navegação lateral compacta;
- sheets ocupam quase toda a altura — prefira full-screen;
- não bloqueie a rotação; adapte.

---

## 11. Estratégia de implementação

1. Escreva o CSS base para mobile, sem media query.
2. Adicione `min-width` conforme o layout ganha espaço.
3. Use `auto-fit`/`clamp`/container queries antes de criar mais breakpoints.
4. Só condicione por JS (`useMediaQuery`) quando a **arquitetura** muda (tabela → lista, modal → sheet). Diferença de estilo é CSS.
5. Sincronize os breakpoints de JS com os do CSS a partir de uma fonte única.
6. Teste em 320px, 390px, 768px, 1024px e 1440px antes de fechar.
