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

namespace FCamara\Getnet\Model\Ui\CreditCard;

use FCamara\Getnet\Model\ConfigInterface;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider implements ConfigProviderInterface, ConfigInterface
{
    const CODE = 'getnet_credit_card';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'info' => 'no info to return'
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritdoc}
     */
    public function clientId()
    {
        return $this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_CLIENT_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritdoc}
     */
    public function clientSecret()
    {
        return $this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_CLIENT_SECRET,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritdoc}
     */
    public function sellerId()
    {
        return $this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_SELLER_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritdoc}
     */
    public function billetEnabled()
    {
        return $this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_BILLET_ACTIVE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return environment
     *
     * @return string
     * @since 100.2.0
     */
    public function environment()
    {
        return $this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_ENVIRONMENT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return sandbox
     *
     * @return string
     * @since 100.2.0
     */
    public function sandboxEndpoint()
    {
        return $this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_SANDBOX_ENDPOINT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return production
     *
     * @return string
     * @since 100.2.0
     */
    public function productionEndpoint()
    {
        return $this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_PRODUCTION_ENDPOINT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return endpoint
     *
     * @return string
     * @since 100.2.0
     */
    public function endpoint()
    {
        if ($this->environment() == \FCamara\Getnet\Model\Adminhtml\Source\Environment::SANDBOX_ENVIRONMENT) {
            return $this->sandboxEndpoint();
        }

        return $this->productionEndpoint();
    }

    /**
     * Return endpoint
     *
     * @return string
     * @since 100.2.0
     */
    public function authorizationEndpoint()
    {
        return $this->endpoint() . '/auth/oauth/v2/token';
    }

    /**
     * Return endpoint
     *
     * @return string
     * @since 100.2.0
     */
    public function billetRegistrationEndpoint()
    {
        return $this->endpoint() . '/v1/payments/boleto';
    }

    /**
     * Return production
     *
     * @return string
     * @since 100.2.0
     */
    public function billetProvider()
    {
        return $this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_BILLET_PROVIDER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return production
     *
     * @return string
     * @since 100.2.0
     */
    public function ourNumber()
    {
        return $this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_BILLET_OUR_NUMBER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return production
     *
     * @return string
     * @since 100.2.0
     */
    public function expirationDays()
    {
        return $this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_BILLET_EXPIRATION_DAYS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return instructions
     *
     * @return string
     * @since 100.2.0
     */
    public function instructions()
    {
        return $this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_BILLET_INSTRUCTIONS,
            ScopeInterface::SCOPE_STORE
        );
    }
}
