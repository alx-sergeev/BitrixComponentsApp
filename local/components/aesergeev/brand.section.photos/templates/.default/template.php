<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<?if($arResult['ITEMS']) {?>
    <div class="brand-photos<?if(empty($arResult['SECTIONS'])){?> row<?}?>">
        <?
        $curSectionName = '';
        foreach($arResult['ITEMS'] as $key => $arItem){?>
            <?if($arItem['SECTION_NAME'] != $curSectionName){?>
                <h2><?= $arItem['SECTION_NAME']; ?></h2>

                <div class="brand-photos__content row">
            <?}?>

                <div class="brand-photos__item col-sm-2 col-xs-3">
                    <div class="brand-photos__item-img">
                        <?if($arItem['FILE']){?>
                            <a href="<?= $arItem['FILE']; ?>" target="_blank" class="brand-photos__item-href">
                                <img src="<?= $arItem['PREVIEW_PICTURE_SRC']; ?>" alt="<?= $arItem['NAME']; ?>" />

                                <span class="brand-photos__item-overlay"></span>
                            </a>
                        <?} else {?>
                            <a href="<?= $arItem['PREVIEW_PICTURE_SRC']; ?>" title="<?= $arItem['NAME']; ?>" rel="brand-photos-sect<?= $arItem['IBLOCK_SECTION_ID']; ?>" class="brand-photos__item-href">
                                <img src="<?= $arItem['PREVIEW_PICTURE_SRC']; ?>" alt="<?= $arItem['NAME']; ?>" />
                            </a>
                        <?}?>
                    </div>

                    <div class="brand-photos__item-title">
                        <?= $arItem['NAME']; ?>
                    </div>
                </div>

                <?$curSectionName = $arItem['SECTION_NAME'];?>

            <?if( ($curSectionName != $arResult['ITEMS'][$key + 1]['SECTION_NAME']) || !$arResult['ITEMS'][$key + 1]){?>
                </div>
            <?}?>
        <?}?>

        <?if($arResult['DESCRIPTION']){?>
            <div class="brand-photos__description row">
                <div class="col-lg-12">
                    <?= $arResult['DESCRIPTION']; ?>
                </div>
            </div>
        <?}?>
    </div>
<?}?>