<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

// подключаем компонент
$APPLICATION->IncludeComponent(
    "intensa:reviews.list",
    "",
    [
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A",
        "SEF_MODE" => $arParams["SEF_MODE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
    ],
    $component
);
