import { Controller } from "stimulus";
import $ from "jquery";

export default class extends Controller {
    static targets = ["close"]
    connect() {
        // When the user clicks on <span> (x), close the modal
        $(this.closeTarget).click(function() {
            $(this).removeClass("active");
        }.bind(this.element));
    }
}