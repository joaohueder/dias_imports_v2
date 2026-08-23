<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$isEdit = !empty($user);
$formAction = $isEdit ? site_url('usuarios/' . $user['id'] . '/editar') : site_url('usuarios/novo');
$userRole = old('role', $user['role'] ?? 'user');
$isActive = (bool) old('is_active', $user['is_active'] ?? 1);
?>

<div class="user-form-container" data-user-form-root>
    <div class="user-form-topbar">
        <a href="<?= site_url('usuarios') ?>" class="back-link-btn">
            <i class="ti ti-chevron-left"></i>
            <span>Usuários</span>
        </a>
    </div>

    <form action="<?= $formAction ?>" method="post" class="user-main-form" data-dirty-user-form id="user-editor-form">
        <?= csrf_field() ?>

        <!-- Bloco 1: Dados da Conta -->
        <section class="form-card-section" aria-labelledby="dados-conta-title">
            <h2 id="dados-conta-title" class="section-card-title">Dados da Conta</h2>

            <div class="form-grid-account">
                <!-- E-mail -->
                <div class="form-group col-full">
                    <label for="email">E-mail *</label>
                    <?php if ($isEdit): ?>
                        <input type="email" id="email" class="form-control" value="<?= esc($user['email']) ?>" readonly disabled>
                        <small class="form-help-text">O e-mail é a identidade do acesso e não pode ser alterado.</small>
                    <?php else: ?>
                        <input type="email" id="email" name="email" class="form-control" value="<?= esc(old('email')) ?>" required placeholder="exemplo@diasimports.com.br" autocomplete="username">
                        <small class="form-help-text">O e-mail será utilizado para entrar no painel.</small>
                    <?php endif; ?>
                </div>

                <!-- Nome Completo -->
                <div class="form-group col-full">
                    <label for="name">Nome Completo *</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= esc(old('name', $user['name'] ?? '')) ?>" required maxlength="120" placeholder="Digite o nome completo" autocomplete="name">
                </div>

                <!-- Senha (apenas criação) -->
                <?php if (! $isEdit): ?>
                    <div class="form-group col-full">
                        <label for="password">Senha Inicial *</label>
                        <div class="input-with-toggle">
                            <input type="password" id="password" name="password" class="form-control" required minlength="6" autocomplete="new-password" placeholder="Mínimo 6 caracteres">
                            <button type="button" class="toggle-pwd-btn" data-toggle-pwd><i class="ti ti-eye"></i></button>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Perfil e Status -->
                <div class="form-group form-group-row col-full">
                    <div class="field-role-select">
                        <label for="role">Perfil</label>
                        <select id="role" name="role" class="form-select" data-role-select <?= ($isSelf && $userRole === 'admin') ? 'readonly disabled' : '' ?>>
                            <option value="user" <?= $userRole === 'user' ? 'selected' : '' ?>>Usuário</option>
                            <option value="admin" <?= $userRole === 'admin' ? 'selected' : '' ?>>Administrador</option>
                        </select>
                        <?php if ($isSelf && $userRole === 'admin'): ?>
                            <input type="hidden" name="role" value="admin">
                        <?php endif; ?>
                    </div>

                    <div class="field-status-checkbox">
                        <label class="custom-checkbox-label">
                            <input type="checkbox" name="is_active" value="1" <?= $isActive ? 'checked' : '' ?> <?= $isSelf ? 'disabled' : '' ?>>
                            <span class="custom-checkbox-box"></span>
                            <span class="checkbox-text">Conta ativa</span>
                        </label>
                        <?php if ($isSelf): ?>
                            <input type="hidden" name="is_active" value="1">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bloco 2: Permissões (Exibido apenas para tipo Usuário / user) -->
        <section class="form-card-section permissions-card-section" aria-labelledby="permissoes-title" data-permissions-container style="<?= $userRole === 'admin' ? 'display: none;' : '' ?>">
            <div class="perm-header-area">
                <h2 id="permissoes-title" class="section-card-title">Permissões</h2>
                <p class="perm-subtitle-info">Visão Geral e Perfil ficam liberados para qualquer conta ativa. Marcar qualquer ação libera automaticamente o acesso de leitura da área.</p>
            </div>

            <div class="permissions-table-wrap">
                <table class="permissions-table">
                    <thead>
                        <tr>
                            <th class="th-area">ÁREA</th>
                            <th class="th-action">VER</th>
                            <th class="th-action">CRIAR</th>
                            <th class="th-action">EDITAR</th>
                            <th class="th-action">EXCLUIR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($permissionGroups as $groupKey => $group): ?>
                            <tr class="tr-group-header">
                                <td colspan="5"><?= esc($group['label']) ?></td>
                            </tr>
                            <?php foreach ($group['modules'] as $modKey => $module): ?>
                                <?php
                                    $hasView = in_array('view', $module['actions'], true);
                                    $hasCreate = in_array('create', $module['actions'], true);
                                    $hasEdit = in_array('edit', $module['actions'], true);
                                    $hasDelete = in_array('delete', $module['actions'], true);

                                    $checkedView = !empty($userPermissions[$modKey]['view']);
                                    $checkedCreate = !empty($userPermissions[$modKey]['create']);
                                    $checkedEdit = !empty($userPermissions[$modKey]['edit']);
                                    $checkedDelete = !empty($userPermissions[$modKey]['delete']);
                                ?>
                                <tr class="tr-module-row" data-module-row="<?= esc($modKey) ?>">
                                    <td class="td-module-name">
                                        <strong><?= esc($module['label']) ?></strong>
                                        <button type="button" class="btn-check-all-module" data-toggle-module-all="<?= esc($modKey) ?>">
                                            Marcar tudo
                                        </button>
                                    </td>

                                    <!-- VER -->
                                    <td class="td-action-check">
                                        <?php if ($hasView): ?>
                                            <label class="table-checkbox">
                                                <input type="checkbox" name="permissions[<?= esc($modKey) ?>][view]" value="1" data-perm-type="view" data-module="<?= esc($modKey) ?>" <?= $checkedView ? 'checked' : '' ?>>
                                                <span class="checkbox-visual"></span>
                                            </label>
                                        <?php else: ?>
                                            <span class="action-disabled">—</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- CRIAR -->
                                    <td class="td-action-check">
                                        <?php if ($hasCreate): ?>
                                            <label class="table-checkbox">
                                                <input type="checkbox" name="permissions[<?= esc($modKey) ?>][create]" value="1" data-perm-type="create" data-module="<?= esc($modKey) ?>" <?= $checkedCreate ? 'checked' : '' ?>>
                                                <span class="checkbox-visual"></span>
                                            </label>
                                        <?php else: ?>
                                            <span class="action-disabled">—</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- EDITAR -->
                                    <td class="td-action-check">
                                        <?php if ($hasEdit): ?>
                                            <label class="table-checkbox">
                                                <input type="checkbox" name="permissions[<?= esc($modKey) ?>][edit]" value="1" data-perm-type="edit" data-module="<?= esc($modKey) ?>" <?= $checkedEdit ? 'checked' : '' ?>>
                                                <span class="checkbox-visual"></span>
                                            </label>
                                        <?php else: ?>
                                            <span class="action-disabled">—</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- EXCLUIR -->
                                    <td class="td-action-check">
                                        <?php if ($hasDelete): ?>
                                            <label class="table-checkbox">
                                                <input type="checkbox" name="permissions[<?= esc($modKey) ?>][delete]" value="1" data-perm-type="delete" data-module="<?= esc($modKey) ?>" <?= $checkedDelete ? 'checked' : '' ?>>
                                                <span class="checkbox-visual"></span>
                                            </label>
                                        <?php else: ?>
                                            <span class="action-disabled">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Barra flutuante Salvar / Cancelar -->
        <div class="user-save-bar save-bar" data-form-save-bar hidden>
            <p>
                <strong>Alterações não salvas</strong>
                <span>Revise as permissões e dados antes de salvar.</span>
            </p>
            <div class="save-actions">
                <button type="button" class="button secondary" data-cancel-form>Cancelar</button>
                <button class="button primary" type="submit">
                    <i class="ti ti-device-floppy" aria-hidden="true"></i>
                    <span>Salvar Alterações</span>
                </button>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
