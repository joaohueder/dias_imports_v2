# Animations — JH7-DESIGNER-APP

Animação em aplicativo tem função: comunicar mudança de estado, hierarquia e origem do conteúdo. Não é decoração.

---

## 1. Duração

| Categoria | Duração | Exemplos |
|---|---|---|
| Microinteração | 120–180ms | hover, press, checkbox, switch, badge, ícone |
| Componente | 180–250ms | dropdown, tooltip, accordion, tab indicator |
| Modal / Sheet | 200–300ms | dialog, bottom sheet, drawer |
| Transição maior | 300–450ms | troca de página, expansão de card, wizard |

Acima de 450ms o app parece lento. Abaixo de 100ms a animação não é percebida — melhor instantâneo.

Saída pode ser ~20% mais rápida que a entrada.

---

## 2. Easing

```css
--ease-out:    cubic-bezier(0.16, 1, 0.3, 1);      /* entrada — padrão */
--ease-in:     cubic-bezier(0.4, 0, 1, 1);         /* saída */
--ease-in-out: cubic-bezier(0.4, 0, 0.2, 1);       /* movimento contínuo */
--ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);  /* leve overshoot, use com parcimônia */
```

Regra: elemento entrando usa `ease-out` (rápido no início, desacelera). Elemento saindo usa `ease-in`. `linear` só para spinner e progress indeterminado.

---

## 3. Propriedades que podem ser animadas

Anime apenas `transform` e `opacity`. Elas rodam em composite, sem layout/paint.

```css
/* ✅ */  transform: translateY(8px); opacity: 0;
/* ❌ */  height, width, top, left, margin, padding, box-shadow, filter
```

Quando precisar animar altura (accordion), use `grid-template-rows: 0fr → 1fr` ou `max-height` com valor conhecido, e evite em listas grandes.

Não deixe `will-change` permanente — aplique só durante a animação.

---

## 4. Padrões prontos

```css
/* fade */
@keyframes fade-in { from { opacity: 0 } to { opacity: 1 } }

/* fade-up — entrada de conteúdo e cards */
@keyframes fade-up {
  from { opacity: 0; transform: translateY(8px) }
  to   { opacity: 1; transform: none }
}

/* scale-in — dialog, popover */
@keyframes scale-in {
  from { opacity: 0; transform: scale(0.96) }
  to   { opacity: 1; transform: none }
}

/* sheet-up — bottom sheet */
@keyframes sheet-up {
  from { transform: translateY(100%) }
  to   { transform: translateY(0) }
}

/* drawer-in — painel lateral */
@keyframes drawer-in {
  from { transform: translateX(100%) }
  to   { transform: translateX(0) }
}

/* shimmer — skeleton */
@keyframes shimmer { 100% { transform: translateX(100%) } }
```

Origem importa: dropdown ancorado tem `transform-origin` no lado do disparador; sheet sobe de baixo; drawer entra do lado onde vai ficar.

---

## 5. Hover (desktop)

```css
@media (hover: hover) and (pointer: fine) {
  .card-interactive {
    transition: transform 160ms var(--ease-out), border-color 160ms, box-shadow 160ms;
  }
  .card-interactive:hover {
    transform: translateY(-2px);
    border-color: rgb(var(--primary) / 0.35);
    box-shadow: var(--shadow-md);
  }
}
```

Hover é bônus do desktop. Nada essencial depende dele.

---

## 6. Press state (mobile e desktop)

Feedback imediato ao toque, começando em menos de 100ms:

```css
.pressable {
  transition: transform 120ms var(--ease-out), background-color 120ms;
  -webkit-tap-highlight-color: transparent;
  touch-action: manipulation;
}
.pressable:active { transform: scale(0.97); }

/* item de lista: escala fica estranha; use background */
.list-item:active { background: rgb(var(--surface-secondary)); transform: none; }

/* botão largo: escala menor para não distorcer */
.btn-block:active { transform: scale(0.99); }
```

Ripple quando o app segue linguagem Android; opacidade quando segue iOS. Escolha uma linguagem e mantenha.

Vibração leve (`navigator.vibrate(10)`) apenas em confirmação de ação relevante.

---

## 7. Focus

```css
:focus-visible {
  outline: 2px solid rgb(var(--ring));
  outline-offset: 2px;
  border-radius: inherit;
}
:focus:not(:focus-visible) { outline: none; }
```

Foco pode ter transição de cor, nunca de posição — movimento no anel de foco atrapalha quem navega por teclado. Jamais remova o indicador sem substituir por outro visível.

---

## 8. Overlays (modal, sheet, drawer)

```css
.overlay {
  background: rgb(var(--overlay));
  animation: fade-in 200ms var(--ease-out);
}
.overlay[data-state='closed'] { animation: fade-in 150ms var(--ease-in) reverse; }

.dialog { animation: scale-in 220ms var(--ease-out); }
.sheet  { animation: sheet-up 260ms var(--ease-out); }
.drawer { animation: drawer-in 250ms var(--ease-out); }
```

Sempre anime a saída também — overlay que desaparece de repente parece bug. Overlay e conteúdo entram juntos; o overlay pode sair um pouco antes.

Durante o arraste de um sheet, siga o dedo sem transição e aplique a transição só ao soltar.

---

## 9. Transições de página

Mobile (sensação nativa): avanço entra da direita, retorno sai para a direita, 280–320ms.

Desktop: fade-up curto (150–200ms) é suficiente; slide de página inteira incomoda no mouse.

Com View Transitions API disponível:

```css
@view-transition { navigation: auto; }
::view-transition-old(root) { animation-duration: 200ms; }
::view-transition-new(root) { animation-duration: 200ms; }
```

Nunca bloqueie a interação esperando a animação terminar. E não anime a troca de página quando o usuário só mudou um filtro.

---

## 10. Stagger em listas

Entrada escalonada dá refinamento, mas com limite:

- máximo 6–8 itens animados, 30–40ms de atraso entre eles;
- só na primeira renderização, não a cada re-render ou scroll infinito;
- em lista longa, anime o container e não cada item.

---

## 11. Skeleton e progresso

```css
.skeleton {
  position: relative; overflow: hidden;
  background: rgb(var(--surface-secondary));
}
.skeleton::after {
  content: ''; position: absolute; inset: 0;
  transform: translateX(-100%);
  background: linear-gradient(90deg, transparent, rgb(var(--foreground) / 0.06), transparent);
  animation: shimmer 1.4s infinite;
}
```

- skeleton só se a espera passar de ~300ms; abaixo disso, aparece e desaparece piscando;
- barra indeterminada para operações sem progresso conhecido; barra determinada quando há percentual real;
- spinner em botão mantém a largura do botão para não deslocar o layout.

---

## 12. Números e valores

Contagem animada em KPI (200–600ms) funciona no primeiro carregamento do dashboard. Não anime a cada atualização em tempo real — vira ruído. Use `tabular-nums` para o número não "dançar" enquanto conta.

Mudança de valor em tempo real: destaque breve de fundo (flash de 400ms) é mais legível que animar o dígito.

---

## 13. Reduced motion

```css
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
```

Mais elegante que zerar tudo: manter fade (opacidade não causa desconforto) e remover translate/scale. Skeleton fica estático. Nenhuma informação pode depender de animação para ser compreendida.

Em JS, respeite a preferência antes de disparar animações imperativas:

```js
const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
```

---

## 14. Performance

- limite a poucas animações simultâneas;
- `backdrop-filter` e `filter: blur()` são caros no celular — poucos elementos, área pequena;
- não anime `box-shadow`; anime a opacidade de um pseudo-elemento com a sombra;
- não anime elemento gigante (tela inteira) com blur;
- remova `will-change` após o uso;
- em lista virtualizada, desative animação de item durante o scroll;
- meça em aparelho intermediário, não só no desktop.

---

## 15. Erros comuns

- animação de 600ms+ em interação frequente;
- bounce/spring em tudo;
- efeito de entrada disparando novamente a cada re-render;
- animar `height: auto` em lista grande;
- overlay sem animação de saída;
- hover em dispositivo de toque, deixando o estado preso;
- carrossel com autoplay em app de gestão;
- parallax e efeito de rolagem **em aplicativo** (isso é linguagem de página pública; em landing é permitido — ver [landing-pages.md](landing-pages.md));
- scroll-jacking em qualquer contexto, inclusive landing: sequestrar a rolagem quebra a expectativa e enjoa no telefone.
