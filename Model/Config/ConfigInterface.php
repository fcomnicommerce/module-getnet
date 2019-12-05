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
 * @copyright Copyright (c) 2019 FCamara Formação e Consultoria
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
     * Return authentication endpoint
     *
     * @return string
     * @since 102.0.3
     */
    public function authenticationEndpoint();
}
