<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= isset($product) ? 'Editar Produto' : 'Novo Produto' ?></h3>
    </div>
    <div class="card-body">
        <form action="<?= isset($product) ? site_url('produtos/' . $product->id . '/editar') : site_url('produtos/novo') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nome do Produto *</label>
                    <input type="text" class="form-control" name="name" value="<?= old('name', $product->name ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Slug (URL) *</label>
                    <input type="text" class="form-control" name="slug" value="<?= old('slug', $product->slug ?? '') ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea class="form-control" name="description" rows="3"><?= old('description', $product->description ?? '') ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Preço *</label>
                    <input type="number" step="0.01" class="form-control" name="price" value="<?= old('price', $product->price ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Preço Promocional</label>
                    <input type="number" step="0.01" class="form-control" name="promotional_price" value="<?= old('promotional_price', $product->promotional_price ?? '') ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Layout da Landing Page</label>
                    <select class="form-select" name="layout">
                        <option value="oferta_direta" <?= old('layout', $product->layout ?? '') == 'oferta_direta' ? 'selected' : '' ?>>Oferta Direta</option>
                        <option value="vitrine_editorial" <?= old('layout', $product->layout ?? '') == 'vitrine_editorial' ? 'selected' : '' ?>>Vitrine Editorial</option>
                        <option value="imersivo" <?= old('layout', $product->layout ?? '') == 'imersivo' ? 'selected' : '' ?>>Imersivo</option>
                        <option value="conversa" <?= old('layout', $product->layout ?? '') == 'conversa' ? 'selected' : '' ?>>Conversa</option>
                        <option value="passo_a_passo" <?= old('layout', $product->layout ?? '') == 'passo_a_passo' ? 'selected' : '' ?>>Passo a Passo</option>
                        <option value="cartaz" <?= old('layout', $product->layout ?? '') == 'cartaz' ? 'selected' : '' ?>>Cartaz</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Paleta de Cores</label>
                    <select class="form-select" name="color_palette">
                        <option value="brasa" <?= old('color_palette', $product->color_palette ?? '') == 'brasa' ? 'selected' : '' ?>>Brasa</option>
                        <option value="menta" <?= old('color_palette', $product->color_palette ?? '') == 'menta' ? 'selected' : '' ?>>Menta</option>
                        <option value="noturno" <?= old('color_palette', $product->color_palette ?? '') == 'noturno' ? 'selected' : '' ?>>Noturno</option>
                        <option value="aurora" <?= old('color_palette', $product->color_palette ?? '') == 'aurora' ? 'selected' : '' ?>>Aurora</option>
                        <option value="areia" <?= old('color_palette', $product->color_palette ?? '') == 'areia' ? 'selected' : '' ?>>Areia</option>
                        <option value="jade" <?= old('color_palette', $product->color_palette ?? '') == 'jade' ? 'selected' : '' ?>>Jade</option>
                        <option value="oceano" <?= old('color_palette', $product->color_palette ?? '') == 'oceano' ? 'selected' : '' ?>>Oceano</option>
                        <option value="algodao" <?= old('color_palette', $product->color_palette ?? '') == 'algodao' ? 'selected' : '' ?>>Algodão</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="active" value="1" <?= old('active', $product->active ?? 1) ? 'checked' : '' ?>>
                    <span class="form-check-label">Produto Ativo</span>
                </label>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary">Salvar Produto</button>
                <a href="<?= site_url('produtos') ?>" class="btn btn-link">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>