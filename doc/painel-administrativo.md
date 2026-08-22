# Painel administrativo

## Objetivo

Área autenticada que centraliza o acesso aos módulos de marketing da Dias Imports. Nesta etapa, o painel fornece navegação e estados iniciais; não há métricas fictícias, consultas ou operações CRUD.

## Rotas protegidas

Todas as rotas abaixo usam o filtro `auth` no servidor:

| Método | Rota | Módulo |
| --- | --- | --- |
| `GET` | `/` | Visão Geral |
| `GET` | `/grupos-whatsapp` | Grupos de WhatsApp |
| `GET` | `/produtos` | Produtos |
| `GET` | `/leads-vip` | Leads VIP |
| `GET` | `/usuarios` | Usuários |
| `GET` | `/configuracoes` | Configurações |

Acesso sem sessão autenticada redireciona para `/login`. O logout permanece exclusivamente em `POST /logout`, protegido pelos filtros `auth` e `csrf`.

## Autorização

O banco atual não possui função, perfil ou permissão administrativa. Portanto, todos os usuários autenticados acessam as seis rotas. Controle RBAC não está implementado e não deve ser presumido.

## Interface

- Desktop: sidebar fixa, topbar e área central de conteúdo.
- A sidebar desktop pode ser compactada para `76px`; mantém os ícones, exibe tooltips e persiste a preferência no `localStorage` do navegador.
- Smartphones e tablets: cabeçalho compacto, barra inferior com Visão Geral, Grupos, Produtos, Leads e botão Mais.
- O botão Mais abre uma folha inferior acessível com Usuários, Configurações e logout.
- Tema escuro alinhado à tela de login.
- Quando a largura não é fluida, a área externa usa um tom mais claro da mesma paleta para distinguir os limites do sistema.
- Sidebar e barras flutuantes usam a largura útil do viewport como referência e permanecem dentro da largura máxima configurada, inclusive quando há scrollbar vertical.
- O footer permanece fixo no limite inferior do painel; no desktop respeita sidebar e largura máxima, e no mobile fica acima da navegação inferior.
- O conteúdo reserva a altura do footer, e as barras Salvar/Cancelar aparecem acima dele sem sobreposição.
- Todas as janelas usam o título `Título | JH7 Marketing`.
- Todas as páginas HTML usam o favicon vetorial `sistema/public/favicon.svg`, identificado pelas iniciais `DI`.
- Estados ativos usam `aria-current="page"`.
- Layout considera safe areas, `100dvh`, navegação por teclado, foco visível, contraste e preferência por movimento reduzido.

## Feedback global de processamento, confirmações e erros

- Todo formulário `POST` do painel abre uma camada fullscreen que bloqueia novas interações somente depois que a validação nativa do formulário é aprovada.
- A camada informa a operação em andamento, exibe ícone, órbitas, progresso e elementos de fundo animados, além de sortear uma mensagem leve a cada envio.
- As mensagens mudam conforme a ação: salvar layout, empresa ou WhatsApp; testar a Evolution API; criar, conectar, definir, desconectar ou excluir instância; e encerrar sessão.
- Ações críticas continuam exigindo confirmação; a camada de processamento começa apenas após a confirmação final.
- O fluxo `POST`/redirect encerra a camada naturalmente quando a resposta carrega e libera a interface.
- Mensagens de sucesso (`success` em flashdata) não são exibidas inline na página: abrem um modal global amigável com ícone divertido e animado, mensagem e botão de confirmação para fechar.
- Falhas armazenadas em flashdata `error` são escapadas no servidor e apresentadas em modal global acessível, fechado por botão, backdrop ou tecla Escape.
- A tela de login usa o mesmo padrão: bloqueio durante validação das credenciais e modal para erros de autenticação.
- No mobile, os modais de sucesso e erro assumem comportamento de folha inferior, respeitando safe areas e alvos de toque.
- Todas as animações respeitam `prefers-reduced-motion`.

## Código

- Filtro: `sistema/app/Filters/AuthFilter.php`
- Alias do filtro: `sistema/app/Config/Filters.php`
- Rotas: `sistema/app/Config/Routes.php`
- Controller: `sistema/app/Controllers/Home.php`
- Layout: `sistema/app/Views/layouts/admin.php`
- Conteúdo: `sistema/app/Views/admin/page.php`
- Estilos: `sistema/public/css/admin.css`
- Interação móvel: `sistema/public/js/admin.js`

## Configuração de layout

A aba `Layout` em `/configuracoes` controla a largura máxima do sistema completo, incluindo sidebar, topbar e área de conteúdo.

- Presets disponíveis: Compacto (`1000px`), Padrão (`1200px`), Largo (`1400px`) e Fluido (`100%`).
- O ajuste personalizado aceita valores entre `900px` e `1800px`, em passos de `10px`.
- Presets e slider aplicam uma prévia imediata do sistema completo somente na página atual.
- A configuração global muda apenas após `POST /configuracoes/layout` com autenticação e CSRF.
- A barra flutuante Salvar/Cancelar aparece somente quando há alteração pendente.
- A barra respeita horizontalmente os limites da largura atual do sistema, inclusive durante a prévia e com a sidebar recolhida.
- A barra usa a maior camada visual do painel e permanece acima dos demais componentes; no mobile, respeita as safe areas e fica acima da navegação inferior.
- O backend valida por allowlist o valor `fluid` ou um inteiro entre `900` e `1800`.

## Dados

O painel usa nome e e-mail existentes na sessão autenticada para identificar o usuário.

A tabela `app_settings` armazena configurações globais por chave única. A chave `layout_max_width` mantém a largura salva; o padrão inicial é `1200`.

- Model: `sistema/app/Models/AppSettingModel.php`
- Migration: `2026-08-21-220000_CreateAppSettingsTable.php`
- SQL incremental: `bd/002-configuracao-layout.sql`
- SQL completo atualizado: `bd/master.sql`

## Configuração da empresa

A aba `Empresa` em `/configuracoes?tab=empresa` mantém o cadastro institucional e os números de atendimento sem dados fictícios iniciais.

- Dados: nome, endereço, número, bairro, cidade, UF e endereço público HTTPS.
- A UF é validada por allowlist no backend; a URL exige HTTPS e rejeita endereços locais.
- O formulário exibe a barra flutuante Salvar/Cancelar somente após alterações.
- Cada WhatsApp possui identificação, máscara brasileira com DDD durante digitação/edição, telefone normalizado, status ativo/inativo e indicação de padrão.
- O primeiro WhatsApp é definido como padrão automaticamente.
- Somente um WhatsApp ativo pode ser padrão; a troca ocorre dentro de transação.
- O número padrão não pode ser inativado ou excluído antes da escolha de outro padrão.
- Tornar padrão, ativar, inativar e excluir usam um modal acessível, amigável e animado, com mensagem específica antes do envio.
- As ações continuam restritas a `POST`, com autenticação, CSRF e nova validação do registro no backend.
- Todas as operações são autenticadas, validadas no servidor e protegidas por CSRF.

| Método | Rota | Operação |
| --- | --- | --- |
| `POST` | `/configuracoes/empresa` | Criar ou atualizar o perfil |
| `POST` | `/configuracoes/empresa/whatsapp` | Criar ou editar WhatsApp |
| `POST` | `/configuracoes/empresa/whatsapp/{id}/padrao` | Definir padrão |
| `POST` | `/configuracoes/empresa/whatsapp/{id}/status` | Ativar ou inativar |
| `POST` | `/configuracoes/empresa/whatsapp/{id}/excluir` | Excluir |

As tabelas `company_profile` e `company_whatsapps` armazenam os dados. O telefone possui índice único; a exclusividade do padrão é garantida pela operação transacional do backend.

- Models: `sistema/app/Models/CompanyProfileModel.php` e `sistema/app/Models/CompanyWhatsappModel.php`
- Migration: `2026-08-21-230000_CreateCompanySettingsTables.php`
- SQL incremental: `bd/003-configuracao-empresa.sql`
- SQL completo atualizado: `bd/master.sql`

## Integração Evolution API

A aba `Evolution API` em `/configuracoes?tab=evolution` configura o servidor e administra instâncias sem expor a Global API Key ao navegador.

### Arquitetura e segurança

- Fluxo: navegador autenticado → backend CodeIgniter → Evolution API 2.3.x.
- O backend envia a chave no header `apikey`; o frontend nunca recebe o segredo persistido.
- A chave é criptografada em repouso pelo serviço de criptografia do CodeIgniter e exige `encryption.key` no ambiente.
- A URL aceita somente origem HTTPS pública, sem caminho, `/api`, parâmetros ou credenciais embutidas.
- A validação resolve o domínio e bloqueia IPs privados/reservados para reduzir SSRF.
- Chamadas externas validam TLS, desabilitam redirects, aplicam timeout de conexão de 3 segundos, timeout total de 10 segundos e limite de resposta de 1 MiB.
- A validação TLS usa primeiro o bundle CA configurado no PHP e, quando disponível, o bundle `extras/ssl/cacert.pem` da própria instalação; nunca desabilita a verificação.
- Falhas de DNS, certificado, timeout e conexão são classificadas sem registrar URL, chave ou conteúdo da resposta.
- Respostas externas e erros são normalizados; a chave e corpos internos não entram em mensagens do navegador.
- Logout e exclusão exigem confirmação em modal e não possuem retry automático.
- Todas as operações internas são `POST`, autenticadas e protegidas por CSRF.
- O sistema ainda não possui RBAC; portanto, qualquer usuário autenticado pode administrar a integração. Isso permanece como risco conhecido.

### Configuração

- `base_url`: origem pública da instalação Evolution.
- `api_key_encrypted`: Global API Key criptografada; nunca é reexibida.
- `min_delay_seconds` e `max_delay_seconds`: intervalo entre 1 e 3600 segundos, com máximo maior ou igual ao mínimo.
- `default_instance_name`: nome da instância padrão selecionada.
- `last_test_status` e `last_tested_at`: estado e data do último teste.
- A barra flutuante Salvar/Cancelar aparece somente quando a configuração é alterada.
- A tela permite testar a conexão, criar instância, solicitar QR Code, definir padrão, testar envio, desconectar e excluir.
- O botão Testar envio aparece somente em instâncias conectadas e abre um modal para informar o WhatsApp destinatário.
- O backend confirma novamente que a instância existe e está conectada, normaliza o telefone brasileiro para `55 + DDD + número` e envia uma mensagem fixa, sem aceitar texto arbitrário do navegador.
- O envio usa `POST /message/sendText/{instanceName}` e não possui retry automático, evitando mensagens duplicadas quando a resposta externa for inconclusiva.
- Na primeira entrada na aba Evolution API, o modal global de processamento bloqueia a interface enquanto o navegador solicita a página com `tab=evolution`; o backend busca as instâncias antes de renderizar e revelar os dados da integração.
- A primeira instância criada é definida automaticamente como padrão; criações posteriores preservam o padrão atual.
- Instâncias não padrão continuam oferecendo a ação manual Tornar padrão.
- A integração foi ajustada para Evolution API 2.3.7.
- O botão Conectar abre um modal acessível com o QR Code, substituído automaticamente por um código novo a cada 20 segundos enquanto estiver aberto.
- Somente ao abrir o modal, o backend força o logout por `DELETE /instance/logout/{instanceName}` e depois solicita o primeiro QR Code.
- As atualizações automáticas e tentativas manuais solicitam um novo QR Code sem repetir o logout.
- Cada renovação invalida o QR anterior; somente o código mais recente exibido deve ser lido.
- A cada renovação, o backend confirma primeiro o estado atual; ao detectar a conexão, a tela é recarregada e a ação muda de Conectar para Desconectar.
- Os cards sincronizam o estado real de todas as instâncias a cada 5 segundos, sem recarregar a página, atualizando o status e alternando imediatamente entre Conectar e Desconectar.
- O polling usa somente o backend autenticado, não armazena cache e é suspenso quando a página fica oculta para evitar requisições desnecessárias.
- O nome da instância aceita de 3 a 80 caracteres, exclusivamente letras ASCII, números, `_` e `-`; a interface remove outros caracteres durante digitação/colagem e o backend revalida antes da chamada externa.

### Rotas internas

| Método | Rota | Operação |
| --- | --- | --- |
| `POST` | `/configuracoes/evolution` | Salvar URL, chave e esperas |
| `POST` | `/configuracoes/evolution/testar` | Testar credenciais/listagem |
| `GET` | `/configuracoes/evolution/instancias/status` | Sincronizar estados atuais das instâncias |
| `POST` | `/configuracoes/evolution/instancias` | Criar instância |
| `POST` | `/configuracoes/evolution/instancias/conectar` | Solicitar conexão/QR Code |
| `POST` | `/configuracoes/evolution/instancias/padrao` | Definir padrão |
| `POST` | `/configuracoes/evolution/instancias/testar-envio` | Enviar mensagem fixa de teste |
| `POST` | `/configuracoes/evolution/instancias/desconectar` | Encerrar sessão WhatsApp |
| `POST` | `/configuracoes/evolution/instancias/excluir` | Excluir instância |

### Endpoints Evolution utilizados

- `GET /instance/fetchInstances`
- `POST /instance/create`
- `GET /instance/connect/{instanceName}`
- `DELETE /instance/logout/{instanceName}`
- `DELETE /instance/delete/{instanceName}`
- `POST /message/sendText/{instanceName}`

Os contratos devem ser validados contra o Swagger da versão 2.3.7 implantada antes do teste real, pois patches podem alterar payloads e respostas.

### Código e banco

- Controller: `sistema/app/Controllers/Evolution.php`
- Serviço: `sistema/app/Libraries/EvolutionApiService.php`
- Model: `sistema/app/Models/EvolutionSettingModel.php`
- Migration: `2026-08-21-235000_CreateEvolutionSettingsTable.php`
- Tabela: `evolution_settings`
- SQL incremental: `bd/004-evolution-api.sql`
- SQL completo: `bd/master.sql`

## Modelos de Mensagens

A aba `Modelos de Mensagens` em `/configuracoes?tab=modelos-mensagens` permite criar, editar, inativar e excluir modelos de mensagens dinâmicos com tags substituíveis no disparo.

### Rotas internas

| Método | Rota | Operação |
| --- | --- | --- |
| `POST` | `/configuracoes/modelos-mensagens` | Criar ou editar modelo de mensagem |
| `POST` | `/configuracoes/modelos-mensagens/{id}/status` | Ativar ou inativar modelo |
| `POST` | `/configuracoes/modelos-mensagens/{id}/excluir` | Excluir modelo |

### Tags Suportadas
- `{{nome}}` - Nome do produto
- `{{descricao}}` - Descrição ou benefícios
- `{{preco}}` - Preço original / tabela
- `{{preco_promocional}}` - Preço de oferta
- `{{desconto}}` - Economia ou percentual
- `{{link}}` - Link de redirecionamento / compra

### Código e banco
- Model: `sistema/app/Models/MessageTemplateModel.php`
- Tabela: `message_templates`
- SQL incremental: `bd/005-modelos-mensagens.sql`
- SQL completo: `bd/master.sql`

## Landing Page de Leads

A aba `Landing Page de Leads` em `/configuracoes?tab=landing-leads` permite configurar os textos, promessas, benefícios, link de redirecionamento para o Grupo VIP no WhatsApp, modelo de layout visual e paleta de cores, com visualização prévia em tempo real simulando um dispositivo mobile.

### Modelos Visuais (6 Opções)
1. **Hero Direct & Glass** (`model-1`): Formulário no topo com efeito glassmorphism e foco total na conversão direta.
2. **Benefits First** (`model-2`): Benefícios e prova de valor apresentados antes do formulário para gerar desejo prévio.
3. **Minimal Compact** (`model-3`): Pílulas arredondadas, direto ao ponto e otimizado para navegação com 1 polegar.
4. **Bento Grid** (`model-4`): Grade moderna em blocos assimétricos estilo Bento Box.
5. **Cyber Tech Neon** (`model-5`): Bordas tracejadas neon pulsantes, visual escuro e tipografia futurista (`Space Grotesk`).
6. **Editorial Luxury** (`model-6`): Tipografia refinada (`Outfit`), curvas orgânicas e gradientes suaves para produtos premium.

### Paletas de Cores (6 Opções)
1. **Aurora Neon** (`palette-aurora`): Violeta & Magenta elétrico com brilhos profundos.
2. **Emerald Tech** (`palette-emerald`): Verde Esmeralda & WhatsApp VIP de alta confiança.
3. **Amber Gold** (`palette-amber`): Ouro e âmbar quente luxuoso.
4. **Ocean Cyan** (`palette-ocean`): Azul Profundo & Ciano moderno.
5. **Crimson Ruby** (`palette-crimson`): Vermelho de alta urgência e impacto de oferta.
6. **Obsidian Minimal** (`palette-obsidian`): Preto Puro e Titânio monocromático.

### Animações de Fundo (6 Modelos de Background)
1. **Partículas & Orbes** (`bg-particles`): Orbes e luzes suaves flutuando harmonicamente com profundidade.
2. **Gradiente Líquido Mesh** (`bg-mesh-gradient`): Fluxo fluido de cores dinâmicas que se misturam em movimento suave contínuo.
3. **Grid Tech Futurista** (`bg-cyber-grid`): Grade tecnológica em perspectiva com linhas luminosas animadas.
4. **Pulso Radial Concêntrico** (`bg-radial-pulse`): Ondas circulares luminosas pulsando e expandindo a partir do centro.
5. **Geometrias Flutuantes** (`bg-floating-shapes`): Formas geométricas translúcidas flutuando com rotação e desfoque suave.
6. **Estático Minimalista** (`bg-minimal-static`): Fundo limpo sem animações contínuas, garantindo foco total no formulário.

### Animações do Botão CTA (6 Opções)
*(Localizado no grupo **Chamada para Ação (CTA) e Grupo VIP** do painel)*
1. **Pulso Rítmico** (`btn-pulse`): Respiração suave periódica com expansão de escala e halo luminoso.
2. **Feixe de Luz / Shimmer** (`btn-shimmer`): Reflexo metálico brilhante atravessando a extensão do botão.
3. **Microvibração / Shake** (`btn-shake`): Vibração atrativa inteligente a cada 4 segundos para despertar a atenção do lead.
4. **Salto Suave / Bounce** (`btn-bounce`): Pulo vertical rítmico direcionando o foco do olhar para o clique.
5. **Onda Expansiva / Ripple** (`btn-glow-expand`): Halo circular de energia que se irradia continuamente para fora do botão.
6. **Estático** (`btn-none`): Botão sem animação contínua automática, com transição suave apenas no hover e active.

### Rotas e Operações

| Método | Rota | Operação |
| --- | --- | --- |
| `GET` | `/leads` | Página pública de captura de alta conversão (100% mobile-first) |
| `POST` | `/leads/capture` | Captura AJAX do nome/WhatsApp do lead e retorno dos dados do modal VIP |
| `POST` | `/configuracoes/landing-leads` | Salvar configurações, modelos, paletas e animações da landing page |

### Funcionalidades
- **SEO & Compartilhamento Social**: Configuração de Título (`seo_title`), Descrição (`seo_description`) e Imagem personalizada (`seo_image`) para o Google e redes sociais (Open Graph / WhatsApp / Facebook / Instagram).
  - Inclui ferramenta interativa de **Upload e Recorte (Crop 1:1 quadrado)** com Cropper.js no painel administrativo.
  - Exibe o tamanho ideal recomendado (**600x600 px**).
  - Permite restaurar/remover a imagem a qualquer momento, voltando à imagem padrão oficial "DI".
  - Prévia em tempo real do card no WhatsApp.
- **Modelos de Layout**: 6 opções de layouts estruturais e responsivos.
- **Paletas de Cores**: 6 temas cromáticos completos.
- **Animações de Fundo (Background FX)**: Efeitos visuais dinâmicos em CSS puro.
- **Animações do Botão Principal**: Efeitos de pulso, brilho (shimmer), tremor (shake), bounce e expansão de glow.
- **Textos e Conteúdo**: Headline, subheadline, badge, botões, 3 benefícios com ícones interativos e modal pós-cadastro.
- **Link do Grupo VIP**: URL direta de convite do WhatsApp.
- **Live Mobile Mockup**: Simulador de smartphone em tempo real no painel administrativo.

### Rotas internas

| Método | Rota | Operação |
| --- | --- | --- |
| `POST` | `/configuracoes/landing-page` | Salvar configurações completas da Landing Page (modelos, paletas, animações, SEO, textos e links) |

### Código e Banco
- Controllers: `sistema/app/Controllers/Landing.php`, `sistema/app/Controllers/Home.php`
- Models: `sistema/app/Models/LandingLeadSettingModel.php`, `sistema/app/Models/LeadModel.php`
- Views: `sistema/app/Views/landing/leads.php`, `sistema/app/Views/admin/settings.php`
- Tabelas: `landing_lead_settings`, `leads`
- Migrations: `2026-08-22-020000_AddTemplateAndPaletteToLandingLeadSettings.php`, `2026-08-22-030000_AddBgAnimationAndBtnAnimationToLandingLeadSettings.php`
- SQL incremental: `bd/006-landing-page-leads.sql`, `bd/008-landing-modelos-paletas.sql`, `bd/009-landing-animacoes-bg-btn.sql`, `bd/010-landing-seo.sql`
- SQL completo: `bd/master.sql`

## Meta Ads (Pixel & Conversions API)

A aba `Meta Ads` em `/configuracoes?tab=meta-ads` permite integrar e gerenciar o Pixel do Meta (Facebook/Instagram Ads) e o Token de Acesso da API de Conversões (CAPI) com suporte a Test Event Code.

### Funcionalidades
- **Pixel ID**: Identificador numérico do Meta Pixel para rastreamento no navegador.
- **Token de Acesso (API de Conversões)**: Token permanente de sistema do Meta Graph API (v19.0+), armazenado de forma criptografada no banco de dados.
- **Test Event Code**: Código opcional de teste (`TESTxxxxx`) gerado no Gerenciador de Eventos da Meta para visualização imediata dos eventos de teste em tempo real.
- **Botão Testar Conexão / Evento**: Envia um evento de teste `TestConnection` direto pelo backend para validar a autenticidade do token e do Pixel ID na Meta Graph API.
- **Tracking na Landing Page de Leads**:
  - `PageView`: Disparado automaticamente no navegador ao carregar a página `/leads` se o Pixel estiver configurado.
  - `Lead`: Disparado simultaneamente no navegador (`fbq('track', 'Lead')`) e pelo servidor via Conversions API (`MetaAdsService::sendLead`) com hash SHA-256 de dados de usuário (telefone, nome, IP e User-Agent) ao submeter o formulário de cadastro com sucesso.

### Rotas internas

| Método | Rota | Operação |
| --- | --- | --- |
| `POST` | `/configuracoes/meta-ads` | Salvar ID do Pixel, Token da API e Código de Teste |
| `POST` | `/configuracoes/meta-ads/testar` | Testar credenciais e enviar evento de teste para o Gerenciador de Eventos Meta |

### Código e Banco
- Controllers: `sistema/app/Controllers/MetaAds.php`, `sistema/app/Controllers/Landing.php`, `sistema/app/Controllers/Home.php`
- Serviço: `sistema/app/Libraries/MetaAdsService.php`
- Model: `sistema/app/Models/MetaAdsSettingModel.php`
- Tabela: `meta_ads_settings`
- Migration: `2026-08-22-010000_CreateMetaAdsSettingsTable.php`
- SQL incremental: `bd/007-meta-ads.sql`
- SQL completo: `bd/master.sql`
