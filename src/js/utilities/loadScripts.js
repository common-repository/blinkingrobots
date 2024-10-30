import DeferJS from "./DeferJS";

const {theme_version} = window.blinkingrobots;
const verEnding = `?ver=${theme_version}`;

export default function () {
    let scriptsToLoad = [];

    let scripts_to_enqueue = window.blinkingrobots && window.blinkingrobots.scripts_to_enqueue ? window.blinkingrobots.scripts_to_enqueue : 'undefined';
    if (typeof scripts_to_enqueue !== 'undefined' && scripts_to_enqueue) {
        for (const [key, data] of Object.entries(scripts_to_enqueue)) {
            if (data) {
                let formattedData = {
                    src: data.src,
                    el: data.elSelector ? document.querySelector(data.elSelector) : document.body,
                    insertInto: data.insertInto && data.insertInto === 'document.head' ? document.head : document.body,
                    insertPosition: data.insertPosition ? data.insertPosition : 'end',
                    dependencies: data.dependencies ? data.dependencies : [],
                };
                if (data.hasOwnProperty('loadImmediatelyIfVisible')) {
                    formattedData.loadImmediatelyIfVisible = data.loadImmediatelyIfVisible;
                }
                if (data.hasOwnProperty('handle')) {
                    formattedData.handle = data.handle;
                }
                if (data.hasOwnProperty('atts')) {
                    formattedData.atts = data.atts;
                }
                if (data.hasOwnProperty('timeout')) {
                    formattedData.timeout = data.timeout;
                }
                if (data.hasOwnProperty('callback')) {
                    formattedData.callback = data.callback;
                }
                if (data.hasOwnProperty('callBefore')) {
                    formattedData.callBefore = data.callBefore;
                }
                scriptsToLoad.push(formattedData);
            }
        }
    }

    scriptsToLoad.forEach((el) => {
        new DeferJS(el);
    });
}
