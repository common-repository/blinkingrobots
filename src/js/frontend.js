'use strict';

import onReady from "./hooks/onReady";
import onScroll from "./hooks/onScroll";
import onResize from "./hooks/onResize";

!(function ($) {

    $(document)
        .on('ready', onReady.bind(this))
        .on('scroll', onScroll.bind(this));

    $(window).on('resize', onResize.bind(this));

})(jQuery);
