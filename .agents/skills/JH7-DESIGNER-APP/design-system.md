# Design System — JH7-DESIGNER-APP

Regras de tokens, cor, tipografia, espaçamento, radius, sombras e temas para aplicativos web.

Antes de criar tokens: verifique se o projeto já tem. Se tiver, estenda o que existe em vez de criar um sistema paralelo.

---

## 1. Tokens semânticos

Nunca use cor literal em componente. Use token com nome de função, não de aparência.

```
❌ text-[#6366F1]   bg-[#0D1424]   border-[#e2e8f0]
✅ text-primary     bg-surface     border-border
```

Conjunto mínimo:

```
background            fundo da aplicação
foreground            texto principal
surface               cartões, painéis, inputs
surface-secondary     áreas de agrupamento, headers de tabela
surface-elevated      modais, sheets, popovers, dropdowns
primary               ação principal, rota ativa
primary-hover
primary-active
primary-foreground    texto sobre primary
secondary
accent
muted                 texto secundário, labels, placeholders
muted-surface         chips, badges neutros
border                divisores e contornos
ring                  anel de foco
success / warning / danger / info      + variantes -surface e -foreground
overlay               fundo escuro atrás de modal/sheet
```

Regra: todo token de estado (success, warning, danger, info) precisa de três variantes — cor sólida, fundo suave (`-surface`) e texto legível (`-foreground`). Sem isso, badges e alertas ficam com contraste ruim.

---

## 2. Implementação

### CSS custom properties (funciona em qualquer stack)

```css
:root {
  --background: 248 250 252;
  --surface: 255 255 255;
  --surface-secondary: 241 245 249;
  --surface-elevated: 255 255 255;
  --foreground: 15 23 42;
  --muted: 100 116 139;
  --primary: 79 70 229;
  --primary-hover: 67 56 202;
  --primary-foreground: 255 255 255;
  --border: 226 232 240;
  --ring: 79 70 229;
  --success: 5 150 105;
  --warning: 217 119 6;
  --danger: 220 38 38;
  --overlay: 15 23 42 / 0.5;
}

[data-theme='dark'] {
  --background: 7 11 20;
  --surface: 13 20 36;
  --surface-secondary: 17 26 46;
  --surface-elevated: 17 26 46;
  --foreground: 248 250 252;
  --muted: 148 163 184;
  --primary: 99 102 241;
  --primary-hover: 129 140 248;
  --primary-foreground: 255 255 255;
  --border: 148 163 184 / 0.15;
  --ring: 129 140 248;
  --success: 16 185 129;
  --warning: 245 158 11;
  --danger: 239 68 68;
  --overlay: 2 6 16 / 0.7;
}
```

Uso: `background: rgb(var(--surface));` e `background: rgb(var(--border) / 0.4);` quando precisar de alpha.

### Tailwind

```js
// tailwind.config.js
theme: {
  extend: {
    colors: {
      background: 'rgb(var(--background) / <alpha-value>)',
      surface: {
        DEFAULT: 'rgb(var(--surface) / <alpha-value>)',
        secondary: 'rgb(var(--surface-secondary) / <alpha-value>)',
        elevated: 'rgb(var(--surface-elevated) / <alpha-value>)',
      },
      foreground: 'rgb(var(--foreground) / <alpha-value>)',
      muted: 'rgb(var(--muted) / <alpha-value>)',
      primary: {
        DEFAULT: 'rgb(var(--primary) / <alpha-value>)',
        hover: 'rgb(var(--primary-hover) / <alpha-value>)',
        foreground: 'rgb(var(--primary-foreground) / <alpha-value>)',
      },
      border: 'rgb(var(--border) / <alpha-value>)',
    },
  },
}
```

Vantagem dessa abordagem: trocar tema é trocar um atributo no `<html>`, sem duplicar classes `dark:` em todo componente.

---

## 3. Paletas de referência

Use como ponto de partida quando não houver marca definida. Se o produto tem identidade, preserve a identidade e apenas organize os tokens.

### Dark app

| Token | Valor |
|---|---|
| background | `#070B14` |
| surface | `#0D1424` |
| surface-elevated | `#111A2E` |
| primary | `#6366F1` |
| secondary | `#8B5CF6` |
| accent | `#06B6D4` |
| success | `#10B981` |
| warning | `#F59E0B` |
| danger | `#EF4444` |
| foreground | `#F8FAFC` |
| muted | `#94A3B8` |
| border | `rgba(148,163,184,0.15)` |

### Light app

| Token | Valor |
|---|---|
| background | `#F8FAFC` |
| surface | `#FFFFFF` |
| surface-secondary | `#F1F5F9` |
| primary | `#4F46E5` |
| secondary | `#7C3AED` |
| accent | `#0891B2` |
| foreground | `#0F172A` |
| muted | `#64748B` |
| border | `#E2E8F0` |

### Como derivar uma paleta de marca

1. Pegue a cor da marca como `primary`.
2. `primary-hover` = mesma cor com luminosidade ~8% menor no light e ~8% maior no dark.
3. Neutros: escolha uma família (slate, zinc, gray, stone) e use a mesma família em todo o app.
4. Estados semânticos ficam fora da marca — verde/amarelo/vermelho precisam ser reconhecíveis.
5. Verifique contraste antes de fechar (ver accessibility.md).

---

## 4. Tipografia

Prefira a fonte já usada no projeto. Com liberdade de escolha: Inter, Geist, Manrope, DM Sans, Plus Jakarta Sans.

Escala (desktop → mobile):

| Papel | Desktop | Mobile | Peso | Uso |
|---|---|---|---|---|
| Display | 40–48px | 30–32px | 700 | número de KPI grande, hero de app |
| H1 | 30–32px | 24px | 700 | título de página |
| H2 | 24px | 20px | 600 | seção |
| H3 | 20px | 18px | 600 | card, bloco |
| Body | 15–16px | 16px | 400 | conteúdo |
| Body strong | 15–16px | 16px | 500/600 | rótulo de item de lista |
| Small | 14px | 14px | 400 | apoio, meta |
| Caption | 12–13px | 12–13px | 500 | label, badge, timestamp |

Regras:

- Body no mobile nunca abaixo de 16px em campos de formulário — iOS dá zoom automático em input com fonte menor.
- Nada abaixo de 12px em texto informativo.
- line-height: 1.2–1.3 em títulos, 1.5–1.6 em corpo.
- letter-spacing negativo leve (-0.01em a -0.02em) só em títulos grandes.
- Números de tabela e valores financeiros: `font-variant-numeric: tabular-nums`.
- Máximo de 2 famílias no app.

---

## 5. Espaçamento

Escala fixa em px: `4 · 8 · 12 · 16 · 20 · 24 · 32 · 40 · 48 · 64`.

Aplicação típica:

| Contexto | Desktop | Mobile |
|---|---|---|
| padding interno de card | 20–24 | 16 |
| gap entre cards | 16–24 | 12–16 |
| padding lateral da página | 24–32 | 16 |
| gap entre campos de form | 20 | 16 |
| gap entre seções | 32–48 | 24–32 |
| altura de item de lista | 56–64 | 64–72 |

Densidade: sistema administrativo pode ser mais denso no desktop; área de cliente e mobile pedem respiro.

---

## 6. Radius

```
sm      8px    badge, input pequeno, chip
md     12px    input, button, card interno
lg     16px    card, painel
xl     20px    modal, dialog, container destacado
2xl    24px    bottom sheet (topo), card mobile grande
full   9999px  avatar, pill, FAB
```

No mobile é natural usar radius maiores em sheets, cards e dialogs. Mantenha coerência: se o card é 16, o botão dentro dele não deve ser 4.

---

## 7. Sombras e elevação

Light theme — sombra suave e difusa:

```css
--shadow-sm: 0 1px 2px rgb(15 23 42 / 0.06);
--shadow-md: 0 4px 12px rgb(15 23 42 / 0.08);
--shadow-lg: 0 12px 32px rgb(15 23 42 / 0.10);
--shadow-xl: 0 24px 56px rgb(15 23 42 / 0.14);
```

Dark theme — sombra quase não aparece. Comunique elevação com **superfície mais clara + borda**, não com sombra forte:

```css
[data-theme='dark'] {
  --shadow-md: 0 4px 16px rgb(0 0 0 / 0.4);
}
/* e use surface-elevated + border para separar camadas */
```

Níveis de elevação:

| Nível | Onde | Recurso |
|---|---|---|
| 0 | página | background |
| 1 | card, input | surface + border |
| 2 | dropdown, popover | surface-elevated + shadow-md |
| 3 | modal, bottom sheet | surface-elevated + shadow-xl + overlay |
| 4 | toast | surface-elevated + shadow-lg |

---

## 8. Efeitos premium (com moderação)

Permitidos quando somam: gradiente sutil em header ou KPI card, glow discreto na cor primária em estado ativo, borda translúcida no dark, overlay com blur atrás de modal, radial gradient de fundo muito suave.

Limites:

- no máximo dois efeitos por tela;
- `backdrop-filter: blur()` em poucos elementos — custa GPU no celular;
- glassmorphism só onde há conteúdo atrás que justifique;
- nunca use gradiente em texto de leitura.

---

## 9. Dark e light como experiências completas

Se o app tem os dois temas, revise nos dois: backgrounds, borders, sombras, gráficos, cards, inputs, navegação, estado ativo, hover, focus, disabled, modais e bottom sheets.

Erros frequentes:

- borda invisível no dark (use branco com alpha, não cinza sólido);
- sombra preta pesada no dark;
- cor de gráfico com contraste ruim contra o background escuro;
- `surface` igual ao `background` no dark, fazendo o card desaparecer;
- imagem/ilustração com fundo branco fixo.

`color-scheme: light dark` no root ajuda scrollbar e controles nativos a acompanharem o tema.

---

## 10. Z-index

```
base            0
sticky header   20
sidebar         30
dropdown        40
overlay         50
modal / sheet   60
toast           70
tooltip         80
```

Escala declarada em token evita a guerra de `z-index: 9999`.
