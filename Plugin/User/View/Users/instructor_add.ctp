<?php $this->extend('Twb./Template/internal') ?>
<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __d('user', 'Instructor Add User'); ?></legend>
	<?php
		echo $this->Form->input('email');
		echo $this->Form->input('password');
		echo $this->Form->input('forename');
		echo $this->Form->input('surname');
		echo $this->Form->input('entry_date');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__d('user', 'Submit')); ?>
</div>