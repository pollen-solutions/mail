<?php

declare(strict_types=1);

namespace Pollen\Mail;

use DateTimeInterface;
use Pollen\Http\ResponseInterface;
use Pollen\Support\Concerns\BuildableTraitInterface;
use Pollen\Support\Concerns\ParamsBagAwareTraitInterface;
use Pollen\Support\ParamsBag;
use Pollen\Support\Proxy\MailProxyInterface;
use Pollen\Support\Proxy\PartialProxyInterface;
use Pollen\Support\Proxy\ViewProxyInterface;
use InvalidArgumentException;
use Pollen\View\ViewInterface;

interface MailableInterface extends
    BuildableTraitInterface,
    MailProxyInterface,
    ParamsBagAwareTraitInterface,
    PartialProxyInterface,
    ViewProxyInterface
{
    /**
     * Resolves class as a string and returns the message of the mail.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Building.
     *
     * @return static
     */
    public function build(): MailableInterface;

    /**
     * Retrieve instance of template datas|Sets array of datas|Gets a data value.
     *
     * @param array|string|null $key
     * @param mixed $default
     *
     * @return string|int|array|ParamsBag
     *
     * @throws InvalidArgumentException
     */
    public function datas($key = null, $default = null);

    /**
     * Gets the css properties.
     * {@internal if inline_css is off.}
     *
     * @return string
     */
    public function getCssProperties(): string;

    /**
     * Gets the locale.
     *
     * @return string
     */
    public function getLocale(): string;

    /**
     * Retrieve the mailer driver.
     *
     * @return MailerDriverInterface
     */
    public function getMailer(): MailerDriverInterface;

    /**
     * Get the name identifier in the mail manager.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Render the mail in debug mode.
     *
     * @return string
     */
    public function debug(): string;

    /**
     * Queue the mail.
     *
     * @param DateTimeInterface|string $date
     * @param array $params
     *
     * @return int
     *
     * @todo
     */
    public function queue($date = 'now', array $params = []): int;

    /**
     * Gets the message in HTML or Plain text format (depends on configuration).
     *
     * @return string
     */
    public function message(): string;

    /**
     * Return the message in HTML or Plain text format (depends on configuration) as an HTTP response.
     *
     * @return ResponseInterface
     */
    public function response(): ResponseInterface;

    /**
     * Send the mail.
     *
     * @return bool
     */
    public function send(): bool;

    /**
     * Sets the list of attachments.
     *
     * @param string|array $attachments
     *
     * @return static
     */
    public function setAttachments($attachments): MailableInterface;

    /**
     * Sets blind carbon copy recipients of the message.
     *
     * @param string|array $bcc
     *
     * @return static
     */
    public function setBcc($bcc): MailableInterface;

    /**
     * Sets carbon copy recipients of the message.
     *
     * @param string|array $cc
     *
     * @return static
     */
    public function setCc($cc): MailableInterface;

    /**
     * Sets CSS properties of the HTML message.
     *
     * @param string $css
     *
     * @return static
     */
    public function setCss(string $css): MailableInterface;

    /**
     * Sets the charset of the message.
     *
     * @param string $charset
     *
     * @return static
     */
    public function setCharset(string $charset): MailableInterface;

    /**
     * Sets the content type of the message.
     *
     * @param string $contentType
     *
     * @return static
     */
    public function setContentType(string $contentType): MailableInterface;

    /**
     * Sets the encoding of the message.
     *
     * @param string $encoding
     *
     * @return static
     */
    public function setEncoding(string $encoding): MailableInterface;

    /**
     * Sets the message sender.
     *
     * @param string|array $from
     *
     * @return static
     */
    public function setFrom($from): MailableInterface;

    /**
     * Sets the HTML message.
     *
     * @param string $html
     *
     * @return static
     */
    public function setHtml(string $html): MailableInterface;

    /**
     * Sets the inline CSS formatting flag.
     *
     * @param bool $inlineCss
     *
     * @return static
     */
    public function setInlineCss(bool $inlineCss = true): MailableInterface;

    /**
     * Set the locale of the message.
     *
     * @param string $locale
     *
     * @return static
     */
    public function setLocale(string $locale): MailableInterface;

    /**
     * Sets the mailer driver instance.
     *
     * @param MailerDriverInterface $mailer
     *
     * @return static
     */
    public function setMailer(MailerDriverInterface $mailer): MailableInterface;

    /**
     * Sets the reply-to contact of the message.
     *
     * @param string|array $replyTo
     *
     * @return static
     */
    public function setReplyTo($replyTo): MailableInterface;

    /**
     * Sets the subject of the message.
     *
     * @param string $subject
     *
     * @return static
     */
    public function setSubject(string $subject): MailableInterface;

    /**
     * Sets the plain text message.
     *
     * @param string $text
     *
     * @return static
     */
    public function setText(string $text): MailableInterface;

    /**
     * Sets the recipients of the message.
     *
     * @param string|array $to
     *
     * @return static
     */
    public function setTo($to): MailableInterface;

    /**
     * Resolves the view instance|Returns a particular template render.
     *
     * @param string|null $name.
     * @param array $data
     *
     * @return ViewInterface|string
     */
    public function view(?string $name = null, array $data = []);
}