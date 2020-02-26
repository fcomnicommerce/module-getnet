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
 * @author    Danilo Cavalcanti de Moura <danilo.moura@fcamara.com.br>
 */

namespace FCamara\Getnet\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use FCamara\Getnet\Model\Client;
use Magento\Framework\Message\ManagerInterface;

class CatalogProductSaveAfter implements ObserverInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * CatalogProductSaveAfter constructor.
     * @param Client $client
     * @param ManagerInterface $messageManager
     */
    public function __construct(Client $client, ManagerInterface $messageManager)
    {
        $this->client = $client;
        $this->messageManager = $messageManager;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        try {
            $product = $observer->getData('product');
            $requestParams = [];

            if ($product->getData('is_recurrence') && is_null($product->getData('recurrence_plan_id'))) {
                $recurrenceSpecific = 0;

                if ($product->getData('recurrence_specific_cycle_in_days') == 'specific') {
                    $recurrenceSpecific = $product->getData('specific_cycle_in_days');
                }

                $requestParams = [
                    'name' => $product->getData('recurrence_name'),
                    'description' => $product->getData('recurrence_description'),
                    'amount' => (int) $product->getData('recurrence_amount'),
                    'currency' => 'BRL',
                    'payment_types' => [
                        'credit_card'
                    ],
                    'sales_tax' => (int) $product->getData('recurrence_sales_tax'),
                    'product_type' => $product->getData('recurrence_product_type'),
                    'period' => [
                        'type' => $product->getData('recurrence_period_type'),
                        'billing_cycle' => (int) $product->getData('recurrence_billing_cycle'),
                        'specific_cycle_in_days' => (int) $recurrenceSpecific
                    ]
                ];

                $responseBody = $this->client->plans($requestParams);

                if (isset($responseBody['plan_id'])) {
                    $product->addData(['plan_id' => $responseBody['plan_id']]);
                    $product->save();
                    $this->messageManager->addSuccessMessage('Recurrence successfully saved!');
                } else {
                    $this->messageManager->addErrorMessage(__('Error saving the recurrence plan, please try again!'));
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }
}
