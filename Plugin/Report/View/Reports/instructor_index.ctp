<?php $this->extend('Twb./Template/internal') ?>
<div class="reports index">
	<h2><?php echo __d('report', 'Reports'); ?></h2>
<?php
	echo $this->TwbTable->render(
		$reports,
		array(
			'columns' => array(
				'label' => array(
					'paginate' => true,
					'label' => __d('report', 'Report'),
				),
				'user_id' => array(
					'paginate' => true,
				),
				'status' => array(
					'paginate' => false
				),
				'actions' => array(
					'label' => __d('report', 'Actions'),
					'items' => array(
						'view' => array(
							'href' => array(
								'action' => 'view',
								'{Report.id}',
								'instructor' => true
							),
						),
					),
				),
			),
			'tbodyReportUserId' => function($helper, $value, $data){
				return $data['User']['label'];
			},
			'tbodyReportStatus' => function($helper, $value, $data){
				return $helper->_View->element(
					'Report.ReportStatus',
					array(
						'report' => $data,
					)
				);
			},
		)
	);
?>
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
