<?php
/**
 * This file contains application specific configurations.
 */
	$config = array(
		'App' => array(
			'DefaultLanguage' => 'deu',
		),
		'Plugins' => array(
			'TinyMCE' => array(
				'configs' => array(
					'default' => array(
						'theme' => 'simple',
					),
					'Word2k7' => array(
						// General options
						'theme' => "advanced",
						'skin' => "o2k7",
						'verify_html' => true,
						'plugins' =>  "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,inlinepopups,autosave",

						// Theme options
						'theme_advanced_buttons1' => "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",
						'theme_advanced_buttons2' => "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
						'theme_advanced_buttons3' => "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
						'theme_advanced_buttons4' => "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,restoredraft",
						'theme_advanced_toolbar_location' => "top",
						'theme_advanced_toolbar_align' => "left",
						'theme_advanced_statusbar_location' => "bottom",
						'theme_advanced_resizing' => true,

						// Drop lists for link/image/media/template dialogs
						'template_external_list_url' => "lists/template_list.js",
						'external_link_list_url' => "lists/link_list.js",
						'external_image_list_url' => "lists/image_list.js",
						'media_external_list_url' => "lists/media_list.js"
					),
				),
			)
		)
	);