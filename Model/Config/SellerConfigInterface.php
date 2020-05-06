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
}
