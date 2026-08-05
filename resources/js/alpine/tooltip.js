import tippy from 'tippy.js'

document.addEventListener('alpine:init', () => {
    Alpine.directive('paver-tooltip', (el, { expression }, { effect, evaluate, cleanup }) => {
        let instance = null

        effect(() => {
            const content = evaluate(expression)

            if (instance) {
                instance.setContent(content)

                return
            }

            // Theme styles live in _tooltip.css. zIndex clears the WP admin
            // bar (99999) and the expanded editor overlay (100000).
            instance = tippy(el, {
                content,
                theme: 'paver',
                animation: 'fade',
                duration: [120, 80],
                arrow: true,
                offset: [0, 6],
                zIndex: 1000001,
                appendTo: () => document.body,
            })
        })

        cleanup(() => {
            instance?.destroy()
            instance = null
        })
    })
})
