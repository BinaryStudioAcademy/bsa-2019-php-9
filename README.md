# Binary Studio Academy 2019

## Домашнее задание #9

### Требования

Это задание направлено на работу очередью сообщений и уведомлениями в Laravel.

***

### Задание

Вам необходимо настроить очередь сообщений `beanstalkd` в Laravel.

Затем создать ресурс:

```
POST /api/photos
multipart/data
```

на который необходимо отправить файл с изображением.

Файл нужно сохранить в файловой системе сервера и записать информацию о нем в модель `App\Entites\Photo`:

| name           | type                        | Description                                                                                   |
|----------------|-----------------------------|-----------------------------------------------------------------------------------------------|
| id             | uint                        |                                                                                               |
| original_photo | varchar(255)                | path to original file on the server                                                           |
| photo_50_50    | varchar(255)                | image 50x50 px                                                                                |
| photo_100_100  | varchar(255)                | image 100x100 px                                                                              |
| photo_250_250  | varchar(255)                | image 250x250 px                                                                              |
| status         | enum(UPLOAD,PROCESS,FINISH) | UPLOAD - original image saved;<br /> PROCESS - processing image started;<br /> FINISH - image has processed |

При помощи очереди сообщений необходимо из загруженного файла сгенерировать 3 его копии с размерами: 50x50px, 100x100px, 250x250px; и сохранить пути к этим копиям в модели. 

В процессе изменения изображения необходимо менять статус обработки:

- UPLOAD - оригинальный файл сохранен на сервере
- PROCESS - перед началом обработки файла
- FINISH - все три копии успешно созданы

После успешного создания копий, необходимо отправить пуш уведомление клиенту с URL'ами этих изображений, и на странице должны создасться 3 изображения без перезагрузки страницы:

```html
<img src='storage/app/public/<file_name>_50_50.jpg' />
<img src='storage/app/public/<file_name>_100_100.jpg' />
<img src='storage/app/public/<file_name>_250_250.jpg' />
```

Также необходимо отправить email сообщение пользователю, который загружал изображение, с текстом:

```
Dear <user name>,

Photos have been successfuly uploaded and prcessed.
Here are links to the images:

<url to 50x50 photo>
<url to 100x100 photo>
<url to 250x250 photo>

Thanks!
```


