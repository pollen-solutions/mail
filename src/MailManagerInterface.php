<?php

declare(strict_types=1);

namespace Pollen\Mail;

use DateTimeInterface;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;
use Pollen\Support\Concerns\ConfigBagAwareTraitInterface;
use Pollen\Support\Concerns\ResourcesAwareTraitInterface;
use Pollen\Support\ParamsBag;
use Pollen\Support\Proxy\ContainerProxyInterface;

interface MailManagerInterface extends
    ConfigBagAwareTraitInterface,
    ResourcesAwareTraitInterface,
    ContainerProxyInterface
{
    /**
     * Adds a mail instance to the list of registered mails.
     *
     * @param MailableInterface $mailable.
     *
     * @return MailManagerInterface
     */
    public function add(MailableInterface $mailable): MailManagerInterface;

    /**
     * Return the list of registered mail instances.
     *
     * @return array<string, MailableInterface>|array
     */
    public function all(): array;

    /**
     * Echoes a mail in debug mode.
     *
     * @param MailableInterface|array|null $mailableDef Used the current mail instance if mailableDef is null.
     *
     * @return void
     */
    public function debug($mailableDef = null): void;

    /**
     * Gets the instance of default parameters|Sets a list of default parameters|Gets a default parameter value.
     *
     * @param array|string|null $key
     * @param mixed $default
     *
     * @return string|int|array|mixed|ParamsBag
     *
     * @throws InvalidArgumentException
     */
    public function defaults($key = null, $default = null);

    /**
     * Gets the mail instance by its name.
     *
     * @param string|null $name Used the current mail instance if its null.
     *
     * @return MailableInterface
     */
    public function get(?string $name = null): ?MailableInterface;

    /**
     * Gets the mailer driver instance.
     *
     * @return MailerDriverInterface
     */
    public function getMailer(): MailerDriverInterface;

    /**
     * Gets the instance of mail queue.
     *
     * @return MailQueueFactoryInterface
     */
    public function getQueueFactory(): MailQueueFactoryInterface;

    /**
     * Check if a current mail instance exists.
     *
     * @return bool
     */
    public function hasMailable(): bool;

    /**
     * Queues a mail.
     *
     * @param MailableInterface|array|null $mailableDef Used the current mail instance if mailableDef is null.
     * @param DateTimeInterface|string $date
     * @param array $context
     *
     * @return int
     */
    public function queue($mailableDef = null, $date = 'now', array $context = []): int;

    /**
     * Remove the registered mail instance by his name.
     *
     * @param string $name
     *
     * @return void
     */
    public function remove(string $name): void;

    /**
     * Send a mail.
     *
     * @param MailableInterface|array|null $mailableDef Used the current mail instance if mailableDef is null.
     *
     * @return boolean
     */
    public function send($mailableDef = null): bool;

    /**
     * Définition de la liste des paramètres globaux par défaut des emails.
     *
     * @param array $attrs
     *
     * @return static
     */
    public function setDefaults(array $attrs): MailManagerInterface;

    /**
     * Sets a new mail as current.
     *
     * @param MailableInterface|string|array|null $mailableDef if null used the existing current mail instance or a new
     *     current instance from defaults params.
     *
     * @return MailManagerInterface
     */
    public function setMailable($mailableDef = null): MailManagerInterface;

    /**
     * Sets the callback of the mailer driver configuration.
     *
     * @param callable $mailerConfigCallback
     *
     * @return static
     */
    public function setMailerConfigCallback(callable $mailerConfigCallback): MailManagerInterface;
}