# Binary Studio Academy 2019

## Домашнее задание #9

### Требования

Это задание направлено на работу c очередью сообщений и уведомлениями в Laravel.

***

### Задание

Необходимо отправить на сервер изображение, при помощи очереди сообщений из него сгенерировать 3 файла с размерами 100х100px, 150х150px и 250х250px, отправить клиенту URLы этих изображений при помощи push уведомлений и вставить их в DOM без перезагрузки страницы. Также необходимо отправить e-mail с текстом:

```
Dear <user name>,

Photos have been successfully uploaded and processed.
Here are links to the images:

<url to 100x100 photo>
<url to 150x150 photo>
<url to 250x250 photo>

Thanks!
```

Шаги выполнения:

Backend:

1) Запустите docker окружение
    
```bash
cp .env.example .env
docker-compose up -d
docker-compose run --rm composer install
docker-compose exec php php artisan key:generate
docker-compose exec php php artisan migrate
```

2) Настройте очередь сообщений `beanstalkd` в Laravel.

*Tip*: В предоставленном Docker окружении beanstalk сервер уже установлен. Имейте ввиду, что внутри контейнера имя хоста соответствует имени сервиса в docker-compose (т.е. hostname = 'beanstalk'). Также учтите, что PHP библиотека для работы с beanstalk не предустановлена. 

3) Запустите очередь внутри контейнера `php`

```bash
docker-compose exec php php artisan ...
```

4) Создайте ресурс для загрузки изображений:

```
POST /api/photos
multipart/form-data
```

Используйте middleware "auth" для этого ресурса, чтобы пользователь был аутентифицирован.

Изображения нужно [сохранять](https://laravel.com/docs/5.8/filesystem#file-uploads) в директорию `storage/app/public/images/{user_id}`.

Сохранять изображения нужно с иходным именем.

`Tip`: В публичном доступе файлы будут доступны по пути `/storage/files/images/{user_id}`

5) Создайте миграцию к таблице `photos` и модель `App\Entites\Photo`:

| name           | type                                                                | Description                                           |
|----------------|---------------------------------------------------------------------|-------------------------------------------------------|
| id             | unsignedBigInteger                                                  | PK                                                    |
| user_id        | unsignedBigInteger                                                  | id of user who uploaded image                         |
| original_photo | char(255)                                                           | path to original file on the server                   |
| photo_100_100  | char(255)                                                           | image 100x100 px                                      |
| photo_150_150  | char(255)                                                           | image 150х150 px                                      |
| photo_250_250  | char(255)                                                           | image 250x250 px                                      |
| status         | enum(UPLOADED,PROCESSING,SUCCESS,FAIL)                              | status of processing photo                            |

- UPLOADED - оригинальное изображение загружено;
- PROCESSING - начало обработки изображения;
- SUCCESS - изображение обработано успешно;
- FAIL - изображение обработано с ошибками

6) Создайте job'у для обработки изображения `App\Jobs\CropJob`, в которой с помощью метода `App\Services\PhotoService::crop` сгенерируйте копии изображения с именами:

`images/<user_id>/<original_name>100x100.<extension>`
`images/<user_id>/<original_name>150x150.<extension>`
`images/<user_id>/<original_name>250x250.<extension>`

7) Создайте уведомления:

- `App\Notifications\ImageProcessedNotification` - для отправки пуш уведомления со статусом `success` и изображениями. А также отправкой e-mail сообщения пользователю.
- `App\Notifications\ImageProcessingFailedNotification` - для отправки пуш уведомления со статусом `fail`.

*Tip*: Для отправки e-mail сообщений используйте `log` драйвер, он уже предустановлен в .env.example. Отправленные сообщения можно будет найти в `storage/logs/`

Frontend:

В файле `resources/js/components/App.vue`:

1) Вам нужно отправить файл на endpoint `/api/photos` в методе `onFile()` при помощи `resources/js/services/requestService.js`. После отправки необходимо установить статус (`processing`)

2) В методе `onAuth()` нужно подписаться на канал уведомлений пользователя. В зависимости от статуса вызывать методы `fail` и `success`. И, если изображения обработаны успешно, отображать их при помощи `addImage`.

### Проверка

Вам необходимо склонировать этот репозиторий, выполнить задание, запушить на bitbucket и прислать ссылку на репозиторий в личном кабинете.

__Форкать репозиторий запрещено!__

Проверяться задание будет по следующим критериям:

1) Тесты выполняются успешно:

    - test_running_job - 1 бал

    - test_handle_image - 2 балла

    - test_success_notification_sent - 2 балла

    - test_failed_notification_sent - 1 бал

2) Следующие пункты задания выполнены корректно:

    a) изображения добавляются на страницу без перезагрузки: 1 балл
    
    b) статус обработки изображения меняется на странице: 1 балл

    c) beanstalk настроен так, чтобы для работы приложения, достаточно было только скопировать .env.example в .env и запустить очередь: 1 балл

3) Код написан чисто и аккуратно в соответствии со стандартом [PSR-2](https://www.php-fig.org/psr/psr-2/), без комментариев в коде, без функций отладки: 1 балл

__изменять тесты запрещено!__

Чтобы проверить себя, вы можете запустить тесты командой:

```bash
docker-compose exec php ./vendor/bin/phpunit
```

### Miscellaneous

Заготовка для frontend части дана и находится в директории `resources/js/`.

Webpack запускается в сервисе `frontend` при старте контейнера, и если все настроено правильно, то дополнительных действий делать не требуется. Для того чтобы следить за компиляцией вебпака выполните:

```bash
docker-compose logs -f frontend
```

Необходимые библиотеки предустановлены (`laravel-echo`, `socket.io`, `axios`) и настроены в файле `resources/js/bootstrap.js`.

Websocket-сервер также установлен и настроен.

Полезные комманды:

```bash
docker-compose run --rm composer require ... # установка composer зависимостей
docker-compose exec frontend npm install ... # установка npm зависимостей
docker-compose logs -f websocket ... # лог веб-сокет сервереа
```
