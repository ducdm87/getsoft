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
class TableSoftware_vendor2011 extends JTable
{
	var $SiteID = null;
	var $VendorID = null;
	var $VendorName = null;
	var $VendorSupportEmail = null;
	var $VendorHomepageURL = null;
	var $VendorAurl = null;
	var $VendorInfo = null;
	var $VendorPhone = null;
	var $VendorFax = null;

	/**
	* @param database A database connector object
	*/
	function TableSoftware_vendor2011(&$db)
	{
		parent::__construct( '#__software_vendor2011', 'VendorID', $db );
	}
}
