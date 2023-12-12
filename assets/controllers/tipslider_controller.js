import { Controller } from "@hotwired/stimulus";
import $ from "jquery";

export default class extends Controller {
    static targets = ["input", "display"]
    connect() {
        // add eventlistener to elements with class range-slider__range
        $(this.inputTarget).on("input", function() {
            // get the value of the range input as number
            var val = parseFloat($(this.inputTarget).val());
            // format val to currency
            var valFormatted = val.toLocaleString(undefined, {
                style: "currency",
                currency: "EUR"
            });
            // update the value of the range-slider__value
            $(this.displayTarget).html(valFormatted);
        }.bind(this));
        // fire input event on class range-slider__range to update value
        $(this.inputTarget).trigger("input");
    }
}