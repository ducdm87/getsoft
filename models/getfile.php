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

class DIRModelGetfile extends JModel{	
	
	var $_data 		=	null;
	var $ext		=	null;
	var $name		=	null;
	var $_config	=	null;
	
	function __construct($id=null)
	{
		
		parent::__construct();
	}
	function getFile()
	{
		$db	=	JFactory::getDBO();
		$dowload	=	new DirDownload_cdn2011();
		$list		=	$dowload->getList('id,SiteID','state<1',0, 5, 'id');
		for ($i=0; $i<count($list); $i++)
		{
			$item	=	$list[$i];
			mosGetFile($item->SiteID, $item->id);		
		}		
		return $arr_obj;
	}	
}