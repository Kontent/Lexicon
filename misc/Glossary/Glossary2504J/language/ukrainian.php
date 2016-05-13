<?php
// Model code: if (!defined('')) define ('', '');
// First list standard definitions needed for cmsapi to function properly
// ukrainian.php by Bisyarin Artyom
if (!defined('_CMSAPI_CPANEL_RETURN')) define ('_CMSAPI_CPANEL_RETURN', 'Панель управління');
if (!defined('_CMSAPI_DISPLAY_NUMBER')) define ('_CMSAPI_DISPLAY_NUMBER', '?');
if (!defined('_CMSAPI_YES')) define ('_CMSAPI_YES', 'Так');
if (!defined('_CMSAPI_NO')) define ('_CMSAPI_NO', 'Ні');
if (!defined('_CMSAPI_CANCEL')) define ('_CMSAPI_CANCEL', 'Відмінити');
if (!defined('_CMSAPI_CONFIG_COMP')) define ('_CMSAPI_CONFIG_COMP', 'Конфігурацію збережено');
if (!defined('_CMSAPI_CLASS_NOT_PRESENT')) define ('_CMSAPI_CLASS_NOT_PRESENT', 'Помилка компонента %s: спроба використання неіснуючого класу %s');
if (!defined('_CMSAPI_METHOD_NOT_PRESENT')) define ('_CMSAPI_METHOD_NOT_PRESENT', 'Помилка компонента %s: відбувся запит задачі %s але відповідного методу для неї немає в класі %s');
if (!defined('_CMSAPI_DISPLAY_NUMBER')) DEFINE('_CMSAPI_DISPLAY_NUMBER','Показати #');
if (!defined('_CMSAPI_PAGE_TEXT')) DEFINE ('_CMSAPI_PAGE_TEXT', 'Сторінка');
if (!defined('_CMSAPI_PAGE_SHOW_RESULTS')) DEFINE('_CMSAPI_PAGE_SHOW_RESULTS','Показати результати ');
if (!defined('_CMSAPI_PAGE_SHOW_RANGE')) DEFINE('_CMSAPI_PAGE_SHOW_RANGE','з %s по %s із %s');
if (!defined('_CMSAPI_PREVIOUS')) define ('_CMSAPI_PREVIOUS', 'Попередня');
if (!defined('_CMSAPI_NEXT')) define ('_CMSAPI_NEXT', 'Наступна');
// Component specific definitions follow
if (!defined('_GLOSSARY_LIST_ENTRIES')) define ('_GLOSSARY_LIST_ENTRIES', 'Продивитися Записи');
if (!defined('_GLOSSARY_LIST_GLOSSARIES')) define ('_GLOSSARY_LIST_GLOSSARIES', 'Продивитися Глосарії');
if (!defined('_GLOSSARY_EDIT_ENTRIES')) define ('_GLOSSARY_EDIT_ENTRIES', 'Редагувати Записи');
if (!defined('_GLOSSARY_EDIT_GLOSSARIES')) define ('_GLOSSARY_EDIT_GLOSSARIES', 'Редагувати Глосарії');
if (!defined('_GLOSSARY_EDIT_CONFIG')) define ('_GLOSSARY_EDIT_CONFIG', 'Редагувати Конфігурацію');
if (!defined('_GLOSSARY_USER_CONFIG')) define ('_GLOSSARY_USER_CONFIG', 'Користувач');
if (!defined('_GLOSSARY_ADMIN_CONFIG')) define ('_GLOSSARY_ADMIN_CONFIG', 'Адміністратор');
if (!defined('_GLOSSARY_ABOUT_HEADING')) define ('_GLOSSARY_ABOUT_HEADING', 'Загальна Інформація');
if (!defined('_GLOSSARY_GLOSSARY_LIST')) define ('_GLOSSARY_GLOSSARY_LIST', 'Інші глосарії доступні тут');
if (!defined('_GLOSSARY_FILTER_TERM')) define ('_GLOSSARY_FILTER_TERM', 'Фільтрувати за терміном (використовуйте регулярні вирази):');
if (!defined('_GLOSSARY_FILTER_DEFINITION')) define ('_GLOSSARY_FILTER_DEFINITION', 'Фільтрувати за визначенням (слова, опціонально їх можно об"єднувати логічними операторами):');
if (!defined('_GLOSSARY_ALL_GLOSSARIES')) define ('_GLOSSARY_ALL_GLOSSARIES', 'Всі глосарії');
if (!defined('_GLOSSARY_REFRESH')) define ('_GLOSSARY_REFRESH', 'Оновити сторінку');
// Glossary entry headings
if (!defined('_GLOSSARY_TERM')) define ('_GLOSSARY_TERM', 'Термін');
if (!defined('_GLOSSARY_LETTER')) define ('_GLOSSARY_LETTER', 'Літера (опціонально)');
if (!defined('_GLOSSARY_DEFINITION')) define ('_GLOSSARY_DEFINITION', 'Визначення');
if (!defined('_GLOSSARY_DATE')) define ('_GLOSSARY_DATE', 'Дата');
if (!defined('_GLOSSARY_PUBLISHED')) define ('_GLOSSARY_PUBLISHED', 'Опубліковано');
if (!defined('_GLOSSARY_NAME')) define ('_GLOSSARY_NAME', 'Назва глосарію');
if (!defined('_GLOSSARY_DESCRIPTION')) define ('_GLOSSARY_DESCRIPTION', 'Опис глосарію');
if (!defined('_GLOSSARY_GLOSSARY_SELECT')) define ('_GLOSSARY_GLOSSARY_SELECT', 'Виберіть глосарій');
// Glossary entry edit labels - admin side
if (!defined('_GLOSSARY_AUTHOR_NAME')) define ('_GLOSSARY_AUTHOR_NAME', 'Ім"я Автору');
if (!defined('_GLOSSARY_LOCALITY')) define ('_GLOSSARY_LOCALITY', 'Місцезнаходження Автора');
if (!defined('_GLOSSARY_MAIL')) define ('_GLOSSARY_MAIL', 'Email Автора');
if (!defined('_GLOSSARY_PAGE')) define ('_GLOSSARY_PAGE', 'Сайт Автора');
if (!defined('_GLOSSARY_COMMENT')) define ('_GLOSSARY_COMMENT', 'Коментар');
// Glossary entry edit labels - user side
if (!defined('_GLOSSARY_USER_LOCALITY')) define ('_GLOSSARY_USER_LOCALITY', 'Ваше Місцезнаходження');
if (!defined('_GLOSSARY_USER_MAIL')) define ('_GLOSSARY_USER_MAIL', 'Ваш eMail');
if (!defined('_GLOSSARY_USER_NAME')) define ('_GLOSSARY_USER_NAME', 'Ваше Ім"я');
if (!defined('_GLOSSARY_USER_URI')) define ('_GLOSSARY_USER_URI', 'Ваш Сайт');
if (!defined('_GLOSSARY_USER_SUBMIT')) define ('_GLOSSARY_USER_SUBMIT', 'Гаразд');
if (!defined('_GLOSSARY_USER_CLEAR')) define ('_GLOSSARY_USER_CLEAR', 'Очистити');
// Glossary item listing headings etc
if (!defined('_GLOSSARY_TERM_HEAD')) define ('_GLOSSARY_TERM_HEAD', 'Термін');
if (!defined('_GLOSSARY_DEFINITION_HEAD')) define ('_GLOSSARY_DEFINITION_HEAD', 'Визначення');
if (!defined('_GLOSSARY_ITEM_COUNT')) define ('_GLOSSARY_ITEM_COUNT', 'У глосарії %d термінів.');
if (!defined('_GLOSSARY_IS_EMPTY')) define ('_GLOSSARY_IS_EMPTY', 'Цей глосарій немає записів');
if (!defined('_GLOSSARY_ADD_ENTRY')) define ('_GLOSSARY_ADD_ENTRY', 'Додати новий запис');
// Glossary search texts
if (!defined('_GLOSSARY_SEARCH_INTRO')) define ('_GLOSSARY_SEARCH_INTRO', 'Шукати записи у глосарії');
if (!defined('_GLOSSARY_BEGINS_WITH')) define ('_GLOSSARY_BEGINS_WITH', 'Починається з');
if (!defined('_GLOSSARY_TERM_CONTAINS')) define ('_GLOSSARY_TERM_CONTAINS', 'Містить');
if (!defined('_GLOSSARY_EXACT_TERM')) define ('_GLOSSARY_EXACT_TERM', 'Точна фраза');
if (!defined('_GLOSSARY_SEARCH_SEARCH')) define ('_GLOSSARY_SEARCH_SEARCH', 'Шукати...');
if (!defined('_GLOSSARY_GO')) define ('_GLOSSARY_GO', 'Уперед!');
// Definitions of glossary configuration items
if (!defined('_GLOSSARY_UTF8')) define ('_GLOSSARY_UTF8', 'Вибрати UTF-8:');
if (!defined('_GLOSSARY_PER_PAGE')) define ('_GLOSSARY_PER_PAGE', 'Записів на сторінку:');
if (!defined('_GLOSSARY_ALLOWENTRY')) define ('_GLOSSARY_ALLOWENTRY', 'Дозволити Записи:');
if (!defined('_GLOSSARY_ANONENTRY')) define ('_GLOSSARY_ANONENTRY', 'Анонімні Записи:');
if (!defined('_GLOSSARY_HIDEAUTHOR')) define ('_GLOSSARY_HIDEAUTHOR', 'Сховати Автора:');
if (!defined('_GLOSSARY_USEEDITOR')) define ('_GLOSSARY_USEEDITOR', 'Використовувати редактор за замовченням:');
if (!defined('_GLOSSARY_SHOWCATEGORIES')) define ('_GLOSSARY_SHOWCATEGORIES', 'Показати Глосарії:');
if (!defined('_GLOSSARY_SHOWCATDESCRIPTIONS')) define ('_GLOSSARY_SHOWCATDESCRIPTIONS', 'Показати Описи Глосаріїв:');
if (!defined('_GLOSSARY_SHOWNUMBEROFENTRIES')) define ('_GLOSSARY_SHOWNUMBEROFENTRIES', 'Показати кількість записів:');
if (!defined('_GLOSSARY_UTF8_ADMIN')) define ('_GLOSSARY_UTF8_ADMIN', 'Вибрати UTF-8:');
if (!defined('_GLOSSARY_SHOW_ALPHABET')) define ('_GLOSSARY_SHOW_ALPHABET', 'Показувати Абетку:');
if (!defined('_GLOSSARY_STRIP_ACCENTS')) define ('_GLOSSARY_STRIP_ACCENTS', 'Видаляти діакритичні знаки:');
if (!defined('_GLOSSARY_AUTOPUBLISH')) define ('_GLOSSARY_AUTOPUBLISH', 'Автопублікація записів:');
if (!defined('_GLOSSARY_NOTIFY')) define ('_GLOSSARY_NOTIFY', 'Повідомляти Уеб-Майстра:');
if (!defined('_GLOSSARY_NOTIFY_EMAIL')) define ('_GLOSSARY_NOTIFY_EMAIL', 'Уеб-Майстрова пошта:');
if (!defined('_GLOSSARY_THANK_USER')) define ('_GLOSSARY_THANK_USER', 'Дякувати користувачу:');
if (!defined('_GLOSSARY_PAGE_SPREAD')) define ('_GLOSSARY_PAGE_SPREAD', 'Протяжність номерів сторінок:');
if (!defined('_GLOSSARY_LANGUAGE')) define ('_GLOSSARY_LANGUAGE', 'Використовувати мову, відмінну від встановленої у всій системі');
// Descriptions of glossary configuration items
if (!defined('_GLOSSARY_DESC_UTF8')) define ('_GLOSSARY_DESC_UTF8', 'Glossary to tell database and browser to use UTF-8');
if (!defined('_GLOSSARY_DESC_PER_PAGE')) define ('_GLOSSARY_DESC_PER_PAGE', 'Кількість записів на сторінку.');
if (!defined('_GLOSSARY_DESC_ALLOWENTRY')) define ('_GLOSSARY_DESC_ALLOWENTRY', 'Дозволяти користувачам додавати нові записи. (Редактори, Видавці, Адміністратори та Супер-Адміністратори завжди можуть додавати записи.)');
if (!defined('_GLOSSARY_DESC_ANONENTRY')) define ('_GLOSSARY_DESC_ANONENTRY', 'Дозволяти незареєстрованим користувачам додавати записи.');
if (!defined('_GLOSSARY_DESC_HIDEAUTHOR')) define ('_GLOSSARY_DESC_HIDEAUTHOR', 'Ховати підробиці про автора (адресу, пошту, тощо)');
if (!defined('_GLOSSARY_DESC_USEEDITOR')) define ('_GLOSSARY_DESC_USEEDITOR', '"Так" для використання редактора за замовченням, "Ні" — звичайна textarea.');
if (!defined('_GLOSSARY_DESC_SHOWCATEGORIES')) define ('_GLOSSARY_DESC_SHOWCATEGORIES', 'Якщо вибрати "Ні", то буде показуватись лише глосарій за замовченням');
if (!defined('_GLOSSARY_DESC_SHOWCATDESCRIPTIONS')) define ('_GLOSSARY_DESC_SHOWCATDESCRIPTIONS', 'Якщо вибрати "Ні", то опис глосарію не з"явиться у фронт-ендовій частині сайту');
if (!defined('_GLOSSARY_DESC_SHOWNUMBEROFENTRIES')) define ('_GLOSSARY_DESC_SHOWNUMBEROFENTRIES', '"Так" для показу кількості записів у фронт-ендовій частині сайту');
if (!defined('_GLOSSARY_DESC_UTF8_ADMIN')) define ('_GLOSSARY_DESC_UTF8_ADMIN', 'Glossary to tell database and browser to use UTF-8');
if (!defined('_GLOSSARY_DESC_SHOW_ALPHABET')) define ('_GLOSSARY_DESC_SHOW_ALPHABET', 'Показувати абетку для перегляду термінів, що починаються з певної літери.');
if (!defined('_GLOSSARY_DESC_STRIP_ACCENTS')) define ('_GLOSSARY_DESC_STRIP_ACCENTS', 'Ігнорувати діакритичні знаки при формуванні абетки');
if (!defined('_GLOSSARY_DESC_AUTOPUBLISH')) define ('_GLOSSARY_DESC_AUTOPUBLISH', 'Автоматично публікувати нові записи в глосарії.');
if (!defined('_GLOSSARY_DESC_NOTIFY')) define ('_GLOSSARY_DESC_NOTIFY', 'Повідомляти Уеб-Майстра коли з"являються нові записи.');
if (!defined('_GLOSSARY_DESC_NOTIFY_EMAIL')) define ('_GLOSSARY_DESC_NOTIFY_EMAIL', 'Пошта, куди слати повідомлення.');
if (!defined('_GLOSSARY_DESC_THANKUSER')) define ('_GLOSSARY_DESC_THANKUSER', 'Відсилати лист-подяку користувачу.');
if (!defined('_GLOSSARY_DESC_PAGE_SPREAD')) define ('_GLOSSARY_DESC_PAGE_SPREAD', 'Скільки номерів інших сторінок показувати у навігації по сторінкам');
if (!defined('_GLOSSARY_DESC_LANGUAGE')) define ('_GLOSSARY_DESC_LANGUAGE', 'Не використовувати автоматичний вибір мови');
