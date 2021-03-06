<?php
/**
 * Dolibase
 * 
 * Open source framework for Dolibarr ERP/CRM
 *
 * Copyright (c) 2018 - 2019
 *
 *
 * @package     Dolibase
 * @author      AXeL
 * @copyright   Copyright (c) 2018 - 2019, AXeL-dev
 * @license     MIT
 * @link        https://github.com/AXeL-dev/dolibase
 * 
 */

include_once '../lib/functions.php';

/**
 * Generate page
 */

$action = getPostData('action');
$message = array();

if ($action == 'generate')
{
	// Get data
	$page_name = getPostData('page_name');
	$module_folder = getPostData('module_folder');
	$object_class = getPostData('object_class');
	$data = array(
		'module_folder' => $module_folder,
		'page_title' => getPostData('page_title'),
		'access_perms' => getPostData('access_perms'),
		'default_view' => getPostData('default_view'),
		'object_class_include' => "dolibase_include_once('core/class/custom_object.php');",
		'object_init' => '$object = new CustomObject();'."\n".'// $object->setTableName(...);'
	);

	// Check if page already exist
	$root = getDolibarrRootDirectory();
	$module_path = $root.'/custom/'.$module_folder;
	$page_file = $module_path.'/'.$page_name;

	if (file_exists($page_file))
	{
		// Set error message
		$message = array(
			'text' => 'Page <strong>'.$page_file.'</strong> already exists.',
			'type' => 'danger'
		);
	}
	else
	{
		// Set object class include & init
		if (! empty($object_class)) {
			$data['object_class_include'] = "dol_include_once('".$module_folder."/class/".$object_class."');";
			$data['object_init'] = '$object = new '.getClassName($module_path.'/class/'.$object_class).'();';
		}

		// Add page into module
		$template = getTemplate(__DIR__ . '/../tpl/page/calendar.php', $data);
		file_put_contents($page_file, $template);

		// Set file permission
		chmod($page_file, 0777);

		// Set success message
		$message = array(
			'text' => 'Page <strong>'.$page_file.'</strong> successfully generated.',
			'type' => 'success'
		);
	}
}

/**
 * Show view
 */

$modules_list = getModulesList();
$object_class_list = empty($modules_list) ? array() : getModuleObjectClassList($modules_list[0]);
$rights_class = empty($modules_list) ? '' : getModuleRightsClass($modules_list[0]);
$options = array(
	'path_prefix' => '../',
	'title' => 'Page Builder',
	'navbar_active' => 'page/calendar',
	'form_name' => 'page/calendar',
	'css' => array(),
	'js' => array('page.js'),
	'message' => $message,
	'modules_list' => $modules_list,
	'object_class_list' => $object_class_list,
	'access_perms' => '$user->rights->'.$rights_class.'->read'
);

include_once '../views/layout.php';
