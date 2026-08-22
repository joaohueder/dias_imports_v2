# Central de Trabalho (Background Jobs & Queue)

A **Central de Trabalho** é o motor de processamento assíncrono e em segundo plano do sistema, responsável por enfileirar e executar rotinas de integração, sincronizações e manutenções periódicas sem sobrecarregar as requisições HTTP do usuário.

---

## 1. Localização e Acesso

- **Monitoramento em Tempo Real:** Localizado no menu lateral em `/central-trabalho` (imediatamente antes de *Usuários*).
- **Configuração de Intervalos e Ativação:** Localizado em `/configuracoes?tab=central-trabalho`.
- **Permissão de Acesso:** Controlada pelo módulo `job_center` (visualizar, editar, excluir) e `central_trabalho` em configurações.

---

## 2. Trabalhos Suportados

### 1. Atualizar Todos os Grupos do WhatsApp (`sync_whatsapp_groups`)
- **Objetivo:** Consultar a Evolution API para cada grupo do WhatsApp cadastrado no banco, trazendo nome atualizado, descrição, contagem de participantes e foto de perfil (`avatar_url`).
- **Intervalo Randomizado:** Configurado com tempo mínimo e máximo em segundos (ex.: 5 a 20 segundos) para evitar bloqueios ou sobrecarga na Evolution API/WhatsApp.
- **Fila Segura:** Para cada grupo é gerado um item na tabela `system_job_queue` com o timestamp escalonado (`scheduled_at`).

---

## 3. Estrutura do Banco de Dados

### Tabela `system_jobs`
Registra a parametrização de cada trabalho do sistema.

| Campo | Tipo | Descrição |
|---|---|---|
| `id` | INT UNSIGNED AUTO_INCREMENT | Identificador do trabalho |
| `job_key` | VARCHAR(100) UNIQUE | Identificador único da rotina |
| `name` | VARCHAR(255) | Nome amigável do trabalho |
| `description` | TEXT | Explicação do que o trabalho realiza |
| `is_active` | TINYINT(1) | Se o trabalho está ativo ou pausado |
| `min_delay_seconds` | INT UNSIGNED | Tempo mínimo de espera em segundos |
| `max_delay_seconds` | INT UNSIGNED | Tempo máximo de espera em segundos |

### Tabela `system_job_queue`
Fila operacional das tarefas a executar.

| Campo | Tipo | Descrição |
|---|---|---|
| `id` | BIGINT UNSIGNED AUTO_INCREMENT | Identificador da tarefa |
| `job_key` | VARCHAR(100) | Identificador do tipo de trabalho |
| `item_reference` | VARCHAR(191) | Referência (ex.: JID do grupo) |
| `payload` | LONGTEXT JSON | Dados contextuais para execução |
| `status` | ENUM('pending', 'processing', 'completed', 'failed') | Status da tarefa |
| `scheduled_at` | DATETIME | Data e hora agendada para execução |
| `started_at` | DATETIME | Quando o motor iniciou a tarefa |
| `completed_at` | DATETIME | Quando o motor finalizou a tarefa |
| `attempts` | INT UNSIGNED | Quantidade de tentativas |
| `error_message` | TEXT | Mensagem detalhada em caso de falha |

---

## 4. Configuração do Cron Job no Servidor Linux

Para garantir que o motor de execução rode continuamente, adicione a seguinte linha no `crontab` do servidor:

```bash
* * * * * cd /caminho/do/projeto/sistema && php spark jobs:process >> /dev/null 2>&1
```

O comando executará as tarefas agendadas a cada minuto, respeitando a ordem cronológica e o intervalo randomizado de espera.
