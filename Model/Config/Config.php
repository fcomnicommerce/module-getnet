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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config implements ConfigInterface
{

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function sellerId()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SELLER_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function clientId()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CLIENT_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function clientSecret()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CLIENT_SECRET,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function environment()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENVIRONMENT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function endpoint()
    {
        if ($this->environment() == \FCamara\Getnet\Model\Adminhtml\Source\Environment::SANDBOX_ENVIRONMENT) {
            return $this->sandboxEndpoint();
        }

        return $this->productionEndpoint();
    }

    /**
     * {@inheritDoc}
     */
    public function sandboxEndpoint()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SANDBOX_ENDPOINT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function productionEndpoint()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCTION_ENDPOINT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function authenticationEndpoint()
    {
        return $this->endpoint() . '/auth/oauth/v2/token';
    }

    /**
     * {@inheritDoc}
     */
    public function cpfSameAsCnpj()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CPF_SAME_AS_CNPJ,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function documentAttribute()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DOCUMENT_ATTRIBUTE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function cpfAttribute()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CPF_ATTRIBUTE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function cnpjAttribute()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CNPJ_ATTRIBUTE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function streetLine()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STREET,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function numberLine()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NUMBER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function complementLine()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_COMPLEMENT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function districtLine()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DISTRICT,
            ScopeInterface::SCOPE_STORE
        );
    }
}
