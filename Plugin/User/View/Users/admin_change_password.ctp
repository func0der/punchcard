<?php $this->extend('Twb./Template/internal') ?>
<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __d('user', 'Instructor Change User Password'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input(
			'new_password',
			array(
				'type' => 'password',
			)
		);
		echo $this->Form->input(
			'new_password_confirm',
			array(
				'type' => 'password',
			)
		);
	?>
	</fieldset>
<?php echo $this->Form->end(__d('user', 'Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __d('user', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('user', 'List Users'), array('action' => 'index')); ?></li>
	</ul>
</div>
