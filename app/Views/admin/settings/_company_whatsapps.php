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