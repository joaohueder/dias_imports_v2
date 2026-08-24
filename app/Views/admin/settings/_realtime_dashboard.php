<!-- Partial para Realtime Dashboard & Worker Status (Tempo Real) -->
<!-- Endpoints de Execução do Worker de Realtime (Cron Job / Webcron) -->
<div class="webcron-card" style="margin-bottom: 24px;">
    <div class="webcron-header">
        <div class="webcron-icon"><i class="ti ti-activity"></i></div>
        <div class="webcron-title-group">
            <h3>Comando de Execução em Segundo Plano (Cron Job / Servidor)</h3>
            <p>Configure este comando no agendador de tarefas / Cron Job da hospedagem (Hostinger, cPanel) para processar o realtime com conexão única e contínua.</p>
        </div>
    </div>
    
    <div class="webcron-items-grid">
        <div class="webcron-item">
            <div class="webcron-item-header">
                <span class="webcron-item-label">Comando para Worker Realtime (cron-realtime)</span>
                <span class="webcron-badge runner"><i class="ti ti-clock"></i> A cada 5 minutos</span>
            </div>
            <?php 
                $realtimeUrl = base_url('cron-realtime.php') . '?token=' . esc(env('app.cronToken') ?: 'dias_imports_cron_secret_2026');
                $realtimeCmd = 'wget -O /dev/null ' . $realtimeUrl;
            ?>
            <div class="webcron-input-group">
                <div class="webcron-url-text" id="setting-cron-realtime-url" style="font-family: monospace; font-size: 12px;"><?= esc($realtimeCmd) ?></div>
                <button class="button secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('setting-cron-realtime-url').textContent.trim()); alert('Comando cron-realtime copiado!');">
                    <i class="ti ti-copy" aria-hidden="true"></i>
                    <span>Copiar</span>
                </button>
            </div>
            <p class="webcron-hint">
                <i class="ti ti-info-circle"></i>
                Mantém <strong>apenas 1 conexão MySQL</strong> aberta processando ciclos a cada 5s por 290s. Cole este comando exato no Cron da Hostinger <strong>a cada 5 minutos</strong>.
            </p>
        </div>
    </div>
</div>

<!-- Mini Dashboard de Diagnóstico e Saúde do Realtime -->
<?php 
    $wStatus = $realtimeWorkerStatus ?? [];
    $isOnline = !empty($wStatus['is_online']);
    $lastHeartbeat = $wStatus['last_heartbeat'] ?? 'Nunca executado';
    $secondsAgo = $wStatus['seconds_ago'] ?? null;
    $cycle = $wStatus['cycle'] ?? 0;
    $jobsProc = $wStatus['jobs_processed'] ?? 0;
    $jobsFail = $wStatus['jobs_failed'] ?? 0;
    $lastError = $wStatus['last_error'] ?? null;
    $snapshots = $wStatus['snapshots'] ?? [];
    $recentErrors = $wStatus['recent_errors'] ?? [];
?>
<div class="webcron-card" style="margin-bottom: 24px;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
        <div class="webcron-header" style="margin: 0;">
            <div class="webcron-icon" style="<?= $isOnline ? 'background: rgb(var(--success) / .12); color: rgb(var(--success)); border-color: rgb(var(--success) / .28);' : 'background: rgb(var(--danger) / .12); color: rgb(var(--danger)); border-color: rgb(var(--danger) / .28);' ?>">
                <i class="ti <?= $isOnline ? 'ti-pulse' : 'ti-alert-circle' ?>"></i>
            </div>
            <div class="webcron-title-group">
                <h3>Status do Worker Realtime</h3>
                <p>Diagnóstico em tempo real da saúde dos snapshots e ciclos</p>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <?php if ($isOnline): ?>
                <span class="webcron-badge" style="background: rgb(var(--success) / .14); color: rgb(var(--success)); border: 1px solid rgb(var(--success) / .28); font-size: 12px; padding: 6px 14px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: rgb(var(--success)); box-shadow: 0 0 8px rgb(var(--success));"></span>
                    ONLINE (Ciclo ativo há <?= $secondsAgo !== null ? $secondsAgo . 's' : '0s' ?>)
                </span>
                <?php if (\App\Libraries\UserPermissions::hasPermission('realtime', 'edit')): ?>
                    <form action="<?= site_url('configuracoes/tempo-real/parar') ?>" method="post" onsubmit="return confirm('Deseja enviar o sinal para parar o Worker Realtime no próximo ciclo?');" style="margin: 0;">
                        <?= csrf_field() ?>
                        <button type="submit" class="button secondary" style="padding: 6px 12px; font-size: 12px; color: rgb(var(--foreground)); border-color: rgb(var(--border)); background: rgb(var(--card));" title="Para no próximo ciclo de 5 segundos">
                            <i class="ti ti-player-pause" aria-hidden="true"></i> Parar
                        </button>
                    </form>
                    <form action="<?= site_url('configuracoes/tempo-real/forcar-parar') ?>" method="post" onsubmit="return confirm('ATENÇÃO: Deseja forçar o encerramento imediato (Kill Process) do worker no servidor e liberar todas as travas?');" style="margin: 0;">
                        <?= csrf_field() ?>
                        <button type="submit" class="button secondary" style="padding: 6px 12px; font-size: 12px; color: rgb(var(--danger)); border-color: rgb(var(--danger) / .3); background: rgb(var(--danger) / .08);" title="Mata o processo imediatamente via SO e remove arquivos lock">
                            <i class="ti ti-flame" aria-hidden="true"></i> Forçar Fechamento (Kill)
                        </button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <span class="webcron-badge" style="background: rgb(var(--danger) / .14); color: rgb(var(--danger)); border: 1px solid rgb(var(--danger) / .28); font-size: 12px; padding: 6px 14px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: rgb(var(--danger));"></span>
                    OFFLINE / PARADO
                </span>
                <?php if (\App\Libraries\UserPermissions::hasPermission('realtime', 'edit')): ?>
                    <form action="<?= site_url('configuracoes/tempo-real/iniciar') ?>" method="post" style="margin: 0;">
                        <?= csrf_field() ?>
                        <button type="submit" class="button primary" style="padding: 6px 14px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="ti ti-player-play" aria-hidden="true"></i> Ativar Worker no Servidor
                        </button>
                    </form>
                    <form action="<?= site_url('configuracoes/tempo-real/forcar-parar') ?>" method="post" onsubmit="return confirm('Deseja limpar travas e garantir que qualquer processo residual do cron-realtime seja finalizado?');" style="margin: 0;">
                        <?= csrf_field() ?>
                        <button type="submit" class="button secondary" style="padding: 6px 12px; font-size: 12px; color: rgb(var(--muted)); border-color: rgb(var(--border)); background: transparent;" title="Garante que não há nenhum processo residual em execução e remove travas de lock">
                            <i class="ti ti-trash" aria-hidden="true"></i> Limpar Travas/Kill
                        </button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mini KPIs de Saúde -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">
        <div class="webcron-item" style="padding: 14px 16px; margin: 0; gap: 4px;">
            <div style="font-size: 11px; font-weight: 700; color: rgb(var(--muted)); text-transform: uppercase; letter-spacing: .05em;">Último Batimento</div>
            <div style="font-size: 14px; font-weight: 700; color: rgb(var(--foreground));"><?= esc($lastHeartbeat) ?></div>
        </div>
        <div class="webcron-item" style="padding: 14px 16px; margin: 0; gap: 4px;">
            <div style="font-size: 11px; font-weight: 700; color: rgb(var(--muted)); text-transform: uppercase; letter-spacing: .05em;">Tarefas Processadas</div>
            <div style="font-size: 14px; font-weight: 700; color: rgb(var(--success));"><?= $jobsProc ?> executadas</div>
        </div>
        <div class="webcron-item" style="padding: 14px 16px; margin: 0; gap: 4px;">
            <div style="font-size: 11px; font-weight: 700; color: rgb(var(--muted)); text-transform: uppercase; letter-spacing: .05em;">Falhas de Tarefas</div>
            <div style="font-size: 14px; font-weight: 700; color: <?= $jobsFail > 0 ? 'rgb(var(--danger))' : 'rgb(var(--foreground))' ?>;"><?= $jobsFail ?> falhas</div>
        </div>
        <div class="webcron-item" style="padding: 14px 16px; margin: 0; gap: 4px; <?= !empty($lastError) ? 'border-color: rgb(var(--danger) / .3); background: rgb(var(--danger) / .08);' : '' ?>">
            <div style="font-size: 11px; font-weight: 700; color: rgb(var(--muted)); text-transform: uppercase; letter-spacing: .05em;">Integridade do Ciclo</div>
            <div style="font-size: 14px; font-weight: 700; color: <?= !empty($lastError) ? 'rgb(var(--danger))' : 'rgb(var(--success))' ?>;">
                <?= !empty($lastError) ? 'Erro detectado' : 'Tudo Operacional' ?>
            </div>
        </div>
    </div>

    <!-- Grade de Snapshots das Telas -->
    <div style="display: flex; flex-direction: column; gap: 12px;">
        <div style="font-size: 12px; font-weight: 700; color: rgb(var(--muted)); text-transform: uppercase; letter-spacing: .06em;">Snapshots Gerados em Disco</div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 10px;">
            <?php 
            $screenTitles = [
                'overview' => 'Visão Geral',
                'whatsapp_groups' => 'Grupos WhatsApp',
                'job_center' => 'Central de Trabalho',
                'products' => 'Catálogo Produtos',
                'vip_leads' => 'Leads VIP',
                'users' => 'Usuários',
                'settings_company' => 'Config. Empresa',
                'settings_templates' => 'Modelos Mensagens',
            ];
            foreach ($screenTitles as $sKey => $sTitle): 
                $snap = $snapshots[$sKey] ?? ['exists' => false];
                $snapExists = !empty($snap['exists']);
            ?>
                <div class="webcron-item" style="padding: 10px 14px; flex-direction: row; align-items: center; justify-content: space-between; margin: 0;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: <?= $snapExists ? 'rgb(var(--success))' : 'rgb(var(--muted))' ?>; <?= $snapExists ? 'box-shadow: 0 0 6px rgb(var(--success) / .6);' : '' ?>"></span>
                        <span style="font-size: 13px; font-weight: 600; color: rgb(var(--foreground));"><?= esc($sTitle) ?></span>
                    </div>
                    <div>
                        <?php if ($snapExists): ?>
                            <span style="font-size: 12px; color: rgb(var(--muted)); font-weight: 500;" title="Atualizado em <?= esc($snap['updated_at']) ?>">
                                <?= $snap['seconds_ago'] !== null ? $snap['seconds_ago'] . 's atrás' : 'OK' ?>
                            </span>
                        <?php else: ?>
                            <span style="font-size: 12px; color: rgb(var(--danger)); font-weight: 600;">Aguardando cron</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Últimos Erros Se Existirem -->
    <?php if (!empty($lastError) || !empty($recentErrors)): ?>
        <div class="webcron-item" style="background: rgb(var(--danger) / .08); border-color: rgb(var(--danger) / .3); padding: 14px 16px; margin: 0; gap: 6px;">
            <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; color: rgb(var(--danger));">
                <i class="ti ti-alert-triangle"></i>
                <span>Últimos Erros Registrados:</span>
            </div>
            <?php if (!empty($lastError)): ?>
                <div style="font-size: 12px; font-family: monospace; color: rgb(var(--danger)); margin-bottom: 2px;">
                    <strong>Último ciclo:</strong> <?= esc($lastError) ?>
                </div>
            <?php endif; ?>
            <?php foreach ($recentErrors as $errLine): ?>
                <div style="font-size: 11px; font-family: monospace; color: rgb(var(--muted)); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <?= esc($errLine) ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
