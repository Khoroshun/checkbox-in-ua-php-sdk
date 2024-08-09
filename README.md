![GitHub CI](https://github.com/khoroshun/checkbox-in-ua-php-sdk/workflows/CI/badge.svg)
[![Packagist](https://img.shields.io/badge/package-khoroshun/checkbox--in--ua--php--sdk-blue.svg?style=flat-square)](https://github.com/khoroshun/checkbox-in-ua-php-sdk)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/khoroshun/checkbox-in-ua-php-sdk.svg?style=flat-square)](https://github.com/khoroshun/checkbox-in-ua-php-sdk)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![PHP >=8.0](https://img.shields.io/badge/php-%3E=_8.0-orange.svg?style=flat-square)](https://github.com/khoroshun/checkbox-in-ua-php-sdk)
[![Total Downloads](https://poser.pugx.org/khoroshun/checkbox-in-ua-php-sdk/downloads)](https://github.com/khoroshun/checkbox-in-ua-php-sdk)

# checkbox-in-ua-php-sdk
### PHP SDK для работы с Checkbox (Украина)

##### Примечание:

> В данном sdk реализованы только функции онлайн режима (оффлайн не реализован)

#### Официальная документация:

<https://api.checkbox.in.ua/api/redoc>

<https://api.checkbox.in.ua/api/docs>

<https://docs.google.com/document/d/1Zhkc4OljKjea_235YafVvZunkWSp6TCAKeckhgl8t2w/edit>

#### Установка:
```bash
composer require khoroshun/checkbox-in-ua-php-sdk
```

#### Настройка:

```php
require_once 'vendor/autoload.php';
```

##### Настройка конфига:

>адрес продакшен сервера http://api.checkbox.in.ua<br/>
>адрес тестового сервера http://dev-api.checkbox.in.ua<br/>
>текущая версия API - v1

```php
$config = new \khoroshun\Checkbox\Config([
    \khoroshun\Checkbox\Config::API_URL => 'https://dev-api.checkbox.in.ua/api/v1',
    \khoroshun\Checkbox\Config::LOGIN => 'логин кассира',
    \khoroshun\Checkbox\Config::PASSWORD => 'пароль кассира', //or
    \khoroshun\Checkbox\Config::PINCODE => 02301230440,
    \khoroshun\Checkbox\Config::LICENSE_KEY => 'ключ лицензии кассы'
]);
```

##### Логин кассира:

```php
$api = new \khoroshun\Checkbox\CheckboxJsonApi($config);
$api->signInCashier();
```
##### Логаут кассира:
```php
$api->signOutCashier();
```
##### Ошибки (Exceptions):
```php
\khoroshun\Checkbox\Errors\InvalidCredentials - не верные данные логина или пароля
```
```php
\khoroshun\Checkbox\Errors\EmptyResponse - пустой ответ
```
```php
\khoroshun\Checkbox\Errors\Validation - ошибка валидации (есть детальные. данные $err->getDetail())
```
```php
\khoroshun\Checkbox\Errors\NoActiveShift - нет активной смены
```
```php
\khoroshun\Checkbox\Errors\AlreadyOpenedShift - смена уже открыта
```
```php
\Exception  - стандартная ошибка
```

#### Основные методы:

##### profile (касир):
```php
$api->getCashierProfile() : \khoroshun\Checkbox\Models\Cashier\Cashier // возвращает профиль кассира
```
##### shifts (смены):
```php
$api->getCashierShift() : \khoroshun\Checkbox\Models\Shifts\Shift // возвращает текущую смену кассира
```
```php
$api->getShift('ид смены') : \khoroshun\Checkbox\Models\Shifts\Shift // возвращает смену по ид
```
```php
$api->getShifts() : \khoroshun\Checkbox\Models\Shifts\Shifts // возвращает смены
```
или
```php
$api->getShifts(
    new \khoroshun\Checkbox\Models\Shifts\ShiftsQueryParams(
        [
            \khoroshun\Checkbox\Models\Shifts\ShiftsQueryParams::STATUS_CLOSED,
            \khoroshun\Checkbox\Models\Shifts\ShiftsQueryParams::STATUS_OPENED
        ], // статусы смен
        false, // desc - сортировка (false or true)
        2, // limit
        0 // offset
    )
): \khoroshun\Checkbox\Models\Shifts\Shifts // возвращает смены с учетом фильтра
```
```php
$api->createShift() : \khoroshun\Checkbox\Models\Shifts\CreateShift // создает смену
```
```php
$api->closeShift() : \khoroshun\Checkbox\Models\Shifts\CloseShift // закрывает смену
```
##### cash registers (пРРО):
```php
$api->getCashRegisters() : \khoroshun\Checkbox\Models\CashRegisters\CashRegisters // возвращает кассовые регистраторы
```
или
```php
$api->getCashRegisters(
    new \khoroshun\Checkbox\Models\CashRegisters\CashRegistersQueryParams(
        true, // inUse - используется или нет (true or false)
        3, // limit
        0 // offset
    )
) : \khoroshun\Checkbox\Models\CashRegisters\CashRegisters // возвращает кассовые регистраторы по фильтру
```
```php
$api->getCashRegister('ид кассы') : \khoroshun\Checkbox\Models\CashRegisters\CashRegister // возвращает кассу по айди
```
```php
$api->getCashRegisterInfo() : \khoroshun\Checkbox\Models\CashRegisters\CashRegisterInfo // возвращает информацию текущей кассы
```
##### taxes (налоги):
```php
$api->getAllTaxes() : \khoroshun\Checkbox\Models\Receipts\Taxes\GoodTaxes // возвращает все налоги
```
##### transactions (транзакции):
```php
$api->getTransactions(
    new \khoroshun\Checkbox\Models\Transactions\TransactionsQueryParams(
        [
            \khoroshun\Checkbox\Models\Transactions\TransactionsQueryParams::STATUS_CREATED,
            \khoroshun\Checkbox\Models\Transactions\TransactionsQueryParams::STATUS_DONE,
            \khoroshun\Checkbox\Models\Transactions\TransactionsQueryParams::STATUS_SIGNED
        ], // статусы транзакции
        [
            \khoroshun\Checkbox\Models\Transactions\TransactionsQueryParams::TYPE_RECEIPT,
            \khoroshun\Checkbox\Models\Transactions\TransactionsQueryParams::TYPE_SHIFT_OPEN,
            \khoroshun\Checkbox\Models\Transactions\TransactionsQueryParams::TYPE_Z_REPORT
        ], // типы транзакций
        2, // limit
        0 // offset
    )
) : \khoroshun\Checkbox\Models\Transactions\Transactions // возвращает транзакции по фильтру
```
```php
$api->getTransaction('ид транзакции') : \khoroshun\Checkbox\Models\Transactions\Transaction // возвращает транзакцию по айди
```
```php
$api->updateTransaction(
    'ид транзакции',
    base64_encode('request_signature')
) : \khoroshun\Checkbox\Models\Transactions\Transaction // меняет request_signature у транзакции, работает только если у транзакции статус PENDING
```
##### reports (отчеты):
```php
$api->createXReport() : \khoroshun\Checkbox\Models\Shifts\ZReport // создает х отчет
```
```php
$api->getReport('ид отчета') : \khoroshun\Checkbox\Models\Shifts\ZReport // возвращает данные отчета по айди
```
```php
$api->getReportText('ид отчета') : string // возвращает данные отчета по айди в виде текста
```
или
```php
$api->getReportText('ид отчета', 60) : string // возвращает данные отчета по айди в виде текста, с указанием ширины текста
```
```php
$api->getPeriodicalReport(
    new \khoroshun\Checkbox\Models\Reports\PeriodicalReportQueryParams(
        '2020-10-27 00:00:00', // дата с
        '2020-11-04 13:15:00', // дата по
        60 // ширина текста
    )
) : string // возвращает данные отчета за период по фильру
```
```php
$api->getReports(
    new \khoroshun\Checkbox\Models\Reports\ReportsQueryParams(
        '2020-10-27 00:00:00', // дата с
        '2020-11-04 13:15:00', // дата по
        [], // массив ид смен
        false, // is_z_report (true or false)
        true, // desc - сортировка (false or true)
        3, // limit
        0 // offset
    )
) : \khoroshun\Checkbox\Models\Reports\Reports // возвращает отчеты по фильтру
```
##### receipts (чеки):
```php
$api->getReceipts() : \khoroshun\Checkbox\Models\Receipts\Receipts // возвращает чеки
```
```php
$api->getReceipts(
    new \khoroshun\Checkbox\Models\Receipts\ReceiptsQueryParams(
        '', // fiscal code
        '', // serial
        false, // desc - сортировка (false or true)
        2, // limit
        0 // offset
    )
) : \khoroshun\Checkbox\Models\Receipts\Receipts // возвращает чеки по фильтру
```
```php
$api->getReceipt('ид чека') : \khoroshun\Checkbox\Models\Receipts\Receipt // возвращает чек по айди
```
```php
$api->getReceiptPdf('ид чека') : pdf // возвращает чек по айди в виде пдф
```
```php
$api->getReceiptHtml('ид чека') : string // возвращает чек по айди в виде html
```
```php
$api->getReceiptText('ид чека') : string // возвращает чек по айди в виде текста
```
```php
$api->getReceiptQrCodeImage('ид чека') : string // возвращает чек по айди в виде qr-кода
```
или
```php
// пример с отображением qr-кода
$rawImageContent = $api->getReceiptQrCodeImage('ид чека');
echo '<img src="data:image/png;base64,' . base64_encode($rawImageContent) . '"/>';
```
###### чек продажи:
```php
$receipt = new \khoroshun\Checkbox\Models\Receipts\SellReceipt(
    'Вася Пупкин', // кассир
    'Отдел продаж', // отдел
    new \khoroshun\Checkbox\Models\Receipts\Goods\Goods(
        [
            new \khoroshun\Checkbox\Models\Receipts\Goods\GoodItemModel( // товар 1
                new \khoroshun\Checkbox\Models\Receipts\Goods\GoodModel(
                    'vm-123', // good_id
                    50 * 100, // 50 грн
                    'Биовак' // название товара
                ),
                1 * 1000 // кол-во товара  1 шт
            ),
            new \khoroshun\Checkbox\Models\Receipts\Goods\GoodItemModel( // товар 2
                new \khoroshun\Checkbox\Models\Receipts\Goods\GoodModel(
                    'vm-124', // good_id
                    20 * 100, // 20 грн
                    'Биовак 2' // название товара
                ),
                2 * 1000 // кол-во товара 2 шт
            )
        ]
    ),
    'admin@gmail.com', // кому отправлять чек по почте
    new \khoroshun\Checkbox\Models\Receipts\Payments\Payments([
        new \khoroshun\Checkbox\Models\Receipts\Payments\CardPaymentPayload( // безналичная оплата
            40 * 100 // 40 грн
        ),
        new \khoroshun\Checkbox\Models\Receipts\Payments\CashPaymentPayload( // наличная оплата
            50 * 100 // 50 грн
        )
    ])
);

$api->createSellReceipt($receipt): \khoroshun\Checkbox\Models\Receipts\Receipt; // выполняем оплату
```
более сложная оплата:
```php
$allTaxes = $api->getAllTaxes(); // получили все налоги
$tax = $allTaxes->getTaxByLabel('Акцизний збір'); // получили один налог по лейбл
$goodTaxes = $allTaxes->getTaxesByLabel('ПДВ'); // получили массив налогов по лейбл
$taxCodes = [];

// подготавливаем массив кодов налогов
foreach ($goodTaxes->results as $goodTax) {
    $taxCodes[] = $goodTax->code;
}

$receipt = new \khoroshun\Checkbox\Models\Receipts\SellReceipt(
    'Вася Пупкин', // имя кассира
    'Отдел продаж', // отдел
    new \khoroshun\Checkbox\Models\Receipts\Goods\Goods( // товары
        [
            new \khoroshun\Checkbox\Models\Receipts\Goods\GoodItemModel(
                new \khoroshun\Checkbox\Models\Receipts\Goods\GoodModel(
                    'vm-123', // good_id айди товара
                    5000, // 50 грн  цена 100 = 1 грн
                    'Биовак', // название
                    '5р47ле78675е3', // баркод
                    'хидер', // хидер
                    'футер', // футер
                    '', // ktzed
                    $goodTaxes // налоги товара
                ),
                1000, // кол-во 1000 = 1 шт
                new \khoroshun\Checkbox\Models\Receipts\Discounts\Discounts( // скидки или надбавки
                    [
                        new \khoroshun\Checkbox\Models\Receipts\Discounts\DiscountModel(
                            \khoroshun\Checkbox\Models\Receipts\Discounts\DiscountModel::TYPE_DISCOUNT, // скидка или надбавка
                            \khoroshun\Checkbox\Models\Receipts\Discounts\DiscountModel::MODE_VALUE, // по значению или по проценту
                            100, // 1 грн  сумма скидки/надбавки  100 = 1 грн
                            0, // сумма (не используется в данном sdk)
                            $tax->code, // код налога (подготовили выше)
                            $taxCodes, // массив кодов налога (подготовили выше)
                            'one good discount' // название
                        )
                    ]
                ),
                $allTaxes->getTaxesByLabel('Акцизний збір'), // налоги товара
                false, // возврат товара (false or true)
                0, // сумма (не используется в данном sdk)
                '' // айди товара (только если вы загружали список товарв (не используется в данном sdk))
            )
        ]
    ),
    'admin@gmail.com', // кому отправлять чек по почте
    new \khoroshun\Checkbox\Models\Receipts\Payments\Payments([ // оплаты
        new \khoroshun\Checkbox\Models\Receipts\Payments\CardPaymentPayload( // безналичная оплата
            400, // сумма оплаты 400 = 4 грн
            'beznalichka', // текст оплаты
            0, // code - не знаю для чего (видимо пин код карты)
            '0000 0000 0000 0000' // номер карты
        ),
        new \khoroshun\Checkbox\Models\Receipts\Payments\CashPaymentPayload( // наличная оплата
            4300, // сумма оплаты 4300 = 43 грн
            'nalichka' // текст оплаты
        )
    ]),
    new \khoroshun\Checkbox\Models\Receipts\Discounts\Discounts( // скидки/надбавки на весь чек
        [
            new \khoroshun\Checkbox\Models\Receipts\Discounts\DiscountModel(
                \khoroshun\Checkbox\Models\Receipts\Discounts\DiscountModel::TYPE_DISCOUNT, // скидка или надбавка
                \khoroshun\Checkbox\Models\Receipts\Discounts\DiscountModel::MODE_VALUE, // по значению или по проценту
                200, // 2 грн  сумма скидки/надбавки  200 = 2 грн
                0, // сумма (не используется в данном sdk)
                $tax->code, // код налога (подготовили выше)
                $taxCodes, // массив кодов налога (подготовили выше)
                'total discount' // название
            )
        ]
    ),
    'check header', // чек хидер
    'check footer', // чек футер
    '45435h543twrege' // баркод
);

$saleReceiptResult = $api->createSellReceipt($receipt): \khoroshun\Checkbox\Models\Receipts\Receipt; // выполняем оплату
```
еще пример
```php
$allTaxes = $api->getAllTaxes();
$tax = $allTaxes->getTaxByLabel('Акцизний збір');
$goodTaxes = $allTaxes->getTaxesByLabel('ПДВ');
$taxCodes = [];

foreach ($goodTaxes->results as $goodTax) {
    $taxCodes[] = $goodTax->code;
}

$receipt = new \khoroshun\Checkbox\Models\Receipts\SellReceipt(
    'Вася Пупкин',
    'Отдел продаж',
    new \khoroshun\Checkbox\Models\Receipts\Goods\Goods(
        [
            new \khoroshun\Checkbox\Models\Receipts\Goods\GoodItemModel(
                new \khoroshun\Checkbox\Models\Receipts\Goods\GoodModel(
                    'vm-123', // good_id
                    5000, // 50 грн
                    'Биовак',
                    '',
                    '',
                    '',
                    '',
                    $goodTaxes
                ),
                1000,
                new \khoroshun\Checkbox\Models\Receipts\Discounts\Discounts(
                    [
                        new \khoroshun\Checkbox\Models\Receipts\Discounts\DiscountModel(
                            \khoroshun\Checkbox\Models\Receipts\Discounts\DiscountModel::TYPE_DISCOUNT,
                            \khoroshun\Checkbox\Models\Receipts\Discounts\DiscountModel::MODE_VALUE,
                            100, // 1 грн
                            0,
                            $tax->code,
                            $taxCodes,
                            'моя скидка'
                        )
                    ]
                ),
                $allTaxes->getTaxesByLabel('Акцизний збір'),
                false,
                0,
                ''
            ),
            new \khoroshun\Checkbox\Models\Receipts\Goods\GoodItemModel(
                new \khoroshun\Checkbox\Models\Receipts\Goods\GoodModel(
                    'vm-124', // good_id
                    2000, // 20 грн
                    'Биовак 2',
                    '',
                    '',
                    '',
                    '',
                    $goodTaxes
                ),
                2000, // 2 шт
                new \khoroshun\Checkbox\Models\Receipts\Discounts\Discounts(
                    [
                        new \khoroshun\Checkbox\Models\Receipts\Discounts\DiscountModel(
                            \khoroshun\Checkbox\Models\Receipts\Discounts\DiscountModel::TYPE_EXTRA_CHARGE,
                            \khoroshun\Checkbox\Models\Receipts\Discounts\DiscountModel::MODE_VALUE,
                            200, // 2 грн
                            0,
                            $tax->code,
                            $taxCodes,
                            'моя надбавка'
                        )
                    ]
                ),
                $allTaxes->getTaxesByLabel('Акцизний збір'),
                false,
                0,
                ''
            )
        ]
    ),
    'admin@gmail.com',
    new \khoroshun\Checkbox\Models\Receipts\Payments\Payments([
        new \khoroshun\Checkbox\Models\Receipts\Payments\CardPaymentPayload(
            4700
        ),
        new \khoroshun\Checkbox\Models\Receipts\Payments\CashPaymentPayload(
            4700
        )
    ]),
    new \khoroshun\Checkbox\Models\Receipts\Discounts\Discounts(
        [
            new \khoroshun\Checkbox\Models\Receipts\Discounts\DiscountModel(
                \khoroshun\Checkbox\Models\Receipts\Discounts\DiscountModel::TYPE_EXTRA_CHARGE,
                \khoroshun\Checkbox\Models\Receipts\Discounts\DiscountModel::MODE_VALUE,
                200, // 2 грн
                0,
                $tax->code,
                $taxCodes,
                'общая надбавка'
            )
        ]
    )
);

$api->createSellReceipt($receipt): \khoroshun\Checkbox\Models\Receipts\Receipt;
```
```php
$api->createServiceReceipt(
    new \khoroshun\Checkbox\Models\Receipts\ServiceReceipt(
        new \khoroshun\Checkbox\Models\Receipts\Payments\CashPaymentPayload(5100)
    )
): \khoroshun\Checkbox\Models\Receipts\Receipt // создаем чек сервисного внесения денег (наличкой)
```
```php
$api->createServiceReceipt(
    new \khoroshun\Checkbox\Models\Receipts\ServiceReceipt(
        new \khoroshun\Checkbox\Models\Receipts\Payments\CardPaymentPayload(1000)
    )
): \khoroshun\Checkbox\Models\Receipts\Receipt // создаем чек сервисного внесения денег (картой)
```
```php
$api->createServiceReceipt(
    new \khoroshun\Checkbox\Models\Receipts\ServiceReceipt(
        new new \khoroshun\Checkbox\Models\Receipts\Payments\CashPaymentPayload(-5100)
    )
): \khoroshun\Checkbox\Models\Receipts\Receipt // создаем чек сервисного вынесения денег (наличкой) (знак минус)
```
```php
$api->createServiceReceipt(
    new \khoroshun\Checkbox\Models\Receipts\ServiceReceipt(
        new new \khoroshun\Checkbox\Models\Receipts\Payments\CardPaymentPayload(-1000)
    )
): \khoroshun\Checkbox\Models\Receipts\Receipt // создаем чек сервисного вынесения денег (картой) (знак минус)
```

##### Рекомендации:

> все операции оборачивать в try cache
```php
try {

    // тут все делаем

} catch (\khoroshun\Checkbox\Errors\InvalidCredentials $err) {
    var_dump('creds err', $err->getMessage());
}  catch (\khoroshun\Checkbox\Errors\EmptyResponse $err) {
    var_dump('empty response', $err->getMessage(), $err->getTraceAsString());
} catch (\khoroshun\Checkbox\Errors\Validation $err) {
    var_dump('valid err', $err->getMessage());
    var_dump('error detail', $err->getDetail());
} catch (\khoroshun\Checkbox\Errors\NoActiveShift $err) {
    var_dump('no shift', $err->getMessage());
} catch (\khoroshun\Checkbox\Errors\AlreadyOpenedShift $err) {
    var_dump('opened shift', $err->getMessage());
} catch (\Exception $err) {
    var_dump('default err', $err->getMessage());
}
```

##### подключение всех неймспейсов из примеров:
```php
use khoroshun\Checkbox\CheckboxJsonApi;
use khoroshun\Checkbox\Config;
use khoroshun\Checkbox\Errors\InvalidCredentials;
use khoroshun\Checkbox\Errors\Validation;
use khoroshun\Checkbox\Errors\NoActiveShift;
use khoroshun\Checkbox\Errors\AlreadyOpenedShift;
use khoroshun\Checkbox\Errors\EmptyResponse;
use khoroshun\Checkbox\Models\CashRegisters\CashRegistersQueryParams;
use khoroshun\Checkbox\Models\Shifts\ShiftsQueryParams;
use khoroshun\Checkbox\Models\Receipts\ReceiptsQueryParams;
use khoroshun\Checkbox\Models\Receipts\Discounts\Discounts;
use khoroshun\Checkbox\Models\Receipts\Discounts\DiscountModel;
use khoroshun\Checkbox\Models\Receipts\SellReceipt;
use khoroshun\Checkbox\Models\Receipts\Payments\Payments;
use khoroshun\Checkbox\Models\Receipts\Payments\CardPaymentPayload;
use khoroshun\Checkbox\Models\Receipts\Payments\CashPaymentPayload;
use khoroshun\Checkbox\Models\Receipts\ServiceReceipt;
use khoroshun\Checkbox\Models\Reports\PeriodicalReportQueryParams;
use khoroshun\Checkbox\Models\Reports\ReportsQueryParams;
use khoroshun\Checkbox\Models\Transactions\TransactionsQueryParams;
use khoroshun\Checkbox\Models\Receipts\Goods\Goods;
use khoroshun\Checkbox\Models\Receipts\Goods\GoodItemModel;
use khoroshun\Checkbox\Models\Receipts\Goods\GoodModel;
```

# Для котрибьюторов


For run all tests
```shell
make all
```
or connect to terminal
```shell
make exec
```

or use built in php server [http://localhost:8080](http://localhost:8080)
```shell
# start server on 8080 port
make serve
# custom port 8081
make serve PORT=8081
```

*Dafault php version is 8.0*. Use PHP_VERSION= for using custom version.
```shell
make all PHP_VERSION=8.0
```

all commands
```shell
# security check
make security
# composer install
make install
# composer install with --no-dev
make install-no-dev
# check code style
make style
# run static analyze tools
make static-analyze
# run unit tests
make unit
#  check coverage
make coverage
```

Without Docker
```
#validate composer json
composer check-composer

#static analyzes and codestyle
composer static

#run unit tests
composer unit-tests

#run all tests

composer all-tests
```

