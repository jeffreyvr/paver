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
            <div class="paver__editor-actions">
                <div x-cloak :class="loading ? 'paver__flex paver__items-center' : 'paver__hidden'">
                    <svg class="paver__loading-spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                <div x-cloak x-show="buttons.viewButton" class="paver__hide-on-mobile">
                    <button x-show="view === 'desktop'" type="button" @click="setView('mobile')" class="paver__btn-icon" x-paver-tooltip="text('Change to mobile')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                    </button>
                    <button x-show="view === 'mobile'" type="button" @click="setView('desktop')" class="paver__btn-icon" x-paver-tooltip="text('Change to desktop')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                        </svg>
                    </button>
                </div>

                <div x-cloak class="paver__hide-on-mobile" x-show="buttons.expandButton">
                    <button type="button" @click="toggleExpand" x-show="!expanded" class="paver__btn-icon" x-paver-tooltip="text('Expand')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                        </svg>
                    </button>
                    <button type="button" @click="toggleExpand" x-show="expanded" class="paver__btn-icon" x-paver-tooltip="text('Minimize')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25" />
                        </svg>
                    </button>
                </div>

                <button x-cloak type="button" x-show="buttons.saveButton" x-on:click="save" class="paver__btn-text paver__btn-text-primary">
                    Save
                </button>
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
        <div class="paver__sticky">
            <div class="paver__section">
                <div class="paver__section-header" x-text="text('Blocks')"></div>
                <div class="paver__section-content">
                    <div x-cloak x-show="blocks.length > 0" class="paver__option">
                        <input type="text" x-model="blockInserter.search" :placeholder="text('Search blocks')" class="paver__search-blocks">
                    </div>
                    <div x-cloak x-show="blocks.length === 0">
                        Whoops, we don't have any blocks (yet)!
                    </div>
                    <div x-ref="blocksInserter" class="paver__block-grid paver__sortable">
                        <?php foreach(paver()->blocks() as $block) : ?>
                            <div class="paver__sortable-item paver__block-handle" data-block="<?php echo $block['reference']; ?>">
                                <span><?php echo $block['icon']; ?></span>
                                <span><?php echo $block['name']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div x-show="blockInserter.showExpandButton">
                        <button type="button" class="paver__expand-btn" x-on:click="blockInserter.showAll = !blockInserter.showAll">
                            <span x-show="! blockInserter.showAll">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Expand
                            </span>
                            <span x-show="blockInserter.showAll">
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
                <div class="paver__section">
                    <div class="paver__section-header">
                        <div x-text="editingBlock.name"></div>
                        <button type="button" @click="exitEditMode" x-paver-tooltip="text('Exit edit mode')" class="paver__btn-icon">
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
    });
</script>

<script>
    window.__paver_start_alpine = <?php echo paver()->alpine ? 'true' : 'false'; ?>;

    <?php echo paver()->loadAssetContent('/js/paver.js'); ?>
</script>
