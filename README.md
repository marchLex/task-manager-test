# Задание

Написать простой todo manager

## Требования к функционалу

- `Бэкэнд: Laravel или Bitrix`
- `Фронтэнд: Bootstrap или свое`
- `Создание задачи (наименование, описание, теги)`
- `Просмотр списка задач`
- `Редактирование`
- `Удаление`
- `Поиск`
- `Пагинация`

## Результат

Загрузить в git и дать ссылку на рабочую версию

# План выполнения

- `Продумать структуру БД`
- `Определиться, что именно реализуем на битриксе (выглядит так, что легче всего сделать все на битриксе, но остановимся только на использовании инфоблоков)`
- `Продумать верстку. В задаче не требуется сделать красивый фронт, но сделаем, как привыкли`
- `В задаче не сказано, нужно ли делать раздельные списки для разных пользователей, но мы предусмотрим такой вариант`
- `Задачу можно реализовать без использования JS, что покроет незначительный процент пользователей с отключенным JS, но мы хотим отзывчивость`
- `В задаче не указано, в каком виде будет сохраняться описание задачи. Будем использовать plain-текст без WYSIWYG`
- `По условию нужно использовать пагинацию. Опять же, проще всего реализовать на битриксе, но мы пойдем по другому пути`
- `Положить log.txt в корень, добавить в .gitignore. Позволит видеть, как обрабатываются проблемные места`