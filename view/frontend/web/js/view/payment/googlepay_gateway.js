define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'googlepay_gateway',
                component: 'Apexx_Googlepay/js/view/payment/method-renderer/googlepay_gateway'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
