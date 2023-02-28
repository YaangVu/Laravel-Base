# Laravel Base Package

This base will help to create simple API (CRUD) for 1 specific entity

## Install

```shell
composer require yaangvu/laravel-base
```

### For Laravel

Publish configuration file and Base Classes

```shell
php artisan vendor:publish --provider="YaangVu\LaravelBase\Providers\BaseServiceProvider"
```

### For lumen

```shell
cp vendor/yaangvu/laravel-base/src/config/laravel-base.php config/laravel-base.php
mkdir -p app/Base
cp vendor/yaangvu/laravel-base/src/Base/Publish/Controller.php app/Base/Controller.php
cp vendor/yaangvu/laravel-base/src/Base/Publish/Service.php app/Base/Service.php
```
## Generator Command
If you want to use Generator Command, Add the following class to the `providers` array in `config/app.php`:

```php
  YaangVu\LaravelBase\Provider\GeneratorServiceProvider::class,
```

If you want to manually load it only in non-production environments, instead you can add this to
your `AppServiceProvider` with the `register()` method:

```php
  public function register()
  {
      if ($this->app->isLocal()) {
          $this->app->register(\YaangVu\LaravelBase\Provider\GeneratorServiceProvider::class);
      }
      // ...
  }
```

## Initial API resource

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
class UserService extends BaseService implements \YaangVu\LaravelBase\Base\Contract\ShouldCache
{}
```

