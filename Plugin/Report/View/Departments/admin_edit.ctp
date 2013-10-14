<?php $this->extend('Twb./Template/internal') ?>
<div class="departments form">
<?php echo $this->Form->create('Department'); ?>
	<fieldset>
		<legend><?php echo __d('report', 'Admin Edit Department'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('label');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__d('report', 'Submit')); ?>
</div>
