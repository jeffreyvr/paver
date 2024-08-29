<?php
use Jeffreyvr\Paver\Blocks\Renderer;
use Jeffreyvr\Paver\Blocks\BlockFactory;

paver()->frame->activate();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php echo paver()->frame->headHtml; ?>

    <style>
        <?php echo paver()->loadAssetContent('/css/frame.css'); ?>
    </style>
</head>
<body>

    <div
        class="paver__editor-frame"
        x-data="PaverFrame({
            api: <?php echo htmlspecialchars(json_encode(paver()->api()), ENT_QUOTES, 'UTF-8'); ?>,
            texts: <?php echo htmlspecialchars(json_encode(paver()->getLocalizations()), ENT_QUOTES, 'UTF-8'); ?>,
        })"
        >
        <div class="paver__editor-root paver__sortable">
            <?php echo Renderer::blocks($blocks, 'editor'); ?>
        </div>
    </div>

    <?php echo paver()->frame->footerHtml; ?>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('PaverFrame', (data) => (
                window.PaverFrame(data)
            ));
        });
    </script>

    <script>
        window.__paver_start_alpine = <?php echo paver()->alpine ? 'true' : 'false'; ?>;

        <?php echo paver()->loadAssetContent('/js/frame.js'); ?>
    </script>
</body>
</html>
