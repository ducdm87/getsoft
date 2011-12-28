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
class TableSmedia2011a extends JTable
{
	var $serviceId 				= null; 
	var $RunId 					= null; 
	var $refindKey 				= null; 
	var $firstExtractionTime 	= null; 
	var $latestExtractionTime 	= null; 
	var $extractedInLatestRun 	= null; 
	var $id 					= null; 
	var $SiteID 				= null; 
	var $pid 					= null; 
	var $ProductID 				= null; 
	var $SourceURL 				= null; 
	var $Category 				= null; 
	var $Title 					= null; 
	var $Description 			= null; 
	var $Size 					= null; 
	var $FileName 				= null; 
	var $path 					= null; 
	var $FileType 				= null; 
	var $type					=	1;
	var $state					=	0;

	
	/**
	* @param database A database connector object
	*/
	function TableSmedia2011a(&$db)
	{
		parent::__construct( '#__smedia2011a', 'id', $db );
	}
}
