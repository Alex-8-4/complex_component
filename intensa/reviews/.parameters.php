<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

if (!CModule::IncludeModule('iblock')) {
    return;
}

// получаем массив всех типов инфоблоков для возможности выбора
$arIBlockType = CIBlockParameters::GetIBlockTypes();
$arInfoBlocks = [];

// выбираем активные инфоблоки
$arFilterInfoBlocks = ['ACTIVE' => 'Y'];

// сортируем по озрастанию поля сортировка
$arOrderInfoBlocks = ['SORT' => 'ASC'];

// если уже выбран тип инфоблока, выбираем инфоблоки только этого типа
if (!empty($arCurrentValues['IBLOCK_TYPE'])) {
    $arFilterInfoBlocks['TYPE'] = $arCurrentValues['IBLOCK_TYPE'];
}
$rsIBlock = CIBlock::GetList($arOrderInfoBlocks, $arFilterInfoBlocks);

// перебираем и выводим в адмику доступные информационные блоки
while ($obIBlock = $rsIBlock->Fetch()) {
    $arInfoBlocks[$obIBlock['ID']] = '[' . $obIBlock['ID'] . '] ' . $obIBlock['NAME'];
}

// настройки компонента, формируем массив $arParams
$arComponentParameters = [
    // основной массив с параметрами
    "PARAMETERS" => [
        // выбор типа инфоблока
        'IBLOCK_TYPE' => [                       // ключ массива $arParams в component.php
            'PARENT' => 'BASE',                  // название группы
            'NAME' => 'Выберите тип инфоблока',  // название параметра
            'TYPE' => 'LIST',                    // тип элемента управления, в котором будет устанавливаться параметр
            'VALUES' => $arIBlockType,           // входные значения
            'REFRESH' => 'Y',                    // перегружать настройки или нет после выбора (N/Y)
            'DEFAULT' => 'news',                 // значение по умолчанию
            'MULTIPLE' => 'N',                   // одиночное/множественное значение (N/Y)
        ],
        // выбор самого инфоблока
        'IBLOCK_ID' => [
            'PARENT' => 'BASE',
            'NAME' => 'Выберите инфоблок',
            'TYPE' => 'LIST',
            'VALUES' => $arInfoBlocks,
            'REFRESH' => 'Y',
            "DEFAULT" => '',
            "ADDITIONAL_VALUES" => "Y",
        ],
        // настройки режима без ЧПУ, доступно в админке до активации чекбокса
        "VARIABLE_ALIASES" => [
            "FEEDBACK_ID" => [
                "NAME" => 'GET параметр для ID фидбэка без ЧПУ',
                "DEFAULT" => "FEEDBACK_ID",
            ],
            "MANAGER_ID" => [
                "NAME" => 'GET параметр для ID менеджера без ЧПУ',
                "DEFAULT" => "MANAGER_ID",
            ]
        ],
        // настройки режима ЧПУ, доступно в админке после активации чекбокса
        "SEF_MODE" => [
            "feedback" => [
                "NAME" => 'Страница фидбэка',
                "DEFAULT" => "feedback/",
            ],
            "managers" => [
                "NAME" => 'Страница менеджеров',
                "DEFAULT" => "managers/",
            ]
        ],
    ]
];
