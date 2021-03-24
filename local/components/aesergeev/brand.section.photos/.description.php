<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    'NAME' => Loc::getMessage('COMP_AES_BRAND_SECT_PHOTOS'),
    'DESCRIPTION' => Loc::getMessage('COMP_AES_BRAND_SECT_PHOTOS_DESC'),
    'PATH' => [
        'ID' => Loc::getMessage('COMP_AES_BRAND_SECT_PHOTOS_PATH_ID'),
        'NAME' => Loc::getMessage('COMP_AES_BRAND_SECT_PHOTOS_PATH_NAME'),
        'SORT' => 1100,
    ],
];