<?php $this->extend('Twb./Template/internal') ?>
<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __d('user', 'Instructor Edit User'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('parent_id');
		echo $this->Form->input('department_id');
		echo $this->Form->input('email');
		echo $this->Form->input('forename');
		echo $this->Form->input('surname');
		echo $this->Form->input('is_instructor');
		echo $this->Form->input('is_admin');
		echo $this->Form->input('entry_date');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__d('user', 'Submit')); ?>
</div>
