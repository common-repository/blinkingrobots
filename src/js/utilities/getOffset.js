export default function getOffset(element) {
    if (element) {
        const box = element.getBoundingClientRect();
        return box.top + jQuery(window).scrollTop();
    } else {
        const nodeRect = this.getBoundingClientRect();
        const bodyRect = document.body.getBoundingClientRect();

        return nodeRect.top - bodyRect.top;
    }
}
