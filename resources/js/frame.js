import Alpine from 'alpinejs'
import morph from '@alpinejs/morph'
import helpers from './helpers.js'
import tippy from 'tippy.js'
import 'tippy.js/dist/tippy.css'
import ApiClient from './apiClient.js'

window.tippy = tippy

Alpine.plugin(morph)

window.Alpine = Alpine

window.PaverFrame = function (data) {
    return {
        blocks: [],

        editingBlock: null,

        editingElement: null,

        api: new ApiClient(data.api),

        init() {
            this.renderEditingBlock()

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

        revert(event) {
            if (event.metaKey && event.key === 'z') {
                event.preventDefault()

                helpers.dispatchToParent('revert', {})
            }
        },

        trash(e) {
            e.preventDefault()

            if (!confirm('Are you sure you want to delete this block?')) {
                return
            }

            let target = e.currentTarget.parentNode.parentNode
            let block = target.getAttribute('data-id')

            console.log(target, block)

            helpers.dispatchToParent('delete', JSON.parse(JSON.stringify(block)))
        },

        clone(e) {
            let target = e.currentTarget.parentNode.parentNode
            let block = JSON.parse(target.getAttribute('data-block'))

            helpers.dispatchToParent('clone', JSON.parse(JSON.stringify({ blockHtml: target.outerHTML })))
        },

        async fetchOptions(e) {
            let target = e.currentTarget.parentNode.parentNode
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

if(window.__paver_start_alpine) {
    console.log('[PAVER] Starting Alpine.js from frame')

    Alpine.start()
}
