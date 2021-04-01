function openSideMenu() {
    var x = document.getElementById("navbar");
    if (x.className === "navbar") {
        x.className += " responsive";
    } else {
        x.className = "navbar";
    }
}

var acc = document.getElementsByClassName("accordion");
var i;
if (acc) {
    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function () {
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
    success: function (result) {
        if (result) {
            $("#informationModalText").html(result.trim());
            $("#informationModal").addClass("active");
        }
    }
});

// When the user clicks on <span> (x), close the modal
$('.close').click(function () {
    $(this).parent().parent().removeClass("active");
})

$(".preorderBtn").click(function (event) {
    event.preventDefault();
    var slug = $(this).attr('data-slug');
    $('#preorderLink').removeAttr('href');
    $('#preorderSlug').html(slug);
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
        function (date) {

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
    onChange: function (selectedDates, dateStr, instance) {
        var today = new Date()
        var picked = new Date(dateStr)
        const diffTime = Math.abs(picked - today);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        $('#preorderLink').attr('href', '/bestellen/' + diffDays + '/' + $('#preorderSlug').html())
    }
});
