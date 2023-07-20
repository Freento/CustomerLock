require([
    'jquery',
    'Magento_Ui/js/modal/confirm'
], function ($, confirmation) {
    let button = document.querySelector('.lock-customer') || document.querySelector('.unlock-customer');
    
    if (!button) {
        return;
    }
    
    let clickListener = button.onclick;
    button.onclick = (event) => {
        let modalConfig = {
            actions: {
                confirm: function () {
                    clickListener(event);
                },
                cancel: function () {
                },
                always: function () {
                }
            }
        };

        if (button.classList.contains('lock-customer')) {
            modalConfig.title = $.mage.__('Lock customer');
            modalConfig.content = $.mage.__('Are you sure you want to lock the customer?');
        } else {
            modalConfig.title = $.mage.__('Unlock customer');
            modalConfig.content = $.mage.__('Are you sure you want to unlock the customer?');
        }

        confirmation(modalConfig);
    };
});
