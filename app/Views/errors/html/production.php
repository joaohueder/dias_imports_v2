<?php
// Verificação de segurança: Se for erro de estrutura de banco de dados (tabela / coluna ausente),
// redireciona ou renderiza a tela de auto-migração imediatamente
if (isset($exception) && $exception instanceof \Throwable) {
    $errorMessage = strtolower($exception->getMessage());
    $errorCode = (int) $exception->getCode();
    $isDbSchemaError = (
        str_contains($errorMessage, "doesn't exist") ||
        str_contains($errorMessage, "unknown column") ||
        str_contains($errorMessage, "base table or view not found") ||
        str_contains($errorMessage, "table not found") ||
        str_contains($errorMessage, "no such table") ||
        str_contains($errorMessage, "no such column") ||
        in_array($errorCode, [1146, 1054, 1051, 1049], true)
    );

    $currentUri = $_SERVER['REQUEST_URI'] ?? '';
    $isMigrateRoute = str_contains($currentUri, 'database/auto-migrate');
    $alreadyAttempted = (isset($_GET['_migrated']) || isset($_GET['migrated_attempted']));

    if ($isDbSchemaError && ! $isMigrateRoute && ! $alreadyAttempted) {
        $returnUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $currentUri;
        $separator = str_contains($returnUrl, '?') ? '&' : '?';
        $safeReturnUrl = $returnUrl . $separator . '_migrated=1';
        $migrateUrl = rtrim(base_url(), '/') . '/database/auto-migrate?return_url=' . urlencode($safeReturnUrl);

        if (! headers_sent()) {
            header('Location: ' . $migrateUrl, true, 302);
            exit(0);
        } else {
            echo "<script>window.location.href = '" . esc($migrateUrl, 'js') . "';</script>";
            exit(0);
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">

    <title><?= function_exists('lang') ? lang('Errors.whoops') : 'Whoops!' ?> | JH7 Marketing</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>⚠️</text></svg>">

    <style>
        <?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.css')) ?>
    </style>
    <script>
        console.error('Erro de Execução (Production): <?= esc(addslashes(lang('Errors.weHitASnag')), 'raw') ?>');
    </script>
</head>
<body>

    <div class="container text-center">

        <h1 class="headline"><?= lang('Errors.whoops') ?></h1>

        <p class="lead"><?= lang('Errors.weHitASnag') ?></p>

    </div>

</body>

</html>
