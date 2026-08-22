# Resposta a incidentes

Plano de ação para quando a prevenção falhou. Objetivo: conter rápido, sem destruir a evidência.

---

## 1. Ciclo

```text
PREPARAÇÃO → DETECÇÃO → CONTENÇÃO → ERRADICAÇÃO
→ RECUPERAÇÃO → LIÇÕES APRENDIDAS
```

---

## 2. Preparação (antes do incidente)

- log centralizado com retenção suficiente para investigar (30–90 dias no mínimo);
- monitoramento com alerta: pico de 5xx, falhas de login em massa, acesso negado repetido, consumo anômalo, latência, disco;
- inventário de credenciais e como rotacionar cada uma;
- backup testado e procedimento de restauração escrito;
- contatos definidos: quem decide, quem executa, quem comunica;
- capacidade de revogar todas as sessões e todas as chaves rapidamente.

---

## 3. Detecção — sinais

| Sinal | Possível causa |
| --- | --- |
| Pico de falhas de login | brute force / credential stuffing |
| Muitos 403/404 em sequência por um token | enumeração / tentativa de IDOR |
| Consulta lenta incomum, `pg_sleep` no log | tentativa de SQL injection |
| Requisições do backend para IP interno | SSRF em andamento |
| Arquivo novo em diretório de upload com extensão inesperada | upload malicioso |
| Usuário virou admin sem trilha de aprovação | escalonamento de privilégio |
| Volume de leitura muito acima do normal | exfiltração |
| Custo de API/IA fora do padrão | abuso de recurso |
| Credencial usada de região/horário improvável | credencial comprometida |
| Alerta de secret scanning | credencial exposta |

---

## 4. Contenção (primeiras ações)

Prioridade: **parar o sangramento sem apagar o rastro.**

```text
1. registrar horário e o que se sabe (linha do tempo começa agora)
2. PRESERVAR evidência: copiar logs relevantes antes que rotacionem
3. revogar a credencial/sessão comprometida
4. bloquear a origem do ataque (IP, chave, conta) quando identificável
5. desativar temporariamente o endpoint vulnerável se não houver correção imediata
6. isolar o serviço afetado se houver suspeita de comprometimento do host
7. NÃO reiniciar nem recriar container antes de coletar log e estado
8. avisar quem decide
```

Em caso de secret exposto: **rotacionar primeiro**, limpar histórico depois. Ver [secrets-credenciais.md](secrets-credenciais.md#7-secret-exposto--resposta).

### O que não fazer

- apagar logs "para limpar";
- reescrever histórico do Git antes de rotacionar;
- reinstalar/recriar o ambiente antes de entender o vetor;
- comunicar publicamente antes de saber o escopo;
- culpar pessoa em vez de processo.

---

## 5. Erradicação

- identificar a **causa raiz**, não apenas o sintoma;
- corrigir a vulnerabilidade (com teste que reproduz a falha antes e passa depois);
- procurar persistência: usuário criado, chave adicionada, webhook novo, cron novo, permissão alterada, arquivo enviado, dependência modificada;
- verificar se o mesmo padrão de falha existe em outros endpoints (quase sempre existe);
- rotacionar tudo que a credencial comprometida podia alcançar.

---

## 6. Recuperação

- restaurar dado a partir de backup íntegro (verificar se o backup já não contém o comprometimento);
- reativar serviços gradualmente, com monitoramento reforçado;
- forçar troca de senha e revogação de sessão quando houver suspeita sobre credenciais de usuários;
- confirmar que o vetor está fechado com teste automatizado no pipeline.

---

## 7. Avaliação de escopo de dados

Para decidir sobre comunicação, responder com base em evidência (não em suposição):

```text
Quais dados eram alcançáveis pela falha?
Quais dados foram efetivamente acessados (log comprova)?
Quantos titulares? Quais tenants?
Havia dado pessoal, sensível, financeiro ou credencial?
Por quanto tempo a falha existiu?
Há indício de exfiltração (volume, destino, horário)?
```

LGPD: incidente com risco relevante aos titulares exige comunicação à ANPD e aos afetados em prazo razoável. Isso é requisito técnico-operacional a ser considerado — a decisão jurídica é do responsável legal/DPO, não da skill.

---

## 8. Comunicação

- interna primeiro, com fatos e status;
- externa com o que se sabe, o que foi feito e o que o usuário deve fazer;
- sem minimizar e sem especular;
- uma fonte única de verdade (documento do incidente), atualizada por horário.

---

## 9. Post-mortem

Sem culpa individual. Foco no que permitiu a falha existir e passar.

```markdown
## Resumo
## Linha do tempo (com horários)
## Impacto (dados, usuários, tenants, duração)
## Causa raiz
## Por que não foi detectado antes
## Correção aplicada
## Ações preventivas (com responsável)
## Como saberemos que não volta (teste/monitor criado)
```

Toda ação preventiva vira item concreto: teste automatizado, alerta, checklist, mudança de padrão. Post-mortem sem ação verificável não fecha o ciclo.

Template em [templates/incident-report.md](../templates/incident-report.md).
