<?php

use Jeffreyvr\Paver\Blocks\Example;
use Jeffreyvr\Paver\Paver;

require __DIR__ . '/../../vendor/autoload.php';

$paver = Paver::instance();

$paver->api->setEndpoints([
    'options' => 'index.php?options',
    'render' => 'index.php?render',
    'fetch' => 'index.php?fetch',
]);

$paver->frame->headHtml = <<<HTML
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
HTML;

$paver->registerBlock(Example::class);

$content = [];

if(isset($_GET['content'])) {
    $content[] = ['block' => 'paver.example'];
}

require 'endpoints.php';
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<body class="flex h-screen">
<script src="https://cdn.tailwindcss.com?plugins=forms"></script>

<?php
$paver->debug(true);
?>

<?php echo $paver->render($content, ['config' => [
    'showExpandButton' => false,
    'showViewButton' => true,
]]); ?>
</body>
