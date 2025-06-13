## Класс \Raketa\BackendTestTask\Core\Response\JsonResponse

Будем считать, что класс полностью реализован и работает в рамках этого задания. 

## Неправильно разбивать получение и добавление сущности на отдельные контроллеры

Непонятно, где и как тут роутер реализован. Но нужно все реализовать в одном контроллере, на примере CartController, который будет содержать методы get, view, create с нужным контекстом. 

- get - Получение списков;
- view - Получение одной сущности;
- create - Добавление.

## Метод get в контроллерах

Этот метод говорит о том, что мы запрашиваем данные GET типом. Поэтому применение ошибочно.
```php
$request->getBody()->getContents();
```

Такой способ получения тела запроса актуален только для методов с типом POST, PUT, PATH.

В данном случае нам нужно получить params из uri в качестве необходимых фильтров для запроса.

Где это видно:
```php
\Raketa\BackendTestTask\Controller\AddToCartController::get
\Raketa\BackendTestTask\Controller\GetProductsController::get
```

## В контроллерах не должно быть сложной логики

Где это видно:
```php
public function get(RequestInterface $request): ResponseInterface
    {
        $rawRequest = json_decode($request->getBody()->getContents(), true);
        $product = $this->productRepository->getByUuid($rawRequest['productUuid']);

        $cart = $this->cartManager->getCart();
        $cart->addItem(new CartItem(
            Uuid::uuid4()->toString(),
            $product->getUuid(),
            $product->getPrice(),
            $rawRequest['quantity'],
        ));

        $response = new JsonResponse();
        $response->getBody()->write(
            json_encode(
                [
                    'status' => 'success',
                    'cart' => $this->cartView->toArray($cart)
                ],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
        );

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(200);
    }
```

Нужно реализовать отдельный сервис CartService (как пример), который будет содержать всю необходимую механику по управлению, добавлению, удалению сущности и работа с репозиторием.

И такого в контроллерах быть не должно:

```php
public function __construct(
        private ProductRepository $productRepository,
        private CartView $cartView,
        private CartManager $cartManager,
    ) {
    }
```

Вызываем для конкретной сущности единый CartService, где есть вся механика с нужными зависимостями.

```php
public function __construct(
        private CartService $сartService,
    ) {
    }
```

## Реализовать BackendController

   1. От контроллера должны наследоваться все контроллеры:
      - \Raketa\BackendTestTask\Controller\CartController
      - \Raketa\BackendTestTask\Controller\ProductController
      - И т.д.      
   
   2. Добавить метод jsonResponse. В нем должна быть заложена механика возвращаемых данных \Raketa\BackendTestTask\Core\Response\JsonResponse.   
   
   3. В контроллере нужно реализовать метод fromRequest обработки входящих данных, который имеет аргумент RequestInterface $request. Должен возвращать массив.   
   
   4. В контроллере нужно реализовать метод fromParams обработки входящих get данных, который имеет аргумент RequestInterface $request. Должен возвращать массив.   
   
   5. Реализовать методы, которые принимают данные:
   
      - response
      - responseError
      - И т.д.
   
      Каждый из этих методов вызывает метод jsonResponse и возвращает объект \Raketa\BackendTestTask\Core\Response\JsonResponse

   6. Реализовать метод responseError, в котором заранее заложить коды ответов для 401, 402, 403, 404 и др. 
      Аргументы метода string $code, string $message.  

      Вызов метода должен осуществляться в методах, перечисленные в пункте 1, в случае, когда было перехвачено исключение с кодом 401, 402, 403, 404 и др.

   ### Что получим в итоге в контроллере CartController
   
   ```php
         class CartController extends BackendController
         {
                public function __construct(
                    private readonly CartView $cartView,
                ) {
                }
            
                public function view(RequestInterface $request): ResponseInterface
                {
                    try {
                        return $this->response(
                            data: $this->cartView->get(
                                data: $this->fromParams($request),
                            ),
                        );
                    } catch (\Throwable $e) {
                        return $this->responseError(
                            message: $e->getMessage(),
                            code: $e->getCode(),
                        );
                    }
                }
            
                public function create(RequestInterface $request): ResponseInterface
                {
                    try {
                        return $this->response(
                            data: $this->cartView->create(
                                data: $this->fromRequest($request),
                            ),
                        );
                    } catch (\Throwable $e) {
                        return $this->responseError(
                            message: $e->getMessage(),
                            code: $e->getCode(),
                        );
                    }
                }
         }
   ```

## Класс \Raketa\BackendTestTask\Controller\JsonResponse

Нужно вынести из контроллера в каталог Core/Response

## Каталог src/Repository/Entity

Нужно вынести на уровень выше. Примет вид:
```
src/Entity
```

## Класс \Raketa\BackendTestTask\Infrastructure\ConnectorException

Нужно вынести в отдельный каталог с исключениями. В этом месте требуется наличие договоренности в команде разработки, где будет находиться каталог Exceptions для такой категории исключений.

Например: 
```
src/Exceptions
```

Или:
```
src/Infrastructure/Exceptions
```


## Класс \Raketa\BackendTestTask\Infrastructure\Connector

1. Неверный тип аргумента и не указан тип возвращаемых данных.

```php
public function get(Cart $key)
    {
        try {
            return unserialize($this->redis->get($key));
        } catch (RedisException $e) {
            throw new ConnectorException('Connector error', $e->getCode(), $e);
        }
    }
```

Аргумент должен быть с типом string. Должен вернуть объект или null. Но лучше сделать реализацию конкретного интерфейса. Будет удобнее расширять функционал.

2. Не указан тип возвращаемых данных.

```php
public function set(string $key, Cart $value)
    {
        try {
            $this->redis->setex($key, 24 * 60 * 60, serialize($value));
        } catch (RedisException $e) {
            throw new ConnectorException('Connector error', $e->getCode(), $e);
        }
    }
```

В конкретном случае должен быть void.

3. Не указан тип аргумента $key.
```php
public function has($key): bool
    {
        return $this->redis->exists($key);
    }
```

Здесь должен быть тип string.

4. В конструкторе не указан тип аргумента и ошибочно возвращать что-либо в нем. 

```php
public function __construct($redis)
   {
   return $this->redis = $redis;
   }
```


## Класс \Raketa\BackendTestTask\Infrastructure\ConnectorFacade

1. В проекте отсутствует реализация работы с переменными окружения.

      Необходимо добавить в проект dot env и перенести туда дефолтные реквизиты подключения. Потому что хранить в явном виде значение порта, логина и пароля не секьюрно.
      
      ```php
          public string $host;
          public int $port = 6379;
          public ?string $password = null;
          public ?int $dbindex = null;
      ```

2. Переменные должны писаться в стиле camelCase.

      ```php
      public ?int $dbIndex = null;
      ```

3. В конструкторе не указан тип аргументов. 

```php
public function __construct($host, $port, $password, $dbindex)
    {
        $this->host = $host;
        $this->port = $port;
        $this->password = $password;
        $this->dbindex = $dbindex;
    }
```

Переменные должны писаться в стиле camelCase.

4. Метод \Raketa\BackendTestTask\Infrastructure\ConnectorFacade::build

Какая-то странная реализация подключения к редису. Тут нет статических переменных, чтобы имело место такая логика. Очень похоже, что кто-то экспериментировал и добавил явный баг.


## Класс \Raketa\BackendTestTask\Repository\ProductRepository

1. Нельзя передавать в sql запрос не экранированные значения.

```php
$row = $this->connection->fetchOne(
            "SELECT * FROM products WHERE uuid = " . $uuid,
        );
```

## Класс \Raketa\BackendTestTask\Manager\CartManager

1. Непонятно от чего зависит dbIndex. Вроде логично брать из настроек. Но в текущей реализации какой-то бардак.

```php
public function __construct(string $host, int $port, ?string $password)
    {
        parent::__construct(
            host: $host,
            port: $port,
            password: $password,
            dbIndex: 1,
        );
        parent::build();
    }
```

## Класс \Raketa\BackendTestTask\Domain\Cart

1. Свойство Readonly должно следовать после public, protected, private.

```php
public function __construct(
        readonly private string $uuid,
        readonly private Customer $customer,
        readonly private string $paymentMethod,
        private array $items,
    ) {
    }
```

## Миграция [schema.init.sql](migrations/schema.init.sql)

1. UUID имеет фиксированную длину 36 символов. 
```sql
uuid varchar(255) not null comment 'UUID товара'
```

2. Категории товаров лучше делать отдельной таблицей и связывать по внешнему ключу.
```sql
category varchar(255) not null comment 'Категория товара'
```

3. Цену надо хранить в копейках целым числом. Таким образом можно избежать коллизий с плавающей точкой.
```sql
price float not null comment 'Цена'
```

4. Индекс только по булеву значению не эффективен. Оптимизатор попросту его не использует. Лучше использовать составной индекс из категории и булева значения активности, так как в текущей реализации применяется именно такая фильтрация товаров.
```sql
create index is_active_idx on products (is_active);
```





