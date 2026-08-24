<?php

namespace App\Controllers;

use App\Models\CompanyProfileModel;
use App\Models\CompanyWhatsappModel;
use App\Models\MetaAdsSettingModel;
use App\Models\ProductImageModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ProductLanding extends BaseController
{
    public function show(string $slug): string
    {
        $productModel = new ProductModel();
        $product = $productModel->where('slug', $slug)->where('active', 1)->first();

        if (!$product) {
            throw PageNotFoundException::forPageNotFound('Produto não encontrado ou inativo.');
        }

        $imageModel = new ProductImageModel();
        $images = $imageModel->where('product_id', $product->id)
            ->orderBy('is_cover', 'DESC')
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        $company = (new CompanyProfileModel())->first();
        $metaAds = (new MetaAdsSettingModel())->first();

        // Número do WhatsApp
        $whatsappNumber = $product->whatsapp_number;
        if (empty($whatsappNumber)) {
            $defaultWhatsapp = (new CompanyWhatsappModel())->where('is_default', 1)->first();
            $whatsappNumber = $defaultWhatsapp ? $defaultWhatsapp['phone_number'] : ($company['phone'] ?? '');
        }
        $cleanWhatsapp = preg_replace('/\D+/', '', (string) $whatsappNumber);

        // Prepara mensagem de checkout no WhatsApp
        $productPrice = $product->promotional_price ?: $product->price;
        $formattedPrice = number_format((float) $productPrice, 2, ',', '.');
        $prefilledText = "Olá, tenho interesse no produto *{$product->name}* no valor de *R$ {$formattedPrice}*. Poderia me passar mais informações?";
        $whatsappUrl = "https://wa.me/{$cleanWhatsapp}?text=" . rawurlencode($prefilledText);

        // Decodificar FAQ se for JSON
        $faqList = [];
        if (!empty($product->faq)) {
            if (is_array($product->faq)) {
                $faqList = $product->faq;
            } else {
                $decoded = json_decode($product->faq, true);
                if (is_array($decoded)) {
                    $faqList = $decoded;
                }
            }
        }

        // Padrão de FAQ se estiver vazio
        if (empty($faqList)) {
            $faqList = [
                ['q' => 'Como eu compro?', 'a' => 'Clique no botão para abrir o WhatsApp. A conversa já abre com o produto e valor especificados. Nossa equipe combina o pagamento e envio.'],
                ['q' => 'O preço desta página é o que eu pago?', 'a' => 'Sim! O valor promocional desta página é exatamente o que você pagará, sem pegadinhas ou acréscimos ocultos.'],
                ['q' => 'Com quem eu estou falando?', 'a' => 'Você falará diretamente com a equipe oficial de atendimento da Dias Imports Barretos.'],
                ['q' => 'E se eu ficar com dúvida antes de decidir?', 'a' => 'Não tem problema! Fale conosco no WhatsApp sem compromisso para tirar todas as dúvidas e ver mais detalhes do produto.']
            ];
        }

        return view('landing/product', [
            'product' => $product,
            'images' => $images,
            'company' => $company,
            'metaAds' => $metaAds,
            'whatsappUrl' => $whatsappUrl,
            'faqList' => $faqList,
        ]);
    }
}
