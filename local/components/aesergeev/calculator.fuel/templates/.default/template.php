<? if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

$this->addExternalCss(SITE_TEMPLATE_PATH . '/css/jquery-ui.css');
$this->addExternalJs(SITE_TEMPLATE_PATH . '/js/jquery-ui.js');
?>

<script>
    BX.ready(function() {
       BX.message({
          arParams:  <?= json_encode($arResult); ?>
       });
    });
</script>

<div class="calculator" id="fuelCalc">
    <div class="calculator__wrapper">
        <div class="calculator__item">
            <h3 class="calculator__item-caption"><?= Loc::getMessage('COMP_AES_CALC_FUEL_SELECT_PRODUCT'); ?></h3>
            <div class="calculator__ch">
                <div class="bx_filter">
                    <input type="checkbox" name="productCalc" id="calculator__product1" value="<?= Loc::getMessage('COMP_AES_CALC_FUEL_PRODUCT_ONE'); ?>">
                    <label for="calculator__product1"><?= Loc::getMessage('COMP_AES_CALC_FUEL_PRODUCT_ONE'); ?></label>
                </div>
                <div class="bx_filter">
                    <input type="checkbox" name="productCalc" id="calculator__product2" value="<?= Loc::getMessage('COMP_AES_CALC_FUEL_PRODUCT_TWO'); ?>">
                    <label for="calculator__product2"><?= Loc::getMessage('COMP_AES_CALC_FUEL_PRODUCT_TWO'); ?></label>
                </div>
                <div class="bx_filter">
                    <input type="checkbox" name="productCalc" id="calculator__product3" value="<?= Loc::getMessage('COMP_AES_CALC_FUEL_PRODUCT_THREE'); ?>">
                    <label for="calculator__product3"><?= Loc::getMessage('COMP_AES_CALC_FUEL_PRODUCT_THREE'); ?></label>
                </div>
            </div>
        </div>
        <div class="calculator__item">
            <h3 class="calculator__item-caption"><?= Loc::getMessage('COMP_AES_CALC_FUEL_SIZE'); ?></h3>
            <div class="calculator__line-block">
                <div class="calculator__line-input">
                    <div class="input">
                        <input maxlength="255" class="form-control" type="text" id="calculator__input" value="100">
                    </div>
                </div>
                <div class="calculator__line">
                    <div id="calculator__slider">

                    </div>
                    <div class="calculator__slider-min">100</div>
                    <div class="calculator__slider-max">5000</div>
                </div>
            </div>
        </div>
        <div class="calculator__item">
            <h3 class="calculator__item-caption"><?= Loc::getMessage('COMP_AES_CALC_FUEL_ADDRESS'); ?></h3>
            <div class="input">
                <input maxlength="255" class="form-control" type="text" id="calculator__address" value="">
            </div>
            <div class="calculator__radio">
                <div class="filter">
                    <input type="radio" name="distanceMkad" value="<?= Loc::getMessage('COMP_AES_CALC_FUEL_ADDRESS_IN_MKAD'); ?>" id="calculator__distance-in" checked>
                    <label for="calculator__distance-in"><?= Loc::getMessage('COMP_AES_CALC_FUEL_ADDRESS_IN_MKAD'); ?></label>
                </div>
                <div class="filter">
                    <input type="radio" name="distanceMkad" value="<?= Loc::getMessage('COMP_AES_CALC_FUEL_ADDRESS_OUT_MKAD'); ?>" id="calculator__distance-out">
                    <label for="calculator__distance-out"><?= Loc::getMessage('COMP_AES_CALC_FUEL_ADDRESS_OUT_MKAD'); ?></label>
                </div>
            </div>
        </div>
    </div>
    <div class="calculator__itogo">
        <div class="calculator__price calculator__price-total"><?= Loc::getMessage('COMP_AES_CALC_FUEL_TOTAL', ['#TOTAL_FORMATED#' => '<span>0.00 руб.</span>']); ?></div>
        <div class="calculator__price calculator__price-liter"><?= Loc::getMessage('COMP_AES_CALC_FUEL_PRICE_LITER', ['#PRICE_LITER_FORMATED#' => '<span>0.00 руб./л</span>']); ?></div>
        <div class="calculator__button">
            <a class="btn btn-default" id="calculator__order-btn" href="#" data-event="jqm" data-param-id="<?= $arResult['WEBFORM_ID']; ?>" data-name="fuel_calc">
                <?= Loc::getMessage('COMP_AES_CALC_FUEL_ORDER_BUTTON'); ?>
            </a>
        </div>
    </div>

    <div id="calculator__params"></div>
</div>