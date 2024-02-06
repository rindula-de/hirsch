import { Controller } from "@hotwired/stimulus";
import $ from "jquery";

export default class extends Controller {
    static values = {
        name: String,
        ordered: String,
        special: String
    }

    connect() {
        $(this.element).click(function() {
            $('#orderedModalTitle').text('Bestellung von '+this.nameValue);
            $('#orderedModalText').text(this.orderedValue);
            if (this.specialValue!='') {
                $('#orderedModalText').append('<br><br>')
                $('#orderedModalText').append(document.createTextNode(this.specialValue));
            }
            $('#orderedModal').addClass('active');
        }.bind(this));
    }
}
