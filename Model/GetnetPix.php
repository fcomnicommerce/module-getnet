<?php

namespace FCamara\Getnet\Model;

use Magento\Payment\Model\Method\AbstractMethod;

class GetnetPix extends AbstractMethod
{
    const CODE = 'getnet_pix';

    protected $_code = self::CODE;

}
