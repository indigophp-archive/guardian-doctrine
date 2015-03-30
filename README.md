# Indigo Guardian Doctrine

[![Latest Version](https://img.shields.io/github/release/indigophp/guardian-doctrine.svg?style=flat-square)](https://github.com/indigophp/guardian-doctrine/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/indigophp/guardian-doctrine.svg?style=flat-square)](https://travis-ci.org/indigophp/guardian-doctrine)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/indigophp/guardian-doctrine.svg?style=flat-square)](https://scrutinizer-ci.com/g/indigophp/guardian-doctrine)
[![Quality Score](https://img.shields.io/scrutinizer/g/indigophp/guardian-doctrine.svg?style=flat-square)](https://scrutinizer-ci.com/g/indigophp/guardian-doctrine)
[![HHVM Status](https://img.shields.io/hhvm/indigophp/guardian-doctrine.svg?style=flat-square)](http://hhvm.h4cc.de/package/indigophp/guardian-doctrine)
[![Total Downloads](https://img.shields.io/packagist/dt/indigophp/guardian-doctrine.svg?style=flat-square)](https://packagist.org/packages/indigophp/guardian-doctrine)

**Doctrine integration for Guardian.**


## Install

Via Composer

``` bash
$ composer require indigophp/guardian-doctrine
```


## Usage

Simply pass an `EntityManagerInterface` instance and an Entity class name to the `Indigo\Guardian\Identifier\Doctrine` class and you are good to go:

``` php
use Indigo\Guardian\Identifier\Doctrine;

$identifier = new Doctrine($entityManager, 'Indigo\Guardian\Caller\User\Simple');

// optional, ['username'] by default
$identifier->setIdentificationFields(['username']);

// optional, 'loginToken' by default
$identifier->setLoginTokenField('id');
```


## Testing

``` bash
$ phpspec run
```


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Credits

- [Márk Sági-Kazár](https://github.com/sagikazarmark)
- [All Contributors](https://github.com/indigophp/guardian-doctrine/contributors)


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
