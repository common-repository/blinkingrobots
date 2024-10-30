const $ = jQuery;

export default function () {
    let vh = window.innerHeight * 0.01;
    $('body').css('--vh', `${vh}px`);
}
//height: calc(var(--vh, 1) * 100)
