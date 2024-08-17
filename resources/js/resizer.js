const resizer = document.querySelector('.paver__resizer');
const sidebar = document.querySelector('.paver__sidebar');
const iframeOverlay = document.querySelector('.paver__iframe-overlay');
const iframeWrapper = document.querySelector('.paver__iframe-wrapper');

let startX, startWidth;

resizer.addEventListener('mousedown', (e) => {
    startX = e.clientX;
    startWidth = parseInt(document.defaultView.getComputedStyle(sidebar).width, 10);
    iframeOverlay.style.display = 'block';
    document.documentElement.addEventListener('mousemove', doDrag, false);
    document.documentElement.addEventListener('mouseup', stopDrag, false);
});

function doDrag(e) {
    const minWidth = parseInt(getComputedStyle(iframeWrapper).minWidth, 10);
    const maxSidebarWidth = window.innerWidth - minWidth;
    let newWidth = startWidth + startX - e.clientX;

    if (newWidth > maxSidebarWidth) {
        newWidth = maxSidebarWidth;
    }

    sidebar.style.width = newWidth + 'px';
}

function stopDrag() {
    iframeOverlay.style.display = 'none';
    document.documentElement.removeEventListener('mousemove', doDrag, false);
    document.documentElement.removeEventListener('mouseup', stopDrag, false);
}
