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

            getSellerId: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.seller_id;
            },

            getToken: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.token;
            },

            getAmount: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.amount;
            },

            getCustomerId: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.customerid;
            },

            getOrderId: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.orderid;
            },

            getButtonClass: function () {
                return 'pay-button-getnet';
            },

            getInstallments: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.installments;
            },

            getCustomerFirstName: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.customer.first_name;
            },

            getCustomerLastName: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.customer.last_name;
            },

            getCustomerDocumentType: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.customer.document_type;
            },

            getCustomerDocumentNumber: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.customer.document_number;
            },

            getCustomerEmail: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.customer.email;
            },

            getCustomerPhoneNumber: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.customer.phone_number;
            },

            getCustomerAddressStreet: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.customer.billing_address.street;
            },

            getCustomerAddressStreetNumber: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.customer.billing_address.number;
            },

            getCustomerAddressNeighborhood: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.customer.billing_address.neighborhood;
            },

            getCustomerAddressCity: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.customer.billing_address.city;
            },

            getCustomerAddressState: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.customer.billing_address.state;
            },

            getCustomerAddressZipcode: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.customer.billing_address.postal_code;
            },

            getCustomerCountry: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.customer.billing_address.country;
            },

            getGetItems: function () {
                return window.checkoutConfig.payment.getnet_checkout_iframe.items;
            },

            placeCheckoutIframe: function () {
                var quoteId = window.checkoutConfig.quoteData.entity_id;
                let getnetCheckoutIfrm = jQuery('#getnet-checkout');
                let scriptTag = jQuery('#container-checkout-iframe script');

                jQuery.ajax({
                    showLoader: true,
                    url: window.checkout.baseUrl + 'rest/V1/getnet/checkoutiframe/amount/' + quoteId,
                    type: "GET",
                }).fail(function(data) {
                    console.error(data);
                    window.location.reload();
                }).done(function (data) {
                    scriptTag.attr('data-getnet-amount', data);

                    if (getnetCheckoutIfrm.length) {
                        getnetCheckoutIfrm.show();
                    } else {
                        jQuery('#container-checkout-iframe .action.primary.checkout.pay-button-getnet').trigger('click');
                    }
                }.bind(this));
            }
        });
    }
);