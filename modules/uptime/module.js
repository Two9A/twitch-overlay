(() => {
    window.addEventListener('load', () => {
        let uptime = 0;
        const pad = (n) => (((""+n).length < 2) ? "0"+n : n);
        setInterval(() => {
            uptime++;
            document.getElementById('uptime').innerText = [
                pad(0|(uptime / 3600)),
                pad(0|((uptime % 3600) / 60)),
                pad(0|(uptime % 60))
            ].join(':');
        }, 1000);
    });
})();
