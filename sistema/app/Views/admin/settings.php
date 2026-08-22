<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$presets = [
    '1000' => ['label' => 'Compacto', 'value' => '1000px', 'class' => 'compact'],
    '1200' => ['label' => 'Padrão', 'value' => '1200px', 'class' => 'standard'],
    '1400' => ['label' => 'Largo', 'value' => '1400px', 'class' => 'wide'],
    'fluid' => ['label' => 'Fluido', 'value' => '100%', 'class' => 'fluid'],
];
$isFluid = $layoutMaxWidth === 'fluid';
$sliderValue = $isFluid ? 1800 : (int) $layoutMaxWidth;
?>
<section class="settings-panel" data-layout-settings data-settings-root data-saved-width="<?= esc($layoutMaxWidth) ?>" data-active-tab="<?= esc($activeSettingsTab) ?>" data-instance-status-url="<?= site_url('configuracoes/evolution/instancias/status') ?>">
    <div class="settings-tabs" role="tablist" aria-label="Categorias de configurações">
        <?php if (\App\Libraries\UserPermissions::hasPermission('layout', 'view')): ?>
            <button class="settings-tab <?= $activeSettingsTab === 'layout' ? 'active' : '' ?>" type="button" role="tab" id="layout-tab" aria-selected="<?= $activeSettingsTab === 'layout' ? 'true' : 'false' ?>" aria-controls="layout-panel" data-settings-tab="layout">Layout</button>
        <?php endif; ?>
        <?php if (\App\Libraries\UserPermissions::hasPermission('company', 'view')): ?>
            <button class="settings-tab <?= $activeSettingsTab === 'empresa' ? 'active' : '' ?>" type="button" role="tab" id="company-tab" aria-selected="<?= $activeSettingsTab === 'empresa' ? 'true' : 'false' ?>" aria-controls="company-panel" data-settings-tab="empresa">Empresa</button>
        <?php endif; ?>
        <?php if (\App\Libraries\UserPermissions::hasPermission('evolution', 'view')): ?>
            <button class="settings-tab <?= $activeSettingsTab === 'evolution' ? 'active' : '' ?>" type="button" role="tab" id="evolution-tab" aria-selected="<?= $activeSettingsTab === 'evolution' ? 'true' : 'false' ?>" aria-controls="evolution-panel" data-settings-tab="evolution">Evolution API</button>
        <?php endif; ?>
        <?php if (\App\Libraries\UserPermissions::hasPermission('meta_ads', 'view')): ?>
            <button class="settings-tab <?= $activeSettingsTab === 'meta-ads' ? 'active' : '' ?>" type="button" role="tab" id="meta-ads-tab" aria-selected="<?= $activeSettingsTab === 'meta-ads' ? 'true' : 'false' ?>" aria-controls="meta-ads-panel" data-settings-tab="meta-ads">Meta Ads</button>
        <?php endif; ?>
        <?php if (\App\Libraries\UserPermissions::hasPermission('message_templates', 'view')): ?>
            <button class="settings-tab <?= $activeSettingsTab === 'modelos-mensagens' ? 'active' : '' ?>" type="button" role="tab" id="templates-tab" aria-selected="<?= $activeSettingsTab === 'modelos-mensagens' ? 'true' : 'false' ?>" aria-controls="templates-panel" data-settings-tab="modelos-mensagens">Modelos de Mensagens</button>
        <?php endif; ?>
        <?php if (\App\Libraries\UserPermissions::hasPermission('landing_leads', 'view')): ?>
            <button class="settings-tab <?= $activeSettingsTab === 'landing-leads' ? 'active' : '' ?>" type="button" role="tab" id="landing-leads-tab" aria-selected="<?= $activeSettingsTab === 'landing-leads' ? 'true' : 'false' ?>" aria-controls="landing-leads-panel" data-settings-tab="landing-leads">Landing Page de Leads</button>
        <?php endif; ?>
    </div>

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
                <input id="layout-width-range" type="range" min="900" max="1800" step="10" value="<?= esc((string) $sliderValue) ?>" data-width-range aria-describedby="width-range-hint">
                <span class="sr-only" id="width-range-hint">A largura pode variar entre 900 e 1800 pixels.</span>
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
                <label class="form-field field-public-url"><span>Endereço público do site</span><input type="url" name="public_url" maxlength="255" required inputmode="url" placeholder="https://diasimports.com.br" value="<?= esc(old('public_url', $companyProfile['public_url'] ?? '')) ?>"><small>Base dos links das landing pages e do catálogo da Meta. Deve começar com https:// e ser um domínio público.</small></label>
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
                <div class="evolution-delay-grid">
                    <label class="form-field"><span>Espera mínima entre envios</span><span class="unit-input"><input type="number" name="min_delay_seconds" min="1" max="3600" required value="<?= esc(old('min_delay_seconds', $evolutionSettings['min_delay_seconds'] ?? 5)) ?>"><em>seg</em></span></label>
                    <label class="form-field"><span>Espera máxima entre envios</span><span class="unit-input"><input type="number" name="max_delay_seconds" min="1" max="3600" required value="<?= esc(old('max_delay_seconds', $evolutionSettings['max_delay_seconds'] ?? 30)) ?>"><em>seg</em></span></label>
                </div>
                <p class="delay-hint"><i class="ti ti-clock-shield" aria-hidden="true"></i>O sistema sorteará uma espera dentro desse intervalo entre cada grupo, reduzindo disparos consecutivos. Valores permitidos: 1 a 3600 segundos.</p>
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
                        <div class="instance-heading">
                            <span class="instance-avatar"><?php if ($instance['profile_picture'] !== ''): ?><img src="<?= esc($instance['profile_picture']) ?>" alt=""><?php else: ?><i class="ti ti-brand-whatsapp" aria-hidden="true"></i><?php endif; ?></span>
                            <span><strong><?= esc($instance['profile_name']) ?></strong><small><?= esc($instance['name']) ?></small></span>
                            <?php if ($isDefaultInstance): ?><span class="default-badge">Padrão</span><?php endif; ?>
                        </div>
                        <dl class="instance-data"><div><dt>Status</dt><dd class="connection-status <?= $instance['connected'] ? 'connected' : 'disconnected' ?>" data-instance-status aria-live="polite"><i class="ti <?= $instance['connected'] ? 'ti-circle-check-filled' : 'ti-circle-x-filled' ?>" aria-hidden="true"></i><span><?= $instance['connected'] ? 'Conectada' : esc(ucfirst($instance['state'])) ?></span></dd></div><?php if ($instance['number'] !== ''): ?><div><dt>Número</dt><dd><?= esc($instance['number']) ?></dd></div><?php endif; ?></dl>
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
                            <p>Disparado instantaneamente no carregamento da Landing Page de Leads tanto pelo Pixel do navegador quanto pela API de Conversões do servidor com deduplicação.</p>
                        </div>

                        <div class="meta-event-card">
                            <div class="meta-event-head">
                                <span class="event-badge success">Lead</span>
                                <span class="meta-event-tag">Cadastro Realizado</span>
                            </div>
                            <p>Disparado ao confirmar o envio do formulário de cadastro, enviando dados higienizados e <code>event_id</code> único para garantir 100% de atribuição de conversão.</p>
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
        <div class="section-title-row">
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
                            <pre><?= esc($tpl['content']) ?></pre>
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

    <!-- Aba Landing Page de Leads -->
    <?php if (\App\Libraries\UserPermissions::hasPermission('landing_leads', 'view')): ?>
    <div class="settings-tab-panel" id="landing-leads-panel" role="tabpanel" aria-labelledby="landing-leads-tab" data-settings-panel="landing-leads" <?= $activeSettingsTab !== 'landing-leads' ? 'hidden' : '' ?>>
        <div class="setting-intro">
            <div class="setting-intro-header">
                <div>
                    <span class="setting-intro-badge"><i class="ti ti-device-mobile"></i> Mobile First & Conversão</span>
                    <h2>Landing Page de Leads</h2>
                    <p>Personalize todos os textos, promessas, benefícios e links da página de captura de clientes para o Grupo VIP no WhatsApp. Acompanhe o resultado em tempo real no mockup ao lado.</p>
                </div>
                <div class="setting-intro-actions">
                    <a href="<?= site_url('leads') ?>" target="_blank" class="button outline" style="text-decoration:none;"><i class="ti ti-external-link" aria-hidden="true"></i>Ver Página Pública</a>
                </div>
            </div>
        </div>

        <?php
        $lp = $landingLeadSetting ?? [
            'template_model' => 'model-1',
            'color_palette' => 'palette-aurora',
            'bg_animation' => 'bg-particles',
            'btn_animation' => 'btn-pulse',
            'seo_title' => 'Grupo VIP Dias Imports | Ofertas e Descontos Exclusivos',
            'seo_description' => 'Participe do nosso grupo restrito de clientes e receba oportunidades imperdíveis de importados em primeira mão, com preços que não publicamos abertamente.',
            'headline' => 'Garanta Descontos Secretos e Ofertas VIP no WhatsApp',
            'subheadline' => 'Participe do nosso grupo restrito de clientes e receba oportunidades imperdíveis de importados em primeira mão, com preços que não publicamos abertamente.',
            'badge_text' => '🔥 GRUPO VIP EXCLUSIVO • VAGAS LIMITADAS',
            'button_text' => 'QUERO MEU ACESSO VIP AGORA',
            'button_subtext' => '🔒 Acesso 100% gratuito e sem spam',
            'whatsapp_group_link' => 'https://chat.whatsapp.com/',
            'card1_icon' => 'ti-discount-check',
            'card1_title' => 'Até 50% de Desconto Real',
            'card1_desc' => 'Preços exclusivos de atacado e varejo direto para membros do grupo.',
            'card2_icon' => 'ti-flame',
            'card2_title' => 'Ofertas Relâmpago e Primeira Mão',
            'card2_desc' => 'Novidades e lançamentos liberados no grupo antes de todo mundo.',
            'card3_icon' => 'ti-shield-lock',
            'card3_title' => '100% Original e com Garantia',
            'card3_desc' => 'Importados com nota fiscal, procedência garantida e suporte humanizado.',
            'modal_title' => '🎉 Parabéns! Seu Acesso VIP Está Liberado',
            'modal_desc' => 'Seu cadastro foi realizado com sucesso. Clique no botão abaixo para entrar agora no Grupo VIP Oficial no WhatsApp e não perder nenhuma oportunidade.',
            'modal_button_text' => 'ENTRAR NO GRUPO VIP DO WHATSAPP',
        ];
        ?>

        <form action="<?= site_url('configuracoes/landing-leads') ?>" method="post" data-landing-form>
            <?= csrf_field() ?>
            <div class="landing-split-layout">
                <!-- Coluna da Esquerda: Configurações -->
                <div class="landing-config-col">
                    <!-- SELETOR DE MODELO VISUAL (6 MODELOS) -->
                    <div class="settings-card-block">
                        <div class="settings-card-header">
                            <div class="card-header-icon" style="background: rgba(99, 91, 255, 0.15); color: #635bff;"><i class="ti ti-template"></i></div>
                            <div>
                                <h3 class="settings-section-title">Modelo Visual da Landing Page (6 Opções)</h3>
                                <p class="settings-section-subtitle">Selecione a estrutura e experiência visual da página. O mockup ao lado atualiza em tempo real.</p>
                            </div>
                        </div>

                        <div class="template-models-grid">
                            <?php
                            $modelsList = [
                                ['id' => 'model-1', 'name' => 'Hero Direct & Glass', 'desc' => 'Form no topo com glassmorphism e foco total na conversão imediata.', 'icon' => 'ti-layout-topbar'],
                                ['id' => 'model-2', 'name' => 'Benefits First', 'desc' => 'Benefícios e prova de valor antes do form para gerar mais desejo.', 'icon' => 'ti-layout-list'],
                                ['id' => 'model-3', 'name' => 'Minimal Compact', 'desc' => 'Pílulas arredondadas, direto ao ponto e otimizado para 1 polegar.', 'icon' => 'ti-pill'],
                                ['id' => 'model-4', 'name' => 'Bento Grid', 'desc' => 'Layout moderno em blocos assimétricos estilo Bento Box.', 'icon' => 'ti-layout-grid'],
                                ['id' => 'model-5', 'name' => 'Cyber Tech Neon', 'desc' => 'Bordas tracejadas neon, visual escuro e tipografia futurista.', 'icon' => 'ti-bolt'],
                                ['id' => 'model-6', 'name' => 'Editorial Luxury', 'desc' => 'Estilo premium e sofisticado para produtos de alto padrão.', 'icon' => 'ti-crown'],
                            ];
                            $currentModel = $lp['template_model'] ?? 'model-1';
                            ?>
                            <?php foreach ($modelsList as $m): ?>
                                <label class="template-model-card <?= $currentModel === $m['id'] ? 'active' : '' ?>">
                                    <input type="radio" name="template_model" value="<?= esc($m['id']) ?>" <?= $currentModel === $m['id'] ? 'checked' : '' ?> data-lp-model-radio>
                                    <div class="template-card-inner">
                                        <div class="template-card-icon"><i class="ti <?= esc($m['icon']) ?>"></i></div>
                                        <div class="template-card-info">
                                            <strong><?= esc($m['name']) ?></strong>
                                            <p><?= esc($m['desc']) ?></p>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- SELETOR DE PALETA DE CORES (6 PALETAS) -->
                    <div class="settings-card-block">
                        <div class="settings-card-header">
                            <div class="card-header-icon" style="background: rgba(236, 72, 153, 0.15); color: #ec4899;"><i class="ti ti-palette"></i></div>
                            <div>
                                <h3 class="settings-section-title">Paleta de Cores & Efeitos (6 Opções)</h3>
                                <p class="settings-section-subtitle">Alterne as cores e vibração de contraste da página de captura instantaneamente.</p>
                            </div>
                        </div>

                        <div class="color-palettes-grid">
                            <?php
                            $palettesList = [
                                ['id' => 'palette-aurora', 'name' => 'Aurora Neon', 'colors' => ['#635bff', '#ec4899'], 'desc' => 'Violeta & Rosa'],
                                ['id' => 'palette-emerald', 'name' => 'Emerald Tech', 'colors' => ['#10b981', '#059669'], 'desc' => 'Verde WhatsApp VIP'],
                                ['id' => 'palette-amber', 'name' => 'Amber Gold', 'colors' => ['#f59e0b', '#ea580c'], 'desc' => 'Ouro & Luxo'],
                                ['id' => 'palette-ocean', 'name' => 'Ocean Cyan', 'colors' => ['#0ea5e9', '#2563eb'], 'desc' => 'Azul & Ciano Tech'],
                                ['id' => 'palette-crimson', 'name' => 'Crimson Ruby', 'colors' => ['#f43f5e', '#be123c'], 'desc' => 'Vermelho Urgência'],
                                ['id' => 'palette-obsidian', 'name' => 'Obsidian Minimal', 'colors' => ['#ffffff', '#71717a'], 'desc' => 'Preto Puro & Titânio'],
                            ];
                            $currentPalette = $lp['color_palette'] ?? 'palette-aurora';
                            ?>
                            <?php foreach ($palettesList as $p): ?>
                                <label class="color-palette-card <?= $currentPalette === $p['id'] ? 'active' : '' ?>">
                                    <input type="radio" name="color_palette" value="<?= esc($p['id']) ?>" <?= $currentPalette === $p['id'] ? 'checked' : '' ?> data-lp-palette-radio>
                                    <div class="palette-card-inner">
                                        <div class="palette-preview-dots">
                                            <span style="background: <?= esc($p['colors'][0]) ?>;"></span>
                                            <span style="background: <?= esc($p['colors'][1]) ?>;"></span>
                                        </div>
                                        <div class="palette-info">
                                            <strong><?= esc($p['name']) ?></strong>
                                            <small><?= esc($p['desc']) ?></small>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- SELETOR DE ANIMAÇÃO DO BACKGROUND (6 MODELOS) -->
                    <div class="settings-card-block">
                        <div class="settings-card-header">
                            <div class="card-header-icon" style="background: rgba(14, 165, 233, 0.15); color: #0ea5e9;"><i class="ti ti-sparkles"></i></div>
                            <div>
                                <h3 class="settings-section-title">Animação de Fundo (6 Modelos de Background)</h3>
                                <p class="settings-section-subtitle">Escolha o efeito dinâmico de partículas, luzes e fluidos do background.</p>
                            </div>
                        </div>

                        <div class="template-models-grid">
                            <?php
                            $bgAnimationsList = [
                                ['id' => 'bg-particles', 'name' => 'Partículas & Orbes', 'desc' => 'Orbes e luzes suaves flutuando harmonicamente no fundo.', 'icon' => 'ti-flare'],
                                ['id' => 'bg-mesh-gradient', 'name' => 'Gradiente Líquido Mesh', 'desc' => 'Fluxo fluido de gradiente que se move continuamente.', 'icon' => 'ti-ripple'],
                                ['id' => 'bg-cyber-grid', 'name' => 'Grid Tech Futurista', 'desc' => 'Grade tecnológica em perspectiva com linhas luminosas.', 'icon' => 'ti-grid-dots'],
                                ['id' => 'bg-radial-pulse', 'name' => 'Pulso Radial Concêntrico', 'desc' => 'Ondas circulares pulsando a partir do centro da tela.', 'icon' => 'ti-circle-dashed'],
                                ['id' => 'bg-floating-shapes', 'name' => 'Geometrias Flutuantes', 'desc' => 'Formas geométricas translúcidas com rotação suave.', 'icon' => 'ti-polygon'],
                                ['id' => 'bg-minimal-static', 'name' => 'Estático Minimalista', 'desc' => 'Sem animação de fundo, foco total no conteúdo e conversão.', 'icon' => 'ti-app-window'],
                            ];
                            $currentBgAnimation = $lp['bg_animation'] ?? 'bg-particles';
                            ?>
                            <?php foreach ($bgAnimationsList as $b): ?>
                                <label class="template-model-card <?= $currentBgAnimation === $b['id'] ? 'active' : '' ?>">
                                    <input type="radio" name="bg_animation" value="<?= esc($b['id']) ?>" <?= $currentBgAnimation === $b['id'] ? 'checked' : '' ?> data-lp-bganim-radio>
                                    <div class="template-card-inner">
                                        <div class="template-card-icon" style="background: rgba(14, 165, 233, 0.12); color: #0ea5e9;"><i class="ti <?= esc($b['icon']) ?>"></i></div>
                                        <div class="template-card-info">
                                            <strong><?= esc($b['name']) ?></strong>
                                            <p><?= esc($b['desc']) ?></p>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- SEO & METADADOS DE COMPARTILHAMENTO (OPEN GRAPH / WHATSAPP / REDES SOCIAIS) -->
                    <div class="settings-card-block">
                        <div class="settings-card-header">
                            <div class="card-header-icon" style="background: rgba(52, 211, 153, 0.15); color: #34d399;"><i class="ti ti-share"></i></div>
                            <div>
                                <h3 class="settings-section-title">SEO & Compartilhamento Social</h3>
                                <p class="settings-section-subtitle">Título, descrição e prévia exibidos no Google, WhatsApp, Facebook e Instagram ao enviar o link.</p>
                            </div>
                        </div>

                        <div class="form-grid single">
                            <label class="form-field">
                                <span class="field-label-row">
                                    <span>Título da Página e de Compartilhamento (SEO Title / OG Title)</span>
                                    <small class="field-hint">Aparece na aba do navegador e no título do card no WhatsApp</small>
                                </span>
                                <input type="text" name="seo_title" value="<?= esc($lp['seo_title'] ?? '') ?>" placeholder="Ex: Grupo VIP Dias Imports | Ofertas e Descontos Exclusivos" maxlength="255" data-lp-input="seo-title">
                            </label>

                            <label class="form-field">
                                <span class="field-label-row">
                                    <span>Descrição da Página e Compartilhamento (Meta Description / OG Description)</span>
                                    <small class="field-hint">Resumo chamativo que aparece abaixo do título no compartilhamento</small>
                                </span>
                                <textarea name="seo_description" rows="3" placeholder="Ex: Participe do nosso grupo restrito de clientes e receba oportunidades imperdíveis de importados em primeira mão." data-lp-input="seo-desc"><?= esc($lp['seo_description'] ?? '') ?></textarea>
                            </label>

                            <!-- UPLOAD E RECORTE DE IMAGEM SOCIAL / WHATSAPP -->
                            <div class="form-field">
                                <span class="field-label-row">
                                    <span>Imagem de Compartilhamento (Open Graph / WhatsApp)</span>
                                    <small class="field-hint">Tamanho ideal: <strong>600x600 px</strong> (quadrada 1:1) ou <strong>1200x630 px</strong> (máx. 2MB)</small>
                                </span>

                                <div class="og-uploader-wrap">
                                    <div class="og-thumb-container">
                                        <?php 
                                        $currentSeoImg = !empty($lp['seo_image']) ? base_url($lp['seo_image']) : base_url('og-image.png');
                                        $hasCustomImg = !empty($lp['seo_image']);
                                        ?>
                                        <img src="<?= $currentSeoImg ?>" alt="Imagem de Compartilhamento" id="seo_preview_img" class="og-thumb-preview" data-default-src="<?= base_url('og-image.png') ?>">
                                        <span class="og-thumb-label" id="og_thumb_label"><?= $hasCustomImg ? 'Personalizada' : 'Padrão (DI)' ?></span>
                                    </div>

                                    <div class="og-uploader-actions">
                                        <input type="file" id="seo_image_input" accept="image/png, image/jpeg, image/webp" hidden>
                                        <input type="hidden" name="seo_image_base64" id="seo_image_base64" value="">
                                        <input type="hidden" name="seo_image_action" id="seo_image_action" value="">

                                        <button type="button" class="button secondary" id="btn_upload_seo_image">
                                            <i class="ti ti-upload"></i>
                                            <span>Enviar nova imagem</span>
                                        </button>

                                        <button type="button" class="button danger-light" id="btn_remove_seo_image" style="<?= $hasCustomImg ? '' : 'display: none;' ?>">
                                            <i class="ti ti-trash"></i>
                                            <span>Restaurar padrão "DI"</span>
                                        </button>
                                        
                                        <div class="og-upload-info">
                                            <i class="ti ti-info-circle"></i>
                                            <span>Formatos aceitos: JPG, PNG, WEBP. Você poderá recortar e enquadrar a imagem antes de salvar.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card de Prévia de Compartilhamento no WhatsApp -->
                            <div class="share-preview-box">
                                <span class="share-preview-badge"><i class="ti ti-brand-whatsapp"></i> Prévia em tempo real no WhatsApp</span>
                                <div class="share-preview-card">
                                    <div class="share-preview-thumb">
                                        <img src="<?= $currentSeoImg ?>" alt="Prévia WhatsApp" id="whatsapp_preview_img">
                                    </div>
                                    <div class="share-preview-content">
                                        <span class="share-preview-domain"><?= parse_url(base_url(), PHP_URL_HOST) ?: 'diasimports.com.br' ?></span>
                                        <strong class="share-preview-title" data-share-preview-title><?= esc(!empty($lp['seo_title']) ? $lp['seo_title'] : ($lp['headline'] ?? 'Grupo VIP Dias Imports')) ?></strong>
                                        <p class="share-preview-desc" data-share-preview-desc><?= esc(!empty($lp['seo_description']) ? $lp['seo_description'] : ($lp['subheadline'] ?? 'Receba oportunidades imperdíveis de importados em primeira mão.')) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="settings-card-block">
                        <div class="settings-card-header">
                            <div class="card-header-icon"><i class="ti ti-flame"></i></div>
                            <div>
                                <h3 class="settings-section-title">Dobra Principal & Promessa Irresistível</h3>
                                <p class="settings-section-subtitle">Textos que causam o primeiro impacto visual e prendem a atenção do visitante no celular.</p>
                            </div>
                        </div>
                        
                        <div class="form-grid single">
                            <label class="form-field">
                                <span class="field-label-row">
                                    <span>Badge de Urgência / Escassez (Topo)</span>
                                    <small class="field-hint">Ex: 🔥 Vagas Limitadas</small>
                                </span>
                                <input type="text" name="badge_text" value="<?= esc($lp['badge_text']) ?>" required maxlength="100" data-lp-input="badge">
                            </label>

                            <label class="form-field">
                                <span class="field-label-row">
                                    <span>Headline Principal (Promessa Forte de Ganho/Desconto)</span>
                                    <small class="field-hint">Destaque o valor principal</small>
                                </span>
                                <input type="text" name="headline" value="<?= esc($lp['headline']) ?>" required maxlength="255" data-lp-input="headline">
                            </label>

                            <label class="form-field">
                                <span class="field-label-row">
                                    <span>Subheadline (Texto de Apoio Persuasivo)</span>
                                    <small class="field-hint">Explique como funciona o grupo exclusivo</small>
                                </span>
                                <textarea name="subheadline" rows="3" data-lp-input="subheadline"><?= esc($lp['subheadline']) ?></textarea>
                            </label>
                        </div>
                    </div>

                    <div class="settings-card-block">
                        <div class="settings-card-header">
                            <div class="card-header-icon" style="background: rgba(37, 211, 102, 0.15); color: #25d366;"><i class="ti ti-brand-whatsapp"></i></div>
                            <div>
                                <h3 class="settings-section-title">Chamada para Ação (CTA) e Grupo VIP</h3>
                                <p class="settings-section-subtitle">Configurações do botão de envio, animação atrativa e link oficial do grupo no WhatsApp.</p>
                            </div>
                        </div>
                        
                        <div class="form-grid single">
                            <label class="form-field">
                                <span class="field-label-row">
                                    <span>Link de Convite do Grupo VIP no WhatsApp</span>
                                    <small class="field-hint">https://chat.whatsapp.com/...</small>
                                </span>
                                <input type="url" name="whatsapp_group_link" value="<?= esc($lp['whatsapp_group_link']) ?>" required placeholder="https://chat.whatsapp.com/..." data-lp-input="group-link">
                            </label>

                            <div class="form-grid">
                                <label class="form-field">
                                    <span>Texto do Botão CTA (Captura)</span>
                                    <input type="text" name="button_text" value="<?= esc($lp['button_text']) ?>" required maxlength="100" data-lp-input="button-text">
                                </label>

                                <label class="form-field">
                                    <span>Microtexto de Segurança (Abaixo do Botão)</span>
                                    <input type="text" name="button_subtext" value="<?= esc($lp['button_subtext']) ?>" maxlength="150" data-lp-input="button-subtext">
                                </label>
                            </div>

                            <!-- SELETOR DE ANIMAÇÕES DO BOTÃO CTA (6 OPÇÕES) -->
                            <div class="cta-animation-group" style="margin-top: 10px;">
                                <span class="field-label-row" style="margin-bottom: 8px;">
                                    <span style="font-weight: 700; font-size: 13px; color: var(--text-heading);">Animação do Botão CTA (6 Opções)</span>
                                    <small class="field-hint">Efeito de movimento para atrair o clique</small>
                                </span>
                                <div class="template-models-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                                    <?php
                                    $btnAnimationsList = [
                                        ['id' => 'btn-pulse', 'name' => 'Pulso Rítmico', 'desc' => 'Respiração contínua com halo de luz e escala.', 'icon' => 'ti-heart-rate-monitor'],
                                        ['id' => 'btn-shimmer', 'name' => 'Feixe de Luz (Shimmer)', 'desc' => 'Reflexo metálico brilhante passando pelo botão.', 'icon' => 'ti-sparkles'],
                                        ['id' => 'btn-shake', 'name' => 'Microvibração (Shake)', 'desc' => 'Vibração atrativa em intervalos regulares.', 'icon' => 'ti-device-mobile-vibration'],
                                        ['id' => 'btn-bounce', 'name' => 'Salto Suave (Bounce)', 'desc' => 'Pequeno pulo vertical periódico chamando atenção.', 'icon' => 'ti-arrow-autofit-up'],
                                        ['id' => 'btn-glow-expand', 'name' => 'Onda Expansiva (Ripple)', 'desc' => 'Onda luminosa circular que se propaga continuamente.', 'icon' => 'ti-ripple'],
                                        ['id' => 'btn-none', 'name' => 'Estático (Sem Efeito)', 'desc' => 'Sem animação contínua, efeito apenas ao passar o mouse.', 'icon' => 'ti-hand-finger'],
                                    ];
                                    $currentBtnAnimation = $lp['btn_animation'] ?? 'btn-pulse';
                                    ?>
                                    <?php foreach ($btnAnimationsList as $btn): ?>
                                        <label class="template-model-card <?= $currentBtnAnimation === $btn['id'] ? 'active' : '' ?>">
                                            <input type="radio" name="btn_animation" value="<?= esc($btn['id']) ?>" <?= $currentBtnAnimation === $btn['id'] ? 'checked' : '' ?> data-lp-btnanim-radio>
                                            <div class="template-card-inner">
                                                <div class="template-card-icon" style="background: rgba(37, 211, 102, 0.12); color: #25d366;"><i class="ti <?= esc($btn['icon']) ?>"></i></div>
                                                <div class="template-card-info">
                                                    <strong><?= esc($btn['name']) ?></strong>
                                                    <p><?= esc($btn['desc']) ?></p>
                                                </div>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="settings-card-block">
                        <div class="settings-card-header">
                            <div class="card-header-icon" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;"><i class="ti ti-star"></i></div>
                            <div>
                                <h3 class="settings-section-title">Cartões de Vantagens e Benefícios</h3>
                                <p class="settings-section-subtitle">3 pilares persuasivos para eliminar objeções e aumentar a taxa de conversão.</p>
                            </div>
                        </div>
                        
                        <!-- Benefício 1 -->
                        <div class="benefit-config-card">
                            <div class="benefit-card-header">
                                <span class="benefit-pill">Benefício 01</span>
                                <div class="benefit-card-actions">
                                    <input type="hidden" name="card1_icon" id="card1_icon" value="<?= esc($lp['card1_icon']) ?>" data-lp-input="card1-icon">
                                    <button type="button" class="icon-picker-trigger-btn" data-open-icon-picker="1" title="Clique para escolher o ícone">
                                        <span class="icon-trigger-preview"><i class="ti <?= esc($lp['card1_icon']) ?>" data-benefit-icon-preview="1"></i></span>
                                        <span>Escolher Ícone</span>
                                        <i class="ti ti-chevron-down icon-trigger-arrow" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-grid single">
                                <label class="form-field">
                                    <span>Título do Benefício</span>
                                    <input type="text" name="card1_title" value="<?= esc($lp['card1_title']) ?>" required maxlength="100" data-lp-input="card1-title">
                                </label>
                                <label class="form-field">
                                    <span>Descrição Detalhada</span>
                                    <input type="text" name="card1_desc" value="<?= esc($lp['card1_desc']) ?>" required maxlength="255" data-lp-input="card1-desc">
                                </label>
                            </div>
                        </div>

                        <!-- Benefício 2 -->
                        <div class="benefit-config-card" style="margin-top:16px;">
                            <div class="benefit-card-header">
                                <span class="benefit-pill">Benefício 02</span>
                                <div class="benefit-card-actions">
                                    <input type="hidden" name="card2_icon" id="card2_icon" value="<?= esc($lp['card2_icon']) ?>" data-lp-input="card2-icon">
                                    <button type="button" class="icon-picker-trigger-btn" data-open-icon-picker="2" title="Clique para escolher o ícone">
                                        <span class="icon-trigger-preview"><i class="ti <?= esc($lp['card2_icon']) ?>" data-benefit-icon-preview="2"></i></span>
                                        <span>Escolher Ícone</span>
                                        <i class="ti ti-chevron-down icon-trigger-arrow" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-grid single">
                                <label class="form-field">
                                    <span>Título do Benefício</span>
                                    <input type="text" name="card2_title" value="<?= esc($lp['card2_title']) ?>" required maxlength="100" data-lp-input="card2-title">
                                </label>
                                <label class="form-field">
                                    <span>Descrição Detalhada</span>
                                    <input type="text" name="card2_desc" value="<?= esc($lp['card2_desc']) ?>" required maxlength="255" data-lp-input="card2-desc">
                                </label>
                            </div>
                        </div>

                        <!-- Benefício 3 -->
                        <div class="benefit-config-card" style="margin-top:16px;">
                            <div class="benefit-card-header">
                                <span class="benefit-pill">Benefício 03</span>
                                <div class="benefit-card-actions">
                                    <input type="hidden" name="card3_icon" id="card3_icon" value="<?= esc($lp['card3_icon']) ?>" data-lp-input="card3-icon">
                                    <button type="button" class="icon-picker-trigger-btn" data-open-icon-picker="3" title="Clique para escolher o ícone">
                                        <span class="icon-trigger-preview"><i class="ti <?= esc($lp['card3_icon']) ?>" data-benefit-icon-preview="3"></i></span>
                                        <span>Escolher Ícone</span>
                                        <i class="ti ti-chevron-down icon-trigger-arrow" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-grid single">
                                <label class="form-field">
                                    <span>Título do Benefício</span>
                                    <input type="text" name="card3_title" value="<?= esc($lp['card3_title']) ?>" required maxlength="100" data-lp-input="card3-title">
                                </label>
                                <label class="form-field">
                                    <span>Descrição Detalhada</span>
                                    <input type="text" name="card3_desc" value="<?= esc($lp['card3_desc']) ?>" required maxlength="255" data-lp-input="card3-desc">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="settings-card-block">
                        <div class="settings-card-header">
                            <div class="card-header-icon" style="background: rgba(124, 105, 255, 0.15); color: #a855f7;"><i class="ti ti-circle-check"></i></div>
                            <div>
                                <h3 class="settings-section-title">Modal de Confirmação & Acesso VIP</h3>
                                <p class="settings-section-subtitle">Exibido imediatamente após o lead preencher o formulário para conduzi-lo ao WhatsApp.</p>
                            </div>
                        </div>
                        
                        <div class="form-grid single">
                            <label class="form-field">
                                <span>Título de Celebração do Modal</span>
                                <input type="text" name="modal_title" value="<?= esc($lp['modal_title']) ?>" required maxlength="150" data-lp-input="modal-title">
                            </label>

                            <label class="form-field">
                                <span>Mensagem de Instrução / Próximo Passo</span>
                                <textarea name="modal_desc" rows="3" data-lp-input="modal-desc"><?= esc($lp['modal_desc']) ?></textarea>
                            </label>

                            <label class="form-field">
                                <span>Texto do Botão Final (Direcionar para o WhatsApp)</span>
                                <input type="text" name="modal_button_text" value="<?= esc($lp['modal_button_text']) ?>" required maxlength="100" data-lp-input="modal-button-text">
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Coluna da Direita: Preview Mobile Interativo e Fiel -->
                <div class="landing-preview-col">
                    <div class="preview-sticky-wrap">
                        <div class="preview-device-header">
                            <div class="preview-device-title">
                                <span class="preview-live-indicator"></span>
                                <strong>Preview Mobile</strong>
                            </div>
                            <!-- Switcher de visualização: Página vs Modal -->
                            <div class="preview-mode-switch" role="tablist">
                                <button type="button" class="preview-mode-btn active" data-preview-mode="page"><i class="ti ti-layout"></i> Página</button>
                                <button type="button" class="preview-mode-btn" data-preview-mode="modal"><i class="ti ti-app-window"></i> Modal VIP</button>
                            </div>
                        </div>
                        
                        <div class="mobile-mockup-frame">
                            <!-- Dynamic Island / Topo do Celular -->
                            <div class="mobile-dynamic-island">
                                <div class="island-camera"></div>
                                <div class="island-sensor"></div>
                            </div>
                            
                            <!-- Barra de Status do Sistema -->
                            <div class="mobile-mockup-status-bar">
                                <span class="status-time">9:41</span>
                                <div class="status-icons">
                                    <i class="ti ti-antenna-bars-5"></i>
                                    <i class="ti ti-wifi"></i>
                                    <i class="ti ti-battery-4"></i>
                                </div>
                            </div>
                            
                            <div class="mobile-screen-content" data-mockup-screen data-mockup-model="<?= esc($lp['template_model'] ?? 'model-1') ?>" data-mockup-palette="<?= esc($lp['color_palette'] ?? 'palette-aurora') ?>" data-mockup-bganim="<?= esc($lp['bg_animation'] ?? 'bg-particles') ?>" data-mockup-btnanim="<?= esc($lp['btn_animation'] ?? 'btn-pulse') ?>">
                                <!-- Camada de Efeitos Dinâmicos de Fundo no Mockup -->
                                <div class="bg-fx-layer" aria-hidden="true">
                                    <div class="bg-particles-wrap">
                                        <div class="particle-orb"></div>
                                        <div class="particle-orb"></div>
                                        <div class="particle-orb"></div>
                                    </div>
                                    <div class="bg-mesh-wrap"></div>
                                    <div class="bg-grid-wrap"><div class="bg-grid-plane"></div></div>
                                    <div class="bg-pulse-wrap">
                                        <div class="pulse-ring"></div>
                                        <div class="pulse-ring"></div>
                                        <div class="pulse-ring"></div>
                                    </div>
                                    <div class="bg-shapes-wrap">
                                        <div class="shape-item"></div>
                                        <div class="shape-item"></div>
                                        <div class="shape-item"></div>
                                    </div>
                                </div>

                                <!-- Modo 1: Página de Captura -->
                                <div class="lp-preview-body" data-preview-screen="page" style="position: relative; z-index: 1;">
                                    <div class="lp-preview-brand">
                                        <div class="lp-preview-logo">DI</div>
                                        <span class="lp-preview-brandname"><?= esc($companyProfile['name'] ?? 'Dias Imports') ?></span>
                                    </div>

                                    <div class="lp-preview-badge" data-preview="badge">
                                        <span class="lp-preview-dot"></span>
                                        <span><?= esc($lp['badge_text']) ?></span>
                                    </div>

                                    <h4 class="lp-preview-title" data-preview="headline"><?= esc($lp['headline']) ?></h4>
                                    <p class="lp-preview-sub" data-preview="subheadline"><?= esc($lp['subheadline']) ?></p>

                                    <div class="lp-preview-form-card">
                                        <div class="lp-preview-card-header">
                                            <div class="lp-card-tag"><i class="ti ti-lock"></i> Acesso Restrito</div>
                                            <strong>Liberar Convite Exclusivo</strong>
                                            <small>Preencha para receber o link direto</small>
                                        </div>
                                        <div class="lp-mock-input"><i class="ti ti-user"></i> <span>Seu Nome Completo</span></div>
                                        <div class="lp-mock-input"><i class="ti ti-brand-whatsapp"></i> <span>(00) 00000-0000</span></div>
                                        <div class="lp-preview-btn">
                                            <i class="ti ti-arrow-narrow-right"></i>
                                            <span data-preview="button-text"><?= esc($lp['button_text']) ?></span>
                                        </div>
                                        <small class="lp-preview-btn-sub" data-preview="button-subtext"><?= esc($lp['button_subtext']) ?></small>
                                    </div>

                                    <div class="lp-preview-benefits">
                                        <div class="lp-preview-bcard">
                                            <div class="lp-preview-bicon"><i class="ti <?= esc($lp['card1_icon']) ?>" data-preview="card1-icon"></i></div>
                                            <div class="lp-preview-binfo">
                                                <strong data-preview="card1-title"><?= esc($lp['card1_title']) ?></strong>
                                                <p data-preview="card1-desc"><?= esc($lp['card1_desc']) ?></p>
                                            </div>
                                        </div>
                                        <div class="lp-preview-bcard">
                                            <div class="lp-preview-bicon"><i class="ti <?= esc($lp['card2_icon']) ?>" data-preview="card2-icon"></i></div>
                                            <div class="lp-preview-binfo">
                                                <strong data-preview="card2-title"><?= esc($lp['card2_title']) ?></strong>
                                                <p data-preview="card2-desc"><?= esc($lp['card2_desc']) ?></p>
                                            </div>
                                        </div>
                                        <div class="lp-preview-bcard">
                                            <div class="lp-preview-bicon"><i class="ti <?= esc($lp['card3_icon']) ?>" data-preview="card3-icon"></i></div>
                                            <div class="lp-preview-binfo">
                                                <strong data-preview="card3-title"><?= esc($lp['card3_title']) ?></strong>
                                                <p data-preview="card3-desc"><?= esc($lp['card3_desc']) ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="lp-preview-footer">
                                        <div class="lp-preview-trust">
                                            <span><i class="ti ti-shield-check"></i> Seguro</span>
                                            <span><i class="ti ti-discount-check"></i> Oficial</span>
                                            <span><i class="ti ti-bolt"></i> Instantâneo</span>
                                        </div>
                                        <small>© <?= esc($companyProfile['name'] ?? 'Dias Imports') ?> • Todos os direitos reservados</small>
                                    </div>
                                </div>

                                <!-- Modo 2: Preview do Modal VIP -->
                                <div class="lp-preview-modal-view" data-preview-screen="modal" hidden>
                                    <div class="lp-modal-preview-box">
                                        <div class="lp-modal-preview-icon"><i class="ti ti-circle-check-filled"></i></div>
                                        <h4 class="lp-modal-preview-title" data-preview="modal-title"><?= esc($lp['modal_title']) ?></h4>
                                        <p class="lp-modal-preview-desc" data-preview="modal-desc"><?= esc($lp['modal_desc']) ?></p>
                                        <div class="lp-modal-preview-btn">
                                            <i class="ti ti-brand-whatsapp"></i>
                                            <span data-preview="modal-button-text"><?= esc($lp['modal_button_text']) ?></span>
                                        </div>
                                        <div class="lp-modal-preview-tip">
                                            <i class="ti ti-info-circle"></i>
                                            <span>Você será redirecionado para o WhatsApp</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Barra Home Indicator do iOS -->
                            <div class="mobile-home-indicator"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barra Flutuante Salvar/Cancelar -->
            <?php if (\App\Libraries\UserPermissions::hasPermission('landing_leads', 'edit')): ?>
            <div class="save-bar" data-landing-save-bar hidden aria-hidden="true">
                <p>
                    <strong>Alterações não salvas na Landing Page</strong>
                    <span>Salve para publicar as alterações na página pública de leads.</span>
                </p>
                <div class="save-actions">
                    <button class="button secondary" type="button" data-cancel-landing>Cancelar</button>
                    <button class="button primary" type="submit"><i class="ti ti-device-floppy" aria-hidden="true"></i>Salvar Alterações</button>
                </div>
            </div>
            <?php endif; ?>
        </form>
    </div>
    <?php endif; ?>

    <!-- Modal de Recorte de Imagem (Cropper) -->
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
