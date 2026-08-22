# Modelos de negócio — SaaS, local e ecommerce

Carregue a seção correspondente ao projeto.

---

## 1. SaaS

**Métricas**

| Métrica | Definição |
| --- | --- |
| MRR / ARR | receita recorrente mensal / anual |
| ARPU / ARPA | receita média por usuário / por conta |
| CAC | custo de aquisição de cliente |
| LTV | ARPA × margem bruta ÷ churn mensal |
| Churn | % de cancelamento no período (logo e receita) |
| NRR | receita retida líquida (com expansão) |
| Payback | CAC ÷ margem mensal por cliente |
| Time to Value | tempo até o primeiro resultado percebido |
| Activation rate | % que executa o evento de ativação |
| Feature adoption | % que usa a função central |

Referências: `LTV/CAC ≥ 3`, payback ≤ 12 meses, churn mensal saudável em SMB ≈ 3–5%, `NRR > 100%` indica expansão compensando cancelamento.

**Funil AARRR**

| Etapa | Foco | Erro comum |
| --- | --- | --- |
| Aquisição | canal e mensagem certos | investir em aquisição com onboarding ruim |
| Ativação | chegar ao valor rápido | trial sem guia, usuário nunca ativa |
| Retenção | uso recorrente | tratar churn como problema de suporte, não de produto |
| Receita | monetizar por valor | preço desconectado do valor entregue |
| Indicação | crescimento orgânico | pedir indicação antes do resultado |

**Ativação é o gargalo mais comum.** Defina o *activation event* (a ação que correlaciona com retenção) e otimize a mídia para ele, não para cadastro.

**PLG vs SLG:** ticket baixo, autoexplicativo e uso individual → product led (trial/freemium, onboarding, self-serve). Ticket alto, implantação complexa, decisão em comitê → sales led (demo, SDR, proposta). Modelos híbridos são comuns: PLG gera lead, vendas fecham as contas maiores.

**Trial vs freemium:** trial cria urgência e qualifica; freemium cria volume e depende de gatilho de upgrade claro (limite de uso, feature paga). Sem gatilho, freemium é custo.

**Alavancas de receita:** upsell de plano, expansão por assentos/uso, anual antecipado, add-ons, redução de churn (a alavanca mais barata).

**Redução de churn:** onboarding assistido, quick win no primeiro dia, alerta de uso caindo, contato proativo, motivo de cancelamento categorizado, oferta de downgrade em vez de saída.

---

## 2. Negócios locais

Aplica-se a: restaurante, pizzaria, loja, concessionária, veículos e motos, imobiliária, turismo, fotógrafo, estúdio, buffet, clínica, prestador de serviço, PME em geral.

**Diferenças em relação a ecommerce nacional**

| Característica | Implicação |
| --- | --- |
| Público limitado pelo raio | saturação rápida, criativo precisa girar mais |
| Compra frequentemente por urgência | velocidade de resposta define a venda |
| Confiança pesa muito | prova local, foto real, review, endereço visível |
| Conversão fora do site | ligação, WhatsApp, rota, visita — precisa medir |
| Concorrência local, não nacional | benchmark de mercado nacional não serve |
| Capacidade operacional limitada | escalar mídia sem capacidade destrói experiência |

**Stack mínimo**
1. Google Business Profile completo e ativo
2. Rotina de reviews (pedir sempre, responder sempre)
3. Landing simples com WhatsApp em um toque
4. Meta Ads com raio compatível e criativo com referência geográfica
5. Google Pesquisa para termos de urgência, se houver volume
6. Atendimento rápido e registrado
7. Base de clientes para reativação e recompra

**Erros frequentes:** raio grande demais para a operação, criativo sem identidade local, não medir clique no WhatsApp, ignorar review negativo, anunciar sem ter quem responda, esquecer a base de clientes antiga.

**Alavanca mais subestimada:** reativação da base existente. Cliente antigo custa uma fração do lead novo. Antes de subir orçamento, mapeie a base e crie campanha de reativação por ciclo de recompra.

**Métricas locais:** custo por conversa, tempo de primeira resposta, taxa conversa → visita, taxa visita → venda, ticket médio, frequência de recompra, ações no perfil do Google (rota, ligação, site).

---

## 3. Ecommerce

**Métricas**

| Métrica | Nota |
| --- | --- |
| AOV (ticket médio) | alavanca mais rápida de lucro |
| Margem de contribuição | após CMV, frete, taxa, embalagem |
| CAC / CPA | precisa caber na margem |
| ROAS e MER | MER (receita total ÷ mídia total) é a leitura mais honesta |
| Taxa de conversão | por dispositivo, sempre |
| Abandono de carrinho / checkout | diagnostique separadamente |
| Taxa de recompra | e intervalo médio entre compras |
| LTV por coorte | por mês de aquisição |
| CAC de primeira compra vs LTV 12m | define quanto pode gastar |

**Alavancas de lucro em ordem de facilidade**
1. Aumentar AOV — bundle, kit, order bump, frete grátis acima do ticket atual, upsell no checkout
2. Recuperar abandono — e-mail + WhatsApp em cadência curta
3. Aumentar recompra — pós-venda, cupom com prazo, reposição programada, CRM
4. Melhorar conversão — CRO (`cro.md`)
5. Reduzir CPA — criativo e oferta
6. Melhorar mix — empurrar produto de maior margem

**Catálogo e feed:** título, descrição, imagem, disponibilidade e preço corretos são pré-requisito para Advantage+/Shopping funcionar. Implementação técnica: skill `JH7-META-ADS`.

**CRM e retenção:** boas-vindas, pós-compra com dica de uso, pedido de review, reposição no ciclo do produto, reativação de inativo, programa de fidelidade. Base própria (e-mail/WhatsApp) reduz dependência de mídia paga.

**Erros frequentes:** julgar campanha só por ROAS da plataforma, ignorar margem por produto, frete surpresa no checkout, checkout longo, não medir conversão por dispositivo, abandonar cliente após a primeira compra.

---

## 4. Escolhendo onde atacar

| Sintoma | Alavanca prioritária |
| --- | --- |
| Muito lead, pouca venda | atendimento e oferta (`funil-comercial.md`) |
| Venda boa, lucro ruim | ticket, margem, mix (`oferta-preco.md`) |
| Cliente compra uma vez e some | retenção, recompra, CRM |
| CAC subindo | criativo, oferta, canal, conversão da página |
| Tráfego bom, conversão baixa | CRO (`cro.md`) |
| Nada acontece, ninguém procura | geração de demanda, posicionamento, oferta |
