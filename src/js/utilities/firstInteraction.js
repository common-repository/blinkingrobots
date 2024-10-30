export default function () {
    const firstInteraction = function () {
        jQuery(document).trigger('firstInteraction');
        document.removeEventListener('scroll', firstInteraction, false);
        document.removeEventListener('mousemove', firstInteraction, false);
        document.removeEventListener('touchstart', firstInteraction, false);
        document.removeEventListener('click', firstInteraction, false);
        window.removeEventListener('resize', firstInteraction, false);
    }
    document.addEventListener('scroll', firstInteraction, false);
    document.addEventListener('mousemove', firstInteraction, false);
    document.addEventListener('touchstart', firstInteraction, false);
    document.addEventListener('click', firstInteraction, false);
    window.addEventListener('resize', firstInteraction, false);
};
