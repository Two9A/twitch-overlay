(() => {
    let noteid = 0;
    const handle = (msg) => {
        let output = msg.content;
        let waitTime = 0;
        if (output) {
            if (document.getElementById('footnote_' + noteid)) {
                slideOut('footnote_' + noteid, 0);
                waitTime = 2000;
            }
            let li = document.createElement('li');
            let li_id = 'footnote_' + (++noteid);
            li.innerHTML = output;
            li.id = li_id;
            setTimeout(() => {
                document.getElementById('footnotes-list').appendChild(li);
            }, 500 + waitTime);
            setTimeout(() => {
                document.getElementById(li_id).style.right = '0px';
            }, 550 + waitTime);
        }
    };
    window.addEventListener('load', () => {
        ws.addEventListener('message', (e) => {
            const msg = JSON.parse(e.data);
            if (msg.type === 'NOTE') {
                handle(msg);
            }
        });
    });
})();
