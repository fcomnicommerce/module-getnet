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

use Magento\Store\Model\ScopeInterface;

class CreditCardConfig extends Config implements CreditCardConfigInterface
{
    /**
     * {@inheritDoc}
     */
    public function isActive()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ACTIVE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function title()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function provider()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PROVIDER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function paymentAction()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_ACTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function orderStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ORDER_STATUS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function sortOrder()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SORT_ORDER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function installments()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_INSTALLMENTS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function qtyInstallments()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_QTY_INSTALLMENTS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function minInstallment()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MIN_INSTALLMENTS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function verificationEndpoint()
    {
        return $this->endpoint() . '/v1/cards/verification';
    }

    /**
     * {@inheritDoc}
     */
    public function authorizeEndpoint()
    {
        return $this->endpoint() . '/v1/payments/credit';
    }

    /**
     * {@inheritDoc}
     */
    public function captureEndpoint()
    {
        return $this->endpoint() . '/v1/payments/credit/{payment_id}/confirm';
    }

    /**
     * {@inheritDoc}
     */
    public function voidEndpoint()
    {
        return $this->endpoint() . '/v1/payments/credit/{payment_id}/cancel';
    }

    /**
     * {@inheritDoc}
     */
    public function vaultEndpoint()
    {
        return $this->endpoint() . '/v1/cards';
    }

    /**
     * @return string
     */
    public function tokensCardEndpoint()
    {
        return $this->endpoint() . '/v1/tokens/card';
    }

    /**
     * @return string
     */
    public function plansEndpoint()
    {
        return $this->endpoint() . '/v1/plans';
    }

    /**
     * @return string
     */
    public function customersEndpoint()
    {
        return $this->endpoint() . '/v1/customers';
    }

    /**
     * @return string
     */
    public function subscriptionsEndpoint()
    {
        return $this->endpoint() . '/v1/subscriptions';
    }

    /**
     * @param $subscriptionId
     * @return string
     */
    public function getSubscriptionEndpoint($subscriptionId)
    {
        return $this->endpoint() . '/v1/subscriptions/' . $subscriptionId;
    }

    /**
     * @param $subscriptionId
     * @return string
     */
    public function cancelSubscriptionEndpoint($subscriptionId)
    {
        return $this->endpoint() . '/v1/subscriptions/' . $subscriptionId . '/cancel';
    }
}
