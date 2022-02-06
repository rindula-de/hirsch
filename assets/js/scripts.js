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
    navigator.serviceWorker.getRegistrations().then(function(registrations) {

        for(let registration of registrations) {
    
                registration.unregister()
    
        }}).catch(function(err) {
    
            console.log('Service Worker registration failed: ', err);
    
        });
}