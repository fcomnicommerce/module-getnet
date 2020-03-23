/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default'
    ],
    function ($, Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'FCamara_Getnet/payment/form',
                transactionResult: ''
            },

            initObservable: function () {
                this._super()
                    .observe([
                        'transactionResult'
                    ]);
                return this;
            },

            /**
             * Check if payment is active
             *
             * @returns {Boolean}
             */
            isActive: function () {
                var active = this.getCode() === this.isChecked();

                this.active(active);

                return active;
            },

            /**
             * Get authorization
             * @returns {Object}
             */
            getAuthorizationBasic: function () {
                return window.checkoutConfig.payment.getnet_credit_card.authorizationBasic;
            },

            /**
             * Get authorization
             * @returns {Object}
             */
            getEndpoint: function () {
                return window.checkoutConfig.payment.getnet_credit_card.endpoint;
            },

            getCode: function() {
                return 'getnet_billet';
            },

            /**
             * Show error messages
             *
             * @param {String[]} errorMessages
             */
            _showErrors: function (errorMessages) {
                $.each(errorMessages, function (index, message) {
                    globalMessageList.addErrorMessage({
                        message: message
                    });
                });
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