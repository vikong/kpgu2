# КПГУ2.0
Проект КПГУ ГосЭДО [НИИ "Восход"](https://www.voskhod.ru/) - контроль процесса работы с поручениями АП РФ

## Команды

Добавление одного контейнера - для тестирования
```code
php artisan process:container file
```
где _file_ может быть любым файлом из App/tests/Resources

Добавление всех контейнеров из папки
```code
php artisan process:folder path
```
где _path_ путь к папке

Добавление всех контейнеров из папки ./public/income
```code
php artisan process:folder
```
