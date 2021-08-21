<?php

declare(strict_types=1);

namespace Pollen\Mail;

interface MailerDriverInterface
{
    /**
     * Add an attachment.
     *
     * @param string $path
     *
     * @return static
     */
    public function addAttachment(string $path): MailerDriverInterface;

    /**
     * Add a blind carbon copy recipients of the message.
     *
     * @param string $email
     * @param string $name
     *
     * @return static
     */
    public function addBcc(string $email, string $name = ''): MailerDriverInterface;

    /**
     * Add carbon copy recipients of the message.
     *
     * @param string $email
     * @param string $name
     *
     * @return static
     */
    public function addCc(string $email, string $name = ''): MailerDriverInterface;

    /**
     * Add a reply-to contact of the message.
     *
     * @param string $email
     * @param string $name
     *
     * @return static
     */
    public function addReplyTo(string $email, string $name = ''): MailerDriverInterface;

    /**
     * Add a recipient of the message.
     *
     * @param string $email
     * @param string $name
     *
     * @return static
     */
    public function addTo(string $email, string $name = ''): MailerDriverInterface;

    /**
     * Returns the current handle error.
     *
     * @return string
     */
    public function error(): string;

    /**
     * Gets the list of attachments.
     *
     * @return array
     */
    public function getAttachments(): array;

    /**
     * Gets the list of blind carbon copy recipients.
     *
     * @return array
     */
    public function getBcc(): array;

    /**
     * Gets the list of carbon copy recipients.
     *
     * @return array
     */
    public function getCc(): array;

    /**
     * Gets the charset of the message.
     *
     * @return string
     */
    public function getCharset(): string;

    /**
     * Gets the content type of the message.
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * Gets the encoding of the message.
     *
     * @return string
     */
    public function getEncoding(): string;

    /**
     * Gets the message sender.
     *
     * @return array
     */
    public function getFrom(): array;

    /**
     * Gets the message sender email.
     *
     * @return string|null
     */
    public function getFromEmail(): ?string;

    /**
     * Gets the message sender name.
     *
     * @return string|null
     */
    public function getFromName(): ?string;

    /**
     * Gets the headers of the message.
     *
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Get the HTML content of the message.
     *
     * @return string
     */
    public function getHtml(): string;

    /**
     * Gets the message in HTML or Plain text format (depends on configuration).
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * Gets the reply-to contact of the message.
     *
     * @return array
     */
    public function getReplyTo(): array;

    /**
     * Gets the subject of the message.
     *
     * @return string
     */
    public function getSubject(): string;

    /**
     * Gets the plain text content of the message.
     *
     * @return string
     */
    public function getText(): string;

    /**
     * Gets the recipients of the message.
     *
     * @return array
     */
    public function getTo(): array;

    /**
     * Check if the mail has an HTML format.
     *
     * @return bool
     */
    public function hasHtml(): bool;

    /**
     * Check if the mail has a plain text format.
     *
     * @return bool
     */
    public function hasText(): bool;

    /**
     * Return list of contact linearized.
     *
     * @param array<string, string> $contacts
     *
     * @return string[]
     */
    public function linearizeContacts(array $contacts): array;

    /**
     * Return a linearized contact by its attributes (email, name).
     *
     * @param string $email
     * @param string|null $name
     *
     * @return string
     */
    public function linearizeContact(string $email, ?string $name = null): string;

    /**
     * Prepare the mail for sending.
     *
     * @return boolean
     */
    public function prepare(): bool;

    /**
     * Sends the mail.
     *
     * @return boolean
     */
    public function send(): bool;

    /**
     * Sets the charset of the message.
     *
     * @param string $charset
     *
     * @return static
     */
    public function setCharset(string $charset = 'utf-8'): MailerDriverInterface;

    /**
     * Sets the content type.
     *
     * @param string $content_type multipart/alternative|text/html|text/plain
     *
     * @return static
     */
    public function setContentType(string $content_type = 'multipart/alternative'): MailerDriverInterface;

    /**
     * Sets the encoding.
     *
     * @param string $encoding 8bit|7bit|binary|base64|quoted-printable.
     *
     * @return static
     */
    public function setEncoding(string $encoding): MailerDriverInterface;

    /**
     * Sets the message sender.
     *
     * @param string $email
     * @param string $name
     *
     * @return static
     */
    public function setFrom(string $email, string $name = ''): MailerDriverInterface;

    /**
     * Sets the message content in HTML format.
     *
     * @param string $message
     *
     * @return static
     */
    public function setHtml(string $message): MailerDriverInterface;

    /**
     * Sets the subject.
     *
     * @param string $subject
     *
     * @return static
     */
    public function setSubject(string $subject = ''): MailerDriverInterface;

    /**
     * Sets the message content in plain text format.
     *
     * @param string $text
     *
     * @return static
     */
    public function setText(string $text): MailerDriverInterface;

    /**
     * Returns configuration parameters as array.
     *
     * @return array
     */
    public function toArray(): array;
}