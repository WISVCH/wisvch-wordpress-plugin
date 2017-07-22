jQuery(function ($) {

    var start_date = $('#_event_start_date'),
        end_date = $('#_event_end_date'),
        date_range_wrapper = $('#_event_date_range_wrapper'),
        date_range_el = $('#_event_date_range');

    // Hide input fields, add date range picker
    $('.flatpickr-hide').hide();
    date_range_wrapper.show();

    // Init Flatpickr
    date_range_el.flatpickr({

        mode: "range",
        defaultDate: [start_date.val(), end_date.val()],
        enableTime: true,
        time_24hr: true,

        // Update start and end date inputs on change
        onChange: function (dates) {

            if (typeof dates[0] !== 'undefined') {
                start_date.val(Flatpickr.prototype.formatDate(dates[0], 'Y-m-d H:i'));

                if (typeof dates[1] !== 'undefined') {
                    end_date.val(Flatpickr.prototype.formatDate(dates[1], 'Y-m-d H:i'));
                } else {
                    end_date.val('');
                }

            } else {
                start_date.val('');
                end_date.val('');
            }

            return dates;

        }

    });

});
