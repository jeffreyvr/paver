<?php

use Jeffreyvr\Paver\Blocks\BlockFactory;

?>
<div
    class="paver__container"
    @keydown.window="handleRevertShortcut"
    @keydown.window.escape="handleEscape"
    x-data="Paver({
        view: 'desktop',
        content: '<?php echo htmlspecialchars($content, ENT_QUOTES, 'UTF-8'); ?>',
        api: <?php echo htmlspecialchars(json_encode(paver()->api()), ENT_QUOTES, 'UTF-8'); ?>,
        blocks: <?php echo htmlspecialchars(json_encode(array_map(function ($block) {
                $instance = BlockFactory::createById($block);

                return [
                    'name' => $instance->name,
                    'reference' => $instance::$reference,
                    'icon' => $instance->getIcon(),
                ];
                }, array_keys(paver()->blocks())))); ?>,
        ...<?php echo htmlspecialchars(json_encode($config)); ?>
    })">

    <div>
        <div class="paver__section" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="paver__section-header" style="border-bottom: 0;">Editor</div>
            <div style="padding: 0 12px; display: flex; gap: 4px;">
                <div x-show="showViewButton" class="paver__hide-on-mobile">
                    <button x-show="view === 'desktop'" type="button" @click="setView('mobile')" class="paver__btn-icon" x-tooltip="Change to mobile">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                    </button>
                    <button x-show="view === 'mobile'" type="button" @click="setView('desktop')" class="paver__btn-icon" x-tooltip="Change to desktop">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                        </svg>
                    </button>
                </div>

                <div class="paver__hide-on-mobile" x-show="showExpandButton">
                    <button type="button" @click="toggleExpand" x-show="!expanded" class="paver__btn-icon" x-tooltip="Expand">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                        </svg>
                    </button>
                    <button type="button" @click="toggleExpand" x-show="expanded" class="paver__btn-icon" x-tooltip="Minimize">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="paver__iframe-wrapper">
            <div class="paver__iframe-overlay"></div>
            <iframe x-ref="editor" id="editor"
                class="paver__editor"
                :class="view == 'desktop' ? 'paver__desktop' : 'paver__mobile'"
                srcdoc="<?php echo htmlspecialchars($editorHtml, ENT_QUOTES, 'UTF-8'); ?>"></iframe>
        </div>
    </div>

    <div class="paver__sidebar">
        <div class="paver__resizer"></div>

        <div class="paver__section">
            <div class="paver__section-header"><?php echo 'Blocks'; ?></div>

            <div class="paver__section-content">
                <input type="text" x-model="search" placeholder="Search blocks" class="paver__search-blocks">

                <div class="paver__block-inserter-wrapper">
                    <div x-ref="blocksInserter" class="paver__block-grid paver__sortable">
                        <?php $index = 0; foreach (paver()->blocks() as $key => $block) :
                            $blockInstance = BlockFactory::createById($key); ?>
                            <div class="paver__sortable-item paver__block-handle" <?php echo $blockInstance->childOnly ? 'data-child-block-only' : ''; ?> x-show="(!allowedBlocks.length && !<?php echo $blockInstance->childOnly ? 'true' : 'false'; ?>) || allowedBlocks.includes('<?php echo $key; ?>')" data-block="<?php echo $key; ?>" <?php echo $blockInstance->childOnly ? 'style="display: none"' : null; ?>>
                                <?php if ($icon = $blockInstance->getIcon()) : ?>
                                    <?php echo $icon; ?>
                                <?php endif; ?>

                                <?php echo $blockInstance->name; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="paver__expand-btn" x-on:click="expandBlockInserter = !expandBlockInserter">
                        <span x-show="! expandBlockInserter">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>

                            Expand
                        </span>
                        <span x-show="expandBlockInserter">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                            </svg>

                            Collapse
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <template x-if="editing">
            <div class="paver__section" style="position: relative;">

                <div class="paver__section-header">
                    <div x-text="editingBlock.name"></div>
                    <button type="button" @click="exitEditMode" x-tooltip="Exit edit mode" class="paver__btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="paver__component-sidebar paver__section-content" x-ref="componentSidebar">
                    <div class="paver__inside"></div>
                </div>
            </div>
        </template>
    </div>

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

    <?php echo paver()->loadAssetContent('/js/paver.js'); ?>
</script>

<script>
    const resizer = document.querySelector('.paver__resizer');
    const sidebar = document.querySelector('.paver__sidebar');
    const iframeOverlay = document.querySelector('.paver__iframe-overlay');
    const iframeWrapper = document.querySelector('.paver__iframe-wrapper');

    let startX, startWidth;

    resizer.addEventListener('mousedown', (e) => {
        startX = e.clientX;
        startWidth = parseInt(document.defaultView.getComputedStyle(sidebar).width, 10);
        iframeOverlay.style.display = 'block'; // Show the overlay
        document.documentElement.addEventListener('mousemove', doDrag, false);
        document.documentElement.addEventListener('mouseup', stopDrag, false);
    });

    function doDrag(e) {
        const minWidth = parseInt(getComputedStyle(iframeWrapper).minWidth, 10);
        const maxSidebarWidth = window.innerWidth - minWidth;
        let newWidth = startWidth + startX - e.clientX;

        if (newWidth > maxSidebarWidth) {
            newWidth = maxSidebarWidth;
        }

        sidebar.style.width = newWidth + 'px';
    }

    function stopDrag() {
        iframeOverlay.style.display = 'none'; // Hide the overlay
        document.documentElement.removeEventListener('mousemove', doDrag, false);
        document.documentElement.removeEventListener('mouseup', stopDrag, false);
    }
</script>
