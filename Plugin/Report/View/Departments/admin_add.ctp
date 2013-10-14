<?php $this->extend('Twb./Template/internal') ?>
<div class="departments form">
<?php echo $this->Form->create('Department'); ?>
	<fieldset>
		<legend><?php echo __d('report', 'Admin Add Department'); ?></legend>
	<?php
		echo $this->Form->input('label');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__d('report', 'Submit')); ?>
</div>
