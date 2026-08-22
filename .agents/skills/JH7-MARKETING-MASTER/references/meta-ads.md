# Meta Ads — estrutura, entrega e tracking

Carregue para: estruturar conta, criar campanha, escolher público, otimizar, escalar, configurar eventos.

Escopo: estratégia e operação de mídia. Para catálogo/feed/Marketing API use a skill `JH7-META-ADS`.

---

## 1. Premissa atual da plataforma

A entrega hoje é dominada por machine learning. O sistema de recomendação (Andromeda e camadas de ranqueamento associadas), Advantage+ e os modelos de otimização por IA encontram o comprador melhor do que segmentação manual estreita, **desde que recebam sinal de conversão limpo e variedade de criativo**.

Consequências práticas:

- Estrutura enxuta vence estrutura fragmentada. Poucas campanhas, poucos conjuntos, orçamento consolidado.
- Público amplo (ou amplo com sugestão de interesse) tende a superar empilhamento de interesses, exceto em nichos muito específicos ou raio local pequeno.
- O criativo é hoje a principal variável de segmentação: o conceito escolhe o público.
- Evitar edições constantes. Cada alteração relevante reinicia aprendizado.
- Sair da fase de aprendizado exige volume de evento otimizado — como referência de projeto, ~50 conversões por conjunto por semana.

**Nunca assuma que estrutura antiga (CBO com 10 conjuntos de interesse, 1 anúncio por conjunto) segue sendo o melhor caminho.** Antes de escrever recomendação sobre formato novo, limite de campanha ou recurso recém-lançado, consulte a documentação oficial da Meta.

---

## 2. Estruturas de referência

**Aquisição enxuta (padrão para a maioria dos projetos)**
```
1 campanha (objetivo = evento de dinheiro)
  1–2 conjuntos: público amplo + segmentação avançada/Advantage+
    4–6 anúncios com conceitos realmente diferentes
```

**Teste de conceito**
```
1 campanha de teste, orçamento próprio
  1 conjunto amplo
    3–5 anúncios = 3–5 ângulos distintos
Vencedor migra para a campanha de aquisição
```

**Local (raio pequeno)**
```
1 campanha
  1 conjunto: raio compatível com a operação (5–30 km) + idade/idioma
    criativos com referência geográfica explícita
```
Em raio pequeno o público satura rápido: troque criativo com mais frequência e acompanhe frequência semanal.

**Remarketing** — só quando há volume de tráfego suficiente para justificar. Com pouco volume, remarketing rouba entrega da aquisição.
```
1 conjunto: visitantes 7/14/30d + engajamento + lista de clientes
  criativos de objeção, prova social, oferta e urgência real
```

---

## 3. Escolha de objetivo e evento

| Situação | Objetivo | Evento de otimização |
| --- | --- | --- |
| Vender online com tracking confiável | Vendas | Purchase |
| Volume baixo de compra (<50/semana) | Vendas | evento intermediário forte (AddToCart / InitiateCheckout) e subir depois |
| Gerar lead para atendimento humano | Cadastros | Lead (com qualificação no formulário) |
| Conversa comercial no WhatsApp | Vendas ou Engajamento com destino WhatsApp | conversa iniciada / mensagem qualificada |
| Instalação/ativação de app ou SaaS | Vendas / App | evento de ativação, não instalação |
| Tráfego para conteúdo (topo) | Tráfego | LPV — só quando o objetivo real é topo de funil |

Regra: otimize sempre para o evento mais próximo do dinheiro que tenha volume suficiente. Otimizar para clique quando o objetivo é venda entrega cliqueiro.

**Formulário nativo vs landing page.** Formulário nativo gera lead mais barato e menos qualificado; landing page gera lead mais caro e mais quente. Se o comercial reclama de qualidade, teste landing page ou adicione perguntas de qualificação.

---

## 4. Públicos

Ordem de preferência:
1. **Amplo** com apenas idade, gênero (se relevante de fato) e localização.
2. **Amplo + sugestão de interesse/comportamento** como sinal inicial.
3. **Lookalike 1–3%** de compradores reais (não de leads brutos).
4. **Interesse específico** só em nicho técnico ou B2B muito recortado.
5. **Remarketing** segmentado por profundidade (visitante → carrinho → cliente).

Cuidados:
- Não empilhe exclusões que sufoquem a entrega.
- Não crie conjuntos que se sobreponham disputando o mesmo leilão.
- Listas de clientes (Custom Audience por CRM) são um dos sinais mais valiosos: mantenha atualizadas.
- Exclua compradores recentes de campanhas de aquisição quando não houver recompra rápida.

---

## 5. Tracking e sinal

Checklist mínimo:

- Pixel instalado e disparando os eventos padrão certos.
- **Conversions API** ativa (server-side) com deduplicação por `event_id` compartilhado com o Pixel.
- Parâmetros de correspondência avançada enviados quando houver consentimento (e-mail, telefone, nome — hasheados).
- Eventos priorizados configurados no Events Manager (relevante para tráfego iOS).
- Domínio verificado no Business Manager.
- UTMs padronizadas e consistentes com o CRM.
- Qualidade do evento monitorada no Events Manager (correspondência, deduplicação, atraso).

Sem Conversions API, esperar performance de conta madura é ilusão. Se o projeto não tem, essa é normalmente a ação 🔴 de maior impacto.

**Privacidade:** trate dados pessoais conforme LGPD. Consentimento antes de disparar eventos de marketing, dados hasheados, sem enviar PII em texto puro para plataforma nenhuma.

---

## 6. Rotina de otimização

**Diário (5 min):** entrega travada? reprovação? gasto anormal? evento parou de chegar?

**A cada 3 dias:** CPA por anúncio, matar quem está claramente fora da meta com volume suficiente, checar frequência.

**Semanal:** CPA/ROAS/lucro por campanha, qualidade de lead com o comercial, subir 1–3 conceitos novos, revisar hipótese em aberto.

**Mensal:** revisar oferta, público, estrutura, orçamento por canal, MER consolidado, aprendizados no `MARKETING_CONTEXT.md`.

Não mexa em orçamento e criativo no mesmo dia — você perde a leitura da causa.

---

## 7. Erros comuns

- Fragmentar orçamento em muitos conjuntos com pouco volume.
- Editar campanha todos os dias e reiniciar aprendizado.
- Julgar criativo em 24h.
- Otimizar para clique quando o objetivo é venda.
- Interesse estreito em conta com sinal de conversão bom.
- Remarketing grande demais para o volume de tráfego existente.
- Mesmo anúncio com pequenas trocas de palavra, chamado de "teste de criativo".
- Escalar sem checar capacidade de atendimento.
- Comparar períodos com janela de atribuição diferente.
- Confiar só no painel da Meta para decidir lucro.

---

## 8. Ao lidar com recurso novo

Se a tarefa envolver formato, automação ou recurso recente (novos modos Advantage+, mudanças de estrutura de campanha, novos controles de criativo, alterações de atribuição ou de segmentação por IA):

1. Consulte a documentação oficial (Meta Business Help, Meta for Developers, Meta Engineering) antes de recomendar.
2. Diga explicitamente o que é documentação oficial e o que é leitura de mercado.
3. Se não conseguir confirmar, declare a incerteza e proponha teste controlado em vez de afirmar.
