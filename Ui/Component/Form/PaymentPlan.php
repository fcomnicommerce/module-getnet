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

namespace FCamara\Getnet\Ui\Component\Form;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\DataObject;
use FCamara\Getnet\Model\Seller\SellerClient;

class PaymentPlan extends DataObject implements OptionSourceInterface
{
    /**
     * @var SellerClient
     */
    protected $sellerClient;

    public function __construct(
        SellerClient $sellerClient,
        array $data = []
    ) {
        $this->sellerClient = $sellerClient;

        parent::__construct($data);
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = $paymentPlans = [];
        $paymentPlans = $this->sellerClient->pfConsultPaymentPlans();

        foreach ($paymentPlans as $plan) {
            $options[] = [
                'label' => $plan['name'] . ' - Antecipação (' . $plan['anticipation'] . ')' ,
                'value' => $plan['paymentplan_id']
            ];
        }

        return $options;
    }
}
