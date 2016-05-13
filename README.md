# AMPLEXOR X-Connect

[![Latest Version on Packagist][ico-packagist]][link-packagist]
[![Build Status][ico-build-master]][link-build-master]
[![Coverage status][ico-code-coverage]][link-code-coverage]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Software License][ico-license]][link-license]

[![SensioLabsInsight][ico-insight]][link-insight]


This library implements a PHP client for the [AMPLEXOR Translation Services][link-gcm].


## Install

Via Composer:

``` bash
$ composer require amplexor/xconnect
```


## Usage

[AMPLEXOR Translation Services][link-gcm] provides (S)FTP environments to upload translation 
requests to and download translation responses. The files are packed in ZIP
archives. These archives always contain a file with details about the request 
(order.xml) and the response (name-of-the-response-file.xml).

The *amplexor/xconnect* library abstracts this file creation and transfer process. 


### Create and send a translation request
Create a new translation request and send it to the AMPLEXOR Translation Service.

``` php
use Amplexor\XConnect\Request;
use Amplexor\XConnect\Request\File\ZipFile;
use Amplexor\XConnect\Service\SFtpService;


// Create a new translation request.
$sourceLanguage = 'en';
$config = [
    'clientId'          => 'abcde-1234567890-edcba',
    'orderNamePrefix'   => 'my_translation_order',
    'dueDate'           => 0,
    'issuedBy'          => 'me@company.com',
    'isConfidential'    => false,
    'needsConfirmation' => true,
    'needsQuotation'    => false,
];
$request = new Request($sourceLanguage, $config);

// Fill in the request details:
// The Language(s) to translate the content to:
$request->addTargetLanguage('nl');
$request->addTargetLanguage('fr');
// Optional instructions and reference:
$request->addInstruction('Instruction to add.');
$request->setReference('MY-INTERNAL-REF-0123456789');

// Add content to translate from files.
$request->addFile('path/to/file/document.docx');
$request->addFile('path/to/file/document.xml');

// Add content to translate from strings, a filename needs to be passed to
// identify the different content items.
$request->addFileContent('filename.html', $content);
$request->addFileContent('filename.xliff', $content);


// Create a service object by passing the connection details.
// There are 2 Service available depending on the AMPLEXOR Translation Service configuration:
// - FtpService : Transport over FTP (no encryption).
// - SFtpService : Transport over SSH (encryption).
$config = [
    'hostname' => 'hostname.com',
    'port'     => 22,
    'timeout'  => 90,
    'username' => 'USERNAME',
    'password' => 'PASSWORD',
    'directory_send' => 'TO_LSP',
    'directory_send_processed' => 'TO_LSP/processed',
    'directory_receive' => 'FROM_LSP',
    'directory_receive_processed' => 'FROM_LSP/processed',
];
$service = new SFtpService($config);


// Send the request as a zip file.
$result = $service->send(ZipFile::create($request, 'directory/to/store/file'));
```

### Scan AMPLEXOR Translation Service for processed translations
Connect to the AMPLEXOR Translation Service and retrieve a list of translated files.

``` php
use Amplexor\XConnect\Service\SFtpService;

// Connect to the AMPLEXOR Translation Service.
$service = new SFtpService($config);

// Get the list of ZIP packages that are ready, this will be an array of 
// filenames. Retrieving these files is possible by using the services receive 
// method. 
$list = $service->scan();
```

### Receive processed translations
Connect to the AMPLEXOR Translation Service, download the processed translation and extract the
content.

``` php
use Amplexor\XConnect\Response;
use Amplexor\XConnect\Response\File\ZipFile;
use Amplexor\XConnect\Service\SFtpService;

// Connect to the AMPLEXOR Translation Service.
$service = new SFtpService($config);

// Retrieve a single translation file (ZIP package).
$filePath = $service->receive(
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

// Let the service know that the response Zip archive is processed.
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

- [zero2one][link-author]
- [MPParsley][link-maintainer]
- [All Contributors][link-contributors]


## License

The MIT License (MIT). Please see [License File][link-license] for more information.



[ico-packagist]: https://img.shields.io/packagist/v/amplexor/xconnect.svg?style=flat-square
[ico-build-master]: https://img.shields.io/travis/amplexor-drupal/xconnect/master.svg?style=flat-square
[ico-code-coverage]: https://img.shields.io/scrutinizer/coverage/g/amplexor-drupal/xconnect.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/amplexor-drupal/xconnect.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-insight]: https://insight.sensiolabs.com/projects/0a2a8e45-e85c-4c30-bea0-1e05ccdc5623/big.png

[link-packagist]: https://packagist.org/packages/amplexor/xconnect
[link-build-master]: https://travis-ci.org/amplexor-drupal/xconnect/branches
[link-code-coverage]: https://scrutinizer-ci.com/g/amplexor-drupal/xconnect
[link-code-quality]: https://scrutinizer-ci.com/g/amplexor-drupal/xconnect/code-structure
[link-license]: LICENSE.md
[link-insight]: https://insight.sensiolabs.com/projects/0a2a8e45-e85c-4c30-bea0-1e05ccdc5623

[link-author]: https://github.com/zero2one
[link-maintainer]: https://github.com/MPParsley
[link-contributors]: https://github.com/amplexor-drupal/xconnect/contributors

[link-gcm]: http://goo.gl/xCQ4em 
