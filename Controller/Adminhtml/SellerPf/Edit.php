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

namespace FCamara\Getnet\Controller\Adminhtml\SellerPf;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use FCamara\Getnet\Model\SellerFactory;
use FCamara\Getnet\Model\Seller\SellerClient;

class Edit extends Action
{
    public const STATUS_AWAITING_DEALING_MKP = 'Aguardando Tratativa MKP';

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
            $seller->addData($data['seller_information']);
            $seller->addData(['business_address' => json_encode($data['seller_address'])]);
            $seller->addData(['mailing_address' => json_encode($data['seller_address'])]);
            $seller->addData(['bank_accounts' => json_encode($data['bank_accounts'])]);
            $seller->addData(['phone' => json_encode($data['phone'])]);
            $seller->addData(['list_commissions' => json_encode($data['list_commissions'])]);
            $seller->addData(['working_hours' => json_encode($data['working_hours'])]);
            $seller->addData(['identification_document' => json_encode($data['identification_document'])]);
            $seller->addData(['cellphone' => json_encode($data['cellphone'])]);

            try {
                if ($seller->getStatus() == 'Tratativa Cadastro') {
                    $updatedSeller = $this->client->pfUpdateComplement($seller->getData());
                } else {
                    $updatedSeller = $this->client->pfUpdateSubSeller($seller->getData());
                }

                if (!isset($updatedSeller['success'])) {
                    if (isset($updatedSeller['ModelState'])) {
                        foreach ($updatedSeller['ModelState'] as $key => $msg) {
                            $this->messageManager->addErrorMessage($key . ': ' . $msg[0]);
                        }
                    }

                    throw new \Exception(__('Error Update Seller, Please try again!'));
                }

                $seller->save();

                $this->messageManager->addSuccessMessage('Seller Successfully Saved!');

                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('fcamara_getnet/seller/index');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Error saving the Seller, please try again!'));
            }
        }

        return $this->resultPageFactory->create();
    }
}
