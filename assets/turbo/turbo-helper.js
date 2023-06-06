export default new class {
    constructor() {
        document.addEventListener('turbo:load', () => {
            _paq.push(['setCustomUrl', window.location.href]);
            _paq.push(['setDocumentTitle', document.title]);
            _paq.push(['trackPageView']);
            console.log(window.location.href);
        });
    }
}
