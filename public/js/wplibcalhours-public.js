(function ($) {
    'use strict';
    $.fn.wplibcalhours = function () {
        return this.each(function () {
            var $this = $(this);
            var weeks = $('tbody', $this);
            var numWeeks = weeks.length;
            var counter = 0;
            var prev = $('.prev', $this);
            var next = $('.next', $this);

            var updateWeeksDisplay = function () {
                for (var i = 0; i < numWeeks; i++) {
                    if (i === counter) {
                        $(weeks[i]).removeClass('hidden');
                    } else {
                        $(weeks[i]).addClass('hidden');
                    }
                }
            };

            var increment = function () {
                counter++;
                if (counter) {
                    prev.removeClass('hidden');
                }
                if (counter === (numWeeks - 1)) {
                    next.addClass('hidden');
                }

                updateWeeksDisplay();
            };

            var decrement = function () {
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
