define([
    'jquery',
    'mage/storage'
], function ($, storage) {
    'use strict';

    return function (orderId) {

        return storage.get(
            'rest/V1/getnet/status-order/'+orderId,
            false
        ).done(function (result) {

            let $result = $('div#result_qrcode');

            if (result == "complete") {
                $result.data('valid', false);
                $result.hide();
            }

        }).fail(function () {
            console.log('fail');
        });
    };
});
