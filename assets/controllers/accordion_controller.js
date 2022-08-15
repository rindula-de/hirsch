import { Controller } from "stimulus";

export default class extends Controller {
    connect() {
        this.element.addEventListener("click", function() {
            this.classList.toggle("active");

            var panel = this.nextElementSibling;
            if (panel.style.maxHeight) {
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = "min-content";
            }
        });

        if (this.element.innerHTML === "Tagesessen") {
            this.element.click()
        }
    }
}