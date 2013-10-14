<?php $this->extend('Twb./Template/internal') ?>
<div class="users index">
	<h2><?php echo __d('user', 'Users'); ?></h2>
	<table class="table table-bordered table-striped table-hover" cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('department_id'); ?></th>
			<th><?php echo $this->Paginator->sort('forename'); ?></th>
			<th><?php echo $this->Paginator->sort('surname'); ?></th>
			<th><?php echo $this->Paginator->sort('entry_date'); ?></th>
			<th><?php echo $this->Paginator->sort('active'); ?></th>
			<th class="actions"><?php echo __d('user', 'Actions'); ?></th>
	</tr>
	<?php foreach ($users as $user): ?>
	<tr>
		<td>
		<?php
			echo $user['Department']['label'];
		?>
		</td>
		<td><?php echo h($user['User']['forename']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['surname']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['entry_date']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['active']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__d('user', 'Edit'), array('action' => 'edit', $user['User']['id'])); ?>
			<?php echo $this->Html->link(__d('user', 'Change password'), array('action' => 'change_password', $user['User']['id'])); ?>
			<?php echo $this->Form->postLink(__d('user', 'Delete'), array('action' => 'delete', $user['User']['id']), null, __d('user', 'Are you sure you want to delete # %s?', $user['User']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __d('user', 'Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __d('user', 'previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__d('user', 'next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __d('user', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('user', 'New User'), array('action' => 'add')); ?></li>
	</ul>
</div>
