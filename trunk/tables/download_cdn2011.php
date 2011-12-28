<?php
/**
 * @version	$Id: f5gallery.php $
 * @package	F5Gallery
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	Commercial
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


/**
 * @package		Joomla
 * @subpackage	Test
 */
class TableDownload_cdn2011 extends JTable
{
	var $id 				= null; 
	var $wsid 				= null; 
	var $PA_ID 				= null; 
	var $SiteID 			= null; 
	var $SiteName 			= null; 
	var $SourceURL 			= null; 
	var $FinalDownloadURL 	= null; 
	var $Checksum 			= null; 
	var $ContentType 		= null; 
	var $TotalTime 			= null; 
	var $SizeDownload 		= null; 
	var $SpeedDownload 		= null; 
	var $DownloadContentLength = null; 
	var $OriginalFileName 	= null; 
	var $FileName 			= null; 
	var $Path 				= null; 
	var $DownloadState 		= null; 
	var $Approved 			= null; 
	var $DownloadDate 		= null; 
	var $Note 				= null;
	var $state 				= 0;

	/**
	* @param database A database connector object
	*/
	function TableDownload_cdn2011(&$db)
	{
		parent::__construct( '#__download_cdn2011', 'id', $db );
	}
}
