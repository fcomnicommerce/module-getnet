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

declare(strict_types=1);

namespace FCamara\Getnet\Gateway\Validator;


use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;

class GeneralResponseValidator extends AbstractValidator
{
    const SUCCESS_CODES = [
        200,
        201,
        202
    ];

    /**
     * Performs validation of result code
     *
     * @param  array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject): ResultInterface
    {
        $isValid = true;
        $messages = [];
        $errorCodes = [];

        if (
            isset($validationSubject['response']) &&
            isset($validationSubject['response']['object']) &&
            isset($validationSubject['response']['object']['status_code']) &&
            !in_array($validationSubject['response']['object']['status_code'], self::SUCCESS_CODES)
        ) {
            $isValid = false;

            foreach ($validationSubject['response']['object']['details'] as $detail) {
                $messages[] = $detail['description'];

                if ($detail['error_code'] == 'GENERIC-400') {
                    $errorCode =  explode('"', $detail['description_detail']);
                    $errorCodes[] =  $detail['error_code'] . '_' . $errorCode[1];
                } else {
                    $errorCodes[] =  $detail['error_code'];
                }
            }
        }

        return $this->createResult($isValid, $messages, $errorCodes);
    }
}
