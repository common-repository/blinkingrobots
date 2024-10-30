import getOffset from "./getOffset";
import getHeaderHeight from "./getHeaderHeight";

const $ = jQuery;

export function getScrollTop() {
    return $(window).scrollTop();
}

export function getElScrollTop(el) {
    return el.getBoundingClientRect().y + document.querySelector('html').scrollTop;
}

export function debounce(func, wait, immediate) {
    let timeout;
    return function () {
        let context = this, args = arguments;
        let later = function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        let callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

/**
 * @param element
 * @returns {*}
 */
export function getBottomBorder(element) {
    return getOffset(element) + $(element).height();
}

export function scrollTo(id, pushState = true) {
    const $target = id.length > 1 ? $(id) : [];
    const scrollTop = $target.length ? getOffset($target[0]) - getHeaderHeight(true, true) : 0;
    $('body, html').animate({scrollTop}, 400, () => {
        if (pushState) {
            history.pushState({}, "", id);
        }
    });
}
