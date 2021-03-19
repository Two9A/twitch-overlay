(() => {
    let eventid = 0;
    const handle = (msg) => {
        let output;
        switch (msg.content.subscription.type) {
            case 'channel.follow':
                output = `<strong>${msg.content.event.user_login}</strong> has pressed the Follow butan`;
                break;
        }
        if (output) {
            const curr_eventid = ++eventid;
            const li = document.createElement('li');
            const li_id = 'event_' + curr_eventid;
            li.innerHTML = output;
            li.id = li_id;
            document.getElementById('events-list').appendChild(li);
            setTimeout(() => {
                document.getElementById(li_id).style.right = '0px';
            }, 500);
            slideOut(li_id, 5000);
        }
    };
    window.addEventListener('load', () => {
        ws.addEventListener('message', (e) => {
            const msg = JSON.parse(e.data);
            if (msg.type === 'EVENT') {
                handle(msg);
            }
        });
    });
})();
