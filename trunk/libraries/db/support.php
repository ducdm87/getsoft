<?php
/*
 * @filename 	: getnews.php
 * @version  	: 1.0
 * @package	 	: vietbao.vn/get news/
 * @subpackage	: component
 * @license		: GNU/GPL 3, see LICENSE.php
 * @author 		: Team : Đức
 * @authorEmail	: 
 *				: ducdm87@binhhoang.com
 * @copyright	: Copyright (C) 2011 Vi?t b�o�. All rights reserved. 
 */

defined('_JEXEC') or die('Restricted access');
JTable::addIncludePath(JPATH_COMPONENT_SITE.DS.'tables');

function mosCommentStore($aid, $domain = "", $name,$datetime,$comment, $param = '')
{
	global $database;
	$db	=	$database;
	$query	=	'INSERT into `#__article2010_comment`
					SET aid = '.$db->quote($aid).',
						domain = '.$db->quote($domain).',
						name = '.$db->quote($name).',
						created = '.$db->quote($datetime).',
						comment = '.$db->quote($comment).',
						field_unique = '.$db->quote(md5($name.$comment)).',
						param = '.$db->quote($param);
	$db->setQuery($query);
	$db->query();	
}
function processVendor($obj_vendor)
{
	$db	=	JFactory::getDBO();
	
	$obj_vendor->VendorName = trim(strtolower($obj_vendor->VendorName));
	$obj_vendor->VendorSupportEmail = trim(strtolower($obj_vendor->VendorSupportEmail));
	$obj_vendor->VendorHomepageURL = trim(strtolower($obj_vendor->VendorHomepageURL));
	
	
	$obj_vendor->VendorHomepageURL	=	str_replace('www.','',$obj_vendor->VendorHomepageURL);
	$obj_vendor->VendorHomepageURL	=	str_replace('http://','',$obj_vendor->VendorHomepageURL);		
	
	$obj_vendor->VendorAurl 	=	str_replace('www.','',$obj_vendor->VendorAurl);
	$obj_vendor->VendorAurl 	=	str_replace('http://','',$obj_vendor->VendorAurl);	

//	$query	=	'select count(*) from `#__software2011a` where SiteID = '.$db->quote($obj_vendor->SiteID);
	$query	=	'SELECT VendorID '.
					' FROM `#__software_vendor2011` '.
					' WHERE SiteID = '.$db->quote($obj_vendor->SiteID).
						' AND (VendorName = '.$db->quote($obj_vendor->VendorName).
								' OR (VendorSupportEmail = '.$db->quote($obj_vendor->VendorSupportEmail).' and VendorSupportEmail<> "")'.
								' OR (VendorHomepageURL = '.$db->quote($obj_vendor->VendorHomepageURL).' and VendorHomepageURL<> "")'.
								' OR (VendorAurl = '.$db->quote($obj_vendor->VendorAurl).' and VendorAurl<> "")'.
							')';
	$db->setQuery($query);
//	echo $db->getQuery();
	$row =& JTable::getInstance('Software_vendor2011','Table');	
	if ($vendor_id = $db->loadResult()) {
		$row->load($vendor_id);	
	}else {
		$row->SiteID = $obj_vendor->SiteID;
	}
	$row->VendorName = $obj_vendor->VendorName;
	$row->VendorSupportEmail = $obj_vendor->VendorSupportEmail;
	$row->VendorHomepageURL = $obj_vendor->VendorHomepageURL;

	$row->store();

	return $row->VendorID;
}