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

namespace FCamara\Getnet\Controller\Subscriptions;

use Magento\Framework\App\Action\Action;
use FCamara\Getnet\Model\Client;
use Magento\Framework\App\Action\Context;

class Deleteaction extends Action
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Deleteaction constructor.
     * @param Context $context
     * @param Client $client
     */
    public function __construct(Context $context, Client $client)
    {
        $this->client = $client;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        try {
            $subscriptionId = $this->getRequest()->getParam('subscription_id', null);
            $cancel = $this->client->cancelSubscription($subscriptionId);

            if ($cancel['status'] != 'canceled') {
                throw new \Exception($cancel['details'][0]['description_detail']);
            }

            $this->messageManager->addSuccessMessage('Subscription successfully canceled!');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->_redirect('getnet/subscriptions/listaction');
    }
}
