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

namespace FCamara\Getnet\Block\Customer\Subscriptions;

use Magento\Framework\View\Element\Template;
use FCamara\Getnet\Model\Client;
use Magento\Customer\Model\Session;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\Order\Config as OrderConfig;

class Listaction extends Template
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var CollectionFactory
     */
    private $orderCollection;

    /**
     * @var OrderConfig
     */
    private $orderConfig;

    /**
     * Listaction constructor.
     * @param Template\Context $context
     * @param Client $client
     * @param Session $customerSession
     * @param CollectionFactory $orderCollection
     * @param OrderConfig $orderConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Client $client,
        Session $customerSession,
        CollectionFactory $orderCollection,
        OrderConfig $orderConfig,
        array $data = []
    ) {
        $this->client = $client;
        $this->customerSession = $customerSession;
        $this->orderCollection = $orderCollection;
        $this->orderConfig = $orderConfig;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getSubscriptionsList()
    {
        $subscriptions = [];
        $customerId = $this->customerSession->getCustomerId();
        $orders = $this->orderCollection->create($customerId)->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'status',
            ['in' => $this->orderConfig->getVisibleOnFrontStatuses()]
        )->addFieldToFilter(
            'subscription_id',
            ['neq' => '']
        )->setOrder(
            'created_at',
            'desc'
        );

        foreach ($orders as $order) {
            $subscription = $this->client->getSubscription($order['subscription_id']);

            if (isset($subscription['status']) && $subscription['status'] != 'canceled') {
                $subscriptions[] = [
                    'subscription_id' => $subscription['subscription']['subscription_id'],
                    'name' => $subscription['plan']['name'],
                    'amount' => $subscription['plan']['amount']
                ];
            }
        }

        return $subscriptions;
    }
}
