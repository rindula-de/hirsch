import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ['icon']
    connect() {
        this.iconTarget.innerHTML = '<i class="material-icons">menu</i>';
        this.iconTarget.addEventListener("click", function() {
            let x = document.getElementById("navbar");
            if (x.classList.contains('responsive')) {
                x.classList.remove("responsive");
                this.classList.add("on-background-text");
                this.classList.remove("background-text");
            } else {
                x.classList.add("responsive");
                this.classList.add("background-text");
                this.classList.remove("on-background-text");
            }
        })
    }
}