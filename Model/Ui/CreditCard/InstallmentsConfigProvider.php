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

namespace FCamara\Getnet\Model\Ui\CreditCard;

use \Magento\Checkout\Model\ConfigProviderInterface;
use FCamara\Getnet\Model\Config\CreditCardConfig;
use Magento\Checkout\Model\Session;
use \Psr\Log\LoggerInterface;

class InstallmentsConfigProvider implements ConfigProviderInterface
{
    const PAYMENT_CODE = 'getnet_credit_card';

    /**
     * @var CreditCardConfig
     */
    protected $creditCardConfig;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * InstallmentsConfigProvider constructor.
     * @param CreditCardConfig $creditCardConfig
     * @param Session $checkoutSession
     * @param LoggerInterface $logger
     */
    public function __construct(
        CreditCardConfig $creditCardConfig,
        Session $checkoutSession,
        LoggerInterface $logger
    ) {
        $this->creditCardConfig = $creditCardConfig;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
    }

    public function getConfig()
    {
        $output = [];

        try {
            $qtyInstallments = (int) $this->creditCardConfig->qtyInstallments();
            $minInstallment = (int) $this->creditCardConfig->minInstallment();
            $grandTotal = $this->checkoutSession->getQuote()->getGrandTotal();

            $installmentValue = $grandTotal / $qtyInstallments;

            if ($installmentValue < $minInstallment) {
                $qtyInstallments = ceil($grandTotal / $minInstallment);
            }

            $output['payment'][self::PAYMENT_CODE]['qty_installments'] = $qtyInstallments;

        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $output;
    }
}
