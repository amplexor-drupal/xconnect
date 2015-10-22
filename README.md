# Amplexor X-Connect

[![Latest Version](https://img.shields.io/github/release/amplexor-drupal/xconnect.svg?style=flat-square)](https://github.com/amplexor-drupal/xconnect/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/amplexor-drupal/xconnect/master.svg?style=flat-square)](https://travis-ci.org/amplexor-drupal/xconnect)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/amplexor-drupal/xconnect.svg?style=flat-square)](https://scrutinizer-ci.com/g/amplexor-drupal/xconnect/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/amplexor-drupal/xconnect.svg?style=flat-square)](https://scrutinizer-ci.com/g/amplexor-drupal/xconnect)


This library implements a PHP client for the Euroscript Global Content 
Management (GCM) language services (see http://goo.gl/VW0KK6).


## Install

Via Composer

``` bash
$ composer require amplexor/xconnect
```


## Usage

### Create and send a request
Create a new translation request and send it to the GCM service.

``` php
// Create a translation request order.
$config = array();
$order = new Amplexor\XConnect\Request\Order('en', $config);
$orderFile = new Amplexor\XConnect\Request\File\Zip('/local/temp/path/', $order);

// Add file(s) that need to be translated.
$orderFile->addFile('/path/to/local/file.ext');
// ...

// Add files as strings that need to be translated.
$orderFile->addFileString('filename.html', $string);
// ...

// Send the translation request to the GCM service.
$config = array(
    'hostname' => 'hostname.com',
    'port'     => 22,
    'username' => 'USERNAME',
    'password' => 'PASSWORD',
    'dir_send' => 'TO_LSP',
    'dir_send_processed' => 'TO_LSP/processed',
    'dir_receive' => 'FROM_LSP',
    'dir_receive_processed' => 'FROM_LSP/processed',
);
$connection = new Amplexor\XConnect\Connection\SFTP($config);
$success = $connection->send($orderFile);
```

### Scan GCM service for processed translations
Connect to the GCM service and retrieve a list of translated files.

``` php
// Connect to the GCM service.
$config = array(
    // ...
);
$connection = new Amplexor\XConnect\Connection\SFTP($config);

// Get the list of ZIP packages that are ready, this will be an array of 
// filenames. 
$list = $connection->scan();
```

### Receive processed translations
Connect to the GCM service, download the processed translation and extract the
content.

``` php
// Connect to the GCM service.
$connection = new Amplexor\XConnect\Connection\SFTP($config);

// Retrieve a single translation file (ZIP package).
$file = $connection->receive('filename.zip', '/local/directory/to/store/the/downloaded/file/');

// The file is an Amplexor\XConnect\Receive\File\Zip() object.
// Get the Delivery information from it (Amplexor\XConnect\Receive\Delivery).
$delivery = $file->getDelivery();

// Get all the translated files from the ZIP packages.
foreach ($file->getTranslationFileNames() as $fileName) {
  $translatedFile = $file->extractFile($fileName, '/local/directory/to/extract/the/file/to/');
}

// You can also get the content of the files as a string.
foreach ($file->getTranslations() as $fileName) {
  $content = $file->extractFileAsString($filename);
}
```


## Testing
Run all tests:

``` bash
$ phpunit
```


## Contributing
Fork this repository and create pull requests if you would like to contribute.

Setup your local development environment by cloning the forked repository and
run composer to get the dependencies:

``` bash
$ cd /path/where/the/repo/is/cloned
$ composer install
```


## Credits

- [zero2one](https://github.com/zero2one)
- [All Contributors](https://github.com/amplexor/xconnect/contributors)


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
