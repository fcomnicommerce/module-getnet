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

        if ($data->getDataByKey('cc_number_token') !== null) {
            $paymentInfo->setAdditionalInformation(
                'cc_number_token',
                $data->getDataByKey('cc_number_token')
            );
        }

        if ($data->getDataByKey('cc_name') !== null) {
            $paymentInfo->setAdditionalInformation(
                'cc_name',
                $data->getDataByKey('cc_name')
            );
        }

        if ($data->getDataByKey('cc_exp_month') !== null) {
            $paymentInfo->setAdditionalInformation(
                'cc_exp_month',
                $data->getDataByKey('cc_exp_month')
            );
        }

        if ($data->getDataByKey('cc_exp_year') !== null) {
            $paymentInfo->setAdditionalInformation(
                'cc_exp_year',
                $data->getDataByKey('cc_exp_year')
            );
        }

        if ($data->getDataByKey('cc_cid') !== null) {
            $paymentInfo->setAdditionalInformation(
                'cc_cid',
                $data->getDataByKey('cc_cid')
            );
        }

        if ($data->getDataByKey('cc_type') !== null) {
            $paymentInfo->setAdditionalInformation(
                'cc_type',
                $data->getDataByKey('cc_type')
            );
        }
    }
}
