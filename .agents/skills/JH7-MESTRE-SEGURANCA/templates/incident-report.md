# Incident Report — [título do incidente]

> ID: `INC-AAAA-NN` · Severidade: P0 / P1 / P2 / P3
> Detectado em: `AAAA-MM-DD HH:MM` (TZ) · Encerrado em: `AAAA-MM-DD HH:MM`
> Status: em andamento / contido / resolvido / em post-mortem

---

## 1. Resumo

O que aconteceu, em 3 a 5 linhas, sem jargão. O que foi afetado e o que já foi feito.

---

## 2. Linha do tempo

| Horário | Evento | Quem |
| --- | --- | --- |
| | primeiro sinal / alerta | |
| | detecção confirmada | |
| | evidência preservada (logs copiados) | |
| | contenção aplicada | |
| | causa raiz identificada | |
| | correção implantada | |
| | serviços normalizados | |
| | comunicação enviada | |

---

## 3. Detecção

**Como foi detectado:** alerta automático / relato de usuário / revisão de código / secret scanning / observação manual.

**Por que não foi detectado antes:** lacuna de monitoramento, log ausente, ausência de teste.

---

## 4. Impacto

| Dimensão | Detalhe |
| --- | --- |
| Dados alcançáveis pela falha | |
| Dados comprovadamente acessados | |
| Titulares afetados (quantidade) | |
| Tenants afetados | |
| Havia dado pessoal / sensível / financeiro? | |
| Credenciais expostas | |
| Duração da exposição | |
| Indício de exfiltração | |
| Indisponibilidade | |

Distinguir sempre **alcançável** de **comprovadamente acessado**. Registrar qual evidência sustenta cada afirmação e o que não foi possível determinar.

---

## 5. Causa raiz

A causa técnica real, não o sintoma. Incluir o trecho responsável quando aplicável.

```text
arquivo:linha — descrição
```

**Fatores contribuintes:** ausência de teste, revisão que não cobriu o caso, padrão inseguro repetido, pressão de prazo, documentação divergente.

---

## 6. Contenção e erradicação

| Ação | Horário | Resultado |
| --- | --- | --- |
| Credencial rotacionada | | |
| Sessões revogadas | | |
| Endpoint desativado | | |
| Origem bloqueada | | |
| Correção implantada | | |
| Busca por persistência (usuário/chave/webhook/cron novos) | | |
| Mesmo padrão verificado em outros endpoints | | |

---

## 7. Recuperação

- [ ] dados restaurados de backup íntegro (verificado que o backup não contém o comprometimento)
- [ ] serviços reativados com monitoramento reforçado
- [ ] troca de senha / revogação forçada quando aplicável
- [ ] teste automatizado reproduzindo a falha (falha antes, passa depois)
- [ ] correção presente no pipeline

---

## 8. Comunicação

| Público | Conteúdo | Quando | Responsável |
| --- | --- | --- | --- |
| Interno | | | |
| Clientes afetados | | | |
| Autoridade (quando aplicável) | | | |

Avaliação de comunicação regulatória (LGPD) é decisão do responsável legal/DPO. Este relatório fornece os fatos técnicos que embasam a decisão.

---

## 9. Ações preventivas

Sem culpa individual. Cada item precisa de responsável e de forma de verificação.

| # | Ação | Tipo | Responsável | Prazo | Como verificar |
| --- | --- | --- | --- | --- | --- |
| 1 | | teste / alerta / padrão / processo | | | |
| 2 | | | | | |

---

## 10. Lições aprendidas

**O que funcionou:**

**O que não funcionou:**

**O que mudaríamos no processo:**

**Como saberemos que não volta:** o teste, alerta ou controle específico que detectaria a recorrência.
