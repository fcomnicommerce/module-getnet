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
        'jquery'
    ],
    function (Component, jQuery) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'FCamara_Getnet/payment/getnet_checkout_iframe'
            },

/*            initObservable: function () {
                alert('initObservable');
                jQuery('#checkout_iframe script').attr('data-getnet-test', '321');
                return this;
            },*/

            /** Returns send check to info */
            getMailingAddress: function() {
                return window.checkoutConfig.payment.getnet_checkout_iframe.mailingAddress;
            },

            /** Returns payable to info */
            getPayableTo: function() {
                return window.checkoutConfig.payment.getnet_checkout_iframe.payableTo;
            },

            getUrl: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.url;
            },

            getSellerId() {
                return window.checkoutConfig.payment.getnet_checkout_iframe.seller_id;
            }
        });
    }
);