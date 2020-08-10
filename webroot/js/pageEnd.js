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

// Get the modal
var modal = document.getElementById("informationModal");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

$.ajax({
    url: "/ngvkjdrfnknvgimhcsllfkhxmujgjcsrj",
    context: document.body,
    success: function(result){
        if (result) {
            $("#informationModalText").html(result.trim());
            $("#informationModal").addClass("active");
        }
    }
})

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    $("#informationModal").removeClass("active");
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target === modal) {
        $("#informationModal").removeClass("active");
    }
}
