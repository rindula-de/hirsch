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
    url: "/ngvkjdrfnknvgimhcsllfkhxmujgjcsrj",
    context: document.body,
    success: function (result) {
        if (result) {
            $("#informationModalText").html(result.trim());
            $("#informationModal").addClass("active");
        }
    }
})

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
})

$('#datepickerPreorder').datepicker({
    onSelect: function (dateText, inst) {
        dateText = dateText.split(".").reverse().join('-')
        var today = new Date()
        var picked = new Date(dateText)
        const diffTime = Math.abs(picked - today);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        $('#preorderLink').attr('href', '/bestellen/' + diffDays + '/' + $('#preorderSlug').html())
    },
    minDate: new Date((new Date()).getTime() + (24 * 60 * 60 * 1000)),
    prevText: '&#x3c;zurück', prevStatus: '',
    prevJumpText: '&#x3c;&#x3c;', prevJumpStatus: '',
    nextText: 'Vor&#x3e;', nextStatus: '',
    nextJumpText: '&#x3e;&#x3e;', nextJumpStatus: '',
    currentText: 'heute', currentStatus: '',
    todayText: 'heute', todayStatus: '',
    clearText: '-', clearStatus: '',
    closeText: 'x', closeStatus: '',
    monthNames: ['Januar','Februar','März','April','Mai','Juni',
        'Juli','August','September','Oktober','November','Dezember'],
    monthNamesShort: ['Jan','Feb','Mär','Apr','Mai','Jun',
        'Jul','Aug','Sep','Okt','Nov','Dez'],
    dayNames: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
    dayNamesShort: ['So','Mo','Di','Mi','Do','Fr','Sa'],
    dayNamesMin: ['So','Mo','Di','Mi','Do','Fr','Sa'],
    showMonthAfterYear: false,
    dateFormat: 'dd.mm.yy',
    firstDay: 1,
    beforeShowDay: $.datepicker.noWeekends
});
$('#datepickerPreorder').setDefaults($.datepicker.regional['de']);
