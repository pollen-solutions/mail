<?php
/**
 * @var Pollen\Mail\MailableTemplateInterface $this
 */

?>
<tr class="rowBodyContent">
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr class="rowBodyContent-section rowBodyContent-section--header">
                <td>
                    <h1>
                        Mail test
                    </h1>
                </td>
            </tr>

            <tr class="rowBodyContent-section rowBodyContent-section--body">
                <td>
                    <p>
                        If this email has reached you, it means that a sending test has been sent from the site :
                    </p>
                    <p>
                        <?php if ($app_url = $this->get('app_url')) : ?>
                        <?php echo $this->partial('tag', [
                            'attrs'   => [
                                'clicktracking' => 'off',
                                'href'          => $app_url,
                                'rel'           => 'noopener',
                                'target'        => '_blank',
                                'title'         => 'Visit the website',
                            ],
                            'content' => $app_url,
                            'tag'     => 'a',
                        ]); ?>
                        <?php else : ?>
                            Sorry, the website url is unknown ...
                        <?php endif; ?>
                    </p>
                    <p>
                        If this is an error and you are not affected by this message, please notify the sender :
                    </p>
                    <p>
                        <?php echo $this->partial('tag', [
                            'attrs'   => [
                                'clicktracking' => 'off',
                                'href'          => 'mailto:' . $this->getFromEmail(),
                                'target'        => '_blank',
                                'title'         => 'Notify the sender',
                            ],
                            'content' => $this->linearizeContact(...$this->getFrom()),
                            'tag'     => 'a',
                        ]); ?>
                    </p>
                    <br>
                    <p>Thank you for your understanding.</p>
                </td>
            </tr>

            <tr class="rowBodyContent-section rowBodyContent-section--footer">
                <td>
                    <table align="center" cellspacing="0" cellpadding="0" border="0" role="presentation"
                           style="margin: auto;">
                        <tr>
                            <td>
                                <br>
                                <em style="font-size:0.9em;color:#999;">
                                    This email was generated with Pollen Solutions Mail component
                                </em>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>