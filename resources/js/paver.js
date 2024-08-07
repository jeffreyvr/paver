import tippy from 'tippy.js'
import Alpine from 'alpinejs'
import morph from '@alpinejs/morph'
import helpers from './helpers.js'
import Sortable from 'sortablejs'
import History from './history.js'
import ApiClient from './apiClient.js'
import 'tippy.js/dist/tippy.css'

Alpine.plugin(morph)

window.Alpine = Alpine

window.tippy = tippy

window.Paver = function (data) {
    return {
        api: new ApiClient(data.api),

        view: data.view,

        blocks: data.blocks,

        content: data.content,

        allowedBlocks: [],

        expandBlockInserter: false,

        editingBlock: null,

        edited: false,

        frame: null,

        search: '',

        history: History,

        debug: true,

        expanded: false,

        editing: false,

        showExpandButton: data.showExpandButton ?? true,

        showViewButton: data.showViewButton ?? true,

        log(...args) {
            if (!this.debug) {
                return
            }

            helpers.log(...args)
        },

        handleRevertShortcut(event) {
            if (event.metaKey && event.key === 'z') {
                event.preventDefault()

                this.revert()
            }
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

            this.root().querySelector('.paver__active-block').querySelectorAll('.paver__sortable').forEach(element => {
                let elementAllowedBlocks = JSON.parse(element.getAttribute('data-allow-blocks'))
                this.allowedBlocks = [...this.allowedBlocks, ...elementAllowedBlocks]
            })

            this.log('Blocks allowed in this block:', this.allowedBlocks)
        },

        init() {
            this.waitForFrame()

            this.listeners()

            this.watchers()

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
                onClone: function (/**Event*/evt) {
                    evt.item.removeAttribute('x-show')
                },
            })
        },

        listeners() {
            helpers.listenFromFrame('blocks', (blocks) => this.content = JSON.stringify(blocks))

            helpers.listenFromFrame('exit', (event) => this.handleEscape())

            helpers.listenFromFrame('revert', (event) => this.revert())

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
                    this.$refs.componentSidebar.innerHTML = event.html
                    this.edited = true

                    this.record()
                })
            })

            helpers.listenFromFrame('update', (event) => {
                this.log('Updating editor content')

                let nestedSortables = this.root().querySelectorAll('.paver-sortable')

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
            this.$watch('search', value => {
                this.$refs.blocksInserter.querySelectorAll('.paver__sortable-item').forEach((el) => {
                    const blockName = el.getAttribute('data-block')
                    const isChildBlockOnly = el.hasAttribute('data-child-block-only')

                    let shouldDisplay = false
                    if (this.allowedBlocks.length > 0) {
                        shouldDisplay = this.allowedBlocks.includes(blockName)
                    } else {
                        shouldDisplay = !isChildBlockOnly
                    }

                    if (shouldDisplay && el.innerText.toLowerCase().includes(value.toLowerCase())) {
                        el.style.display = 'flex'
                    } else {
                        el.style.display = 'none'
                    }
                })

            })

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

            this.$watch('expandBlockInserter', value => {
                document.querySelector('.paver__block-inserter-wrapper').classList.toggle('expanded', value)
            })
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
                }
            }, 100)
        },

        record() {
            this.history.add({
                root: this.root().outerHTML,
                editingBlock: JSON.parse(JSON.stringify(this.editingBlock))
            })

            this.log('Recorded entry in history', this.history)
        },

        frameInit() {
            this.record()

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
                onAdd: (evt) => this.fetchBlock(evt),
                onEnd: (evt) => this.rebuildContent()
            })

            let nestedSortables = this.root().querySelectorAll('.paver__sortable')

            nestedSortables.forEach(element => {
                this.initNestedSoratable(element)
            })

            this.hoveringStates()

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

                        let draggedBlock = draggedElement.getAttribute('data-block').trim().toLowerCase()

                        return allowedBlocks.some(block => block.trim().toLowerCase() === draggedBlock)
                    }
                },
                handle: '.paver__block-handle',
                direction: element.getAttribute('data-direction') || 'vertical',
                animation: 150,
                onAdd: (evt) => this.fetchBlock(evt),
                onEnd: (evt) => this.rebuildContent()
            })
        },

        frameHeightManager() {
            const iframeBodyHeight = this.$refs.editor.contentWindow.document.body.scrollHeight;

            document.querySelector('iframe').style.height = iframeBodyHeight + 'px'

            helpers.listenFromFrame('height', (height) => {
                this.log('Setting editor height to', height);

                this.$refs.editor.style.height = height + 'px'
            })

            // let lastHeight = iframeBody.scrollHeight;

            // const ro = new ResizeObserver(entries => {
            //     for (let entry of entries) {
            //         if (entry.target === iframeBody) {
            //             const newHeight = iframeBody.scrollHeight;
            //             if (newHeight !== lastHeight) {
            //                 console.log('changing...');
            //                 this.$refs.editor.style.height = `${newHeight}px`;
            //                 lastHeight = newHeight;
            //             }
            //         }
            //     }
            // });

            // ro.observe(iframeBody)

            // this.$watch('expanded', value => {
            //     if (value) {
            //         ro.unobserve(iframeBody)
            //         this.$refs.editor.style.height = '100%'
            //     } else {
            //         ro.observe(iframeBody)
            //         this.$refs.editor.style.height = `${iframeBody.scrollHeight}px`
            //     }
            // })
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

            this.hoveringStates()
        },

        async fetchBlock(evt) {
            const block = evt.item.getAttribute('data-block')

            try {
                const response = await this.api.fetchBlock(block, this.api.payload)

                console.log(response);

                evt.item.setAttribute('data-block', JSON.stringify({ block, data: response.data }))
                evt.item.setAttribute('data-id', response.id)

                Alpine.morph(evt.item, response.render)

                this.hoveringStates()

                evt.item.querySelectorAll('.paver__sortable').forEach(element => {
                    this.log('Nested sortable found, initializing', element)

                    this.initNestedSoratable(element)
                })

                this.rebuildContent()

                this.record()
            } catch (error) {
                this.log('error', 'Error fetching options:', error)
            }
        }
    }
}

if(window.__paver_start_alpine) {
    console.log('[PAVER] Starting Alpine.js')

    Alpine.start()
}
