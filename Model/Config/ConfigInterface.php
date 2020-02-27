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


interface ConfigInterface
{
    /**
     * Enabled config path
     */
    const XML_PATH_ENABLED = 'payment/getnet/enabled';

    /**
     * Api seller id config path
     */
    const XML_PATH_SELLER_ID = 'payment/getnet/seller_id';

    /**
     * Api client id config path
     */
    const XML_PATH_CLIENT_ID = 'payment/getnet/client_id';

    /**
     * Api client secret config path
     */
    const XML_PATH_CLIENT_SECRET = 'payment/getnet/client_secret';

    /**
     * Api environment config path
     */
    const XML_PATH_ENVIRONMENT = 'payment/getnet/environment';

    /**
     * Api sandbox endpoint
     */
    const XML_PATH_SANDBOX_ENDPOINT = 'payment/getnet/sandbox_endpoint';

    /**
     * Api production endpoint
     */
    const XML_PATH_PRODUCTION_ENDPOINT = 'payment/getnet/production_endpoint';

    /**
     * Api fingerprint endpoint
     */
    const XML_PATH_FINGERPRINT_ENDPOINT = 'payment/getnet/fingerprint_endpoint';

    /**
     * Api fingerprint sandbox param ord_id
     */
    const XML_PATH_FINGERPRINT_SANDBOX_ORG_ID = 'payment/getnet/fingerprint_sandbox_org_id';

    /**
     * Api fingerprint production param ord_id
     */
    const XML_PATH_FINGERPRINT_PRODUCTION_ORG_ID = 'payment/getnet/fingerprint_production_org_id';

    /**
     * Check if CPF is the same as CNPJ
     */
    const XML_PATH_CPF_SAME_AS_CNPJ = 'payment/getnet/cpf_same_as_cnpj';

    /**
     * Document Number
     */
    const XML_PATH_DOCUMENT_ATTRIBUTE = 'payment/getnet/document_attribute';

    /**
     * CPF attribute
     */
    const XML_PATH_CPF_ATTRIBUTE = 'payment/getnet/cpf_attribute';

    /**
     * CNPJ attribute
     */
    const XML_PATH_CNPJ_ATTRIBUTE = 'payment/getnet/cnpj_attribute';

    /**
     * CNPJ attribute
     */
    const XML_PATH_STREET = 'payment/getnet/street';

    /**
     * CNPJ attribute
     */
    const XML_PATH_NUMBER = 'payment/getnet/number';

    /**
     * CNPJ attribute
     */
    const XML_PATH_COMPLEMENT = 'payment/getnet/complement';

    /**
     * CNPJ attribute
     */
    const XML_PATH_DISTRICT = 'payment/getnet/district';

    /**
     * Enabled config path figerprint
     */

    const XML_PATH_ENABLED_FINGERPRINT = 'payment/getnet/enabled_fingerprint';

    /**
     * Enabled config path Checkout Iframe
     */
    const XML_PATH_ENABLED_CHECKOUT_IFRAME = 'payment/getnet/checkout_iframe_enabled';

    /**
     * Check if getnet module is enabled
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
     * Return environment
     *
     * @return string
     * @since 102.0.3
     */
    public function environment();

    /**
     * Return endpoint
     *
     * @return string
     * @since 102.0.3
     */
    public function endpoint();

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
     * Return if CPF is the same attribute as CNPJ
     *
     * @return string
     * @since 102.0.3
     */
    public function cpfSameAsCnpj();

    /**
     * Return Document Attribute
     *
     * @return string
     * @since 102.0.3
     */
    public function documentAttribute();

    /**
     * Return CPF Attribute
     *
     * @return string
     * @since 102.0.3
     */
    public function cpfAttribute();

    /**
     * Return CNPJ Attribute
     *
     * @return string
     * @since 102.0.3
     */
    public function cnpjAttribute();

    /**
     * Return street line
     *
     * @return string | null
     * @since 102.0.3
     */
    public function streetLine();

    /**
     * Return number line
     *
     * @return string | null
     * @since 102.0.3
     */
    public function numberLine();

    /**
     * Return complement line
     *
     * @return string | null
     * @since 102.0.3
     */
    public function complementLine();

    /**
     * Return district line
     *
     * @return string | null
     * @since 102.0.3
     */
    public function districtLine();

    /**
     * Return authentication endpoint
     *
     * @return string
     * @since 102.0.3
     */
    public function authenticationEndpoint();

    /**
     * Return fingerprint endpoint
     *
     * @return string
     * @since 102.0.3
     */
    public function fingerprintEndpoint();

    /**
     * Return fingerprint sandbox param org_id
     *
     * @return mixed
     */
    public function fingerprintSandboxOrgId();

    /**
     * Return fingerprint production param org_id
     *
     * @return mixed
     */
    public function fingerprintProductionOrgId();

    /**
     * Return fingerprint is enabled
     *
     * @return mixed
     */
    public function isEnabledFingerprint();

    /**
     * Return Checkout Iframe is enabled
     *
     * @return mixed
     */
    public function isEnabledCheckoutIframe();
}
