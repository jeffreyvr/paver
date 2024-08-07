<?php
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
        })"
        @keydown.window.escape="exit"
        @keydown.window="revert"
        >
        <div class="paver__editor-root paver__sortable">
            <?php foreach ($blocks as $block) :
                $_block = BlockFactory::createById($block['block'], $block['data'] ?? [], $block['children'] ?? []);

                echo $_block->renderer('editor')->render();
            endforeach; ?>
        </div>
    </div>

    <?php echo paver()->frame->footerHtml; ?>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('PaverFrame', (data) => (
                window.PaverFrame(data)
            ));

            Alpine.magic('tooltip', el => message => {
                let instance = tippy(el, { content: message, trigger: 'manual' })

                instance.show()

                setTimeout(() => {
                    instance.hide()

                    setTimeout(() => instance.destroy(), 150)
                }, 2000)
            })

            Alpine.directive('tooltip', (el, { expression }) => {
                tippy(el, { content: expression })
            })
        });
    </script>

    <script>
        window.__paver_start_alpine = <?php echo paver()->alpine ? 'true' : 'false'; ?>;

        <?php echo paver()->loadAssetContent('/js/frame.js'); ?>
    </script>
</body>
</html>
