<?php if (empty($leads)): ?>
    <tr data-leads-empty-server-row>
        <td colspan="5" class="lead-empty-row">
            <div class="lead-empty-content">
                <i class="ti ti-inbox" aria-hidden="true"></i>
                <p>Nenhum contato encontrado para os filtros selecionados.</p>
                <span>Novos contatos captados pela Landing Page aparecerão aqui automaticamente.</span>
                <button type="button" class="button secondary" data-leads-clear-filters style="margin-top: 8px;">
                    <i class="ti ti-filter-off" aria-hidden="true"></i>
                    <span>Limpar Filtros</span>
                </button>
            </div>
        </td>
    </tr>
<?php else: ?>
    <?php foreach ($leads as $lead): ?>
        <?php
            $createdAt = !empty($lead['created_at']) ? strtotime($lead['created_at']) : null;
            $formattedDate = $createdAt ? date('d/m/Y', $createdAt) : '--/--/----';
            $formattedTime = $createdAt ? date('H:i', $createdAt) : '--:--';
            $isToday = $createdAt && date('Y-m-d', $createdAt) === date('Y-m-d');

            // Formatação do WhatsApp
            $phoneClean = preg_replace('/\D+/', '', $lead['phone'] ?? '');
            $phoneDisplay = $lead['phone'];
            if (strlen($phoneClean) === 11) {
                $phoneDisplay = '(' . substr($phoneClean, 0, 2) . ') ' . substr($phoneClean, 2, 5) . '-' . substr($phoneClean, 7);
            } elseif (strlen($phoneClean) === 10) {
                $phoneDisplay = '(' . substr($phoneClean, 0, 2) . ') ' . substr($phoneClean, 2, 4) . '-' . substr($phoneClean, 6);
            } elseif (strlen($phoneClean) === 13 && substr($phoneClean, 0, 2) === '55') {
                $phoneDisplay = '+' . substr($phoneClean, 0, 2) . ' (' . substr($phoneClean, 2, 2) . ') ' . substr($phoneClean, 4, 5) . '-' . substr($phoneClean, 9);
            }

            // WhatsApp link direto
            $waLink = 'https://wa.me/' . (str_starts_with($phoneClean, '55') ? $phoneClean : '55' . $phoneClean);

            // Origem / Dispositivo
            $ua = $lead['user_agent'] ?? '';
            $originDevice = 'Desktop';
            $originIcon = 'ti-device-laptop';
            if (stripos($ua, 'Mobile') !== false || stripos($ua, 'Android') !== false || stripos($ua, 'iPhone') !== false) {
                $originDevice = 'Mobile';
                $originIcon = 'ti-device-mobile';
            }
        ?>
        <tr class="lead-row" data-lead-row data-id="<?= esc($lead['id']) ?>"
            data-name="<?= esc(mb_strtolower($lead['name'])) ?>"
            data-phone="<?= esc(preg_replace('/\D+/', '', $lead['phone'])) ?>"
            data-date="<?= esc($createdAt ? date('Y-m-d', $createdAt) : '') ?>">
            <!-- DATA / HORA -->
            <td>
                <div class="lead-date-cell">
                    <span class="lead-date-primary"><?= esc($formattedDate) ?></span>
                    <span class="lead-date-secondary"><i class="ti ti-clock" aria-hidden="true"></i> <?= esc($formattedTime) ?></span>
                </div>
            </td>

            <!-- CONTATO -->
            <td>
                <div class="lead-contact-cell">
                    <div class="lead-avatar-circle" aria-hidden="true">
                        <?= esc(mb_strtoupper(mb_substr(trim($lead['name']), 0, 1))) ?>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 2px;">
                        <span class="lead-name-txt"><?= esc($lead['name']) ?></span>
                        <?php if ($isToday): ?>
                            <span style="font-size: 10px; font-weight: 700; color: #10b981;">Captado hoje</span>
                        <?php endif; ?>
                    </div>
                </div>
            </td>

            <!-- WHATSAPP -->
            <td>
                <a href="<?= esc($waLink) ?>" target="_blank" rel="noopener noreferrer" class="lead-wa-link" title="Abrir WhatsApp com <?= esc($lead['name']) ?>">
                    <i class="ti ti-brand-whatsapp" aria-hidden="true"></i>
                    <span><?= esc($phoneDisplay) ?></span>
                </a>
            </td>

            <!-- ORIGEM -->
            <td>
                <span class="lead-origin-badge landing" title="Dispositivo: <?= esc($originDevice) ?>">
                    <i class="ti <?= esc($originIcon) ?>" aria-hidden="true"></i>
                    <span>Landing VIP</span>
                </span>
            </td>

            <!-- AÇÕES -->
            <td>
                <div class="lead-actions-cell">
                    <?php if (\App\Libraries\UserPermissions::hasPermission('vip_leads', 'edit')): ?>
                        <button type="button" class="btn-lead-action" data-edit-lead data-id="<?= esc($lead['id']) ?>" data-name="<?= esc($lead['name']) ?>" data-phone="<?= esc($lead['phone']) ?>" title="Editar contato">
                            <i class="ti ti-pencil" aria-hidden="true"></i>
                        </button>
                    <?php endif; ?>

                    <?php if (\App\Libraries\UserPermissions::hasPermission('vip_leads', 'delete')): ?>
                        <form action="<?= site_url('leads-vip/' . $lead['id'] . '/excluir') ?>" method="post" data-confirm-action="lead-delete" data-action-name="<?= esc($lead['name']) ?>" style="margin: 0;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-lead-action action-danger" title="Excluir contato">
                                <i class="ti ti-trash" aria-hidden="true"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
