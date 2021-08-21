<?php
/**
 * @var Pollen\Metabox\MetaboxTemplate $this
 */
?>
<?php if ($info = $this->get('info', true)) : ?>
    <em><?php echo $info; ?></em>
    <hr>
<?php endif; ?>

<?php if ($this->get('enabled.activation', true)) : ?>
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><?php _e('Activation', 'tify'); ?></th>
            <td>
                <?php echo field('toggle-switch', [
                    'name'  => $this->getName() . '[enabled]',
                    'value' => filter_var($this->getValue('enabled'), FILTER_VALIDATE_BOOL) ? 'on' : 'off',
                ]); ?>
            </td>
        </tr>
        </tbody>
    </table>
<?php endif; ?>

<?php if ($this->get('enabled.sender', true)) : ?>
    <h3><?php echo $this->get('sender.title'); ?></h3>

    <?php if ($info = $this->get('sender.info')) : ?>
        <em><?php echo $info; ?></em>
    <?php endif; ?>

    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><?php _e('Email (requis)', 'tify'); ?></th>
            <td>
                <?php echo field('text', [
                    'name'  => $this->getName() . '[sender][email]',
                    'value' => $this->getValue('sender.email'),
                    'attrs' => [
                        'size'         => 40,
                        'autocomplete' => 'off',
                    ],
                ]); ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Nom (optionnel)', 'tify'); ?></th>
            <td>
                <?php echo field('text', [
                    'name'  => $this->getName() . '[sender][name]',
                    'value' => $this->getValue('sender.name'),
                    'attrs' => [
                        'size'         => 40,
                        'autocomplete' => 'off',
                    ],
                ]); ?>
            </td>
        </tr>
        </tbody>
    </table>
<?php endif; ?>

<?php if ($this->get('enabled.recipients', true)) : ?>
    <h3><?php echo $this->get('recipients.title'); ?></h3>

    <?php if ($info = $this->get('recipients.info')) : ?>
        <em><?php echo $info; ?></em>
    <?php endif; ?>

    <?php echo field('repeater', [
        'button' => [
            'content' => __('Ajouter un destinataire', 'tify'),
        ],
        'name'   => $this->getName() . '[recipients]',
        'value'  => $this->getValue('recipients'),
        'viewer' => [
            'override_dir' => dirname($this->path()) . '/repeater',
        ],
    ]); ?>
<?php endif;