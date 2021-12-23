$(document).ready(() => {
    $(document).bind("contextmenu", function(event) {

        // Avoid the real one
        event.preventDefault();
        $(".custom-menu").empty();
        if ($(event.target).hasClass('paypalmebutton')) {
            $(".custom-menu").html('<li data-id="' + event.target.value + '" data-action="edit-paypalme">Bearbeiten</li>');
            if ($(event.target).parent().hasClass('active')) {
                $(".custom-menu").append('<li data-id="' + event.target.value + '" data-action="deactivate-paypalme">Als aktiven Bezahler entfernen</li>');
            }
        } else if ($(event.target).hasClass('orderarea')) {
            $(".custom-menu").html('<li data-text="' + event.target.value + '" data-action="copy">Kopieren</li>');
        }

        // If the menu element is clicked
        $(".custom-menu li").click(function() {

            // This is the triggered action name
            switch ($(this).attr("data-action")) {
                case "edit-paypalme":
                    window.location.href = "/paypal/edit/" + $(this).attr("data-id");
                    break;
                case "copy":
                    navigator.clipboard.writeText($(this).attr("data-text"));
                    break;
                case "deactivate-paypalme":
                    window.location.href = "/paypal/remove-active/" + $(this).attr("data-id");
                    break;
            }

            // Hide it AFTER the action was triggered
            $(".custom-menu").hide(100);
        });

        // Show contextmenu
        $(".custom-menu").finish().toggle(100).

        // In the right position (the mouse)
        css({
            top: event.pageY + "px",
            left: event.pageX + "px"
        });
    });


    // If the document is clicked somewhere
    $(document).bind("mousedown", function(e) {

        // If the clicked element is not the menu
        if (!$(e.target).parents(".custom-menu").length > 0) {

            // Hide it
            $(".custom-menu").hide(100);
        }
    });

    document.getElementById("navicon").addEventListener("click", function() {
        let x = document.getElementById("navbar");
        if (x.className === "navbar") {
            x.className += " responsive";
        } else {
            x.className = "navbar";
        }
    })

    var acc = document.getElementsByClassName("accordion");
    var i;
    if (acc) {
        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
                this.classList.toggle("active");

                var panel = this.nextElementSibling;
                if (panel.style.maxHeight) {
                    panel.style.maxHeight = null;
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            });

            if (acc[i].innerHTML === "Tagesessen") {
                acc[i].click()
            }
        }

    }

    $.ajax({
        url: "/modalInformationText",
        context: document.body,
        success: function(result) {
            if (result) {
                $("#informationModalText").html(result.trim());
                $("#informationModal").addClass("active");
            }
        }
    });

    $.ajax({
        url: "/order-until",
        context: document.body,
        success: function(result) {
            if (result) {
                $("#order-until").html(result.trim());
            }
        }
    });

    // When the user clicks on <span> (x), close the modal
    $('.close').click(function() {
        $(this).parent().parent().removeClass("active");
    })

    $(".preorderBtn").click(function(event) {
        event.preventDefault();
        var slug = $(this).attr('data-slug');
        $('#preorderLink').removeAttr('href');
        $('#preorderSlug').text(slug);
        $('#preorderModal').addClass('active')
    });

    if (typeof holidays !== 'undefined')
        $(".datepicker").flatpickr({
            altInput: true,
            altFormat: "j F, Y",
            dateFormat: "Y-m-d",
            minDate: new Date().fp_incr(1),
            maxDate: new Date().fp_incr(14),
            disable: [
                function(date) {

                    var holidayDate = false;
                    var today = new Date(date);
                    for (let j = 0; j < holidays.length; j++) {
                        var start = new Date(holidays[j]['from']).setHours(0);
                        var end = new Date(holidays[j]['to']).setHours(0);

                        if (today >= start && today <= end) {
                            holidayDate = true;
                            break;
                        }
                    }
                    // return true to disable
                    return (date.getDay() === 0 || date.getDay() === 1 || date.getDay() === 6) || holidayDate;

                }
            ],
            locale: {
                "firstDayOfWeek": 1 // start week on Monday
            },
            weekNumbers: true,
            onChange: function(selectedDates, dateStr, instance) {
                var today = new Date()
                var picked = new Date(dateStr)
                const diffTime = Math.abs(picked - today);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                $('#preorderLink').attr('href', '/bestellen/' + diffDays + '/' + $('#preorderSlug').html())
            }
        });

    // add eventlistener to elements with class range-slider__range
    $(".range-slider__range").on("input", function() {
        // get the value of the range input as number
        var val = parseFloat($(this).val());
        // format val to currency
        var valFormatted = val.toLocaleString(undefined, {
            style: "currency",
            currency: "EUR"
        });
        // update the value of the range-slider__value
        $(this).next().html(valFormatted);
    });
    // fire input event on class range-slider__range to update value
    $(".range-slider__range").trigger("input");
});

// check if the browser supports serviceWorker at all
if ('serviceWorker' in navigator) {
    // wait for the page to load
    window.addEventListener('load', async() => {
        // register the service worker from the file specified
        const registration = await navigator.serviceWorker.register('/sw.js')

        // ensure the case when the updatefound event was missed is also handled
        // by re-invoking the prompt when there's a waiting Service Worker
        if (registration.waiting) {
            registration.waiting.postMessage('SKIP_WAITING')
        }

        // detect Service Worker update available and wait for it to become installed
        registration.addEventListener('updatefound', () => {
            if (registration.installing) {
                // wait until the new Service worker is actually installed (ready to take over)
                registration.installing.addEventListener('statechange', () => {
                    if (registration.waiting) {
                        // if there's an existing controller (previous Service Worker), send update message
                        if (navigator.serviceWorker.controller) {
                            registration.waiting.postMessage('SKIP_WAITING')
                        } else {
                            // otherwise it's the first install, nothing to do
                            console.log('Service Worker initialized for the first time')
                        }
                    }
                })
            }
        })

        let refreshing = false;

        // detect controller change and refresh the page
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (!refreshing) {
                window.location.reload(true);
                refreshing = true
            }
        })
    })
}