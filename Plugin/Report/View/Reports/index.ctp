<?php $this->extend('Twb./Template/internal') ?>
<div class="form">
	<h3>
	<?php
		echo __d('report', 'View a specific week')
	?>
	</h3>
<?php
	echo $this->Form->create('Report');
	echo $this->Form->input(
		'week',
		array(
			'options' => array_slice(range(0, 53), 1, null, true),
			'value' => ($this->Form->value('week') ? $this->Form->value('week') : DATE_WEEK)
		)
	);
	echo $this->Form->input(
		'year',
		array(
			// Sloppy
			'options' => array_combine(
				range(
					$this->Time->format(
						$user['User']['entry_date'],
						'%Y'
					),
					DATE_YEAR
				),
				range(
					$this->Time->format(
						$user['User']['entry_date'],
						'%Y'
					),
					DATE_YEAR
				)
			),
			'value' => ($this->Form->value('year') ? $this->Form->value('year') : DATE_YEAR)
		)
	);
	echo $this->Form->end(__d('report', 'Submit'));
?>
</div>

<div class="reports index">
	<h2><?php echo __d('report', 'Reports'); ?></h2>
<?php
	echo $this->TwbTable->render(
		$reports,
		array(
			'columns' => array(
				'label' => array(
					'paginate' => false,
					'label' => __d('report', 'Report'),
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
								'{Report.week}',
								'{Report.year}',
							),
						),
						'download' => array(
							'href' => array(
								'action' => 'download',
								'{Report.id}'
							),
							'icon' => 'download',
							'icon-only' => true,
						),
					),
				),
			),
			'tbodyReportReport' => function($helper, $value, $data){
				return $data['']
				;
			},
			'tbodyReportPublished' => function($helper, $value, $data){
				return ($value === true) ?
					__d('report', 'Yes') :
					__d('report', 'No')
				;
			},
			'tbodyReportStatus' => function($helper, $value, $data){
				return $helper->_View->element(
					'Report.ReportStatus',
					array(
						'report' => $data,
					)
				);
			},
			'tbodyActionsView' => function($helper, $config, $data){
				if($data['Report']['week'] === DATE_WEEK && $data['Report']['year'] === DATE_YEAR){
					$config['href'] = array(
						'action' => 'view',
						false,
					);
				}
				return $config;
			},
			'tbodyActionsDownload' => function($helper, $config, $data){
				if($data['Report']['accepted'] !== true){
					return false;
				}
				return $config;
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
