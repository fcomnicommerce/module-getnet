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

namespace FCamara\Getnet\Gateway\Request\Billet;

use FCamara\Getnet\Model\ConfigInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use FCamara\Getnet\Model\Config\SellerConfig;
use FCamara\Getnet\Model\SellerFactory;
use FCamara\Getnet\Model\Seller\SellerClient;
use FCamara\Getnet\Model\Seller\SellerClientPj;

class DataRequest implements BuilderInterface
{
    public const STATUS_SELLER_APPROVED = [
        'Aprovado Transacionar',
        'Aprovado Transacionar e Antecipar',
        'Aprovado'
    ];

    /**
     * @var SellerFactory
     */
    protected $seller;

    /**
     * @var SellerClient
     */
    protected $clientPf;

    /**
     * @var SellerClientPj
     */
    protected $clientPj;

    /**
     * @var SellerConfig
     */
    protected $sellerConfig;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * DataRequest constructor.
     * @param ConfigInterface $config
     * @param TimezoneInterface $timezone
     * @param Session $checkoutSession
     * @param SellerConfig $sellerConfig
     * @param SellerFactory $seller
     * @param SellerClient $clientPf
     * @param SellerClientPj $clientPj
     */
    public function __construct(
        ConfigInterface $config,
        TimezoneInterface $timezone,
        Session $checkoutSession,
        SellerConfig $sellerConfig,
        SellerFactory $seller,
        SellerClient $clientPf,
        SellerClientPj $clientPj
    ) {
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
        $this->timezone = $timezone;
        $this->sellerConfig = $sellerConfig;
        $this->seller = $seller;
        $this->clientPf = $clientPf;
        $this->clientPj = $clientPj;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Date_Exception
     */
    public function build(array $buildSubject)
    {
        if (
            !isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];
        $order = $paymentDO->getOrder();

        $address = $this->checkoutSession->getQuote()->getBillingAddress();
        $customer = $this->checkoutSession->getQuote()->getCustomer();
        $streetData = $address->getStreet();
        $district = $complement = $number = $street = 'NAO INFORMADO';

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

        $time = $this->timezone->date()->getTimestamp();
        $date = new \Zend_Date($time, \Zend_Date::TIMESTAMP);

        if (null != $this->config->expirationDays()) {
            $date->addDay($this->config->expirationDays());
        } else {
            $date->addDay(1);
        }

        $expirationDate = $date->get('dd/MM/YYYY');

        $postcode = str_replace('-', '', $address->getPostcode());

        $response = [
            'seller_id' => $this->config->sellerId(),
            'amount' => (int) $order->getGrandTotalAmount() * 100,
            'currency' => 'BRL',
            'order' => [
                'order_id' => $order->getOrderIncrementId(),
                'sales_tax' => 0,
                'product_type' => 'service',
            ],
            'boleto' => [
                'our_number' => $this->config->ourNumber(),
                'expiration_date' => $expirationDate,
                'instructions' => $this->config->instructions(),
                'provider' => $this->config->billetProvider(),
            ],
            'customer' => [
                'first_name' => $customer->getFirstname(),
                'name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
                'document_type' => 'CPF',
                'document_number' => $customer->getTaxvat(),
                'billing_address' => [
                    'street' => $street,
                    'number' => $number,
                    'complement' => $complement,
                    'district' => $district,
                    'city' => $address->getCity(),
                    'state' => $address->getRegionCode(),
                    'postal_code' => $postcode,
                ],
            ],
        ];

        if ($this->sellerConfig->isEnabled()) {
            $sellers = [];
            $subSellerSalesAmount = [];

            foreach ($order->getItems() as $item) {
                if ($item->getPrice() <= 0) {
                    continue;
                }

                $product = $item->getProduct();

                if ($product->getSellerId()) {
                    $sellerData = $this->seller->create()->loadBySubSellerId($product->getSellerId());

                    if (!$this->checkSellerIsApproved($sellerData)) {
                        continue;
                    }

                    $sellers[$product->getSellerId()]['order_items'][] = [
                        'amount' => (($item->getPrice() - $item->getDiscountAmount()) * $item->getQtyOrdered()) * 100,
                        'currency' => 'BRL',
                        'id' => $product->getId(),
                        'description' => $product->getName()
                    ];
                }
            }

            $amount = 0;
            foreach ($sellers as $sellerId => $seller) {
                foreach ($seller['order_items'] as $orderItem) {
                    $amount += $orderItem['amount'];
                    $subSellerSalesAmount[$sellerId] = ['subseller_sales_amount' => $amount];
                }

                $response['marketplace_subseller_payments'][] = [
                    'subseller_sales_amount' => (int) $subSellerSalesAmount[$sellerId]['subseller_sales_amount'],
                    'subseller_id' => $sellerId,
                    'order_items' => $sellers[$sellerId]['order_items']
                ];
            }
        }

        return $response;
    }

    /**
     * @param $seller
     * @return bool
     */
    protected function checkSellerIsApproved($seller)
    {
        $result = false;

        if ($seller['type'] == 'PF') {
            $pfCallback = $this->clientPf->pfCallback($seller['merchant_id'], $seller['legal_document_number']);

            if (array_key_exists($pfCallback['status'], self::STATUS_SELLER_APPROVED)) {
                $result = true;
            }
        }

        if ($seller['type'] == 'PJ') {
            $pfCallback = $this->clientPj->pjCallback($seller['merchant_id'], $seller['legal_document_number']);

            if (array_key_exists($pfCallback['status'], self::STATUS_SELLER_APPROVED)) {
                $result = true;
            }
        }

        return $result;
    }
}
