require([
    'domReady!'
    ],
    function() {
        onRenderComplete: function () {
            var viewModel = this;
            var popupOptions = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                modalClass: 'my-popup',
                title: '<b>Save Thing</b>',
                'buttons': [{
                    text: 'Cancel',
                    class: 'action'
                }],
                opened: function () {
                    // Because magento modal copies the dom... we need to apply bindings. But it only copies once. So we want to only apply the bindings on first open.
                    if (this.appliedBindings === undefined) {
                        ko.applyBindings(viewModel, this);
                        this.appliedBindings = true;
                    }
                }
            };
    }
);
