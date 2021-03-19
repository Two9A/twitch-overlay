const slideOut = (elId, waitTime) => {
    if (document.getElementById(elId)) {
        setTimeout(() => {
            document.getElementById(elId).style.right = '-1280px';
        }, waitTime);
        setTimeout(() => {
            document.getElementById(elId).remove();
        }, waitTime + 2000);
    }
};
const ws = new WebSocket(window.__CONFIG.wsUri);
const ping = () => {
    ws.send(JSON.stringify({type: 'PING'}));
}
window.addEventListener('load', () => {
    let pingHandle;
    ws.onopen = () => {
        ping();
        pingHandle = setInterval(ping, 60000);
    };
    ws.onclose = () => {
        clearInterval(pingHandle);
        setTimeout(connect, 3000);
    };
});
