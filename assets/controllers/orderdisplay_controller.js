import { Controller } from "stimulus";
import $ from "jquery";

export default class extends Controller {
    connect() {
        $(this.element).click(function() {
            $('#orderedModalTitle').text('Bestellung von '+$(this.element).attr('data-name'));
            $('#orderedModalText').text($(this.element).attr('data-ordered'));
            if ($(this.element).attr('data-special')!='') {
                $('#orderedModalText').append('<br><br>')
                $('#orderedModalText').append(document.createTextNode($(this.element).attr('data-special')));
            }
            $('#orderedModal').addClass('active');
        }.bind(this));
    }
}