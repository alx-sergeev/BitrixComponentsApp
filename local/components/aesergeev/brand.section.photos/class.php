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

    public function onPrepareComponentParams($arParams) {
        $this->arParams = $arParams;
        $this->arResult = [];
        $request = Application::getInstance()->getContext()->getRequest();

        global $APPLICATION;


        /**
         * Получаем необходимые данные.
         */
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

        $this->arResult['DESCRIPTION'] = $arCurSection['DESCRIPTION'];

        $el = new \CIBlockElement;
        $arBrand = $el->GetList(
            [],
            ['IBLOCK_ID' => $ibBrand, 'CODE' => $brandCode, 'PROPERTY_LINK_TO_SECTIONS_PHOTO' => $arCurSection['ID']],
            false,
            ['nTopCount' => 1],
            ['NAME', 'CODE']
        )->GetNext();
        if(!$arBrand) $this->setProcess404();

        if($arBrand && $this->arParams['SEF_FOLDER_PATH']) {
            $APPLICATION->AddChainItem($arBrand['NAME'], $this->arParams['SEF_FOLDER_PATH'] . $arBrand['CODE'] . '/');
        }


        /**
         * SEO
         */
        $ipropSectionValues = new SectionValues($ibForBrand, $arCurSection['ID']);
        $arSEO = $ipropSectionValues->getValues();

        if ($arSEO['SECTION_META_TITLE']) {
            $APPLICATION->SetPageProperty("title", $arSEO['SECTION_META_TITLE']);
        }
        if ($arSEO['SECTION_META_KEYWORDS']) {
            $APPLICATION->SetPageProperty("keywords", $arSEO['SECTION_META_KEYWORDS']);
        }
        if ($arSEO['SECTION_META_DESCRIPTION']) {
            $APPLICATION->SetPageProperty("description", $arSEO['SECTION_META_DESCRIPTION']);
        }
        if ($arSEO['SECTION_PAGE_TITLE']) {
            $APPLICATION->SetTitle($arSEO['SECTION_PAGE_TITLE']);
        } else if ($arCurSection['NAME']) {
            $APPLICATION->SetTitle($arCurSection['NAME']);
        }


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
        $this->arResult['SECTIONS'] = $arSections;

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

            $this->arResult['ITEMS'][] = $arItem;
        }

        usort($this->arResult['ITEMS'], function($a, $b) {
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

        return $this->arParams;
    }

    public function executeComponent() {
        $this->includeComponentTemplate();
    }
}