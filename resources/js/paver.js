import Localization from './localization.js'
import Shortcuts from './shortcuts.js'
import ApiClient from './apiClient.js'
import morph from '@alpinejs/morph'
import History from './history.js'
import helpers from './helpers.js'
import Sortable from 'sortablejs'
import 'tippy.js/dist/tippy.css'
import Alpine from 'alpinejs'
import tippy from 'tippy.js'
import './alpine/tooltip.js'
import './resizer.js'

Alpine.plugin(morph)

window.Paver = function (data) {
    return {
        ...Localization,

        breadcrumb: [],

        texts: data.texts,

        api: new ApiClient(data.api),

        view: data.view,

        blocks: data.blocks,

        content: data.content,

        allowedBlocks: [],

        editingBlock: null,

        edited: false,

        frame: null,

        history: History,

        debug: data.debug ?? false,

        expanded: false,

        editing: false,

        loading: true,

        blockInserter: {
            search: '',
            showAll: false,
            limit: data.blockInserterLimit ?? 6,
            showExpandButton: false
        },

        buttons: {
            expandButton: data.showExpandButton ?? true,
            viewButton: data.showViewButton ?? true,
            saveButton: data.showSaveButton ?? false
        },

        allowRootDrop: false,

        log(...args) {
            if (!this.debug) {
                return
            }

            helpers.log(...args)
        },

        save() {
            this.$dispatch('paver-save', {content: this.content})
        },

        revert() {
            if (this.history.get().length > 1 === false) {
                return
            }

            this.history.revert()

            let last = this.history.last()

            this.log('Restoring =', last)

            if (last.editingBlock === null) {
                this.exitEditMode()
            } else {
                this.editingBlock = last.editingBlock

                if(last.componentSidebar) {
                    this.$refs.componentSidebar.innerHTML = last.componentSidebar
                }
            }

            Alpine.morph(this.root(), last.root)

            this.$nextTick(() => {
                this.rebuildContent()
            })
        },

        handleEscape() {
            if (this.expanded && this.editingBlock === null) {
                this.toggleExpand()

                return
            }

            this.exitEditMode()
        },

        exitEditMode() {
            this.editing = false

            this.frame.querySelectorAll('.paver__active-block').forEach((el) => el.classList.remove('paver__active-block'))

            this.$nextTick(() => {
                this.editingBlock = null
            })
        },

        setView(view) {
            this.view = view
        },

        toggleExpand() {
            this.expanded = !this.expanded

            document.querySelector('body').classList.toggle('paver__expanded-editor')
        },

        blockChange(event) {
            this.editingBlock.data[event.detail.key] = event.detail.value
            this.log('Block change', event)
        },

        determineAllowedBlocks() {
            this.allowedBlocks = []

            let element = this.root().querySelector('.paver__active-block .paver__sortable')

            if(! element) {
                return
            }

            if(! element.hasAttribute('data-allow-blocks')) {
                return
            }

            let elementAllowedBlocks = JSON.parse(element.getAttribute('data-allow-blocks'))

            this.allowedBlocks = [...this.allowedBlocks, ...elementAllowedBlocks]

            this.log('Blocks allowed in this block:', this.allowedBlocks)
        },

        init() {
            this.waitForFrame()

            this.listeners()

            this.determineVisibleInsertableBlocks()

            this.watchers()

            Shortcuts.revert(() => this.revert())
            Shortcuts.expand(() => this.toggleExpand())
            Shortcuts.exit(() => this.handleEscape())

            Sortable.create(this.$refs.blocksInserter, {
                ghostClass: "paver__sortable-ghost",
                chosenClass: "paver__sortable-chosen",
                dragClass: "paver__sortable-drag",
                group: {
                    name: 'shared',
                    pull: 'clone',
                    put: false,
                    revertClone: false
                },
                sort: false,
                animation: 150,
                onStart: (evt) => {
                    evt.clone.setAttribute('x-ignore', '')
                },
            })
        },

        listeners() {
            helpers.listenFromFrame('blocks', (blocks) => this.content = JSON.stringify(blocks))

            helpers.listenFromFrame('exit', (event) => this.handleEscape())

            helpers.listenFromFrame('revert', (event) => this.revert())

            helpers.listenFromFrame('expand', (event) => this.toggleExpand())

            Shortcuts.selectParentBlock(() => {
                helpers.dispatchToFrame(this.$refs.editor, 'selectParentBlock')
            })

            helpers.listenFromFrame('loading', () => this.isLoading())
            helpers.listenFromFrame('loaded', () => this.isLoaded())

            helpers.listenFromFrame('updateBreadcrumb', (event) => this.breadcrumb = event.breadcrumb)

            document.addEventListener('loading', () => this.isLoading())
            document.addEventListener('loaded', () => this.isLoaded())

            helpers.listenFromFrame('editingBlock', (event) => {
                this.edited = false
                this.editing = true

                this.editingBlock = {
                    name: event.name,
                    ...event.block
                }

                this.log('Editing block from frame')

                this.determineAllowedBlocks()

                this.$nextTick(() => {
                    this.$refs.componentSidebar.querySelector('.paver__inside').innerHTML = event.html
                    this.edited = true

                    this.record()
                })
            })

            helpers.listenFromFrame('update', (event) => {
                this.log('Updating editor content')

                let nestedSortables = this.root().querySelectorAll('.paver__sortable')

                nestedSortables.forEach(element => {
                    this.initNestedSoratable(element)
                })

                this.rebuildContent()

                this.record()
            })

            helpers.listenFromFrame('delete', (block) => {
                this.log('Deleting block')

                let blockElement = this.root().querySelector('[data-id="' + block + '"]')

                blockElement.remove()

                this.$nextTick(() => {
                    this.exitEditMode()

                    this.rebuildContent()

                    this.record()
                })
            })

            helpers.listenFromFrame('clone', (block) => {
                this.log('Cloning block')

                let newBlock = document.createElement('div')
                newBlock.innerHTML = block.blockHtml

                this.root().appendChild(newBlock.firstElementChild)

                this.exitEditMode()

                this.rebuildContent()

                this.record()
            })
        },

        watchers() {
            this.$watch('editingBlock', value => {
                if (value === null) {
                    this.allowedBlocks = []

                    return
                }

                if (!this.edited) {
                    return
                }

                this.log('Updating editing block')

                helpers.dispatchToFrame(this.$refs.editor, 'updateEditingBlock', JSON.parse(JSON.stringify(value)))
            })

            this.$watch('blockInserter.search', () => {
                this.determineVisibleInsertableBlocks()
            })

            this.$watch('blockInserter.limit', () => {
                this.determineVisibleInsertableBlocks()
            })

            this.$watch('blockInserter.showAll', () => {
                this.determineVisibleInsertableBlocks()
            })

            this.$watch('allowedBlocks', () => {
                this.determineVisibleInsertableBlocks()
            })
        },

        determineVisibleInsertableBlocks() {
            const searchTerm = this.blockInserter.search.trim().toLowerCase()
            const allowedBlocks = this.allowedBlocks.length ? this.allowedBlocks : null
            const blocks = this.$refs.blocksInserter.querySelectorAll('.paver__block-handle')

            let visibleCount = 0
            let totalVisible = 0

            blocks.forEach(block => {
                const blockData = JSON.parse(block.getAttribute('data-block'))
                const blockName = blockData.block.trim().toLowerCase()
                const isHidden = block.classList.contains('paver__hide_from_block_inserter')
                const matchesSearch = !searchTerm || blockName.includes(searchTerm)
                const isAllowed = !allowedBlocks || allowedBlocks.includes(blockName)
                const withinLimit = this.blockInserter.showAll || visibleCount < this.blockInserter.limit

                if (isHidden && (!allowedBlocks || !allowedBlocks.includes(blockName))) {
                    block.style.display = 'none'
                    return
                }

                if (matchesSearch && isAllowed) {
                    totalVisible++
                    block.style.display = withinLimit ? 'flex' : 'none'
                    if (withinLimit) visibleCount++
                } else {
                    block.style.display = 'none'
                }
            })

            this.blockInserter.showExpandButton = totalVisible > this.blockInserter.limit
        },

        root() {
            return this.frame.querySelector('.paver__editor-root')
        },

        waitForFrame() {
            const interval = setInterval(() => {
                this.frame = this.$refs.editor.contentDocument || this.$refs.editor.contentWindow.document

                if (this.root()) {
                    this.log('Found root in frame, initializing sortable')

                    clearInterval(interval)

                    this.frameInit()

                    this.loading = false

                    this.$dispatch('paver-ready')
                }
            }, 100)
        },

        record() {
            let record = {
                root: this.root().outerHTML,
                editingBlock: JSON.parse(JSON.stringify(this.editingBlock)),
            }

            if(this.$refs.componentSidebar) {
                record.componentSidebar = this.$refs.componentSidebar.innerHTML
            }

            this.history.add(record)

            this.log('Recorded entry in history', this.history)
        },

        frameInit() {
            this.record()

            // `allowRootDrop` is a dirty hack as Sortable does not recognize it
            // when an item is first dragged over the frame and then dragged
            // out of it it will still add the item on 'dragend' - yuck.
            this.root().addEventListener("dragenter", () => this.allowRootDrop = true)
            document.querySelector('body').addEventListener("dragenter", () => this.allowRootDrop = false)

            Sortable.create(this.root(), {
                ghostClass: "paver__sortable-ghost",
                chosenClass: "paver__sortable-chosen",
                dragClass: "paver__sortable-drag",
                group: {
                    name: 'shared',
                    put: (to, from, draggedElement) => {
                        if (draggedElement.hasAttribute('data-child-block-only')) {
                            return false
                        }

                        return true
                    },
                },
                animation: 150,
                handle: '.paver__block-handle',
                onAdd: (evt) => {
                    if(! this.allowRootDrop) {
                        evt.item.remove()
                    } else {
                        evt.item.innerHTML = ''
                        this.fetchBlock(evt)
                    }
                },
                onEnd: () => this.rebuildContent()
            })

            let nestedSortables = this.root().querySelectorAll('.paver__sortable')

            nestedSortables.forEach(element => {
                this.initNestedSoratable(element)
            })

            this.hoveringStates()

            this.linkClickWarnings()

            this.frameHeightManager()
        },

        initNestedSoratable(element) {
            Sortable.create(element, {
                ghostClass: "paver__sortable-ghost",
                chosenClass: "paver__sortable-chosen",
                dragClass: "paver__sortable-drag",
                group: {
                    name: 'nested',
                    pull: false,
                    put: true,
                    put: (to, from, draggedElement) => {
                        if (to.el.hasAttribute('data-allow-blocks') === false) {
                            return true
                        }

                        let allowedBlocks = JSON.parse(to.el.getAttribute('data-allow-blocks'))

                        let draggedBlock = JSON.parse(draggedElement.getAttribute('data-block')).block.trim().toLowerCase()

                        return allowedBlocks.some(block => block.trim().toLowerCase() === draggedBlock)
                    }
                },
                handle: '.paver__block-handle',
                direction: element.getAttribute('data-direction') || 'vertical',
                animation: 150,
                onAdd: (evt) => {
                    if(evt.from === this.$refs.blocksInserter) {
                        this.fetchBlock(evt)
                    }
                },
                onEnd: (evt) => this.rebuildContent()
            })
        },

        frameHeightManager() {
            const iframeBodyHeight = this.$refs.editor.contentWindow.document.body.scrollHeight

            document.querySelector('iframe').style.height = iframeBodyHeight + 'px'

            helpers.listenFromFrame('height', (height) => {
                this.log('Setting editor height to', height)

                this.$refs.editor.style.height = height + 'px'
            })
        },

        hoveringStates() {
            this.log('Applying hover states on blocks in editor')

            this.root().addEventListener('mouseover', (e) => {
                const element = e.target.closest('.paver__sortable-item')
                if (element) {
                    element.classList.add('paver__hover-block')
                }
            })

            this.root().addEventListener('mouseout', (e) => {
                const element = e.target.closest('.paver__sortable-item')
                if (element) {
                    element.classList.remove('paver__hover-block')
                }
            })
        },

        linkClickWarnings() {
            let warning = (e) => {
                e.preventDefault()

                var proceed = confirm(this.text('Do you really want to follow this link?'))

                if (proceed) {
                    window.open(link.href, '_blank')
                }

                return
            }

            this.root().querySelectorAll('a').forEach((link) => {
                link.removeEventListener('click', warning)
                link.addEventListener('click', warning)
            })
        },

        rebuildContent() {
            this.log('Rebuilding the content value')

            const gatherBlocks = (list) => {
                let blocks = list.querySelectorAll(':scope > .paver__sortable-item')
                let newBlocks = []

                blocks.forEach(block => {
                    let blockData = JSON.parse(block.getAttribute('data-block'))

                    let childList = block.querySelector('.paver__sortable')

                    if (childList) {
                        blockData.children = gatherBlocks(childList)
                    }

                    newBlocks.push(blockData)
                })

                return newBlocks
            }

            let newBlocks = gatherBlocks(this.root())

            this.content = JSON.stringify(newBlocks)
            this.$dispatch('paver-change', {content: this.content})

            this.hoveringStates()
            this.linkClickWarnings()
        },

        isLoading() {
            this.loading = true
        },

        isLoaded() {
            setTimeout(() => {
                this.loading = false
            }, 100)
        },

        async fetchBlock(evt) {
            const block = JSON.parse(evt.item.getAttribute('data-block'))

            try {
                const response = await this.api.fetchBlock(block.block, this.api.payload)

                const newElement = document.createElement('div')
                newElement.innerHTML = response.render

                newElement.firstElementChild.setAttribute('data-block', JSON.stringify({...block, data: response.data}))
                newElement.firstElementChild.setAttribute('data-id', response.id)

                evt.item.outerHTML = newElement.innerHTML

                let elementInRoot = this.root().querySelector('div[data-id="'+response.id+'"]')

                elementInRoot.querySelectorAll('.paver__sortable').forEach(element => {
                    this.log('Nested sortable found, initializing', element)

                    this.initNestedSoratable(element)
                })

                this.hoveringStates()
                this.linkClickWarnings()

                this.rebuildContent()

                this.record()
            } catch (error) {
                this.log('error', 'Error fetching options:', error)
            }
        }
    }
}

if(! window.tippy) {
    window.tippy = tippy
}

if(! window.Alpine) {
    window.Alpine = Alpine
}

if(window.__paver_start_alpine) {
    console.log('[PAVER] Starting Alpine.js')

    Alpine.start()
}
