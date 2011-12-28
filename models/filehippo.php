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

class DIRModelFilehippo extends JModel{	
	
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
				FROM `#__software_category_hippo`
				WHERE id = $cat_id";
			$db->setQuery($query);
			$db->loadObject($obj);
			
			$arr_obj[]	=	$obj;
			$arr_obj[]	=	$obj;
		}else {
			$query = "SELECT *
				FROM `#__software_category_hippo`
				WHERE publish = 1 and (lastGet_param = '' or `lastGet_param` like ".$db->quote('%getold=1;%').")".
				" ORDER BY `last_run` LIMIT 0,2";
			$db->setQuery($query);		
			$arr_obj	=	$db->loadObjectList();
			if (count($arr_obj) == 1) {
				$arr_obj[]		=	$arr_obj[0];
			}
		}

		return $arr_obj;
	}
	
	function getListContent($link,$page =1, $cid, $secid, $catid = 0, $cat_parent, $siteID = 'fhp100', $SiteName ='filehippo.com')
	{
		global $arrErr;
		$db		=	JFactory::getDBO();
		$root	=	'http://www.filehippo.com/';
		$href	=	new href();
	//	http://www.vinacorp.vn/news/thi-truong-chung-khoan
	//	http://www.vinacorp.vn/news/thi-truong-chung-khoan/4		
		echo '<br/>';
		echo $link;	
		echo '<br/>';	
		$browser	=	new phpWebHacks();
		$response	=	$browser->get($link);		
		$arr_link_article	=	array();
		$html		=	loadHtmlString($response);
		$arr_link	=	array();
		$arr_id		=	array();
		$arr_title	=	array();
		$arr_alias	=	array();
//		$arr_icon	=	array();
	//	http://www.vinacorp.vn/news/nhung-bat-ngo-thu-vi-cua-chung-khoan-tuan-qua/ct-478537
		$reg_alias = '/\/([^\/]+)\/$/ism';
		
		// find catNews
		if ($content = $html->find('div[id="content-2col"]',0)) {
			if ($right = $content->find('div[class="right"]',0)) {
				if ($items = $right->find('h2')) {
					for ($i=0; $i<count($items); $i++)
					{
						$item			= 	$items[$i];						
//						$link_icon		= 	$items[$i]->parent()->parent()->children(0)->children(0)->children(0)->src;	
						$link			=	$href->process_url($item->children(0)->href,$root);
						if (preg_match($reg_alias, $link, $matches)) {
							$arr_link[]	=	$link;
							$arr_id[]	=	md5($link);
							$arr_alias[]=	$matches[1];
							$arr_title[]=	strip_tags($item->children(0)->innertext);
//							$arr_icon[]	=	$link_icon;
						}	
					}					
				}	
			}			
		}
		
		$reg_name	=	'/^([^\d]+\s)(\d+\.*.*?)$/ism';	
		$date = JFactory::getDate();
		$max_id	=	0;
		for ($i=0; $i<count($arr_link); $i++)
		{
			$article = new DIRsoftware();
			if ($max_id == 0) {
				$max_id	=	$article->getResult('max(id) as max','SiteID = '.$db->quote($siteID));
				if (!$max_id) {
					$max_id	=	90000000;
				}
			}
			
			$max_id++;
			
			
			$content = new stdClass();
			
			$content->id		=	$max_id;
			$content->pid		=	$arr_id[$i];
			$content->sid		=	1;
//			$content->Icon		=	$arr_icon[$i];			
			$content->ProductName_alias = 	$arr_alias[$i];
			$content->SourceURL 		=	$arr_link[$i];
			$content->firstExtractionTime		= 	$date->toMySQL();
			$content->latestExtractionTime		= 	$date->toMySQL();
			$content->SiteID 			= 	$siteID;
			$content->SiteName 			= 	$SiteName;
			$content->state 			= 	0;
			$SoftwareTitle				=	SoftwareTitle2($arr_title[$i]);
			$content->ProductName 		= 	$SoftwareTitle[0];
			$content->version 			= 	$SoftwareTitle[1];
			if ($catid == 0) {
				$content->CategoryID 	= 	$cid;
			}else {
				$content->SectionID		= 	$secid;
				$content->CategoryID	= 	$cid;
			}
			
			
			$article->save($content);	
		}
		$db 	= JFactory::getDBO();
		$query	=	'UPDATE `#__software_category_hippo` 
					SET `last_run` = '.$db->quote(date ( 'Y-m-d H:i:s' )).', 
						`lastGet_param` = '.$db->quote(" ").'
					WHERE `id` ='. $cid;
		$db->setQuery($query);
		$db->query();
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
		
		$list_content	=	$software->getList('id,SourceURL,state,ProductName,version,pid',$w,0,$numbercontent, 'id');
	
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
			$arr_post['task']		=	'filehippo.getDetail';
			$arr_post['content_id']	=	$list_content[$i]->id;
			$arr_post['pid']		=	$list_content[$i]->pid;
			$arr_post['content_title']		=	$list_content[$i]->ProductName.' '.$list_content[$i]->version;
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
	function getDetail($content_id, $SourceURL, $pid, $content_title, $siteID	=	'fhp100', $SiteName = 'filehippo.com', $arr_param)
	{
		echo $SourceURL;
		echo '<hr />';
		$obj_article 	=	new DIRsoftware();
		$obj_content	=	$obj_article->get($content_id);
		$date = JFactory::getDate();
		
		$root	=	'http://www.filehippo.com/';
		$href	=	new href();		
				
		$browser	=	new phpWebHacks();
		$response	=	$browser->get($SourceURL);
		$html		=	loadHtmlString($response);
		$share_button	=	$html->find('a[class="twitter-share-button"]',0)->outertext;		
		
		$check_id	=	'/\/([^\/]+)\/(\d+)\/$/ism';
		$reg_pid	=	'/data-url="([^"]*\/(\d+)\/)"/ism';
		$article	=	new stdClass();
		///////////////////////////////////////////////////////////
		////	GET OTHER VERSION	///////////////////////////////
		$arr_link	=	array();
		$arr_title	=	array();
		$arr_id		=	array();
		$arr_alias	=	array();
		print_debug(2,'BEGIN get other version');
		$isSub	=	false;	
		if ($subVersion	=	$html->find('div[class="desc"]',0)->find('div[class="subprograms"]',0)) {				
			////	GET SUB VERSION	///////////////////////////////			
			$items		=	$subVersion->find('a[class="title"]');
			
			$reg_pid	=	'/\/([^\/]+)\/$/ism';
			for ($i=0; $i<count($items); $i++)
			{
				$item	=	$items[$i];
				$link	=	$href->process_url($item->href, $root);
				$title	=	trim(strip_tags($item->innertext));
				if (!preg_match($reg_pid, $link, $matches_id)) {
					continue;
				}
				$arr_alias[]=	$matches_id[1];
				$arr_id[]	=	md5($link);
							
				$arr_link[]	=	$link;
				$arr_title[]	=	$title;
			}
			$article->state 	 			=  2;
			$isSub	=	true;
		}else if (!$previewID = $obj_content->PreviousVersions) {
			$list_version	=	$html->find('div[id="dlbox"]',0);
			$arr_link	=	array();			
			$this->getOtherVersion($list_version, $pid, $previewID, $arr_link, $arr_title, $arr_alias, $arr_id);
		}
		
		print_debug(2,'END get other version');
		
		///////////////////////////////////////////////////////////
		////	GET INFO ///////////////////////////////
		print_debug(2,'BEGIN get info');
		$desc		=	$html->find('div[class="desc"]',0)->innertext;
		
		$obj_cuttext	=	new AutoCutText($desc,10);	
		
		$article->id		=	$content_id;		
		$article->pid		=	$pid;
		$article->SourceURL		=	$SourceURL;
		$article->ShortDesc	=	trim($obj_cuttext->getIntro());
		$article->LongDesc	=	trim(($desc));
		$article->ProductName	=	$obj_content->ProductName;
		$article->version		=	$obj_content->version;
		$article->PageHTML 	 =  $response;
		print_debug(2,'END get info');
		
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
		$link_icon	=	$href->process_url($html->find('img[itemprop="image"]',0)->src, $root);
		if ($link_icon = mosGetOneImages($pid, $siteID, $image_name, $link_icon, $patch_icon, '#__smedia2011a', $root_icon, 3)) {
			$article->Icon	=	$link_icon;
		}
		
		print_debug(2,'END get icon');
		$checkSum	=	'';
		///////////////////////////////////////////////////////////
		////	GET TECHNICAL	///////////////////////////////		
		if ($html->find('a[title="Technical"]',0)) {
			print_debug(2,'BEGIN get technical');
			$technical		=	$href->process_url($html->find('a[title="Technical"]',0)->href, $root);
			$html_tech		=	loadHtmlString($browser->get($technical));
			
			$tech	=	$html_tech->find('div[class="desc"]',0);
			$items	=	$tech->children(0)->children();
			
			for ($i=0; $i<count($items); $i++)
			{
				$item	=	$items[$i];
				$title	=	trim(strip_tags($item->children(0)->innertext));
				if (preg_match('/Title:/ism',$title)) {
					$content_title = trim(strip_tags($item->children(1)->innertext));
					continue;
				}
				if (preg_match('/Requirements:/ism',$title)) {
					$article->Requirement = trim(strip_tags($item->children(1)->innertext));					
					continue;
				}
				if (preg_match('/License:/ism',$title)) {
					$article->License 		=	trim(strip_tags($item->children(1)->innertext));
					$license				=	convert_license($article->License);					
					$article->License 		=	$license[0];
					continue;
				}
				if (preg_match('/Languages:/ism',$title)) {
					$article->language 		=	trim(strip_tags($item->children(1)->innertext));
					continue;
				}
				if (preg_match('/File size:/ism',$title)) {
					$article->FileSize 		=	trim(strip_tags($item->children(1)->innertext));
					if (preg_match('/\(([^\s\)]+)\s*(bytes|byte)\)/ism',$article->FileSize, $mathces)) {
						$article->FileSize	=	str_replace(',','',$mathces[1]);
						$article->FileSize	=	str_replace('.','',$article->FileSize);
					}					
					continue;
				}
				if (preg_match('/Date added:/ism',$title)) {
					$time 	 				=	strtotime(trim(strip_tags($item->children(1)->innertext)));
					$time 					=	date('Y-m-d H:i:s',$time); 
					$article->Add_Date 	 	=	$time;
					continue;
				}
				if (preg_match('/Author:/ism',$title)) {
					$VendorName = ($item->children(1));
					$article->VendorAurl	=	$VendorName->find('a',0)->href;
					for ($j=0; $j<count($VendorName->children()); $j++)
					{
						$VendorName->children($j)->outertext = '';
					}
					$VendorName	=	$VendorName->innertext;		
					$article->VendorName	=	trim(strip_tags($VendorName));	
					continue;
				}
				if (preg_match('/Homepage:/ism',$title)) {
					$article->VendorHomepageURL	=	trim(strip_tags($item->children(1)->innertext));
					continue;
				}
				if (preg_match('/MD5 Checksum:/ism',$title)) {
					$checkSum	=	trim(strip_tags($item->children(1)->innertext));
					continue;
				}
			}			
			$article->PreviousVersions	=	$previewID;
			$article->latestExtractionTime	=	$date->toMySQL();
			print_debug(2,'END get technical');
		}
		
		///////////////////////////////////////////////////////////
		////	GET VERTION HISTORY	///////////////////////////////
		if ($html->find('a[title="Change Log"]',0)) {
			print_debug(2,'BEGIN get Version history');	
			$chang_log		=	$href->process_url($html->find('a[title="Change Log"]',0)->href, $root);
			$html_log		=	loadHtmlString($browser->get($chang_log));		
			$article->version_history	=	$html_log->find('div[class="desc"]',0)->innertext;
			print_debug(2,'END get Version history');
		}
		
		if (!$isSub) {
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
			$items	=	$html->find('a[class="scr"]');
			$arr_scr	=	array();		
			$image_prefix	=	$href->take_file_name($article->ProductName . ' ' .$article->version).'-'.date("Y",strtotime($article->Add_Date)).'-'.date("m",strtotime($article->Add_Date));	
			
			$path_image	=	$arr_param['patch_image'].DS.date("Y",strtotime($article->Add_Date)).DS.date("m",strtotime($article->Add_Date));
			$root_image	=	$arr_param['root_image'].'/'.date("Y",strtotime($article->Add_Date)).'/'.date("m",strtotime($article->Add_Date));	
			
			for ($i=0; $i<count($items); $i++)
			{
				$number	=	count($arr_images)+1;
				$image_name	=	$image_prefix.'-'.$number;
				
				$item = $items[$i];
				$link_sc	=	$href->process_url($item->href, $root);
				$html_sc	=	loadHtmlString($browser->get($link_sc));
				$content	=	$html_sc->find('div[id="content-full"]',0);
				$content	=	$content->find('center',1);
				$link_sc	=	$content->find('img',0)->src;
				$arr_scr[]	=	$link_sc;
				$arr_images[]	=	$link_sc;
				mosGetOneImages($pid, $siteID, $image_name, $link_sc, $path_image, '#__smedia2011a', $root_image, 1);			
				$arr_Images[]	=	1;
			}
			print_debug(2,'END get screenshot');
		}
		if (!$isSub) {
			if (preg_match($check_id, $SourceURL)) {
				$article->state	=	1;
				// 
//				$path_file	=	$arr_param['patch_file'];
//				if (!is_dir($path_file)) {
//					mkdir($path_file);
//				}
//				$path_file	.=	DS.date("Y",strtotime($article->Add_Date));
//				if (!is_dir($path_file)) {
//					mkdir($path_file);		
//				}	
//				$path_file	.=	DS.date("m",strtotime($article->Add_Date));
//				if (!is_dir($path_file)) {
//					mkdir($path_file);		
//				}
//			
//				$dowload	=	new DirDownload_cdn2011();
//				$dow		=	new stdClass();
//				$dow->SiteID 	=	$siteID;
//				$dow->SiteName 	=	$SiteName;
//				$dow->SourceURL	=	$SourceURL;
//				$dow->Checksum	=	$checkSum;
//				$dow->Path		=	$path_file;
//				$dow->FileName	=	$href->take_file_name($content_title);
//				$dow->state		=	0;
//				$dow->PA_ID		=	$article->id;
//				$dowload->save($dow);
			}else {
				$article->state	=	3;
			}
		}
		
		///////////////////////////////////////////////////////////
		////	STORE ARTICLE	///////////////////////////////	
		print_debug(2,'BEGIN store article');
			$obj_article->save($article);
		print_debug(2,'END store article');	
		
		///////////////////////////////////////////////////////////
		////	STORE OTHER VERSION	///////////////////////////////	
		print_debug(2,'BEGIN store other version');		
		$reg_name	=	'/^([^\d]+\s)(\d+\.*.*?)$/ism';	
		$date 		= JFactory::getDate();		
		$this->storeOtherVersion($arr_link, $arr_title, $arr_id, $arr_alias, $siteID, $SiteName, $isSub, $article->SectionID, $article->CategoryID);
		print_debug(2,'END store other version');		
	}
	
	function storeOtherVersion($arr_link, $arr_title, $arr_id, $arr_alias, $siteID, $SiteName, $isOther = true, $secid, $catid)
	{
		$db	=	JFactory::getDBO();
		$reg_name	=	'/^([^\d]+\s)(\d+\.*.*?)$/ism';	
		$date 		= JFactory::getDate();		
		$max_id		=	0;
		
		$setID	=	0;
		
		for ($i=0; $i<count($arr_link); $i++)
		{
			if ($i<count($arr_id) -1) {
				$previewID	=	$arr_id[$i+1];
			}else {
				$previewID	=	0;
			}			
			if ($isOther == true) {
				$previewID	=	0;
			}
			
			$software = new DIRsoftware();
			$content = new stdClass();
			
			if ($max_id == 0) {
				$max_id	=	$software->getResult('max(id) as max','SiteID = '.$db->quote($siteID));
				if (!$max_id) {
					$max_id	=	90000001;
				}
			}
			
			if ($id = $software->checkExiting($arr_id[$i])) {
				$content->id		=	$id;
			}else {
				$max_id++;
				$content->id		=	$max_id;
				$content->state 	= 	0;
			}
			if ($i == 0) {
				$setID	=	$content->id;
			}
			
			$content->ProductSetID	=	$setID;
			
			$content->pid		=	$arr_id[$i];
			$content->sid		=	1;
//			$content->Icon		=	$link_icon;
			$content->PreviousVersions 	= 	$previewID;
			$content->ProductName_alias = 	$arr_alias[$i];
			$content->SourceURL 		=	$arr_link[$i];
			$content->firstExtractionTime		= 	$date->toMySQL();
			$content->latestExtractionTime		= 	$date->toMySQL();
			$content->SiteID 			= 	$siteID;
			$content->SiteName 			= 	$SiteName;			
			$SoftwareTitle				=	SoftwareTitle2($arr_title[$i]);
			$content->ProductName 		= 	$SoftwareTitle[0];
			$content->version 			= 	$SoftwareTitle[1];
			$content->SectionID			= 	$secid;
			$content->CategoryID		= 	$catid;	
			$software->save($content);
			echo '<hr />';

		}
	}
	
	function getOtherVersion($list_version, $pid, & $previewID = 0, & $arr_link, & $arr_title, & $arr_alias, & $arr_id)
	{
		$previewID = 0;
		$root	=	'http://www.filehippo.com';
		$href	=	new href();
		$browser	=	new phpWebHacks();
		$list	=	$list_version->find('center small',0);
		$link_more	=	$href->process_url($list->last_child()->href, $root);
		$bool	=	true;
		
		$arr_link	=	array();
		$arr_title	=	array();
		$arr_id		=	array();
		$arr_alias	=	array();
		
		$reg_pid	=	'/\/([^\/]+)\/(\d+)\/$/ism';
		$page	=	1;		
		do {
			$html_more	=	loadHtmlString($browser->get($link_more));
			
			$items		=	$html_more->find('div[class="desc"]',0)->children();
			for ($i=0; $i<count($items); $i++)
			{
				$item	=	$items[$i];
				$link	=	$href->process_url($item->children(1)->href, $root);
				$title	=	trim(strip_tags($item->children(1)->innertext));
				if (!preg_match($reg_pid, $link, $matches_id)) {
					continue;
				}
				$arr_alias[]=	$matches_id[1];
				$arr_id[]	=	$matches_id[2];
				if ($previewID == -1) {
					$previewID	=	$matches_id[2];
				}
				if ($pid == $matches_id[2]) {
					$previewID	=	-1;
				}				
				$arr_link[]	=	$link;
				$arr_title[]	=	$title;
			}
			$page++;
			$middle	=	$html_more->find('div[class="middle"]',0);
			$middle->find('div[class="desc"]',0)->outertext	=	'';
			$middle	=	$middle->innertext;
			
			$reg_page	=	'/<a href="([^"]*\/history\/'.$page.'\/)">\s*'.$page.'\s*<\/a>/ism';
			if (preg_match($reg_page, $middle, $matches_page)) {
				$bool	=	true;
				$link_more	=	$href->process_url($matches_page[1], $root);
			}else {
				$bool	=	false;
			}

		}while ($bool);
		return true;
	}
	
	function getFile($siteID = 'fhp100', $patch_file = 'media/soft_driver/fhp100')
	{		
		$href	=	new href();	
	    ini_set('display_errors',true);//Just in case we get some errors, let us know....

		  $curl_options_header0 = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => true,    // don't return headers
        CURLOPT_NOBODY         => true,
        CURLOPT_CUSTOMREQUEST  => 'HEAD',
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11", // who am i
//        CURLOPT_USERAGENT      => "Mozilla/5.0 (compatible; Konqueror/4.1; DragonFly) KHTML/4.1.4 (like Gecko)", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    );

    $curl_options_header = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => true,    // don't return headers
        CURLOPT_NOBODY         => true,
        CURLOPT_CUSTOMREQUEST  => 'HEAD',
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    );

    $curl_options_html = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => true,    // don't return headers
 //       CURLOPT_NOBODY         => true,
 //       CURLOPT_CUSTOMREQUEST  => 'HEAD',
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    );  
    
		$SiteName = 'filehippo.com';  
//		$path_save ='D:\\wamp\\www\\software_driver\\media\soft_driver\\fhp100';
		
		//$SiteName = 'Esellerate';  
		//$path_save ='C:\\ws\\downloadfile\\esl\\';
		
		$database	=	JFactory::getDBO();
		$SiteID 	=	'fhp100';
		
		$dbname='software_driver';
		$table_source='#__software2011a';
		
		$dbname_save='software_driver';
		$table_save='#__download_cdn2011';
		
		$query = "SELECT max(PA_ID) FROM  $dbname_save.`$table_save` WHERE `SiteID`=". $database->quote($SiteID);
		$database->setQuery($query);
		
		$maxid = $database->loadResult();
		if (!isset ($maxid)) $maxid = 0;

		$type_get	=	JRequest::getVar('type_get',1);
		if ($type_get == 1) {
/*		get new soft	*/
			$query	=	"SELECT id, SourceURL, Add_Date, pid, DestURL, DownloadURL ".
							"FROM $dbname.`$table_source` ".
							"WHERE `SiteID`=". $database->quote($SiteID)."  AND id> $maxid and state = 1 ORDER BY id limit 10"; // AND id> $maxid 	
		}elseif ($type_get == 2)
		{
/*		try get soft is wrong	*/
			$query	=	"SELECT id, SourceURL, Add_Date, pid, DestURL, DownloadURL".
								"FROM $dbname.`$table_source` ".
								"WHERE id in (SELECT PA_ID ".
												"FROM  $dbname.`$table_save` ".
												"WHERE DownloadState<>200)"; // AND id> $maxid	
		}elseif ($type_get == 3)
		{
/*		check soft	*/
			$query	=	"SELECT t1.id,t1.id as pid,t1.DestURL,DownloadURL ".
								"FROM $dbname.`$table_source` t1 ".
								"WHERE  t1.id not in (SELECT PA_ID ".
														"FROM $dbname_save.`$table_save` )   ".
								"ORDER BY t1.id limit 1"; // AND t1.id> $maxid id not in (SELECT PA_ID FROM $dbname_save.`$table_save` ) AND	
		}	
		
		$database->setQuery( $query );
		
		$rows=$database->loadObjectList();  
		$countrow = count($rows);
		echo $database->getQuery(); 
//		echo $countrow;
//		die;
		//                    $database->query() or die($database->stderr());  
		IF ($countrow>0) {
		    
		    foreach ($rows as $row) 
		    {
		    	$SourceURL 		=	urldecode($row->SourceURL);
		        $downloadpage 	=	urldecode($row->DownloadURL );
		        
			    echo '<hr />';			   
			    print_debug(3,'Step 1: '.$SourceURL);			    

		        $PA_ID=$row->id;
		        $PA_ID2=$row->id  + 1234 ;
		         
		        if (strpos($SourceURL,'filehippo.com/')!==false)
		        {
		            $SourceURL_c=str_replace(' ','%20',$SourceURL);
		            $ch = curl_init($SourceURL_c);
		            curl_setopt_array( $ch, $curl_options_header );
		            $content = curl_exec ($ch);

		            $header_items=curl_getinfo($ch);
		            curl_close ($ch);

		            $header_items['content_type']=trim($header_items['content_type']);
		            print_debug(3,'Step 2: ');
		    		echo '<br/>'. print_r($header_items);

		    		if (strpos($header_items['content_type'],'text/html')===false AND $header_items['content_type']!=='')
		            {// khong fai la trang text/html
		            	print_debug(3,'Step 2a0 ');		    			
		                $finaldownloadURL=$header_items['url'];
		            } else if (strpos($header_items['content_type'],'text/html')!==false | $header_items['content_type']=='')
		            {
		            	print_debug(3,'Step 2a1 '.$SourceURL);		    			
		                 if (strpos($SourceURL,'filehippo.com/download')!==false)  {// no 
		                 	print_debug(3,'Step 2a2 '.$SourceURL);
		                        $SourceURL_c=str_replace(' ','%20',$SourceURL);
		                        $ch = curl_init($SourceURL_c);
		
		                        curl_setopt_array( $ch, $curl_options_html );
		                        $content = curl_exec ($ch);
		                        $header_items=curl_getinfo($ch);
		                        curl_close ($ch); 
		                        $html	=	loadHtmlString($content);
		                        $dlbox	=	$html->find('div[id="dlbox"]',0);
		                       
		                        $SourceURL=getDownloadPage($dlbox->innertext);
		                        $SourceURL	=	$href->process_url($SourceURL,$SourceURL_c);			    				
		                }else if (strpos($SourceURL,'/download/')===false) {
		                	print_debug(3,'Step 2c '.$SourceURL);		    				 
		                  // get full body html
		                    $SourceURL_c=str_replace(' ','%20',$SourceURL);   
		                    $ch = curl_init($SourceURL_c);
		
		                    curl_setopt_array( $ch, $curl_options_html );
		                    $content = curl_exec ($ch);                
		                    $header_items=curl_getinfo($ch);
		                    curl_close ($ch);
		                     print_r($SourceURL);               
		                    $finaldownloadURL_temp=getMetaRedirect($content);
		                      
		                    $SourceURL_c=str_replace(' ','%20',$finaldownloadURL_temp);   
		                    $ch = curl_init($SourceURL_c);
		                    
		                    curl_setopt_array( $ch, $curl_options_html );
		                    $content = curl_exec ($ch);                
		                    $header_items=curl_getinfo($ch);
		                    curl_close ($ch);
		                    
		                    $SourceURL=getMetaRedirect($content);                                      
			             }
			             print_debug(3,'Step 3 '.$SourceURL);
		                // continute:
		                $SourceURL_c=str_replace(' ','%20',$SourceURL);  
		                $ch = curl_init($SourceURL_c);
		
		                curl_setopt_array( $ch, $curl_options_html );
		                $content = curl_exec ($ch);                
		                $header_items=curl_getinfo($ch);
		                curl_close ($ch);               
		               	print_r($header_items);
		 //               echo   $content;
		
		                $finaldownloadURL=getFinalDownloadURL($content);		                
		                $finaldownloadURL	=	$href->process_url($finaldownloadURL,$SourceURL_c);		                
		             }
//		http://www.filehippo.com/download/file/cad237331a401a5198db9fb2873724c412a65d13be7214153fea6046bf0509fd/
					if (strpos($finaldownloadURL,'filehippo.com/download/file/')!==false)
		            {
		            	print_debug(3,'Step 4 '.$finaldownloadURL);
						$path_save	=	$patch_file;
		             	
						if (!is_dir($path_save)) {
							mkdir($path_save);
						}
						$path_save	.=	DS.date("Y",strtotime($row->Add_Date));
						if (!is_dir($path_save)) {
							mkdir($path_save);
						}	
						$path_save	.=	DS.date("m",strtotime($row->Add_Date));
						if (!is_dir($path_save)) {
							mkdir($path_save);
						}
						
//						print_debug(4,'Step 4a: Begin getinfo: '.$finaldownloadURL);
//						  $ch = curl_init($finaldownloadURL);		
//						  curl_setopt_array( $ch, $curl_options_html );
//						  $content = curl_exec ($ch);
//						  $header_items=curl_getinfo($ch);
//						  print_r($header_items);
//						  curl_close ($ch);      						
//						$filename		=	getDownloadFilename($header_items['url']);
//						$filename_save	=	$PA_ID2.'-'.$filename;
//						$finaldownloadURL	=	$header_items['url'];
//		             	print_debug(4,'Step 4b: End getinfo '.$finaldownloadURL);
						//******//
						$filename_save	=	$filename	=	'tmp_file';
						//*//
		             	
		             	print_debug(3,'Step 5: BEGIN get file: '.$path_save);
		             		$downloadInfo	=	downloadFile( $finaldownloadURL,$path_save.DS,$filename_save);
		             		print_r($downloadInfo);
		             	print_debug(3,'Step 5: END get file: '.$path_save);
		             	
		               if ($downloadInfo['http_code']<>200)
		               		$downloadInfo	=	downloadFile( $finaldownloadURL,$path_save,$filename_save);

		               if ($downloadInfo['http_code'] == 200)
		               {
		               		print_debug(3,'Step 6 '.$downloadInfo['url']);
		               		$filename	=	getDownloadFilename($downloadInfo['url']);
		               		//******//
		               		$_filename	=	$PA_ID2.'-'.$filename;
		               		if (file_exists($path_save.DS.$_filename)) {
		               				print_debug(4,'Step 6a: remove file existing: '.$_filename);
		               			JFile::delete($path_save.DS.$_filename);
		               		}
		               		
		               		print_debug(4,'Step 6b: rename file: '.$filename_save.' => '.$_filename);
			               	rename($path_save.DS.$filename_save,$path_save.DS.$_filename);

			                //*//
							$download_content_type	=	$downloadInfo['content_type'];
							$download_finalurl		=	$downloadInfo['url'];
							$download_state			=	$downloadInfo['http_code'];
							$download_total_time	=	$downloadInfo['total_time'];
//							$download_size			=	convert_file_size($downloadInfo['size_download'],'b','mb');
							$download_size			=	$downloadInfo['size_download'];
							$download_speed			=	$downloadInfo['speed_download'];
							$download_content_length=	$downloadInfo['download_content_length'];
							
							$SourceURL2			=	mysql_escape_string($SourceURL);
							$path_save2			=	mysql_escape_string($path_save);
							$filename2			=	mysql_escape_string($_filename);		//	FileName
							$filename_save2		=	mysql_escape_string($filename);	// OriginalFileName
							$download_content_type2 =	mysql_escape_string($download_content_type);
							$download_finalurl2	=	mysql_escape_string($download_finalurl);
							print_debug(3,'Step 6, update database; '.$filename);

			                $query="REPLACE INTO $dbname_save.`$table_save` 
			                					(id, wsid,`PA_ID`, `SiteID`, `SiteName`, `SourceURL`,
			                					 `FinalDownloadURL`, `ContentType`, `TotalTime`, `SizeDownload`,
			                					 `SpeedDownload`, `DownloadContentLength`, `OriginalFileName`, `FileName`,
			                					 `Path`, `DownloadState`, `Approved`, `DownloadDate`)
			                			VALUES ($PA_ID2, $PA_ID,$PA_ID, '$SiteID', '$SiteName','$SourceURL2',
			                					'$download_finalurl2', '$download_content_type2', '$download_total_time','$download_size',
			                					'$download_speed', '$download_content_length', '$filename_save2','$filename2',
			                					'$path_save2', '$download_state', '0',now())" ;			               	
		                    $database->setQuery($query);
		                    $database->query() or die($database->stderr());  
		            	   }else {
				         	      	$SourceURL2=mysql_escape_string($SourceURL);  
				                    $download_finalurl2=mysql_escape_string($finaldownloadURL);
				                    $download_content_type2=mysql_escape_string($header_items['content_type']);
				                 	$query="REPLACE INTO $dbname_save.`$table_save` ( id,wsid,`PA_ID`, `SiteID`, `SiteName`, `SourceURL`, `FinalDownloadURL`, `ContentType`, `TotalTime`, `SizeDownload`, `SpeedDownload`, `DownloadContentLength`, `OriginalFileName`, `FileName`, `Path`, `DownloadState`, `Approved`, `DownloadDate`) VALUES ( $PA_ID2,$PA_ID,$PA_ID, '$SiteID', '$SiteName', '$SourceURL2','$download_finalurl2', '$download_content_type2', '', '','', '', '','', '', '1', '0',now())" ;
				                  
				                    $database->setQuery($query);
				                    $database->query() or die($database->stderr());
		               		}
		               
		              } else {
		                    $SourceURL2			=	mysql_escape_string($SourceURL);
		                    $download_finalurl2	=	mysql_escape_string($finaldownloadURL);
		                    $download_content_type2	=	mysql_escape_string($header_items['content_type']);		                                                                     
		                 	$query="REPLACE INTO $dbname_save.`$table_save` ( id,wsid,`PA_ID`, `SiteID`, `SiteName`, `SourceURL`, `FinalDownloadURL`, `ContentType`, `TotalTime`, `SizeDownload`, `SpeedDownload`, `DownloadContentLength`, `OriginalFileName`, `FileName`, `Path`, `DownloadState`, `Approved`, `DownloadDate`) VALUES ( $PA_ID2,$PA_ID,$PA_ID, '$SiteID', '$SiteName', '$SourceURL2','$download_finalurl2', '$download_content_type2', '', '', '', '', '','', '', '1', '0',now())" ;
		                  	$database->setQuery($query);
		                    $database->query() or die($database->stderr());
		        	}
		    	}		    
			}
		}
		return $countrow;
	}
}