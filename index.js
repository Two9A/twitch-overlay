let uptime = 0;
let eventid = 0;
let ws;

const ping = () => {
    ws.send(JSON.stringify({type: 'PING'}));
}
const handle = (msg) => {
    let output;
    switch (msg.content.subscription.type) {
        case 'channel.follow':
            output = `<strong>${msg.content.event.user_login}</strong> has pressed the Follow butan`;
            break;
    }
    if (output) {
        const curr_eventid = eventid;
        const li = document.createElement('li');
        const li_id = 'event_' + curr_eventid;
        li.innerHTML = output;
        li.id = li_id;
        document.getElementById('events').appendChild(li);
        setTimeout(() => {
            document.getElementById(li_id).style.right = '0px';
        }, 500);
        setTimeout(() => {
            document.getElementById(li_id).style.right = '-1280px';
        }, 5000);
        setTimeout(() => {
            document.getElementById(li_id).remove();
        }, 7000);
    }
};
const connect = () => {
    let pingHandle;
    ws = new WebSocket(window.__CONFIG.wsUri);
    ws.onopen = () => {
        ping();
        pingHandle = setInterval(ping, 60000);
    };
    ws.onclose = () => {
        clearInterval(pingHandle);
        setTimeout(connect, 3000);
    };
    ws.onmessage = (e) => {
        const msg = JSON.parse(e.data);
        if (msg.type) {
            switch (msg.type) {
                case 'RECONNECT':
                    setTimeout(connect, 3000);
                    break;
                case 'EVENT':
                    handle(msg);
                    break;
            }
        }
    };
};

window.onload = () => {
    const pad = (n) => (((""+n).length < 2) ? "0"+n : n);
    setInterval(() => {
        uptime++;
        document.getElementById('uptime').innerText = [
            pad(0|(uptime / 3600)),
            pad(0|((uptime % 3600) / 60)),
            pad(0|(uptime % 60))
        ].join(':');
    }, 1000);
    connect();
};
