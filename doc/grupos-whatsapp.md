# Grupos de WhatsApp

O módulo **Grupos de WhatsApp** permite listar, sincronizar, criar, testar envio, ativar/inativar e gerenciar os grupos vinculados às instâncias da Evolution API v2.

## Funcionalidades

- **Sincronização com Evolution API**: busca todos os grupos em que o WhatsApp conectado participa (`/group/fetchAllGroups`).
- **Criação de Grupos**: cria novos grupos diretamente no WhatsApp através da API (`/group/createGroup`).
- **Envio de Teste**: dispara mensagens de teste personalizadas para validação de entrega no grupo.
- **Filtros e Busca**:
  - Filtro em tempo real por nome/descrição/categoria.
  - Filtros de status: *Todos*, *Ativos*, *Inativos*.
- **Controle de Acesso (RBAC)**: integrado à matriz de permissões (`whatsapp_groups`: `view`, `create`, `edit`, `delete`).

## Estrutura do Banco de Dados

Tabela `whatsapp_groups`:
- `id`: identificador único
- `group_jid`: JID do grupo no WhatsApp (`xxxx@g.us`)
- `instance_name`: nome da instância da Evolution API responsável
- `name`: título/assunto do grupo
- `description`: descrição do grupo
- `participants_count`: quantidade de participantes
- `avatar_url`: foto de perfil do grupo
- `status`: `active` ou `inactive`
- `is_admin_only`: flag indicando se apenas administradores enviam mensagem
- `is_restricted`: flag de restrição de configurações
- `category`: categoria/etiqueta do grupo
- `last_synced_at`: data e hora da última sincronização
- `created_at`, `updated_at`: timestamps
