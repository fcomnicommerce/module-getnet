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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class SellerConfig implements SellerConfigInterface
{
    /**
     * @var ScopeInterface
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
     * @return string
     */
    public function clientSecret()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CLIENT_SECRET,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function merchantId()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MERCHANT_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function guarantorDocumentType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GUARANTOR_DOCUMENT_TYPE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function guarantorDocumentNumber()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GUARANTOR_DOCUMENT_NUMBER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function guarantorName()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GUARANTOR_NAME,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function environment()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENVIRONMENT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed|string
     */
    public function endpoint()
    {
        if ($this->environment() == \FCamara\Getnet\Model\Adminhtml\Source\Environment::SANDBOX_ENVIRONMENT) {
            return $this->sandboxEndpoint();
        }

        return $this->productionEndpoint();
    }

    /**
     * @return string
     */
    public function sandboxEndpoint()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SANDBOX_ENDPOINT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function productionEndpoint()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCTION_ENDPOINT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed|string
     */
    public function authenticationEndpoint()
    {
        return $this->endpoint() . '/credenciamento/auth/oauth/v2/token';
    }


    /**
     * @param $merchantId
     * @param $cpf
     * @return mixed|string
     */
    public function pfCallbackEndpoint($merchantId, $cpf)
    {
        return $this->endpoint() . '/v1/mgm/pf/callback/' . $merchantId . '/' . $cpf;
    }

    /**
     * @param $merchantId
     * @return mixed|string
     */
    public function pfConsultPaymentPlansEndpoint($merchantId)
    {
        return $this->endpoint() . '/v1/mgm/pf/consult/paymentplans/' . $merchantId;
    }

    /**
     * @return mixed
     */
    public function pfCreatePreSubSellerEndpoint()
    {
        return $this->endpoint() . '/v1/mgm/pf/create-presubseller';
    }

    /**
     * @return mixed
     */
    public function pfComplementEndpoint()
    {
        return $this->endpoint() . '/v1/mgm/pf/complement';
    }

    /**
     * @param $merchantId
     * @param $subSellerId
     * @return mixed|string
     */
    public function pfDeAccreditEndpoint($merchantId, $subSellerId)
    {
        return $this->endpoint() . '/v1/mgm/pf/de-accredit/' . $merchantId . '/' . $subSellerId;
    }

    /**
     * @return mixed
     */
    public function pfUpdateSubSellerEndpoint()
    {
        return $this->endpoint() . '/v1/mgm/pf/update-subseller';
    }

    /**
     * @param $merchantId
     * @param $cnpj
     * @return mixed|string
     */
    public function pjConsultEndpoint($merchantId, $cnpj)
    {
        return $this->endpoint() . '/v1/mgm/pj/consult/' . $merchantId . '/' . $cnpj;
    }

    /**
     * @param $merchantId
     * @param $cnpj
     * @return mixed|string
     */
    public function pjCallbackEndpoint($merchantId, $cnpj)
    {
        return $this->endpoint() . '/v1/mgm/pj/callback/' . $merchantId . '/' . $cnpj;
    }

    /**
     * @param $merchantId
     * @return mixed|string
     */
    public function pjConsultPaymentPlansEndpoint($merchantId)
    {
        return $this->endpoint() . '/v1/mgm/pj/consult/paymentplans/' . $merchantId;
    }

    /**
     * @return mixed|string
     */
    public function pjCreatePreSubSellerEndpoint()
    {
        return $this->endpoint() . '/v1/mgm/pj/create-presubseller';
    }

    /**
     * @return mixed|string
     */
    public function pjComplementEndpoint()
    {
        return $this->endpoint() . '/v1/mgm/pj/complement';
    }

    /**
     * @param $merchantId
     * @param $subSellerId
     * @return mixed|string
     */
    public function pjDeAccreditEndpoint($merchantId, $subSellerId)
    {
        return $this->endpoint() . '/v1/mgm/pj/de-accredit/' . $merchantId . '/' . $subSellerId;
    }

    /**
     * @return mixed
     */
    public function pjUpdateSubSellerEndpoint()
    {
        return $this->endpoint() . '/v1/mgm/pj/update-subseller';
    }

    /**
     * @return mixed
     */
    public function adjustmentRequestAdjustmentsEndpoint()
    {
        return $this->endpoint() . '/v1/mgm/adjustment/request-adjustments';
    }

    /**
     * @return mixed
     */
    public function adjustmentRequestBilletReversalEndpoint()
    {
        return $this->endpoint() . '/v1/mgm/adjustment/request-boleto-reversal';
    }

    /**
     * @return mixed
     */
    public function adjustmentCheckStatusEndpoint()
    {
        return $this->endpoint() . '/v1/mgm/adjustment/check-status';
    }

    /**
     * @return mixed
     */
    public function statementEndpoint()
    {
        return $this->endpoint() . '/v1/mgm/statement';
    }

    /**
     * @return mixed
     */
    public function paginatedStatementEndpoint()
    {
        return $this->endpoint() . '/v1/mgm/paginatedstatement';
    }
}
