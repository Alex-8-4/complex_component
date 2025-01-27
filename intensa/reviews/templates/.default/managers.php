<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

// подключаем компонент
$APPLICATION->IncludeComponent(
    "intensa:reviews.managers",
    "",
    [
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A",
        "SEF_FOLDER" => $arParams["SEF_FOLDER"],
        "SEF_MODE" => $arParams["SEF_MODE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "MANAGER_ID" => $arResult["VARIABLES"]["MANAGER_ID"],
        "MANAGER_CODE" => $arResult["VARIABLES"]["MANAGER_CODE"]
    ],
    $component
);
