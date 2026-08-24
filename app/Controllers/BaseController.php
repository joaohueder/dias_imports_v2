<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * Cache estático em memória na requisição para largura do layout
     */
    protected static ?string $cachedLayoutMaxWidth = null;

    /**
     * Retorna a largura do layout sem disparar queries repetidas
     */
    protected function getLayoutMaxWidth(): string
    {
        if (self::$cachedLayoutMaxWidth !== null) {
            return self::$cachedLayoutMaxWidth;
        }

        try {
            $setting = (new \App\Models\AppSettingModel())->where('setting_key', 'layout_max_width')->first();
            $storedLayoutWidth = $setting['setting_value'] ?? '1200';
            $storedNumericWidth = filter_var($storedLayoutWidth, FILTER_VALIDATE_INT);
            self::$cachedLayoutMaxWidth = ($storedLayoutWidth === 'fluid' || ($storedNumericWidth !== false && $storedNumericWidth >= 900 && $storedNumericWidth <= 1800))
                ? $storedLayoutWidth
                : '1200';
        } catch (\Throwable $e) {
            self::$cachedLayoutMaxWidth = '1200';
        }

        return self::$cachedLayoutMaxWidth;
    }

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        $this->helpers = array_merge($this->helpers, ['form', 'url', 'cookie', 'telemetry']);

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }
}
