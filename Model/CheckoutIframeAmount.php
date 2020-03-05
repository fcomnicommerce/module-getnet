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

namespace FCamara\Getnet\Model;

use FCamara\Getnet\Api\AmountInterface;
use Magento\Checkout\Model\Session;

class CheckoutIframeAmount implements AmountInterface
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * CheckoutIframeAmount constructor.
     * @param Session $checkoutSession
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(Session $checkoutSession)
    {
        $this->quote = $checkoutSession->getQuote();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getAmountByQuote($id)
    {
        if ($this->quote->getId() == $id) {
            return number_format($this->quote->getData('grand_total'), 2, '.', '');
        }
    }
}
