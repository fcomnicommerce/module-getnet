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

namespace FCamara\Getnet\Controller\Adminhtml\SellerPj;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use FCamara\Getnet\Model\Seller;
use FCamara\Getnet\Model\Seller\SellerClientPj;

class DeleteAction extends Action
{
    /**
     * @var Seller
     */
    protected $seller;

    /**
     * @var SellerClientPj
     */
    protected $sellerClient;

    /**
     * DeleteAction constructor.
     * @param Context $context
     * @param Seller $seller
     * @param SellerClientPj $sellerClient
     */
    public function __construct(Context $context, Seller $seller, SellerClientPj $sellerClient)
    {
        $this->seller = $seller;
        $this->sellerClient = $sellerClient;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $deAccredit = [];

        if (!($seller = $this->seller->load($id))) {
            $this->messageManager->addErrorMessage(__('Unable to proceed. Please, try again.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('fcamara_getnet/seller/index', array('_current' => true));
        }

        try {
            $deAccredit = $this->sellerClient->pjDeAccredit($seller->getData('subseller_id'));

            if (!isset($deAccredit['success']) || !$deAccredit['success']) {
                foreach ($deAccredit['errors'] as $error) {
                    $this->messageManager->addErrorMessage($error);
                }

                throw new \Exception(__('Error Delete Seller, Please try again!'));
            }

            $seller->delete();
            $this->messageManager->addSuccessMessage(__('Seller has been deleted !'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('fcamara_getnet/seller/index', array('_current' => true));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('fcamara_getnet/seller/index', array('_current' => true));
    }
}
