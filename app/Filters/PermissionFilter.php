<?php

namespace App\Filters;

use App\Libraries\UserPermissions;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('authenticated')) {
            return redirect()->to('/login')->with('error', 'Entre para acessar o painel administrativo.');
        }

        if (empty($arguments)) {
            return;
        }

        $module = $arguments[0] ?? null;
        $action = $arguments[1] ?? 'view';

        if ($module && ! UserPermissions::hasPermission($module, $action)) {
            if ($request->isAJAX()) {
                return response()->setStatusCode(403)->setJSON(['error' => 'Você não tem permissão para acessar este recurso.']);
            }
            return redirect()->to('/')->with('error', 'Você não tem permissão para acessar este recurso.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
