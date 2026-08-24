<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$presets = [
    '1200' => ['label' => 'Padrão', 'value' => '1200px', 'class' => 'standard'],
    '1400' => ['label' => 'Largo', 'value' => '1400px', 'class' => 'wide'],
    'fluid' => ['label' => 'Fluido', 'value' => '100%', 'class' => 'fluid'],
];
$isFluid = $layoutMaxWidth === 'fluid';
$sliderValue = $isFluid ? 1800 : (int) $layoutMaxWidth;
?>
<section class="settings-panel" data-layout-settings data-settings-root data-saved-width="<?= esc($layoutMaxWidth) ?>" data-active-tab="<?= esc($activeSettingsTab) ?>" data-instance-status-url="<?= site_url('configuracoes/evolution/instancias/status') ?>"
    data-realtime-feed-url="<?= site_url('configuracoes/tempo-real/feed') ?>"
    data-company-feed-url="<?= site_url('configuracoes/empresa/feed') ?>"
    data-templates-feed-url="<?= site_url('configuracoes/modelos-mensagens/feed') ?>"
    data-rt-company-active="<?= !empty($realtimeScreenSettings['settings_company']['active']) ? '1' : '0' ?>"
    data-rt-company-interval="<?= esc((string) ($realtimeScreenSettings['settings_company']['interval'] ?? 5)) ?>"
    data-rt-evolution-active="<?= !empty($realtimeScreenSettings['settings_evolution']['active']) ? '1' : '0' ?>"
    data-rt-evolution-interval="<?= esc((string) ($realtimeScreenSettings['settings_evolution']['interval'] ?? 5)) ?>"
    data-rt-templates-active="<?= !empty($realtimeScreenSettings['settings_templates']['active']) ? '1' : '0' ?>"
    data-rt-templates-interval="<?= esc((string) ($realtimeScreenSettings['settings_templates']['interval'] ?? 5)) ?>"
    data-rt-realtime-active="1"
    data-rt-realtime-interval="<?= esc((string) ($realtimeSleepSeconds ?? 5)) ?>">
    <div class="settings-layout">
        <aside class="settings-sidebar">
            <nav class="settings-tabs" role="tablist" aria-label="Categorias de configurações">
                <?php if (\App\Libraries\UserPermissions::hasPermission('layout', 'view')): ?>
                    <button class="settings-tab <?= $activeSettingsTab === 'layout' ? 'active' : '' ?>" type="button" role="tab" id="layout-tab" aria-selected="<?= $activeSettingsTab === 'layout' ? 'true' : 'false' ?>" aria-controls="layout-panel" data-settings-tab="layout">
                        <i class="ti ti-layout-dashboard" aria-hidden="true"></i>
                        <span>Layout</span>
                    </button>
                <?php endif; ?>
                <?php if (\App\Libraries\UserPermissions::hasPermission('company', 'view')): ?>
                    <button class="settings-tab <?= $activeSettingsTab === 'empresa' ? 'active' : '' ?>" type="button" role="tab" id="company-tab" aria-selected="<?= $activeSettingsTab === 'empresa' ? 'true' : 'false' ?>" aria-controls="company-panel" data-settings-tab="empresa">
                        <i class="ti ti-building-store" aria-hidden="true"></i>
                        <span>Empresa</span>
                    </button>
                <?php endif; ?>
                <?php if (\App\Libraries\UserPermissions::hasPermission('evolution', 'view')): ?>
                    <button class="settings-tab <?= $activeSettingsTab === 'evolution' ? 'active' : '' ?>" type="button" role="tab" id="evolution-tab" aria-selected="<?= $activeSettingsTab === 'evolution' ? 'true' : 'false' ?>" aria-controls="evolution-panel" data-settings-tab="evolution">
                        <i class="ti ti-brand-whatsapp" aria-hidden="true"></i>
                        <span>Evolution API</span>
                    </button>
                <?php endif; ?>
                <?php if (\App\Libraries\UserPermissions::hasPermission('meta_ads', 'view')): ?>
                    <button class="settings-tab <?= $activeSettingsTab === 'meta-ads' ? 'active' : '' ?>" type="button" role="tab" id="meta-ads-tab" aria-selected="<?= $activeSettingsTab === 'meta-ads' ? 'true' : 'false' ?>" aria-controls="meta-ads-panel" data-settings-tab="meta-ads">
                        <i class="ti ti-brand-meta" aria-hidden="true"></i>
                        <span>Meta Ads</span>
                    </button>
                <?php endif; ?>
                <?php if (\App\Libraries\UserPermissions::hasPermission('message_templates', 'view')): ?>
                    <button class="settings-tab <?= $activeSettingsTab === 'modelos-mensagens' ? 'active' : '' ?>" type="button" role="tab" id="templates-tab" aria-selected="<?= $activeSettingsTab === 'modelos-mensagens' ? 'true' : 'false' ?>" aria-controls="templates-panel" data-settings-tab="modelos-mensagens">
                        <i class="ti ti-message-2" aria-hidden="true"></i>
                        <span>Modelos de Mensagens</span>
                    </button>
                <?php endif; ?>
                <?php if (\App\Libraries\UserPermissions::hasPermission('central_trabalho', 'view')): ?>
                    <button class="settings-tab <?= $activeSettingsTab === 'central-trabalho' ? 'active' : '' ?>" type="button" role="tab" id="central-trabalho-tab" aria-selected="<?= $activeSettingsTab === 'central-trabalho' ? 'true' : 'false' ?>" aria-controls="central-trabalho-panel" data-settings-tab="central-trabalho">
                        <i class="ti ti-briefcase" aria-hidden="true"></i>
                        <span>Central de Trabalho</span>
                    </button>
                <?php endif; ?>
                <?php if (\App\Libraries\UserPermissions::hasPermission('realtime', 'view')): ?>
                    <button class="settings-tab <?= $activeSettingsTab === 'tempo-real' ? 'active' : '' ?>" type="button" role="tab" id="realtime-tab" aria-selected="<?= $activeSettingsTab === 'tempo-real' ? 'true' : 'false' ?>" aria-controls="realtime-panel" data-settings-tab="tempo-real">
                        <i class="ti ti-refresh" aria-hidden="true"></i>
                        <span>Atualização em Tempo Real</span>
                    </button>
                <?php endif; ?>
            </nav>
        </aside>

        <div class="settings-content">
            <?php if (\App\Libraries\UserPermissions::hasPermission('layout', 'view')): ?>
    <form action="<?= site_url('configuracoes/layout') ?>" method="post" data-layout-form>
        <?= csrf_field() ?>
        <input type="hidden" name="layout_max_width" value="<?= esc($layoutMaxWidth) ?>" data-width-input>

        <div class="settings-tab-panel" id="layout-panel" role="tabpanel" aria-labelledby="layout-tab" data-settings-panel="layout" <?= $activeSettingsTab !== 'layout' ? 'hidden' : '' ?>>
            <div class="setting-intro">
                <h2>Largura Máxima do Sistema</h2>
                <p>Defina a largura máxima que o conteúdo do sistema deve ocupar na tela.</p>
            </div>

            <fieldset class="width-presets">
                <legend class="sr-only">Presets de largura máxima</legend>
                <?php foreach ($presets as $value => $preset): ?>
                    <button class="width-preset <?= $layoutMaxWidth === $value ? 'selected' : '' ?>" type="button" data-width-preset="<?= esc($value) ?>" aria-pressed="<?= $layoutMaxWidth === $value ? 'true' : 'false' ?>">
                        <span class="width-preview <?= esc($preset['class']) ?>" aria-hidden="true"><span></span></span>
                        <strong><?= esc($preset['label']) ?></strong>
                        <small><?= esc($preset['value']) ?></small>
                    </button>
                <?php endforeach; ?>
            </fieldset>

            <div class="custom-width">
                <div class="custom-width-header">
                    <label for="layout-width-range">Ajuste Personalizado</label>
                    <output for="layout-width-range" data-width-output><?= $isFluid ? '100%' : esc($layoutMaxWidth) . 'px' ?></output>
                </div>
                <input id="layout-width-range" type="range" min="1200" max="1800" step="10" value="<?= esc((string) $sliderValue) ?>" data-width-range aria-describedby="width-range-hint">
                <span class="sr-only" id="width-range-hint">A largura pode variar entre 1200 e 1800 pixels.</span>
            </div>
        </div>

        <?php if (\App\Libraries\UserPermissions::hasPermission('layout', 'edit')): ?>
        <div class="save-bar" data-save-bar hidden>
            <p><strong>Alterações não salvas</strong><span>A prévia está aplicada apenas nesta tela.</span></p>
            <div class="save-actions">
                <button class="button secondary" type="button" data-cancel-layout>Cancelar</button>
                <button class="button primary" type="submit"><i class="ti ti-device-floppy" aria-hidden="true"></i>Salvar Alterações</button>
            </div>
        </div>
        <?php endif; ?>
    </form>
    <?php endif; ?>

    <?php if (\App\Libraries\UserPermissions::hasPermission('company', 'view')): ?>
    <div class="settings-tab-panel company-panel" id="company-panel" role="tabpanel" aria-labelledby="company-tab" data-settings-panel="empresa" <?= $activeSettingsTab !== 'empresa' ? 'hidden' : '' ?>>
        <form action="<?= site_url('configuracoes/empresa') ?>" method="post" data-dirty-form>
            <?= csrf_field() ?>
            <div class="setting-intro">
                <h2>Dados da Empresa</h2>
                <p>Endereço e identificação da empresa. Os números de atendimento ficam na relação de WhatsApps logo abaixo.</p>
            </div>

            <div class="company-fields">
                <label class="form-field field-company-name"><span>Nome da empresa</span><input type="text" name="name" maxlength="120" required autocomplete="organization" value="<?= esc(old('name', $companyProfile['name'] ?? '')) ?>"></label>
                <label class="form-field field-address"><span>Endereço</span><input type="text" name="address" maxlength="190" required autocomplete="address-line1" value="<?= esc(old('address', $companyProfile['address'] ?? '')) ?>"></label>
                <label class="form-field field-number"><span>Número</span><input type="text" name="number" maxlength="20" required autocomplete="address-line2" value="<?= esc(old('number', $companyProfile['number'] ?? '')) ?>"></label>
                <label class="form-field field-district"><span>Bairro</span><input type="text" name="district" maxlength="100" required value="<?= esc(old('district', $companyProfile['district'] ?? '')) ?>"></label>
                <label class="form-field field-city"><span>Cidade</span><input type="text" name="city" maxlength="100" required autocomplete="address-level2" value="<?= esc(old('city', $companyProfile['city'] ?? '')) ?>"></label>
                <label class="form-field field-state"><span>UF</span><select name="state" required autocomplete="address-level1"><option value="">Selecione</option><?php $selectedState = old('state', $companyProfile['state'] ?? ''); foreach ($brazilianStates as $state): ?><option value="<?= esc($state) ?>" <?= $selectedState === $state ? 'selected' : '' ?>><?= esc($state) ?></option><?php endforeach; ?></select></label>
                <label class="form-field field-public-url"><span>Endereço público do site</span><input type="url" name="public_url" maxlength="255" readonly inputmode="url" placeholder="https://diasimports.com.br" value="<?= esc(base_url()) ?>" style="background-color: var(--input-bg-disabled, rgba(255, 255, 255, 0.04)); cursor: default;"><small>Base dos links das landing pages e do catálogo da Meta (definido no <code>.env</code> em <code>app.baseURL</code>).</small></label>
            </div>

            <?php if (\App\Libraries\UserPermissions::hasPermission('company', 'edit')): ?>
            <div class="company-save-bar save-bar" data-form-save-bar hidden>
                <p><strong>Alterações não salvas</strong><span>Revise os dados antes de salvar.</span></p>
                <div class="save-actions"><button class="button secondary" type="reset">Cancelar</button><button class="button primary" type="submit"><i class="ti ti-device-floppy" aria-hidden="true"></i>Salvar Alterações</button></div>
            </div>
            <?php endif; ?>
        </form>

        <section class="whatsapp-section" aria-labelledby="whatsapp-title">
            <div class="section-title-row">
                <div><h2 id="whatsapp-title">WhatsApps da Empresa</h2><p>Cadastre os números de atendimento e escolha o padrão usado quando nenhum número específico for selecionado.</p></div>
                <?php if (\App\Libraries\UserPermissions::hasPermission('company', 'create')): ?>
                <button class="button outline" type="button" data-open-whatsapp><i class="ti ti-plus" aria-hidden="true"></i>Novo WhatsApp</button>
                <?php endif; ?>
            </div>

            <?php if (\App\Libraries\UserPermissions::hasPermission('company', 'create') || \App\Libraries\UserPermissions::hasPermission('company', 'edit')): ?>
            <form class="whatsapp-editor" action="<?= site_url('configuracoes/empresa/whatsapp') ?>" method="post" data-whatsapp-editor hidden>
                <?= csrf_field() ?><input type="hidden" name="whatsapp_id" data-whatsapp-id>
                <label class="form-field"><span>Identificação</span><input type="text" name="whatsapp_name" maxlength="80" required placeholder="Ex.: Loja 1" data-whatsapp-name></label>
                <label class="form-field"><span>WhatsApp</span><input type="tel" name="whatsapp_phone" maxlength="20" required inputmode="tel" autocomplete="tel" placeholder="(17) 98800-4745" data-whatsapp-phone></label>
                <div class="editor-actions"><button class="button secondary" type="button" data-close-whatsapp>Cancelar</button><button class="button primary" type="submit">Salvar WhatsApp</button></div>
            </form>
            <?php endif; ?>

            <div class="whatsapp-list-container" data-company-whatsapps-container>
                <?= view('admin/settings/_company_whatsapps', ['companyWhatsapps' => $companyWhatsapps]) ?>
            </div>
        </section>
    </div>
    <?php endif; ?>

    <?php if (\App\Libraries\UserPermissions::hasPermission('evolution', 'view')): ?>
    <div class="settings-tab-panel evolution-panel" id="evolution-panel" role="tabpanel" aria-labelledby="evolution-tab" data-settings-panel="evolution" <?= $activeSettingsTab !== 'evolution' ? 'hidden' : '' ?>>
        <form action="<?= site_url('configuracoes/evolution') ?>" method="post" data-evolution-form>
            <?= csrf_field() ?>
            <div class="setting-intro">
                <h2>Integração Evolution API</h2>
                <p>Configure as credenciais para conectar o sistema à sua instância da Evolution API. Por segurança, os valores salvos não são exibidos.</p>
            </div>

            <?php if (! $evolutionEncryptionReady): ?>
                <div class="settings-alert warning" role="alert"><i class="ti ti-lock-exclamation" aria-hidden="true"></i>Configure <strong>encryption.key</strong> no ambiente para salvar a Global API Key criptografada.</div>
            <?php elseif (($evolutionSettings['api_key_encrypted'] ?? '') !== ''): ?>
                <p class="credential-status"><i class="ti ti-shield-check" aria-hidden="true"></i>Credenciais configuradas e armazenadas criptografadas no servidor.</p>
            <?php endif; ?>

            <div class="evolution-credentials">
                <label class="form-field"><span>URL da API</span><input type="url" name="base_url" maxlength="255" required inputmode="url" autocomplete="url" placeholder="https://evolution.seudominio.com" value="<?= esc(old('base_url', $evolutionSettings['base_url'] ?? '')) ?>"><small>Informe somente a URL base HTTPS pública, sem /api ou caminhos adicionais.</small></label>
                <label class="form-field"><span>Global API Key</span><span class="secret-input"><input type="password" name="api_key" maxlength="1000" autocomplete="new-password" placeholder="<?= ($evolutionSettings['api_key_encrypted'] ?? '') !== '' ? '•••••••• (manter atual)' : 'Informe a chave' ?>" data-secret-input><button type="button" aria-label="Mostrar Global API Key" data-toggle-secret><i class="ti ti-eye" aria-hidden="true"></i></button></span><small>Deixe vazio para manter a chave atual. O valor nunca retorna ao navegador.</small></label>
            </div>

            <div class="evolution-test-row">
                <button class="button secondary" type="submit" formaction="<?= site_url('configuracoes/evolution/testar') ?>" formnovalidate <?= ! $evolutionEncryptionReady || ($evolutionSettings['api_key_encrypted'] ?? '') === '' ? 'disabled' : '' ?>><i class="ti ti-plug-connected" aria-hidden="true"></i>Testar Conexão</button>
                <?php if (($evolutionSettings['last_test_status'] ?? null) !== null): ?><small class="last-test <?= esc($evolutionSettings['last_test_status']) ?>"><i class="ti <?= $evolutionSettings['last_test_status'] === 'success' ? 'ti-circle-check' : 'ti-alert-circle' ?>" aria-hidden="true"></i>Último teste: <?= $evolutionSettings['last_test_status'] === 'success' ? 'concluído' : 'falhou' ?><?= ($evolutionSettings['last_tested_at'] ?? null) ? ' em ' . esc(date('d/m/Y H:i', strtotime($evolutionSettings['last_tested_at']))) : '' ?></small><?php endif; ?>
            </div>

            <?php if (\App\Libraries\UserPermissions::hasPermission('evolution', 'edit')): ?>
            <div class="save-bar" data-evolution-save-bar hidden>
                <p><strong>Alterações não salvas</strong><span>As credenciais serão armazenadas somente no servidor.</span></p>
                <div class="save-actions"><button class="button secondary" type="reset">Cancelar</button><button class="button primary" type="submit" <?= ! $evolutionEncryptionReady ? 'disabled' : '' ?>><i class="ti ti-device-floppy" aria-hidden="true"></i>Salvar Configuração</button></div>
            </div>
            <?php endif; ?>
        </form>

        <section class="evolution-instances" aria-labelledby="instances-title">
            <div class="section-title-row">
                <div><h2 id="instances-title">Instâncias Cadastradas</h2><p>Lista sincronizada diretamente com a Evolution API.</p></div>
                <?php if (\App\Libraries\UserPermissions::hasPermission('evolution', 'create')): ?>
                <button class="button outline" type="button" data-open-instance <?= ($evolutionSettings['api_key_encrypted'] ?? '') === '' ? 'disabled' : '' ?>><i class="ti ti-plus" aria-hidden="true"></i>Nova Instância</button>
                <?php endif; ?>
            </div>

            <?php if (\App\Libraries\UserPermissions::hasPermission('evolution', 'create')): ?>
            <form class="instance-editor" action="<?= site_url('configuracoes/evolution/instancias') ?>" method="post" data-instance-editor hidden>
                <?= csrf_field() ?>
                <label class="form-field"><span>Nome da instância</span><input type="text" name="instance_name" minlength="3" maxlength="80" required pattern="[A-Za-z0-9][A-Za-z0-9_-]{2,79}" autocomplete="off" autocapitalize="none" spellcheck="false" placeholder="Ex.: dias-imports-atendimento" data-instance-name><small>Somente letras sem acento, números, _ e -; espaços não são permitidos.</small></label>
                <div class="editor-actions"><button class="button secondary" type="button" data-close-instance>Cancelar</button><button class="button primary" type="submit">Criar Instância</button></div>
            </form>
            <?php endif; ?>

            <?php if ($evolutionLoadError): ?><div class="settings-alert error" role="alert"><i class="ti ti-cloud-off" aria-hidden="true"></i><?= esc($evolutionLoadError) ?></div><?php endif; ?>

            <div class="instance-list-container" data-evolution-instances-container>
                <?= view('admin/settings/_evolution_instances', ['evolutionInstances' => $evolutionInstances, 'evolutionSettings' => $evolutionSettings]) ?>
            </div>
        </section>
    </div>
    <?php endif; ?>

    <!-- Aba Meta Ads -->
    <?php if (\App\Libraries\UserPermissions::hasPermission('meta_ads', 'view')): ?>
    <div class="settings-tab-panel meta-ads-panel" id="meta-ads-panel" role="tabpanel" aria-labelledby="meta-ads-tab" data-settings-panel="meta-ads" <?= $activeSettingsTab !== 'meta-ads' ? 'hidden' : '' ?>>
        <form action="<?= site_url('configuracoes/meta-ads') ?>" method="post" data-meta-ads-form>
            <?= csrf_field() ?>
            <div class="setting-intro">
                <div class="setting-intro-header">
                    <div>
                        <span class="setting-intro-badge" style="background: rgba(24, 119, 242, 0.12); color: #1877f2; border-color: rgba(24, 119, 242, 0.25);"><i class="ti ti-brand-meta"></i> Meta Conversions API & Pixel</span>
                        <h2>Meta Ads (Pixel e API de Conversões)</h2>
                        <p>Configure o identificador do Pixel do Meta Ads e o Token de Acesso da API de Conversões (CAPI) para rastrear com alta precisão os acessos e cadastros de leads na Landing Page.</p>
                    </div>
                </div>
            </div>

            <?php if (! ($metaAdsEncryptionReady ?? false)): ?>
                <div class="settings-alert warning" role="alert"><i class="ti ti-lock-exclamation" aria-hidden="true"></i>Configure <strong>encryption.key</strong> no ambiente para salvar o Token de Acesso criptografado com segurança.</div>
            <?php elseif (($metaAdsSettings['access_token_encrypted'] ?? '') !== ''): ?>
                <p class="credential-status"><i class="ti ti-shield-check" aria-hidden="true"></i>Credenciais da Meta salvas com criptografia de ponta a ponta no servidor.</p>
            <?php endif; ?>

            <div class="meta-ads-layout">
                <!-- Card 1: Credenciais e Conexão Principal -->
                <div class="settings-card-block">
                    <div class="settings-card-header">
                        <div class="card-header-icon" style="background: rgba(24, 119, 242, 0.12); color: #1877f2; border-color: rgba(24, 119, 242, 0.25);"><i class="ti ti-brand-meta"></i></div>
                        <div>
                            <h3 class="settings-section-title">Credenciais de Rastreamento</h3>
                            <p class="settings-section-subtitle">Chaves de integração geradas no Gerenciador de Eventos da Meta.</p>
                        </div>
                    </div>

                    <div class="meta-ads-credentials">
                        <label class="form-field">
                            <span class="field-label-row">
                                <span>Pixel ID</span>
                                <small class="field-hint">Identificador numérico</small>
                            </span>
                            <input type="text" name="pixel_id" maxlength="50" required pattern="\d{10,25}" autocomplete="off" placeholder="Ex.: 123456789012345" value="<?= esc(old('pixel_id', $metaAdsSettings['pixel_id'] ?? '')) ?>">
                            <small>Gerenciador de Eventos da Meta &gt; Configurações do Conjunto de Dados.</small>
                        </label>

                        <label class="form-field">
                            <span class="field-label-row">
                                <span>Token da API do Pixel (Conversions API)</span>
                                <small class="field-hint">Token do Gerenciador de Eventos</small>
                            </span>
                            <span class="secret-input">
                                <input type="password" name="access_token" maxlength="1000" autocomplete="new-password" placeholder="<?= ($metaAdsSettings['access_token_encrypted'] ?? '') !== '' ? '•••••••• (manter atual)' : 'Cole o Token de Acesso gerado na Meta' ?>" data-secret-input>
                                <button type="button" aria-label="Mostrar Token da API" data-toggle-secret><i class="ti ti-eye" aria-hidden="true"></i></button>
                            </span>
                            <small>Gerenciador de Eventos &gt; Configurações &gt; API de Conversões &gt; "Gerar token de acesso". Deixe em branco para manter o atual.</small>
                        </label>

                        <label class="form-field">
                            <span class="field-label-row">
                                <span>Código do Evento de Teste (Opcional)</span>
                                <small class="field-hint">Apenas para validação no Gerenciador de Eventos</small>
                            </span>
                            <input type="text" name="test_event_code" maxlength="50" autocomplete="off" placeholder="Ex.: TEST12345" value="<?= esc(old('test_event_code', $metaAdsSettings['test_event_code'] ?? '')) ?>">
                            <small>Copie da aba "Testar Eventos" da Meta para ver os disparos em tempo real. Deixe vazio em produção.</small>
                        </label>
                    </div>

                    <div class="meta-test-row">
                        <button class="button secondary" type="submit" formaction="<?= site_url('configuracoes/meta-ads/testar') ?>" formnovalidate <?= ! ($metaAdsEncryptionReady ?? false) || ($metaAdsSettings['access_token_encrypted'] ?? '') === '' ? 'disabled' : '' ?>>
                            <i class="ti ti-plug-connected" aria-hidden="true"></i>Testar Conexão com a API
                        </button>
                        <?php if (($metaAdsSettings['last_test_status'] ?? null) !== null): ?>
                            <small class="last-test <?= esc($metaAdsSettings['last_test_status']) ?>">
                                <i class="ti <?= $metaAdsSettings['last_test_status'] === 'success' ? 'ti-circle-check' : 'ti-alert-circle' ?>" aria-hidden="true"></i>
                                Último teste: <?= $metaAdsSettings['last_test_status'] === 'success' ? 'conectado com sucesso' : 'falhou' ?>
                                <?= ($metaAdsSettings['last_tested_at'] ?? null) ? ' em ' . esc(date('d/m/Y H:i', strtotime($metaAdsSettings['last_tested_at']))) : '' ?>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Card 2: Como funciona e eventos automáticos -->
                <div class="settings-card-block">
                    <div class="settings-card-header">
                        <div class="card-header-icon" style="background: rgba(124, 105, 255, 0.12); color: rgb(var(--primary-hover)); border-color: rgba(124, 105, 255, 0.25);"><i class="ti ti-activity"></i></div>
                        <div>
                            <h3 class="settings-section-title">Eventos Rastreados Automaticamente</h3>
                            <p class="settings-section-subtitle">Rastreamento redundante via navegador (Pixel) e servidor (CAPI) para máxima mensuração.</p>
                        </div>
                    </div>

                    <div class="meta-events-grid">
                        <div class="meta-event-card">
                            <div class="meta-event-head">
                                <span class="event-badge">PageView</span>
                                <span class="meta-event-tag">Visualização de Página</span>
                            </div>
                            <p>Disparado instantaneamente no carregamento da Landing Page de Leads e nas Páginas de Produtos configuradas com Meta Ads ativo.</p>
                        </div>

                        <div class="meta-event-card">
                            <div class="meta-event-head">
                                <span class="event-badge success">Lead</span>
                                <span class="meta-event-tag">Cadastro Realizado</span>
                            </div>
                            <p>Disparado ao confirmar o envio do formulário de cadastro, enviando dados higienizados e <code>event_id</code> único para garantir 100% de atribuição de conversão.</p>
                        </div>

                        <div class="meta-event-card">
                            <div class="meta-event-head">
                                <span class="event-badge" style="background: rgba(13, 202, 240, 0.15); color: #0dcaf0; border-color: rgba(13, 202, 240, 0.3);">ViewContent</span>
                                <span class="meta-event-tag">Visualização de Produto</span>
                            </div>
                            <p>Disparado ao acessar a Landing Page de um produto com Meta Ads ativo, enviando nome do produto, ID, valor e moeda (BRL).</p>
                        </div>

                        <div class="meta-event-card">
                            <div class="meta-event-head">
                                <span class="event-badge success" style="background: rgba(32, 201, 151, 0.15); color: #20c997; border-color: rgba(32, 201, 151, 0.3);">Purchase</span>
                                <span class="meta-event-tag">Clique de Compra (WhatsApp)</span>
                            </div>
                            <p>Disparado quando o cliente clica nos botões de CTA/compra da página do produto (tanto o principal quanto a barra flutuante inferior), enviando nome, ID, valor e moeda.</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (\App\Libraries\UserPermissions::hasPermission('meta_ads', 'edit')): ?>
            <div class="save-bar" data-meta-ads-save-bar hidden>
                <p><strong>Alterações não salvas</strong><span>O Token de Acesso será armazenado de forma criptografada e segura.</span></p>
                <div class="save-actions">
                    <button class="button secondary" type="reset">Cancelar</button>
                    <button class="button primary" type="submit" <?= ! ($metaAdsEncryptionReady ?? false) ? 'disabled' : '' ?>><i class="ti ti-device-floppy" aria-hidden="true"></i>Salvar Configurações</button>
                </div>
            </div>
            <?php endif; ?>
        </form>
    </div>
    <?php endif; ?>

    <?php if (\App\Libraries\UserPermissions::hasPermission('message_templates', 'view')): ?>
    <div class="settings-tab-panel templates-panel" id="templates-panel" role="tabpanel" aria-labelledby="templates-tab" data-settings-panel="modelos-mensagens" <?= $activeSettingsTab !== 'modelos-mensagens' ? 'hidden' : '' ?>>
        <div class="section-title-row" style="flex-direction: column; align-items: stretch;">
            <div class="setting-intro">
                <h2>Modelos de Mensagens</h2>
                <p>Modelos reutilizáveis para divulgar produtos. As tags são trocadas pelos dados reais no momento do envio.</p>
            </div>
            <div class="section-title-actions">
                <div class="status-filter" data-template-filter>
                    <button type="button" class="filter-btn active" data-filter="all">Todos</button>
                    <button type="button" class="filter-btn" data-filter="active">Ativos</button>
                    <button type="button" class="filter-btn" data-filter="inactive">Inativos</button>
                </div>
                <?php if (\App\Libraries\UserPermissions::hasPermission('message_templates', 'create')): ?>
                <button class="button outline" type="button" data-open-template><i class="ti ti-plus" aria-hidden="true"></i>Novo Modelo</button>
                <?php endif; ?>
            </div>
        </div>

        <div class="templates-list-container" data-templates-list-container>
            <?= view('admin/settings/_message_templates', ['messageTemplates' => $messageTemplates]) ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (\App\Libraries\UserPermissions::hasPermission('central_trabalho', 'view')): ?>
    <div class="settings-tab-panel central-trabalho-panel" id="central-trabalho-panel" role="tabpanel" aria-labelledby="central-trabalho-tab" data-settings-panel="central-trabalho" <?= $activeSettingsTab !== 'central-trabalho' ? 'hidden' : '' ?>>
        <div class="setting-intro">
            <h2>Central de Trabalho</h2>
            <p>Configure os parâmetros de execução das tarefas em segundo plano e endpoints para agendadores automáticos (Webcron / Cronjob).</p>
        </div>

        <!-- Endpoints de Execução e Limpeza (Webcron / URLs de Automação) -->
        <div class="webcron-card">
            <div class="webcron-header">
                <div class="webcron-icon"><i class="ti ti-terminal-2"></i></div>
                <div class="webcron-title-group">
                    <h3>Comandos de Execução e Manutenção (Cron Job / Servidor)</h3>
                    <p>Copie e cole diretamente no agendador de tarefas / Cron Job da hospedagem (ex: cPanel, Hostinger) ou serviços webcron.</p>
                </div>
            </div>
            
            <div class="webcron-items-grid">
                <div class="webcron-item">
                    <div class="webcron-item-header">
                        <span class="webcron-item-label">Comando para Execução da Fila (cron-runner)</span>
                        <span class="webcron-badge runner"><i class="ti ti-clock"></i> A cada 5 minutos</span>
                    </div>
                    <?php 
                        $runnerUrl = base_url('cron-runner.php') . '?token=' . esc(env('app.cronToken') ?: 'dias_imports_cron_secret_2026') . '&limit=50';
                        $runnerCmd = 'wget -O /dev/null ' . $runnerUrl;
                    ?>
                    <div class="webcron-input-group">
                        <div class="webcron-url-text" id="setting-cron-runner-url" style="font-family: monospace; font-size: 12px;"><?= esc($runnerCmd) ?></div>
                        <button class="button secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('setting-cron-runner-url').textContent.trim()); alert('Comando cron-runner copiado!');">
                            <i class="ti ti-copy" aria-hidden="true"></i>
                            <span>Copiar</span>
                        </button>
                    </div>
                    <p class="webcron-hint">
                        <i class="ti ti-info-circle"></i>
                        Processa o lote de tarefas pendentes na fila. Cole este comando exato no campo "Comando para executar" do Cron da Hostinger <strong>a cada 5 minutos</strong>.
                    </p>
                </div>

                <div class="webcron-item">
                    <div class="webcron-item-header">
                        <span class="webcron-item-label">Comando para Limpeza de Fila Travada (cron-clean)</span>
                        <span class="webcron-badge clean"><i class="ti ti-clock"></i> A cada 10 minutos</span>
                    </div>
                    <?php 
                        $cleanUrl = base_url('cron-clean.php') . '?token=' . esc(env('app.cronToken') ?: 'dias_imports_cron_secret_2026');
                        $cleanCmd = 'wget -O /dev/null ' . $cleanUrl;
                    ?>
                    <div class="webcron-input-group">
                        <div class="webcron-url-text" id="setting-cron-clean-url" style="font-family: monospace; font-size: 12px;"><?= esc($cleanCmd) ?></div>
                        <button class="button secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('setting-cron-clean-url').textContent.trim()); alert('Comando cron-clean copiado!');">
                            <i class="ti ti-copy" aria-hidden="true"></i>
                            <span>Copiar</span>
                        </button>
                    </div>
                    <p class="webcron-hint">
                        <i class="ti ti-info-circle"></i>
                        Redefine automaticamente tarefas travadas e limpa locks. Cole este comando exato no Cron da Hostinger <strong>a cada 10 minutos</strong>.
                    </p>
                </div>
            </div>
        </div>

        <?php if (empty($systemJobs)): ?>
            <div class="empty-state">
                <i class="ti ti-cpu" aria-hidden="true"></i>
                <p>Nenhum trabalho registrado no sistema.</p>
            </div>
        <?php else: ?>
            <div class="jobs-list">
                <?php foreach ($systemJobs as $job): ?>
                    <form action="<?= site_url('configuracoes/central-trabalho') ?>" method="post" class="job-settings-card" data-dirty-form>
                        <?= csrf_field() ?>
                        <input type="hidden" name="job_key" value="<?= esc($job['job_key']) ?>">
                        
                        <div class="job-card-header">
                            <div class="job-card-title">
                                <h3><?= esc($job['name']) ?></h3>
                                <p><?= esc($job['description']) ?></p>
                                <div style="margin-top: 8px; display: flex; align-items: center; gap: 6px; font-size: 12px; color: rgb(var(--muted));">
                                    <i class="ti ti-info-circle"></i>
                                    <span>Define se a rotina em segundo plano pode ser executada ou chamada por outras partes do sistema.</span>
                                </div>
                            </div>
                            <div class="job-card-toggle">
                                <label class="toggle-switch" title="<?= $job['is_active'] ? 'Trabalho ativo' : 'Trabalho inativo' ?>">
                                    <input type="checkbox" name="is_active" value="1" <?= $job['is_active'] ? 'checked' : '' ?> <?= !\App\Libraries\UserPermissions::hasPermission('central_trabalho', 'edit') ? 'disabled' : '' ?>>
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label-text"><?= $job['is_active'] ? 'Ativo' : 'Inativo' ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="job-card-body">
                            <div class="form-grid">
                                <label class="form-field">
                                    <span>Tempo Mínimo de Espera (segundos)</span>
                                    <input type="number" name="min_delay_seconds" value="<?= esc($job['min_delay_seconds']) ?>" min="1" required <?= !\App\Libraries\UserPermissions::hasPermission('central_trabalho', 'edit') ? 'disabled' : '' ?>>
                                </label>
                                <label class="form-field">
                                    <span>Tempo Máximo de Espera (segundos)</span>
                                    <input type="number" name="max_delay_seconds" value="<?= esc($job['max_delay_seconds']) ?>" min="1" required <?= !\App\Libraries\UserPermissions::hasPermission('central_trabalho', 'edit') ? 'disabled' : '' ?>>
                                </label>
                            </div>
                        </div>

                        <?php if (\App\Libraries\UserPermissions::hasPermission('central_trabalho', 'edit')): ?>
                        <div class="save-bar" data-form-save-bar hidden>
                            <p><strong>Alterações não salvas</strong><span>Salve para aplicar as novas configurações de tempo.</span></p>
                            <div class="save-actions">
                                <button class="button secondary" type="button" data-cancel-form>Cancelar</button>
                                <button class="button primary" type="submit"><i class="ti ti-device-floppy" aria-hidden="true"></i>Salvar Alterações</button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </form>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (\App\Libraries\UserPermissions::hasPermission('realtime', 'view')): ?>
    <div class="settings-tab-panel realtime-panel" id="realtime-panel" role="tabpanel" aria-labelledby="realtime-tab" data-settings-panel="tempo-real" <?= $activeSettingsTab !== 'tempo-real' ? 'hidden' : '' ?>>
        <div class="setting-intro">
            <h2>Atualização em Tempo Real</h2>
            <p>Configure quais telas do sistema devem ser atualizadas automaticamente em tempo real e o intervalo em segundos para cada tela.</p>
        </div>

        <div data-realtime-dashboard-container>
            <?= view('admin/settings/_realtime_dashboard', [
                'realtimeWorkerStatus' => $realtimeWorkerStatus ?? [],
            ]) ?>
        </div>

        <?php if (empty($realtimeScreens)): ?>
            <div class="empty-state">
                <i class="ti ti-refresh" aria-hidden="true"></i>
                <p>Nenhuma tela configurada para atualização em tempo real.</p>
            </div>
        <?php else: ?>
            <form action="<?= site_url('configuracoes/tempo-real') ?>" method="post" data-dirty-form>
                <?= csrf_field() ?>

                <div class="webcron-card" style="margin-bottom: 16px; padding: 16px 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                        <div>
                            <h3 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: rgb(var(--foreground));">
                                <i class="ti ti-clock-pause" style="margin-right: 6px; color: #8b7cff;"></i>
                                Intervalo Global de Execução do Worker Realtime
                            </h3>
                            <p style="margin: 0; font-size: 12px; color: rgb(var(--muted));">
                                Tempo de pausa (sleep) entre cada ciclo de processamento do <code style="color: #8b7cff;">cron-realtime.php</code>. Todas as telas ativas sincronizam automaticamente neste mesmo intervalo.
                            </p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <label for="realtime_sleep_seconds" style="font-size: 13px; font-weight: 500; color: rgb(var(--foreground)); white-space: nowrap; margin: 0;">Pausa / Sleep (segundos):</label>
                            <input type="number" id="realtime_sleep_seconds" name="realtime_sleep_seconds" value="<?= esc((string) ($realtimeSleepSeconds ?? 5)) ?>" min="1" max="60" required style="width: 80px; padding: 6px 10px; font-size: 14px; font-weight: 600; text-align: center; border-radius: 6px; border: 1px solid var(--border-color, #334155); background: var(--bg-card, #1e293b); color: rgb(var(--foreground));" <?= !\App\Libraries\UserPermissions::hasPermission('realtime', 'edit') ? 'disabled' : '' ?>>
                        </div>
                    </div>
                </div>

                <div class="jobs-list" style="gap: 8px;">
                    <?php foreach ($realtimeScreens as $screen): ?>
                        <div class="job-settings-card" style="padding: 14px 20px; display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: space-between !important; margin: 0; width: 100%; gap: 16px; flex-wrap: wrap;">
                            <div class="job-card-title" style="margin: 0; flex: 1; min-width: 200px;">
                                <h3 style="margin: 0; font-size: 14px; font-weight: 500; text-align: left; color: rgb(var(--foreground));"><?= esc($screen['screen_name']) ?></h3>
                            </div>
                            <div style="display: flex; align-items: center;">
                                <div class="job-card-toggle" style="margin: 0; display: flex; align-items: center;">
                                    <label class="toggle-switch" title="<?= $screen['is_active'] ? 'Atualização ativa' : 'Atualização inativa' ?>" style="margin: 0; display: inline-flex; align-items: center; gap: 8px;">
                                        <input type="checkbox" name="screens[<?= $screen['id'] ?>][is_active]" value="1" <?= $screen['is_active'] ? 'checked' : '' ?> <?= !\App\Libraries\UserPermissions::hasPermission('realtime', 'edit') ? 'disabled' : '' ?>>
                                        <span class="toggle-slider"></span>
                                        <span class="toggle-label-text" style="font-size: 13px; color: rgb(var(--foreground));"><?= $screen['is_active'] ? 'Ativo' : 'Inativo' ?></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (\App\Libraries\UserPermissions::hasPermission('realtime', 'edit')): ?>
                <div class="save-bar" data-form-save-bar hidden>
                    <p><strong>Alterações não salvas</strong><span>Salve para aplicar as novas configurações de atualização em tempo real.</span></p>
                    <div class="save-actions">
                        <button class="button secondary" type="button" data-cancel-form>Cancelar</button>
                        <button class="button primary" type="submit"><i class="ti ti-device-floppy" aria-hidden="true"></i>Salvar Alterações</button>
                    </div>
                </div>
                <?php endif; ?>
            </form>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    </div>
</section>
    <div class="template-dialog" id="cropper_modal" hidden aria-hidden="true">
        <section class="template-dialog-card cropper-dialog-card" role="dialog" aria-modal="true">
            <button class="template-dialog-close" type="button" id="btn_close_cropper" aria-label="Fechar modal"><i class="ti ti-x" aria-hidden="true"></i></button>
            
            <div class="template-dialog-header">
                <div class="card-header-icon" style="background: rgba(124, 105, 255, 0.15); color: #8b7cff;"><i class="ti ti-crop"></i></div>
                <div>
                    <h2 class="template-dialog-title">Recortar e Enquadrar Imagem</h2>
                    <p class="template-dialog-desc">Arraste para mover, use o scroll ou botões para zoom e enquadre a imagem no formato quadrado (1:1).</p>
                </div>
            </div>

            <!-- Área de Visualização do Cropper -->
            <div class="cropper-viewport-wrap">
                <img id="cropper_image" src="" alt="Imagem para recorte">
            </div>

            <!-- Barra de Ferramentas / Controles de Zoom, Rotação e Reset -->
            <div class="cropper-toolbar">
                <div class="cropper-toolbar-group">
                    <button type="button" class="cropper-tool-btn" id="btn_crop_zoom_in" title="Aumentar Zoom">
                        <i class="ti ti-zoom-in"></i>
                    </button>
                    <button type="button" class="cropper-tool-btn" id="btn_crop_zoom_out" title="Diminuir Zoom">
                        <i class="ti ti-zoom-out"></i>
                    </button>
                    <button type="button" class="cropper-tool-btn" id="btn_crop_rotate_left" title="Girar 90° para a esquerda">
                        <i class="ti ti-rotate-2"></i>
                    </button>
                    <button type="button" class="cropper-tool-btn" id="btn_crop_rotate_right" title="Girar 90° para a direita">
                        <i class="ti ti-rotate-clockwise-2"></i>
                    </button>
                    <button type="button" class="cropper-tool-btn" id="btn_crop_flip_x" title="Espelhar Horizontalmente">
                        <i class="ti ti-flip-horizontal"></i>
                    </button>
                    <button type="button" class="cropper-tool-btn" id="btn_crop_reset" title="Restaurar Posição Original">
                        <i class="ti ti-refresh"></i>
                    </button>
                </div>

                <div class="cropper-info-badge">
                    <i class="ti ti-aspect-ratio"></i>
                    <span>Proporção 1:1 (600x600 px)</span>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="cropper-dialog-actions">
                <button type="button" class="button secondary" id="btn_cancel_cropper">Cancelar</button>
                <button type="button" class="button primary" id="btn_apply_cropper"><i class="ti ti-check"></i> Concluir Recorte</button>
            </div>
        </section>
    </div>

    <div class="template-dialog" data-template-dialog hidden aria-hidden="true">
        <section class="template-dialog-card template-dialog-split" role="dialog" aria-modal="true" aria-labelledby="template-dialog-title">
            <button class="template-dialog-close" type="button" data-close-template aria-label="Fechar modal"><i class="ti ti-x" aria-hidden="true"></i></button>
            
            <div class="template-dialog-form-col">
                <div class="template-dialog-header">
                    <div class="template-dialog-icon" aria-hidden="true"><i class="ti ti-message-2"></i></div>
                    <div>
                        <p class="template-dialog-kicker">Modelo de Mensagem</p>
                        <h2 id="template-dialog-title" data-template-modal-title>Novo Modelo</h2>
                    </div>
                </div>
                
                <form action="<?= site_url('configuracoes/modelos-mensagens') ?>" method="post" data-template-editor>
                    <?= csrf_field() ?>
                    <input type="hidden" name="template_id" data-template-id>
                    <div class="template-editor-grid">
                        <label class="form-field">
                            <span>Nome do modelo</span>
                            <input type="text" name="name" maxlength="120" required placeholder="Ex.: Chamada Curta + Foco no Benefício" data-template-name>
                        </label>
                        <label class="form-field">
                            <span>Conteúdo da mensagem</span>
                            <textarea class="template-textarea" name="content" rows="8" required placeholder="Digite a mensagem com as tags..." data-template-content></textarea>
                        </label>
                    </div>
                    <div class="template-tags-hint">
                        <span>Tags disponíveis (clique para inserir):</span>
                        <div class="tags-group">
                            <button type="button" class="tag-btn" data-insert-tag="{{nome}}">{{nome}}</button>
                            <button type="button" class="tag-btn" data-insert-tag="{{descricao}}">{{descricao}}</button>
                            <button type="button" class="tag-btn" data-insert-tag="{{preco}}">{{preco}}</button>
                            <button type="button" class="tag-btn" data-insert-tag="{{preco_promocional}}">{{preco_promocional}}</button>
                            <button type="button" class="tag-btn" data-insert-tag="{{desconto}}">{{desconto}}</button>
                            <button type="button" class="tag-btn" data-insert-tag="{{link}}">{{link}}</button>
                        </div>
                    </div>
                    <div class="template-dialog-actions">
                        <button class="button secondary" type="button" data-cancel-template>Cancelar</button>
                        <button class="button primary" type="submit"><i class="ti ti-device-floppy" aria-hidden="true"></i>Salvar Modelo</button>
                    </div>
                </form>
            </div>

            <div class="template-dialog-preview-col">
                <div class="whatsapp-mockup">
                    <div class="phone-notch"></div>
                    <div class="phone-status-bar">
                        <span>09:41</span>
                        <div class="status-icons"><i class="ti ti-wifi"></i><i class="ti ti-battery-4"></i></div>
                    </div>
                    <div class="phone-chat-header">
                        <div class="chat-header-avatar"><i class="ti ti-users"></i></div>
                        <div class="chat-header-info">
                            <strong>Grupo VIP • Ofertas</strong>
                            <small><?= esc($company['name'] ?? 'Dias Imports') ?>, Você e outros</small>
                        </div>
                    </div>
                    <div class="phone-chat-body">
                        <div class="chat-date-pill">HOJE</div>
                        <div class="chat-bubble-container">
                            <div class="chat-bubble">
                                <span class="bubble-sender"><?= esc($company['name'] ?? 'Dias Imports') ?></span>
                                <div class="bubble-image-preview" style="display: none; margin-bottom: 8px; border-radius: 8px; overflow: hidden;">
                                    <img src="" alt="Produto" style="width: 100%; height: auto; display: block;">
                                </div>
                                <div class="bubble-content" data-template-bubble-text>
                                    Olá! Confira as novidades imperdíveis da nossa loja.
                                </div>
                                <div class="bubble-meta">
                                    <span class="bubble-time">09:41</span>
                                    <i class="ti ti-checks bubble-checks"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="phone-chat-footer">
                        <div class="mock-input">Mensagem...</div>
                        <div class="mock-mic"><i class="ti ti-microphone"></i></div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="qr-dialog" data-qr-dialog hidden aria-hidden="true">
        <section class="qr-dialog-card" role="dialog" aria-modal="true" aria-labelledby="qr-dialog-title" aria-describedby="qr-dialog-message">
            <button class="qr-dialog-close" type="button" data-close-qr aria-label="Fechar QR Code"><i class="ti ti-x" aria-hidden="true"></i></button>
            <div class="qr-dialog-icon" aria-hidden="true"><i class="ti ti-brand-whatsapp"></i></div>
            <p class="qr-dialog-kicker">Conexão segura</p>
            <h2 id="qr-dialog-title">Conecte seu WhatsApp</h2>
            <p id="qr-dialog-message">No WhatsApp, acesse <strong>Aparelhos conectados</strong> e leia o código para conectar <span data-qr-instance></span>.</p>
            <div class="qr-code-stage" data-qr-stage aria-live="polite">
                <div class="qr-loading" data-qr-loading><i class="ti ti-loader-2" aria-hidden="true"></i><span>Gerando QR Code...</span></div>
                <img data-qr-image hidden alt="QR Code temporário para conectar a instância">
                <div class="qr-error" data-qr-error hidden><i class="ti ti-alert-triangle" aria-hidden="true"></i><strong>Não foi possível gerar o QR Code</strong><span data-qr-error-message></span><button class="button secondary" type="button" data-retry-qr>Tentar novamente</button></div>
            </div>
            <div class="qr-refresh-status"><i class="ti ti-refresh" aria-hidden="true"></i><span data-qr-countdown>Atualização automática em 20s</span></div>
        </section>
    </div>

    <div class="send-test-dialog" data-send-test-dialog hidden aria-hidden="true">
        <section class="send-test-dialog-card" role="dialog" aria-modal="true" aria-labelledby="send-test-dialog-title" aria-describedby="send-test-dialog-message">
            <button class="send-test-dialog-close" type="button" data-close-send-test aria-label="Fechar teste de envio"><i class="ti ti-x" aria-hidden="true"></i></button>
            <div class="send-test-dialog-icon" aria-hidden="true"><i class="ti ti-send"></i></div>
            <p class="send-test-dialog-kicker">Teste da instância</p>
            <h2 id="send-test-dialog-title">Enviar mensagem de teste</h2>
            <p id="send-test-dialog-message">Informe o WhatsApp que receberá a mensagem enviada por <strong data-send-test-instance></strong>.</p>
            <form action="<?= site_url('configuracoes/evolution/instancias/testar-envio') ?>" method="post" data-send-test-form>
                <?= csrf_field() ?>
                <input type="hidden" name="instance_name" data-send-test-instance-name>
                <label class="form-field"><span>WhatsApp destinatário</span><input type="tel" name="whatsapp" maxlength="20" required inputmode="numeric" autocomplete="tel" placeholder="(17) 98800-4745" data-send-test-phone></label>
                <div class="send-test-dialog-actions">
                    <button class="button secondary" type="button" data-cancel-send-test>Cancelar</button>
                    <button class="button primary" type="submit"><i class="ti ti-send" aria-hidden="true"></i>Enviar teste</button>
                </div>
            </form>
        </section>
    </div>

    <!-- Modal Picker de Ícones -->
    <div class="icon-picker-dialog" data-icon-picker-dialog hidden aria-hidden="true">
        <section class="icon-picker-card" role="dialog" aria-modal="true" aria-labelledby="icon-picker-title">
            <button class="icon-picker-close" type="button" data-close-icon-picker aria-label="Fechar seletor de ícones"><i class="ti ti-x" aria-hidden="true"></i></button>
            <div class="icon-picker-header">
                <div class="icon-picker-badge"><i class="ti ti-icons"></i></div>
                <div>
                    <h3 id="icon-picker-title">Biblioteca de Ícones</h3>
                    <p>Selecione um ícone com visual atrativo para o benefício</p>
                </div>
            </div>

            <div class="icon-picker-search">
                <i class="ti ti-search"></i>
                <input type="text" placeholder="Buscar por nome (ex: star, flame, discount, shield)..." data-icon-search-input>
            </div>

            <div class="icon-picker-grid" data-icon-grid>
                <!-- Preenchido via JS ou lista padrão de ícones Tabler populares -->
            </div>
        </section>
    </div>
</section>
<?= $this->endSection() ?>
