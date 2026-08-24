<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Controllers\Home;
use App\Models\AppSettingModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProductModel;
use App\Models\ProductImageModel;
use App\Models\CompanyWhatsappModel;
use App\Models\ProductAccessLogModel;

class Products extends BaseController
{
    private const LAYOUT_SETTING_KEY = 'layout_max_width';
    private const DEFAULT_LAYOUT_WIDTH = '1200';

    protected $productModel;
    protected $productImageModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->productImageModel = new ProductImageModel();
    }

    private function renderPage(string $viewPath, array $data = []): string
    {
        $userName = trim((string) session()->get('user_name')) ?: 'Usuário';
        $nameParts = preg_split('/\s+/', $userName) ?: [$userName];
        $firstName = $nameParts[0];
        $lastName = count($nameParts) > 1 ? $nameParts[count($nameParts) - 1] : '';
        $userInitials = mb_strtoupper(mb_substr($firstName, 0, 1) . mb_substr($lastName, 0, 1));
        
        $layoutMaxWidth = $this->getLayoutMaxWidth();

        $viewData = array_merge([
            'layoutMaxWidth' => $layoutMaxWidth,
            'userName' => $userName,
            'userEmail' => (string) session()->get('user_email'),
            'firstName' => $firstName,
            'userInitials' => $userInitials,
            'navigation' => Home::getNavigationList(),
        ], $data);

        return view($viewPath, $viewData);
    }

    public function index()
    {
        $products = $this->productModel->orderBy('created_at', 'DESC')->findAll();
        
        if (! empty($products)) {
            $productIds = array_column($products, 'id');
            $allImages = $this->productImageModel
                ->whereIn('product_id', $productIds)
                ->orderBy('is_cover', 'DESC')
                ->orderBy('sort_order', 'ASC')
                ->findAll();

            $imagesByProduct = [];
            foreach ($allImages as $img) {
                $imagesByProduct[$img->product_id][] = $img;
            }

            $accessLogModel = new ProductAccessLogModel();
            $batchStats = $accessLogModel->getBatchProductMetrics($productIds);

            $jobQueueModel = new \App\Models\SystemJobQueueModel();
            $sendsRows = $jobQueueModel->select("payload, COUNT(*) as cnt")
                ->where('job_key', 'send_product_to_group')
                ->groupBy('payload')
                ->findAll();

            $sendsByProduct = [];
            foreach ($sendsRows as $sr) {
                $pData = json_decode($sr['payload'] ?? '', true);
                if (!empty($pData['product_id'])) {
                    $pid = (int)$pData['product_id'];
                    $sendsByProduct[$pid] = ($sendsByProduct[$pid] ?? 0) + (int)$sr['cnt'];
                }
            }

            foreach ($products as $product) {
                $product->images = $imagesByProduct[$product->id] ?? [];
                $stat = $batchStats[$product->id] ?? ['pageviews' => 0, 'clicks' => 0, 'conversionRate' => 0];
                $product->pageviews = $stat['pageviews'];
                $product->clicks = $stat['clicks'];
                $product->conversionRate = $stat['conversionRate'];
                $product->sendsCount = $sendsByProduct[$product->id] ?? 0;
            }
        }

        $sendJob = (new \App\Models\SystemJobModel())->where('job_key', 'send_product_to_group')->first();
        $isSendJobActive = !empty($sendJob) && (int)$sendJob['is_active'] === 1;

        $realtimeModel = new \App\Models\RealtimeScreenSettingModel();
        $isRealtimeActive = $realtimeModel->isScreenActive('products');
        $realtimeInterval = $realtimeModel->getInterval('products');

        $data = [
            'pageTitle' => 'Produtos',
            'pageDescription' => 'Gerencie os produtos e suas landing pages.',
            'activePage' => 'products',
            'products' => $products,
            'activeGroups' => (new \App\Models\WhatsappGroupModel())->where('status', 'active')->orderBy('name', 'ASC')->findAll(),
            'messageTemplates' => (new \App\Models\MessageTemplateModel())->where('is_active', 1)->orderBy('name', 'ASC')->findAll(),
            'isSendJobActive' => $isSendJobActive,
            'isRealtimeActive' => $isRealtimeActive,
            'realtimeInterval' => $realtimeInterval,
        ];

        return $this->renderPage('admin/products/index', $data);
    }

    public function feed(): \CodeIgniter\HTTP\ResponseInterface
    {
        $snapshotService = new \App\Services\RealtimeSnapshotService();
        $snapshot = $snapshotService->getSnapshot('products');

        if ($snapshot !== null && !empty($snapshot['data'])) {
            helper('telemetry');
            $telemetry = get_footer_telemetry();

            return $this->response->setJSON([
                'success' => true,
                'htmlCards' => $snapshot['data']['htmlCards'],
                'totalResults' => $snapshot['data']['totalResults'],
                'footerHtml' => $telemetry['html'],
                'telemetry' => [
                    'connectionsLastHour' => $telemetry['connectionsLastHour'],
                    'maxConnectionsPerHour' => $telemetry['maxConnectionsPerHour'],
                    'loadTime' => $telemetry['loadTime'],
                ],
            ]);
        }

        $products = $this->productModel->orderBy('created_at', 'DESC')->findAll();
        
        if (! empty($products)) {
            $productIds = array_column($products, 'id');
            $allImages = $this->productImageModel
                ->whereIn('product_id', $productIds)
                ->orderBy('is_cover', 'DESC')
                ->orderBy('sort_order', 'ASC')
                ->findAll();

            $imagesByProduct = [];
            foreach ($allImages as $img) {
                $imagesByProduct[$img->product_id][] = $img;
            }

            $accessLogModel = new ProductAccessLogModel();
            $batchStats = $accessLogModel->getBatchProductMetrics($productIds);

            $jobQueueModel = new \App\Models\SystemJobQueueModel();
            $sendsRows = $jobQueueModel->select("payload, COUNT(*) as cnt")
                ->where('job_key', 'send_product_to_group')
                ->groupBy('payload')
                ->findAll();

            $sendsByProduct = [];
            foreach ($sendsRows as $sr) {
                $pData = json_decode($sr['payload'] ?? '', true);
                if (!empty($pData['product_id'])) {
                    $pid = (int)$pData['product_id'];
                    $sendsByProduct[$pid] = ($sendsByProduct[$pid] ?? 0) + (int)$sr['cnt'];
                }
            }

            foreach ($products as $product) {
                $product->images = $imagesByProduct[$product->id] ?? [];
                $stat = $batchStats[$product->id] ?? ['pageviews' => 0, 'clicks' => 0, 'conversionRate' => 0];
                $product->pageviews = $stat['pageviews'];
                $product->clicks = $stat['clicks'];
                $product->conversionRate = $stat['conversionRate'];
                $product->sendsCount = $sendsByProduct[$product->id] ?? 0;
            }
        }

        $htmlCards = view('admin/products/_cards', ['products' => $products]);

        helper('telemetry');
        $telemetry = get_footer_telemetry();

        return $this->response->setJSON([
            'success' => true,
            'htmlCards' => $htmlCards,
            'totalResults' => count($products),
            'footerHtml' => $telemetry['html'],
            'telemetry' => [
                'connectionsLastHour' => $telemetry['connectionsLastHour'],
                'maxConnectionsPerHour' => $telemetry['maxConnectionsPerHour'],
                'loadTime' => $telemetry['loadTime'],
            ],
        ]);
    }

    public function create()
    {
        $whatsappModel = new CompanyWhatsappModel();
        $whatsapps = $whatsappModel->where('is_active', 1)->orderBy('is_default', 'DESC')->orderBy('name', 'ASC')->findAll();

        $data = [
            'pageTitle' => 'Novo Produto',
            'pageDescription' => 'Cadastre um novo produto.',
            'activePage' => 'products',
            'whatsapps' => $whatsapps
        ];

        return $this->renderPage('admin/products/form', $data);
    }

    private function cleanPrice($price): ?float
    {
        if ($price === null || $price === '') {
            return null;
        }
        if (is_numeric($price)) {
            return (float) $price;
        }
        // Converte formato brasileiro (1.299,90) para float (1299.90)
        $clean = str_replace('.', '', (string) $price);
        $clean = str_replace(',', '.', $clean);
        return is_numeric($clean) ? (float) $clean : null;
    }

    public function store()
    {
        $data = $this->request->getPost();
        $data['price'] = $this->cleanPrice($data['price'] ?? null);
        $data['promotional_price'] = $this->cleanPrice($data['promotional_price'] ?? null);

        $rules = [
            'name' => 'required|max_length[255]',
            'price' => 'required|numeric',
        ];

        // Passa os dados já tratados para o validador
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Garante slug único com 6 dígitos no backend
        $slug = $this->request->getPost('slug');
        if (empty($slug) || !preg_match('/-\d{6}$/', (string) $slug) || $this->productModel->where('slug', $slug)->first()) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);
        }

        $data['meta_ads_active'] = $this->request->getPost('meta_ads_active') ? 1 : 0;
        // O status 'active' agora é gerenciado apenas na listagem, então ao criar, definimos como ativo por padrão
        $data['active'] = 1;

        // Pula validação interna do Model pois já validamos no Controller
        $insertId = $this->productModel->skipValidation(true)->insert($data);
        if ($insertId) {
            $activeTab = $this->request->getPost('active_tab');
            $redirectUrl = "produtos/{$insertId}/editar" . ($activeTab ? "?tab={$activeTab}" : '');
            return redirect()->to($redirectUrl)->with('success', 'Produto criado com sucesso.');
        }

        $modelErrors = $this->productModel->errors();
        $errorMsg = !empty($modelErrors) ? implode(', ', $modelErrors) : 'Erro ao criar produto.';
        return redirect()->back()->withInput()->with('error', $errorMsg);
    }

    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $baseSlug = mb_url_title($name, '-', true);
        if (empty($baseSlug)) {
            $baseSlug = 'produto';
        }

        do {
            $randomNumber = random_int(100000, 999999);
            $slug = "{$baseSlug}-{$randomNumber}";
            
            $builder = $this->productModel->where('slug', $slug);
            if ($excludeId !== null) {
                $builder->where('id !=', $excludeId);
            }
            $exists = $builder->first();
        } while ($exists !== null);

        return $slug;
    }

    public function edit($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('produtos')->with('error', 'Produto não encontrado.');
        }

        // Se o slug existente não tiver o sufixo numérico de 6 dígitos, atualiza e garante unicidade
        if (!preg_match('/-\d{6}$/', (string) $product->slug)) {
            $product->slug = $this->generateUniqueSlug($product->name, (int) $id);
            $this->productModel->update($id, ['slug' => $product->slug]);
        }

        $whatsappModel = new CompanyWhatsappModel();
        $whatsapps = $whatsappModel->where('is_active', 1)->orderBy('is_default', 'DESC')->orderBy('name', 'ASC')->findAll();

        $statsPeriod = (int) ($this->request->getGet('stats_period') ?: 7);
        if (!in_array($statsPeriod, [7, 14, 21, 30], true)) {
            $statsPeriod = 7;
        }

        $logModel = new ProductAccessLogModel();
        $stats = $logModel->getProductMetrics((int) $id, $statsPeriod);

        $data = [
            'pageTitle' => 'Editar Produto',
            'pageDescription' => 'Edite as informações do produto.',
            'activePage' => 'products',
            'product' => $product,
            'whatsapps' => $whatsapps,
            'images' => $this->productImageModel->where('product_id', $id)->orderBy('sort_order', 'ASC')->findAll(),
            'stats' => $stats,
        ];

        return $this->renderPage('admin/products/form', $data);
    }

    public function update($id)
    {
        $data = $this->request->getPost();
        $data['price'] = $this->cleanPrice($data['price'] ?? null);
        $data['promotional_price'] = $this->cleanPrice($data['promotional_price'] ?? null);

        $rules = [
            'name' => 'required|max_length[255]',
            'price' => 'required|numeric',
            'slug' => "required|max_length[255]|is_unique[products.slug,id,{$id}]"
        ];

        // Passa os dados já tratados para o validador
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data['meta_ads_active'] = $this->request->getPost('meta_ads_active') ? 1 : 0;
        // Removemos a atualização do status 'active' daqui, pois agora é feito apenas via toggleStatus na listagem
        if (isset($data['active'])) {
            unset($data['active']);
        }

        // Pula validação interna do Model pois já validamos no Controller
        if ($this->productModel->skipValidation(true)->update($id, $data)) {
            $activeTab = $this->request->getPost('active_tab');
            $redirectUrl = "produtos/{$id}/editar" . ($activeTab ? "?tab={$activeTab}" : '');
            return redirect()->to($redirectUrl)->with('success', 'Produto atualizado com sucesso.');
        }

        $modelErrors = $this->productModel->errors();
        $errorMsg = !empty($modelErrors) ? implode(', ', $modelErrors) : 'Erro ao atualizar produto.';
        return redirect()->back()->withInput()->with('error', $errorMsg);
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
        // Exclui imagens do disco associadas a este produto
        $images = $this->productImageModel->where('product_id', $id)->findAll();
        foreach ($images as $img) {
            $filePath = FCPATH . 'uploads/products/' . $img->image_path;
            if (is_file($filePath)) {
                @unlink($filePath);
            }
        }
        $this->productImageModel->where('product_id', $id)->delete();

        if ($this->productModel->delete($id)) {
            return redirect()->to('produtos')->with('success', 'Produto excluído com sucesso.');
        }
        return redirect()->to('produtos')->with('error', 'Erro ao excluir produto.');
    }

    /**
     * Retorna os dados estatísticos agregados via AJAX para atualização sem reload
     */
    public function getStatsData($id)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return $this->response->setJSON(['success' => false, 'message' => 'Produto não encontrado.']);
        }

        $statsPeriod = (int) ($this->request->getGet('stats_period') ?: 7);
        if (!in_array($statsPeriod, [7, 14, 21, 30], true)) {
            $statsPeriod = 7;
        }

        $logModel = new ProductAccessLogModel();
        $stats = $logModel->getProductMetrics((int) $id, $statsPeriod);

        return $this->response->setJSON($stats);
    }
    public function stats($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('produtos')->with('error', 'Produto não encontrado.');
        }

        $statsPeriod = (int) ($this->request->getGet('stats_period') ?: 7);
        if (!in_array($statsPeriod, [7, 14, 21, 30], true)) {
            $statsPeriod = 7;
        }

        $logModel = new ProductAccessLogModel();
        $stats = $logModel->getProductMetrics((int) $id, $statsPeriod);

        $data = [
            'pageTitle' => 'Estatísticas do Produto',
            'pageDescription' => 'Métricas de acesso e conversão.',
            'activePage' => 'products',
            'product' => $product,
            'stats' => $stats,
        ];

        return $this->renderPage('admin/products/stats', $data);
    }
    public function uploadImage()
    {
        $productId = $this->request->getPost('product_id');
        $file = $this->request->getFile('image');
        $base64Image = $this->request->getPost('image_base64');

        if (!$productId || (!$file && !$base64Image)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nenhuma imagem enviada.'
            ]);
        }

        $uploadDir = FCPATH . 'uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = 'prod_' . $productId . '_' . time() . '_' . bin2hex(random_bytes(3)) . '.webp';
        $targetPath = $uploadDir . $filename;

        if (!empty($base64Image) && preg_match('/^data:image\/(\w+);base64,/', $base64Image)) {
            $data = substr($base64Image, strpos($base64Image, ',') + 1);
            $decodedData = base64_decode($data);
            if ($decodedData === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Falha ao decodificar imagem.']);
            }

            $tempFile = tempnam(sys_get_temp_dir(), 'prod_crop_');
            file_put_contents($tempFile, $decodedData);

            $processed = $this->compressAndSaveImage($tempFile, $targetPath, 300 * 1024);
            @unlink($tempFile);

            if (!$processed) {
                file_put_contents($targetPath, $decodedData);
            }
        } elseif ($file && $file->isValid()) {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            if (!in_array($file->getMimeType(), $allowedMimes, true)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Formato não suportado. Apenas JPG, PNG ou WEBP.'
                ]);
            }

            // Processa, redimensiona e comprime a imagem para ficar com qualidade e < 300KB (em WEBP/JPEG)
            $tempPath = $file->getTempName();
            $processed = $this->compressAndSaveImage($tempPath, $targetPath, 300 * 1024);

            if (!$processed) {
                $filename = $file->getRandomName();
                $file->move($uploadDir, $filename);
            }
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Arquivo inválido.']);
        }

        // Determina se é a primeira imagem para definir como capa
        $existingCount = $this->productImageModel->where('product_id', $productId)->countAllResults();
        $isCover = ($existingCount === 0);

        $insertedId = $this->productImageModel->insert([
            'product_id' => $productId,
            'image_path' => $filename,
            'is_cover'   => $isCover ? 1 : 0,
            'sort_order' => $existingCount + 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'csrfHash' => csrf_hash(),
            'image' => [
                'id' => $insertedId,
                'product_id' => $productId,
                'image_path' => $filename,
                'url' => base_url('uploads/products/' . $filename),
                'is_cover' => $isCover,
                'sort_order' => $existingCount + 1
            ]
        ]);
    }

    public function deleteImage()
    {
        $imageId = $this->request->getPost('image_id');
        $image = $this->productImageModel->find($imageId);

        if (!$image) {
            return $this->response->setJSON(['success' => false, 'message' => 'Imagem não encontrada.']);
        }

        $filePath = FCPATH . 'uploads/products/' . $image->image_path;
        if (is_file($filePath)) {
            @unlink($filePath);
        }

        $this->productImageModel->delete($imageId);

        // Se era capa, define a primeira restante como capa
        if ($image->is_cover) {
            $next = $this->productImageModel->where('product_id', $image->product_id)->orderBy('sort_order', 'ASC')->first();
            if ($next) {
                $this->productImageModel->update($next->id, ['is_cover' => 1]);
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'csrfHash' => csrf_hash()
        ]);
    }

    public function setCoverImage()
    {
        $imageId = $this->request->getPost('image_id');
        $productId = $this->request->getPost('product_id');

        $image = $this->productImageModel->find($imageId);
        if (!$image || $image->product_id != $productId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Imagem não encontrada.']);
        }

        $this->productImageModel->where('product_id', $productId)->set(['is_cover' => 0])->update();
        $this->productImageModel->update($imageId, ['is_cover' => 1]);

        return $this->response->setJSON([
            'success' => true,
            'csrfHash' => csrf_hash()
        ]);
    }

    public function enqueueSend(): ResponseInterface
    {
        $productId = (int) $this->request->getPost('product_id');
        $templateMode = (string) ($this->request->getPost('template_mode') ?: 'random');
        $selectedTemplateId = $this->request->getPost('template_id') ? (int) $this->request->getPost('template_id') : null;
        $groupIds = (array) $this->request->getPost('group_ids');

        if ($productId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Produto não informado.',
            ]);
        }

        if (empty($groupIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Selecione ao menos 1 grupo do WhatsApp para envio.',
            ]);
        }

        $jobCenterService = new \App\Services\JobCenterService();
        $result = $jobCenterService->enqueueProductDispatches($productId, $groupIds, $templateMode, $selectedTemplateId);

        return $this->response->setJSON($result);
    }

    public function reorderImages()
    {
        $order = $this->request->getPost('order');
        if (is_array($order)) {
            foreach ($order as $index => $id) {
                $this->productImageModel->update($id, ['sort_order' => $index + 1]);
            }
        }
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Comprime e redimensiona a imagem para ficar abaixo do tamanho limite (300KB) com alta qualidade
     */
    private function compressAndSaveImage(string $sourcePath, string $destinationPath, int $maxBytes = 307200): bool
    {
        if (!extension_loaded('gd') || !function_exists('getimagesize')) {
            return false;
        }

        $info = @\getimagesize($sourcePath);
        if (!$info) return false;

        $mime = $info['mime'] ?? '';
        $srcImg = null;

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                if (function_exists('imagecreatefromjpeg')) {
                    $srcImg = @\imagecreatefromjpeg($sourcePath);
                }
                break;
            case 'image/png':
                if (function_exists('imagecreatefrompng')) {
                    $srcImg = @\imagecreatefrompng($sourcePath);
                }
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $srcImg = @\imagecreatefromwebp($sourcePath);
                }
                break;
        }

        if (!$srcImg) return false;

        $origWidth = \imagesx($srcImg);
        $origHeight = \imagesy($srcImg);

        // Se dimensões forem muito grandes (> 1600px), redimensiona proporcionalmente mantendo alta resolução
        $maxDimension = 1600;
        $width = $origWidth;
        $height = $origHeight;

        if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
            if ($origWidth >= $origHeight) {
                $width = $maxDimension;
                $height = (int) round(($origHeight / $origWidth) * $maxDimension);
            } else {
                $height = $maxDimension;
                $width = (int) round(($origWidth / $origHeight) * $maxDimension);
            }
        }

        $targetImg = \imagecreatetruecolor($width, $height);
        
        // Preserva transparência se existir
        \imagealphablending($targetImg, false);
        \imagesavealpha($targetImg, true);
        $transparent = \imagecolorallocatealpha($targetImg, 255, 255, 255, 127);
        \imagefilledrectangle($targetImg, 0, 0, $width, $height, $transparent);
        \imagealphablending($targetImg, true);

        \imagecopyresampled($targetImg, $srcImg, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);
        \imagedestroy($srcImg);

        // Tentamos salvar em WEBP com ajuste fino de qualidade
        $quality = 85;
        if (function_exists('imagewebp')) {
            do {
                ob_start();
                \imagewebp($targetImg, null, $quality);
                $data = ob_get_clean();
                $size = strlen($data);
                if ($size <= $maxBytes || $quality <= 35) {
                    file_put_contents($destinationPath, $data);
                    \imagedestroy($targetImg);
                    return true;
                }
                $quality -= 10;
            } while ($quality >= 30);

            file_put_contents($destinationPath, $data);
            \imagedestroy($targetImg);
            return true;
        }

        // Fallback para JPEG
        $quality = 85;
        $destinationPath = preg_replace('/\.webp$/i', '.jpg', $destinationPath);
        do {
            ob_start();
            \imagejpeg($targetImg, null, $quality);
            $data = ob_get_clean();
            $size = strlen($data);
            if ($size <= $maxBytes || $quality <= 35) {
                file_put_contents($destinationPath, $data);
                \imagedestroy($targetImg);
                return true;
            }
            $quality -= 10;
        } while ($quality >= 30);

        file_put_contents($destinationPath, $data);
        \imagedestroy($targetImg);
        return true;
    }
}
