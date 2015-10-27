# Amplexor X-Connect

[![Latest Version](https://img.shields.io/github/release/amplexor-drupal/xconnect.svg?style=flat-square)](https://github.com/amplexor-drupal/xconnect/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/amplexor-drupal/xconnect/master.svg?style=flat-square)](https://travis-ci.org/amplexor-drupal/xconnect)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/amplexor-drupal/xconnect.svg?style=flat-square)](https://scrutinizer-ci.com/g/amplexor-drupal/xconnect/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/amplexor-drupal/xconnect.svg?style=flat-square)](https://scrutinizer-ci.com/g/amplexor-drupal/xconnect)


This library implements a PHP client for the Euroscript Global Content 
Management (GCM) language services (see http://goo.gl/VW0KK6).


## Install

Via Composer:

``` bash
$ composer require amplexor/xconnect
```


## Usage

### Create and send a translation request
Create a new translation request and send it to the GCM service.

``` php
use Amplexor\XConnect\Request;
use Amplexor\XConnect\Request\File\ZipFile;
use Amplexor\XConnect\Service\SFTPService;


// Create a new translation request.
$sourceLanguage = 'en';
$request = new Request(
    $sourceLanguage
    array(
        'clientId'          => 'abcde-1234567890-edcba',
        'orderNamePrefix'   => 'my_translation_order',
        'dueDate'           => 0,
        'issuedBy'          => 'me@company.com',
        'isConfidential'    => false,
        'needsConfirmation' => true,
        'needsQuotation'    => false,
    )
);

// Fill in the request details.
$request->addTargetLanguage('nl');
$request->addTargetLanguage('fr');
$request->addInstruction('Instruction to add');
$request->setReference('MY-INTERNAL-REF-0123456789');

// Add the content to translate.
$request->addFile('path/to/file/document.docx');
$request->addFile('path/to/file/document.xml');
$request->addFileContent('filename.html', $content);
$request->addFileContent('filename.xliff', $content);


// Send the file using the SFTP service.
$service = new SFTPService(
    array(
        'hostname' => 'hostname.com',
        'port'     => 22,
        'username' => 'USERNAME',
        'password' => 'PASSWORD',
        'directory_send' => 'TO_LSP',
        'directory_send_processed' => 'TO_LSP/processed',
        'directory_receive' => 'FROM_LSP',
        'directory_receive_processed' => 'FROM_LSP/processed',
    )
);


// Send the request as a zip file.
$result = $service->send(new ZipFile($request));
```

### Scan GCM service for processed translations
Connect to the GCM service and retrieve a list of translated files.

``` php
use Amplexor\XConnect\Service\SFTPService;

// Connect to the GCM service.
$service = new SFTPService($config);

// Get the list of ZIP packages that are ready, this will be an array of 
// filenames. Retrieving these files is possible by using the services receive 
// method. 
$list = $service->scan();
```

### Receive processed translations
Connect to the GCM service, download the processed translation and extract the
content.

``` php
use Amplexor\XConnect\Response;
use Amplexor\XConnect\Response\ZipFile;
use Amplexor\XConnect\Service\SFTPService;

// Connect to the GCM service.
$service = new SFTPService($config);

// Retrieve a single translation file (ZIP package).
$filePath = $connection->receive(
    // The filename ready to be picked up.
    'filename.zip', 
    // The local directory where to store the downloaded file.
    '/local/directory/to/store/the/downloaded/file/'
);

// Create a response object as a wrapper around the received file.
$response = new Response(new ZipFile($filePath));

// Get the translations from the Response.
$translations = $response->getTranslations();

// Get the content of the translations.
foreach ($translations as $translation) {
  $content = $translation->getContent();
}

// Let the service now that the response is processed.
$service->processed('filename.zip');
```


## Testing
Run all tests (make sure that you first have downloaded the dependencies with
composer, see Contributing):

``` bash
$ cd /path/where/the/repo/is/cloned
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
