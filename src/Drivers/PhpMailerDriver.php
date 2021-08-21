<?php

declare(strict_types=1);

namespace Pollen\Mail\Drivers;

use BadMethodCallException;
use InvalidArgumentException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use Pollen\Mail\MailerDriver;
use Pollen\Mail\MailerDriverInterface;
use Throwable;
use Exception;

/**
 * @mixin PHPMailer
 */
class PhpMailerDriver extends MailerDriver
{
    /**
     * PHP Mailer instance.
     * @var PHPMailer
     */
    protected PHPMailer $phpMailer;

    /**
     *
     * @param PHPMailer|null $phpmailer
     *
     * @return void
     */
    public function __construct(?PHPMailer $phpmailer = null)
    {
        $this->phpMailer = $phpmailer ?: new PHPMailer();
    }

    /**
     * Delegate calling of PHP Mailer method.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function __call(string $method, array $arguments)
    {
        try {
            return $this->phpMailer->{$method}(...$arguments);
        } catch (Exception $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new BadMethodCallException(
                sprintf(
                    '[%s] Delegate calling of PHP Mailer method [%s] throws an exception: %s.',
                    __CLASS__,
                    $method,
                    $e->getMessage()
                ), 0, $e
            );
        }
    }

    /**
     * Delegate getting of PHP Mailer variable.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        try {
            return $this->phpMailer->$key;
        } catch (Throwable $e) {
            throw new InvalidArgumentException(
                sprintf(
                    '[%s] Delegate getting of PHPMailer variable [%s] throws an exception: %s.',
                    __CLASS__,
                    $key,
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * Delegate setting of PHP Mailer variable.
     *
     * @param mixed $key
     *
     * @return void
     */
    public function __set($key, $value)
    {
        try {
            $this->phpMailer->$key = $value;
        } catch (Throwable $e) {
            throw new InvalidArgumentException(
                sprintf(
                    '[%s] Delegate setting of PHP Mailer variable [%s] throws an exception: %s.',
                    __CLASS__,
                    $key,
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * Delegate checking of PHP Mailer variable existence.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        try {
            return isset($this->phpMailer->$key);
        } catch (Throwable $e) {
            throw new InvalidArgumentException(
                sprintf(
                    '[%s] Delegate checking of PHP Mailer variable existence [%s] throws an exception: %s.',
                    __CLASS__,
                    $key,
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function addAttachment(string $path): MailerDriverInterface
    {
        $args = func_get_args();

        $this->phpMailer->addAttachment(...$args);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function addBcc(string $email, string $name = ''): MailerDriverInterface
    {
        $this->phpMailer->addBCC($email, $name);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function addCc(string $email, string $name = ''): MailerDriverInterface
    {
        $this->phpMailer->addCC($email, $name);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function addReplyTo(string $email, string $name = ''): MailerDriverInterface
    {
        $this->phpMailer->addReplyTo($email, $name);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function addTo(string $email, string $name = ''): MailerDriverInterface
    {
        $this->phpMailer->addAddress($email, $name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function error(): string
    {
        return $this->phpMailer->ErrorInfo;
    }

    /**
     * @inheritDoc
     */
    public function getAttachments(): array
    {
        return $this->phpMailer->getAttachments();
    }

    /**
     * @inheritDoc
     */
    public function getBcc(): array
    {
        return $this->phpMailer->getBccAddresses();
    }

    /**
     * @inheritDoc
     */
    public function getCharset(): string
    {
        return $this->phpMailer->CharSet;
    }

    /**
     * @inheritDoc
     */
    public function getCc(): array
    {
        return $this->phpMailer->getCcAddresses();
    }

    /**
     * @inheritDoc
     */
    public function getContentType(): string
    {
        return $this->phpMailer->ContentType;
    }

    /**
     * @inheritDoc
     */
    public function getEncoding(): string
    {
        return $this->phpMailer->Encoding;
    }

    /**
     * @inheritDoc
     */
    public function getFrom(): array
    {
        return [$this->phpMailer->From, $this->phpMailer->FromName];
    }

    /**
     * @inheritDoc
     */
    public function getFromEmail(): ?string
    {
        return $this->getFrom()[0] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getFromName(): ?string
    {
        return $this->getFrom()[1] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return explode($this->phpMailer::getLE(), $this->phpMailer->createHeader());
    }

    /**
     * @inheritDoc
     */
    public function getHtml(): string
    {
        if (!$this->hasHtml()) {
            return '';
        }
        return $this->phpMailer->Body;
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        return $this->phpMailer->Body;
    }

    /**
     * @inheritDoc
     */
    public function getReplyTo(): array
    {
        return $this->phpMailer->getReplyToAddresses();
    }

    /**
     * @inheritDoc
     */
    public function getSubject(): string
    {
        return $this->phpMailer->Subject;
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        if (!$this->hasText()) {
            return '';
        }
        return $this->hasHtml() ? $this->phpMailer->AltBody : $this->phpMailer->Body;
    }

    /**
     * @inheritDoc
     */
    public function getTo(): array
    {
        return $this->phpMailer->getToAddresses();
    }

    /**
     * @inheritDoc
     */
    public function hasHtml(): bool
    {
        return in_array($this->getContentType(), ['text/html', 'multipart/alternative']);
    }

    /**
     * @inheritDoc
     */
    public function hasText(): bool
    {
        return in_array($this->getContentType(), ['text/plain', 'multipart/alternative']);
    }

    /**
     * @inheritDoc
     */
    public function prepare(): bool
    {
        try {
            return $this->preSend();
        } catch (PHPMailerException $e) {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function send(): bool
    {
        return $this->phpMailer->send();
    }

    /**
     * @inheritDoc
     */
    public function setCharset(string $charset = 'utf-8'): MailerDriverInterface
    {
        $this->phpMailer->CharSet = $charset;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContentType(string $content_type = 'multipart/alternative'): MailerDriverInterface
    {
        $this->phpMailer->ContentType = in_array($content_type, ['text/html', 'text/plain', 'multipart/alternative'])
            ? $content_type : 'multipart/alternative';

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setEncoding(string $encoding): MailerDriverInterface
    {
        $this->phpMailer->Encoding = in_array($encoding, ['8bit', '7bit', 'binary', 'base64', 'quoted-printable'])
            ? $encoding : '8bit';

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function setFrom(string $email, string $name = ''): MailerDriverInterface
    {
        $this->phpMailer->setFrom($email, $name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHtml(string $message): MailerDriverInterface
    {
        $this->phpMailer->Body = $message;
        $this->phpMailer->isHTML(true);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSubject(string $subject = ''): MailerDriverInterface
    {
        $this->phpMailer->Subject = $subject;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setText(string $text): MailerDriverInterface
    {
        $this->phpMailer->AltBody = $text;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $this->prepare();

        return [
            'from'         => $this->getFrom(),
            'to'           => $this->getTo(),
            'cc'           => $this->getCc(),
            'bcc'          => $this->getBccAddresses(),
            'reply-to'     => $this->getReplyTo(),
            'attachments'  => $this->getAttachments(),
            'charset'      => $this->getCharset(),
            'content_type' => $this->getContentType(),
            'encoding'     => $this->getEncoding(),
            'headers'      => $this->getHeaders(),
            'subject'      => $this->getSubject(),
            'html'         => $this->getHtml(),
            'text'         => $this->getText(),
        ];
    }
}