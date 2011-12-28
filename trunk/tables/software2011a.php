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
	/**
	 * Enter description here...
	 *
	 * @var unknown_type
	 */
	var $sid				=	 null;
	/**
	 * Enter description here...
	 *
	 * @var unknown_type
	 */
	var $sRunId				=	 null;
	/**
	 * Enter description here...
	 *
	 * @var unknown_type
	 */
	var $refindKey				=	 null;
	var $firstExtractionTime	=	 null;
	var $latestExtractionTime	=	 null;
	var $extractedInLatestRun	=	 null;
	var $id						=	 null;
	var $wsid					=	 null;
	var $id_hec					=	 null;
	var $pid					=	 null;
	var $SourceURL				=	 null;
	var $NavigatorPath			=	 null;
	var $SiteID					=	 null;
	var $SiteName				=	 null;
	var $AffiliateProgramID		=	 null;
	var $AffiliatedProductID	=	 null;
	var $ProductID				=	 null;
	var $ProductName			=	 null;
	var $ProductSetID			=	 null;
	var $ProductName_alias		=	 null;
	var $ProductInfoURL			=	 null;
	var $VendorID				=	 null;
	var $VendorName				=	 null;
	var $VendorSupportEmail		=	 null;
	var $VendorHomepageURL		=	 null;
	var $VendorAurl				=	 null;
	var $VendorInfo				=	 null;
	var $USDPrice				=	 null;
	var $VendorPhone			=	 null;
	var $VendorFax				=	 null;
	var $SectionID				=	 null;
	var $SectionName			=	 null;
	var $CategoryID				=	 null;
	var $CategoryName			=	 null;
	var $ShortDesc				=	 null;
	var $LongDesc				=	 null;
	var $TrialURL				=	 null;
	var $keywords1				=	 null;
	var $keywords2				=	 null;
	var $DirectPurchaseURL		=	 null;
	var $Platform1				=	 null;
	var $Platform2				=	 null;
	var $Boxshot				=	 null;
	var $Screenshot				=	 null;
	var $Icon					=	 null;
	var $Commission				=	 null;
	var $Commission_structure	=	 null;
	var $Add_Date				=	 null;
	var $FileSize				=	 null;
	var $language				=	 null;
	var $License				=	 null;
	var $Requirement			=	 null;
	var $Visits					=	 null;
	var $AdditionalRequirements	=	 null;
	var $version				=	 null;
	var $version_history		=	 null;
	var $PreviousVersions		=	 null;
	var $Limitations			=	 null;
	var $delivery				=	 null;
	var $download_time			=	 null;
	var $UserRating				=	 null;
	var $Votes					=	 null;
	var $EditorRating			=	 null;
	var $EditorReview			=	 null;
	var $EditorReviewSlogan		=	 null;
	var $Reviewedby				=	 null;
	var $VersionReviewed		=	 null;
	var $DownloadURL			=	 null;
	var $DestURL				=	 null;
	var $FinalDownloadURL		=	 null;
	var $TotalDownload			=	 null;
	var $DownloadThisWeek		=	 null;
	var $site_certificate		=	 null;
	var $CertifiedProduct		=	 null;
	var $EPC					=	 null;
	var $PowerRank				=	 null;
	var $affiliate_status		=	 null;
	var $rank					=	 null;
	var $note					=	 null;
	var $state					=	 null;
	var $stepid					=	 null;
	var $PageHTML				=	 null;
	var $temp					=	 null;
	
	/**
	* @param database A database connector object
	*/
	function TableSoftware2011a(&$db)
	{
		parent::__construct( '#__software2011a', 'id', $db );
	}
}
