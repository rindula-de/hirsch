import { Controller } from "stimulus";
import $ from "jquery";

export default class extends Controller {
    connect() {
        $(this.element).click(function() {
            $('#orderedModalTitle').html('Bestellung von '+$(this.element).attr('data-name'));
            $('#orderedModalText').html($(this.element).attr('data-ordered')+(($(this.element).attr('data-special')!='')?'<br><br>'+$(this.element).attr('data-special'):''));
            $('#orderedModal').addClass('active');
        }.bind(this));
    }
}