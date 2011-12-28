<?php
/**
 * @version	$Id: image.php $
 * @package	get_soft
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	Commercial
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class DIRControllerCnet extends DIRController
{
	var $return_url='';
	
	var $notify_url='';	
	
	function __construct()
	{	
		parent::__construct();	
		
	}
	function getFile()
	{
		require_once(dirname(__FILE__).DS.'..'.DS.'configs'.DS.'cnet.php');
		require_once(JPATH_COMPONENT_SITE.DS.'libraries/getfile/cnet.php');
		$db	=	dbo::getDBO($host, $user, $password, $dbname, $prefix, $dbtype);
	
		$model = $this->getModel('cnet');
		$defalutExecution = ini_get('max_execution_time');
		set_time_limit(0);
			$model->getFile($db, $user_cnet, $pass_cnet, $link_login, $table_name, $numbercontent);
		set_time_limit($defalutExecution);
		
		$href	=	new href();
		$param	=	array();	
		$param['option']	=	JRequest::getVar('option');
		$param['task']		=	'cnet.getFile';		
		$param['s']	=	uniqid();
		
		$refresh	=	$href->refresh($param, 10000);
		echo ($refresh);
		die();
	}
}

///////////////


