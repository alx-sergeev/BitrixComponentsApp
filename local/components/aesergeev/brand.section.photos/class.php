<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Application,
    \Bitrix\Main\Loader,
    \Bitrix\Main\Localization\Loc,
    \Bitrix\Iblock\Component\Tools,
    \Bitrix\Iblock\IblockTable,
    \Bitrix\Iblock\InheritedProperty\SectionValues;

Loader::includeModule('iblock');


/**
 * Class BrandSectionPhotos
 * Компонент для вывода фотогаллереи с иеархической структурой для раздела Производители.
 */
class BrandSectionPhotos extends CBitrixComponent {
    public function setProcess404() {
        Tools::process404(
            ""
            , true
            , true
            , true
            , false
        );

        return true;
    }

    public function executeComponent() {
        global $APPLICATION;

        $arParams = $this->arParams;
        $arResult = [];

        $CPHPCache = new CPHPCache;
        $CACHE_TIME = 4 * 604800;
        $CACHE_PATH = '/' . SITE_ID . $this->GetRelativePath();
        $CACHE_ID = SITE_ID . '|' . $APPLICATION->GetCurPage() . '|';
        foreach($arParams as $k => $v) {
            if(strncmp('~', $k, 1))
                $CACHE_ID .= ',' . $k . '=' . $v;
        }

        if($CPHPCache->StartDataCache($CACHE_TIME, $CACHE_ID, $CACHE_PATH)) {
            /**
             * Получаем необходимые данные.
             */
            $request = Application::getInstance()->getContext()->getRequest();

            $brandCode = $request->getQuery('BRAND_CODE');
            $brandSectionCode = $request->getQuery('SECTION_BRAND_CODE');
            if(!$brandCode || !$brandSectionCode) $this->setProcess404();


            $ibBrand = IblockTable::getList([
                'filter' => ['CODE' => 'brand'],
                'select' => ['ID'],
                'cache' => ['ttl' => 4 * 604800],
                'limit' => 1
            ])->fetch()['ID'];

            $ibForBrand = IblockTable::getList([
                'filter' => ['CODE' => 'for_brand'],
                'select' => ['ID'],
                'cache' => ['ttl' => 4 * 604800],
                'limit' => 1
            ])->fetch()['ID'];
            if(!$ibBrand || !$ibForBrand) $this->setProcess404();


            /**
             * Проверки на существование текущего раздела и производителя с привязкой к нему.
             * Добавляем хлебные крошки.
             */
            $bs = new \CIBlockSection;
            $arCurSection = $bs->GetList(
                [],
                ['IBLOCK_ID' => $ibForBrand, 'CODE' => $brandSectionCode],
                false,
                ['ID', 'NAME', 'DESCRIPTION'],
                false,
                ['nTopCount' => 1]
            )->GetNext();
            if(!$arCurSection) $this->setProcess404();

            $arResult['DESCRIPTION'] = $arCurSection['DESCRIPTION'];

            $el = new \CIBlockElement;
            $arBrand = $el->GetList(
                [],
                ['IBLOCK_ID' => $ibBrand, 'CODE' => $brandCode, 'PROPERTY_LINK_TO_SECTIONS_PHOTO' => $arCurSection['ID']],
                false,
                ['nTopCount' => 1],
                ['NAME', 'CODE']
            )->GetNext();
            if(!$arBrand) $this->setProcess404();


            /**
             * Сортировка разделов и элементов.
             */
            $arSections = [];
            $dbSections = $bs->GetList(
                [],
                ['IBLOCK_ID' => $ibForBrand, 'SECTION_ID' => $arCurSection['ID']],
                false,
                ['ID', 'NAME', 'SORT'],
                false,
                false
            );
            while($arSect = $dbSections->GetNext()) {
                $arSections[ $arSect['ID'] ] = $arSect;
            }
            $arResult['SECTIONS'] = $arSections;

            $arItems = $el->GetList(
                ['SORT' => 'ASC'],
                ['IBLOCK_ID' => $ibForBrand, 'SECTION_ID' => $arCurSection['ID'], 'INCLUDE_SUBSECTIONS' => 'Y'],
                false,
                false,
                ['IBLOCK_ID', 'ID', 'NAME', 'PREVIEW_PICTURE', 'IBLOCK_SECTION_ID', 'PROPERTY_FILE']
            );
            while($arItem = $arItems->GetNext()) {
                if($arItem['IBLOCK_SECTION_ID'] && $arSections[$arItem['IBLOCK_SECTION_ID']]) {
                    $arItem['SECTION_NAME'] = $arSections[$arItem['IBLOCK_SECTION_ID']]['NAME'];
                    $arItem['SECTION_SORT'] = $arSections[$arItem['IBLOCK_SECTION_ID']]['SORT'];
                } else if(empty($arSections)) {
                } else {
                    $arItem['SECTION_NAME'] = Loc::getMessage('COMP_AES_SECTION_OTHER_TITLE');
                    $arItem['SECTION_SORT'] = 999999;
                }

                if($arItem['PROPERTY_FILE_VALUE']) {
                    $arItem['FILE'] = \CFile::GetPath($arItem['PROPERTY_FILE_VALUE']);
                }

                $arResult['ITEMS'][] = $arItem;
            }

            usort($arResult['ITEMS'], function($a, $b) {
                $orderBy = ['SECTION_SORT'=>'asc', 'SORT'=>'asc'];

                $result = 0;
                foreach ($orderBy as $key => $value) {
                    if ($a[$key] == $b[$key]) {
                        continue;
                    }

                    $result = ($a[$key] < $b[$key]) ? -1 : 1;

                    if ($value == 'desc') {
                        $result = -$result;
                    }
                    break;
                }

                return $result;
            });

            $arResult['ID'] = $arCurSection['ID'];
            $arResult['NAME'] = $arCurSection['NAME'];
            $arResult['IB_BRAND'] = $ibBrand;
            $arResult['IB_FOR_BRAND'] = $ibForBrand;
            $arResult['BRAND_NAME'] = $arBrand['NAME'];
            $arResult['BRAND_CODE'] = $arBrand['CODE'];
            $arResult['SEF_FOLDER_PATH'] = $arParams['SEF_FOLDER_PATH'];

            $CPHPCache->EndDataCache(['arResult' => $arResult]);
        } else {
            extract($CPHPCache->GetVars());
        }

        $this->arResult = $arResult;
        $this->includeComponentTemplate();


        /**
         * SEO настройки раздела и хлебные крошки.
         */
        if($arResult['BRAND_NAME'] && $arResult['BRAND_CODE'] && $arResult['SEF_FOLDER_PATH']) {
            $APPLICATION->AddChainItem($arResult['BRAND_NAME'], $arResult['SEF_FOLDER_PATH'] . $arResult['BRAND_CODE'] . '/');
        }

        $ipropSectionValues = new SectionValues($arResult['IB_FOR_BRAND'], $arResult['ID']);
        $arResult['SEO'] = $ipropSectionValues->getValues();

        if(!empty($arResult['SEO'])) {
            if ($arResult['SEO']['SECTION_META_TITLE']) {
                $APPLICATION->SetPageProperty("title", $arResult['SEO']['SECTION_META_TITLE']);
            } else if ($arResult['NAME']) {
                $APPLICATION->SetPageProperty("title", $arResult['NAME']);
            }

            if ($arResult['SEO']['SECTION_META_KEYWORDS']) {
                $APPLICATION->SetPageProperty("keywords", $arResult['SEO']['SECTION_META_KEYWORDS']);
            }

            if ($arResult['SEO']['SECTION_META_DESCRIPTION']) {
                $APPLICATION->SetPageProperty("description", $arResult['SEO']['SECTION_META_DESCRIPTION']);
            }

            if ($arResult['SEO']['SECTION_PAGE_TITLE']) {
                $APPLICATION->SetTitle($arResult['SEO']['SECTION_PAGE_TITLE']);
            } else if ($arResult['NAME']) {
                $APPLICATION->SetTitle($arResult['NAME']);
            }
        } else if ($arResult['NAME']) {
            $APPLICATION->SetTitle($arResult['NAME']);
        }
    }
}