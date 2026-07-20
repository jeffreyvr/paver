<div x-cloak class="paver__sidebar-backdrop" x-show="sidebarOpen" @click="sidebarOpen = false"></div>
<div class="paver__sidebar" :class="sidebarOpen ? 'paver__is-open' : ''">
    <div class="paver__resizer"></div>
    <div class="paver__sticky">
        <div class="paver__sidebar-tabs">
            <button type="button"
                :class="sidebarPane === 'blocks' ? 'paver__is-active' : ''"
                x-on:click="sidebarPane = 'blocks'"
                x-text="text('Blocks')"></button>
            <button type="button"
                :class="sidebarPane === 'edit' ? 'paver__is-active' : ''"
                :disabled="! editing"
                x-on:click="sidebarPane = 'edit'"
                x-text="text('Edit block')"></button>
        </div>

        <div class="paver__section" x-show="sidebarPane === 'blocks'">
            <div class="paver__section-content">
                <div x-cloak x-show="blocks.length > 0" class="paver__option">
                    <input type="text" x-model="blockInserter.search" :placeholder="text('Search blocks')" class="paver__search-blocks">
                </div>
                <div x-cloak x-show="blocks.length === 0">
                    Whoops, we don't have any blocks (yet)!
                </div>
                <div x-ref="blocksInserter" class="paver__block-grid paver__sortable">
                    <?php foreach(paver()->blocks(withInstance: true) as $block): ?>
                        <div class="paver__sortable-item paver__block-handle <?php echo ($block['instance']->asChildOnly()) ? 'paver__hide_from_block_inserter' : ''; ?>"
                            data-block="<?php echo htmlentities($block['instance']->toJson(['block', 'name']), ENT_QUOTES, 'UTF-8'); ?>">
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

        <div class="paver__section" x-show="sidebarPane === 'edit'">
            <div class="paver__section-header">
                <div x-text="editingBlock ? editingBlock.name : text('Edit block')"></div>
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
    </div>
</div>
