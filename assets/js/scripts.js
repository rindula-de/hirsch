import $ from 'jquery';

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
        } else {
            // show grayed out info
            $(".custom-menu").html('<li class="disabled">Keine Aktionen verfügbar</li>');
        }

        // If the menu element is clicked
        $(".custom-menu li").click(function() {

            var id_regex = new RegExp('^[0-9]+$');
            // This is the triggered action name
            switch ($(this).attr("data-action")) {
                case "edit-paypalme":
                    var id = $(this).attr("data-id");
                    if (id_regex.test(id)) window.location.href = "/paypal/edit/" + id;
                    break;
                case "copy":
                    navigator.clipboard.writeText($(this).attr("data-text"));
                    break;
                case "deactivate-paypalme":
                    var id = $(this).attr("data-id");
                    if (id_regex.test(id)) window.location.href = "/paypal/remove-active/" + id;
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

    $('#showChangelog').click(() => {
        if ($('#changelogModal').hasClass('active')) {
            $('#changelogModal').removeClass('active');
        }
        $(this).hide();
        let me = this;
        $.ajax({
            url: "/modalChangelog",
            context: document.body,
            success: function(result) {
                if (result) {
                    $("#changelogModalText").html(result.trim());
                    $("#changelogModal").addClass("active");
                }
            },
            error: function(result){
                $("#changelogModalText").html(result.trim());
                $("#changelogModal").addClass("active");
            },
            complete: function() {
                $(me).show();
            }
        });
    });

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
    })
}
