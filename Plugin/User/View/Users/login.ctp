<?php $this->extend('Twb./Template/internal') ?>
<div class="users form">
<?php echo $this->Form->create('User');?>
	<?php
		echo $this->Form->input('email');
		echo $this->Form->input('password');
	?>
<?php echo $this->Form->end(__d('user', 'Submit'));?>
</div>
