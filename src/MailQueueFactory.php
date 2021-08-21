<?php

declare(strict_types=1);

namespace Pollen\Mail;

use Pollen\Support\Proxy\MailProxy;

/**
 * @todo
 */
class MailQueueFactory implements MailQueueFactoryInterface
{
    use MailProxy;

    /**
     * @param MailManagerInterface|null $mailManager
     */
    public function __construct(?MailManagerInterface $mailManager = null)
    {
        if ($mailManager !== null) {
            $this->setMailManager($mailManager);
        }

        /*db()->register('mail.queue', [
            'name'          => 'mail_queue',
            'install'       => true,
            'col_prefix'    => 'mq_',
            'meta'          => true,
            'columns'       => [
                'id'                => [
                    'type'              => 'BIGINT',
                    'size'              => 20,
                    'unsigned'          => true,
                    'auto_increment'    => true
                ],
                'session_id'        => [
                    'type'              => 'VARCHAR',
                    'size'              => 32,
                    'default'           => null
                ],
                'date_created'      => [
                    'type'              => 'DATETIME',
                    'default'           => '0000-00-00 00:00:00'
                ],
                'date_created_gmt'  => [
                    'type'              => 'DATETIME',
                    'default'           => '0000-00-00 00:00:00'
                ],
                'sending'           => [
                    'type'              => 'VARCHAR',
                    'size'              => 10,
                ],
                'params'            => [
                    'type'              => 'LONGTEXT'
                ]
            ]
        ]);*/

        /*cron()->register('mail.queue', [
            'title'         => __('File d\'expédition des emails', 'tify'),
            'description'   => __('Expédition des emails en partance de la file d\'attente.', 'tify'),
            'freq'    => [
                'id'            => 'every_minute',
                'interval'      => 60,
                'display'       => __('Chaque minute', 'tify')
            ],
            'command'        => function ($args, CronJob $job) {
                if ($queue = db('mail.queue')) :
                    if (
                        $emails = $queue->select()->rows(
                            [
                                'sending'   => [
                                    'value'     => (new \DateTime())->getTimestamp(),
                                    'compare'   => '<='
                                ],
                                'orderby'   => 'sending',
                                'order'     => 'ASC'
                            ]
                        )
                    ) :
                        foreach ($emails as $email) :
                            $params = unserialize(base64_decode($email->mq_params));
                            $queue->handle()->delete_by_id($email->mq_id);

                            // @var Mailer $mailer
                            $mailer = app()->get('mailer');
                            $mailer->send($params);

                            $job->log()->notice(__('Email expédié avec succès', 'tify'));
                        endforeach;
                    endif;
                endif;
            }
        ]); */
    }

    /**
     * @inheritDoc
     */
    public function add(MailableInterface $mail, $date = 'now', array $context = []): int
    {
        /*if ($queue = db('mail.queue')) {
            $id = 0;
            $session_id = uniqid('tFymq_', true);
            $date_created = (new DateTime(null,
                new \DateTimeZone(get_option('timezone_string'))))->format('Y-m-d H:i:s');
            $date_created_gmt = (new DateTime())->format('Y-m-d H:i:s');
            $sending = (new DateTime($date, new \DateTimeZone(get_option('timezone_string'))))->getTimestamp();
            $params = base64_encode(serialize($params));
            $data = compact(['id', 'session_id', 'date_created', 'date_created_gmt', 'sending', 'params', 'item_meta']);

            return ($insert_id = $queue->handle()->create($data))
                ? $queue->select()->cell_by_id($id, 'session_id')
                : 0;
        }*/

        return 0;
    }
}