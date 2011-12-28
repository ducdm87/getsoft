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

class DIRControllerFilehippo extends DIRController
{
	var $return_url='';
	
	var $notify_url='';	
	
	function __construct()
	{	
		parent::__construct();	
		
	}
	function getCategory()
	{
		echo 'get category complete';
		die();
		$model = $this->getModel('category');
		$defalutExecution = ini_get('max_execution_time');
		@set_time_limit(60 * 5);
			$model->getFilehippo();
		@set_time_limit($defalutExecution);
		echo 'get category for filehippo.com sucessfully';
		die();
	}
	
	function getData()
	{
		require_once(dirname(__FILE__).DS.'..'.DS.'configs'.DS.'filehippo.php');
		
		$href	=	new href();
		$param	=	array();	
		$param['option']	=	JRequest::getVar('option');
		$param['task']		=	'filehippo.getData';		
		$param['live']		=	'1';
		if (isset($_REQUEST['catid']))
		{
			$param['catid']		=	$_REQUEST['catid'];
		}
		if (isset($_REQUEST['tpe_get']))
		{
			$param['tpe_get']	=	$_REQUEST['tpe_get'];
		}
		$param['s']	=	uniqid();

		$refresh	=	$href->refresh($param,15000);
		echo ($refresh);
//		die();
		$model = $this->getModel('filehippo');
		$defalutExecution = ini_get('max_execution_time');
		@set_time_limit(60 * 30);
			$arr_obj = $model->getCat();
			if (count($arr_obj)<2) {
				echo 'success';
				die();
			}
			$siteID	=	'fhp100';
			$obj_cat		=	$arr_obj[0];
			$model->getListContent($obj_cat->link, 1, $obj_cat->id, $obj_cat->secid, $obj_cat->catid, $obj_cat->parent, $siteID, $SiteName);
			$total	=	0;
			$model->getContent($SiteID, $numbercontent, $total);
						
		@set_time_limit($defalutExecution);
		
echo '<hr /><br /> get category for filehippo.com sucessfully';
echo '<br /> Number of article got sucessfully: '. $numbercontent.'/'.$total;
echo "<br /> Category: ID: $obj_cat->id ||SECID: $obj_cat->secid ||catid: $obj_cat->catid || ";

echo '<br />Time: '. date('Y-m-d h:m:s');
		die();
	}

	function getDetail()
	{	
		require_once(dirname(__FILE__).DS.'..'.DS.'configs'.DS.'filehippo.php');
		
		$content_id	=	JRequest::getVar('content_id');
		$pid	=	JRequest::getVar('pid');
		$SourceURL	=	JRequest::getVar('SourceURL');		
		$content_title	=	JRequest::getVar('content_title');
		$begin_get_content	=	JRequest::getVar('begin_get_content');
		$end_get_content	=	JRequest::getVar('end_get_content');
		echo $begin_get_content;
		$model 		= $this->getModel('filehippo');
		$siteID	=	'fhp100';
		
		$arr_param	=	array();
		$arr_param['root_icon']		=	$root_icon;
		$arr_param['patch_icon']	=	$patch_icon;
		
		$arr_param['root_image']	=	$root_image;
		$arr_param['patch_image']	=	$patch_image;
		
		$arr_param['root_file']	=	$root_file;
		$arr_param['patch_file']	=	$patch_file;
		
		
		$defalutExecution = ini_get('max_execution_time');
		@set_time_limit(60 * 15);
		print_debug(1,'BEGIN get content');
			$model->getDetail($content_id, $SourceURL, $pid, $content_title, $siteID, $SiteName, $arr_param);
		print_debug(1,'END get content');
		@set_time_limit($defalutExecution);
		echo $end_get_content;
		die();
	}

	function getFile()
	{
//		$title	=	'Asus U43JC Bios 216 ';
//		echo $title;
//		echo '<hr />';
//		$title	=	SoftwareTitle2($title);
//		var_dump($title);
//		die();
		require_once(dirname(__FILE__).DS.'..'.DS.'configs'.DS.'filehippo.php');
		require_once(JPATH_COMPONENT_SITE.DS.'libraries/getfile/filehippo.php');
		
		$siteID	=	'fhp100';
		
		$model 		= 	$this->getModel('filehippo');
		
		$time2 = microtime();		
		$time2 = explode(" ", $time2);
		$time2 = $time2[1] + $time2[0];
		$start2 = $time2;		
		$defalutExecution = ini_get('max_execution_time');
//		sleep(5);
		set_time_limit(0);	
			$countrow	=	$model->getFile($siteID, $patch_file);			
		@set_time_limit($defalutExecution);
		$time2 = microtime();
		$time2 = explode(" ", $time2);
		$time2 = $time2[1] + $time2[0];
		$finish2 = $time2;
		$totaltime2 = ($finish2 - $start2);
		printf ("<br/>took %f s to load.", $totaltime2);
		
		$wait_delay	=	900000;
		if ($countrow > 0) {
			$wait_delay = 30000;
		}
		
		$href	=	new href();
		$param	=	array();	
		$param['option']	=	JRequest::getVar('option');
		$param['task']		=	'filehippo.getFile';
		$param['tpe_get']		=	'1';
		$param['s']	=	uniqid();
		
		$refresh	=	$href->refresh($param, $wait_delay);
		echo ($refresh);
		die();
	}
}

///////////////


