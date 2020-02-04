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

namespace FCamara\Getnet\Gateway\Http\Client\CreditCard;

class SettleClient extends AbstractClient
{
    /**
     * Process http request
     * @param array $data
     * @return mixed
     */
    protected function process(array $data)
    {
        $adapter = $this->adapterFactory->create();

        return $adapter->capture($data);
    }
}
