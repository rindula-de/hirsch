// let holidays = JSON.parse('<?= json_encode($holidays) ?>');
let template = document.getElementById("foodcard_template");
let tagesessen_panel = document.getElementById('tagesessen_panel');

$.ajax({
    url: "/api/get-tagesessen",
    context: document.body,
    dataType: 'json',
    success: function(result) {
        tagesessen_panel.innerHTML = "";
        tagesessen_panel.classList.remove("loading");
        if (result && result.file) {
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
            tagesessen_panel.innerHTML += "<button class='btn' onclick=\"window.open('" + url + "', 'pdf_karte', 'location=yes')\">PDF Karte ansehen</button>";
        }

        if (result && result.displayData && result.displayData.length > 0) {
            for (let i = 0; i < result.displayData.length; i++) {
                let resultElement = result.displayData[i];
                let date = new Date(resultElement['date']);
                let today = new Date();
                date.setUTCHours(0, 0, 0, 0);
                today.setUTCHours(0, 0, 0, 0);
                if (date < today) {

                    continue;
                }
                let holidayDate = false;
                // for (let j = 0; j < holidays.length; j++) {
                //     let start = new Date(holidays[j]['from']).setHours(0);
                //     let end = new Date(holidays[j]['to']).setHours(23);

                //     if (date >= start && date <= end) {
                //         holidayDate = true;
                //         break;
                //     }
                // }
                if (resultElement['gericht'].toLowerCase().includes("ruhetag")) holidayDate = true;
                let clone = template.content.cloneNode(true);
                clone.querySelector("[data-role=title]").innerHTML = "Tagesessen für den " + date.toLocaleDateString();
                clone.querySelector("[data-role=gericht]").innerHTML = resultElement['gericht'] + ((holidayDate) ? "" : " <a target='menu_preview' href='https://www.google.com/search?tbm=isch&q=" + resultElement['gericht'] + "'>📷</a>");
                let btn = clone.querySelector("[data-role=order]");
                if (!holidayDate) {
                    btn.innerHTML = (date.getTime() === today.getTime()) ? "Bestellen" : "Vorbestellen";
                    btn.setAttribute("href", "/order/_i_/tagesessen");
                    btn.setAttribute("href", btn.getAttribute("href").replace("_i_", Math.floor((date - today) / (1000 * 60 * 60 * 24))));
                } else {
                    clone.querySelector("[data-role=card]").classList.add("ruhetag");
                    btn.remove();
                }

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
    }
});
