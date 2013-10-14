<?php $this->extend('Twb./Template/internal') ?>
<div class="departments view">
<h2><?php  echo __d('report', 'Department'); ?></h2>
	<dl>
		<dt><?php echo __d('report', 'Label'); ?></dt>
		<dd>
			<?php echo h($department['Department']['label']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="related">
	<h3><?php echo __d('report', 'Related Users'); ?></h3>
	<?php if (!empty($department['User'])): ?>
	<table class="table table-bordered table-striped table-hover" cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __d('report', 'Email'); ?></th>
		<th><?php echo __d('report', 'Forename'); ?></th>
		<th><?php echo __d('report', 'Surname'); ?></th>
		<th class="actions"><?php echo __d('report', 'Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($department['User'] as $user): ?>
		<tr>
			<td><?php echo $user['email']; ?></td>
			<td><?php echo $user['forename']; ?></td>
			<td><?php echo $user['surname']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__d('report', 'Edit'), array('plugin' => 'user', 'controller' => 'users', 'action' => 'edit', $user['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>
</div>
