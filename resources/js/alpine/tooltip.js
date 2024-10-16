document.addEventListener('alpine:init', () => {
    Alpine.directive('paver-tooltip', (el, { expression }, { effect, evaluate }) => {
        const content = evaluate(expression);

        effect(() => {
            tippy(el, { content: content });
        });
    });
});
