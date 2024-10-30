import getOffset from "./getOffset";
import getScrolledBottomCorner from "./getScrolledBottomCorner";

function removeAttr(node, attribute, attributeSet) {

    if (node.classList.contains('aload-bg')) {
        node.style.backgroundImage = 'url(' + node.getAttribute(attribute) + ')';
        node.classList.remove('aload-bg')
        node.classList.remove('aload-later')
    } else {
        node[node.tagName !== 'LINK' ? 'src' : 'href'] = node.getAttribute(attribute);
    }

    if (node.classList.contains('attachment-svg')) {

        const img = new Image();

        // Set divider height
        // img.onload = () => node.removeAttribute(attribute);

        // assign url to new image
        img.src = node.getAttribute(attribute);

    }


    if (node.getAttribute(attributeSet)) {
        node.setAttribute('srcset', node.getAttribute(attributeSet));
        node.removeAttribute(attributeSet);
    }
    if (node.getAttribute(attribute)) {
        node.removeAttribute(attribute);
    }
}

/**
 *
 * @param nodes
 * @param loadAlways
 * @returns {*}
 */
export function aload(nodes, loadAlways = false) {

    const attribute = 'data-aload';
    const attributeSet = 'data-aload-srcset';

    nodes = nodes || window.document.querySelectorAll('[' + attribute + ']' + ', [' + attributeSet + ']');

    if (nodes.length === undefined) {
        nodes = [nodes];
    }

    [].forEach.call(nodes, function (node) {

        if (node.tagName === 'IMG' || node.tagName === 'IFRAME' || node.classList.contains('aload-bg')) {
            const offset = getOffset(node);
            if ((offset && (getScrolledBottomCorner() >= offset)) && !node.classList.contains('aload-later') || loadAlways) {
                removeAttr(node, attribute, attributeSet);
            }
        } else {
            removeAttr(node, attribute, attributeSet);
        }
    });

    return nodes;
}
