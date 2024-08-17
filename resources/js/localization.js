const Localization = {
    text(text) {
        if (this.texts && this.texts[text]) {
            return this.texts[text]
        }

        return text
    },
}

export default Localization
