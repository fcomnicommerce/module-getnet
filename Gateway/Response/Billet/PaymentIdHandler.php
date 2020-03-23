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
 * @author    Danilo Cavalcanti <danilo.moura@fcamara.com.br>
 */

namespace FCamara\Getnet\Gateway\Response\Billet;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;

class PaymentIdHandler implements HandlerInterface
{
    /**
     * @param array $handlingSubject
     * @param array $response
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function handle(array $handlingSubject, array $response)
    {
        /** @var PaymentDataObjectInterface $paymentDataObject */
        $paymentDataObject = $handlingSubject['payment'];
        /** @var Payment $payment */
        $payment = $paymentDataObject->getPayment();
        if (
            isset($response["object"]) &&
            isset($response["object"]["payment_id"])
        ) {
            $payment->setAdditionalInformation("payment_id", $response["object"]["payment_id"]);
            $payment->setAdditionalInformation("billet_data", $response["object"]["boleto"]);
        }
    }
}
