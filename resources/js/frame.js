import Localization from './localization.js'
import ApiClient from './apiClient.js'
import Shortcuts from './shortcuts.js'
import morph from '@alpinejs/morph'
import helpers from './helpers.js'
import 'tippy.js/dist/tippy.css'
import Alpine from 'alpinejs'
import tippy from 'tippy.js'
import './alpine/tooltip.js'

Alpine.plugin(morph)

window.PaverFrame = function (data) {
    return {
        ...Localization,

        locale: data.locale,

        texts: data.texts,

        blocks: [],

        editingBlock: null,

        editingElement: null,

        api: new ApiClient(data.api),

        init() {
            this.renderEditingBlock()

            Shortcuts.revert(() => this.revert())
            Shortcuts.expand(() => this.expand())
            Shortcuts.exit(() => this.exit())

            document.addEventListener('loading', () => helpers.dispatchToParent('loading'))
            document.addEventListener('loaded', () => helpers.dispatchToParent('loaded'))

            helpers.listenFromFrame('updateEditingBlock', (block) => {
                this.editingBlock = block
            })

            this.$watch('blocks', (value) => {
                helpers.dispatchToParent('blocks', JSON.parse(JSON.stringify(value)))
            })

            const resizeObserver = new ResizeObserver(entries => {
                for (let entry of entries) {
                    if (entry.target === document.body) {
                        helpers.dispatchToParent('height', document.body.scrollHeight)
                    }
                }
            });

            resizeObserver.observe(document.body)
        },

        exit() {
            helpers.dispatchToParent('exit', {})
        },

        expand() {
            helpers.dispatchToParent('expand', {})
        },

        revert(event) {
            helpers.dispatchToParent('revert', {})
        },

        trash(e) {
            e.preventDefault()

            if (!confirm(this.text('Are you sure you want to delete this block?'))) {
                return
            }

            let target = e.currentTarget.parentNode.parentNode
            let block = target.getAttribute('data-id')

            helpers.dispatchToParent('delete', JSON.parse(JSON.stringify(block)))
        },

        clone(e) {
            let target = e.currentTarget.parentNode.parentNode
            // let block = JSON.parse(target.getAttribute('data-block'))

            helpers.dispatchToParent('clone', JSON.parse(JSON.stringify({ blockHtml: target.outerHTML })))
        },

        async edit(e) {
            let target = e.currentTarget.parentNode.parentNode

            if (target.classList.contains('paver__active-block')) {
                return;
            }

            let block = JSON.parse(target.getAttribute('data-block'))

            document.querySelectorAll('.paver__active-block').forEach((el) => {
                el.classList.remove('paver__active-block')
            })

            target.classList.add('paver__active-block')

            try {
                const response = await this.api.fetchBlockOptions(block)

                helpers.dispatchToParent('editingBlock', {
                    html: response.optionsHtml,
                    name: response.name,
                    block: { ...{ render: false }, ...JSON.parse(JSON.stringify(block)) }
                })

                this.editingElement = target

            } catch (error) {
                console.log('error', 'Error fetching options:', error)
            }
        },

        async renderEditingBlock() {
            this.$watch('editingBlock', (value) => {
                this.debouncedFetchAndUpdate()
            })

            this.debouncedFetchAndUpdate = Alpine.debounce(async () => {
                let editingBlock = JSON.parse(JSON.stringify(this.editingBlock))

                // Rebuild the child blocks in case they have changed.
                function getBlocks(element) {
                    let blocks = []

                    Array.from(element.children).forEach((el) => {
                        if (el.hasAttribute('data-block')) {
                            let block = JSON.parse(el.getAttribute('data-block'))
                            block.children = getBlocks(el)
                            blocks.push(block)
                        } else {
                            let nestedBlocks = getBlocks(el)

                            blocks = blocks.concat(nestedBlocks)
                        }
                    })

                    return blocks
                }

                if (this.editingElement.querySelectorAll('[data-block]').length > 0) {
                    let children = getBlocks(this.editingElement)
                    editingBlock.children = children
                }

                const response = await this.api.renderBlock(editingBlock)

                this.editingElement.removeAttribute('x-show')

                let currentBlock = JSON.parse(this.editingElement.getAttribute('data-block'))

                currentBlock.data = response.data

                Alpine.morph(this.editingElement, response.render)
                const newElement = document.createElement('div')
                newElement.innerHTML = response.render

                this.editingElement.innerHTML = newElement.querySelector('[data-block]').innerHTML

                this.editingElement.setAttribute('data-block', JSON.stringify(currentBlock))

                helpers.dispatchToParent('update', {})
            }, 1000)
        },

        updateBlockById(id, newData) {
            const updateRecursive = (items) => {
                for (let item of items) {
                    if (item.id === id) {
                        Object.keys(newData).forEach(key => {
                            item[key] = newData[key]
                        })

                        return true
                    }
                    if (item.children) {
                        const updated = updateRecursive(item.children)
                        if (updated) return true
                    }
                }
                return false
            }

            updateRecursive(this.blocks)
        }
    }
}

if (!window.tippy) {
    window.tippy = tippy
}

if (!window.Alpine) {
    window.Alpine = Alpine
}

if (window.__paver_start_alpine) {
    console.log('[PAVER] Starting Alpine.js from frame')

    Alpine.start()
}
