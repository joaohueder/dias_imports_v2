# Diagnóstico, métricas e gargalo

Carregue este arquivo para: analisar campanha, ler métricas, achar gargalo, calcular lucro, definir meta de CPA/CAC.

---

## 1. Ordem fixa de análise de campanha

Quando receber métricas, analise **nesta ordem**. Não pule etapas e não comece pelo criativo.

| # | Camada | Pergunta |
| --- | --- | --- |
| 1 | Objetivo | a campanha está otimizando para o evento que gera dinheiro? |
| 2 | Oferta | a proposta é boa o suficiente para o preço e o mercado? |
| 3 | Público | está compatível com o nível de consciência e a região atendida? |
| 4 | Criativo | o conceito comunica a oferta em 3 segundos? |
| 5 | CPM | custo de mídia está normal para o nicho/região/período? |
| 6 | CTR (link) | o criativo gera clique com intenção? |
| 7 | CPC | o custo por clique cabe no CPA-alvo? |
| 8 | LPV | quem clicou chegou a carregar a página? |
| 9 | CPL / CPA | custo por resultado está dentro da meta? |
| 10 | Qualidade do lead | o lead é do perfil, tem verba e urgência? |
| 11 | Conversão comercial | o time/atendimento converte o lead? |
| 12 | ROAS / MER | receita gerada sobre mídia |
| 13 | Lucro | o que sobra depois de todos os custos |

Feche a análise sempre com: **o que está bom / o que está ruim / provável causa / prioridade / ação recomendada**.

---

## 2. Tabela de sintoma → causa provável → ação

| Sintoma | Causas prováveis | Ação |
| --- | --- | --- |
| CPM muito alto | público pequeno, leilão concorrido, criativo com baixo engajamento, sazonalidade, restrição de política | ampliar público, testar novo conceito, checar reprovações; CPM alto não é automaticamente criativo ruim |
| CTR baixo (<0,8% link) | hook fraco, oferta invisível, público errado, criativo genérico | novo hook nos 3 primeiros segundos, oferta explícita no criativo |
| CTR alto + CPC alto | disputa de leilão, formato caro, público muito estreito | ampliar público, testar posicionamento |
| CTR alto + LPV muito abaixo do clique | página lenta, erro de carregamento, link errado, redirect, mobile quebrado | medir LCP/TTFB no mobile, corrigir peso de imagem, revisar UTM/redirect |
| LPV bom + conversão baixa | proposta de valor fraca, headline desalinhada do anúncio, formulário longo, fricção, falta de prova, preço sem contexto | auditar página com `cro.md`, alinhar promessa anúncio↔página |
| CPL bom + poucas vendas | público desqualificado, oferta atrai curioso, atendimento lento, falta de follow-up, script ruim, preço fora | qualificar no formulário, medir tempo de 1ª resposta, revisar script e cadência |
| Vendas boas + lucro ruim | margem baixa, desconto excessivo, CAC alto, frete/taxa, mix de produto errado | recalcular unit economics, mudar mix, subir ticket com bundle/upsell |
| Resultado caiu de repente | fadiga de criativo, mudança de público, aumento de concorrência, quebra de tracking, sazonalidade | primeiro valide o tracking, depois frequência, depois criativo |
| Resultado instável dia a dia | volume baixo de conversão, orçamento fragmentado, edições frequentes | consolidar orçamento, parar de editar, avaliar por janela de 7 dias |

Regra: nunca conclua causa única sem checar a camada anterior do funil.

---

## 3. Unit economics

```
Margem de contribuição = Ticket médio − CMV − taxas − frete − comissão
CPA máximo             = Margem de contribuição × % que você aceita gastar em mídia
CAC                    = Mídia total + custo comercial ÷ nº de clientes novos
LTV                     = Ticket médio × margem % × nº de compras na vida do cliente
Payback (meses)        = CAC ÷ margem mensal por cliente
MER                    = Receita total ÷ investimento total em mídia
```

Referências de leitura:
- `LTV / CAC ≥ 3` é confortável; entre 1 e 2 exige atenção; abaixo de 1 é prejuízo por cliente.
- Payback aceitável depende do caixa: negócio local e ecommerce costumam precisar de payback na primeira compra; SaaS tolera 6–12 meses.
- ROAS-alvo aproximado = `1 ÷ margem de contribuição %`. Margem 40% → ROAS mínimo ≈ 2,5 só para empatar.

Sempre pergunte a margem antes de julgar ROAS. Sem margem, qualquer conclusão sobre "ROAS bom" é chute.

---

## 4. Cálculo reverso da meta

Comece pela meta financeira e volte até a impressão:

```
Meta de faturamento           R$ 100.000
Ticket médio                  R$ 2.000        → 50 vendas
Taxa de fechamento            20%             → 250 leads qualificados
Taxa de qualificação          50%             → 500 leads
Conversão da landing page     10%             → 5.000 visitantes
CTR link                      1,5%            → 333.000 impressões
CPM                           R$ 25           → R$ 8.325 de mídia
CAC resultante                R$ 166          → comparar com margem
```

Se qualquer etapa exigir número irreal (ex.: conversão de 40% em landing de alto ticket), a meta ou o orçamento estão errados. Diga isso.

---

## 5. Volume mínimo para decidir

Não declare vencedor sem volume.

| Decisão | Mínimo prático |
| --- | --- |
| Matar criativo obviamente ruim | ~1.000 impressões sem clique relevante |
| Comparar criativos | 50+ resultados por variação, ou 3–7 dias |
| Comparar landing pages | 100+ conversões por variação para diferenças de ~20% |
| Avaliar qualidade de lead | 30+ leads atendidos e classificados |
| Avaliar canal novo | 1 ciclo de venda completo |

Abaixo disso, informe explicitamente que o resultado é indicativo e não conclusivo.

---

## 6. Escala e corte

**Escalar quando:** CPA dentro da meta por pelo menos 5–7 dias, lucro confirmado, capacidade operacional de atender, tracking confiável.

Como: aumentos de 20–30% no orçamento a cada 2–3 dias, ou duplicação horizontal (novo conjunto/público). Evite dobrar de uma vez em conta com pouco histórico.

**Cortar quando:** CPA acima da meta com volume suficiente, frequência alta com CTR em queda, lead sem qualidade recorrente.

Antes de escalar, confirme: o comercial aguenta o volume? Se não, escalar só piora a experiência e o CAC.

---

## 7. Higiene de dados

Antes de qualquer conclusão, valide:
- evento de conversão dispara uma vez e no momento certo;
- deduplicação entre Pixel e Conversions API (mesmo `event_id`);
- UTMs padronizadas (`utm_source`, `utm_medium`, `utm_campaign`, `utm_content`, `utm_term`);
- janela de atribuição usada na comparação é a mesma;
- receita do painel de anúncio confere com a receita real do sistema/CRM;
- moeda, fuso e período iguais entre as fontes.

Divergência entre plataforma e CRM é normal. Use o CRM/financeiro como verdade para decisão de lucro e a plataforma para otimização.
