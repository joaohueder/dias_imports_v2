# UX Checklist — JH7-DESIGNER-APP

Rode estes checklists antes de considerar qualquer tela pronta. Se um item relevante falhar, corrija antes de finalizar.

**Landing page tem gate próprio.** Para página pública de conversão, rode o gate geral (seção 1) e depois o da seção 12 de [landing-pages.md](landing-pages.md) — **não** o gate mobile abaixo, que cobra Bottom Navigation, sidebar transformada e navegação de app, coisas que numa landing são erro.

---

## 1. Quality gate geral

- [ ] Hierarquia visual clara: o olho encontra a informação principal primeiro
- [ ] Consistência com o resto do app (componentes, nomes, padrões)
- [ ] Espaçamento na escala definida, sem valores aleatórios
- [ ] Tipografia na escala definida, sem tamanhos avulsos
- [ ] Cores vindas de tokens, nenhuma cor literal no componente
- [ ] Contraste conferido em light e dark
- [ ] Componentes reutilizados em vez de duplicados
- [ ] Performance: sem blur/sombra/animação em excesso, sem re-render desnecessário
- [ ] Estados tratados: loading, empty, error, focus, disabled, sucesso
- [ ] Feedback presente em toda ação
- [ ] Animações com duração e easing adequados; `prefers-reduced-motion` respeitado
- [ ] Responsivo em mobile, tablet e desktop
- [ ] Dark e light revisados (quando ambos existem)
- [ ] Funcionalidade preservada: APIs, rotas, permissões, validações, integrações
- [ ] Acessibilidade básica: semântica, teclado, labels, aria em ícones
- [ ] Permissões: área/ação sem permissão não aparece

---

## 2. Quality gate mobile (o mais importante)

- [ ] Parece um aplicativo e não um site
- [ ] O mobile foi realmente projetado, não apenas adaptado
- [ ] Sem scroll horizontal desnecessário (testado em 320px)
- [ ] Navegação adequada ao toque (Bottom Nav, drawer ou header contextual)
- [ ] Sidebar desktop removida/transformada no mobile
- [ ] Tabelas convertidas em listas ou cards
- [ ] Avaliado se Bottom Navigation faz sentido nesta arquitetura
- [ ] Avaliado se Bottom Sheet é melhor que modal em cada caso
- [ ] Modal complexo virou full-screen no mobile
- [ ] Botões e alvos com pelo menos 44x44px
- [ ] Ação principal alcançável pelo polegar (inferior, FAB ou sticky bar)
- [ ] Safe areas (topo, base, laterais) respeitadas em elementos fixos
- [ ] `viewport-fit=cover` presente no meta viewport
- [ ] `100dvh` em vez de `100vh`
- [ ] Teclado virtual não esconde ação primária nem campo em foco
- [ ] Inputs com `type`, `inputmode` e `autocomplete` corretos, fonte ≥ 16px
- [ ] Elementos fixos não cobrem conteúdo (padding compensatório aplicado)
- [ ] Toast não cobre Bottom Nav, ação primária nem teclado
- [ ] Uso confortável com uma mão
- [ ] Feedback de toque presente (press state) em tudo que é tocável
- [ ] Skeleton no loading, com a forma do conteúdo final
- [ ] Empty state com CTA confortável para toque
- [ ] Error state com mensagem clara e "Tentar novamente"
- [ ] Voltar previsível; scroll e filtros preservados ao retornar
- [ ] Gestos com alternativa por botão
- [ ] Testado em 320, 360, 390 e 430px
- [ ] Se instalado como PWA (sem barra de endereço), a navegação é autossuficiente

Pergunta final: escondendo a barra de endereço, alguém acreditaria que é um app nativo? Se não, continue melhorando.

---

## 3. Desktop

- [ ] Espaço horizontal aproveitado (não é uma coluna estreita centralizada)
- [ ] Sidebar com estado ativo claro e itens agrupados
- [ ] Estado colapsado da sidebar persistido, com tooltip
- [ ] Page header com título, meta/contador e ação primária
- [ ] Largura de leitura controlada em formulários e texto
- [ ] Tabela com header sticky, ordenação, seleção e ações
- [ ] Números alinhados à direita com `tabular-nums`
- [ ] Filtros à vista com chips do que está aplicado e opção de limpar
- [ ] Estado de filtros e aba na URL
- [ ] Hover em linhas, cards e itens de menu (dentro de `@media (hover: hover)`)
- [ ] Atalhos de teclado nas ações frequentes; `Esc` fecha overlays
- [ ] Detalhe em drawer quando manter a lista visível ajuda
- [ ] Sem FAB nem Bottom Navigation no desktop
- [ ] Densidade equilibrada: informativo sem parecer planilha

---

## 4. Tablet

- [ ] Navegação colapsável (não é sidebar desktop apertada nem Bottom Nav esticada)
- [ ] Grid em 2 colunas onde faz sentido
- [ ] Tabela com colunas essenciais ou cards em 2 colunas
- [ ] Alvos de toque adequados (é dispositivo de toque, não use hover como requisito)
- [ ] Portrait e landscape verificados
- [ ] Drawer e sheets dimensionados para a tela maior

---

## 5. Formulários

- [ ] Uma coluna no mobile; agrupamento em 2 colunas no desktop quando útil
- [ ] Labels visíveis e ligadas ao campo por `for`/`id`
- [ ] Obrigatoriedade indicada de forma não só visual
- [ ] Hint quando o formato não é óbvio
- [ ] Validação no blur, não a cada tecla
- [ ] Erro inline, próximo ao campo, explicando como corrigir
- [ ] Foco vai ao primeiro campo inválido ao submeter
- [ ] Máscara não impede colar valor
- [ ] Teclado correto por tipo de campo
- [ ] Ação primária acessível (sticky no mobile)
- [ ] Loading no botão, sem permitir duplo envio
- [ ] Formulário longo dividido em etapas ou seções
- [ ] Estado preservado ao navegar; aviso ao sair com alterações não salvas
- [ ] Sucesso confirmado com feedback e destino claro

---

## 6. Listagens

- [ ] Contador de registros visível
- [ ] Busca com debounce e indicador de carregamento
- [ ] Filtros com estado visível e opção de limpar
- [ ] Ordenação onde faz sentido
- [ ] Skeleton na primeira carga
- [ ] Empty state (sem dados) diferente de sem resultado de busca
- [ ] Ação de criar evidente
- [ ] Ações por item acessíveis (menu, não só hover/swipe)
- [ ] Paginação no desktop; carregar mais / infinito no mobile
- [ ] Status como badge com texto
- [ ] Desktop: tabela · Mobile: lista/cards
- [ ] Posição de scroll preservada ao voltar do detalhe
- [ ] Atualização em tempo real sem refresh e sem roubar o foco

---

## 7. Dashboards

- [ ] KPI principal em destaque
- [ ] Período/contexto selecionável e visível
- [ ] Variação com sinal, cor e período de comparação
- [ ] Sinal de variação correto para o tipo de métrica (alta de despesa não é verde)
- [ ] Gráficos legíveis no mobile (altura, labels reduzidos, tooltip por toque)
- [ ] Cores de gráfico funcionando em light e dark
- [ ] Empty state por widget, não tela vazia
- [ ] Skeleton preservando a altura dos cards
- [ ] Drill-down para o detalhe dos números
- [ ] Mobile: ordem por prioridade, KPIs em 2 colunas ou carrossel com snap
- [ ] Sem excesso de widgets em uma única tela

---

## 8. Navegação

- [ ] Rota ativa clara em todos os níveis
- [ ] Título/breadcrumb informa onde o usuário está
- [ ] Voltar volta um nível, não à home
- [ ] Botão/gesto voltar do Android fecha overlay antes de mudar rota
- [ ] Deep link funciona: abrir a URL direto entrega a tela correta
- [ ] Itens sem permissão ocultos
- [ ] Transições coerentes com a hierarquia
- [ ] Command palette ou busca global no desktop, quando o app é grande
- [ ] Mobile: 3–5 destinos principais, resto em "Mais"

---

## 9. Ações críticas

- [ ] Confirmação com ícone contextual, título direto e explicação da consequência
- [ ] Botão nomeado com o verbo da ação (não "OK")
- [ ] Variante `danger` em ação destrutiva
- [ ] Foco inicial na opção segura
- [ ] Loading no confirmar, sem duplo disparo
- [ ] Undo via toast quando a ação é reversível
- [ ] Confirmação por digitação em ação irreversível de alto impacto
- [ ] Resultado comunicado ao final

---

## 10. Antes de entregar

- [ ] Nenhuma regra de negócio alterada sem autorização
- [ ] Nenhuma biblioteca nova instalada sem necessidade real
- [ ] Build/lint passando
- [ ] Sem console.log e código morto deixados para trás
- [ ] Versão no rodapé atualizada, se o projeto usa esse padrão
- [ ] Documentação do projeto atualizada, se o projeto exige
