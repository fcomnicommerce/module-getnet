define([
    'jquery',
    'mage/storage'
], function ($, storage) {
    'use strict';

    return function (amount, currency, customerId, orderId) {

        return storage.post(
            'rest/V1/getnet/generateqrcode',
            JSON.stringify({
                amount: amount,
                currency: currency,
                orderId: orderId,
                customerId: customerId
            }),
            false
        ).done(function (result) {

            let object = JSON.parse(result);
            let $result = $('div#result_qrcode');

            if (object.status == "WAITING") {
                let $img = '<img src="' + object.code_generated + '" alt="QR Code" width="130px" />'
                let $code = '<input type="text" style="display: none;" id="code" value="' + object.code + '">';
                $result.html($img);

                $result.append($code);
            }

        }).fail(function () {
            console.log('fail');
        });
    };
});
