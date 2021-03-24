<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

$arComponentParameters = [
    'GROUPS' => [],
    'PARAMETERS' => [
        'SEF_FOLDER_PATH' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('PARAM_BRAND_PHOTOS_FOLDER_PATH'),
            'TYPE' => 'STRING',
            'DEFAULT' => '',
        ],
    ],
];
