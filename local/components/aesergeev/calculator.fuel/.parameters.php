<? if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

Loader::includeModule('form');


$arFormList = [];
$isFiltered = true;
$rsForms = CForm::GetList($by = 's_sort', $order = 'asc', ['SITE_ID' => SITE_ID], $isFiltered);
while($arForm = $rsForms->Fetch()) {
    $arFormList[ $arForm['SID'] ] = '[' . $arForm['ID'] . '] ' . $arForm['NAME'];
}

$arComponentParameters = [
    'GROUPS' => [
        'SERVICES_SETTINGS' => [
            'NAME' => Loc::getMessage('PARAM_AES_CALC_GROUP_SERVICES_SETTINGS_NAME'),
            'SORT' => 100,
        ],
        'CALCULATOR_PARAMS' => [
            'NAME' => Loc::getMessage('PARAM_AES_CALC_GROUP_CALCULATOR_PARAMS_NAME'),
            'SORT' => 200,
        ],
    ],
    'PARAMETERS' => [
        'WEBFORM_CODE' => [
            'PARENT' => 'SERVICES_SETTINGS',
            'NAME' => Loc::getMessage('PARAM_AES_CALC_PARAM_WEBFORM_CODE_NAME'),
            'TYPE' => 'LIST',
            'VALUES' => $arFormList,
        ],
        'PRICE_IN_MKAD' => [
            'PARENT' => 'CALCULATOR_PARAMS',
            'NAME' => Loc::getMessage('PARAM_AES_CALC_PARAM_PRICE_IN_MKAD_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => '500',
        ],
        'PRICE_OUT_MKAD' => [
            'PARENT' => 'CALCULATOR_PARAMS',
            'NAME' => Loc::getMessage('PARAM_AES_CALC_PARAM_PRICE_OUT_MKAD_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => '1000',
        ],
        'PRICES_PRODUCT_ONE' => [
            'PARENT' => 'CALCULATOR_PARAMS',
            'NAME' => Loc::getMessage('PARAM_AES_CALC_PARAM_PRICES_PRODUCT_ONE_NAME'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'Y',
            'DEFAULT' => [30, 29, 28, 28],
        ],
        'PRICES_PRODUCT_TWO' => [
            'PARENT' => 'CALCULATOR_PARAMS',
            'NAME' => Loc::getMessage('PARAM_AES_CALC_PARAM_PRICES_PRODUCT_TWO_NAME'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'Y',
            'DEFAULT' => [30, 29, 28, 28],
        ],
        'PRICES_PRODUCT_THREE' => [
            'PARENT' => 'CALCULATOR_PARAMS',
            'NAME' => Loc::getMessage('PARAM_AES_CALC_PARAM_PRICES_PRODUCT_THREE_NAME'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'Y',
            'DEFAULT' => [30, 29, 28, 28],
        ],
    ],
];