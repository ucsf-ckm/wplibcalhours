(function ($) {
    'use strict';
    $.fn.wplibcalhours = function () {
        return this.each(function () {
            let $this = $(this);
            let weeks = $('tbody', $this);
            let numWeeks = weeks.length;
            let counter = 0;
            let prev = $('.prev', $this);
            let next = $('.next', $this);

            let updateWeeksDisplay = function () {
                for (let i = 0; i < numWeeks; i++) {
                    if (i === counter) {
                        $(weeks[i]).removeClass('hidden');
                    } else {
                        $(weeks[i]).addClass('hidden');
                    }
                }
            };

            let increment = function () {
                counter++;
                if (counter) {
                    prev.removeClass('hidden');
                }
                if (counter === (numWeeks - 1)) {
                    next.addClass('hidden');
                }

                updateWeeksDisplay();
            };

            let decrement = function () {
                counter--;
                if (!counter) {
                    prev.addClass('hidden');
                }
                if (counter !== numWeeks) {
                    next.removeClass('hidden');
                }

                updateWeeksDisplay();
            };

            prev.on('click', decrement);
            next.on('click', increment);
        });
    };

    $(document).ready(function () {
        $('.wplibcalhours').wplibcalhours();
    });
})(jQuery);
