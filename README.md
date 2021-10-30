# Kyanda Api

[![GitHub TestCI Workflow](https://github.com/Nabcellent/laravel-kyanda/actions/workflows/test.yml/badge.svg?branch=master)](https://github.com/Nabcellent/laravel-kyanda/actions/workflows/test.yml)
[![Github StyleCI Workflow](https://github.com/Nabcellent/laravel-kyanda/actions/workflows/styleci.yml/badge.svg?branch=master)](https://github.com/Nabcellent/laravel-kyanda/actions/workflows/styleci.yml)
[![codecov](https://codecov.io/gh/Nabcellent/laravel-kyanda/branch/master/graph/badge.svg?token=6b0d0ba1-c2c6-4077-8c3a-1f567eea88a0)](https://codecov.io/gh/Nabcellent/laravel-kyanda)
[![Total Downloads](https://poser.pugx.org/nabcellent/laravel-kyanda/downloads)](https://packagist.org/packages/nabcellent/laravel-kyanda)
[![License](https://poser.pugx.org/nabcellent/laravel-kyanda/license)](https://github.com/Nabcellent/laravel-kyanda/blob/master/LICENSE.md)

This is a <i>Laravel 8</i> package that interfaces with [Kyanda](https://kyanda.co.ke/) Payments Api.
The API enables you to initiate mobile payments, disburse payments to mobile and bank, purchase airtime & bundles* and to pay for utility bills.

Check out their [api documentation](https://kyanda.co.ke/developer/index.html).

## Documentation

### Installation

You can install the package via composer:

``` bash
composer require nabcellent/laravel-kyanda
```

The package will automatically register itself.

You can publish the config file with the following command:
```bash
php artisan kyanda:install
```

### Getting Started
- ### Account
Enables you to check the status of items

1. Account balance
```php
Account::balance();
```

2. Transaction status
```php
Account::transactionStatus("KYAAPI___");
```


- ### Utility
Enables purchase of payment of goods and services

1. Airtime Purchase
```php
Utility::airtimePurchase(0712345678, 100);
```

2. Bill Payment
```php
Utility::billPayment(11011011011, 1000, Providers::KPLC_PREPAID);
```


- ### Notification
Enables registration of callback url via API call

1. Instant Payment Notification callback registration
```php
Notification::registerCallbackURL();
```

- ### Payments
Coming soon

<br>

### NOTE: Phone Number Validation
The phone validator was built using regex and the latest allocation of prefixes by Communication authority of Kenya (Apr, 2021).
Check the [docs](docs) to see the pdf listing with allocations.

## Testing

You can run the tests with:

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email [nabcellent.dev@gmail.com](mailto:nabcellent.dev@gmail.com) instead of using the issue tracker.

## Credits

- [Nabcellent](https://github.com/nabcellent)
- [Dr H](https://github.com/drh97)

[comment]: <> (- [All Contributors]&#40;../../contributors&#41;)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
