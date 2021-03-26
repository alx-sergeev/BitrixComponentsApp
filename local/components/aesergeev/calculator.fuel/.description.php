<? if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    'NAME' => Loc::getMessage('COMP_AES_CALCULATOR_FUEL'),
    'DESCRIPTION' => Loc::getMessage('COMP_AES_CALCULATOR_FUEL_DESC'),
    'PATH' => [
        'ID' => Loc::getMessage('COMP_AES_CALCULATOR_FUEL_PATH_ID'),
        'NAME' => Loc::getMessage('COMP_AES_CALCULATOR_FUEL_PATH_NAME'),
        'SORT' => 1100,
    ],
];