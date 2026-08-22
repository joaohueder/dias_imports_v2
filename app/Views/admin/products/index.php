<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Lista de Produtos</h3>
        <a href="<?= site_url('produtos/novo') ?>" class="btn btn-primary">
            <i class="ti ti-plus me-2"></i> Novo Produto
        </a>
    </div>
    <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="4" class="text-center">Nenhum produto encontrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= esc($product->name) ?></td>
                            <td>R$ <?= number_format($product->price, 2, ',', '.') ?></td>
                            <td>
                                <label class="form-check form-switch">
                                    <input class="form-check-input toggle-status" type="checkbox" data-id="<?= $product->id ?>" <?= $product->active ? 'checked' : '' ?>>
                                </label>
                            </td>
                            <td>
                                <a href="<?= site_url('produtos/' . $product->id . '/editar') ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                <button class="btn btn-sm btn-outline-danger delete-product" data-id="<?= $product->id ?>">Excluir</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButtons = document.querySelectorAll('.toggle-status');
    toggleButtons.forEach(button => {
        button.addEventListener('change', function() {
            const id = this.dataset.id;
            fetch(`<?= site_url('produtos/') ?>${id}/status`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                }
            }).then(response => response.json())
              .then(data => {
                  if (!data.success) {
                      alert(data.message || 'Erro ao alterar status.');
                      this.checked = !this.checked;
                  }
              });
        });
    });

    const deleteButtons = document.querySelectorAll('.delete-product');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Tem certeza que deseja excluir este produto?')) {
                const id = this.dataset.id;
                fetch(`<?= site_url('produtos/') ?>${id}/excluir`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    }
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          window.location.reload();
                      } else {
                          alert(data.message || 'Erro ao excluir produto.');
                      }
                  });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>