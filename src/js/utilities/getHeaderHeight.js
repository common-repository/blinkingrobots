const $ = jQuery;

export default function getHeaderHeight(includeAdminbar = false, includePageNav = false) {
    let h = 0;
    // let header = $('.elementor-location-header .elementor-sticky.elementor-sticky--active');
    // if (!header.length) {
    //     header = $('.elementor-location-header .elementor-sticky');
    // }
    const adminBar = $('#wpadminbar');
    const pageNav = $('.js-page-nav.is-mob .js-page-nav-header');
    if (includeAdminbar && adminBar.length) {
        h += adminBar.innerHeight();
    }
    if (includePageNav && pageNav.length) {
        h += pageNav.outerHeight();
    }
    // h += header.outerHeight();
    return h;
}
