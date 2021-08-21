<?php

namespace FCamara\Getnet\Model\Ui\Pix;

use FCamara\Getnet\Model\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider
{
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
}
