/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'FCamara_Getnet/payment/credit_card/form',
                transactionResult: ''
            },

            initObservable: function () {

                // this._super()
                //     .observe([
                //         'transactionResult'
                //     ]);
                return this;
            },

            getCode: function() {
                return 'getnet_credit_card';
            },

            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'transaction_result': 'AAAAAAAAAAAA'
                    }
                };
            },

        });
    }
);