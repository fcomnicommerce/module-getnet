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
//        $order = $this->orderRepository->get($order->getId());

        $billingAddress = $order->getBillingAddress();
        $customer = $this->customerRepository->getById($order->getCustomerId());

        $customerDocument = $this->customerDocument($customer);
        $address = $this->customerAddress($order);
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
                        'street' => $address['street'],
                        'number' => $address['number'],
                        'complement' => $address['complement'],
                        'district' => $address['district'],
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

    private function customerAddress(\Magento\Payment\Gateway\Data\OrderAdapterInterface $order)
    {
        $billingAddress = $order->getBillingAddress();

        $address = [
            $billingAddress->getStreetLine1(),
            $billingAddress->getStreetLine2(),
            $billingAddress->getStreetLine3(),
            $billingAddress->getStreetLine4()
        ];

        if (!isset($address)) {
            return [
                'street' => 'NÃO INFORMADO',
                'number' => 'NÃO INFORMADO',
                'complement' => 'NÃO INFORMADO',
                'district' => 'NÃO INFORMADO'
            ];
        }

        $street = $address[$this->creditCardConfig->streetLine()];
        $number = $address[$this->creditCardConfig->numberLine()];
        $complement = $address[$this->creditCardConfig->complementLine()];
        $district = $address[$this->creditCardConfig->districtLine()];

        return [
            'street' => $street,
            'number' => $number,
            'complement' => $complement,
            'district' => $district
        ];
    }

    /**
     * @param $postcode
     * @return string
     */
    public function cleanZipcode($postcode)
    {
        $postcode = explode("-", $postcode);
        return count($postcode) > 1 ? $postcode[0] . $postcode[1] : $postcode;
    }
}
