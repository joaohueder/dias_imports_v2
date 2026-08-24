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
<section class="settings-panel" data-layout-settings data-settings-root data-saved-width="<?= esc($layoutMaxWidth) ?>" data-active-tab="<?= esc($activeSettingsTab) ?>" data-instance-status-url="<?= site_url('configuracoes/evolution/instancias/status') ?>">
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

            <div class="whatsapp-list">
                <?php if ($companyWhatsapps === []): ?>
                    <div class="whatsapp-empty"><i class="ti ti-brand-whatsapp" aria-hidden="true"></i><p>Nenhum WhatsApp cadastrado.</p><span>Adicione o primeiro número de atendimento.</span></div>
                <?php else: foreach ($companyWhatsapps as $whatsapp): ?>
                    <article class="whatsapp-item <?= (int) $whatsapp['is_active'] !== 1 ? 'inactive' : '' ?>">
                        <div class="whatsapp-identity"><strong><?= esc($whatsapp['name']) ?></strong><span>— <?= esc($whatsapp['phone']) ?></span><?php if ((int) $whatsapp['is_default'] === 1): ?><span class="default-badge">Padrão</span><?php elseif ((int) $whatsapp['is_active'] !== 1): ?><span class="inactive-badge">Inativo</span><?php endif; ?></div>
                        <div class="whatsapp-actions">
                            <?php if (\App\Libraries\UserPermissions::hasPermission('company', 'edit') && (int) $whatsapp['is_default'] !== 1 && (int) $whatsapp['is_active'] === 1): ?><form action="<?= site_url('configuracoes/empresa/whatsapp/' . $whatsapp['id'] . '/padrao') ?>" method="post" data-confirm-action="default" data-whatsapp-name="<?= esc($whatsapp['name']) ?>"><?= csrf_field() ?><button class="button secondary compact" type="submit"><i class="ti ti-star"></i>Tornar padrão</button></form><?php endif; ?>
                            <?php if (\App\Libraries\UserPermissions::hasPermission('company', 'edit')): ?>
                            <button class="button secondary compact" type="button" data-edit-whatsapp data-id="<?= esc($whatsapp['id']) ?>" data-name="<?= esc($whatsapp['name']) ?>" data-phone="<?= esc($whatsapp['phone']) ?>"><i class="ti ti-pencil"></i>Editar</button>
                            <form action="<?= site_url('configuracoes/empresa/whatsapp/' . $whatsapp['id'] . '/status') ?>" method="post" data-confirm-action="<?= (int) $whatsapp['is_active'] === 1 ? 'deactivate' : 'activate' ?>" data-whatsapp-name="<?= esc($whatsapp['name']) ?>"><?= csrf_field() ?><button class="button secondary compact" type="submit"><i class="ti ti-power"></i><?= (int) $whatsapp['is_active'] === 1 ? 'Inativar' : 'Ativar' ?></button></form>
                            <?php endif; ?>
                            <?php if (\App\Libraries\UserPermissions::hasPermission('company', 'delete')): ?>
                            <form action="<?= site_url('configuracoes/empresa/whatsapp/' . $whatsapp['id'] . '/excluir') ?>" method="post" data-confirm-action="delete" data-whatsapp-name="<?= esc($whatsapp['name']) ?>"><?= csrf_field() ?><button class="button secondary compact danger" type="submit"><i class="ti ti-trash"></i>Excluir</button></form>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; endif; ?>
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

            <div class="instance-list">
                <?php if ($evolutionInstances === []): ?>
                    <div class="whatsapp-empty"><i class="ti ti-plug-connected-x" aria-hidden="true"></i><p>Nenhuma instância disponível.</p><span>Salve e teste as credenciais; depois crie a primeira instância.</span></div>
                <?php else: foreach ($evolutionInstances as $instance): ?>
                    <?php $isDefaultInstance = ($evolutionSettings['default_instance_name'] ?? null) === $instance['name']; ?>
                    <article class="instance-card" data-instance-card="<?= esc($instance['name']) ?>">
                        <div class="instance-main">
                            <div class="instance-heading">
                                <span class="instance-avatar"><?php if ($instance['profile_picture'] !== ''): ?><img src="<?= esc($instance['profile_picture']) ?>" alt=""><?php else: ?><i class="ti ti-brand-whatsapp" aria-hidden="true"></i><?php endif; ?></span>
                                <span><strong><?= esc($instance['profile_name']) ?></strong><small><?= esc($instance['name']) ?></small></span>
                                <?php if ($isDefaultInstance): ?><span class="default-badge">Padrão</span><?php endif; ?>
                            </div>
                            <dl class="instance-data"><div><dt>Status</dt><dd class="connection-status <?= $instance['connected'] ? 'connected' : 'disconnected' ?>" data-instance-status aria-live="polite"><i class="ti <?= $instance['connected'] ? 'ti-circle-check-filled' : 'ti-circle-x-filled' ?>" aria-hidden="true"></i><span><?= $instance['connected'] ? 'Conectada' : esc(ucfirst($instance['state'])) ?></span></dd></div><?php if ($instance['number'] !== ''): ?><div><dt>Número</dt><dd><?= esc($instance['number']) ?></dd></div><?php endif; ?></dl>
                        </div>
                        <div class="instance-actions">
                            <?php if (\App\Libraries\UserPermissions::hasPermission('evolution', 'edit') && ! $isDefaultInstance): ?><form action="<?= site_url('configuracoes/evolution/instancias/padrao') ?>" method="post" data-confirm-action="evolution-default" data-action-name="<?= esc($instance['name']) ?>"><?= csrf_field() ?><input type="hidden" name="instance_name" value="<?= esc($instance['name']) ?>"><button class="button secondary compact" type="submit"><i class="ti ti-star"></i>Tornar padrão</button></form><?php endif; ?>
                            <?php if (\App\Libraries\UserPermissions::hasPermission('evolution', 'edit')): ?>
                            <form action="<?= site_url('configuracoes/evolution/instancias/conectar') ?>" method="post" data-qr-connect-form data-instance-label="<?= esc($instance['profile_name']) ?>" <?= $instance['connected'] ? 'hidden' : '' ?>><?= csrf_field() ?><input type="hidden" name="instance_name" value="<?= esc($instance['name']) ?>"><button class="button secondary compact" type="submit"><i class="ti ti-qrcode"></i>Conectar</button></form>
                            <button class="button secondary compact send-test-trigger" type="button" data-instance-send-test data-instance-name="<?= esc($instance['name']) ?>" data-instance-label="<?= esc($instance['profile_name']) ?>" <?= $instance['connected'] ? '' : 'hidden' ?>><i class="ti ti-send" aria-hidden="true"></i>Testar envio</button>
                            <form action="<?= site_url('configuracoes/evolution/instancias/desconectar') ?>" method="post" data-confirm-action="evolution-logout" data-action-name="<?= esc($instance['name']) ?>" data-instance-disconnect <?= $instance['connected'] ? '' : 'hidden' ?>><?= csrf_field() ?><input type="hidden" name="instance_name" value="<?= esc($instance['name']) ?>"><button class="button secondary compact" type="submit"><i class="ti ti-plug-x"></i>Desconectar</button></form>
                            <?php endif; ?>
                            <?php if (\App\Libraries\UserPermissions::hasPermission('evolution', 'delete')): ?>
                            <form action="<?= site_url('configuracoes/evolution/instancias/excluir') ?>" method="post" data-confirm-action="evolution-delete" data-action-name="<?= esc($instance['name']) ?>"><?= csrf_field() ?><input type="hidden" name="instance_name" value="<?= esc($instance['name']) ?>"><button class="button secondary compact danger" type="submit"><i class="ti ti-trash"></i>Excluir</button></form>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; endif; ?>
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

        <?php if ($messageTemplates === []): ?>
            <div class="template-empty-state">
                <div class="template-empty-icon"><i class="ti ti-message-2" aria-hidden="true"></i></div>
                <p>Nenhum modelo cadastrado.</p>
                <span>Crie o primeiro modelo reutilizável para disparo.</span>
            </div>
        <?php else: ?>
            <div class="templates-grid" data-templates-grid>
                <?php foreach ($messageTemplates as $tpl): ?>
                    <article class="template-card <?= (int)$tpl['is_active'] !== 1 ? 'inactive' : '' ?>">
                        <div class="template-card-header">
                            <div class="template-card-title-group">
                                <h3 class="template-title"><?= esc($tpl['name']) ?></h3>
                                <div class="template-meta-row">
                                    <span class="template-badge <?= (int)$tpl['is_active'] === 1 ? 'active' : 'inactive' ?>"><?= (int)$tpl['is_active'] === 1 ? 'Ativo' : 'Inativo' ?></span>
                                    <span class="template-sends-badge"><i class="ti ti-send" aria-hidden="true"></i> <?= (int)$tpl['send_count'] ?> envios</span>
                                </div>
                            </div>
                        </div>

                        <div class="template-content-preview">
                            <?php
                                $sampleContent = strtr($tpl['content'], [
                                    '{{nome}}' => 'iPhone 15 Pro Max 256GB',
                                    '{{descricao}}' => 'Titânio Natural, tela Super Retina XDR e chip A17 Pro.',
                                    '{{preco}}' => 'R$ 7.890,00',
                                    '{{preco_promocional}}' => 'R$ 6.990,00',
                                    '{{desconto}}' => '12% OFF',
                                    '{{link}}' => 'https://diasimports.com.br/p/iphone-15',
                                ]);
                            ?>
                            <pre><?= esc($sampleContent) ?></pre>
                        </div>

                        <div class="template-card-footer">
                            <span class="template-meta">Criado em <?= $tpl['created_at'] ? date('d/m/Y', strtotime($tpl['created_at'])) : date('d/m/Y') ?></span>
                            <div class="template-actions">
                                <?php if (\App\Libraries\UserPermissions::hasPermission('message_templates', 'edit')): ?>
                                <button class="button secondary compact icon-btn" type="button" data-edit-template data-id="<?= esc($tpl['id']) ?>" data-name="<?= esc($tpl['name']) ?>" data-content="<?= esc($tpl['content']) ?>" title="Editar Modelo">
                                    <i class="ti ti-edit" aria-hidden="true"></i> Editar
                                </button>
                                <form action="<?= site_url('configuracoes/modelos-mensagens/' . $tpl['id'] . '/status') ?>" method="post" data-confirm-action="<?= (int)$tpl['is_active'] === 1 ? 'deactivate' : 'activate' ?>" data-action-name="<?= esc($tpl['name']) ?>">
                                    <?= csrf_field() ?>
                                    <button class="button secondary compact" type="submit" title="<?= (int)$tpl['is_active'] === 1 ? 'Inativar Modelo' : 'Ativar Modelo' ?>">
                                        <i class="ti <?= (int)$tpl['is_active'] === 1 ? 'ti-eye-off' : 'ti-eye' ?>" aria-hidden="true"></i>
                                        <?= (int)$tpl['is_active'] === 1 ? 'Inativar' : 'Ativar' ?>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <?php if (\App\Libraries\UserPermissions::hasPermission('message_templates', 'delete')): ?>
                                <form action="<?= site_url('configuracoes/modelos-mensagens/' . $tpl['id'] . '/excluir') ?>" method="post" data-confirm-action="delete" data-action-name="<?= esc($tpl['name']) ?>">
                                    <?= csrf_field() ?>
                                    <button class="button secondary compact danger" type="submit" title="Excluir Modelo">
                                        <i class="ti ti-trash" aria-hidden="true"></i> Excluir
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="template-empty-state" data-templates-empty-filter style="display: none; grid-column: 1 / -1; margin-top: 16px;">
                <div class="template-empty-icon"><i class="ti ti-message-off" aria-hidden="true"></i></div>
                <p>Nenhum modelo encontrado para o filtro selecionado.</p>
                <span>Tente alternar para outra visualização.</span>
                <button type="button" class="button secondary" data-templates-clear-filters style="margin-top: 8px;">
                    <i class="ti ti-filter-off" aria-hidden="true"></i>
                    <span>Limpar Filtro</span>
                </button>
            </div>
        <?php endif; ?>
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
