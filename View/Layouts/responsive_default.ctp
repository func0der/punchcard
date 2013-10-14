<?php
/**
 * Default Layout
 * Twitter Bootstrap - UI Plugin
 */
?>

<!DOCTYPE HTML>
<!--[if lt IE 7]> <html class="ie ie6 oldie badie it" lang="it"> <![endif]-->
<!--[if IE 7]>    <html class="ie ie7 oldie badie it" lang="it"> <![endif]-->
<!--[if IE 8]>    <html class="ie ie8 oldie it" lang="it"> <![endif]-->
<!--[if IE 9]>    <html class="ie ie9 it" lang="it"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="good it" lang="it"> <!--<![endif]-->
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		
		
		<?php
		/*
		<link rel="shortcut icon" type="image/x-icon" href="<?=$this->Html->assetUrl('Ui.images/favicon.png')?>" />
		*/
		
		/**
		 * SEO Meta Tags
		 */
		echo $this->Html->tag('title', BB::read('Twb.meta.title'));
		if (BB::check('Twb.meta.description')) {
			echo $this->Html->meta('description', BB::read('Twb.meta.description', BB::read('Twb.meta.title')));
		}
		if (BB::check('Twb.meta.keywords')) {
			echo $this->Html->meta('keywords', BB::read('Twb.meta.keywords'));
		}
		
		/**
		 * Favicon
		 */
		echo $this->Html->meta('favicon.ico', Router::url(BB::read('Twb.favicon', '/favicon.ico')), array('type' => 'icon'));
		
		/**
		 * Canonical Url
		 * best served as full static url!
		 */
		if (BB::check('Twb.canonical')) {
			echo $this->Html->meta(null, BB::read('Twb.canonical'), array('rel' => 'canonical', 'type' => null));
		}
		
		
		/**
		 * LayoutCSS
		 */
		$css = 'Twb.compiled/bootstrap';
		if (BB::check('Twb.cssTheme')) {
			$css = 'Twb.themed/' . BB::read('Twb.cssTheme');
			BB::write('Twb.cssMin', true);
		}
		if (BB::check('Twb.cssMin')) {
			$css.= '.min';
		}
		
		$this->Html->css(array(
			$css,
			'Twb.compiled/bootstrap-responsive.min',
			'Twb.twb-core'
		), false);
		
		/**
		 * LayoutJS
		 */
		$this->Html->script(array(
			'Twb.compiled/jquery-1.9.0',
			'Twb.compiled/bootstrap',
			'Twb.twb-core'
		), false);
		
		// pNotify Plugin
		$this->Html->css('Twb./js/3rd/pnotify-1.2.0/jquery.pnotify.default', array('inline' => false, 'prepend' => false));
		$this->Html->script('Twb.3rd/pnotify-1.2.0/jquery.pnotify.min', array('inline' => false, 'prepend' => false));
		
		// form plugin
		$this->Html->script('Twb.3rd/jquery.form', array('inline' => false, 'prepend' => false));
		
		// MediaTable Plugin
		$this->Html->css('Twb./js/3rd/mediatable/jquery.mediatable', array('inline' => false, 'prepend' => false));
		$this->Html->script('Twb.3rd/mediatable/jquery.mediatable', array('inline' => false, 'prepend' => false));
		
		// UI plugins
		$this->Html->script(array(
			'Twb.3rd/jquery.numeric',
			'Twb.3rd/jquery.lowercase',
			'Twb.3rd/autosize/jquery.autosize',
		), array('inline' => false, 'prepend' => false));
		
		
		echo $this->fetch('css');
		
		/**
		 * Analytics
		 */
		// custom element
		if ($this->elementExists('analytics')) {
			echo $this->element('analytics');
		// Twb element with GA ID
		} elseif (BB::check('Twb.analytics')) {
			// to implement standard traking code by GA-ID
			$this->append('script', $this->element('Twb.analytics', array(
				'ga' => BB::read('Twb.analytics')
			)));
		}
		?>
		
	</head>

	<?php
	/**
	 * Build Layout's Body TAG
	 */
	$bodyOptions = BB::setDefaultAttrs(BB::read('twb.layout.body'));
	
	/**
	 * Disable UI Javascript behaviors
	 */
	if (BB::read('Twb.layout.disable.smartMsg') !== true) {
		$bodyOptions['data-smartMsg'] = 'true';
	}
	if (BB::read('Twb.layout.disable.ajaxForm') !== true) {
		$bodyOptions['data-ajaxForm'] = 'true';
	}
	if (BB::read('Twb.layout.disable.mediaTable') !== true) {
		$bodyOptions['data-mediaTable'] = 'true';
	}
	if (BB::read('Twb.layout.disable.stickyUi') !== true) {
		$bodyOptions['data-stickyUi'] = 'true';
	}

	echo $this->Html->tag(BB::extend($bodyOptions, array(
		'tag' => 'body',
		'content' => array(
			$this->element('Layout/NoScript'),
			$this->Session->flash(),
			$this->fetch('content'),
			$this->fetch('script'),
			$this->fetch('inlineScript')
		)
	)));
	
	?>
</html>