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

class DIRControllerAndroid extends DIRController
{
	var $return_url='';
	
	var $notify_url='';	
	
	function __construct()
	{	
		parent::__construct();	
		
	}
	function getCategory()
	{
//		echo 'get category complete';
//		die();
		$model = $this->getModel('category');
		$defalutExecution = ini_get('max_execution_time');
		@set_time_limit(60 * 5);
			$model->getAndroid();
		@set_time_limit($defalutExecution);
		echo 'get category for market.android.com sucessfully';
		die();
	}
	
	function getData()
	{
		require_once(dirname(__FILE__).DS.'..'.DS.'configs'.DS.'android.php');
		
		$href	=	new href();
		$param	=	array();	
		$param['option']	=	JRequest::getVar('option');
		$param['task']		=	'android.getData';
		$param['live']		=	'1';
		if (isset($_REQUEST['cat_id']))
		{
			$param['cat_id']		=	$_REQUEST['cat_id'];
		}
		$param['s']	=	uniqid();
		
		$refresh	=	$href->refresh($param,15000);
		echo ($refresh);
//		die();
		$model = $this->getModel('android');
		$defalutExecution = ini_get('max_execution_time');
		@set_time_limit(60 * 30);
			$arr_obj = $model->getCat();
			if (count($arr_obj)<2) {
				echo 'success';
				die();
			}			
			$obj_cat		=	$arr_obj[0];			
			$param	=	$obj_cat->lastGet_param;
			if ($param == '') {
				$param	=	'getold=1||1;page=0||0;';
			}
			
			preg_match('/getold=([^;]*);page=([^;]*);/ism',$param,$matches_param);
			$getold		=	'1||1';	$page	=	'0||0';	
			if(isset($matches_param[1]))
				$getold	=	$matches_param[1];
			if(isset($matches_param[2]) and $matches_param[2])
				$page	=	$matches_param[2];
			
			$page	=	explode('||',$page);
			$getold	=	explode('||',$getold);
			
			if($getold[0] == 0 and $page[0] >5)
				$page[0]	=	1;
			if($getold[1] == 1 and $page[1] >5)
				$page[1]	=	1;
				
			$total	=	0;
			
			$cid	=	$model->getListContent($obj_cat->link, $obj_cat->alias_origional, $getold, $page, $obj_cat->id, $obj_cat->secid, $obj_cat->catid, $obj_cat->parent, $SiteID, $SiteName);
			if ($cid) {
				$getold	=	implode('||',$getold);
				$page	=	implode('||',$page);
				
				$str_param	=	"getold=$getold;page=$page;";
				
				$db 	= JFactory::getDBO();
				$query	=	'UPDATE `#__software_category_androi` 
							SET `last_run` = '.$db->quote(date ( 'Y-m-d H:i:s' )).', 
								`lastGet_param` = '.$db->quote($str_param).'
							WHERE `id` ='. $cid;
				$db->setQuery($query);
				$db->query();
			}
			
			$model->getContent($SiteID, $numbercontent, $total);
						
		@set_time_limit($defalutExecution);

echo '<hr /><br /> get category for market.android.com sucessfully';
echo '<hr /><br /> Param change( free||paid ): ['.$param.'] => ['. $str_param.']';
echo '<br /> Number of article got sucessfully: '. $numbercontent.'/'.$total;
echo "<br /> Category: ID: $obj_cat->id ||SECID: $obj_cat->secid ||catid: $obj_cat->catid || ";
echo '<br />Time: '. date('Y-m-d h:m:s');
		die();
	}

	function getDetail()
	{	
		require_once(dirname(__FILE__).DS.'..'.DS.'configs'.DS.'android.php');
		
		$content_id	=	JRequest::getVar('content_id');
		$pid	=	JRequest::getVar('pid');
		$SourceURL	=	JRequest::getVar('SourceURL');		
		$content_title	=	JRequest::getVar('content_title');
		$begin_get_content	=	JRequest::getVar('begin_get_content');
		$end_get_content	=	JRequest::getVar('end_get_content');
		echo $begin_get_content;
		$model 		= $this->getModel('android');
		
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
			$model->getDetail($content_id, $SourceURL, $pid, $content_title, $SiteID, $SiteName, $arr_param);
		print_debug(1,'END get content');
		@set_time_limit($defalutExecution);
		echo $end_get_content;
		die();
	}
	
}

///////////////


