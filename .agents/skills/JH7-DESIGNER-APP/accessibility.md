# Accessibility — JH7-DESIGNER-APP

Acessibilidade em app de gestão não é opcional: quem usa o sistema oito horas por dia depende de teclado, contraste e previsibilidade.

Alvo prático: **WCAG 2.2 nível AA**. Validação completa exige teste manual com tecnologia assistiva e revisão especializada — o que está aqui cobre o essencial de implementação.

---

## 1. Contraste

| Elemento | Mínimo |
|---|---|
| Texto normal (< 18.66px) | 4.5:1 |
| Texto grande (≥ 24px ou 18.66px bold) | 3:1 |
| Ícone e componente de UI | 3:1 |
| Borda de input, indicador de foco | 3:1 |
| Texto desabilitado | sem exigência, mas mantenha legível |

Pontos que costumam falhar: texto `muted` sobre `surface-secondary`, placeholder, badge com fundo suave, texto branco sobre primária clara, cor de gráfico no dark, borda de input no dark.

Regra prática: teste `muted` sobre todas as superfícies onde ele aparece, não só sobre o background principal.

Cor nunca é o único portador de informação. Status precisa de texto ou ícone. Campo com erro precisa de mensagem, não só borda vermelha. Item ativo de navegação precisa de peso, ícone preenchido ou indicador, além da cor.

---

## 2. HTML semântico

```html
<header>  <nav>  <main>  <aside>  <section>  <footer>
<button>  <a href>  <table>  <th scope>  <label for>  <fieldset>  <legend>
```

- `<button>` para ação, `<a href>` para navegação — nunca `div` com `onClick`;
- um `<h1>` por página, hierarquia de headings sem pular níveis;
- `<main>` único, com skip link no início da página;
- listas de dados em `<ul>`/`<li>` ou `<table>`, conforme a natureza;
- landmark com `aria-label` quando houver mais de um do mesmo tipo (`<nav aria-label="Navegação principal">`).

Componente customizado que substitui um nativo precisa reproduzir todo o comportamento de teclado do nativo. Se não vai reproduzir, use o nativo.

---

## 3. Teclado

Toda funcionalidade acessível por mouse ou toque tem de funcionar por teclado.

| Tecla | Comportamento esperado |
|---|---|
| Tab / Shift+Tab | percorre elementos interativos na ordem visual |
| Enter | ativa botão e link |
| Espaço | ativa botão, marca checkbox |
| Esc | fecha modal, sheet, dropdown, cancela edição inline |
| ↑ ↓ | navega itens de menu, select, combobox, lista |
| ← → | navega tabs e itens de toolbar |
| Home / End | primeiro e último item |
| ⌘K / Ctrl+K | command palette |

Regras:

- ordem de tab acompanha a ordem visual; não use `tabindex` positivo;
- nenhum elemento interativo fica inalcançável;
- não há armadilha de foco fora de modal/sheet;
- overlay prende o foco enquanto aberto e devolve ao disparador ao fechar;
- ação revelada só no hover precisa de equivalente alcançável por teclado;
- atalhos de letra única não podem interferir na digitação em campos.

---

## 4. Foco visível

```css
:focus-visible { outline: 2px solid rgb(var(--ring)); outline-offset: 2px; }
```

Indicador com contraste 3:1 contra o fundo adjacente, envolvendo o elemento inteiro. Não remova `outline` sem substituir. Ao abrir overlay, mova o foco para dentro dele (título ou primeiro campo); ao fechar, devolva.

Ao submeter formulário com erro, mova o foco para o primeiro campo inválido e anuncie o erro.

---

## 5. Formulários

```html
<label for="cnpj">CNPJ</label>
<input id="cnpj" name="cnpj" inputmode="numeric" autocomplete="off"
       aria-describedby="cnpj-hint cnpj-error" aria-invalid="true" aria-required="true">
<p id="cnpj-hint" class="hint">Somente números</p>
<p id="cnpj-error" role="alert">CNPJ inválido. Verifique os 14 dígitos.</p>
```

- label visível sempre; `aria-label` só quando o rótulo visual é realmente impossível;
- obrigatoriedade indicada em texto ou `aria-required`, não só com asterisco colorido;
- erro descreve o problema e como corrigir, próximo ao campo;
- resumo de erros no topo em formulários longos, com links para os campos;
- `autocomplete` correto ajuda todo mundo, não só leitores de tela;
- agrupamento de rádios em `<fieldset>` com `<legend>`;
- não valide de forma agressiva enquanto o usuário digita: valide no blur.

---

## 6. Overlays

```html
<div role="dialog" aria-modal="true" aria-labelledby="t" aria-describedby="d">
  <h2 id="t">Inativar cliente</h2>
  <p id="d">O cliente deixará de aparecer nas listagens. Você pode reativar depois.</p>
</div>
```

Checklist: foco preso dentro, Esc fecha, foco retorna ao disparador, conteúdo de fundo com `aria-hidden` ou `inert`, scroll do body travado, botão de fechar com `aria-label`.

Em dialog de confirmação, o foco inicial vai para a ação **segura**, não para a destrutiva.

---

## 7. Conteúdo dinâmico

```html
<div role="status" aria-live="polite">12 clientes encontrados</div>
<div role="alert">Falha ao salvar. Tente novamente.</div>
<button aria-busy="true">Salvando…</button>
```

- resultado de busca e filtro anunciado com contagem;
- toast de sucesso como `status`, erro como `alert`;
- loading com `aria-busy`; skeleton com `aria-hidden` e uma região `status` dizendo "Carregando clientes";
- atualização em tempo real não deve roubar o foco nem reordenar a lista sob o cursor do usuário;
- região live existe no DOM antes da mensagem aparecer.

---

## 8. Tabelas e listas

```html
<table>
  <caption class="sr-only">Clientes cadastrados</caption>
  <thead>
    <tr><th scope="col" aria-sort="ascending">
      <button>Cliente</button>
    </th></tr>
  </thead>
</table>
```

- `<th scope="col">` e `scope="row"` quando houver cabeçalho de linha;
- ordenação via `<button>` no cabeçalho com `aria-sort`;
- checkbox de seleção com label descritiva ("Selecionar João Silva");
- ação em massa anuncia a quantidade afetada;
- ícone-ação em linha com `aria-label` incluindo o registro ("Editar João Silva").

---

## 9. Ícones e imagens

```html
<button aria-label="Filtrar clientes"><FilterIcon aria-hidden="true" /></button>
<span><CheckIcon aria-hidden="true" /> Ativo</span>
<img src="logo.png" alt="Logotipo da empresa">
<img src="grafico.png" alt="" role="presentation">
```

Ícone decorativo ao lado de texto: `aria-hidden`. Ícone que é a única indicação: `aria-label` no botão. Gráfico: forneça alternativa textual ou tabela de dados equivalente.

---

## 10. Mobile

- alvo mínimo 44x44px, com 8px de separação;
- suporte a zoom até 200% sem perda de conteúdo (não use `user-scalable=no`);
- respeite o tamanho de fonte do sistema — evite altura fixa em containers de texto;
- não bloqueie orientação;
- gesto sempre com alternativa por botão;
- `viewport-fit=cover` + safe areas para o conteúdo não ficar sob elementos do sistema;
- leitores de tela (VoiceOver, TalkBack) navegam por ordem de DOM: mantenha DOM coerente com o visual.

---

## 11. Movimento e preferências

```css
@media (prefers-reduced-motion: reduce) { /* remova translate/scale, mantenha fade curto */ }
@media (prefers-contrast: more)         { /* bordas mais fortes, texto mais escuro */ }
```

Nada essencial depende de animação. Sem autoplay de conteúdo em movimento; se houver, ofereça pausa. Nada que pisque mais de 3 vezes por segundo.

---

## 12. Utilitário obrigatório

```css
.sr-only {
  position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
  overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0;
}
.skip-link:focus { position: fixed; top: 8px; left: 8px; z-index: 100; }
```

---

## 13. Verificação rápida

1. Navegue a tela inteira só com Tab e Enter. Consegue fazer tudo?
2. O foco está sempre visível e na ordem esperada?
3. Desligue as cores (grayscale). Ainda é possível entender status e estados?
4. Dê zoom de 200%. Algo se perde ou sobrepõe?
5. Todo campo tem label ligada por `for`/`id`?
6. Todo botão de ícone tem `aria-label`?
7. Modal prende e devolve o foco?
8. Erros são anunciados e descritivos?
9. Contraste conferido em light e dark?
10. Toque: alvos com pelo menos 44px?

Ferramentas: axe DevTools, Lighthouse, eslint-plugin-jsx-a11y. Elas pegam parte do problema; teclado e leitor de tela precisam de teste manual.
