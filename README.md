# Laravel Base Package

This base will help to create simple API (CRUD) for 1 specific entity, such as User

## Install

`composer require yaangvu/laravel-base`

## Initial

### Generate code
```shell
php artisan yaangvu:base Post <option>
```
Option:
- -S: generate code with default Swagger annotation
- -i: Auto inject Service in Controller methods

### Directory structure of generated code

```
├── app
│   ├── Domains
│   │   └── Post
│   │       ├── Controllers
│   │       │   └── PostController.php
│   │       ├── Models
│   │       │   └── Post.php
│   │       └── Services
│   │           └── PostService.php
```

### Route

```php
Route::base('/posts', \App\Domains\Post\Controllers\PostController::class);
```


## Usage

### Dynamic query parameters

#### Operators supported

```
$operators
        = [
            '__gt' => OperatorConstant::GT, // Greater than
            '__ge' => OperatorConstant::GE, // Greater than or equal
            '__lt' => OperatorConstant::LT, // Less than
            '__le' => OperatorConstant::LE, // Less than or equal
            '__~'  => OperatorConstant::LIKE // Like
        ];
```

#### To query, you can add more params with format:

`{param-name}{operator} = {value}`

#### Example:

1. `username = admin` ----> `username` equal `admin`
2. `name__~ = super`  ---->  `name` like `%super%`
3. `age__gt = 18`     ---->  `age` gather than `18`

#### Full request example

Request to query user with `username=admin` and `name LIKE %super%` and `age > 18`

```
curl --location --request GET 'http://localhost:8000/api/v1/users?username=admin&name__~=super&age__gt=18'
```

### Validate before Add an entity

Support full Laravel validation: [Validation](https://laravel.com/docs/master/validation)

```
class UserService extends BaseService
{
    public function storeRequestValidate(object $request, array $rules = []): bool|array
    {
        $rules = [
            'username' => 'required|max:255|unique:users',
        ];

        return parent::storeRequestValidate($request, $rules);
    }
}
```

### Validate before Update an entity

Support full Laravel validation: [Validation](https://laravel.com/docs/master/validation)

```
class UserService extends BaseService
{
    public function updateRequestValidate(int|string $id, object $request, array $rules = []): bool|array
    {
        $rules = [
            'username' => 'required|max:255|unique:users,id',
        ];
        
        return parent::updateRequestValidate($id, $request, $rules);
    }
}
```

### Service Observe

It supports these observe function:
1. `function postAdd()`
2. `function postUpdate()`
3. `function postDelete()`
4. `function postGet()`
5. `function postGetAll()`

### Cache data
If you want to cache data when `create` `update` `select`, implement `ShouldCache` interface
```php
class UserService extends BaseService implements \YaangVu\LaravelBase\Interfaces\ShouldCache
{}
```

### Event driven
If you apply Event driven, please consider use these:
```php
trait HasEvent
{
    private string|array $allSelectionEvents  = [];
    private string|array $selectionEvents     = [];
    private string|array $uuidSelectionEvents = [];
    private string|array $additionEvents      = [];
    private string|array $patchEvents         = [];
    private string|array $putEvents           = [];
    private string|array $idDeletionEvents    = [];
    private string|array $idsDeletionEvents   = [];
    private string|array $uuidDeletionEvents  = [];
    private string|array $uuidsDeletionEvents = [];
}
```

### Upload file

```
use YaangVu\LaravelBase\Helpers\LocalFileHelper;

$fileHelper = new LocalFileHelper();
    if ($request->video)
            $videoPath = $fileHelper->upload($request, 'video', 'university/video');
```
