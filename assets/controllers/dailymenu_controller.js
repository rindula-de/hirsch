import { Controller } from "stimulus";
import $ from "jquery";

export default class extends Controller {
    static targets = [ "dailyfood", 'menu', 'template' ];

    connect() {
        const template = this.templateTarget;
        $.ajax({
            url: "/api/holidays",
            context: this,
            dataType: 'json'
        }).done(function(holidays) {
            $.ajax({
                url: "/api/get-menu",
                context: this.menuTarget,
                dataType: 'json'
            }).done(function(menus) {
                let menu_panel = this;
                menu_panel.innerHTML = "";
                menu_panel.classList.remove("loading");
                for (const menu of menus) {
                    let clone = template.content.cloneNode(true);
                    clone.querySelector("[data-role=title]").innerHTML = menu['name'];
                    clone.querySelector("[data-role=gericht]").innerHTML = "" //"<a target='menu_preview' href='https://www.google.com/search?tbm=isch&q=" + menu['name'] + "'>📷</a>";
                    let btn = clone.querySelector("[data-role=order]");
                    btn.innerHTML = "Bestellen";
                    btn.setAttribute("href", "/order/0/" + menu['slug']);
        
                    menu_panel.append(clone);
                }
        
            });
            $.ajax({
                url: "/api/get-tagesessen",
                context: this.dailyfoodTarget,
                dataType: 'json',
            }).done(function(result) {
                var tagesessen_panel = this;
                tagesessen_panel.innerHTML = "";
                tagesessen_panel.classList.remove("loading");
        
                $.ajax({
                    url: "/api/get-tagesessen-karte",
                    context: document.body,
                    dataType: 'json',
                }).done(result => {
                    if (!(result && result.file)) return
                        // decode base64 string, remove space for IE compatibility
                    var binary = atob(result.file.replace(/\s/g, ''));
                    var len = binary.length;
                    var buffer = new ArrayBuffer(len);
                    var view = new Uint8Array(buffer);
                    for (var i = 0; i < len; i++) {
                        view[i] = binary.charCodeAt(i);
                    }
        
                    var blob = new Blob([view], { type: 'application/pdf' });
                    var url = URL.createObjectURL(blob);
                    tagesessen_panel.innerHTML = "<button class='btn' onclick=\"window.open('" + url + "', 'pdf_karte', 'location=yes')\">PDF Karte ansehen</button><br><br>" + tagesessen_panel.innerHTML;
        
                    tagesessen_panel.style.maxHeight = tagesessen_panel.scrollHeight + "px";
                })
        
                if (result && result.displayData && result.displayData.length > 0) {
                    var i = 0;
                    for (let resultElement of result.displayData) {
                        let date = new Date(resultElement['date']);
                        let today = new Date();
                        date.setHours(14, 0, 0, 0);
                        // today.setHours(0, 0, 0, 0);
                        if (date < today) {
                            continue;
                        }
                        date.setHours(0, 0, 0, 0);
                        let holidayDate = resultElement['gericht'].toLowerCase().includes("ruhetag");
                        if (!holidayDate) {
                            for (let j = 0; j < holidays.length; j++) {
                                let start = new Date(holidays[j]['start']);
                                let end = new Date(holidays[j]['end']);
                                start.setHours(0, 0, 0, 0);
                                end.setHours(0, 0, 0, 0);
        
                                if (date >= start && date <= end) {
                                    holidayDate = true;
                                    break;
                                }
                            }
                        }
                        let clone = template.content.cloneNode(true);
                        clone.querySelector("[data-role=title]").innerHTML = "Tagesessen für den " + date.toLocaleDateString();
                        clone.querySelector("[data-role=gericht]").innerHTML = resultElement['gericht'] + ((holidayDate) ? "" : " <a target='menu_preview' href='https://www.google.com/search?tbm=isch&q=" + resultElement['gericht'] + "'>📷</a>");
                        let btn = clone.querySelector("[data-role=order]");
                        if (!holidayDate) {
                            btn.innerHTML = (i === 0 && date.getDate() === today.getDate()) ? "Bestellen" : "Vorbestellen";
                            btn.setAttribute("href", "/order/_i_/tagesessen");
                            btn.setAttribute("href", btn.getAttribute("href").replace("_i_", Math.ceil((date - today) / (1000 * 60 * 60 * 24))));
                        } else {
                            clone.querySelector("[data-role=card]").classList.add("ruhetag");
                            btn.remove();
                        }
        
                        tagesessen_panel.append(clone);
                        i++;
                    }
                    
                    if (i === 0) {
                        let clone = template.content.cloneNode(true);
                        clone.querySelector("[data-role=title]").innerHTML = "Information";
                        clone.querySelector("[data-role=gericht]").innerHTML = "Es sind keine weiteren Tagesessen für die Woche geplant.";
                        clone.querySelector("[data-role=order]").remove();
                        tagesessen_panel.append(clone);
                    }
                } else {
                    let clone = template.content.cloneNode(true);
                    clone.querySelector("[data-role=title]").innerHTML = "Tagesessen konnten nicht geladen werden";
                    clone.querySelector("[data-role=gericht]").innerHTML = "Die Liste der Tagesessen konnten nicht geladen werden! Wenn du trotzdem bestellen möchtest, findest du die Karte unter <a href='https://www.hirsch-restaurant.de/speisekarte/' target='speisekarte'>https://www.hirsch-restaurant.de/speisekarte/</a>";
                    clone.querySelector("[data-role=order]").innerHTML = "Bestellen";
                    clone.querySelector("[data-role=order]").setAttribute("href", "/order/0/tagesessen");
        
                    tagesessen_panel.append(clone);
                }
                tagesessen_panel.style.maxHeight = tagesessen_panel.scrollHeight + "px";
            });
        });
    }
}
