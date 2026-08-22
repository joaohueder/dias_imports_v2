# Plano de marketing, orçamento e templates

Carregue para: montar plano completo, distribuir orçamento, criar cronograma, criar `MARKETING_CONTEXT.md`, registrar teste.

---

## 1. Estrutura do plano de marketing

Ordem obrigatória. Não pule para canais e criativos antes de fechar diagnóstico, ICP, posicionamento e oferta.

| # | Seção | Conteúdo mínimo |
| --- | --- | --- |
| 1 | Diagnóstico | situação atual, números disponíveis, gargalo principal |
| 2 | Mercado | tamanho, maturidade, sazonalidade |
| 3 | Concorrentes | tabela comparativa e espaço não ocupado |
| 4 | ICP / Persona | quem é e quem não é |
| 5 | Posicionamento | frase de posicionamento defensável |
| 6 | Oferta | oferta completa com garantia e ancoragem |
| 7 | Jornada | do primeiro contato à recompra |
| 8 | Funil | etapas, taxas atuais e metas por etapa |
| 9 | Canais | escolhidos com justificativa; o que fica de fora e por quê |
| 10 | Conteúdo | temas por nível de consciência e frequência |
| 11 | Tráfego | estrutura de campanha, públicos, objetivos |
| 12 | Criativos | matriz com ângulos e hooks |
| 13 | Landing pages | páginas necessárias e função de cada uma |
| 14 | WhatsApp / Comercial | script, cadência, SLA de resposta |
| 15 | CRM | campos, etapas, motivos de perda |
| 16 | Retenção | pós-venda, recompra, indicação |
| 17 | Métricas | o que medir, onde e com qual frequência |
| 18 | Testes | fila de hipóteses priorizada |
| 19 | Orçamento | distribuição e justificativa |
| 20 | Cronograma | semanas 1–4 e meses 2–3 |
| 21 | Metas | número por etapa, com cálculo reverso |

Cada seção precisa ser executável. Se algum dado não existir, declare a hipótese usada e marque como "a confirmar".

---

## 2. Orçamento

**Regra:** trabalhe dentro da verba informada. Nunca proponha plano incompatível com a realidade do projeto.

| Verba mensal de mídia | Estratégia |
| --- | --- |
| Baixa | 1 canal, 1 oferta, 1 público amplo, 3–4 criativos. Foco total. Sem experimentação paralela. |
| Média | 1 canal principal + 1 secundário, remarketing, 2 ofertas, teste contínuo de criativo. |
| Alta | Múltiplos canais, verba dedicada a experimentação (10–20%), topo de funil e branding, criativo em produção contínua. |

Distribuição de referência para conta em crescimento: ~70% no que já performa, ~20% em teste estruturado, ~10% em aposta nova. Ajuste conforme maturidade.

Antes de aumentar orçamento, confirme as três condições: CPA dentro da meta com volume, lucro confirmado, capacidade de atendimento. Faltando qualquer uma, a recomendação é corrigir o gargalo, não subir verba.

Se a verba não é suficiente para gerar volume de decisão (ex.: menos de ~30 conversões/mês), diga isso e proponha reduzir escopo em vez de espalhar a verba.

---

## 3. Cronograma de referência (90 dias)

**Semana 1 — fundação**
Ler contexto do projeto, pesquisa de concorrentes e VoC, definir ICP e posicionamento, validar tracking e conversões, criar `MARKETING_CONTEXT.md`.

**Semana 2 — oferta e ativos**
Fechar oferta, escrever copy, publicar/ajustar landing page, definir script comercial e SLA de resposta, produzir os primeiros criativos.

**Semana 3 — subir**
Estrutura de campanha enxuta, 4–6 criativos de ângulos distintos, checar eventos chegando, monitorar entrega.

**Semana 4 — primeira leitura**
Analisar funil completo, identificar gargalo, cortar criativo ruim, registrar aprendizado, definir próximo teste.

**Mês 2 — otimização**
Ciclos semanais de criativo, CRO da página, ajuste de qualificação, cadência de follow-up, primeiro remarketing se houver volume.

**Mês 3 — escala e retenção**
Escalar o que provou lucro, abrir segundo canal se justificado, montar rotina de pós-venda, recompra e indicação, revisar unit economics.

Adapte prazos ao ciclo de venda do projeto. Alto ticket exige janelas maiores.

---

## 4. Template — `MARKETING_CONTEXT.md`

Crie na raiz do projeto (ou em `docs/`) quando não houver memória estratégica. Atualize sempre que houver aprendizado relevante.

```markdown
# Marketing Context — [Projeto]

Atualizado em: AAAA-MM-DD

## Empresa
## Produto/Serviço
## Mercado
## Público
## ICP
## Personas
## Anti-ICP
## Problemas
## Desejos
## Objeções
## Oferta
## Preços
## Margem e ticket médio
## Diferenciais
## Concorrentes
## Canais
## Meta Ads
## Google Ads
## SEO
## Conteúdo
## WhatsApp
## Comercial
## Funil (taxas atuais)
## Métricas
## CAC
## LTV
## Histórico de Testes
## Criativos Vencedores
## Criativos Perdedores
## Aprendizados
## Próximos Testes
```

Este arquivo é a memória estratégica do marketing. Leia antes de propor, atualize depois de executar.

Nota: `MARKETING_CONTEXT.md` guarda estratégia de marketing. Decisões de sistema, banco e arquitetura continuam em `docs/PROJETO.md` (skill `JH7-DOC-PROJETO`).

---

## 5. Template — registro de teste

```markdown
## Teste MKT-012

Data:
Canal:
Etapa do funil:

Hipótese:
Controle:
Variação:

Métrica principal:
Métricas secundárias:
Volume mínimo / critério de decisão:

Resultado:
Conclusão:
Aprendizado:
Próxima ação:
```

Regras:
- Numere sequencialmente e nunca apague registro antigo.
- Antes de propor teste novo, consulte o histórico.
- Teste que já falhou só volta com explicação de **por que falhou, o que mudou e qual a nova hipótese**.
- Registre também os inconclusivos: eles indicam volume insuficiente ou variável mal isolada.

O objetivo é o marketing do projeto ficar progressivamente mais inteligente.

---

## 6. Metas

Toda meta precisa de cálculo reverso (`diagnostico.md` §4) e de checagem de realismo:

- as taxas usadas vêm do histórico do projeto ou são hipótese declarada?
- o orçamento suporta o volume necessário?
- a operação suporta o volume de atendimento e entrega?
- a margem suporta o CAC resultante?

Se qualquer resposta for negativa, apresente a meta possível junto com a meta pedida e explique a diferença.
