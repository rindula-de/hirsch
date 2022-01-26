function processData(data) {
    let orders = [];
    let ordersarea = $('#orders');
    if (data.length > 0) {
        for (const order of data) {
            // if orders[order.orderedSlug] is undefined, create it
            if (orders[order.ordered] === undefined) {
                orders[order.ordered] = {};
            }
            // if orders[order.orderedSlug][order.note] is undefined, create it
            if (orders[order.ordered][order.note] === undefined) {
                orders[order.ordered][order.note] = 1;
            } else {
                orders[order.ordered][order.note]++;
            }
        }
        ordersarea.empty();
        for (const ordersKey in orders) {
            for (const ordersNote in orders[ordersKey]) {
                if (ordersarea.val()) ordersarea.append(`\n\n`);
                ordersarea.append(`${orders[ordersKey][ordersNote]}x ${ordersKey}` + (ordersNote ? "\nSonderwunsch: " + ordersNote : ""));
            }
        }
    } else {
        ordersarea.empty();
        ordersarea.append('--- Keine Bestellugen ---')
    }

}

if (typeof (EventSource) !== "undefined") {
    var source = new EventSource("/api/orders/stream");
    source.onmessage = function (event) {
        data = JSON.parse(event.data);
        processData(data);
    };
} else {
    $.ajax({
        url: '/api/orders/1',
        type: 'GET',
        dataType: 'json',
    }).done(processData).fail(function (jqXHR, textStatus, errorThrown) {
        console.error(jqXHR);
        console.error(textStatus);
        console.error(errorThrown);
    });
}