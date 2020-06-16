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

namespace FCamara\Getnet\Controller\Adminhtml\Seller;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use FCamara\Getnet\Model\SellerFactory;
use FCamara\Getnet\Model\Seller\SellerClient;

class Edit extends Action
{
    /**
     * @var SellerFactory
     */
    protected $seller;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var SellerClient
     */
    protected $client;

    /**
     * Edit constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param SellerFactory $seller
     * @param SellerClient $client
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        SellerFactory $seller,
        SellerClient $client
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->seller = $seller;
        $this->client = $client;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $data = $this->getRequest()->getParam('main_fieldset');
        $id = $this->getRequest()->getParam('id');

        if ($id && is_array($data)) {
            $seller = $this->seller->create()->load($id);
            $seller->addData(['merchant_id' => '1']);
            $seller->addData($data['seller_information']);
            $seller->addData(['business_address' => json_encode($data['seller_address'])]);
            $seller->addData(['mailing_address' => json_encode($data['seller_address'])]);
            $seller->addData(['bank_accounts' => json_encode($data['seller_bank_account'])]);

            if (isset($data['seller_working_hours'])) {
                $seller->addData(['working_hours' => json_encode($data['seller_working_hours'])]);
            }

            try {
                if ($data['seller_information']['type'] == 'PF') {
                    $integratedSeller = $this->client->pfUpdateSubSeller($data);
                }

                if (!isset($integratedSeller['success'])) {
                    throw new \Exception(__('Error Update Seller, Please try again!'));
                }

                $seller->save();
                $this->messageManager->addSuccessMessage('Seller Successfully Saved!');
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/index');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Error saving the Seller, please try again!'));
            }
        }

        return $this->resultPageFactory->create();
    }
}
