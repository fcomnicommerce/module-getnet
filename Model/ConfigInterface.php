<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://fcamara.com.br for more information.
 *
 * @category  Fcamara
 * @package   Fcamara_Getnet
 * @copyright Copyright (c) 2019 FCamara Formação e Consultoria
 * @Agency    FCamara Formação e Consultoria, Inc. (http://www.fcamara.com.br)
 * @author    Jonatan Santos <jonatan.santos@fcamara.com.br>
 */

namespace FCamara\Getnet\Model;

interface ConfigInterface
{
    /**
     * Billet enabled config path
     */
    const XML_PATH_BILLET_ACTIVE = 'payment/getnet_billet/active';

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
     * Api billet provider
     */
    const XML_PATH_BILLET_PROVIDER = 'payment/getnet_billet/provider';

    /**
     * Enabled config path
     */
    const XML_PATH_ENABLED = 'payment/getnet/enabled';

    /**
     * Check if getnet module is enabled
     *
     * @return bool
     * @since 100.2.0
     */
    public function isEnabled();

    /**
     * Return client Id
     *
     * @return string
     * @since 100.2.0
     */
    public function clientId();

    /**
     * Return client secret
     *
     * @return string
     * @since 100.2.0
     */
    public function clientSecret();

    /**
     * Return seller id
     *
     * @return string
     * @since 100.2.0
     */
    public function sellerId();

    /**
     * Return environment
     *
     * @return string
     * @since 100.2.0
     */
    public function environment();

    /**
     * Return sandbox
     *
     * @return string
     * @since 100.2.0
     */
    public function sandboxEndpoint();

    /**
     * Return endpoint
     *
     * @return string
     * @since 100.2.0
     */
    public function endpoint();

    /**
     * Return production
     *
     * @return string
     * @since 100.2.0
     */
    public function productionEndpoint();

    /**
     * Check if billet method is enabled
     *
     * @return string
     * @since 100.2.0
     */
    public function billetEnabled();

    /**
     * Check auth endpoint
     *
     * @return string
     * @since 100.2.0
     */
    public function authorizationEndpoint();

    /**
     * Billet registration endpoint
     *
     * @return string
     * @since 100.2.0
     */
    public function billetRegistrationEndpoint();

    /**
     * Billet provider
     *
     * @return string
     * @since 100.2.0
     */
    public function billetProvider();
}
