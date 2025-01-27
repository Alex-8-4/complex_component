<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Loader;

class ReviewsManagers extends CBitrixComponent
{
    // выполняет основной код компонента, аналог конструктора (метод подключается автоматически)
    public function executeComponent()
    {
        try {
            $this->checkModules();
            $this->getResult();
        } catch (SystemException $e) {
            ShowError($e->getMessage());
        }
    }

    // подключение языковых файлов (метод подключается автоматически)
    public function onIncludeComponentLang()
    {
        Loc::loadMessages(__FILE__);
    }

    protected function checkModules()
    {
        if (!Loader::includeModule('iblock')) {
            throw new SystemException(Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED'));
        }
    }

    // обработка массива $arParams (метод подключается автоматически)
    public function onPrepareComponentParams($arParams)
    {
        if (empty($arParams['CACHE_TIME'])) {
            $arParams['CACHE_TIME'] = 3600;
        } else {
            $arParams['CACHE_TIME'] = (int) $arParams['CACHE_TIME'];
        }

        return $arParams;
    }

    protected function getResult()
    {
        if ($this->startResultCache()) {
            $res = \Bitrix\Iblock\ElementTable::getList([
                'select' => ["ID", "NAME", "DETAIL_TEXT", "CODE"],
                'filter' => [
                    'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                    'ACTIVE' => 'Y',
                ],
            ]);

            while ($arItem = $res->fetch()) {
                $this->arResult[] = $arItem;
            }

            if (!empty($this->arResult)) {
                $this->SetResultCacheKeys(
                    []
                );
                $this->IncludeComponentTemplate();
            } else {
                $this->AbortResultCache();
                \Bitrix\Iblock\Component\Tools::process404(
                    "Элементы не найдены",
                    true,
                    true
                );
            }
        }
    }
}
