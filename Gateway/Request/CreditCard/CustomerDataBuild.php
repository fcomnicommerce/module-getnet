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

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;

class CustomerDataBuild implements BuilderInterface
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\CustomerRepository
    */
    private $customerRepository;

    public function __construct(\Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
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

        /**
         * @todo get correct customer adddress
        */
        $address = $order->getBillingAddress();
        $customer = $this->customerRepository->getById($order->getCustomerId());
        $streetData = ['AAAA',"123", 'BBBB', 'CCCC'];
        $district = $complement = $number = $street = '';

        if (isset($streetData[0])) {
            $street = $streetData[0];
        }
        if (isset($streetData[1])) {
            $number = $streetData[1];
        }
        if (isset($streetData[2])) {
            $complement = $streetData[2];
        }
        if (isset($streetData[3])) {
            $district = $streetData[3];
        }

        $response = [
                'customer' => [
                    'customer_id' => $customer->getId(),
                    'first_name' => $customer->getFirstname(),
                    'last_name' => $customer->getLastname(),
                    'name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
                    'email' => $customer->getEmail(),
                    'document_type' => 'CPF',
                    'document_number' => $customer->getTaxvat(),
                    'phone_number' => $address->getTelephone(),
                    'billing_address' =>[
                        'street' => $street,
                        'number' => $number,
                        'complement' => $complement,
                        'district' => $district,
                        'city' => $address->getCity(),
                        'state' => 'SP',
                        'country' => 'Brasil',
                        'postal_code' => $address->getPostcode(),
                    ],
                ]
        ];

        return $response;
    }
}
