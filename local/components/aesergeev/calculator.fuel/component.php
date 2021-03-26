<? if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Loader;

Loader::includeModule('form');


/**
 * Значения по умолчанию.
 */
$arResult = [];
if(!$arParams['PRICE_IN_MKAD']) {
    $arParams['PRICE_IN_MKAD'] = 500;
}

if(!$arParams['PRICE_OUT_MKAD']) {
    $arParams['PRICE_OUT_MKAD'] = 1000;
}

if(!$arParams['WEBFORM_CODE']) {
    $arParams['WEBFORM_CODE'] = 'fuel_calc';
}

if(!empty($arParams['PRICES_PRODUCT_ONE'])) {
    $arParams['PRICES_PRODUCT_ONE'] = array_diff($arParams['PRICES_PRODUCT_ONE'], ['']);

    if(count($arParams['PRICES_PRODUCT_ONE']) != 4) {
        $arParams['PRICES_PRODUCT_ONE'] = ['30', '29', '28', '28'];
    }
}

if(!empty($arParams['PRICES_PRODUCT_TWO'])) {
    $arParams['PRICES_PRODUCT_TWO'] = array_diff($arParams['PRICES_PRODUCT_TWO'], ['']);

    if(count($arParams['PRICES_PRODUCT_TWO']) != 4) {
        $arParams['PRICES_PRODUCT_TWO'] = ['30', '29', '28', '28'];
    }
}

if(!empty($arParams['PRICES_PRODUCT_THREE'])) {
    $arParams['PRICES_PRODUCT_THREE'] = array_diff($arParams['PRICES_PRODUCT_THREE'], ['']);

    if(count($arParams['PRICES_PRODUCT_THREE']) != 4) {
        $arParams['PRICES_PRODUCT_THREE'] = ['30', '29', '28', '28'];
    }
}

/**
 * Заполнение результирующего массива
 */
$webformId = CForm::GetBySID($arParams['WEBFORM_CODE'])->Fetch()['ID'];

$arResult = [
    'WEBFORM_ID' => $webformId,
    'PRICE_IN_MKAD' => intval($arParams['PRICE_IN_MKAD']),
    'PRICE_OUT_MKAD' => intval($arParams['PRICE_OUT_MKAD']),
    'PRICES_PRODUCT_ONE' => $arParams['PRICES_PRODUCT_ONE'],
    'PRICES_PRODUCT_TWO' => $arParams['PRICES_PRODUCT_TWO'],
    'PRICES_PRODUCT_THREE' => $arParams['PRICES_PRODUCT_THREE'],
];

$this->IncludeComponentTemplate();