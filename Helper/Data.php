<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to https://www.fcamara.com.br/ for more information.
 *
 * @category  FCamara
 * @package   FCamara_
 * @copyright Copyright (c) 2020 FCamara Formação e Consultoria
 * @Agency    FCamara Formação e Consultoria, Inc. (http://www.fcamara.com.br)
 * @author    Danilo Cavalcanti de Moura <danilo.moura@fcamara.com.br>
 */

namespace FCamara\Getnet\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    public const MODULE_CODE = 'FCamara_Getnet';

    /**
     * Data constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * @param array $sellerData
     * @return array
     */
    public function pfUpdateComplementArray($sellerData = [])
    {
        $data = [];

        foreach ($sellerData as $key => $value) {
            switch ($key) {
                case 'merchant_id':
                case 'subseller_id':
                case 'legal_document_number':
                case 'legal_name':
                case 'email':
                case 'payment_plan':
                case 'marketplace_store':
                case 'sex':
                case 'marital_status':
                case 'nationality':
                case 'mothers_name':
                case 'fathers_name':
                case 'spouse_name':
                case 'birth_place':
                case 'birth_city':
                case 'birth_state':
                case 'birth_country':
                case 'occupation':
                case 'monthly_income':
                case 'ppe_indication':
                case 'ppe_description':
                case 'patrimony':
                    if (!$value) {
                        continue;
                    }
                    $data[$key] = $value;
                    break;
                case 'birth_date':
                    if (!$value) {
                        continue;
                    }
                    $data['date'] = date_format(date_create($value), 'Y-m-d');
                    break;
                case 'working_hours':
                    if (!$value) {
                        continue;
                    }
                    $data[$key] = json_decode($value, true);
                    break;
                case 'business_address':
                    if (!$value) {
                        continue;
                    }
                    $businessAddress = json_decode($value, true);
                    $data['adresses'][] = [
                        'address_type' => 'business',
                        'street' => $businessAddress['street'],
                        'number' => $businessAddress['number'],
                        'district' => $businessAddress['district'],
                        'city' => $businessAddress['city'],
                        'state' => $businessAddress['state'],
                        'postal_code' => $businessAddress['postal_code']
                    ];
                    break;
                case 'mailing_address':
                    if (!$value) {
                        continue;
                    }
                    $mailingAddress = json_decode($value, true);
                    $data['adresses'][] = [
                        'address_type' => 'mailing',
                        'street' => $mailingAddress['street'],
                        'number' => $mailingAddress['number'],
                        'district' => $mailingAddress['district'],
                        'city' => $mailingAddress['city'],
                        'state' => $mailingAddress['state'],
                        'postal_code' => $mailingAddress['postal_code']
                    ];
                    break;
                case 'identification_document':
                    $identificationDocument = json_decode($value, true);
                    if (!$value || !$identificationDocument['document_type']) {
                        continue;
                    }
                    $data[$key] = [
                        'document_type' => $identificationDocument['document_type'],
                        'document_number' => $identificationDocument['document_number'],
                        'document_issue_date' => date_format(
                            date_create($identificationDocument['document_issue_date']),
                            'Y-m-d'
                        ),
                        'document_expiration_date' => date_format(
                            date_create($identificationDocument['document_expiration_date']),
                            'Y-m-d'
                        ),
                        'document_issuer' => $identificationDocument['document_issuer'],
                        'document_issuer_state' => $identificationDocument['document_issuer_state']
                    ];
                    break;
                case 'bank_accounts':
                    if (!$value) {
                        continue;
                    }
                    $bankAccounts = json_decode($value, true);
                    $data[$key] = [
                        'type_accounts' => 'unique',
                        'unique_account' => [
                            'bank' => $bankAccounts['bank'],
                            'agency' => $bankAccounts['agency'],
                            'account' => $bankAccounts['account'],
                            'account_type' => $bankAccounts['account_type'],
                            'account_digit' => $bankAccounts['account_digit']
                        ]
                    ];
                    break;
                case 'list_commissions':
                    if (!$value) {
                        continue;
                    }
                    $listCommissions = [];

                    foreach (json_decode($value, true) as $keyCommission => $commission) {
                        if (
                            !$commission['product']
                            || !$commission['commission_percentage']
                            || !$commission['payment_plan']
                        ) {
                            continue;
                        }

                        $listCommissions[] = [
                            'brand' => $keyCommission,
                            'product' => $commission['product'],
                            'commission_percentage' => $commission['commission_percentage'],
                            'payment_plan' => $commission['payment_plan']
                        ];
                    }

                    if ($listCommissions) {
                        $data[$key] = $listCommissions;
                    }
                    break;
                default:
                    break;
            }
        }

        return $data;
    }
}
