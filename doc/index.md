# Índice técnico

- [Autenticação](autenticacao.md): login, sessão, persistência por 30 dias, tabelas e usuário inicial.
- [Usuários e Permissões](usuarios.md): gerenciamento de usuários, papéis (admin/user), matriz granular de permissões e controle de acesso.
- [Painel administrativo](painel-administrativo.md): módulos, rotas protegidas, autorização, interface responsiva e configurações da Empresa/Evolution API.
- [Grupos de WhatsApp](grupos-whatsapp.md): sincronização, criação, testes de envio e gestão de grupos via Evolution API.
- [Central de Trabalho](central-de-trabalho.md): fila de processamento assíncrono, motor CLI/Cron Job e atualização de grupos com intervalo randomizado.


## Convenções de URL

- URLs públicas são amigáveis e não incluem `index.php`.
- Rotas usam segmentos legíveis em minúsculas, separados por hífen quando necessário.
- O servidor web deve apontar o document root para `sistema/public` e permitir as regras de rewrite do `.htaccess`.
