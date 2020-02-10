/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to https://www.fcamara.com.br/ for more information.
 *
 * @category  FCamara
 * @package   FCamara_Getnet
 * @copyright Copyright (c) 2020 Getnet
 * @Agency    FCamara Formação e Consultoria, Inc. (http://www.fcamara.com.br)
 * @author    Danilo Cavalcanti de Moura <danilo.moura@fcamara.com.br>
 */

define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/checkout-data',
        'Magento_Vault/js/view/payment/method-renderer/vault'
    ],
    function (Component, selectPaymentMethod, checkoutData, VaultComponent) {
        'use strict';

        return VaultComponent.extend({
            defaults: {
                template: 'Magento_Vault/payment/form'
            },

            /**
             * @returns {exports.initObservable}
             */
            initObservable: function () {
                this._super()
                    .observe([]);

                return this;
            },

            /**
             * @returns
             */
            selectPaymentMethod: function () {
                selectPaymentMethod(
                    {
                        method: this.getId()
                    }
                );
                checkoutData.setSelectedPaymentMethod(this.getId());

                return true;
            },

            /**
             * @returns {String}
             */
            getTitle: function () {
                return '';
            },

            /**
             * @returns {String}
             */
            getToken: function () {
                return '';
            },

            /**
             * @returns {String}
             */
            getId: function () {
                return this.index;
            },

            /**
             * @returns {String}
             */
            getCode: function () {
                return this.code;
            },

            /**
             * Get last 4 digits of card
             * @returns {String}
             */
            getMaskedCard: function () {
                return '';
            },

            /**
             * Get expiration date
             * @returns {String}
             */
            getExpirationDate: function () {
                return '';
            },

            /**
             * Get card type
             * @returns {String}
             */
            getCardType: function () {
                return '';
            },

            /**
             * @param {String} type
             * @returns {Boolean}
             */
            getIcons: function (type) {
                return window.checkoutConfig.payment.ccform.icons.hasOwnProperty(type) ?
                    window.checkoutConfig.payment.ccform.icons[type]
                    : false;
            },

            /**
             * @returns {*}
             */
            getData: function () {
                var data = {
                    method: this.getCode()
                };

                data['additional_data'] = {};
                data['additional_data']['public_hash'] = this.getToken();

                return data;
            }
        });
    }
);
