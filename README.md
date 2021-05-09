# Laravel Base Package

This base will help to create simple API (CRUD) for 1 specific entity, such as User

## Install

`composer require yaangvu/laravel-base`

## Initial

### Directory structure

```
.
├── src
│   ├── Constants
│   │   ├── DataCastConstant.php
│   │   └── OperatorConstant.php
│   ├── Controllers
│   │   └── BaseController.php
│   ├── Exceptions
│   │   ├── BadRequestException.php
│   │   ├── BaseException.php
│   │   ├── ForbiddenException.php
│   │   ├── GatewayTimeOutException.php
│   │   ├── Handler.php
│   │   ├── NotFoundException.php
│   │   ├── SystemException.php
│   │   └── UnauthorizedException.php
│   ├── Helpers
│   │   ├── FileHelper.php
│   │   ├── LocalFileHelper.php
│   │   ├── QueryHelper.php
│   │   └── RouterHelper.php
│   ├── LaravelBaseServiceProvider.php
│   ├── Services
│   │   ├── BaseServiceInterface.php
│   │   └── impl
│   └── config
│       └── laravel-base.php
```

### Route

```
use YaangVu\LaravelBase\Helpers\RouterHelper;
RouterHelper::resource($router, '/users', 'UserController');
```

### Service

```
use App\Models\User;
use YaangVu\LaravelBase\Services\impl\BaseService;

class UserService extends BaseService
{

    function createModel(): void
    {
        $this->model = new User();
    }
}
```

### Controller

```
use App\Services\UserService;
use YaangVu\LaravelBase\Controllers\BaseController;

class UserController extends BaseController
{
    public function __construct()
    {
        $this->service = new UserService();
        parent::__construct();
    }
}
```

### Model

To insert or update an entity, you must define columns can give data

```
class User extends Model
{
    protected $fillable
        = [
            'username', 'email', 'password', 'first_name', 'last_name'
        ];
}
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

1. `preAdd`
2. `postAdd`
3. `preUpdate`
4. `postUpdate`
5. `preDelete`
6. `postDelete`
7. `preGet`
8. `postGet`
9. `preGetAll`
10. `postGetAll`

### Upload file

```
use YaangVu\LaravelBase\Helpers\LocalFileHelper;

$fileHelper = new LocalFileHelper();
    if ($request->video)
            $videoPath = $fileHelper->upload($request, 'video', 'university/video');
```
