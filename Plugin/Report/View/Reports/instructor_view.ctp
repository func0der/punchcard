<?php $this->extend('Twb./Template/internal') ?>
<div class="reports view">
	<div class="clearfix headline">
		<h2 class="pull-left">
		<?php
			echo __d(
				'report',
				'Week %s in %s',
				$report['Report']['week'],
				$report['Report']['year']
			);
		?>
		</h2>
		<span class="pull-left">
		<?php
			echo __d(
				'report',
				'by %s',
				$report['User']['label']
			);
		?>
		</span>
<?php
	if($report['Report']['published']):
		echo $this->Html->link(
			(
				$report['Report']['accepted'] ?
				__d('report', 'Reject') :
				__d('report', 'Accept')
			),
			(
				$report['Report']['accepted'] ?
				array(
					'action' => 'reject',
					$report['Report']['id'],
				) :
				array(
					'action' => 'accept',
					$report['Report']['id'],
				)
			),
			array(
				'class' => 'pull-left reportAcceptLink'
			)
		);
	endif;
?>
	</div>
	<div class="information">
		<div class="department">
		<?php
			echo __d(
				'report',
				'Department: %s',
				$report['Department']['label']
			);
		?>
		</div>
	</div>
	<div class="reportItems">
	<?php
		if(!empty($reportItems)):
			echo $this->Form->create('ReportItem');
			echo $this->Form->input(
				'Report.review',
				array(
					'checked' => ($report['Report']['review'] === true),
				)
			);
	?>
		<ul class="resettedList">
		<?php
			foreach($reportItems as $index => $reportItem):
		?>
			<li>
			<?php
				echo $this->Form->input(
					$index . '.ReportItem.id',
					array(
						'type' => 'hidden',
					)
				);
			?>
				<div class="date">
				<?php
					echo $this->element(
						'Report.DateRepresentation',
						array(
							'date' => $reportItem['ReportItem']['date'],
						)
					);
				?>
				</div>
			<?php
				// echo $this->Html->link(
				// 	__d('report', 'Edit comment'),
				// 	'#',
				// 	array(
				// 		'class' => 'reportItemEditCommentToggle',
				// 	)
				// );
			?>
				<div class="activities">
				<?php
					if(!empty($reportItem['Activity'])):
				?>
					<ul class="resettedList">
					<?php
						foreach($reportItem['Activity'] as $activity):
					?>
						<li>
							<div class="fLeft label">
							<?php
								echo $activity['label'];
							?>
							</div>
							<div class="fLeft duration">
							<?php
								echo $activity['duration'];
							?>
							</div>
							<div class="fClear"></div>
						</li>
					<?php
						endforeach;
					?>
					</ul>
				<?php
					else:
						echo __d('report', 'There are currently no activities for this day.');
					endif;
				?>
				</div>
				<div class="fRight">
					<div class="fLeft totalDuration">
					<?php
						echo $reportItem['ReportItem']['duration'];
					?>
					</div>
					<div class="fLeft workingHoursDifference">
					<?php
						echo $this->element(
							'Report.WorkingHoursDifference',
							array(
								'timeWorked' => $reportItem['ReportItem']['duration'],
							)
						);
					?>
					</div>
				</div>
				<div class="fClear"></div>
				<div class="commentContainer">
				<?php
					echo $this->Form->input(
						$index . '.ReportItem.Comment.id',
						array(
							'type' => 'hidden',
							'value' => $reportItem['Comment']['id'],
						)
					);
					echo $this->Form->input(
						$index . '.ReportItem.Comment.comment',
						array(
							'class' => 'commentInput',
							// 'disabled' => true,
							'type' => 'textarea',
							'value' => $reportItem['Comment']['comment'],
						)
					);
					echo $this->Form->input(
						$index . '.ReportItem.Comment.read',
						array(
							'class' => 'commentInput',
							'type' => 'hidden',
							'value' => 0
						)
					);
				?>
				</div>
			</li>
		<?php
			endforeach;
		?>
		</ul>
	<?php
			echo $this->Form->end(__d('report', 'Submit'));
		else:
			echo __d(
				'report',
				'No entries for week %s in year %s.',
				$report['Report']['week'],
				$report['Report']['year']
			);
		endif;
	?>
	</div>
</div>