<?php
use Jeffreyvr\Paver\View;
use Jeffreyvr\Paver\Blocks\Renderer;

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
            <?php echo Renderer::blocks($blocks, paver()->editorContext()); ?>
        </div>
        <?php echo (new View(paver()->viewPath().'/editor/breadcrumb.php')); ?>
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
        // Frame is a separate document; always boot its Alpine bundle.
        // `paver()->alpine` flag only controls the outer editor shell (e.g.
        // deferred start for a teleported sidebar) and must not silence here.
        window.__paver_start_alpine = true;

        <?php echo paver()->loadAssetContent('/js/frame.js'); ?>
    </script>
</body>
</html>
