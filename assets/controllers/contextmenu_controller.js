import {Controller} from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    id_regex = new RegExp('^\\d{8}-\\d{4}-\\d{4}-\\d{4}-\\d{12}$');

    menu(event) {

        // Avoid the real one
        event.preventDefault();
        this.element.innerHTML = '';
        if (event.target.classList.contains('paypalmebutton')) {
            this.element.innerHTML = this.element.innerHTML + '<li data-contextmenu-id-param="' + event.target.value + '" data-action="click->contextmenu#editPaypalme">Bearbeiten</li>';
            if (event.target.parent().classList.contains('active')) {
                this.element.innerHTML = this.element.innerHTML + '<li data-contextmenu-id-param="' + event.target.value + '" data-action="click->contextmenu#deactivatePaypalme">Als aktiven Bezahler entfernen</li>';
            }
        } else if (event.target.classList.contains('orderarea')) {
            this.element.innerHTML = this.element.innerHTML + '<li data-contextmenu-text-param="' + event.target.value + '" data-action="click->contextmenu#copy">Kopieren</li>';
        } else {
            // show grayed out info
            this.element.innerHTML = this.element.innerHTML + '<li class="disabled">Keine Aktionen verfügbar</li>';
        }

        // Show contextmenu
        this.element.style.display = 'block'; // Show the menu
        this.element.style.position = 'absolute';
        this.element.style.top = `${event.pageY}px`;
        this.element.style.left = `${event.pageX}px`;
    }
    editPaypalme(event) {
        const id = event.params.id;
        if (this.id_regex.test(id)) window.location.href = "/paypal/edit/" + id;
    }
    deactivatePaypalme(event) {
        const id = event.params.id;
        if (this.id_regex.test(id)) window.location.href = "/paypal/remove-active/" + id;
    }
    async copy(event) {
        await navigator.clipboard.writeText(event.params.text);
    }
    close(event) {
        this.element.style.display = 'none';
    }
}
