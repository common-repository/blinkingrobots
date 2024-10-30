export default function getScrolledBottomCorner() {
    return document.querySelector('html').scrollTop + window.innerHeight;
}
