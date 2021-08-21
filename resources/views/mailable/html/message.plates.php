<?php
/**
 * @var Pollen\Mail\MailableTemplateInterface $this
 */

?>
<?php $this->layout('html/layout', $this->all()); ?>

<?php $this->start('header'); ?>
<?php if ($this->get('header') !== false) : ?>
    <?php echo is_string($this->get('header')) ? $this->get('header') : $this->fetch('html/header', $this->all()); ?>
<?php endif; ?>
<?php $this->end(); ?>

<?php $this->start('footer'); ?>
<?php if ($this->get('footer') !== false) : ?>
    <?php echo is_string($this->get('footer')) ? $this->get('footer') : $this->fetch('html/footer', $this->all()); ?>
<?php endif; ?>
<?php $this->end(); ?>

<?php if ($this->get('body') !== false) : ?>
    <?php echo is_string($this->get('body')) ? $this->get('body') : $this->fetch('html/body', $this->all()); ?>
<?php endif; ?>