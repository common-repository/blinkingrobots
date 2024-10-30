'use strict';

!(function ($) {

    class ExampleComponent {
        constructor(wrapper) {
            this.wrapper = wrapper;


            this.init();
        }

        init() {
            console.log('example-component');
        }

    }

    $('.js-example-component').each((i, wrapper) => {
        new ExampleComponent(wrapper);
    });


})(jQuery);
