<?php
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
 * @author    Jonatan Santos <jonatan.santos@fcamara.com.br>
 */

namespace FCamara\Getnet\Model\Config;


interface CreditCardConfigInterface
{
    /**
     * Credit card active config path
     */
    const XML_PATH_ACTIVE = 'payment/getnet_credit_card/active';

    /**
     * Credit card title config path
     */
    const XML_PATH_TITLE = 'payment/getnet_credit_card/title';

    /**
     * Credit card provider config path
     */
    const XML_PATH_PROVIDER = 'payment/getnet_credit_card/provider';

    /**
     * Credit card payment action config path
     */
    const XML_PATH_PAYMENT_ACTION = 'payment/getnet_credit_card/payment_action';

    /**
     * Credit card order status config path
     */
    const XML_PATH_ORDER_STATUS = 'payment/getnet_credit_card/order_status';

    /**
     * Credit card sort order config path
     */
    const XML_PATH_SORT_ORDER = 'payment/getnet_credit_card/sort_order';

    /**
     * Credit card installments config path
     */
    const XML_PATH_INSTALLMENTS = 'payment/getnet_credit_card/installments';

    /**
     * Credit card qty installments config path
     */
    const XML_PATH_QTY_INSTALLMENTS = 'payment/getnet_credit_card/qty_installments';

    /**
     * Credit card minimum installment config path
     */
    const XML_PATH_MIN_INSTALLMENTS = 'payment/getnet_credit_card/min_installment';

    /**
     * Credit card installments interest in %
     */
    const XML_PATH_INSTALLMENTS_INTEREST = 'payment/getnet_credit_card/installments_interest';

    /**
     * Credit card minimum installment config path
     */
    const XML_PATH_MAX_NON_INTEREST_INSTALLMENTS = 'payment/getnet_credit_card/max_non_interest_installments';


    /**
     * Check if getnet credit card method is active
     *
     * @return bool
     * @since 102.0.3
     */
    public function isActive();

    /**
     * Return credit card title
     *
     * @return string
     * @since 102.0.3
     */
    public function title();

    /**
     * Return credit card provider
     *
     * @return string
     * @since 102.0.3
     */
    public function provider();

    /**
     * Return credit card payment action
     *
     * @return string
     * @since 102.0.3
     */
    public function paymentAction();

    /**
     * Return credit card order status
     *
     * @return string
     * @since 102.0.3
     */
    public function orderStatus();

    /**
     * Return credit card sort order
     *
     * @return string
     * @since 102.0.3
     */
    public function sortOrder();

    /**
     * Return credit card installments
     *
     * @return string
     * @since 102.0.3
     */
    public function installments();

    /**
     * Return credit card verification endpoint
     *
     * @return string
     * @since 102.0.3
     */
    public function verificationEndpoint();

    /**
     * Return credit card authorize endpoint
     *
     * @return string
     * @since 102.0.3
     */
    public function authorizeEndpoint();

    /**
     * Return credit card capture endpoint
     *
     * @return string
     * @since 102.0.3
     */
    public function captureEndpoint();

    /**
     * Return credit card void endpoint
     *
     * @return string
     * @since 102.0.3
     */
    public function voidEndpoint();

    /**
     * Return credit card qty installments
     *
     * @return mixed
     */
    public function qtyInstallments();

    /**
     * Return credit card minimum installment value
     *
     * @return mixed
     */
    public function minInstallment();

    /**
     * Return credit card installment interest in %
     *
     * @return mixed
     */
    public function installmentInterest();

    /**
     * Return maximum installments with no interest
     *
     * @return mixed
     */
    public function maxNonInterestInstalments();
}
