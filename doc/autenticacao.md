# Autenticação

## Rotas

- `GET /login`: formulário e restauração da sessão persistente.
- `POST /login`: valida credenciais, inicia sessão e opcionalmente persiste por 30 dias.
- `POST /logout`: encerra sessão e revoga tokens persistentes.

## Código

- Controller: `sistema/app/Controllers/Auth.php`
- Models: `UserModel.php` e `RememberTokenModel.php`
- View: `sistema/app/Views/auth/login.php`
- Migration: `2026-08-21-202500_CreateAuthenticationTables.php`
- Seeder manual: `AdminSeeder.php`; requer `INITIAL_ADMIN_NAME`, `INITIAL_ADMIN_EMAIL` e `INITIAL_ADMIN_PASSWORD`.

## Segurança

Senhas exigem no mínimo 6 caracteres e usam `password_hash()`/`password_verify()`. O cookie persistente contém seletor e validador aleatórios; somente o SHA-256 do validador fica no banco. Login limitado a cinco tentativas por IP/minuto; a chave de cache usa o SHA-256 do IP para aceitar IPv4 e IPv6 sem expor o endereço. POST protegido por CSRF. Sessão regenerada após autenticação.

## Banco

- `users`: credenciais, estado e último acesso.
- `auth_remember_tokens`: tokens persistentes expirados em 30 dias.
- SQL completo: `bd/master.sql`
- SQL incremental: `bd/001-autenticacao.sql`
