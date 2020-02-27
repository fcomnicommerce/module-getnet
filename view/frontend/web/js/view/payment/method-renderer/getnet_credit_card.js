/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Payment/js/view/payment/cc-form',
        'Magento_Payment/js/model/credit-card-validation/credit-card-data',
        'card'
    ],
    function ($, Component, card) {
        'use strict';

        return Component.extend({
            defaults: {
                active: false,
                template: 'FCamara_Getnet/payment/credit_card/form',
                ccForm: 'FCamara_Getnet/payment/cc-form',
                transactionResult: '',
                creditCardType: '',
                creditCardExpYear: '',
                creditCardExpMonth: '',
                creditCardName: '',
                creditCardExpiry: '',
                creditCardNumber: '',
                creditCardNumberToken: '',
                creditCardSsStartMonth: '',
                creditCardSsStartYear: '',
                creditCardSsIssue: '',
                creditCardVerificationNumber: '',
                creditCardInstallment: '',
                selectedCardType: null,
                saveCardData: '',
                cardId: null
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
                        'creditCardExpiry',
                        'creditCardInstallment',
                        'creditCardVerificationNumber',
                        'creditCardSsStartMonth',
                        'creditCardSsStartYear',
                        'creditCardSsIssue',
                        'selectedCardType',
                        'saveCardData',
                        'cardId'
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

                new Card({
                    form: document.querySelector('.getnet-card'),
                    container: '.card-wrapper'
                });
            },

            getCcInstallmentValues: function () {
                var allInstallments = [];
                var totalPrice  = window.checkoutConfig.quoteData.grand_total;
                var installmentsQty = window.checkoutConfig.payment.getnet_credit_card.qty_installments;

                for (var i = 1; i <= installmentsQty; i++) {
                    var installmentPrice = (totalPrice/i).toFixed(2).toString().replace(".", ",");
                    allInstallments.push({
                        'value': i,
                        'installment': i + ' x R$ ' + installmentPrice
                    });
                }

                return allInstallments;
            },

            /**
             * Return cards saved
             * @returns {Array}
             */
            getSavedCardsList: function () {
                return window.checkoutConfig.payment.saved_cards;
            },

            changeSavedCard: function(id) {
                if (id) {
                    jQuery('div.field.type, div.field.name.required, div.field.number.required, div.field.expiry.required, div.field.save-card-data, div.card-wrapper').fadeOut();
                } else {
                    jQuery('#co-payment-form .payment-method._active input').val('');
                    jQuery('div.field.type, div.field.name.required, div.field.number.required, div.field.expiry.required, div.field.save-card-data, div.card-wrapper').fadeIn('low');
                }
            },

            getData: function() {
                let creditCardExpiry = this.creditCardExpiry();
                let expiryArray = creditCardExpiry.split("/");
                let exp_year = '';
                let exp_month = '';
                if(expiryArray.length === 2) {
                    exp_month = expiryArray[0];
                    exp_month = exp_month.trim();
                    exp_year = expiryArray[1];
                    exp_year = exp_year.trim();
                }
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'cc_cid': this.creditCardVerificationNumber(),
                        'cc_type': this.creditCardType(),
                        'cc_exp_year': exp_year,
                        'cc_exp_month': exp_month,
                        'cc_number_token': this.creditCardNumberToken(),
                        'cc_name': this.creditCardName(),
                        'cc_expiry': this.creditCardExpiry(),
                        'cc_installment': this.creditCardInstallment(),
                        'save_card_data': this.saveCardData(),
                        'cc_number': this.creditCardNumber(),
                        'card_id': this.cardId()
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

            beforePlaceOrder: function () {
                var endpoint = this.getEndpoint();
                var authorization = this.getAuthorizationBasic();
                var creditCardNumber = this.creditCardNumber();
                creditCardNumber = creditCardNumber.replace(/\s/g, '');

                if (this.cardId()) {
                    var savedCards = window.checkoutConfig.payment.saved_cards;

                    for (var i = 0; i < savedCards.length; i++) {
                        if (savedCards[i].card_id === this.cardId()) {
                            var card = savedCards[i];
                            this.creditCardType(card.brand);
                            this.creditCardExpiry(card.expiration_month + '/' + card.expiration_year);
                            this.creditCardNumberToken(card.number_token);
                            this.creditCardName(card.cardholder_name);
                            this.saveCardData(false);
                            this.cardId(card.id);

                            this.placeOrder();
                        }
                    }
                } else {
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
                                    request.setRequestHeader("Content-type", 'application/json; charset=utf-8');
                                },
                                data: JSON.stringify({card_number: creditCardNumber}),
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
                }
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