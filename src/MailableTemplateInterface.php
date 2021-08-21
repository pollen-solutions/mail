<?php

declare(strict_types=1);

namespace Pollen\Mail;

use Pollen\ViewExtends\PlatesTemplateInterface;

/**
 * @method array getAttachments()
 * @method array getBcc()
 * @method array getCc()
 * @method array getCssProperties()
 * @method string getCharset()
 * @method string getContentType()
 * @method string getEncoding()
 * @method array getFrom()
 * @method string getFromEmail()
 * @method string getFromName()
 * @method array getHeaders()
 * @method string getHtml()
 * @method string getLocale()
 * @method string getMessage()
 * @method array getReplyTo()
 * @method string getSubject()
 * @method string getText()
 * @method array getTo()
 * @method bool hasHtml()
 * @method bool hasText()
 * @method string linearizeContact(string $email, string|null $name = null)
 * @method string[] linearizeContacts(array $contacts)
 */
interface MailableTemplateInterface extends PlatesTemplateInterface
{
}