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

        $paymentInfo = $method->getInfoInstance();

        if ($data->getDataByKey('number_token') !== null) {
            $paymentInfo->setAdditionalInformation(
                'number_token',
                $data->getDataByKey('number_token')
            );
        }

        if ($data->getDataByKey('cardholder_name') !== null) {
            $paymentInfo->setAdditionalInformation(
                'cardholder_name',
                $data->getDataByKey('cardholder_name')
            );
        }

        if ($data->getDataByKey('expiration_month') !== null) {
            $paymentInfo->setAdditionalInformation(
                'expiration_month',
                $data->getDataByKey('expiration_month')
            );
        }

        if ($data->getDataByKey('expiration_year') !== null) {
            $paymentInfo->setAdditionalInformation(
                'expiration_year',
                $data->getDataByKey('expiration_year')
            );
        }
    }
}
