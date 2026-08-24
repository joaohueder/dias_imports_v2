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