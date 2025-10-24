# posts-api
Простое REST API для работы с публикациями
# Установка
```
git clone https://github.com/razielin/posts-api
cd posts-api
```
Установка зависимостей
```
composer install
npm install
```
Создание локального .env файла
```shell
php -r "file_exists('.env') || copy('.env.example', '.env');"
```
Открываем `.env` файл в любом редакторе и заполняем сетевой
адрес и логин/пароль к MySQL базе
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=posts_api
DB_USERNAME=LOGIN    # вставить свое значение
DB_PASSWORD=PASSWORD # вставить свое значение
```
Генерируем APP_KEY
```shell
php artisan key:generate
```
Выполняем миграции
```shell
php artisan migrate
```
Запускаем dev сервер
```shell
composer run dev
```
Теперь api должен работать:

http://localhost:8000/api/posts

# Запуск тестов
```shell
php artisan test
```
Тесты хранятся в папке `tests/Unit`

# Документация

### Получение списка всех постов (GET /api/posts)
GET http://localhost:8000/api/posts

Пример ответа:
```json
{
    "success": true,
    "data": [
        {
            "id": 3,
            "title": "a test post 2",
            "slug": "a-test-post-2",
            "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
            "is_published": false,
            "published_at": null,
            "created_at": "2025-10-22T13:17:28.000000Z",
            "updated_at": "2025-10-22T13:17:28.000000Z"
        },
        {
            "id": 4,
            "title": "a test post 3",
            "slug": "a-test-post-3",
            "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
            "is_published": true,
            "published_at": "2025-10-22T13:19:41.000000Z",
            "created_at": "2025-10-22T13:19:41.000000Z",
            "updated_at": "2025-10-22T13:19:41.000000Z"
        }
    ]
}
```

### Получение одного поста (GET /api/posts/{id})
GET http://localhost:8000/api/posts/1

Пример ответа:
```json
{
    "success": true,
    "data": {
        "id": 5,
        "title": "a test post 4",
        "slug": "a-test-post-4",
        "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
        "is_published": false,
        "published_at": null,
        "created_at": "2025-10-22T13:20:23.000000Z",
        "updated_at": "2025-10-22T13:20:23.000000Z"
    }
}
```
### ВАЖНО!
Для работы всех последующих апи, нужно вставить заголовки
`Accept` и `Content-Type`:
```
Accept:application/json
Content-Type:application/json
```

### Создание нового поста (POST /api/posts)
POST http://localhost:8000/api/posts

Пример body запроса:
```json
{
    "title": "a test post",
    "post_content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
    "is_published": true
}
```
Пример ответа:
```json
{
    "success": true,
    "data": {
        "id": 7,
        "title": "a test post",
        "slug": "a-test-post",
        "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
        "is_published": true,
        "published_at": "2025-10-22T19:44:18.000000Z",
        "created_at": "2025-10-22T19:44:18.000000Z",
        "updated_at": "2025-10-22T19:44:18.000000Z"
    }
}
```

При дублировании поля `slug` (при одинаковом `title`)
, к `slug` добавляется суффикс -1, -2 и т.д.

Пример ответа:
```json
{
    "success": true,
    "data": {
        "id": 7,
        "title": "a test post",
        "slug": "a-test-post-1",
        "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
        "is_published": true,
        "published_at": "2025-10-22T19:44:18.000000Z",
        "created_at": "2025-10-22T19:44:18.000000Z",
        "updated_at": "2025-10-22T19:44:18.000000Z"
    }
}
```
```json
{
    "success": true,
    "data": {
        "id": 8,
        "title": "a test post",
        "slug": "a-test-post-2",
        "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
        "is_published": true,
        "published_at": "2025-10-22T19:44:18.000000Z",
        "created_at": "2025-10-22T19:44:18.000000Z",
        "updated_at": "2025-10-22T19:44:18.000000Z"
    }
}
```

### Редактирование поста (PUT /api/posts/{id})
PUT http://localhost:8000/api/posts/1

Пример body запроса:
```json
{
    "title": "a test post",
    "post_content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
    "is_published": true
}
```
Каждое поле опционально, достаточно, чтобы было хотя бы одно поле:
```json
{
    "is_published": true
}
```
Пример ответа:
```json
{
    "success": true,
    "data": {
        "id": 3,
        "title": "a test post",
        "slug": "a-test-post",
        "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
        "is_published": true,
        "published_at": "2025-10-22T19:47:13.000000Z",
        "created_at": "2025-10-22T13:17:28.000000Z",
        "updated_at": "2025-10-22T19:47:13.000000Z"
    }
}
```
При дублировании поля `slug` (при одинаковом `title`)
, к `slug` добавляется суффикс -1, -2 и т.д. (как и при добавлении)

### Удаление поста (DELETE /api/posts/{id})
DELETE http://localhost:8000/api/posts/1

Пример ответа:
```json
{
    "success": true,
    "data": {
        "message": "Post deleted successfully"
    }
}
```
Ответ при попытки удалить пост несуществующим id:
```json
{
    "success": false,
    "message": "Entity #1 not found"
}
```
