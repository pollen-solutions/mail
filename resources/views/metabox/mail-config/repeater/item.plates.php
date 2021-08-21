<?php
/**
 * @var Pollen\Field\FieldTemplate $this
 * @var string $name
 * @var string $index
 * @var string $value
 */
?>
<table class="form-table">
    <tbody>
    <tr>
        <th scope="row"><?php _e('Email (requis)', 'tify'); ?></th>
        <td>
            <?php echo $this->field('text', [
                'name'  => "{$this->getName()}[{$this->get('index')}][email]",
                'value' => $this->get('value.email', ''),
                'attrs' => [
                    'autocomplete' => 'off',
                    'size'         => 40
                ],
            ]); ?>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php _e('Nom (optionnel)', 'tify'); ?></th>
        <td>
            <?php echo $this->field('text', [
                'name'  => "{$this->getName()}[{$this->get('index')}][name]",
                'value' => $this->get('value.name', ''),
                'attrs' => [
                    'autocomplete' => 'off',
                    'size'         => 40
                ],
            ]); ?>
        </td>
    </tr>
    </tbody>
</table>