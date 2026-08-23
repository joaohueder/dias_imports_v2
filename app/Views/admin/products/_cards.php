<?php
$helperInitials = static function(string $name): string {
    $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', $name);
    $parts = preg_split('/\s+/', trim($clean));
    if (!$parts || $parts[0] === '') return 'PR';
    if (count($parts) === 1) return mb_strtoupper(mb_substr($parts[0], 0, 2));
    return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
};
?>
<?php foreach ($products as $product): ?>
    <?php
        $isActive = (bool) $product->active;
        $initials = $helperInitials($product->name);
        $price = (float) $product->price;
        $promoPrice = $product->promotional_price ? (float) $product->promotional_price : null;
        
        $hasPromo = ($promoPrice !== null && $promoPrice > 0 && $promoPrice < $price);
        $discountPercent = $hasPromo ? round((($price - $promoPrice) / $price) * 100) : null;
        
        $priceFormatted = number_format($price, 2, ',', '.');
        $promoPriceFormatted = $promoPrice ? number_format($promoPrice, 2, ',', '.') : null;
        
        $images = $product->images ?? [];
        $totalImages = count($images);
        $coverImage = !empty($images) ? $images[0]->image_path : null;

        // Métricas simuladas/iniciais conforme visual de alta conversão
        $accessCount = 0;
        $clickCount = 0;
        $conversionRate = 0;
        $enviosCount = 0;
    ?>
    <article class="product-card-premium <?= ! $isActive ? 'is-inactive' : '' ?>"
             data-product-card
             data-id="<?= esc($product->id) ?>"
             data-name="<?= esc(mb_strtolower($product->name)) ?>"
             data-status="<?= $isActive ? 'active' : 'inactive' ?>">

        <!-- Capa do Produto -->
        <div class="product-card-banner">
            <?php if ($discountPercent): ?>
                <span class="product-discount-badge">-<?= $discountPercent ?>%</span>
            <?php endif; ?>

            <?php if ($coverImage): ?>
                <img src="<?= base_url('uploads/products/' . $coverImage) ?>" alt="<?= esc($product->name) ?>">
            <?php else: ?>
                <div class="no-image-placeholder">
                    <i class="ti ti-photo-off"></i>
                    <span>Sem fotos</span>
                </div>
            <?php endif; ?>

            <?php if ($totalImages > 0): ?>
                <span class="product-photos-badge">
                    <i class="ti ti-photo"></i> <?= $totalImages ?> <?= $totalImages === 1 ? 'foto' : 'fotos' ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Conteúdo do Card -->
        <div class="product-card-body">
            <!-- Título -->
            <h3 class="product-card-title" title="<?= esc($product->name) ?>">
                <?= esc($product->name) ?>
            </h3>

            <!-- Preços -->
            <div class="product-card-price-row">
                <?php if ($hasPromo): ?>
                    <span class="product-price-current">R$ <?= $promoPriceFormatted ?></span>
                    <span class="product-price-old">R$ <?= $priceFormatted ?></span>
                <?php else: ?>
                    <span class="product-price-current">R$ <?= $priceFormatted ?></span>
                <?php endif; ?>
            </div>

            <!-- Mini Galeria (Thumbs) -->
            <?php if (!empty($images)): ?>
                <div class="product-card-gallery-row">
                    <?php foreach (array_slice($images, 0, 5) as $img): ?>
                        <div class="product-gallery-thumb">
                            <img src="<?= base_url('uploads/products/' . $img->image_path) ?>" alt="Miniatura">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Chips de Status / Tags -->
            <div class="product-card-chips">
                <?php if ($isActive): ?>
                    <span class="product-chip chip-status-active">Ativo</span>
                <?php else: ?>
                    <span class="product-chip chip-status-inactive">Inativo</span>
                <?php endif; ?>

                <?php if ($hasPromo): ?>
                    <span class="product-chip chip-promo">Em promoção</span>
                <?php endif; ?>

                <span class="product-chip chip-sends"><?= $enviosCount ?> envios</span>
            </div>

            <!-- Métricas -->
            <div class="product-card-metrics">
                <div class="product-metric-item">
                    <span class="product-metric-val"><i class="ti ti-eye"></i> <?= $accessCount ?></span>
                    <span class="product-metric-label">acessos</span>
                </div>
                <div class="product-metric-item">
                    <span class="product-metric-val"><i class="ti ti-click"></i> <?= $clickCount ?></span>
                    <span class="product-metric-label">cliques</span>
                </div>
                <div class="product-metric-item">
                    <span class="product-metric-val metric-rate"><?= $conversionRate ?>%</span>
                    <span class="product-metric-label">conversão</span>
                </div>
            </div>

            <span class="product-card-time">Último acesso recente</span>

            <!-- Ações -->
            <div class="product-card-actions-grid">
                <div class="product-actions-row">
                    <?php if (\App\Libraries\UserPermissions::hasPermission('products', 'edit')): ?>
                        <a href="<?= site_url('produtos/' . $product->id . '/editar') ?>" class="btn-product-action">
                            <i class="ti ti-edit"></i>
                            <span>Editar</span>
                        </a>
                    <?php endif; ?>

                    <a href="<?= site_url('p/' . $product->slug) ?>" target="_blank" class="btn-product-action action-send">
                        <i class="ti ti-send"></i>
                        <span>Enviar</span>
                    </a>
                </div>

                <div class="product-actions-row">
                    <?php if (\App\Libraries\UserPermissions::hasPermission('products', 'edit')): ?>
                        <button type="button" class="btn-product-action" style="width:100%;" onclick="toggleProductStatus(<?= $product->id ?>)">
                            <span id="status-text-<?= $product->id ?>"><?= $isActive ? 'Inativar' : 'Ativar' ?></span>
                        </button>
                    <?php endif; ?>

                    <?php if (\App\Libraries\UserPermissions::hasPermission('products', 'delete')): ?>
                        <form action="<?= site_url('produtos/' . $product->id . '/excluir') ?>" method="post" data-confirm-action="product-delete" data-action-name="<?= esc($product->name) ?>" data-processing-title="Excluindo produto" data-processing-message="Removendo o produto do catálogo." style="margin:0; width:100%;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-product-action action-danger" style="width:100%;">
                                <span>Excluir</span>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </article>
<?php endforeach; ?>