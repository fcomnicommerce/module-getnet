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

namespace FCamara\Getnet\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;

class CreditCardDataAssignObserver extends AbstractDataAssignObserver
{

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $method = $this->readMethodArgument($observer);
        $data = $this->readDataArgument($observer);
        $additionalInfo = $data->getDataByKey('additional_data');
        $paymentInfo = $method->getInfoInstance();

        if (isset($additionalInfo['cc_number_token'])) {
            $paymentInfo->setAdditionalInformation(
                'cc_number_token',
                $additionalInfo['cc_number_token']
            );
        }

        if (isset($additionalInfo['cc_name'])) {
            $paymentInfo->setAdditionalInformation(
                'cc_name',
                $additionalInfo['cc_name']
            );
        }

        if (isset($additionalInfo['cc_exp_month'])) {
            $paymentInfo->setAdditionalInformation(
                'cc_exp_month',
                $additionalInfo['cc_exp_month']
            );
        }

        if (isset($additionalInfo['cc_exp_year'])) {
            $paymentInfo->setAdditionalInformation(
                'cc_exp_year',
                $additionalInfo['cc_exp_year']
            );
        }

        if (isset($additionalInfo['cc_cid'])) {
            $paymentInfo->setAdditionalInformation(
                'cc_cid',
                $additionalInfo['cc_cid']
            );
        }

        if (isset($additionalInfo['cc_type'])) {
            $paymentInfo->setAdditionalInformation(
                'cc_type',
                $additionalInfo['cc_type']
            );
        }

        if (isset($additionalInfo['cc_installment'])) {
            $paymentInfo->setAdditionalInformation(
                'cc_installment',
                $additionalInfo['cc_installment']
            );
        }
    }
}
