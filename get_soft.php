<?php
/**
 * @version	$Id: get_soft.php $
 * @package	get_soft
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	Commercial
 */

// no direct access
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Set the path definitions
$view = JRequest::getCmd('view',null);
//$popup_upload = JRequest::getCmd('pop_up',null);
$path = "file_path";

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

$cmd = JRequest::getCmd('task', null);

// Require specific controller if requested
if($controllerName = JRequest::getVar('controller')) {
	require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php');
} else {
	if (strpos($cmd, '.') != false)
	{
		// We have a defined controller/task pair -- lets split them out
		list($controllerName, $task) = explode('.', $cmd);
	
		// Define the controller name and path
		$controllerName	= strtolower($controllerName);
		
		$controllerPath	= JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php';
	
		// If the controller file path exists, include it ... else lets die with a 500 error
		if (file_exists($controllerPath)) {
			require_once($controllerPath);
		} else {
			JError::raiseError(500, 'Invalid Controller');
		}
	}
	else
	{
		// Base controller, just set the task :)
		$controllerName = null;
		$task = $cmd;
	}
}

// Set the name for the controller and instantiate it
$controllerClass = 'DIRController'.ucfirst($controllerName);

if (class_exists($controllerClass)) {
	$controller = new $controllerClass();
} else {
	JError::raiseError(500, 'Invalid Controller Class');
}

$debug	=	JRequest::getVar('debug',0);
define( 'DEBUG',	$debug);


// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();

function dump_data()
{
	$arr = func_get_args();
	dump_one_data($arr);	
}

function dump_one_data($data)
{
	if (is_array($data)){
		echo '<hr />';
		echo 'array'; 
		echo '<br />';
		echo count($data);
		echo '<br />';
		foreach ($data as $k=>$_data) {
			echo 'key: ['.$k.']:';
			if (is_array($_data) or is_object($_data)) {
				dump_one_data($_data);
			}else {
				echo ' value: ';
				var_dump($_data);
			}
			echo '<br />';
		}
	}	
	
	if (is_object($data))
	{		
		echo '<hr />';
		echo 'object';
		echo '<br />';
		foreach (get_object_vars( $data ) as $k => $v) {
			echo 'key: ['.$k.']:';
			if (is_array($v) or is_object($v)) {				
				dump_one_data($v);
			}else {
				echo ' value: ';
				var_dump($v);
			}
			echo '<br />';
		}		
	}
}

?>