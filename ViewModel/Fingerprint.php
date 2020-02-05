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
 * @copyright Copyright (c) Getnet
 * @Agency    FCamara Formação e Consultoria, Inc. (http://www.fcamara.com.br)
 * @author    Danilo Cavalcanti de Moura <danilo.moura@fcamara.com.br>
 */

namespace FCamara\Getnet\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use FCamara\Getnet\Model\Config\Config;

class Fingerprint implements ArgumentInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \FCamara\Getnet\Model\Config
     */
    private $configGetnet;

    /**
     * Fingerprint constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Config $configGetnet
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        Config $configGetnet
    ) {
        $this->customerSession = $customerSession;
        $this->configGetnet = $configGetnet;
    }

    /**
     * @return mixed
     */
    public function getFingerprinEndpoint()
    {
        $endpoint = sprintf(
            '%s?org_id=%s&session_id=%s',
            $this->configGetnet->fingerprintEndpoint(),
            $this->configGetnet->fingerprintOrgId(),
            $this->customerSession->getSessionId()
        );

        return str_replace(' ', '', $endpoint);
    }

}