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
 * @author    Jonatan Santos <jonatan.santos@fcamara.com.br>
 */

namespace FCamara\Getnet\Gateway\Request\CreditCard;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

class CustomerDataBuild implements BuilderInterface
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\CustomerRepository
    */
    private $customerRepository;

    /**
     * @var \Magento\Sales\Model\OrderRepository
    */
    private $orderRepository;

    /**
     * @var \FCamara\Getnet\Model\Config\CreditCardConfig
     */
    private $creditCardConfig;

    public function __construct(
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository,
        \FCamara\Getnet\Model\Config\CreditCardConfig $creditCardConfig
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->creditCardConfig = $creditCardConfig;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];
        $order = $paymentDO->getOrder();

        $billingAddress = $order->getBillingAddress();
        $customer = $this->customerRepository->getById($order->getCustomerId());

        $customerDocument = $this->customerDocument($customer);
        $address = $this->getAddressLines($billingAddress);
        $postcode = $this->cleanZipcode($billingAddress->getPostcode());
        $response = [
                'customer' => [
                    'customer_id' => $customer->getId(),
                    'first_name' => $customer->getFirstname(),
                    'last_name' => $customer->getLastname(),
                    'name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
                    'email' => $customer->getEmail(),
                    'document_type' => $customerDocument['document_type'],
                    'document_number' => $customerDocument['document_number'],
                    'phone_number' => $billingAddress->getTelephone(),
                    'billing_address' => [
                        'street' => $address[0],
                        'number' => $address[1],
                        'complement' => $address[2],
                        'district' => $address[3],
                        'city' => $billingAddress->getCity(),
                        'state' => $order->getBillingAddress()->getRegionCode(),
                        'country' => $order->getBillingAddress()->getCountryId(),
                        'postal_code' => $postcode,
                    ],
                ]
        ];

        return $response;
    }

    private function customerDocument(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        $documentType = 'CPF';
        $documentAttribute = $this->creditCardConfig->documentAttribute();
        $documentNumber = 'NÃO INFORMADO';
        $customerData = $customer->__toArray();

        if ($this->creditCardConfig->cpfSameAsCnpj()) {
            $documentNumber = preg_replace('/[^0-9]/', '', $customerData[$documentAttribute]);
            if (strlen($documentNumber) == 14) {
                $documentType = 'CNPJ';
            }
            return ['document_type' => $documentType, 'document_number' => $documentNumber];
        }

        $cpfAttribute = $this->creditCardConfig->cpfAttribute();
        $cnpjAttribute = $this->creditCardConfig->cnpjAttribute();
        $cpfNumber = preg_replace('/[^0-9]/', '', $customerData[$cpfAttribute]);
        $cnpjNumber = preg_replace('/[^0-9]/', '', $customerData[$cnpjAttribute]);

        if (strlen($cpfNumber) == 11) {
            $documentType = 'CPF';
            $documentNumber = $cnpjNumber;
        }

        if (strlen($cnpjNumber) == 14) {
            $documentType = 'CNPJ';
            $documentNumber = $cnpjNumber;
        }

        return ['document_type' => $documentType, 'document_number' => $documentNumber];
    }

    private function getAddressLines($billingAddress)
    {
        $streetPos = $this->creditCardConfig->streetLine() != null ? $this->creditCardConfig->streetLine() + 1 : 0;
        $numberPos = $this->creditCardConfig->numberLine() != null ? $this->creditCardConfig->numberLine() + 1 : 0;
        $complementPos = $this->creditCardConfig->complementLine() != null ? $this->creditCardConfig->complementLine() + 1 : 0;
        $districtPos = $this->creditCardConfig->districtLine() != null ? $this->creditCardConfig->districtLine() + 1 : 0;
        $positions = [$streetPos, $numberPos, $complementPos, $districtPos];
        $addressLines = [];
        foreach ($positions as $position) {
            $function_pos = "getStreetLine" . $position;
            $addressLines[] = $billingAddress->$function_pos();
        }
        return $addressLines;
    }
    /**
     * @param $postcode
     * @return string
     */
    public function cleanZipcode($postcode)
    {
        $postcode = explode("-", $postcode);
        return count($postcode) > 1 ? $postcode[0] . $postcode[1] : $postcode[0];
    }
}
