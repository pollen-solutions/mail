# Mail Component

[![Latest Stable Version](https://img.shields.io/packagist/v/pollen-solutions/mail.svg?style=for-the-badge)](https://packagist.org/packages/pollen-solutions/mail)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)
[![PHP Supported Versions](https://img.shields.io/badge/PHP->=7.4-8892BF?style=for-the-badge&logo=php)](https://www.php.net/supported-versions.php)

Pollen Solutions **Mail** Component provides tools for creating and sending emails. 

## Installation

```bash
composer require pollen-solutions/mail
```

## Basic Usage

### Send an email

```php
use Pollen\Mail\MailManager;

$mail = new MailManager();

$mail->send([
    'to'   => ['to@domain.ltd', 'Recipient Name'],
    'from' => ['from@domain.ltd', 'Sender Name'],
]);
```

### Debug an email

```php
use Pollen\Mail\MailManager;

$mail = new MailManager();

$mail->debug([
    'to'   => ['to@domain.ltd', 'Recipient Name'],
    'from' => ['from@domain.ltd', 'Sender Name']
]);
```

### Queue an email (work in progress)

coming soon ...

## Mail configuration

```php
/**
 * Name identifier in mail manager.
 * @var string|null
 */
'name'         => null,
/**
 * Recipients of the message.
 * @var string|array|null
 */
'to'           => null,
/**
 * Message sender.
 * @var string|array|null
 */
'from'         => null,
/**
 * Reply-to contact of the message.
 * @var string|array|null
 */
'reply-to'     => null,
/**
 * Blind carbon copy recipients of the message.
 * @var string|array|null
 */
'bcc'          => null,
/**
 * Carbon copy recipients of the message.
 * @var string|array|null
 */
'cc'           => null,
/**
 * Attachments of the message.
 * @var string|array|null
 */
'attachments'  => null,
/**
 * HTML content of the message.
 * {@internal false or empty to disable|true to use the html/message template|string of the HTML
 * content|array of message parts. The Array parts could have header|body|footer key and typed
 * as bool|string.}
 * @var bool|string|array|null
 */
'html'         => true,
/**
 * Plain text content of the message.
 * {@internal false or empty to disable|true to use the text/message template|string of text content.}
 * @var string|null
 */
'text'         => null,
/**
 * List of template datas.
 * @var array
 */
'datas'        => [],
/**
 * Subject of the message.
 * @var string
 */
'subject'      => 'Mail test',
/**
 * Locale of the message.
 * @var string
 */
'locale'       => 'en',
/**
 * Charset of the message.
 * @var string
 */
'charset'      => 'utf-8',
/**
 * Encoding of the message.
 * @var string|null 8bit|7bit|binary|base64|quoted-printable
 */
'encoding'     => null,
/**
 * Content type of the message.
 * @var string|null multipart/alternative|text/html|text/plain
 */
'content_type' => null,
/**
 * Css properties of the HTML message.
 * @var string
 */
'css'          => file_get_contents($this->mail()->resources('/assets/css/styles.css')),
/**
 * Inline CSS formatting flag.
 * @var bool
 */
'inline_css'   => true,
/**
 * List of parameters of the template view|View instance.
 * @var array|ViewInterface $view
 */
'view'         => [],
```

## Use registered mail

```php
use Pollen\Mail\MailManager;

$mail = new MailManager();

$mail->setMailable([
    'name' => 'my-first-mail',
    'to'   => ['to@domain.ltd', 'Recipient Name'],
    'from' => ['from@domain.ltd', 'Sender Name'],
]);

if ($mailable = $mail->get('my-first-mail')) {
    $mailable->send();
}
```

## Use Mailable

### Send an email with the base Mailable

```php
use Pollen\Mail\MailManager;
use Pollen\Mail\Mailable;

$mail = new MailManager();
$mailable = new Mailable($mail);
$mailable
    ->setFrom(['from@domain.ltd', 'Sender Name'])
    ->setTo(['to@domain.ltd', 'Recipient Name']);

$mailable->send();
```

### Send an email with a custom Mailable

```php
use Pollen\Mail\MailManager;
use Pollen\Mail\Mailable;

$mail = new MailManager();
$mailable = new class($mail) extends Mailable {
    protected ?array $from = ['from@domain.ltd', 'Sender Name'];
    protected ?array $to = ['to@domain.ltd', 'Recipient Name'];
};

$mailable->send();
```

## Templating email

@see [https://tedgoas.github.io/Cerberus/](https://tedgoas.github.io/Cerberus/)

## Tips

### Email testing with MailHog

MailHog must be installed and running on your server.

More details : https://github.com/mailhog/MailHog

1. Start MailHog with default configuration

```bash
~/go/bin/MailHog
```

2. Configure Pollen Mail Component for MailHog

```php
use Pollen\Mail\MailManager;
use Pollen\Mail\Drivers\PhpMailerDriver;

$mail = new MailManager();
$mail->setMailerConfigCallback(function (PhpMailerDriver $mailer) {
    $mailer->isSMTP();
    $mailer->Host = '0.0.0.0';
    $mailer->Username = 'mailhog.example';
    $mailer->Port = 1025;
});

$mail->send();
```

Visit MailHog Web Ui : [http://0.0.0.0:8025](http://0.0.0.0:8025)

### Configure MailHog in Wordpress

For Wordpress environnement add this configuration in current theme functions.php

```php
# functions.php 
add_action('phpmailer_init', function (PHPMailer $mailer) {
    $mailer->isSMTP();
    $mailer->Host = '0.0.0.0';
    $mailer->Username = 'mailhog.example';
    $mailer->Port = 1025;
});
```
