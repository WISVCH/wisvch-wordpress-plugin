jQuery(function ($) {

    /**
     * Events object.
     */
    var Events = {

        /**
         * Test if URL exists
         * @param url CH Connect URL
         * @param cb Callback function
         */
        testConnection: function (url, cb) {

            // @TODO: really verify a CH Connect instance is running at the URL
            $.ajax({
                type: 'HEAD',
                url: url,
                success: function () {
                    cb(true);
                },
                error: function (status) {
                    console.log(status);
                    cb(false);
                }
            });
        }

    };

    // Add listeners
    $("#eventsSync").on('click tap', "#checkConnection", function (e) {

        var t = $(this);
        var url = $("#wisvch_events_sync-ch_connect_url").val();

        // Suppress default behaviour
        e.preventDefault();
        t.blur();

        // Add spinner
        t.find('.label').hide();
        t.find('.spinner').addClass('is-active');
        t.prop('readonly', true);

        // Connect to CH Events
        Events.testConnection(url, function (exists) {

            // Remove spinner
            t.find('.spinner').removeClass('is-active');

            // Update label
            if (exists) {
                t.find('.label').html("Success").show();
                t.prop('disabled', true);
            } else {
                t.find('.label').html("Connection error").show();
                t.prop('disabled', false);
            }

        });

    });

});
