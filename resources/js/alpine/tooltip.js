document.addEventListener('alpine:init', () => {
    Alpine.directive('tooltip', (el, { expression }, { effect, evaluate }) => {
        const content = evaluate(expression);

        effect(() => {
            tippy(el, { content: content });
        });
    });
});
