<?php

declare(strict_types=1);

namespace Pollen\Mail;

use InvalidArgumentException;
use Pollen\Http\Response;
use Pollen\Http\ResponseInterface;
use Pollen\Http\UrlHelper;
use Pollen\Support\Concerns\BuildableTrait;
use Pollen\Support\ParamsBag;
use Pollen\Support\Concerns\ParamsBagAwareTrait;
use Pollen\Support\Proxy\MailProxy;
use Pollen\Support\Proxy\PartialProxy;
use Pollen\Support\Proxy\ViewProxy;
use Pollen\View\ViewInterface;
use RuntimeException;

class Mailable implements MailableInterface
{
    use BuildableTrait;
    use MailProxy;
    use ParamsBagAwareTrait;
    use PartialProxy;
    use ViewProxy;

    /**
     * Mailer driver instance.
     * @var MailerDriverInterface|null
     */
    private ?MailerDriverInterface $mailer = null;

    /**
     * Mail name identifier.
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * Message locale.
     * @var string|null
     */
    protected ?string $locale = null;

    /**
     * Message sender.
     * @var array<string, string>|null
     */
    protected ?array $from = null;

    /**
     * List of recipients of the message.
     * @var array<array<string, string>>|null
     */
    protected ?array $to = null;

    /**
     * List of carbon copy recipients of the message.
     * @var array<array<string, string>>|null
     */
    protected ?array $cc = null;

    /**
     * List of blind carbon copy recipients of the message.
     * @var array|null
     */
    protected ?array $bcc = null;

    /**
     * List of reply-to contact of the message.
     * @var array<array<string, string>>|null
     */
    protected ?array $replyTo = null;

    /**
     * List of attachments of the message.
     * @var string[]|null
     */
    protected ?array $attachments = null;

    /**
     * Charset of the message.
     * @var string|null
     */
    protected ?string $charset = null;

    /**
     * Encoding of the message.
     * @var string|null 8bit|7bit|binary|base64|quoted-printable|null
     */
    protected ?string $encoding = null;

    /**
     * Content type of the message.
     * @var string|null multipart/alternative|text/html|text/plain|null
     */
    protected ?string $contentType = null;

    /**
     * Subject of the message.
     * @var string|null
     */
    protected ?string $subject = null;

    /**
     * HTML content of the message.
     * @var string|null
     */
    protected ?string $html = null;

    /**
     * Plain text content of the message.
     * @var string|null
     */
    protected ?string $text = null;

    /**
     * Inline CSS formatting flag.
     * @var bool|null
     */
    protected ?bool $inlineCss = null;

    /**
     * CSS properties of the HTML message.
     * @var string|null
     */
    protected ?string $css = null;

    /**
     * Instance of template view datas.
     * @var ParamsBag|null
     */
    protected ?ParamsBag $datasBag = null;

    /**
     * Template view instance.
     * @var ViewInterface|null
     */
    protected ?ViewInterface $view = null;

    /**
     * @param MailManagerInterface|null $mailManager
     */
    public function __construct(?MailManagerInterface $mailManager = null)
    {
        if ($mailManager !== null) {
            $this->setMailManager($mailManager);
        }
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->message();
    }

    /**
     * @inheritDoc
     */
    public function build(): MailableInterface
    {
        if (!$this->isBuilt()) {
            $this->buildParams();
            $this->buildMailer();
            $this->buildRender();

            $this->setBuilt();
        }

        return $this;
    }

    /**
     * Build mailer driver parameters.
     *
     * @return void
     */
    protected function buildMailer(): void
    {
        $mailer = $this->getMailer();

        if ($this->from !== null) {
            $args = MailManager::parseContact($this->from)[0];
            $mailer->setFrom(...$args);
        }

        if ($this->to !== null) {
            $addresses = MailManager::parseContact($this->to);
            foreach ($addresses as $args) {
                $mailer->addTo(...$args);
            }
        }

        if ($this->replyTo !== null) {
            $addresses = MailManager::parseContact($this->replyTo);
            foreach ($addresses as $args) {
                $mailer->addReplyTo(...$args);
            }
        }

        if ($this->bcc !== null) {
            $addresses = MailManager::parseContact($this->bcc);
            foreach ($addresses as $args) {
                $mailer->addBcc(...$args);
            }
        }

        if ($this->cc !== null) {
            $addresses = MailManager::parseContact($this->cc);
            foreach ($addresses as $args) {
                $mailer->addCc(...$args);
            }
        }

        if ($this->attachments !== null) {
            $attachments = MailManager::parseAttachment($this->attachments);
            foreach ($attachments as $attachment) {
                $mailer->addAttachment($attachment);
            }
        }

        if ($this->charset !== null) {
            $mailer->setCharset($this->charset);
        }

        if ($this->encoding !== null) {
            $mailer->setEncoding($this->encoding);
        }

        if ($this->contentType !== null) {
            $mailer->setContentType($this->contentType);
        }

        if ($this->subject !== null) {
            $mailer->setSubject($this->subject);
        }
    }

    /**
     * Build the mail parameters.
     *
     * @return void
     */
    protected function buildParams(): void
    {
        if (($oldName = $this->name) && ($newName = $this->params('name')) && $this->mail($this->name)) {
            $this->name = $newName;
            $this->mail()->add($this);
            $this->mail()->remove($oldName);
        }

        if ($this->from === null) {
            $this->paramsBuilder('from', [$this, 'setFrom']);
        }

        if ($this->to === null) {
            $this->paramsBuilder('to', [$this, 'setTo']);
        }

        if ($this->replyTo === null) {
            $this->paramsBuilder('reply-to', [$this, 'setReplyTo']);
        }

        if ($this->bcc === null) {
            $this->paramsBuilder('bcc', [$this, 'setBcc']);
        }

        if ($this->cc === null) {
            $this->paramsBuilder('cc', [$this, 'setCc']);
        }

        if ($this->attachments === null) {
            $this->paramsBuilder('attachments', [$this, 'setAttachments']);
        }

        if ($this->locale === null) {
            $this->paramsBuilder('locale', [$this, 'setLocale']);
        }

        if ($this->charset === null) {
            $this->paramsBuilder('charset', [$this, 'setCharset']);
        }

        if ($this->encoding === null) {
            $this->paramsBuilder('encoding', [$this, 'setEncoding']);
        }

        if ($this->contentType === null) {
            $this->paramsBuilder('content_type', [$this, 'setContentType']);
        }

        if ($this->subject === null) {
            $this->paramsBuilder('subject', [$this, 'setSubject']);
        }
    }

    /**
     * Build the mail render.
     *
     * @return void
     */
    protected function buildRender(): void
    {
        /** @var ParamsBag $dataBag */
        $dataBag = $this->datas();

        $dataBag->set($this->params('datas', []));

        if ($this->inlineCss === null) {
            $this->paramsBuilder('inline_css', [$this, 'setInlineCss']);
        }

        if ($this->css === null && (($css = $this->params('css')) !== null)) {
            if (is_string($css)) {
                $this->setCss($css);
            } elseif ($css !== false) {
                $this->setCss(file_get_contents($this->mail()->resources('/assets/css/default.css')));
            }
        }

        if ($this->html === null && (($html = $this->params('html')) !== null)) {
            if (is_array($html)) {
                if ($body = $html['body'] ?? true) {
                    $body = is_string($body) ? $body : $this->view('html/body', $dataBag->all());
                }

                if ($header = $html['header'] ?? true) {
                    $header = is_string($header) ? $header : $this->view('html/header', $dataBag->all());
                }

                if ($footer = $html['footer'] ?? true) {
                    $footer = is_string($footer) ? $footer : $this->view('html/footer', $dataBag->all());
                }
                $html = $this->view(
                    'html/message',
                    array_merge($dataBag->all(), compact('body', 'header', 'footer'))
                );
            } elseif ($html !== false) {
                $html = is_string($html) ? $html : $this->view('html/message', $dataBag->all());
            }
        } elseif ($this->html === null) {
            $html = $this->params('text') ?: $this->view('html/message', $dataBag->all());
        } else {
            $html = $this->html;
        }

        if (!MailManager::hasHtmlHead($html)) {
            $html = $this->view('html/message', array_merge($dataBag->all(), ['body' => $html]));
        }

        if ($this->text === null) {
            $text = $this->params('text');

            if (is_string($text)) {
                $this->setText($text);
            } elseif ($text !== false) {
                $this->setText(MailManager::htmlToText($html ?: $this->view('text/message', $dataBag->all())));
            }
        }

        if ($html) {
            if ($this->css && $this->inlineCss) {
                try {
                    $html = MailManager::htmlInlineCss($html, $this->css);
                } catch (RuntimeException $e) {
                    unset($e);
                }
            }

            $this->setHtml($html);
        }

        $mailer = $this->getMailer();

        switch ($this->contentType) {
            default:
            case 'multipart/alternative' :
                $mailer->setHtml($this->html);
                $mailer->setText($this->text);
                break;
            case 'text/html' :
                $mailer->setHtml($this->text);
                break;
            case 'text/plain' :
                $mailer->setText($this->text);
                break;
        }
    }

    /**
     * @inheritDoc
     */
    public function datas($key = null, $default = null)
    {
        if (!$this->datasBag instanceof ParamsBag) {
            $this->datasBag = new ParamsBag(
                array_merge(
                    ['app_url' => (new UrlHelper())->getAbsoluteUrl()],
                    $this->mail()->defaults('datas', [])
                )
            );
        }

        if (is_null($key)) {
            return $this->datasBag;
        }

        if (is_string($key)) {
            return $this->datasBag->get($key, $default);
        }

        if (is_array($key)) {
            $this->datasBag->set($key);
            return $this->datasBag;
        }

        throw new InvalidArgumentException('Invalid Mailable DatasBag passed method arguments.');
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
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
             * {@internal false to disable|true to use the text/message template|string of text content.}
             * @var bool|string|null
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
             * @var bool|string
             */
            'css'          => true,
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
        ];
    }

    /**
     * @inheritDoc
     */
    public function debug(): string
    {
        /** @var MailableInterface $mailable */
        $mailable = $this->mail($this->build());

        $mailable->getMailer()->prepare();

        return $this->view(
            'debug',
            ['html' => $this->html, 'text' => $this->text]
        );
    }

    /**
     * Generate a uniq name identifier.
     *
     * @return string
     */
    private function generateName(): string
    {
        return sha1(get_class($this) . count($this->mail()->all()));
    }

    /**
     * @inheritDoc
     */
    public function getCssProperties(): string
    {
        return !$this->inlineCss ? (string) $this->css : '';
    }

    /**
     * @inheritDoc
     */
    public function getLocale(): string
    {
        return $this->locale ?? 'en';
    }

    /**
     * @inheritDoc
     */
    public function getMailer(): MailerDriverInterface
    {
        if ($this->mailer === null) {
            $this->mailer = $this->mail()->getMailer();
        }
        return $this->mailer;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        if ($this->name === null) {
            $this->name = (($name = $this->params()->pull('name')) && is_string($name)) ? $name : $this->generateName();
        }

        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function queue($date = 'now', array $params = []): int
    {
        $queueFactory = $this->mail()->getQueueFactory();

        $this->mail($this->build());

        return $queueFactory->add($this, $date, $params);
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        /** @var MailableInterface $mailable */
        $mailable = $this->mail($this->build());

        return $mailable->getMailer()->getMessage();
    }

    /**
     * Parameters builder.
     * {@internal Sets a parameter based on the custom value exists or the default value in mail manager or the
     * provided a fallback value.}
     *
     * @param string $param
     * @param callable $setter
     * @param null $fallbackValue
     *
     * @return void
     */
    private function paramsBuilder(string $param, callable $setter, $fallbackValue = null): void
    {
        /** @var ParamsBag $defaultBag */
        $defaultBag = $this->mail()->defaults();

        if((($value = $this->params($param)) !== null)) {
            $setter($value);
        } elseif ($defaultBag->has($param)) {
            $setter($defaultBag->get($param));
        } elseif ($fallbackValue !== null) {
            $setter($fallbackValue);
        }
    }

    /**
     * @inheritDoc
     */
    public function response(): ResponseInterface
    {
        return new Response($this->message());
    }

    /**
     * @inheritDoc
     */
    public function send(): bool
    {
        /** @var MailableInterface $mail */
        $mailable = $this->mail($this->build());

        return $mailable->getMailer()->send();
    }

    /**
     * @inheritDoc
     */
    public function setAttachments($attachments): MailableInterface
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setBcc($bcc): MailableInterface
    {
        $this->bcc = $bcc;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCc($cc): MailableInterface
    {
        $this->cc = $cc;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCharset(string $charset): MailableInterface
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContentType(string $contentType): MailableInterface
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCss(string $css): MailableInterface
    {
        $this->css = $css;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setEncoding(string $encoding): MailableInterface
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setFrom($from): MailableInterface
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHtml(string $html): MailableInterface
    {
        $this->html = $html;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setInlineCss(bool $inlineCss = true): MailableInterface
    {
        $this->inlineCss = $inlineCss;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLocale(string $locale): MailableInterface
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMailer(MailerDriverInterface $mailer): MailableInterface
    {
        $this->mailer = $mailer;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setReplyTo($replyTo): MailableInterface
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSubject(string $subject): MailableInterface
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setText(string $text): MailableInterface
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTo($to): MailableInterface
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function view(?string $name = null, array $data = [])
    {
        if ($this->view === null) {
            $this->view = $this->viewResolver();
        }

        if (func_num_args() === 0) {
            return $this->view;
        }

        return $this->view->render($name, $data);
    }

    /**
     * Resolves view instance.
     *
     * @return ViewInterface
     */
    protected function viewResolver(): ViewInterface
    {
        $default = $this->mail()->config('view', []);
        $viewDef = $this->params('view');

        if (!$viewDef instanceof ViewInterface) {
            $directory = $this->params('view.directory');
            if ($directory && !file_exists($directory)) {
                $directory = null;
            }

            $overrideDir = $this->params('view.override_dir');
            if ($overrideDir && !file_exists($overrideDir)) {
                $overrideDir = null;
            }

            if ($directory === null && isset($default['directory'])) {
                $default['directory'] = rtrim($default['directory'], '/');
                if (file_exists($default['directory'])) {
                    $directory = $default['directory'];
                }
            }

            if ($overrideDir === null && isset($default['override_dir'])) {
                $default['override_dir'] = rtrim($default['override_dir'], '/');
                if (file_exists($default['override_dir'])) {
                    $overrideDir = $default['override_dir'];
                }
            }

            if ($directory === null) {
                $directory = $this->mail()->resources('/views/mailable');
                if (!file_exists($directory)) {
                    throw new InvalidArgumentException(
                        sprintf('Mailable class [%s] must have an accessible view directory.', __CLASS__)
                    );
                }
            }

            $view = $this->viewManager()->createView('plates')
                ->setDirectory($directory);

            if ($overrideDir !== null) {
                $view->setOverrideDir($overrideDir);
            }
        } else {
            $view = $viewDef;
        }

        return $this->viewAddExtensions($view);
    }

    /**
     * Add mailable view extensions.
     *
     * @param ViewInterface $view
     *
     * @return ViewInterface
     */
    public function viewAddExtensions(ViewInterface $view): ViewInterface
    {
        $functions = [
            'getCssProperties',
            'getLocale'
        ];
        foreach ($functions as $fn) {
            $view->addExtension($fn, [$this, $fn]);
        }

        $mailerFunctions = [
            'getAttachments',
            'getBcc',
            'getCc',
            'getCharset',
            'getContentType',
            'getEncoding',
            'getFrom',
            'getFromEmail',
            'getFromName',
            'getHeaders',
            'getHtml',
            'getMessage',
            'getReplyTo',
            'getSubject',
            'getText',
            'getTo',
            'hasHtml',
            'hasText',
            'linearizeContact',
            'linearizeContacts',
        ];
        foreach ($mailerFunctions as $fn) {
            $view->addExtension($fn, [$this->getMailer(), $fn]);
        }

        return $view;
    }
}
