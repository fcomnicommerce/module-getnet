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

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use FCamara\Getnet\Model\Config\Config;
use \Magento\Customer\Model\Session;

class DeviceDataBuild implements BuilderInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var  FCamara\Getnet\Model\Config\Config
     */
    private $configGetnet;

    /**
     * DeviceDataBuild constructor.
     * @param Session $customerSession
     * @param Config $configGetnet
     */
    public function __construct(
        Session $customerSession,
        Config $configGetnet
    ) {
        $this->customerSession = $customerSession;
        $this->configGetnet = $configGetnet;
    }

    /**
     * @param array $buildSubject
     * @return array
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

        $response = [
            'device' => [
                'ip_address' => $order->getRemoteIp(),
                'device_id' => $this->configGetnet->isEnabledFingerprint() ? $this->customerSession->getSessionId() : 'hash-device-id',
            ]
        ];

        return $response;
    }
}
