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

class DIRModelTucows extends JModel{	
	
	var $_data 		=	null;
	var $ext		=	null;
	var $name		=	null;
	var $_config	=	null;
	
	function __construct($id=null)
	{
		parent::__construct();
	}
	
	function getCat()
	{
		$db	=	JFactory::getDBO();
		$arr_obj	=	array();
		if (isset($_REQUEST['cat_id'])) {
			$cat_id	=	$_REQUEST['cat_id'];
			$query = "SELECT *
				FROM `#__software_category_tucows`
				WHERE id = $cat_id AND parent <> 0";
			$db->setQuery($query);			
			$obj	=	$db->loadObject();

			$arr_obj[]	=	$obj;
			$arr_obj[]	=	$obj;
		}else {
			$query = "SELECT *
				FROM `#__software_category_tucows`
				WHERE publish = 1 and (lastGet_param = '' or `lastGet_param` like ".$db->quote('%getold=1;%').")  AND parent <> 0".
				" ORDER BY `last_run` LIMIT 0,2";
			$db->setQuery($query);		
			$arr_obj	=	$db->loadObjectList();
			if (count($arr_obj) == 1) {
				$arr_obj[]		=	$arr_obj[0];
			}
		}
		
		return $arr_obj;
	}
	
	function getListContent($link,$cat_alias, & $page =1, $cid, $secid, $catid = 0, $cat_parent, $siteID = 'mad93', $SiteName ='android.com')
	{ 
		global $arrErr;
		$db		=	JFactory::getDBO();
		$root	=	'http://www.tucows.com';
		$href	=	new href();
	
		$link	=	$link."?pg=$page&f=all";
		echo '<br />';
		echo $link;
		echo '<br />';

		$browser	=	new phpWebHacks();			
		$response	=	$browser->get($link);	
		
		$arr_link_article	=	array();
		$html		=	loadHtmlString($response);	
		$arr_link	=	array();
		$arr_id		=	array();
		$arr_title	=	array();
		$arr_alias	=	array();
		$arr_intro	=	array();
		$arr_date	=	array();
//		$arr_icon	=	array();
	
		
		$titles		=	$html->find('div[id="titles"]',0);
//		
		$items		=	$titles->find('div[class="title"]');
				
//		http://www.tucows.com/preview/500420/All-Video-To-MP3-Converter
		$reg = '/\/(\d+)\/([^\/]+)$/ism';
		$reg_2	=	'/&url=(.*?)$/ism';
		$reg_date	=	'/Added\s*([^,]+,\s*\d+)/ism';	
		// find catNews
		
		for ($i=0; $i<count($items); $i++)
		{
			$item		= 	$items[$i];

			$link		=	$href->process_url($item->find('div[class="titleLeft2"]',0)->children(0)->href,$root);
			$link		=	str_replace('%3A',':',$link);
			$link		=	str_replace('%2F','/',$link);
			$link		=	str_replace('%2E','.',$link);
			
			$titleRight			=	$item->find('div[class="titleRight"]',0)->innertext;

			if(preg_match($reg, $link, $matches))
			{
				$arr_id[]	=	$matches[1];
				$arr_alias[]=	$matches[2];
			}else if (preg_match($reg_2, $link, $matches)){
				$_reg = '/\/(\d+)$/ism';
				$link		=	$href->process_url($matches[1],$root);
				if (!preg_match($_reg, $matches[1], $matches)) {
					continue;
				}
				$arr_id[]	=	$matches[1];
				$arr_alias[]=	'';
//				http://bounce.tucows.com/perl/ppc?id=category&params=pos=1%3Bcpc_id=90343%3Bt=
//				20111031152715&url=http%3A%2F%2Fwww%2Etucows%2Ecom%2Fpreview%2F500609
			}else {
				continue;
			}
			preg_match($reg_date, $titleRight, $matches_date);
			$date	=	$matches_date[1];
			$time 	=	strtotime(trim(strip_tags($date)));
			$time 	=	date('Y-m-d H:i:s',$time); 

			$arr_link[]	=	$link;

			$arr_date[]	=	$time;
		}
		
		$reg_name	=	'/^([^\d]+\s)(\d+\.*.*?)$/ism';	
		
		$date = JFactory::getDate();
		$max_id	=	0;
		for ($i=0; $i<count($arr_link); $i++)
		{
			$article = new DIRsoftware();
			$content = new stdClass();
			
			if ($max_id == 0) {
				$max_id	=	$article->getResult('max(id) as max','SiteID = '.$db->quote($siteID));
				if (!$max_id) {
					$max_id	=	94000000;
				}
			}
			
			if ($id = $article->checkExiting($arr_id[$i])) {
				$content->id		=	$id;
			}else {
				$max_id++;
				$content->id		=	$max_id;
				$content->state 	= 	0;
			}

			$content->pid		=	$arr_id[$i];
			$content->sid		=	1;
//			$content->Icon		=	$arr_icon[$i];			
			$content->ProductName_alias = 	$arr_alias[$i];
			$content->SourceURL 		=	$arr_link[$i];
			$content->Add_Date	 		=	$arr_date[$i];
			$content->firstExtractionTime		= 	$date->toMySQL();
			$content->latestExtractionTime		= 	$date->toMySQL();
			$content->SiteID 			= 	$siteID;
			$content->SiteName 			= 	$SiteName;
//			$content->ProductName 		= 	$arr_title[$i];
//			$SoftwareTitle				=	SoftwareTitle2($arr_title[$i]);
//			$content->ProductName 		= 	$SoftwareTitle[0];
//			$content->version 			= 	$SoftwareTitle[1];
			if ($catid == 0) {
				$content->CategoryID 	= 	$cid;
			}else {
				$content->SectionID		= 	$secid;
				$content->CategoryID	= 	$cid;
			}

			$article->save($content);
		}
		
		if ($paging = $html->find('div[id="titlesnavtop"]',0)) {
			$page++;
			$paging	=	$paging->innertext;			
			$str_reg	=	'/\?pg='.$page.'\&f=all/ism';
			if (!preg_match($str_reg, $paging)) {
				$page = 0;	
			}
		}
		
//die();
		$obj_return	=	new stdClass();	
		return true;
	}

	function getContent($SiteID, & $numbercontent, & $total = 0)
	{
		$software 		=	new DIRsoftware();

		$db			=	JFactory::getDBO();
		$w			=	'state <1 and SiteID = ' . $db->quote($SiteID);
		
		$total		=	$software->getCount($w);
		
		$list_content	=	$software->getList('id,SourceURL,state,ProductName,version,pid',$w,0,$numbercontent,'id');
		
		$option	=	$_REQUEST['option'];
		$browser	=	new phpWebHacks();
		$numbercontent	=	0;
		for ($i=0; $i<count($list_content); $i++)
		{
			print_debug('1','get content['.$i.']['.$list_content[$i]->id.']: &nbsp; '.$list_content[$i]->SourceURL);
			$software = new DIRsoftware();
			$content		=	new stdClass();
			$content->id 	= 	$list_content[$i]->id;
			$content->state = 	intval($list_content[$i]->state) - 1;
			
			$software->save($content);
			$begin		=	md5('BEGIN_GET_CONTENT_FHP100');
			$end		=	md5('END_GET_CONTENT_FHP100');
		
			$url		=	JRoute::_(JURI::root()."index.php?option=$option");	
			$arr_post	=	array();
			$arr_post['task']		=	'tucows.getDetail';
			$arr_post['content_id']	=	$list_content[$i]->id;
			$arr_post['pid']		=	$list_content[$i]->pid;
			$arr_post['SourceURL']	=	$list_content[$i]->SourceURL;
			$arr_post['begin_get_content']	=	$begin;
			$arr_post['end_get_content']	=	$end;		
			
//			echo $url;		
//			$a	=	array();
//			foreach ($arr_post as $k=>$v) {
//				$a[]	=	"$k=$v";
//			}
//			echo '<br /> <hr />';
//			echo implode('&',$a);
//			die();

			$info	=	$browser->post($url,$arr_post);
			
			if (preg_match('/' . $begin . '(.*?)' . $end . '/ism', $info, $match)) 
			{                   
			 $info=trim($match[1]);
			}
			else {
				$message	=	'ERROR_GET_CONTENT_FHP100| #123 API false '.$list_content[$i]->SourceURL.' '.$info;
				JError::raiseWarning('c',$message);				
				continue;
			}
			if (stristr($info,'ERROR_GET_CONTENT_FHP100')) {
				$message	=	'ERROR_GET_CONTENT_FHP100| '.$info;
				JError::raiseWarning('c',$message);
				continue;
			}
			$numbercontent ++;	
				
		}
	}
	
	/**
	 * Get detail of software or drver.	
	 * getdetail
	  		- get content:			2s
	 		- get other version
	 		- get info
	 		- get icon				1s
	 		- ge technical			2s
	 		- get version history	3s
	 		- get images
	 		- store images
	 		- store article
	 		- get screenshot		7s
	 		- store other version	
	 * @param unknown_type $content_id
	 * @param unknown_type $SourceURL
	 * @param unknown_type $content_title
	 * @param unknown_type $siteID
	 * @param unknown_type $arr_param
	 * @return unknown
	 */
	function getDetail($content_id, $SourceURL, $pid, $siteID	=	'fhp100', $SiteName = 'filehippo.com', $arr_param)
	{
		echo $SourceURL;
		echo '<hr />';
		
		$obj_article 	=	new DIRsoftware();
		
		$obj_content	=	$obj_article->get($content_id);
		$date = JFactory::getDate();
		
		$root	=	'http://www.tucows.com/';
		$href	=	new href();		
				
		$browser	=	new phpWebHacks();
		$response	=	$browser->get($SourceURL);
		$html		=	loadHtmlString($response);
	
		$article	=	new stdClass();		
		///////////////////////////////////////////////////////////
		////	GET INFO ///////////////////////////////
		$Tag_whitelist='<p><br><hr><blockquote>'.
									'<b><i><u><sub><sup><strong><em><var>'.
									'<code><xmp><cite><pre><abbr><acronym><address><samp>'.
									'<fieldset><legend>'.
									'<img><h1><h2><h3><h4><h4><h5><h6>'.
									'<ul><ol><li><dl><dt><frame><frameset>';
		print_debug(2,'BEGIN get info');		
		$article->id		=	$content_id;		
		$article->pid		=	$pid;
		$article->SourceURL		=	$SourceURL;
		
		$items			=	$html->find('div[class="atention_block"]',0)->children();
		$ShortDesc		=	'';
		for ($i=0; $i<count($items); $i++)
		{
			if ($items[$i]->tag == 'p') {
				if (trim($items[$i]->innertext) == '') {
					continue;
				}
				$ShortDesc	.=	'<p>'.trim($items[$i]->innertext).'</p>';
			}
			
		}
		
		$article->ShortDesc	=	trim($ShortDesc);
		
		$longDest			=	$html->find('div[class="item_description"]',0);
		//$boxScreenShot		=	$longDest->find('div[id="boxScreenShot"]',0)->innertext;
		$longDest->find('div[id="boxScreenShot"]',0)->outertext	=	'';
		$article->LongDesc	=	strip_tags(mostidy_clean(trim($longDest->innertext)), $Tag_whitelist);
		$article->PageHTML 	 =  $response;

		print_debug(2,'END get info');
		///////////////////////////////////////////////////////////
		////	GET TITLE AND VERSION	///////////////////////////////
		print_debug(2,'BEGIN get title and version');
		$title	=	trim(strip_tags($html->find('h1[class="item_title"]',0)->innertext));
		$SoftwareTitle				=	SoftwareTitle2($title);
		$article->ProductName 		= 	$SoftwareTitle[0];
		$article->version 			= 	$SoftwareTitle[1];
		print_debug(2,'END get title and version');	
		
		///////////////////////////////////////////////////////////
		////	GET ICON	///////////////////////////////
		print_debug(2,'BEGIN get icon');
		$patch_icon	=	$arr_param['patch_icon'];
		if (!is_dir($patch_icon)) {
			mkdir($patch_icon);
		}		
		$root_icon	=	$arr_param['root_icon'];
		$image_name	=	$href->take_file_name($obj_content->ProductName);
		$image_name	=	strtolower(trim(preg_replace('/\d+$/ism','',$image_name),' -_'));		
		print_debug(2,'END get icon');
		$checkSum	=	'';
		///////////////////////////////////////////////////////////
		////	GET TECHNICAL	///////////////////////////////		
		if ($html->find('div[class="item_details_right"]',0)) {
			print_debug(2,'BEGIN get technical');
			
			$tech	=	$html->find('div[class="item_details_right"]',0);
			$items	=	$tech->find('div[class="row"]');

			for ($i=0; $i<count($items); $i++)
			{
				$item	=	$items[$i];
				
				if (!$item->find('div[class="r_left"]',0) or !$item->find('div[class="r_right"]',0)) {
					continue;
				}
				$title	=	trim(strip_tags($item->find('div[class="r_left"]',0)->innertext));
				
				if (preg_match('/License/ism',$title)) {
					$article->License 		=	trim(strip_tags($item->find('div[class="r_right"]',0)->innertext));
					$license				=	convert_license($article->License);
					$article->License 		=	$license[0];
					continue;
				}
				if (preg_match('/Published by:/ism',$title)) {
					$VendorName = ($item->find('div[class="r_right"]',0));
					$article->VendorAurl	=	$VendorName->find('a',0)->href;
					$article->VendorName	=	trim(strip_tags($VendorName->innertext));
					$article->VendorHomepageURL	=	$VendorName->find('a',0)->href;					
					continue;
				}
				
				if (preg_match('/OS:/ism',$title)) {
					$article->Platform1 = trim(strip_tags($item->find('div[class="r_right"]',0)->innertext));					
					continue;
				}	
				if (preg_match('/Cost/ism',$title)) {
					$article->USDPrice = trim(strip_tags($item->find('div[class="r_right"]',0)->innertext));
					continue;
				}
				
			}
			print_debug(2,'END get technical');
		}
		if ($html->find('div[class="file_size"]',0)) {
			$fileSize	=	$html->find('div[class="file_size"]',0)->children(0)->innertext;
			$reg_filesize	=	'/([\d\.]+)\s*(B|KB|MB|GB|TB|PB)/ism';
			preg_match($reg_filesize, $fileSize, $matches_filesize);
			$article->FileSize 		=	convert_file_size($matches_filesize[1],$matches_filesize[2],'B');
		}		
				
		$article->latestExtractionTime	=	$date->toMySQL();
		
		$article->Add_Date 	=	$obj_content->Add_Date;
		
			///////////////////////////////////////////////////////////
			////	GET IMAGES	///////////////////////////////////////
			print_debug(2,'BEGIN get Images');
				mosGetImages($article, $root, $arr_images, $arr_param['patch_image'], $arr_param['root_image']);
			print_debug(2,'END get Images');
			
			///////////////////////////////////////////////////////////
			////	STORE IMAGES	//////////////////////////////////
			print_debug(2,'BEGIN store Images');
			for ($i=0; $i<count($arr_images); $i++)
			{
				$image	=	$arr_images[$i];
				$image->siteID 	= $siteID;
				$image->type	= 2;
				$image->pid		= $pid;
				mosInsertImage($image);
			}
			print_debug(2,'END store Images');
			
			///////////////////////////////////////////////////////////
			////	GET SCREENSHOT	///////////////////////////////	
			print_debug(2,'BEGIN get screenshot');			
			$items	=	$html->find('img[class="item_screen"]');
			$arr_scr	=	array();		
			$image_prefix	=	$href->take_file_name($article->ProductName . ' ' .$article->version).'-'.date("Y",strtotime($article->Add_Date)).'-'.date("m",strtotime($article->Add_Date));	
			
			$path_image	=	$arr_param['patch_image'].DS.date("Y",strtotime($article->Add_Date)).DS.date("m",strtotime($article->Add_Date));
			$root_image	=	$arr_param['root_image'].'/'.date("Y",strtotime($article->Add_Date)).'/'.date("m",strtotime($article->Add_Date));	
//
			for ($i=0; $i<count($items); $i++)
			{
				$number	=	count($arr_images)+1;
				$image_name	=	$image_prefix.'-'.$number;
				
				$item = $items[$i];
				$link_sc	=	$href->process_url($item->src, $root);			
				$arr_scr[]	=	$link_sc;
				$arr_images[]	=	$link_sc;
				mosGetOneImages($pid, $siteID, $image_name, $link_sc, $path_image, '#__smedia2011a', $root_image, 1);			
				$arr_Images[]	=	1;
			}
	
			print_debug(2,'END get screenshot');
	
		$article->DestURL		=	'http://www.tucows.com/thankyou.html?swid='.$pid;		
		$article->DownloadURL	=	'http://www.tucows.com/thankyou.html?swid='.$pid;		
		$article->state	=	1;		
		
		///////////////////////////////////////////////////////////
		////	STORE ARTICLE	///////////////////////////////	
		print_debug(2,'BEGIN store article');
			$obj_article->save($article);
		print_debug(2,'END store article');	
			
	}
}