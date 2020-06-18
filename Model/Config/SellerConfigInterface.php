<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to https://www.fcamara.com.br/ for more information.
 *
 * @category  FCamara
 * @package   FCamara_
 * @copyright Copyright (c) 2020 FCamara Formação e Consultoria
 * @Agency    FCamara Formação e Consultoria, Inc. (http://www.fcamara.com.br)
 * @author    Danilo Cavalcanti de Moura <danilo.moura@fcamara.com.br>
 */

namespace FCamara\Getnet\Model\Config;

interface SellerConfigInterface
{
    /**
     * Enabled config path
     */
    public const XML_PATH_ENABLED = 'getnet/split/enabled';

    /**
     * Api environment config path
     */
    public const XML_PATH_ENVIRONMENT = 'payment/getnet/environment';

    /**
     * Api sandbox endpoint
     */
    public const XML_PATH_SANDBOX_ENDPOINT = 'payment/getnet/sandbox_endpoint';

    /**
     * Api production endpoint
     */
    public const XML_PATH_PRODUCTION_ENDPOINT = 'payment/getnet/production_endpoint';

    /**
     * Api seller id config path
     */
    public const XML_PATH_SELLER_ID = 'payment/getnet/seller_id';

    /**
     * Api client id config path
     */
    public const XML_PATH_CLIENT_ID = 'payment/getnet/client_id';

    /**
     * Api client secret config path
     */
    public const XML_PATH_CLIENT_SECRET = 'payment/getnet/client_secret';

    public const XML_PATH_MERCHANT_ID = 'payment/getnet/merchant_id';

    /**
     * Check if module is enabled
     *
     * @return bool
     * @since 102.0.3
     */
    public function isEnabled();

    /**
     * Return seller id
     *
     * @return string
     * @since 102.0.3
     */
    public function sellerId();

    /**
     * Return client Id
     *
     * @return string
     * @since 102.0.3
     */
    public function clientId();

    /**
     * Return client secret
     *
     * @return string
     * @since 102.0.3
     */
    public function clientSecret();

    /**
     * @return mixed
     */
    public function authenticationEndpoint();

    /**
     * Return sandbox
     *
     * @return string
     * @since 102.0.3
     */
    public function sandboxEndpoint();

    /**
     * Return production
     *
     * @return string
     * @since 102.0.3
     */
    public function productionEndpoint();

    /**
     * @param $merchantId
     * @param $cpf
     * @return mixed
     */
    public function pfCallbackEndpoint($merchantId, $cpf);

    /**
     * @param $merchantId
     * @return mixed
     */
    public function pfConsultPaymentPlansEndpoint($merchantId);

    /**
     * @return mixed
     */
    public function pfCreatePreSubSellerEndpoint();

    /**
     * @return mixed
     */
    public function pfComplementEndpoint();

    /**
     * @param $merchantId
     * @param $subSellerId
     * @return mixed
     */
    public function pfDeAccreditEndpoint($merchantId, $subSellerId);

    /**
     * @return mixed
     */
    public function pfUpdateSubSellerEndpoint();

    /**
     * @param $merchantId
     * @param $cnpj
     * @return mixed
     */
    public function pjConsultEndpoint($merchantId, $cnpj);

    /**
     * @param $merchantId
     * @param $cnpj
     * @return mixed
     */
    public function pjCallbackEndpoint($merchantId, $cnpj);

    /**
     * @param $merchantId
     * @return mixed
     */
    public function pjConsultPaymentPlansEndpoint($merchantId);

    /**
     * @return mixed
     */
    public function pjCreatePreSubSellerEndpoint();

    /**
     * @return mixed
     */
    public function pjComplementEndpoint();

    /**
     * @param $merchantId
     * @param $subSellerId
     * @return mixed
     */
    public function pjDeAccreditEndpoint($merchantId, $subSellerId);

    /**
     * @return mixed
     */
    public function pjUpdateSubSellerEndpoint();

    /**
     * @return mixed
     */
    public function adjustmentRequestAdjustmentsEndpoint();

    /**
     * @return mixed
     */
    public function adjustmentRequestBilletReversalEndpoint();

    /**
     * @return mixed
     */
    public function adjustmentCheckStatusEndpoint();

    /**
     * @return mixed
     */
    public function statementEndpoint();

    /**
     * @return mixed
     */
    public function paginatedStatementEndpoint();

    /**
     * @return mixed
     */
    public function endpoint();
}
