export default new class {
    constructor() {
        document.addEventListener('turbo:load', () => {
            _paq.push(['setCustomUrl', window.location.href]);
            _paq.push(['setDocumentTitle', document.title]);

            if (window.location.href.includes('/order/')) {
                //slug is the last element of the url
                let slug = window.location.href.split('/').pop();
                _paq.push(['setEcommerceView', slug, window.gericht??"", "Food", 3.5]);
                window.gericht = null;
            }

            if (window.order_id) {
                // Order Array - Parameters should be generated dynamically
                _paq.push(['trackEcommerceOrder',
                    window.order_id, // (Required) orderId
                    3.5, // (Required) grandTotal (revenue)
                ]);
                // unset order_id
                window.order_id = null;
            }

            _paq.push(['trackPageView']);
        });
    }
}
