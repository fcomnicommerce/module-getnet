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
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory;

class Data extends AbstractHelper
{
    public const MODULE_CODE = 'FCamara_Getnet';

    protected const CREDIT_BRANDS = [
        'VISA',
        'MASTERCARD',
        'AMEX',
        'ELO CRÉDITO',
        'HIPERCARD'
    ];

    /**
     * @var CollectionFactory
     */
    protected $paymentCollectionFactory;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * Data constructor.
     * @param Context $context
     * @param CollectionFactory $paymentCollectionFactory
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        Context $context,
        CollectionFactory $paymentCollectionFactory,
        OrderFactory $orderFactory
    ) {
        $this->paymentCollectionFactory = $paymentCollectionFactory;
        $this->orderFactory = $orderFactory;

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
                        break;
                    }
                    $data[$key] = $value;
                    break;
                case 'birth_date':
                    if (!$value) {
                        break;
                    }

                    $date = str_replace("/", "-", $value);
                    $data[$key] = date("Y-m-d", strtotime($date));
                    break;
                case 'working_hours':
                    if (!$value) {
                        break;
                    }
                    $data[$key] = json_decode($value, true);
                    break;
                case 'business_address':
                    if (!$value) {
                        break;
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
                        break;
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
                        break;
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
                        break;
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
                        break;
                    }
                    $listCommissions = [];

                    $comissionsToArray = json_decode($value, true);
                    $sigleComission = $comissionsToArray['SINGLECOMISSION'];
                    unset($comissionsToArray['SINGLECOMISSION']);

                    foreach ($comissionsToArray as $keyCommission => $commission) {
                        if (!empty($sigleComission) && in_array($keyCommission, self::CREDIT_BRANDS)) {
                            $listCommissions[] = [
                                'brand' => $keyCommission,
                                'product' => $sigleComission['product'],
                                'commission_percentage' => $sigleComission['commission_percentage'],
                                'commission_value' => $sigleComission['commission_value'] ? $sigleComission['commission_value'] : 0,
                                'payment_plan' => $sigleComission['payment_plan']
                            ];

                            continue;
                        }

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
                            'commission_value' => $commission['commission_value'] ? $commission['commission_value'] : 0,
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

    /**
     * @param array $sellerData
     * @return array
     */
    public function pfUpdateSubSellerArray($sellerData = [])
    {
        $data = [];

        foreach ($sellerData as $key => $value) {
            switch ($key) {
                case 'merchant_id':
                case 'legal_document_number':
                case 'legal_name':
                case 'mothers_name':
                case 'occupation':
                case 'monthly_gross_income':
                case 'subseller_id':
                case 'block_payments':
                case 'email':
                case 'acquirer_merchant_category_code':
                case 'liability_chargeback':
                case 'marketplace_store':
                case 'payment_plan':
                    if ($value) {
                        $data[$key] = $value;
                    }
                    break;
                case 'birth_date':
                    $date = str_replace("/", "-", $value);
                    $data[$key] = date("Y-m-d", strtotime($date));

                    break;
                case 'business_address':
                    $businessAddress = json_decode($value, true);
                    $data[$key] = [
                        'mailing_address_equals' => 'S',
                        'street' => $businessAddress['street'],
                        'number' => $businessAddress['number'],
                        'district' => $businessAddress['district'],
                        'city' => $businessAddress['city'],
                        'state' => $businessAddress['state'],
                        'postal_code' => $businessAddress['postal_code']
                    ];
                    break;
                case 'mailing_address':
                    $mailingAddress = json_decode($value, true);
                    $data[$key] = [
                        'street' => $mailingAddress['street'],
                        'number' => $mailingAddress['number'],
                        'district' => $mailingAddress['district'],
                        'city' => $mailingAddress['city'],
                        'state' => $mailingAddress['state'],
                        'postal_code' => $mailingAddress['postal_code']
                    ];
                    break;
                case 'working_hours':
                    $workingHours = json_decode($value, true);
                    if (
                        $workingHours['start_day']
                        && $workingHours['end_day']
                        && $workingHours['start_time']
                        && $workingHours['end_time']
                    ) {
                        $data[$key] = $workingHours;
                    }
                    break;
                case 'phone':
                    $data[$key] = json_decode($value, true);
                    break;
                case 'cellphone':
                    $cellphone = json_decode($value, true);
                    if ($cellphone['area_code'] && $cellphone['phone_number']) {
                        $data[$key] = $cellphone;
                    }
                    break;
                case 'bank_accounts':
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
                    $listCommissions = [];

                    $comissionsToArray = json_decode($value, true);
                    $sigleComission = $comissionsToArray['SINGLECOMISSION'];
                    unset($comissionsToArray['SINGLECOMISSION']);

                    foreach ($comissionsToArray as $keyCommission => $commission) {
                        if (!empty($sigleComission) && in_array($keyCommission, self::CREDIT_BRANDS)) {
                            $listCommissions[] = [
                                'brand' => $keyCommission,
                                'product' => $sigleComission['product'],
                                'commission_percentage' => $sigleComission['commission_percentage'],
                                'commission_value' => $sigleComission['commission_value'] ? $sigleComission['commission_value'] : 0,
                                'payment_plan' => $sigleComission['payment_plan']
                            ];

                            continue;
                        }

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
                            'commission_value' => $commission['commission_value'] ? $commission['commission_value'] : 0,
                            'payment_plan' => $commission['payment_plan']
                        ];
                    }

                    $data[$key] = $listCommissions;
                    break;
            }
        }

        return $data;
    }

    /**
     * @param array $sellerData
     * @return array
     */
    public function createSellerPfArray($sellerData = [])
    {
        $data = [];

        foreach ($sellerData as $key => $value) {
            switch ($key) {
                case 'merchant_id':
                case 'legal_document_number':
                case 'legal_name':
                case 'mothers_name':
                case 'occupation':
                case 'monthly_gross_income':
                case 'subseller_id':
                case 'block_payments':
                case 'email':
                case 'acquirer_merchant_category_code':
                case 'liability_chargeback':
                case 'marketplace_store':
                case 'payment_plan':
                case 'accepted_contract':
                    if ($value) {
                        $data[$key] = $value;
                    }
                    break;
                case 'birth_date':
                    $date = str_replace("/", "-", $value);
                    $data[$key] = date("Y-m-d", strtotime($date));
                    break;
                case 'business_address':
                    $businessAddress = json_decode($value, true);
                    $data[$key] = [
                        'mailing_address_equals' => 'S',
                        'street' => $businessAddress['street'],
                        'number' => $businessAddress['number'],
                        'district' => $businessAddress['district'],
                        'city' => $businessAddress['city'],
                        'state' => $businessAddress['state'],
                        'postal_code' => $businessAddress['postal_code']
                    ];
                    break;
                case 'mailing_address':
                    $mailingAddress = json_decode($value, true);
                    $data[$key] = [
                        'street' => $mailingAddress['street'],
                        'number' => $mailingAddress['number'],
                        'district' => $mailingAddress['district'],
                        'city' => $mailingAddress['city'],
                        'state' => $mailingAddress['state'],
                        'postal_code' => $mailingAddress['postal_code']
                    ];
                    break;
                case 'working_hours':
                    $workingHours = json_decode($value, true);
                    if (
                        $workingHours['start_day']
                        && $workingHours['end_day']
                        && $workingHours['start_time']
                        && $workingHours['end_time']
                    ) {
                        $data[$key] = $workingHours;
                    }
                    break;
                case 'phone':
                    $data[$key] = json_decode($value, true);
                    break;
                case 'cellphone':
                    $cellphone = json_decode($value, true);
                    if ($cellphone['area_code'] && $cellphone['phone_number']) {
                        $data[$key] = $cellphone;
                    }
                    break;
                case 'bank_accounts':
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
                    $listCommissions = [];

                    $comissionsToArray = json_decode($value, true);
                    $sigleComission = $comissionsToArray['SINGLECOMISSION'];
                    unset($comissionsToArray['SINGLECOMISSION']);

                    foreach ($comissionsToArray as $keyCommission => $commission) {
                        if (!empty($sigleComission) && in_array($keyCommission, self::CREDIT_BRANDS)) {
                            $listCommissions[] = [
                                'brand' => $keyCommission,
                                'product' => $sigleComission['product'],
                                'commission_percentage' => $sigleComission['commission_percentage'],
                                'commission_value' => $sigleComission['commission_value'] ? $sigleComission['commission_value'] : 0,
                                'payment_plan' => $sigleComission['payment_plan']
                            ];

                            continue;
                        }

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
                            'commission_value' => $commission['commission_value'] ? $commission['commission_value'] : 0,
                            'payment_plan' => $commission['payment_plan']
                        ];
                    }

                    $data[$key] = $listCommissions;
                    break;
            }
        }

        return $data;
    }

    /**
     * @param array $sellerData
     * @return array
     */
    public function createSellerPjArray($sellerData = [])
    {
        $data = [];

        foreach ($sellerData as $key => $value) {
            switch ($key) {
                case 'merchant_id':
                case 'legal_document_number':
                case 'legal_name':
                case 'trade_name':
                case 'state_fiscal_document_number':
                case 'email':
                case 'accepted_contract':
                case 'liability_chargeback':
                case 'marketplace_store':
                case 'payment_plan':
                case 'business_entity_type':
                case 'economic_activity_classification_code':
                case 'monthly_gross_income':
                case 'federal_registration_status':
                    if ($value) {
                        $data[$key] = $value;
                    }
                    break;
                case 'founding_date':
                    if ($value) {
                        $data[$key] = date_format(date_create($value), 'Y-m-d');
                    }
                    break;
                case 'business_address':
                    $businessAddress = json_decode($value, true);
                    $data[$key] = [
                        'mailing_address_equals' => 'S',
                        'street' => $businessAddress['street'],
                        'number' => $businessAddress['number'],
                        'district' => $businessAddress['district'],
                        'city' => $businessAddress['city'],
                        'state' => $businessAddress['state'],
                        'postal_code' => $businessAddress['postal_code']
                    ];
                    break;
                case 'phone':
                    $data[$key] = json_decode($value, true);
                    break;
                case 'cellphone':
                    $cellphone = json_decode($value, true);
                    if ($cellphone['area_code'] && $cellphone['phone_number']) {
                        $data[$key] = $cellphone;
                    }
                    break;
                case 'bank_accounts':
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
                    $listCommissions = [];

                    $comissionsToArray = json_decode($value, true);
                    $sigleComission = $comissionsToArray['SINGLECOMISSION'];
                    unset($comissionsToArray['SINGLECOMISSION']);

                    foreach ($comissionsToArray as $keyCommission => $commission) {
                        if (!empty($sigleComission) && in_array($keyCommission, self::CREDIT_BRANDS)) {
                            $listCommissions[] = [
                                'brand' => $keyCommission,
                                'product' => $sigleComission['product'],
                                'commission_percentage' => $sigleComission['commission_percentage'],
                                'commission_value' => $sigleComission['commission_value'] ? $sigleComission['commission_value'] : 0,
                                'payment_plan' => $sigleComission['payment_plan']
                            ];

                            continue;
                        }

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
                            'commission_value' => $commission['commission_value'] ? $commission['commission_value'] : 0,
                            'payment_plan' => $commission['payment_plan']
                        ];
                    }

                    $data[$key] = $listCommissions;
                    break;
                case 'legal_representative':
                    $legalRepresentative = json_decode($value, true);
                    if (
                        $legalRepresentative['name']
                        && $legalRepresentative['birth_date']
                        && $legalRepresentative['legal_document_number']
                    ) {
                        $data[$key] = [
                            'name' => $legalRepresentative['name'],
                            'birth_date' => date_format(date_create($legalRepresentative['birth_date']), 'Y-m-d'),
                            'legal_document_number' => $legalRepresentative['legal_document_number']
                        ];
                    }
            }
        }

        return $data;
    }

    /**
     * @param array $sellerData
     * @return array
     */
    public function pjUpdateSubSellerArray($sellerData = [])
    {
        $data = [];

        foreach ($sellerData as $key => $value) {
            switch ($key) {
                case 'merchant_id':
                case 'subseller_id':
                case 'legal_document_number':
                case 'legal_name':
                case 'trade_name':
                case 'state_fiscal_document_number':
                case 'email':
                case 'accepted_contract':
                case 'liability_chargeback':
                case 'marketplace_store':
                case 'payment_plan':
                case 'business_entity_type':
                case 'economic_activity_classification_code':
                case 'monthly_gross_income':
                case 'federal_registration_status':
                    if ($value) {
                        $data[$key] = $value;
                    }
                    break;
                case 'founding_date':
                    if ($value) {
                        $data[$key] = date_format(date_create($value), 'Y-m-d');
                    }
                    break;
                case 'business_address':
                    $businessAddress = json_decode($value, true);
                    $data[$key] = [
                        'street' => $businessAddress['street'],
                        'number' => $businessAddress['number'],
                        'district' => $businessAddress['district'],
                        'city' => $businessAddress['city'],
                        'state' => $businessAddress['state'],
                        'postal_code' => $businessAddress['postal_code']
                    ];
                    break;
                case 'bank_accounts':
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
                    $listCommissions = [];

                    $comissionsToArray = json_decode($value, true);
                    $sigleComission = $comissionsToArray['SINGLECOMISSION'];
                    unset($comissionsToArray['SINGLECOMISSION']);

                    foreach ($comissionsToArray as $keyCommission => $commission) {
                        if (!empty($sigleComission) && in_array($keyCommission, self::CREDIT_BRANDS)) {
                            $listCommissions[] = [
                                'brand' => $keyCommission,
                                'product' => $sigleComission['product'],
                                'commission_percentage' => $sigleComission['commission_percentage'],
                                'commission_value' => $sigleComission['commission_value'] ? $sigleComission['commission_value'] : 0,
                                'payment_plan' => $sigleComission['payment_plan']
                            ];

                            continue;
                        }

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
                            'commission_value' => $commission['commission_value'] ? $commission['commission_value'] : 0,
                            'payment_plan' => $commission['payment_plan']
                        ];
                    }

                    $data[$key] = $listCommissions;
                    break;
                case 'legal_representative':
                    $legalRepresentative = json_decode($value, true);
                    if (
                        $legalRepresentative['name']
                        && $legalRepresentative['birth_date']
                        && $legalRepresentative['legal_document_number']
                    ) {
                        $data[$key] = [
                            'name' => $legalRepresentative['name'],
                            'birth_date' => date_format(date_create($legalRepresentative['birth_date']), 'Y-m-d'),
                            'legal_document_number' => $legalRepresentative['legal_document_number']
                        ];
                    }
            }
        }

        return $data;
    }

    /**
     * @param array $sellerData
     * @return array
     */
    public function pjUpdateComplementArray($sellerData = [])
    {
        $data = [];

        foreach ($sellerData as $key => $value) {
            switch ($key) {
                case 'merchant_id':
                case 'subseller_id':
                case 'legal_document_number':
                case 'legal_name':
                case 'trade_name':
                case 'state_fiscal_document_number':
                case 'email':
                case 'accepted_contract':
                case 'liability_chargeback':
                case 'marketplace_store':
                case 'payment_plan':
                case 'business_entity_type':
                case 'economic_activity_classification_code':
                case 'monthly_gross_income':
                case 'federal_registration_status':
                    if ($value) {
                        $data[$key] = $value;
                    }
                    break;
                case 'founding_date':
                    if ($value) {
                        $data['date'] = date_format(date_create($value), 'Y-m-d');
                    }
                    break;
                case 'business_address':
                    if (!$value) {
                        break;
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
                        break;
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
                case 'bank_accounts':
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
                    $listCommissions = [];

                    $comissionsToArray = json_decode($value, true);
                    $sigleComission = $comissionsToArray['SINGLECOMISSION'];
                    unset($comissionsToArray['SINGLECOMISSION']);

                    foreach ($comissionsToArray as $keyCommission => $commission) {
                        if (!empty($sigleComission) && in_array($keyCommission, self::CREDIT_BRANDS)) {
                            $listCommissions[] = [
                                'brand' => $keyCommission,
                                'product' => $sigleComission['product'],
                                'commission_percentage' => $sigleComission['commission_percentage'],
                                'commission_value' => $sigleComission['commission_value'] ? $sigleComission['commission_value'] : 0,
                                'payment_plan' => $sigleComission['payment_plan']
                            ];

                            continue;
                        }

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
                            'commission_value' => $commission['commission_value'] ? $commission['commission_value'] : 0,
                            'payment_plan' => $commission['payment_plan']
                        ];
                    }

                    $data[$key] = $listCommissions;
                    break;
                case 'legal_representative':
                    $legalRepresentative = json_decode($value, true);
                    if (
                        $legalRepresentative['name']
                        && $legalRepresentative['birth_date']
                        && $legalRepresentative['legal_document_number']
                    ) {
                        $data[$key] = [
                            'name' => $legalRepresentative['name'],
                            'birth_date' => date_format(date_create($legalRepresentative['birth_date']), 'Y-m-d'),
                            'legal_document_number' => $legalRepresentative['legal_document_number']
                        ];
                    }
            }
        }

        return $data;
    }

    /**
     * @param $paymentId
     * @return mixed
     */
    public function getOrderByPaymentId($paymentId)
    {
        $order = [];

        $collection = $this->paymentCollectionFactory->create()
            ->addAttributeToSelect('parent_id')
            ->addAttributeToFilter(
                'additional_information',
                ['like' => '%"payment_id":"' . $paymentId . '"%']
            );

        $orderId = $collection->getFirstItem()->getParentId();

        if ($orderId) {
            $order = $this->orderFactory->create()->load($orderId);
        }

        return $order;
    }
}
