import getScrolledBottomCorner from "./getScrolledBottomCorner";
import getOffset from "./getOffset";

const {theme_path} = window.blinkingrobots;

const initiated = [];
const loaded = [];

export default class {

    /**
     * @param src
     * @param insert: 'instant' / 'defer'
     * @param insertInto: document.head / document.body
     * @param insertPosition: 'start' / 'end'
     * @param loadImmediatelyIfVisible :boolean
     * @param atts
     * @param timeout
     * @param handle
     * @param el
     * @param callBefore
     * @param callback
     * @param dependencies
     */
    constructor({
                    src = '',
                    insert = 'defer',
                    insertInto = document.body,
                    insertPosition = 'end',
                    loadImmediatelyIfVisible = true,
                    atts = {},
                    timeout = 0,
                    handle = '',
                    el = null,
                    callBefore = function () {
                    },
                    callback = function () {
                    },
                    dependencies = [],
                }) {
        this.insertInto = insertInto;
        this.insertPosition = insertPosition;
        this.loadImmediatelyIfVisible = loadImmediatelyIfVisible;
        this.handle = handle;
        this.atts = atts;
        this.timeout = timeout;
        this.dependencies = this.formatDependenciesSrc(dependencies);
        this.src = this.getSrc(src);
        this.callBefore = typeof (callBefore) === 'string' ? eval('(' + callBefore + ')') : callBefore;
        this.callback = typeof (callback) === 'string' ? eval('(' + callback + ')') : callback;
        this.el = el;
        this.insertInited = false;

        if (insert === 'instant') {
            this.run_insert(callback, callBefore);
        }

        /**
         * Avoid duplicated including
         */
        if (initiated.includes(src)) {
            return this;
        }

        initiated.push(src);

        if (this.el && insert === 'defer') {
            this.defer();
        }
    }

    getSrc(src) {
        return src[0] === '/' ? `${theme_path}${src}` : src;
    }

    formatDependenciesSrc(dependencies = []) {
        let deps = [];
        dependencies.forEach((data) => {
            data.src = this.getSrc(data.src);
            deps.push(data);
        });
        return deps;
    }

    defer() {
        const _this = this;
        let isVisible = getScrolledBottomCorner() >= getOffset(this.el);
        if (this.loadImmediatelyIfVisible && isVisible) {
            this.run_insert(_this.callback, _this.callBefore);
            return true;
        }

        let insertFunc = function () {
            _this.run_insert(_this.callback(), _this.callBefore)
        };

        jQuery(document).on('firstInteraction', insertFunc);
    }

    insert(callback, callBefore, alreadyLoadedCallback = false) {
        callBefore();
        const scriptEl = document.querySelector(`[src="${this.src}"]`);
        if (scriptEl) {
            if (loaded.includes(this.src)) {
                return callback ? callback() : this.callback();
            }

            if (alreadyLoadedCallback) {
                alreadyLoadedCallback();
            }
            // no need duplicated script loading
            return null;
        }


        if (this.dependencies.length) {
            let syncScriptsArray = [...this.dependencies];
            syncScriptsArray.push({src: this.src});
            this.loadScriptsSync(syncScriptsArray);
        } else {
            this.insertScript(this.src, callback ? callback : this.callback, true);
        }
    }

    run_insert(callback, callBefore, alreadyLoadedCallback = false) {
        if (!this.insertInited) {
            this.insertInited = true;
            if (this.timeout) {
                setTimeout(() => {
                    this.insert(callback, callBefore, alreadyLoadedCallback);
                }, this.timeout);
            } else {
                this.insert(callback, callBefore, alreadyLoadedCallback);
            }
        }
    }

    insertScript(src, callback, triggerFinish = false) {
        if (document.querySelector(`[src="${src}"]`)) {
            callback();
            return true;
        }
        if (!src) {
            return false;
        }
        const element = document.createElement('script');
        element.async = true;
        element.src = src;
        element.onload = () => {
            callback();
            if (triggerFinish) {
                this.triggerFinish();
            }
        };
        element.dataset.loadByDeferJs = '';
        if (this.atts && typeof this.atts === 'object') {
            for (const [key, value] of Object.entries(this.atts)) {
                element.setAttribute(key, value);
            }
        }

        if (this.insertPosition === 'start') {
            this.insertInto.prepend(element);
        } else if (this.insertPosition === 'end') {
            this.insertInto.append(element);
        }
    }

    loadScriptsSync(scripts) {
        const self = this;
        let x = 0;
        let loopArray = function (scripts) {
            // call itself
            self.insertScript(scripts[x].src, function () {
                // set x to next item
                x++;
                // any more items in array?
                if (x < scripts.length) {
                    loopArray(scripts);
                } else {
                    self.callback();
                    self.triggerFinish();
                }
            })
        }
        loopArray(scripts);
    }

    triggerFinish() {
        const handle = this.handle ? this.handle : this.src
        jQuery('body').trigger('DeferJS/' + handle);
    }
};
