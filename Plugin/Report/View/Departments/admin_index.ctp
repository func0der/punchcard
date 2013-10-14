<?php $this->extend('Twb./Template/internal') ?>
<div class="departments index">
	<h2><?php echo __d('report', 'Departments'); ?></h2>
	<table class="table table-bordered table-striped table-hover" cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('label'); ?></th>
			<th><?php echo $this->Paginator->sort('active'); ?></th>
			<th class="actions"><?php echo __d('report', 'Actions'); ?></th>
	</tr>
	<?php foreach ($departments as $department): ?>
	<tr>
		<td><?php echo h($department['Department']['id']); ?>&nbsp;</td>
		<td><?php echo h($department['Department']['label']); ?>&nbsp;</td>
		<td><?php echo h($department['Department']['active']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__d('report', 'View'), array('action' => 'view', $department['Department']['id'])); ?>
			<?php echo $this->Html->link(__d('report', 'Edit'), array('action' => 'edit', $department['Department']['id'])); ?>
			<?php echo $this->Form->postLink(__d('report', 'Delete'), array('action' => 'delete', $department['Department']['id']), null, __d('report', 'Are you sure you want to delete # %s?', $department['Department']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __d('report', 'Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __d('report', 'previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__d('report', 'next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __d('report', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('report', 'New Department'), array('action' => 'add')); ?></li>
	</ul>
</div>
