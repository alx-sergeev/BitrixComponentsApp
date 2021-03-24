<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if($arResult['ITEMS']) {
    foreach($arResult['ITEMS'] as $key => $arItem) {
        if(!$arItem['PREVIEW_PICTURE']) continue;

        $resizeImage = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], ['width' => 400, 'height' => 400], BX_RESIZE_IMAGE_EXACT, false);

        $arResult['ITEMS'][ $key ]['PREVIEW_PICTURE_SRC'] = $resizeImage['src'];
    }
}