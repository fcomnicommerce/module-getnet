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

namespace FCamara\Getnet\Model\Ui;

use \Magento\Checkout\Model\ConfigProviderInterface;
use FCamara\Getnet\Model\Client;
use \Magento\Customer\Model\Session;
use \Psr\Log\LoggerInterface;

class SavedCardConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SavedCardConfigProvider constructor.
     * @param Client $client
     * @param Session $customerSession
     * @param LoggerInterface $logger
     */
    public function __construct(
        Client $client,
        Session $customerSession,
        LoggerInterface $logger
    ) {
        $this->client = $client;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
    }

    /**
     * @return array|mixed
     */
    public function getConfig()
    {
        $output = [];

        try {
            $cards = $this->client->cardList($this->customerSession->getCustomerId());

            foreach ($cards['cards'] as $card) {
                $output['payment']['saved_cards'][] = [
                    'card_id' => $card['card_id'],
                    'last_four_digits' => $card['last_four_digits'],
                    'expiration_month' => $card['expiration_month'],
                    'expiration_year' => $card['expiration_year'],
                    'brand' => $card['brand'],
                    'cardholder_name' => $card['cardholder_name'],
                    'customer_id' => $card['customer_id'],
                    'number_token' => $card['number_token'],
                    'used_at' => $card['used_at'],
                    'created_at' => $card['created_at'],
                    'updated_at' => $card['updated_at'],
                    'status' => $card['status'],
                    'card_data' => '**** **** **** ' . $card['last_four_digits'] . ' - ' . $card['brand']
                ];
            }
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $output;
    }
}
