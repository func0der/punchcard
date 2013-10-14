<?php $this->extend('Twb./Template/internal') ?>
<div class="users form">
<?php
	echo $this->Form->create(
		'User',
		array(
			'type' => 'file'
		)
	); 
?>
	<fieldset>
		<legend><?php echo __d('user', 'Instructor Edit Profile'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('email');
		echo $this->Form->input('forename');
		echo $this->Form->input('surname');
		echo $this->Form->label('password');
		echo $this->Html->link(
			__d('user', 'Change password'),
			array(
				'action' => 'change_password',
				$this->Form->value('id'),
			),
			array(
				'id' => 'UserPassword',
			)
		);
		echo $this->Form->input(
			'signature_cert',
			array(
				'after' => (($signatureData && $signatureData['cert']) ? __d('user', 'Already uploaded.') : 'Missing'),
				'label' => __d('user', 'Signature certificate'),
				'type' => 'file',
			)
		);
		echo $this->Form->input(
			'signature_image',
			array(
				'after' => (($signatureData && $signatureData['image']) ? __d('user', 'Already uploaded.') : 'Missing'),
				'label' => __d('user', 'Signature image'),
				'type' => 'file',
			)
		);
	?>
	<div class="note">
	<?php
		echo __d('report', 'If you upload a new signature, please, upload both, certificate and image, at the same time');
	?>
	</div>
	</fieldset>
<?php echo $this->Form->end(__d('user', 'Submit')); ?>
</div>
