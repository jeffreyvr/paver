const helpers = {
    dispatchToFrame(frame, name, data) {
        frame.contentWindow.postMessage({ type: 'paver' + '.' + name, message: data }, '*');
    },

    dispatchToParent(name, data = null) {
        window.parent.postMessage({ type: 'paver' + '.' + name, message: data }, '*');
    },

    listenFromFrame(name, callback) {
        window.addEventListener('message', (event) => {
            if (event.data.type === 'paver' + '.' + name) {
                callback(event.data.message);
            }
        });
    },

     log(...args) {
        let type = 'log';

        if (typeof args[args.length - 1] === 'string' && ['log', 'info', 'warn', 'error'].includes(args[args.length - 1])) {
            type = args.pop();
        }

        console[type]('[PAVER]', ...args);
    }
}

export default helpers;
