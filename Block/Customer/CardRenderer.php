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

namespace FCamara\Getnet\Block\Customer;

use FCamara\Getnet\Model\Ui\ConfigProvider;
use Magento\Framework\View\Element\Template;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Block\AbstractCardRenderer;

class CardRenderer extends AbstractCardRenderer
{
    /**
     * Can render specified token
     *
     * @param PaymentTokenInterface $token
     * @return boolean
     */
    public function canRender(PaymentTokenInterface $token)
    {
        return true;
    }

    /**
     * @return string
     */
    public function getNumberLast4Digits()
    {
        return $this->getTokenDetails()['maskedCC'];
    }

    /**
     * @return string
     */
    public function getExpDate()
    {
        return $this->getTokenDetails()['expirationDate']['date'];
    }

    /**
     * @return string
     */
    public function getIconUrl()
    {
        return 'data:image/png;base64,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';
    }

    /**
     * @return int
     */
    public function getIconHeight()
    {
        return 100;
    }

    /**
     * @return int
     */
    public function getIconWidth()
    {
        return 100;
    }
}

