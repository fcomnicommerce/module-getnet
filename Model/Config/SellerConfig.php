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

use Magento\Store\Model\ScopeInterface;

class SellerConfig extends Config implements SellerConfigInterface
{
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
