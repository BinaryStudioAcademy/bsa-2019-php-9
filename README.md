# Binary Studio Academy 2019

## Домашнее задание #9

### Требования

Это задание направлено на работу c очередью сообщений и уведомлениями в Laravel.

***

### Задание

Необходимо отправить на сервер изображение, при помощи очереди сообщений сгенерировать 3 копии этого файла с размерами 100х100px, 150х150px и 250х250px, отправить клиенту URLы этих изображений при помощи push уведомлений и вставить их в DOM без перезагрузки страницы. Также необходимо отправить e-mail с текстом:

```
Dear <user name>,

Photos have been successfuly uploaded and processed.
Here are links to the images:

<url to 50x50 photo>
<url to 100x100 photo>
<url to 250x250 photo>

Thanks!
```

Шаги выполнения:

Backend:

1) Запустите docker окружение
    
```bash
docker-compose up -d
cp .env.example .env
docker-compose exec composer install
docker-compose exec php php artisan key:generate
docker-compose exec php php artisan migrate
```

2) Настройте очередь сообщений `beanstalkd` в Laravel. В предоставленном Docker окружении beanstalk сервер уже установлен. Имейте ввиду, что внутри контейнера имя хоста соответствует имени сервиса в docker-compose (т.е. hostname = 'beanstalk').

3) Запустите очередь внутри контейнера `php`

```bash
docker-compose exec php php artisan ...
```

4) Создайте ресурс для загрузки изображений:

```
POST /api/photos
multipart/form-data
```

Используйте middleware "auth" для этого ресурса, чтобы пользователь аутентифицировался.

Изображения нужно [сохранять](https://laravel.com/docs/5.8/filesystem#file-uploads) в директорию `storage/app/public/images`.

5) Создайте миграцию для модели загрузки фото `App\Entites\Photo`:

| name           | type                        | Description                                                                                   |
|----------------|-----------------------------|-----------------------------------------------------------------------------------------------|
| id             | uint                        |                                                                                               |
| user_id        | uint                        | id of user who uploaded image                                                                 |
| original_photo | varchar(255)                | path to original file on the server                                                           |
| photo_100_100  | varchar(255)                | image 100x100 px                                                                              |
| photo_150_150  | varchar(255)                | image 150х150 px                                                                              |
| photo_250_250  | varchar(255)                | image 250x250 px                                                                              |
| status         | enum(UPLOADED,PROCESSING,SUCCESS,FAIL) | status of processing photo |

UPLOADED - оригинальное изображение загружено; \
PROCESSING - начало обработки изображения; \
SUCCESS - изображение обработано успешно; \
FAIL - изображение обработано с ошибками

6) Создайте job'у для обработки изображения `App\Jobs\ResizeJob`

7) Создайте уведомления:
- `App\Notifications\ImageProcessingNotification` - для отправки пуш уведомления со статусом начала обработки (`processing`)
- `App\Notifications\ImageProcessedNotification` - для отправки пуш уведомления со статусом обработки (`success` или `fail`) и изображениями. А также отправкой e-mail сообщения пользователю.

Frontend:

В файле `resources/js/components/App.vue`:

1) Вам нужно отправить файл на endpoint `/api/photos` в методе `onFile()` при помощи `resources/js/services/requestService.js`

2) В методе `mounted()` нужно подписаться на канал уведомлений и изменять статус обработки и загрузки изображений при помощи данных методов (`addImage`, `success`, `fail`, `processing`).

# Проверка

Вам необходимо склонировать этот репозиторий, выполнить задание, запушить на bitbucket и прислать ссылку на репозиторий в личном кабинете.

__Форкать репозиторий запрещено!__

Проверяться задание будет по следующим критериям:

1) Тесты проходят: 4 балла
2) Все пункты задания выполнены корректно:

    a) изображения генерируются - 1 балл \
    b) статус загрузки меняется на фронтенде без перезагрузки страницы: 1 балл\
    c) изображения добавляются на странице без перезагрузки: 1 балл\
    d) e-mail сообщение отправляется пользователю: 1 балл \
    e) информация об изображении сохраняется в базе данных: 1 балл
3) Код написан чисто и аккуратно в соответствии со стандартом [PSR-2](https://www.php-fig.org/psr/psr-2/), без комментариев в коде, без функций отладки: 1 балл

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

Вы можете свободно добавлять или удалять код, если это необходимо для выполнения задания.

Websocket-сервер также установлен и настроен.

Полезные комманды:

```bash
docker-compose run --rm composer require ... # установка composer зависимостей
docker-compose exec frontend npm install ... # установка npm зависимостей
```
