<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProductModel;
use App\Models\ProductImageModel;

class Products extends BaseController
{
    protected $productModel;
    protected $productImageModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->productImageModel = new ProductImageModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Produtos',
            'pageDescription' => 'Gerencie os produtos e suas landing pages.',
            'activePage' => 'products',
            'products' => $this->productModel->orderBy('created_at', 'DESC')->findAll()
        ];

        return $this->renderPage('admin/products/index', $data);
    }

    public function create()
    {
        $data = [
            'pageTitle' => 'Novo Produto',
            'pageDescription' => 'Cadastre um novo produto.',
            'activePage' => 'products'
        ];

        return $this->renderPage('admin/products/form', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|max_length[255]',
            'price' => 'required|numeric',
            'slug' => 'required|max_length[255]|is_unique[products.slug]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $data['active'] = $this->request->getPost('active') ? 1 : 0;
        $data['meta_ads_active'] = $this->request->getPost('meta_ads_active') ? 1 : 0;

        if ($this->productModel->insert($data)) {
            return redirect()->to('produtos')->with('success', 'Produto criado com sucesso.');
        }

        return redirect()->back()->withInput()->with('error', 'Erro ao criar produto.');
    }

    public function edit($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('produtos')->with('error', 'Produto não encontrado.');
        }

        $data = [
            'pageTitle' => 'Editar Produto',
            'pageDescription' => 'Edite as informações do produto.',
            'activePage' => 'products',
            'product' => $product,
            'images' => $this->productImageModel->where('product_id', $id)->orderBy('sort_order', 'ASC')->findAll()
        ];

        return $this->renderPage('admin/products/form', $data);
    }

    public function update($id)
    {
        $rules = [
            'name' => 'required|max_length[255]',
            'price' => 'required|numeric',
            'slug' => "required|max_length[255]|is_unique[products.slug,id,{$id}]"
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $data['active'] = $this->request->getPost('active') ? 1 : 0;
        $data['meta_ads_active'] = $this->request->getPost('meta_ads_active') ? 1 : 0;

        if ($this->productModel->update($id, $data)) {
            return redirect()->to('produtos')->with('success', 'Produto atualizado com sucesso.');
        }

        return redirect()->back()->withInput()->with('error', 'Erro ao atualizar produto.');
    }

    public function toggleStatus($id)
    {
        $product = $this->productModel->find($id);
        if ($product) {
            $this->productModel->update($id, ['active' => !$product->active]);
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Produto não encontrado.']);
    }

    public function delete($id)
    {
        if ($this->productModel->delete($id)) {
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Erro ao excluir produto.']);
    }
}
