<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule('iblock')) {
    return;
}

use Bitrix\Iblock\Component\Tools;
use Bitrix\Main\Loader;

class ReviewsComplexComponent extends CBitrixComponent
{
    // выполняет основной код компонента, аналог конструктора (метод подключается автоматически)
    public function executeComponent(): void
    {
        Loader::includeModule('iblock');

        // если выбран режим поддержки ЧПУ, вызываем метод sefMode()
        if ($this->arParams["SEF_MODE"] === "Y") {
            $componentPage = $this->sefMode();
        }

        // если отключен режим поддержки ЧПУ, вызываем метод noSefMode()
        if ($this->arParams["SEF_MODE"] != "Y") {
            $componentPage = $this->noSefMode();
        }

        // отдаем 404 статус если не найден шаблон
        if (!$componentPage) {
            Tools::process404(
                $this->arParams["MESSAGE_404"],
                ($this->arParams["SET_STATUS_404"] === "Y"),
                ($this->arParams["SET_STATUS_404"] === "Y"),
                ($this->arParams["SHOW_404"] === "Y"),
                $this->arParams["FILE_404"]
            );
        }

        $this->IncludeComponentTemplate($componentPage);
    }

    // метод обработки режима ЧПУ
    protected function sefMode()
    {
        // дополнительные GET параметры которые будем отлавливать в запросе, в массив $arVariables будет добавлена переменная sort,
        // значение которой будет получено из $_REQUEST['sort'], применяется когда не нужно указывать точный псевдоним для ключа
        $arComponentVariables = [
            'sort'
        ];

        // дополнительные GET параметры которые будем отлавливать в запросе, полезно например для постраничной навигации.
        // В массив $arVariableAliases будет добавлена переменная ELEMENT_COUNT, значение которой будет получено из $_REQUEST['count']
        $arDefaultVariableAliases404 = [
            'section' => [
                'ELEMENT_COUNT' => 'count',
            ]
        ];

        // метод предназначен для объединения дефолтных GET параметров которые приходят в $arParams["VARIABLE_ALIASES"], в режиме ЧПУ $arParams["VARIABLE_ALIASES"] будет пустой и дополнительных GET параметров из массива $arDefaultVariableAliases404. Параметры из настроек $arrParams заменяют дополнительные из $arDefaultVariableAliases404
        $arVariableAliases = CComponentEngine::makeComponentVariableAliases(
            // массив псевдонимов переменных из GET параметра
            $arDefaultVariableAliases404,
            // массив псевдонимов из $arParams, в режиме ЧПУ $arParams["VARIABLE_ALIASES"] будет пустой
            $this->arParams["VARIABLE_ALIASES"]
        );

        // если в комплексном компоненте не задан базовый URL
        if (empty($this->arParams["SEF_FOLDER"])) {
            // получаем данные из настроек инфоблока
            $dbResult = CIBlock::GetByID($this->arParams["IBLOCK_ID"])->GetNext();
            if (!empty($dbResult)) {
                // перетираем данные в $arParams["SEF_URL_TEMPLATES"]
                $this->arParams["SEF_URL_TEMPLATES"]["managers"] = $dbResult["DETAIL_PAGE_URL"];
                $this->arParams["SEF_URL_TEMPLATES"]["feedback"] = $dbResult["SECTION_PAGE_URL"];
                $this->arParams["SEF_FOLDER"] = $dbResult["LIST_PAGE_URL"];
            }
        }

        // значение маски URL по умолчанию
        $arDefaultUrlTemplates404 = [
            "managers" => "managers/",
            "feedback" => "feedback/",
        ];

        // метод предназначен для объединения дефолтных параметров масок URL которые приходят в arParams["SEF_URL_TEMPLATES"] и из массива $arDefaultUrlTemplates404. Параметры из настроек $arrParams заменяют дефолтные из $arDefaultUrlTemplates404
        $arUrlTemplates = CComponentEngine::makeComponentUrlTemplates(
            // массив переменных с масками по умолчанию
            $arDefaultUrlTemplates404,
            // массив переменных с масками из входных параметров $arParams["SEF_URL_TEMPLATES"]
            $this->arParams["SEF_URL_TEMPLATES"]
        );

        // объект для поиска шаблонов
        $engine = new CComponentEngine($this);
        // главная переменная комплексного компонента, именно она будут записана в массив $arResult, как результат работы комплексного компонента.
        // она будет доступна в файлах feedback.php, managers.php, list.php, которые будут подключены, после того как отработает class.php
        $arVariables = [];
        // определение шаблона, какой файл подключать и заполнение $arVariables получеными URL в соответствие с масками
        $componentPage = $engine->guessComponentPath(
            // путь до корня секции
            $this->arParams["SEF_FOLDER"],
            // массив масок
            $arUrlTemplates,
            $arVariables
        );

        // проверяем, если не удалось сопоставить шаблон, значит выводим list.php
        if ($componentPage === false) {
            $componentPage = 'list';
        }
        // метод предназначен для объединения GET и URL параметров, результат записываем в $arVariables
        CComponentEngine::initComponentVariables(
            // нужен для режима ЧПУ, содержит файл который будет подключен feedback.php, managers.php, list.php
            $componentPage,
            // массив дополнительных GET параметров без псевдонимов
            $arComponentVariables,
            // массив основных GET параметров с псевдонимами
            $arVariableAliases,
            // обьединяем все найденные URL и GET параметры и записываем в переменну
            $arVariables
        );

        // формируем arResult
        $this->arResult = [
            // данные полученые из GET и URL параметров 
            "VARIABLES" => $arVariables,
            // массив с параметрами псевдонимов для возможности востановления дальше в обычном компоненте
            "ALIASES" => $arVariableAliases
        ];

        return $componentPage;
    }

    // метод обработки режима без ЧПУ
    protected function noSefMode(): string
    {
        // дополнительные GET параметры которые будем отлавливать в запросе, полезно например для постраничной навигации. В массив $arVariableAliases будет добавлена переменная ELEMENT_COUNT, значение которой будет получено из $_REQUEST['count'], в итоге данные попадут в $arVariables, применяется когда нужно указать точный псевдоним для ключа 
        $arDefaultVariableAliases = [
            'ELEMENT_COUNT' => 'count',
        ];

        // метод предназначен для объединения дефолтных GET параметров которые приходят в $arParams["VARIABLE_ALIASES"] и дополнительных GET параметров из массива $arDefaultVariableAliases. Параметры из настроек $arrParams заменяют дополнительные из $arDefaultVariableAliases
        $arVariableAliases = CComponentEngine::makeComponentVariableAliases(
            // массив псевдонимов переменных из GET параметра
            $arDefaultVariableAliases,
            // массив псевдонимов из $arParams
            $this->arParams["VARIABLE_ALIASES"]
        );
        // главная переменная комплексного компонента, именно она будут записана в массив $arResult, как результат работы комплексного компонента.
        // она будет доступна в файлах feedback.php, managers.php, list.php, которые будут подключены, после того как отработает class.php
        $arVariables = [];

        // дополнительные GET параметры которые будем отлавливать в запросе, в массив $arVariables будет добавлена переменная sort, значение которой будет получено из $_REQUEST['sort'], применяется когда не нужно указывать точный псевдоним для ключа 
        $arComponentVariables = [
            'sort',
            'FEEDBACK_CODE',
            'MANAGER_CODE'
        ];

        // метод предназначен для получения и объединения GET параметров результат записываем в $arVariables
        CComponentEngine::initComponentVariables(
            // нужен для режима ЧПУ, содержит файл который будет подключен feedback.php, managers.php, list.php
            false,
            // массив дополнительных GET параметров без псевдонимов
            $arComponentVariables,
            // массив основных GET параметров с псевдонимами
            $arVariableAliases,
            // обьединяем все найденные GET параметры и записываем в переменную
            $arVariables
        );

        // по найденным параметрам $arVariables определяем тип страницы
        if ((isset($arVariables["FEEDBACK_ID"]) && intval($arVariables["FEEDBACK_ID"]) > 0)
            || (isset($arVariables["FEEDBACK_CODE"]) && $arVariables["FEEDBACK_CODE"] <> '')
        ) {
            $componentPage = "feedback";
        } elseif ((isset($arVariables["MANAGER_ID"]) && intval($arVariables["MANAGER_ID"]) > 0)
            || (isset($arVariables["MANAGER_CODE"]) && $arVariables["MANAGER_CODE"] <> '')
        ) {
            $componentPage = "managers";
        } else {
            $componentPage = "list";
        }

        // формируем $arResult
        $this->arResult = [
            // данные полученые из GET параметров 
            "VARIABLES" => $arVariables,
            // массив с параметрами псевдонимов для возможности востановления дальше в обычном компоненте
            "ALIASES" => $arVariableAliases
        ];

        return $componentPage;
    }
}
