# Пример комплексного компонента D7 для Bitrix

Если подключаем комплексный компонент в режиме работы с поддержкой ЧПУ, в файл **urlrewrite.php** автаматически добавляется правило, но лучше проверить:

```
array (
    'CONDITION' => '#^/reviews/#',
    'RULE' => '',
    'ID' => 'intensa:reviews',
    'PATH' => '/reviews/index.php',
    'SORT' => 100,
)
```

Маски URL по которым работает комплексный компонент:

В режиме ЧПУ:
1. /reviews/ - список отзывов
2. /reviews/feedback/ - фидбэк
3. /reviews/managers/ - менеджеры

В обычном режиме:
1. /reviews/?FEEDBACK_ID=1
2. /reviews/?MANAGER_ID=1

