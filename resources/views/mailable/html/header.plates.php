<?php
/**
 * @var Pollen\Mail\MailableTemplateInterface $this
 */
?>
<?php if ($logo = $this->get('infos.logo')) : ?>
    <tr class="rowHeaderContent">
        <td>
            <?php echo is_array($logo) ? $this->partial('tag', [
                'attrs' => [
                    'class'  => 'BodyHeader-logo',
                    'src'    => $logo['src'] ?? '',
                    'width'  => $logo['width'] ?? 200,
                    'height' => $logo['height'] ?? 40,
                    'alt'    => $logo['alt'] ?? 'logo',
                    'border' => 0,
                ],
                'tag'   => 'img',
            ]) : (string)$logo; ?>
        </td>
    </tr>
<?php endif;