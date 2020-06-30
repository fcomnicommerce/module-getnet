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
namespace FCamara\Getnet\Model\Seller;

use FCamara\Getnet\Model\ResourceModel\Seller\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;
use FCamara\Getnet\Model\Seller\SellerClient;
use FCamara\Getnet\Model\Seller\SellerClientPj;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var \FCamara\Getnet\Model\Seller\SellerClient
     */
    protected $client;

    /**
     * @var \FCamara\Getnet\Model\Seller\SellerClientPj
     */
    protected $clientPj;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param $primaryFieldName
     * @param $requestFieldName
     * @param CollectionFactory $sellerCollectionFactory
     * @param \FCamara\Getnet\Model\Seller\SellerClient $client
     * @param \FCamara\Getnet\Model\Seller\SellerClientPj $clientPj
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $sellerCollectionFactory,
        SellerClient $client,
        SellerClientPj $clientPj,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $sellerCollectionFactory->create();
        $this->client = $client;
        $this->clientPj = $clientPj;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $registrationComplement = [];

        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        $this->loadedData = array();

        foreach ($items as $seller) {
            $merchantId = $seller->getData('merchant_id');
            $legalDocumentNumber = $seller->getData('legal_document_number');
            $type = $seller->getData('type');
            $registrationComplement = $this->registrationComplement($type, $merchantId, $legalDocumentNumber);

            $seller->addData($registrationComplement);
            $seller->save($registrationComplement);


            $this->loadedData[$seller->getId()]['main_fieldset'] = [
                'seller_information' => $seller->getData(),
                'phone' => json_decode($seller->getData('phone'), true),
                'seller_address' => json_decode($seller->getData('business_address'), true),
                'bank_accounts' => json_decode($seller->getData('bank_accounts'), true),
                'list_commissions' => json_decode($seller->getData('list_commissions'), true),
            ];

            if ($seller->getData('cellphone')) {
                $this->loadedData[$seller->getId()]['main_fieldset']['cellphone'] = json_decode(
                    $seller->getData('cellphone'),
                    true
                );
            }

            if ($seller->getData('working_hours')) {
                $this->loadedData[$seller->getId()]['main_fieldset']['working_hours'] = json_decode(
                    $seller->getData('working_hours'),
                    true
                );
            }
        }

        return $this->loadedData;
    }

    /**
     * @param $type
     * @param $merchantId
     * @param $legalDocumentNumber
     * @return array
     */
    protected function registrationComplement($type, $merchantId, $legalDocumentNumber)
    {
        $data = [];
        $registrationComplement = [];

        if ($type == 'PF') {
            $registrationComplement = $this->client->pfCallback($merchantId, $legalDocumentNumber);
        }

        if ($type == 'PJ') {
            $registrationComplement = $this->clientPj->pjCallback($merchantId, $legalDocumentNumber);
        }

        if (isset($registrationComplement['subseller_id'])) {
            $data = [
                'subseller_id' => $registrationComplement['subseller_id'],
                'legal_document_number' => $registrationComplement['legal_document_number'],
                'fiscal_type' => $registrationComplement['fiscal_type'],
                'enabled' => $registrationComplement['enabled'],
                'status' => $registrationComplement['status'],
                'payment_plan' => $registrationComplement['payment_plan'],
                'capture_payments_enabled' => $registrationComplement['capture_payments_enabled'],
                'anticipation_enabled' => $registrationComplement['anticipation_enabled'],
                'accepted_contract' => $registrationComplement['accepted_contract'],
                'lock_schedule' => $registrationComplement['lock_schedule'],
                'lock_capture_payments' => $registrationComplement['lock_capture_payments']
            ];
        }

        return $data;
    }
}
