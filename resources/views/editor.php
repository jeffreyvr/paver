<div
    class="paver__container"
    x-cloak
    x-data="Paver({
        view: 'desktop',
        texts: <?php echo htmlspecialchars(json_encode(paver()->getLocalizations()), ENT_QUOTES, 'UTF-8'); ?>,
        content: '<?php echo htmlspecialchars($content, ENT_QUOTES, 'UTF-8'); ?>',
        api: <?php echo htmlspecialchars(json_encode(paver()->api()), ENT_QUOTES, 'UTF-8'); ?>,
        blocks: <?php echo htmlspecialchars(paver()->blocks(encode: true)); ?>,
        ...<?php echo htmlspecialchars(json_encode($config)); ?>
    })">

    <div>
        <div class="paver__section paver__section-main">
            <div class="paver__section-header">
                Editor
            </div>

            <?php echo new \Jeffreyvr\Paver\View(paver()->viewPath().'editor/actions.php'); ?>
        </div>
        <div class="paver__iframe-wrapper">
            <div class="paver__iframe-overlay"></div>
            <iframe x-ref="editor" id="editor"
                class="paver__editor"
                :class="view == 'desktop' ? 'paver__desktop' : 'paver__mobile'"
                srcdoc="<?php echo htmlspecialchars($editorHtml, ENT_QUOTES, 'UTF-8'); ?>"></iframe>
        </div>
    </div>

    <?php echo new \Jeffreyvr\Paver\View(paver()->viewPath().'editor/sidebar.php'); ?>

    <input type="hidden" name="paver_editor_content" x-model="content">
</div>

<style>
<?php echo paver()->loadAssetContent('/css/paver.css'); ?>
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('Paver', (data) => (
            window.Paver(data)
        ));
    });
</script>

<script>
    window.__paver_start_alpine = <?php echo paver()->alpine ? 'true' : 'false'; ?>;

    <?php echo paver()->loadAssetContent('/js/paver.js'); ?>
</script>
