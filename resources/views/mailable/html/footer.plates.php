<?php
/**
 * @var Pollen\Mail\MailableTemplateInterface $this
 */
?>
<?php if ($this->get('infos')) : ?>
    <tr class="rowFooterContent">
        <td>
            <?php if ($this->get('infos.company.name')) : ?>
                <b><?php echo strtoupper($this->get('infos.company.name')); ?></b>
                <br>
            <?php endif; ?>

            <?php if (
                $this->get('infos.contact.address1') ||
                $this->get('infos.contact.address2') ||
                $this->get('infos.contact.address3') ||
                $this->get('infos.contact.postcode') ||
                $this->get('infos.contact.city')
            ): ?>
                <span class="unstyle-auto-detected-links">
                    <?php echo implode(
                        ' ',
                        array_filter(
                            [
                                $this->get('infos.contact.address1', ''),
                                $this->get('infos.contact.address2', ''),
                                $this->get('infos.contact.address3', ''),
                                $this->get('infos.contact.postcode', ''),
                                strtoupper($this->get('infos.contact.city', '')),
                            ]
                        )
                    ); ?>
                </span>
                <br>
            <?php endif; ?>

            <?php if ($this->get('infos.contact.phone') || $this->get('infos.contact.fax')) : ?>
                <span class="unstyle-auto-detected-links">
                    <?php echo implode(
                        ' - ',
                        array_filter(
                            [
                                ($phone = $this->get('infos.contact.phone')) ? sprintf('Phone: %s', $phone) : '',
                                ($fax = $this->get('infos.contact.fax')) ? sprintf('Fax : %s', $fax) : '',
                            ]
                        )
                    ); ?>
                </span>
            <?php endif; ?>
        </td>
    </tr>
<?php endif;