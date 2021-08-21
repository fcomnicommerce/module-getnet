define([
    'uiComponent',
    'jquery',
    'FCamara_Getnet/js/action/qrcode-pix',
    'FCamara_Getnet/js/action/validation-status-order'
], function (Component, $, qrcodePix, validationStatusOrder) {
    'use strict';

    return Component.extend({

        initialize: function () {
            let that = this;
            let $result = $('div#result_qrcode');
            let $img = $result.find("img");

            this._super();

            $result.data('qrcode', true);
            $result.data('valid', true);

            this.generateQRCode();

            let interval = setInterval(function() {
                that.generateQRCode();

                if (!$result.data('qrcode')) {
                    clearInterval(interval);
                }
            }, 100000);

            let validInterval = setInterval(function() {

                that.validationStatus();

                if (!$result.data('valid')) {
                    clearInterval(validInterval);
                }

            }, 10000);
        },

        validationStatus: function() {
            let orderId = $('input#orderId').val();
            validationStatusOrder(orderId);
        },

        generateQRCode: function() {
            let amount = $('input#amount').val();
            let currency = $('input#currency').val();
            let customerId = $('input#customerId').val();
            let orderId = $('input#orderId').val();

            qrcodePix(amount, currency, customerId, orderId);
        }
    });
});
