<?php
/**
 * @version	$Id: image.php $
 * @package	get_soft
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	Commercial
 */
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport( 'joomla.application.component.model' );
JTable::addIncludePath(JPATH_COMPONENT_SITE.DS.'tables');

class DIRModelCompany extends JModel{	
	
	var $_data 		=	array();
	var $ext		=	null;
	var $name		=	null;
	var $_config	=	null;
	
	function __construct($id=null)
	{
		
		parent::__construct();
	}
	
	public function getNodevice($SiteID){
		$link = 'http://www.nodevice.com/driver/more_companies.html';
		$db			=&	JFactory::getDBO();
		$browser 	= new phpWebHacks();
		$response 	= $browser->get($link);
		$html 		=	loadHtmlString($response);
		$arr_com 	=	array();
		$href		=	new href();

		if ($main_pad		=	$html->find('div[class="main_pad"]',0)) {			
			$items	=	$main_pad->find('li');			
			for ($i=0; $i<count($items); $i++)
			{
				$item			=	$items[$i];				
				$obj_com		=	new stdClass();
				//$obj_com->link	=	$href->process_url($item->children(0)->href,$link);
				$obj_com->title	=	strip_tags($item->children(0)->innertext);				
				$arr_com[]		=	$obj_com;									
			}					
		}	
	
		JTable::addIncludePath(JPATH_COMPONENT_SITE.DS.'tables');		
		for ($i=0; $i<count($arr_com); $i++)
		{
			$obj	=	new stdClass();
			$obj->SiteID	=	$SiteID;
			$obj->VendorName	=	$arr_com[$i]->title;
			$obj->VendorSupportEmail	=	'';
			$obj->VendorHomepageURL	=	'';
			$obj->VendorAurl	=	'';		
			processVendor($obj);			
		}
	}
}