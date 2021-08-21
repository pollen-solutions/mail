<?php

declare(strict_types=1);

namespace Pollen\Mail;

use Html2Text\Html2Text;
use InvalidArgumentException;
use Pelago\Emogrifier\CssInliner;
use Pollen\Mail\Drivers\PhpMailerDriver;
use Pollen\Support\Arr;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Concerns\ResourcesAwareTrait;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\Proxy\ContainerProxy;
use Pollen\Support\ParamsBag;
use Pollen\Validation\Validator as v;
use Psr\Container\ContainerInterface as Container;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

class MailManager implements MailManagerInterface
{
    use ConfigBagAwareTrait;
    use ResourcesAwareTrait;
    use ContainerProxy;

    /**
     * Mail manager main instance.
     * @var MailManagerInterface|null
     */
    private static ?MailManagerInterface $instance = null;

    /**
     * Default params bag instance.
     * @var ParamsBag|null
     */
    protected ?ParamsBag $defaultsBag = null;

    /**
     * Current mail instance.
     * @var MailableInterface|null
     */
    protected ?MailableInterface $mailable = null;

    /**
     * List of registered mail instances.
     * @var array<string, MailableInterface>|array
     */
    protected array $mailables = [];

    /**
     * Mailer driver configuration callback.
     * @var callable|null
     */
    protected $mailerConfigCallback;

    /**
     * Queue manager instance.
     * @var MailQueueFactoryInterface|null
     */
    protected ?MailQueueFactoryInterface $queueFactory = null;

    /**
     * @param array $config
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if ($container !== null) {
            $this->setContainer($container);
        }

        $this->setResourcesBaseDir(dirname(__DIR__) . '/resources');

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Retrieve the mail manager main instance.
     *
     * @return static
     */
    public static function getInstance(): MailManagerInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * Check if HEAD tag exists in a HTML content.
     *
     * @param string $html
     *
     * @return bool
     */
    public static function hasHtmlHead(string $html): bool
    {
        return (bool)(new Crawler($html))->filter('head')->count();
    }

    /**
     * Passes the CSS properties declaration to the HTML tags of a content.
     *
     * @param string $html
     * @param string $css
     *
     * @return string
     */
    public static function htmlInlineCss(string $html, string $css): string
    {
        try {
            return CssInliner::fromHtml($html)->inlineCss($css)->render();
        } catch (Throwable $e) {
            throw new RuntimeException('Mailer HTML message inline CSS convertion throws an exception.', 0, $e);
        }
    }

    /**
     * Converts HTML content to plain text content.
     *
     * @param string $html
     *
     * @return string
     */
    public static function htmlToText(string $html): string
    {
        return (new Html2Text($html))->getText();
    }

    /**
     * Parses a list of contact.
     *
     * @param string|string[]|array $contact
     *
     * @return array|null
     */
    public static function parseContact($contact): ?array
    {
        $output = (func_num_args() === 2) ? func_get_arg(1) : [];

        if (is_string($contact)) {
            $email = '';
            $name = '';
            $bracket_pos = strpos($contact, '<');
            if ($bracket_pos !== false) {
                if ($bracket_pos > 0) {
                    $name = substr($contact, 0, $bracket_pos - 1);
                    $name = str_replace('"', '', $name);
                    $name = trim($name);
                }

                $email = substr($contact, $bracket_pos + 1);
                $email = str_replace('>', '', $email);
                $email = trim($email);
            } elseif (!empty($contact)) {
                $email = $contact;
            }

            if ($email && v::email()->validate($email)) {
                $output[] = array_filter([$email, $name]);
            }
        } elseif (is_array($contact)) {
            if (!Arr::isAssoc($contact)) {
                if ((count($contact) === 2) && is_string($contact[0]) && is_string($contact[1]) &&
                    v::email()->validate($contact[0]) && !v::email()->validate($contact[1])
                ) {
                    $output[] = $contact;
                } else {
                    foreach ($contact as $c) {
                        if ($value = static::parseContact($c, $output)) {
                            $output = $value;
                        }
                    }
                }
            } else {
                $email = $contact['email'] ?? null;

                if (v::email()->validate($email)) {
                    $output[] = array_filter([$email, $contact['name'] ?? null]);
                }
            }
        }

        return array_filter($output) ?: null;
    }

    /**
     * Parses a list of attachment.
     *
     * @param string|string[]|array $attachments
     *
     * @return array
     */
    public static function parseAttachment($attachments): array
    {
        $output = (func_num_args() === 2) ? func_get_arg(1) : [];

        if (is_string($attachments)) {
            if (is_file($attachments)) {
                $output[] = $attachments;
            }
        } elseif (is_array($attachments)) {
            foreach ($attachments as $a) {
                if (is_string($a)) {
                    $output = static::parseAttachment($a, $output);
                } elseif (is_array($a)) {
                    $filename = $a[0] ?? null;

                    if ($filename && is_file($filename)) {
                        $output[] = $a;
                    }
                }
            }
        }

        return $output;
    }

    /** ------------------------------------------------------------------------------------------------------------- */

    /**
     * @inheritDoc
     */
    public function add(MailableInterface $mailable): MailManagerInterface
    {
        $this->mailables[$mailable->getName()] = $mailable;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->mailables;
    }

    /**
     * @inheritDoc
     */
    public function debug($mailableDef = null): void
    {
        $this->setMailable($mailableDef);

        echo $this->mailable->debug();
        exit;
    }

    /**
     * @inheritDoc
     */
    public function defaults($key = null, $default = null)
    {
        if (!$this->defaultsBag instanceof ParamsBag) {
            $this->defaultsBag = new ParamsBag();
        }

        if (is_null($key)) {
            return $this->defaultsBag;
        }

        if (is_string($key)) {
            return $this->defaultsBag->get($key, $default);
        }

        if (is_array($key)) {
            $this->defaultsBag->set($key);
            return $this->defaultsBag;
        }

        throw new InvalidArgumentException('Invalid Mailer DefaultsBag passed method arguments.');
    }

    /**
     * @inheritDoc
     */
    public function get(?string $name = null): ?MailableInterface
    {
        if ($name === null) {
            $this->setMailable();

            return $this->mailable;
        }

        return $this->mailables[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getMailer(): MailerDriverInterface
    {
        $mailer = $this->containerHas(MailerDriverInterface::class)
            ? $this->containerGet(MailerDriverInterface::class) : new PhpMailerDriver();

        $mailerConfig = $this->mailerConfigCallback;
        if ($mailerConfig && is_callable($mailerConfig)) {
            $mailerConfig($mailer);
        }

        return $mailer;
    }

    /**
     * @inheritDoc
     */
    public function getQueueFactory(): MailQueueFactoryInterface
    {
        if ($this->queueFactory === null) {
            $this->queueFactory = $this->containerHas(MailQueueFactoryInterface::class)
                ? $this->containerGet(MailQueueFactoryInterface::class) : new MailQueueFactory($this);
        }

        return $this->queueFactory;
    }

    /**
     * @inheritDoc
     */
    public function hasMailable(): bool
    {
        return (bool)$this->mailable;
    }

    /**
     * @inheritDoc
     */
    public function queue($mailableDef = null, $date = 'now', array $context = []): int
    {
        $this->setMailable($mailableDef);

        return $this->mailable->queue($date, $context);
    }

    /**
     * @inheritDoc
     */
    public function remove(string $name): void
    {
        unset($this->mailables[$name]);
    }

    /**
     * @inheritDoc
     */
    public function send($mailableDef = null): bool
    {
        $this->setMailable($mailableDef);

        return $this->mailable->send();
    }

    /**
     * @inheritDoc
     */
    public function setDefaults(array $attrs): MailManagerInterface
    {
        $this->defaults($attrs);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMailable($mailableDef = null): MailManagerInterface
    {
        if ($this->mailable instanceof MailableInterface && $mailableDef === null) {
            return $this;
        }

        if ($mailableDef instanceof Mailable) {
            $this->mailable = $mailableDef;
        } elseif (is_string($mailableDef)) {
            if ($mailable = $this->get($mailableDef)) {
                $this->mailable = $mailable;

                return $this;
            }
            throw new RuntimeException(sprintf('Mailable [%s] does not exists.', $mailableDef));
        } else {
            $mailableParams = array_merge(
                $this->config()->all(),
                is_array($mailableDef) ? $mailableDef : []
            );

            $this->mailable = $this->containerHas(MailableInterface::class)
                ? $this->containerGet(MailableInterface::class) : new Mailable($this);

            $this->mailable->setParams($mailableParams);
        }

        return $this->add($this->mailable);
    }

    /**
     * @inheritDoc
     */
    public function setMailerConfigCallback(callable $mailerConfigCallback): MailManagerInterface
    {
        $this->mailerConfigCallback = $mailerConfigCallback;

        return $this;
    }
}