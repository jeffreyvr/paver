const Shortcuts = {
    revert(callback) {
        document.addEventListener('keydown', (event) => {
            if (event.metaKey && event.key === 'z') {
                event.preventDefault()

                callback()
            }
        })
    },

    expand(callback) {
        document.addEventListener('keydown', (event) => {
            if (event.ctrlKey && event.altKey && event.shiftKey && event.key === 'M') {
                event.preventDefault()

                callback()
            }
        })
    },

    exit(callback) {
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                event.preventDefault()

                callback()
            }
        })
    }
}

export default Shortcuts
