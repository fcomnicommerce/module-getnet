/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Payment/js/view/payment/cc-form'
    ],
    function ($, Component) {
        'use strict';

        return Component.extend({
            defaults: {
                active: false,
                template: 'FCamara_Getnet/payment/credit_card/form',
                ccForm: 'Magento_Payment/payment/cc-form',
                transactionResult: '',
                creditCardType: '',
                creditCardExpYear: '',
                creditCardExpMonth: '',
                creditCardName: '',
                creditCardNumber: '',
                creditCardNumberToken: '',
                creditCardSsStartMonth: '',
                creditCardSsStartYear: '',
                creditCardSsIssue: '',
                creditCardVerificationNumber: '',
                selectedCardType: null
            },

            initObservable: function () {

                this._super()
                    .observe([
                        'active',
                        'transactionResult',
                        'creditCardType',
                        'creditCardExpYear',
                        'creditCardExpMonth',
                        'creditCardNumber',
                        'creditCardNumberToken',
                        'creditCardName',
                        'creditCardVerificationNumber',
                        'creditCardSsStartMonth',
                        'creditCardSsStartYear',
                        'creditCardSsIssue',
                        'selectedCardType'
                    ]);
                return this;
            },

            getCode: function() {
                return 'getnet_credit_card';
            },

            /**
             * Initialize form elements for validation
             */
            initFormElement: function (element) {
                this.formElement = element;
                $(this.formElement).validation();
            },

            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'cc_cid': this.creditCardVerificationNumber(),
                        'cc_type': this.creditCardType(),
                        'cc_exp_year': this.creditCardExpYear(),
                        'cc_exp_month': this.creditCardExpMonth(),
                        'cc_number_token': this.creditCardNumberToken(),
                        'cc_name': this.creditCardName()
                    }
                };
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

            beforePlaceOrder: function () {
                console.log('passei aqui antes de fechar o pedido');

                var endpoint = this.getEndpoint();
                var authorization = this.getAuthorizationBasic();
                var creditCardNumber = this.creditCardNumber();

                $.ajax({
                    showLoader: true,
                    url: endpoint + 'auth/oauth/v2/token',
                    beforeSend: function (request) {
                        request.setRequestHeader("Authorization", authorization);
                        request.setRequestHeader("content-type", 'application/x-www-form-urlencoded');
                        request.setRequestHeader("Accept", 'application/json');
                    },
                    data: 'scope=oob&grant_type=client_credentials',
                    type: "POST",
                    dataType: 'json'
                }).fail(function(data) {
                    console.log(data);
                }).done(function (data) {
                    console.log(data);
                    if(data.access_token) {
                        $.ajax({
                            showLoader: true,
                            url: endpoint + 'v1/tokens/card',
                            beforeSend: function (request) {
                                request.setRequestHeader("Authorization", 'Bearer ' + data.access_token);
                                request.setRequestHeader("Accept", 'application/json');
                            },
                            data: {
                                "card_number": creditCardNumber
                            },
                            type: "POST",
                            dataType: 'json'
                        }).fail(function(data) {
                            console.log(data);
                        }).done(function (data) {
                            this.creditCardNumberToken(data.number_token);
                            this.placeOrder();
                        }.bind(this));
                    }
                }.bind(this));

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
            }

        });
    }
);