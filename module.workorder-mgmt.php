<?php
// Git commit info
if (file_exists(dirname(__FILE__).'/.git/HEAD'))
{
	$sHead = trim(preg_replace('/ref:/', '', file_get_contents(dirname(__FILE__).'/.git/HEAD'), -1, $iCount));
	if ($iCount === 1)
	{
		$sCommit = '(commit '.file_get_contents(dirname(__FILE__).'/.git/'.$sHead, false, null, 0, 7).')';
	}
	elseif ($iCount === 0)
	{
		$sCommit = '(commit '.substr($sHead, 0, 7).')';
	}
	else
	{
		$sCommit = '(commit info not found)';
	}
}
else
{
	$sCommit = '';
}

//
// iTop module definition file
//

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'workorder-mgmt/0.2.1',
	array(
		// Identification
		//
		'label' => "Work Order Management $sCommit",
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'itop-tickets/2.2.0',
			'itop-config-mgmt/2.2.0'
			),
		'mandatory' => false,
		'visible' => true,

		// Components
		//
		'datamodel' => array(
			'model.workorder-mgmt.php',
			'main.workorder-mgmt.php',
			'dashletcalendar.class.php'
		),
		'webservice' => array(

		),
		'data.struct' => array(
			// add your 'structure' definition XML files here,
		),
		'data.sample' => array(
			// add your sample data XML files here,
		),

		// Documentation
		//
		'doc.manual_setup' => '', // hyperlink to manual setup documentation, if any
		'doc.more_information' => '', // hyperlink to more information, if any

		// Default settings
		//
		'settings' => array(
			// Module specific settings go here, if any
		),
	)
);


?>
