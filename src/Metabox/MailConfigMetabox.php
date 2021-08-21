<?php

declare(strict_types=1);

namespace Pollen\Mail\Metabox;

use Pollen\Mail\MailManagerInterface;
use Pollen\Metabox\MetaboxDriver;
use Pollen\Metabox\MetaboxManagerInterface;

/**
 * @todo
 */
class MailConfigMetabox extends MetaboxDriver
{
    /**
     * Mail manager instance.
     * @var MailManagerInterface
     */
    protected ?MailManagerInterface $mailManager;

    /**
     * @var string
     */
    protected string $name = 'mail_config';

    /**
     * @param MailManagerInterface $mailManager
     * @param MetaboxManagerInterface $metabox
     */
    public function __construct(MailManagerInterface $mailManager, MetaboxManagerInterface $metabox)
    {
        $this->mailManager = $mailManager;

        parent::__construct($metabox);
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'enabled'    => [
                    'activation' => true,
                    'sender'     => true,
                    'recipients' => true,
                ],
                'info'       => '',
                'sender'     => [
                    'title' => 'Réglages de l\'expéditeur',
                ],
                'recipients' => [
                    'title' => 'Réglages des destinataires',
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getDefaultValue()
    {
        return array_merge(['enabled' => true], $this->defaultValue ?: []);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? 'Mail configuration';
    }

    /**
     * Returns mail manager instance.
     *
     * @return MailManagerInterface
     */
    public function mailManager(): MailManagerInterface
    {
        return $this->mailManager;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $defaultMail = $this->mailManager()->getDefaults('to');

        if (isset($defaultMail[0])) {
            if (!$this->get('sender.default.email')) {
                $this->set(['sender.default.email' => $defaultMail[0]]);
            }
            if (!$this->get('sender.default.name')) {
                $this->set(['sender.default.name' => $defaultMail[1] ?? '']);
            }
            if (!$this->get('sender.info')) {
                $this->set(
                    [
                        'sender.info' => sprintf(
                            'Par défaut : %s',
                            implode(
                                ' - ',
                                array_filter([$defaultMail[0], $defaultMail[1] ?? ''])
                            )
                        ),
                    ]
                );
            }
            if (!$this->get('recipients.default.email')) {
                $this->set(['recipients.default.email' => $defaultMail[0]]);
            }
            if (!$this->get('recipients.default.name')) {
                $this->set(['recipients.default.name' => $defaultMail[1] ?? '']);
            }
            if (!$this->get('recipients.info')) {
                $this->set(
                    [
                        'recipients.info' => sprintf(
                            'Par défaut : %s',
                            implode(
                                ' - ',
                                array_filter([$defaultMail[0], $defaultMail[1] ?? ''])
                            )
                        ),
                    ]
                );
            }
        }
        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->mailManager()->resources('views/metabox/mail-config');
    }
}