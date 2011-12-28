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
class TableSoftware2011a extends JTable
{
	 var $id = null; 
	 var $wsid = null; 
	 var $AffiliateProgramID = null; 
	 var $AffiliatedProductID = null; 
	 var $ProductID = null; 
	 var $ProductName = null; 
	 var $ProductName_alias = null; 
	 var $ProductInfoURL = null; 
	 var $VendorID = null; 
	 var $VendorName = null; 
	 var $VendorSupportEmail = null; 
	 var $VendorHomepageURL = null; 
	 var $VendorAurl = null; 
	 var $VendorInfo = null; 
	 var $USDPrice = null; 
	 var $SectionID = null; 
	 var $SectionName = null; 
	 var $CategoryID = null; 
	 var $CategoryName = null; 
	 var $ShortDesc = null; 
	 var $LongDesc = null; 
	 var $keywords1 = null; 
	 var $keywords2 = null; 
	 var $TrialURL = null; 
	 var $DirectPurchaseURL = null; 
	 var $Platform1 = null; 
	 var $Platform2 = null; 
	 var $Boxshot = null; 
	 var $Screenshot = null; 
	 var $Icon = null; 
	 var $Banner125x125 = null; 
	 var $Banner468x60 = null; 
	 var $Banner120x90 = null; 
	 var $Banner728x90 = null; 
	 var $Banner300x250 = null; 
	 var $Banner392x72 = null; 
	 var $Banner234x60 = null; 
	 var $Banner120x240 = null; 
	 var $Banner120x60 = null; 
	 var $Banner88x31 = null; 
	 var $PromoText = null; 
	 var $EncodingCharSet = null; 
	 var $Commission = null; 
	 var $Commission_structure = null; 
	 var $Add_Date = null; 
	 var $FileSize = null; 
	 var $version = null; 
	 var $language = null; 
	 var $delivery = null; 
	 var $download_time = null; 
	 var $all = null; 
	 var $sitecertificate = null; 
	 var $affiliate_status = null; 
	 var $note = null;

	
	/**
	* @param database A database connector object
	*/
	function TableLink_affiliate_product2011(&$db)
	{
		parent::__construct( '#__link_affiliate_product2011', 'id', $db );
	}
}
