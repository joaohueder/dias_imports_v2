# Landing Pages — JH7-DESIGNER-APP

Documento para **página pública de conversão**: landing de produto, página de venda, página de oferta, página de captura.

Landing page não é tela de aplicativo. As duas usam os mesmos tokens, a mesma escala e as mesmas regras de acessibilidade, mas resolvem problemas opostos:

| | Aplicativo | Landing page |
|---|---|---|
| Quem usa | pessoa logada, que volta amanhã | estranho, que chegou de um anúncio e nunca voltará |
| Objetivo | executar muitas tarefas | tomar **uma** decisão |
| Navegação | menu, sidebar, bottom nav, abas | nenhuma — saída é perda |
| Densidade | alta, controlada | baixa, um argumento por vez |
| Sucesso | tarefa concluída sem atrito | taxa de conversão |
| Tolerância a lentidão | alguma (já investiu no login) | zero — quem não conhece a marca fecha a aba |

Por isso vários acertos de app são erros aqui: menu de navegação, sidebar, bottom navigation, abas, breadcrumb, densidade de dashboard, tabela.

Sempre responda em português do Brasil.

---

## 1. Regra fundamental

A landing tem **um** objetivo e **uma** ação. Tudo que não empurra para essa ação está tirando conversão.

Antes de desenhar, responda em uma frase cada:

1. Qual é a **única** ação? (falar no WhatsApp, comprar, agendar, deixar contato)
2. De onde vem o tráfego? (anúncio de Reels, Stories, busca, link na bio, remarketing)
3. O visitante **já conhece** o produto ou está vendo agora?
4. Qual é a objeção principal — preço, confiança na loja, dúvida sobre o produto, medo do processo?
5. Qual é o argumento mais forte que temos?

A resposta 4 define o **layout**. A resposta 5 define o que vem primeiro. Se essas duas não estiverem claras, a página vai ficar bonita e não vender.

**Conteúdo, oferta e copy são domínio da skill `JH7-MARKETING-MASTER`.** Este documento cuida da forma: hierarquia, ritmo, performance, mobile e acessibilidade. Quando a tarefa envolver decidir a oferta, o preço, o texto ou a ordem estratégica dos argumentos, use as duas juntas.

---

## 2. Mobile first 100%

Em landing de tráfego pago, **80% a 95% das visitas são de telefone**, quase sempre por dentro do navegador embutido do Instagram ou Facebook, em 4G, com a tela em uma mão.

O desktop é o caso secundário. Desenhe a coluna do telefone primeiro, valide nela, e só depois deixe a coluna crescer.

### Regras não negociáveis

- **Coluna única.** Sem grid de duas colunas no telefone. Grid lado a lado só a partir de 900px, se houver ganho real.
- **Largura máxima de leitura.** A coluna trava em algo entre 440px e 560px mesmo no desktop. Landing de produto esticada em 1400px perde o ritmo e parece formulário corporativo.
- **Uma decisão por rolagem.** Cada bloco entrega um argumento e cabe na tela. Bloco que exige duas rolagens para ser entendido é bloco mal cortado.
- **Ação sempre alcançável.** CTA acima da dobra **e** barra fixa inferior que aparece na rolagem. Ninguém deve rolar de volta para comprar.
- **Polegar.** O CTA fica na faixa inferior, largura cheia ou quase. Alvo mínimo de 44x44px, com 48px de altura sendo o alvo prático em botão principal.
- **`100dvh`, nunca `100vh`.** A barra do navegador embutido muda de altura ao rolar; `100vh` corta o CTA.
- **Safe area.** Barra fixa inferior soma `env(safe-area-inset-bottom)` ao padding, e a página reserva o mesmo espaço no fim para o conteúdo não ficar embaixo dela.
- **Sem hover como única pista.** Em toque, hover fica preso. Todo estado importante tem versão `:active` e `:focus-visible`.
- **Teste em 320, 360, 390 e 430px.** O preço grande é o primeiro a estourar em 320.

### Tipografia no telefone

- Título da dobra: agressivo é permitido e desejado. `clamp()` com base em `vw` deixa o título grande sem estourar — mas **sempre com teto**, senão em tablet fica absurdo.
- Corpo: mínimo de 16px. Abaixo disso o iOS dá zoom no input e o texto cansa.
- `text-wrap: balance` em título, para não sobrar uma palavra órfã na última linha.
- Fonte de sistema ou uma fonte só, com `font-display: swap`. Cada peso extra é download.

---

## 3. A dobra

A dobra decide se a pessoa rola. Ela precisa responder três coisas em menos de 3 segundos: **o que é**, **quanto custa ou qual é a vantagem**, e **o que eu faço agora**.

Anatomia mínima:

1. Selo curto de contexto ou vantagem (opcional, mas ajuda a ancorar)
2. Título direto, dizendo o produto e o benefício
3. Imagem do produto, ou o preço em corpo grande, dependendo do que vende mais
4. Preço, com o valor cheio riscado quando houver desconto real
5. CTA
6. Uma linha de segurança: sem cadastro, atendimento humano, garantia, frete

O que **não** entra na dobra: menu, logo gigante, carrossel automático, vídeo com autoplay e som, texto institucional, "bem-vindo ao nosso site".

**Nada acima da dobra deve animar entrada por rolagem.** Animar o que já está visível atrasa o argumento e piora o LCP. Reveal-on-scroll é para o que está abaixo.

---

## 4. Hierarquia dos argumentos

A ordem dos blocos é a estratégia da página, não decoração. Três padrões que funcionam:

**Preço primeiro** — para tráfego frio de oferta e desconto. Título, preço, benefícios, foto. Elimina rápido quem não vai comprar, o que é bom: visita que não converte custa dinheiro.

**Imagem primeiro** — para produto que se vende pelo visual (moda, decoração, acessório). Título, foto grande, benefícios, preço. O desejo vem antes do número.

**Processo primeiro** — para primeira compra, ticket alto ou loja desconhecida. A objeção não é o produto, é o que acontece depois de clicar. Explique o caminho, numerado, e termine na ação.

Regras que valem nos três:

- **Um argumento por bloco.** Bloco com três ideias não tem nenhuma.
- **Benefício antes de especificação.** "Dura o dia todo" antes de "bateria 5000mAh".
- **Prova perto do preço.** É ali que a dúvida aparece.
- **CTA repetido.** Ao menos na dobra, depois do preço e no fim. Barra fixa cobre o meio.
- **Nunca invente prova.** Depoimento fabricado, avaliação falsa, contador de estoque mentiroso e "23 pessoas vendo agora" sem dado real são fraude e destroem a marca quando descobertos. Sem prova real, o bloco não existe.

---

## 5. CTA

- **Um CTA primário por página.** Botão secundário concorrendo com o principal divide o clique.
- **Texto na primeira pessoa do visitante**, dizendo o que ele recebe: "Quero aproveitar", "Falar no WhatsApp agora". Nunca "Enviar", "Clique aqui", "Saiba mais".
- **Largura cheia no telefone.** Botão centralizado estreito é alvo pior e parece menos importante.
- **Contraste alto** entre o botão e tudo em volta. Se a página tem cinco elementos coloridos, o botão deixou de ser o mais chamativo.
- **Nota de apoio embaixo**, curta, matando o último atrito: "sem cadastro", "resposta em minutos", "compra pelo WhatsApp".
- **Link externo** leva `target="_blank"` com `rel="noopener noreferrer"`.
- **Nome acessível sempre.** Se o botão fica só com ícone em algum estado, o rótulo continua no HTML escondido por `clip-path`, nunca por `display: none` — leitor de tela precisa anunciar a ação.
- **Estado desabilitado honesto.** Em prévia ou pré-visualização, use `role="button"` com `aria-disabled="true"` em vez de um link que não vai a lugar nenhum.

---

## 6. Performance

Em landing, performance **é** conversão. Cada segundo de espera derruba a taxa, e quem chega de anúncio não tem paciência nem investimento na marca.

### Metas

| Métrica | Meta | Por que importa |
|---|---|---|
| LCP | < 2,0s em 4G | é a imagem ou o título da dobra; acima de 2,5s a queda é medível |
| CLS | < 0,05 | conteúdo que salta faz o dedo errar o CTA |
| INP | < 200ms | primeiro toque tem que responder |
| Peso da dobra | < 200KB | o resto pode carregar depois |
| Requisições até a dobra | poucas, sem terceiros | cada domínio novo é um DNS + TLS |

### Como chegar lá

- **Imagem da dobra sem `loading="lazy"`.** Ela é o LCP; adiar é atirar no próprio pé. Use `fetchpriority="high"` nela e `loading="lazy"` em todas as outras.
- **`width`/`height` ou `aspect-ratio` em toda imagem.** Sem isso o texto salta quando a foto chega, e o CLS vai para o lixo.
- **Formato moderno** (WebP/AVIF) e tamanho real de entrega. Foto de 3000px servida num container de 360px é o desperdício mais comum.
- **`decoding="async"`** para a decodificação não travar a rolagem.
- **CSS crítico primeiro, fonte com `swap`.** Fonte bloqueante é tela branca.
- **Zero biblioteca de animação.** Landing anima com CSS e `IntersectionObserver`. Trazer uma lib de 40KB para fazer um fade é troca ruim.
- **Nada de biblioteca de gráfico, ícone completo ou framework de UI** só para a landing. Ícone é SVG inline.
- **Animar só `transform` e `opacity`.** `width`, `height`, `top` e `margin` forçam layout a cada quadro e travam no telefone intermediário.
- **`blur()` com parcimônia.** Blur grande em elemento que se move é o assassino silencioso de FPS em Android médio.
- **Terceiros depois da dobra.** Pixel, chat e mapa entram após o conteúdo principal.
- **Meça em aparelho intermediário e 4G**, não no desktop com fibra.

---

## 7. Movimento

Landing pode e deve ter movimento — é linguagem do formato, ao contrário do aplicativo, onde paralaxe e efeito de rolagem são erro. Mas movimento tem função: dirigir o olhar e dar vida, não impressionar.

- **Reveal-on-scroll** com `IntersectionObserver`, disparando **uma vez** e desconectando. Efeito que reaparece a cada rolagem irrita.
- **Distância curta.** 12 a 24px de deslocamento, 400–600ms, easing de saída. Bloco que voa 200px parece propaganda de 2012.
- **Sem escalonamento longo.** Atraso em cascata acima de ~80ms por item faz o visitante esperar a página se montar.
- **Paralaxe e progresso de rolagem são permitidos**, com uma regra: quem desenha é o CSS. O JavaScript publica a posição numa variável (`--scroll`, 0 a 1) dentro de `requestAnimationFrame` e não toca em estilo diretamente. Isso mantém o efeito em `transform`/`opacity` e barato.
- **Nunca scroll-jacking.** Sequestrar a rolagem, travar seções ou forçar velocidade própria quebra a expectativa e enjoa no telefone.
- **Sem autoplay com som.** Vídeo, se houver, é `muted`, `playsinline` e com `poster`.
- **Carrossel só manual**, com indicação clara de que há mais fotos e suporte a arrastar (limiar de ~44px para não confundir com rolagem).
- **`prefers-reduced-motion` desliga tudo** e restaura o estado final. Elemento que só aparece via animação **precisa** ficar visível nesse modo, senão o conteúdo desaparece para quem pediu menos movimento.
- Timings gerais em [animations.md](animations.md) continuam valendo.

---

## 8. Modernidade sem cair no genérico

Landing moderna não é fundo preto com neon roxo e vidro fosco em tudo. Isso já é o visual padrão de IA e o público reconhece.

Onde buscar sofisticação:

- **Tipografia com personalidade.** Contraste entre um display com carga (serifa editorial, sem-serifa apertada e pesada) e um corpo neutro faz mais pela página do que qualquer gradiente.
- **Espaço.** Respiro generoso entre blocos é o que separa premium de amador.
- **Uma cor de destaque, usada pouco.** Destaque em tudo é destaque em nada.
- **Um efeito de assinatura, não cinco.** Escolha: fundo em movimento, ou vidro, ou grão, ou brilho. Empilhar todos deixa lento e confuso.
- **Foto tratada.** Recorte, proporção consistente e fundo limpo pesam mais que efeito de CSS.
- **Detalhe pequeno e caro.** Um selo bem posicionado, um risco animado sob o título, o preço com `tabular-nums` alinhado. Refinamento se nota de perto.

Sinais de página datada: sombra dura preta, gradiente arco-íris, borda de 3px, ícone de biblioteca colorida misturado com linear, texto centralizado em parágrafo longo, "Lorem ipsum" sobrevivente.

---

## 9. Sistema de cor

- **A landing tem conjunto próprio de tokens**, separado do painel. Página pública de venda não deve herdar a identidade da ferramenta administrativa, e o dark/light do app não se aplica: a landing escolhe seu tom.
- **Contrato fechado por paleta.** Cada paleta define **todas** as variáveis que a folha consome. Nenhuma cor literal nova entra no CSS depois disso — só variáveis. Assim, acrescentar paleta não exige tocar em estilo.
- **Papéis separados para o destaque.** Uma cor que decora raramente serve para ser lida. Separe: um tom para decoração, um mais escuro/claro para texto de destaque, e um par para o preenchimento do botão. Sem isso, o texto sobre o destaque não fecha 4.5:1 em todas as paletas.
- **Meça o contraste nos pares que decidem a venda**, em cada paleta: preço sobre a superfície, texto de apoio sobre a superfície, benefício sobre o cartão, urgência sobre seu fundo, e texto do botão sobre o gradiente. Paleta clara é a mais arriscada — sobre branco, o piso de 4.5:1 é o que define o tom do texto secundário.
- **Nunca use cor como único portador de informação.** Desconto, urgência e disponibilidade também precisam de texto ou forma.

---

## 10. Formulário (quando houver)

Todo campo custa conversão. O melhor formulário de landing é não ter formulário — se WhatsApp resolve, use WhatsApp.

Se precisar:

- **Só o essencial.** Cada campo extra derruba a taxa. Sobrenome, empresa e "como nos conheceu" quase nunca se pagam.
- **Um campo por linha** no telefone.
- **`type`, `inputmode` e `autocomplete` corretos.** `inputmode="numeric"` para telefone, `type="email"` para e-mail, `autocomplete="tel"`, `autocomplete="name"`. Isso troca o teclado e o autopreenchimento do sistema.
- **Fonte ≥ 16px**, senão o iOS dá zoom e desloca a tela.
- **Label real e visível.** Placeholder como label é falha de acessibilidade e desaparece na digitação.
- **Erro embaixo do campo**, com texto que diz o que fazer, ligado por `aria-describedby`.
- **Botão com estado de envio** e proteção contra duplo clique.
- **Máscara não deve travar a digitação** nem apagar o que a pessoa colou.

---

## 11. Acessibilidade

Landing tem público mais amplo que app — inclui quem tem pouca visão, tela no sol, mão trêmula, conexão ruim.

- **Um `<h1>` só**, que é o título da oferta, com hierarquia real depois (`h2` nas seções).
- **`alt` descritivo** na foto do produto. Imagem puramente decorativa recebe `alt=""`.
- **Botão é `<button>` ou `<a>`.** `div` com `onClick` não recebe foco nem teclado.
- **`:focus-visible` visível** em fundo escuro e claro.
- **Contraste AA** em todo texto, inclusive no preço grande e no selo pequeno.
- **Conteúdo não pode depender de animação para existir** (ver `prefers-reduced-motion` na seção 7).
- **Zoom até 200% sem quebra.** Texto em `px` fixo dentro de container de altura fixa é o que quebra primeiro.
- **Ordem de leitura igual à ordem visual.** Reordenar com `order` do flex confunde leitor de tela.
- Detalhes em [accessibility.md](accessibility.md).

---

## 12. Quality gate — landing page

Rode antes de considerar a página pronta.

**Estratégia**

- [ ] A página tem **uma** ação, e ela está clara em 3 segundos
- [ ] A dobra responde: o que é, quanto custa, o que eu faço
- [ ] A ordem dos blocos corresponde à objeção principal do público
- [ ] Nenhuma prova social, avaliação ou escassez inventada
- [ ] Nenhum menu, sidebar, bottom nav ou link que tire o visitante da página
- [ ] CTA presente na dobra, perto do preço e no fim

**Mobile**

- [ ] Coluna única, testada em 320, 360, 390 e 430px
- [ ] Sem rolagem horizontal
- [ ] CTA alcançável pelo polegar, largura cheia ou quase
- [ ] Barra fixa inferior com `env(safe-area-inset-bottom)` e espaço reservado no fim da página
- [ ] `100dvh` em vez de `100vh`
- [ ] Alvos de toque ≥ 44x44px
- [ ] Corpo de texto ≥ 16px
- [ ] Preço grande não estoura a linha em 320px
- [ ] Estado `:active` presente em tudo que é tocável
- [ ] Testada dentro do navegador embutido do Instagram, se o tráfego vem de lá

**Performance**

- [ ] Imagem da dobra sem `lazy`, com `fetchpriority="high"`
- [ ] Toda imagem com dimensão ou `aspect-ratio` declarada
- [ ] Imagens em formato moderno e no tamanho de entrega real
- [ ] Nenhuma biblioteca de animação, ícone ou UI carregada só para a landing
- [ ] Animações apenas em `transform`/`opacity`
- [ ] Fonte com `font-display: swap`, poucos pesos
- [ ] Terceiros carregando depois da dobra
- [ ] Medida em aparelho intermediário e rede 4G

**Forma**

- [ ] Cores vindas dos tokens da landing, nenhuma literal na folha
- [ ] Contraste medido nos pares que decidem a venda, em todas as paletas
- [ ] Um efeito de assinatura, não cinco
- [ ] Reveal dispara uma vez e desconecta
- [ ] Nada acima da dobra animando entrada
- [ ] `prefers-reduced-motion` desliga o movimento **e** mantém o conteúdo visível
- [ ] Um `<h1>`, hierarquia real, `alt` descritivo, `:focus-visible` visível
- [ ] Zoom em 200% sem quebra

**Pergunta final:** um estranho, no telefone, no 4G, com a página aberta por 5 segundos, entende a oferta e sabe onde clicar? Se não, o problema não é estética.
