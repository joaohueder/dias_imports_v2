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