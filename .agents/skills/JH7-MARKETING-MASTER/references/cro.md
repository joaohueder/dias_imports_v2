# CRO — otimização de conversão

Carregue para: auditar site, landing page, página de produto, página de vendas, checkout, formulário.

---

## 1. Pergunta que guia tudo

> **Qual é a próxima ação que eu quero que o usuário realize nesta tela?**

A tela inteira precisa conduzir para essa ação. Tudo que não ajuda, atrapalha.

Uma ação principal por página. Se houver ação secundária, ela precisa ser visualmente subordinada.

---

## 2. Auditoria acima da dobra (mobile)

Em 5 segundos no celular o visitante precisa entender:

- [ ] **O que é** — produto/serviço identificado
- [ ] **Para quem é** — reconhecimento imediato
- [ ] **Qual o resultado** — promessa concreta
- [ ] **Por que confiar** — prova visível (nota, número, logo, depoimento curto)
- [ ] **O que fazer** — CTA claro e alcançável com o polegar

Faltando qualquer item, corrija antes de qualquer outra otimização.

---

## 3. Checklist completo

**Proposta de valor**
- [ ] Headline específica, não intercambiável com concorrente
- [ ] Subheadline explica para quem e como
- [ ] Promessa alinhada com o anúncio que trouxe o visitante
- [ ] Benefício em resultado, não em feature

**Hierarquia visual**
- [ ] Um único elemento dominante por seção
- [ ] CTA com o maior contraste da tela
- [ ] Escaneável por subtítulos
- [ ] Espaçamento consistente, sem poluição

**CTA**
- [ ] Verbo de ação + resultado ("Receber orçamento em 1h")
- [ ] Repetido ao longo da página em pontos de decisão
- [ ] Botão com área de toque ≥ 44px
- [ ] Diz o que acontece depois do clique

**Prova**
- [ ] Depoimento com nome, foto e resultado
- [ ] Números reais
- [ ] Casos ou antes/depois
- [ ] Selos, avaliações, credenciais
- [ ] Prova próxima do CTA, não só no fim

**Objeções**
- [ ] 3+ objeções principais tratadas no corpo
- [ ] FAQ cobrindo as dúvidas reais do atendimento
- [ ] Preço com contexto e ancoragem
- [ ] Garantia visível

**Fricção**
- [ ] Formulário com o mínimo de campos necessários
- [ ] Sem cadastro obrigatório antes do valor
- [ ] Sem campo redundante (CEP que preenche endereço, por ex.)
- [ ] Validação inline com mensagem descritiva
- [ ] Sem exigir dado que o negócio não vai usar
- [ ] Poucos passos até a conclusão, com progresso visível

**Confiança**
- [ ] CNPJ, endereço, telefone, canais de contato
- [ ] Política de privacidade e troca/devolução
- [ ] HTTPS e selos de pagamento no checkout
- [ ] Foto real (equipe, loja, produto) em vez de banco de imagem genérico

**Distração**
- [ ] Sem menu completo em landing de campanha
- [ ] Sem link que leve o usuário para fora do fluxo
- [ ] Sem pop-up que atrapalhe antes da leitura
- [ ] Sem carrossel automático na dobra

---

## 4. Mobile first — obrigatório

A maior parte do tráfego pago é mobile. Projete e teste no celular primeiro.

- Fonte de corpo ≥ 16px; headline grande e legível
- Parágrafos de 1–3 linhas
- Botão alcançável com o polegar; CTA fixo quando fizer sentido
- Formulário com teclado correto por campo (`type="tel"`, `type="email"`, `inputmode`)
- Imagens otimizadas; peso da página controlado
- LCP bom no 4G, não só no wi-fi
- Sem rolagem horizontal, sem zoom necessário
- WhatsApp com um toque quando for o canal comercial
- Sensação de aplicativo quando o projeto pedir (ver skill `JH7-DESIGNER-APP`)

---

## 5. Velocidade

Velocidade é conversão. Sintoma clássico: CTR bom no anúncio e LPV muito abaixo dos cliques.

Verifique: peso e formato das imagens, número de scripts de terceiro, fonte bloqueando renderização, JS não usado, cache/CDN, TTFB do servidor, LCP mobile.

Ordem prática de ganho: imagens → scripts de terceiro → fontes → JS → servidor.

---

## 6. Formulários

| Situação | Campos |
| --- | --- |
| Lead simples | nome, WhatsApp |
| Lead qualificado | nome, WhatsApp, + 1–2 perguntas de qualificação |
| Orçamento | nome, WhatsApp, tipo de necessidade, prazo |
| Checkout | só o necessário para cobrar e entregar |

Cada campo extra reduz conversão e aumenta qualificação. Escolha conscientemente: comercial reclamando de qualidade → adicione pergunta. Volume baixo demais → remova campo.

Sempre: label real (não só placeholder), erro descritivo próximo ao campo, sem limpar o formulário após erro, feedback claro no envio, mensagem de sucesso dizendo o próximo passo.

---

## 7. Checkout / ecommerce

- Checkout em uma página ou com passos visíveis
- Convidado permitido, sem cadastro obrigatório
- Frete e prazo calculados cedo, sem surpresa
- Resumo do pedido sempre visível
- Múltiplas formas de pagamento (Pix, cartão, parcelas)
- Cupom sem tirar o usuário do fluxo
- Order bump relevante e opcional
- Sem link de saída no checkout
- Recuperação de carrinho por e-mail e WhatsApp

---

## 8. Priorização de teste

Ordene por `impacto × confiança ÷ esforço`. Onde normalmente está o maior ganho, em ordem:

1. Oferta e proposta de valor
2. Headline acima da dobra
3. Redução de campos do formulário
4. Velocidade mobile
5. Prova social perto do CTA
6. Tratamento de objeção / FAQ
7. Clareza e posição do CTA
8. Layout e cor do botão (último — costuma ser o menor ganho)

Teste uma variável relevante por vez. Volume mínimo para landing: ~100 conversões por variação para detectar diferença de ~20%. Sem volume, prefira mudanças óbvias de usabilidade a teste A/B estatístico.

---

## 9. Formato de entrega da auditoria

```
## Diagnóstico
o que a página comunica hoje e onde perde o visitante

## Gargalo principal
o item que mais custa conversão

## Correções 🔴 ALTA
1. ...
## Correções 🟡 MÉDIA
## Correções 🟢 BAIXA

## Hipótese de teste
hipótese, controle, variação, métrica, volume mínimo, critério
```

Sempre com item concreto ("trocar a headline X por Y"), nunca "melhorar a comunicação".
