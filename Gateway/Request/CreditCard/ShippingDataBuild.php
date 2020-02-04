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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

class ShippingDataBuild implements BuilderInterface
{
    /** @var \Magento\Customer\Model\ResourceModel\CustomerRepository */
    private $customerRepository;

    /** @var \FCamara\Getnet\Model\Config\CreditCardConfig */
    private $creditCardConfig;

    public function __construct(
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository,
        \FCamara\Getnet\Model\Config\CreditCardConfig $creditCardConfig
    ) {
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

        $shipping = $order->getShippingAddress();
        $customer = $this->customerRepository->getById($order->getCustomerId());
        $address = $this->getAddressLines($shipping);
        $street = $address[0];
        $number = $address[1];
        $complement = $address[2];
        $district = $address[3];

        $postcode =  $this->cleanZipcode($shipping->getPostcode());

        $response['shippings'][] = [
            'first_name' => $shipping->getFirstname(),
            'name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
            'email' => $customer->getEmail(),
            'phone_number' =>  $shipping->getTelephone(),
            'shipping_amount' => (int) $paymentDO->getPayment()->getOrder()->getShippingAmount() * 100,
            'address' =>[
                'street' => $street,
                'number' => $number,
                'complement' => $complement,
                'district' => $district,
                'city' => $shipping->getCity(),
                'state' => $order->getShippingAddress()->getRegionCode(),
                'country' => 'Brasil',
                'postal_code' => $postcode,
            ],
        ];

        return $response;
    }
    /**
     * @param $postcode
     * @return string
     */
    private function cleanZipcode($postcode)
    {
        $postcode = explode("-", $postcode);
        return count($postcode) > 1 ? $postcode[0] . $postcode[1] : $postcode;
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
}
