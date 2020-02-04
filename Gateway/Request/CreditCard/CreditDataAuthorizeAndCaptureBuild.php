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

namespace FCamara\Getnet\Gateway\Request\CreditCard;

use FCamara\Getnet\Model\Client;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

class CreditDataAuthorizeAndCaptureBuild implements BuilderInterface
{
    /**
     * @var \FCamara\Getnet\Model\Config\CreditCardConfig
     */
    private $creditCardConfig;

    public function __construct(
        \FCamara\Getnet\Model\Config\CreditCardConfig $creditCardConfig
    ) {
        $this->creditCardConfig = $creditCardConfig;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];
        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();

        $ccNumberToken = $payment->getAdditionalInformation('cc_number_token');
        $ccName = $payment->getAdditionalInformation('cc_name');
        $ccExpMonth = $payment->getAdditionalInformation('cc_exp_month');
        $ccExpYear = $payment->getAdditionalInformation('cc_exp_year');
        $ccCid = $payment->getAdditionalInformation('cc_cid');
        $ccType = $payment->getAdditionalInformation('cc_type');
        $ccType = Client::CREDIT_CARD_BRADS[$ccType];

        $installments = $payment->getAdditionalInformation('cc_installment') ? $payment->getAdditionalInformation('cc_installment') : 1;
        $transactionType = 'FULL';

        if ($installments > 1) {
            $transactionType = $this->creditCardConfig->installments();
        }

        $response = [
            'credit' => [
                'delayed' => false,
                'authenticated' => false,
                'pre_authorization' => false,
                'save_card_data' => false,
                'transaction_type' => $transactionType,
                'number_installments' => $installments,
                'card' => [
                    'number_token' => $ccNumberToken,
                    'cardholder_name' => $ccName,
                    'security_code' => $ccCid,
                    'brand' => $ccType,
                    'expiration_month' => $ccExpMonth,
                    'expiration_year' => substr($ccExpYear, -2),
                ],
            ],
        ];

        return $response;
    }
}
