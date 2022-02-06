import { Controller } from "stimulus";

export default class extends Controller {
    static targets = ['icon']
    connect() {
        this.iconTarget.innerHTML = '<i class="material-icons">menu</i>';
        this.iconTarget.addEventListener("click", function() {
            let x = document.getElementById("navbar");
            if (x.className === "navbar") {
                x.className += " responsive";
            } else {
                x.className = "navbar";
            }
        })
    }
}