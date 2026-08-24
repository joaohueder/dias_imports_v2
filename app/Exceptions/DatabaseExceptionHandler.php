<?php

namespace App\Exceptions;

use CodeIgniter\Debug\BaseExceptionHandler;
use CodeIgniter\Debug\ExceptionHandlerInterface;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Exceptions as ExceptionsConfig;
use Throwable;

class DatabaseExceptionHandler extends BaseExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * Identifica se a exceção é decorrente de tabela/coluna ausente no banco de dados.
     * Códigos MySQL comuns:
     * 1146: Table '...' doesn't exist
     * 1054: Unknown column '...' in 'field list' / 'where clause' / 'order clause'
     * 1048: Column cannot be null (quando falta migration de ajuste)
     * 1049: Unknown database (se banco não inicializou tabelas)
     * 1051: Unknown table
     * 1060: Duplicate column name
     * 1146: Table doesn't exist
     */
    protected function isMissingDatabaseStructureError(Throwable $exception): bool
    {
        $current = $exception;
        while ($current !== null) {
            $msg = strtolower($current->getMessage());
            $code = $current->getCode();

            // Mensagens típicas de erro de schema/migração no MySQL / CodeIgniter
            if (
                str_contains($msg, "doesn't exist") ||
                str_contains($msg, "unknown column") ||
                str_contains($msg, "base table or view not found") ||
                str_contains($msg, "table not found") ||
                str_contains($msg, "no such table") ||
                str_contains($msg, "no such column") ||
                in_array($code, [1146, 1054, 1051, 1049], true)
            ) {
                return true;
            }

            $current = $current->getPrevious();
        }

        return false;
    }

    public function handle(
        Throwable $exception,
        RequestInterface $request,
        ResponseInterface $response,
        int $statusCode,
        int $exitCode,
    ): void {
        // Se a requisição for para a própria tela de migração ou API de migração, evitamos loop
        $uriPath = '';
        if ($request instanceof IncomingRequest) {
            $uriPath = trim($request->getUri()->getPath(), '/');
        }

        $isMigrateRoute = str_starts_with($uriPath, 'database/auto-migrate');

        // Se o migrador já tentou rodar nesta sessão ou requisição para evitar loops infinitos
        $alreadyAttempted = (isset($_GET['_migrated']) || isset($_GET['migrated_attempted']));

        if (! $isMigrateRoute && ! $alreadyAttempted && $this->isMissingDatabaseStructureError($exception)) {
            if ($request instanceof IncomingRequest) {
                $returnUrl = current_url();
                // Acrescenta flag para prevenir loop infinito caso falte algo não migrável
                $separator = str_contains($returnUrl, '?') ? '&' : '?';
                $safeReturnUrl = $returnUrl . $separator . '_migrated=1';
                $migrateUrl = site_url('database/auto-migrate?return_url=' . urlencode($safeReturnUrl));

                // Se for requisição AJAX, responde com instrução de migração ou redirecionamento
                if ($request->isAJAX() || str_contains($request->getHeaderLine('accept'), 'application/json')) {
                    if (! headers_sent()) {
                        header('Content-Type: application/json; charset=utf-8', true, 200);
                    }
                    
                    echo json_encode([
                        'success' => false,
                        'need_migration' => true,
                        'redirect_url' => $migrateUrl,
                        'message' => 'Estrutura de banco de dados desatualizada. Redirecionando para sincronização...'
                    ], JSON_UNESCAPED_UNICODE);
                    
                    if (ENVIRONMENT !== 'testing') {
                        exit(0);
                    }
                    return;
                }

                // Requisição Web normal: redireciona para a tela de bloqueio e auto-migração
                if (! headers_sent()) {
                    header('Location: ' . $migrateUrl, true, 302);
                } else {
                    echo "<script>window.location.href = '" . esc($migrateUrl, 'js') . "';</script>";
                }

                if (ENVIRONMENT !== 'testing') {
                    exit(0);
                }
                return;
            }
        }

        // Fallback para o ExceptionHandler padrão do CodeIgniter 4
        $defaultHandler = new \CodeIgniter\Debug\ExceptionHandler($this->config);
        $defaultHandler->handle($exception, $request, $response, $statusCode, $exitCode);
    }
}
