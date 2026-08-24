<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$counts = [
    'total' => count($products),
    'active' => count(array_filter($products, fn($p) => $p->active)),
    'inactive' => count(array_filter($products, fn($p) => !$p->active)),
];
?>
<section class="users-view-container" data-products-module>
    <!-- Top toolbar -->
    <div class="users-header-actions">
        <div class="users-search-box">
            <i class="ti ti-search users-search-icon" aria-hidden="true"></i>
            <input type="text" id="products-search-input" class="users-search-input" placeholder="Filtrar por nome..." aria-label="Filtrar produtos por nome" data-products-filter-input>
        </div>

        <div class="users-filter-pills" role="tablist" aria-label="Filtros de status de produtos">
            <button type="button" class="filter-pill active" data-filter="all" role="tab" aria-selected="true">
                Todos <span class="pill-badge"><?= $counts['total'] ?></span>
            </button>
            <button type="button" class="filter-pill" data-filter="active" role="tab" aria-selected="false">
                Ativos <span class="pill-badge"><?= $counts['active'] ?></span>
            </button>
            <button type="button" class="filter-pill" data-filter="inactive" role="tab" aria-selected="false">
                Inativos <span class="pill-badge"><?= $counts['inactive'] ?></span>
            </button>
        </div>

        <?php if (\App\Libraries\UserPermissions::hasPermission('products', 'create')): ?>
        <div class="users-create-action">
            <a href="<?= site_url('produtos/novo') ?>" class="button primary">
                <i class="ti ti-plus" aria-hidden="true"></i>
                <span>Novo Produto</span>
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Cards Grid -->
    <div class="users-grid products-grid" data-products-grid>
        <?= view('admin/products/_cards', [
            'products' => $products,
            'isSendJobActive' => $isSendJobActive,
            'messageTemplates' => $messageTemplates,
        ]) ?>
    </div>

    <!-- Empty search state -->
    <div class="users-empty-search col-span-full" data-products-empty style="display: none; grid-column: 1 / -1; width: 100%; padding: 60px 20px; text-align: center; flex-direction: column; align-items: center; justify-content: center; gap: 8px; color: rgb(var(--muted));">
        <div class="empty-icon" style="width: 64px; height: 64px; border-radius: 16px; background: rgb(var(--surface-secondary)); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 8px;">
            <i class="ti ti-inbox" style="font-size: 32px; color: rgb(var(--muted));"></i>
        </div>
        <p style="margin: 0; font-size: 15px; font-weight: 600; color: rgb(var(--foreground));">Nenhum produto encontrado.</p>
        <span style="font-size: 14px; margin-bottom: 12px;">Tente ajustar a busca ou o status selecionado.</span>
        <button type="button" class="button secondary" data-clear-filters style="margin-top: 4px;">
            <i class="ti ti-filter-off" aria-hidden="true"></i>
            <span>Limpar Filtros</span>
        </button>
    </div>

    <!-- Modal de Envio para Grupos de WhatsApp -->
    <div id="modal-send-product" class="template-dialog" hidden aria-hidden="true" style="display: none;">
        <div class="template-dialog-card" style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border)); border-radius: 16px; width: 100%; max-width: 680px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 20px 40px -15px rgba(0,0,0,0.3); overflow: hidden; margin: auto;">
            <!-- Header do Modal -->
            <div style="padding: 20px 24px; border-bottom: 1px solid rgb(var(--border)); display: flex; align-items: center; justify-content: space-between; background: rgb(var(--surface-secondary));">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(37, 211, 102, 0.15); color: #25d366; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                        <i class="ti ti-brand-whatsapp"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 17px; font-weight: 700; color: rgb(var(--foreground));">Disparar Produto no WhatsApp</h3>
                        <p id="send-modal-product-title" style="margin: 2px 0 0; font-size: 13px; color: rgb(var(--muted));">Selecione o modelo e os grupos de destino</p>
                    </div>
                </div>
                <button type="button" onclick="closeSendModal()" style="background: none; border: none; font-size: 20px; color: rgb(var(--muted)); cursor: pointer; padding: 4px; border-radius: 6px; display: flex; align-items: center; justify-content: center;" title="Fechar">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <!-- Corpo do Modal com Scroll -->
            <form id="form-send-product-groups" style="padding: 20px 24px; overflow-y: auto; display: flex; flex-direction: column; gap: 20px; flex: 1;">
                <?= csrf_field() ?>
                <input type="hidden" id="send-product-id" name="product_id" value="">

                <!-- Opção de Modelo de Mensagem -->
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-size: 13px; font-weight: 600; color: rgb(var(--foreground)); display: flex; align-items: center; gap: 6px;">
                        <i class="ti ti-message-2" style="color: var(--primary, #6366f1);"></i>
                        <span>Modelo de Mensagem</span>
                    </label>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <label style="display: flex; align-items: center; gap: 10px; padding: 12px 14px; border: 1px solid rgb(var(--border)); border-radius: 10px; cursor: pointer; background: rgb(var(--surface-secondary)); transition: all 0.2s;" id="label-mode-random">
                            <input type="radio" name="template_mode" value="random" checked onchange="toggleTemplateMode('random')" style="accent-color: #6366f1;">
                            <div style="display: flex; flex-direction: column;">
                                <strong style="font-size: 13px; color: rgb(var(--foreground));">Aleatório</strong>
                                <span style="font-size: 11px; color: rgb(var(--muted));">Sorteia entre os modelos ativos</span>
                            </div>
                        </label>

                        <label style="display: flex; align-items: center; gap: 10px; padding: 12px 14px; border: 1px solid rgb(var(--border)); border-radius: 10px; cursor: pointer; background: rgb(var(--surface-secondary)); transition: all 0.2s;" id="label-mode-specific">
                            <input type="radio" name="template_mode" value="specific" onchange="toggleTemplateMode('specific')" style="accent-color: #6366f1;">
                            <div style="display: flex; flex-direction: column;">
                                <strong style="font-size: 13px; color: rgb(var(--foreground));">Escolher Modelo</strong>
                                <span style="font-size: 11px; color: rgb(var(--muted));">Selecionar modelo específico</span>
                            </div>
                        </label>
                    </div>

                    <!-- Select do Modelo Específico (oculto por padrão) -->
                    <div id="wrapper-specific-template" style="display: none; margin-top: 6px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 14px; max-height: 380px; overflow-y: auto; padding: 4px;">
                            <?php if (empty($messageTemplates)): ?>
                                <div style="grid-column: 1 / -1; padding: 16px; text-align: center; background: rgb(var(--surface-secondary)); border: 1px dashed rgb(var(--border)); border-radius: 10px; color: rgb(var(--muted)); font-size: 13px;">
                                    Nenhum modelo de mensagem ativo cadastrado.
                                </div>
                            <?php else: ?>
                                <?php foreach ($messageTemplates as $tpl): ?>
                                    <label class="iphone-card-select" style="display: flex; flex-direction: column; border: 2px solid rgb(var(--border)); border-radius: 24px; cursor: pointer; background: #0b141a; transition: all 0.2s; overflow: hidden; position: relative; box-shadow: 0 8px 24px rgba(0,0,0,0.3);">
                                        <input type="radio" name="template_id" value="<?= esc($tpl['id']) ?>" style="position: absolute; top: 14px; right: 14px; accent-color: #6366f1; width: 18px; height: 18px; z-index: 10;">
                                        
                                        <!-- Header estilo iPhone -->
                                        <div style="background: #1f2c34; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06);">
                                            <div style="display: flex; align-items: center; gap: 8px; max-width: calc(100% - 30px);">
                                                <div style="width: 24px; height: 24px; border-radius: 50%; background: #00a884; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">
                                                    <i class="ti ti-brand-whatsapp"></i>
                                                </div>
                                                <div style="overflow: hidden;">
                                                    <strong style="font-size: 12px; color: #e9edef; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= esc($tpl['name']) ?></strong>
                                                    <span style="font-size: 10px; color: #8696a0;"><?= (int)$tpl['send_count'] ?> envios</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Corpo do Chat WhatsApp estilo iPhone -->
                                        <div style="padding: 12px; background: #0b141a; min-height: 140px; display: flex; flex-direction: column; justify-content: flex-start; gap: 6px;">
                                            <div style="align-self: center; background: #182229; color: #8696a0; font-size: 9px; font-weight: 600; padding: 2px 8px; border-radius: 6px; text-transform: uppercase;">Hoje</div>

                                            <div style="background: #005c4b; color: #e9edef; padding: 8px 10px; border-radius: 10px; border-top-right-radius: 2px; font-size: 11px; line-height: 1.4; position: relative; box-shadow: 0 1px 0.5px rgba(0,0,0,0.15); width: 100%;">
                                                <div data-template-id-text="<?= esc($tpl['id']) ?>" style="white-space: pre-wrap; word-break: break-word; font-family: inherit;"><?= esc($tpl['content']) ?></div>
                                                <div style="display: flex; justify-content: flex-end; align-items: center; gap: 3px; margin-top: 4px; font-size: 9px; color: rgba(255,255,255,0.6);">
                                                    <span>09:41</span>
                                                    <i class="ti ti-checks" style="color: #53bdeb; font-size: 13px;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Seleção dos Grupos do WhatsApp -->
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <label style="font-size: 13px; font-weight: 600; color: rgb(var(--foreground)); display: flex; align-items: center; gap: 6px;">
                            <i class="ti ti-users-group" style="color: #25d366;"></i>
                            <span>Grupos de Destino (Ativos)</span>
                        </label>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" onclick="selectAllGroups(true)" style="background: none; border: none; color: var(--primary, #6366f1); font-size: 12px; font-weight: 600; cursor: pointer; padding: 0;">Marcar todos</button>
                            <span style="color: rgb(var(--border));">•</span>
                            <button type="button" onclick="selectAllGroups(false)" style="background: none; border: none; color: rgb(var(--muted)); font-size: 12px; font-weight: 500; cursor: pointer; padding: 0;">Desmarcar</button>
                        </div>
                    </div>

                    <?php if (empty($activeGroups)): ?>
                        <div style="padding: 24px; text-align: center; background: rgb(var(--surface-secondary)); border: 1px dashed rgb(var(--border)); border-radius: 10px; color: rgb(var(--muted)); font-size: 13px;">
                            <i class="ti ti-users-minus" style="font-size: 28px; display: block; margin-bottom: 6px; color: rgb(var(--muted));"></i>
                            Nenhum grupo ativo encontrado no WhatsApp. Ative grupos em <strong>Grupos do WhatsApp</strong>.
                        </div>
                    <?php else: ?>
                        <div style="max-height: 200px; overflow-y: auto; border: 1px solid rgb(var(--border)); border-radius: 10px; padding: 8px; background: rgb(var(--surface)); display: flex; flex-direction: column; gap: 6px;">
                            <?php foreach ($activeGroups as $grp): ?>
                                <label style="display: flex; align-items: center; gap: 10px; padding: 8px 12px; border-radius: 8px; cursor: pointer; transition: background 0.15s; background: rgb(var(--surface-secondary));" class="group-select-item">
                                    <input type="checkbox" name="group_ids[]" value="<?= esc($grp['id']) ?>" class="group-checkbox" checked style="accent-color: #25d366; width: 16px; height: 16px; cursor: pointer;">
                                    <?php if (!empty($grp['avatar_url'])): ?>
                                        <img src="<?= esc($grp['avatar_url']) ?>" alt="Avatar" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;">
                                    <?php else: ?>
                                        <div style="width: 28px; height: 28px; border-radius: 50%; background: rgba(37, 211, 102, 0.15); color: #25d366; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                            <i class="ti ti-users"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div style="display: flex; flex-direction: column; overflow: hidden; flex: 1;">
                                        <span style="font-size: 13px; font-weight: 600; color: rgb(var(--foreground)); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= esc($grp['name']) ?></span>
                                        <span style="font-size: 11px; color: rgb(var(--muted));"><?= (int)($grp['participants_count'] ?? 0) ?> membros</span>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div style="padding: 12px 14px; border-radius: 8px; background: rgba(99, 102, 241, 0.08); border: 1px solid rgba(99, 102, 241, 0.2); font-size: 12px; color: rgb(var(--foreground)); display: flex; align-items: flex-start; gap: 8px;">
                    <i class="ti ti-info-circle" style="color: #6366f1; font-size: 16px; margin-top: 1px;"></i>
                    <span>Ao confirmar, os disparos serão adicionados à <strong>Fila da Central de Trabalho</strong> e executados com o intervalo anti-bloqueio configurado.</span>
                </div>
            </form>

            <!-- Rodapé do Modal -->
            <div style="padding: 16px 24px; border-top: 1px solid rgb(var(--border)); display: flex; align-items: center; justify-content: flex-end; gap: 10px; background: rgb(var(--surface-secondary));">
                <button type="button" class="button secondary" onclick="closeSendModal()">Cancelar</button>
                <button type="button" class="button primary" id="btn-submit-send-groups" onclick="submitSendProduct()" <?= empty($activeGroups) ? 'disabled' : '' ?>>
                    <i class="ti ti-send"></i>
                    <span>Enviar para a Fila</span>
                </button>
            </div>
        </div>
    </div>
</section>

<style>
.iphone-card-select {
    border-color: rgba(255,255,255,0.08);
}
.iphone-card-select:hover {
    border-color: rgba(99, 102, 241, 0.4);
    transform: translateY(-2px);
}
.iphone-card-select:has(input:checked) {
    border-color: #00a884 !important;
    box-shadow: 0 0 0 2px #00a884, 0 10px 25px rgba(0, 168, 132, 0.2) !important;
}
</style>

<script>
const productsData = <?= json_encode(array_map(function($p) {
    return [
        'id' => $p->id,
        'name' => $p->name,
        'description' => $p->description ?? '',
        'regular_price' => number_format((float)($p->regular_price ?? 0), 2, ',', '.'),
        'promotional_price' => number_format((float)($p->promotional_price ?? 0), 2, ',', '.'),
        'discount_percentage' => (int)($p->discount_percentage ?? 0),
        'slug' => $p->slug ?? '',
        'url' => site_url('p/' . ($p->slug ?? '')),
    ];
}, $products), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

const messageTemplatesRaw = <?= json_encode($messageTemplates, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('[data-products-filter-input]');
    const filterPills = document.querySelectorAll('.filter-pill');
    const productCards = document.querySelectorAll('[data-product-card]');
    const emptyState = document.querySelector('[data-products-empty]');
    const clearFiltersBtn = document.querySelector('[data-clear-filters]');
    
    let currentFilter = 'all';
    let currentSearch = '';

    function applyFilters() {
        let visibleCount = 0;
        
        productCards.forEach(card => {
            const name = card.dataset.name || '';
            const status = card.dataset.status || '';
            
            const matchesSearch = currentSearch === '' || name.includes(currentSearch);
            const matchesStatus = currentFilter === 'all' || status === currentFilter;
            
            if (matchesSearch && matchesStatus) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'flex' : 'none';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            currentSearch = e.target.value.toLowerCase().trim();
            applyFilters();
        });
    }

    filterPills.forEach(pill => {
        pill.addEventListener('click', () => {
            filterPills.forEach(p => {
                p.classList.remove('active');
                p.setAttribute('aria-selected', 'false');
            });
            pill.classList.add('active');
            pill.setAttribute('aria-selected', 'true');
            currentFilter = pill.dataset.filter;
            applyFilters();
        });
    });

    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            currentSearch = '';
            
            const allPill = document.querySelector('.filter-pill[data-filter="all"]');
            if (allPill) {
                allPill.click();
            } else {
                currentFilter = 'all';
                applyFilters();
            }
        });
    }
});

function formatTemplateContent(content, product) {
    if (!content || !product) return content || '';
    return content
        .replaceAll('{{nome}}', product.name || '')
        .replaceAll('{{descricao}}', product.description || '')
        .replaceAll('{{preco}}', 'R$ ' + (product.regular_price || '0,00'))
        .replaceAll('{{preco_promocional}}', 'R$ ' + (product.promotional_price || '0,00'))
        .replaceAll('{{desconto}}', (product.discount_percentage || '0') + '%')
        .replaceAll('{{link}}', product.url || '');
}

function openSendModal(productId, productName) {
    const idInput = document.getElementById('send-product-id');
    const titleElem = document.getElementById('send-modal-product-title');
    const modal = document.getElementById('modal-send-product');
    
    if (idInput) idInput.value = productId;
    if (titleElem) titleElem.textContent = `Produto: ${productName}`;

    const prod = productsData.find(p => p.id == productId);
    if (prod) {
        document.querySelectorAll('[data-template-id-text]').forEach(elem => {
            const tplId = elem.dataset.templateIdText;
            const tpl = messageTemplatesRaw.find(t => t.id == tplId);
            if (tpl) {
                elem.textContent = formatTemplateContent(tpl.content, prod);
            }
        });
    }

    if (modal) {
        modal.hidden = false;
        modal.removeAttribute('hidden');
        modal.setAttribute('aria-hidden', 'false');
        modal.style.setProperty('display', 'grid', 'important');
        requestAnimationFrame(() => modal.classList.add('open'));
    }
}

function closeSendModal() {
    const modal = document.getElementById('modal-send-product');
    if (modal) {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        setTimeout(() => {
            if (!modal.classList.contains('open')) {
                modal.hidden = true;
                modal.setAttribute('hidden', 'true');
                modal.style.setProperty('display', 'none', 'important');
            }
        }, 200);
    }
}

function toggleTemplateMode(mode) {
    const specificWrapper = document.getElementById('wrapper-specific-template');
    if (mode === 'specific') {
        specificWrapper.style.display = 'block';
    } else {
        specificWrapper.style.display = 'none';
    }
}

function selectAllGroups(checked) {
    document.querySelectorAll('.group-checkbox').forEach(cb => cb.checked = checked);
}

function submitSendProduct() {
    const form = document.getElementById('form-send-product-groups');
    const checkedGroups = document.querySelectorAll('.group-checkbox:checked');
    if (checkedGroups.length === 0) {
        alert('Selecione pelo menos um grupo de WhatsApp.');
        return;
    }

    const btn = document.getElementById('btn-submit-send-groups');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader rotate"></i><span>Enfileirando...</span>';

    const csrfTokenName = document.querySelector('meta[name="csrf-token-name"]')?.getAttribute('content') || '<?= csrf_token() ?>';
    const csrfHash = document.querySelector('meta[name="csrf-hash"]')?.getAttribute('content') || '<?= csrf_hash() ?>';

    const formData = new FormData(form);
    if (!formData.has(csrfTokenName)) {
        formData.append(csrfTokenName, csrfHash);
    }

    fetch('<?= site_url('produtos/enviar-grupos') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        if (data.success) {
            closeSendModal();
            if (window.Toast) {
                Toast.success(data.message);
            } else {
                alert(data.message);
            }
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            alert(data.message || 'Erro ao enfileirar disparos.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        alert('Falha na comunicação com o servidor: ' + err);
    });
}

function toggleProductStatus(id) {
    const csrfTokenName = document.querySelector('meta[name="csrf-token-name"]')?.getAttribute('content') || '<?= csrf_token() ?>';
    const csrfHash = document.querySelector('meta[name="csrf-hash"]')?.getAttribute('content') || '<?= csrf_hash() ?>';

    const formData = new FormData();
    formData.append(csrfTokenName, csrfHash);

    fetch(`<?= site_url('produtos') ?>/${id}/status`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Erro ao alterar status.');
        }
    })
    .catch(() => {
        window.location.reload();
    });
}
</script>
<?= $this->endSection() ?>