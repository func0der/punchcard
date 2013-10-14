<nav>
	<ul class="navigation">
		<li>
		<?php
			echo $this->Html->link(
				__d('report', 'Reports'),
				array(
					'plugin' => 'report',
					'controller' => 'reports',
					'action' => 'index'
				)
			);
		?>
		</li>
	</ul>
</nav>