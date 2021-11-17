$.ajax({
    url: '/api/orders/1',
    type: 'GET',
    dataType: 'json',
}).done(function(data) {
    console.log(data);
    let orders = [];
    let ordersarea = $('#orders');

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
        console.log(ordersKey);
        for (const ordersNote in orders[ordersKey]) {
            console.log(ordersNote);
            ordersarea.append(`${orders[ordersKey][ordersNote]}x ${ordersKey}`+(ordersNote?"\nSonderwunsch: "+ordersNote:"")+`\n\n`);
        }
    }

}).fail(function(jqXHR, textStatus, errorThrown) {
    console.log(jqXHR);
    console.log(textStatus);
    console.log(errorThrown);
});
