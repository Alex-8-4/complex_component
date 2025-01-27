<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

// подключаем компонент
$APPLICATION->IncludeComponent(
    "intensa:reviews.feedback",
    "",
    [
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A",
        "SEF_MODE" => $arParams["SEF_MODE"],
        "SEF_FOLDER" => $arParams["SEF_FOLDER"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "FEEDBACK_ID" => $arResult["VARIABLES"]["FEEDBACK_ID"],
        "FEEDBACK_CODE" => $arResult["VARIABLES"]["FEEDBACK_CODE"]
    ],
    $component
);
