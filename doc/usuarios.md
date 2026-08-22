# Módulo de Usuários e Permissões

## Objetivo

Gerenciamento completo de usuários, perfis de acesso (Administrador / Usuário comum) e matriz granular de permissões por módulo e ação (Ver, Criar, Editar, Excluir).

## Rotas Protegidas

| Método | Rota | Descrição |
| --- | --- | --- |
| `GET` | `/usuarios` | Listagem com cards, busca por nome/email e filtros rápidos |
| `GET` | `/usuarios/novo` | Formulário de criação de usuário com dados de acesso e permissões |
| `POST` | `/usuarios/novo` | Gravação do novo usuário com senha criptografada |
| `GET` | `/usuarios/(:num)/editar` | Formulário de edição de dados e permissões do usuário |
| `POST` | `/usuarios/(:num)/editar` | Atualização de dados cadastrais, role e matriz de permissões |
| `POST` | `/usuarios/(:num)/redefinir-senha` | Redefinição direta de senha |
| `POST` | `/usuarios/(:num)/status` | Ativação / Inativação de conta de usuário |
| `POST` | `/usuarios/(:num)/excluir` | Exclusão permanente de usuário |

## Regras de Negócio e Segurança

- **Papéis (Roles)**:
  - `admin`: Acesso total e irrestrito a todas as áreas e funcionalidades do painel. A tabela de permissões fica desabilitada e marcada informativamente.
  - `user`: Acesso personalizado conforme as marcações na matriz de permissões.
- **Auto-proteção**: O usuário logado não pode inativar ou excluir a sua própria conta nem rebaixar seu papel de administrador.
- **Hierarquia de Permissões**: Marcar qualquer ação de escrita (Criar, Editar, Excluir) marca e exige automaticamente a permissão de visualização (`view`) da respectiva área.
- **Segurança de Senha**: Senhas armazenadas com hash `PASSWORD_DEFAULT` (Bcrypt) com validação mínima de 6 caracteres.
- **Feedback & UX**: Barra flutuante de salvar/cancelar em alterações não salvas, modal animado de confirmação para ações críticas (inativação/exclusão) e modal de redefinição rápida de senha.

## Estrutura do Banco de Dados

- Colunas adicionadas na tabela `users`:
  - `role`: `ENUM('admin', 'user') NOT NULL DEFAULT 'user'`
  - `permissions`: `LONGTEXT NULL` (JSON com matriz de permissões)
- Migração incremental: `bd/012-usuarios-permissoes.sql` e migration CodeIgniter `2026-08-22-115606_AddRoleAndPermissionsToUsers.php`.
- Schema mestre atualizado: `bd/master.sql`.
