export default function () {
  const $ = jQuery

  const scrolledClassName = 'is-scrolled'

  const scrolled = $(window).scrollTop() || document.documentElement.scrollTop
  const $html = $('html')

  if (scrolled > 20) {
    $html.addClass(scrolledClassName)
  } else {
    $html.removeClass(scrolledClassName)
  }
};
