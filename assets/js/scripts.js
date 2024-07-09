import $ from 'jquery';

$(document).ready(() => {
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
