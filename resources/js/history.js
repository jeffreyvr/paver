const History = {
    entries: [],

    add(entry) {
        this.entries.push(entry)
    },

    revert() {
        this.entries.pop()
    },

    last() {
        return this.entries[this.entries.length - 1]
    },

    clear() {
        this.entries = []
    },

    get() {
        return this.entries
    }
}

export default History
