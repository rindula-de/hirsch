import { Controller } from "stimulus";
import $ from "jquery";

export default class extends Controller {
    connect() {
        $.ajax({
            url: "/order-until",
            context: this,
        }).done(function(result) {
            if (result) {
                $(this.element).html(result.trim());
            }
        });
    }
}