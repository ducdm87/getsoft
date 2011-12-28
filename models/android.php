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

class DIRModelAndroid extends JModel{	
	
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
				FROM `#__software_category_androi`
				WHERE id = $cat_id AND parent <> 0";
			$db->setQuery($query);
			$obj	=	$db->loadObject();
			
			$arr_obj[]	=	$obj;
			$arr_obj[]	=	$obj;
		}else {
			$query = "SELECT *
				FROM `#__software_category_androi`
				WHERE publish = 1 and (lastGet_param = '' or `lastGet_param` NOT LIKE ".$db->quote('%getold=0||0;%').")  AND parent <> 0".
				" ORDER BY `last_run` LIMIT 0,2";
			$db->setQuery($query);		
			$arr_obj	=	$db->loadObjectList();
			if (count($arr_obj) == 1) {
				$arr_obj[]		=	$arr_obj[0];
			}
		}	
		
		return $arr_obj;
	}
	
	function getListContent($link,$cat_alias, &$getold = array(0,0), &$page = array(0,0), $cid, $secid, $catid = 0, $cat_parent, $siteID = 'mad93', $SiteName ='android.com')
	{
		global $arrErr;
		$db		=	JFactory::getDBO();
		$root	=	'https://market.android.com';
		$href	=	new href();
	
		$start_free	=	$page[0]	*	24;
		$start_paid	=	$page[1]	*	24;
		
	//	https://market.android.com/details?id=apps_topselling_paid&cat=ARCADE&start=24&num=24
		$link_free	=	"https://market.android.com/details?id=apps_topselling_free&cat=$cat_alias&start=$start_free&num=24";
		$link_paid	=	"https://market.android.com/details?id=apps_topselling_paid&cat=$cat_alias&start=$start_paid&num=24";
		
		echo '<br />';
		echo $link;
		echo '<br />';
		echo $link_free;
		echo '<br />';
		echo $link_paid;
		echo '<br />';
		
		$browser	=	new phpWebHacks();
		$response_free	=	$browser->get($link_free);		
		$response_paid	=	$browser->get($link_paid);	
		
		$arr_link_article	=	array();
		$html_free		=	loadHtmlString($response_free);
		$html_paid		=	loadHtmlString($response_paid);
		$arr_link	=	array();
		$arr_id		=	array();
		$arr_title	=	array();
		$arr_alias	=	array();
		$arr_intro	=	array();
//		$arr_icon	=	array();
	
		
		$items_free	=	$html_free->find('li[class="goog-inline-block"]');
		$items_paid	=	$html_paid->find('li[class="goog-inline-block"]');
		$items		=	array_merge($items_free, $items_paid);		
		
		$reg_alias = '/\/([^\/]+)\/$/ism';
		// find catNews
		
		for ($i=0; $i<count($items); $i++)
		{
			$item			= 	$items[$i];						
//			$link_icon		= 	$items[$i]->parent()->parent()->children(0)->children(0)->children(0)->src;	
			$link			=	$href->process_url($item->find('a[class="title"]',0)->href,$root);			
			$arr_link[]	=	$link;
			if (preg_match('/id=([^=&]+)&/ism', $link, $matches)) {
				$arr_id[]	=	$matches[1];
			}else {
				preg_match('/id=([^=&]+)$/ism', $link, $matches);
				$arr_id[]	=	$matches[1];
			}
										
			$arr_title[]=	strip_tags($item->find('a[class="title"]',0)->innertext);
			$arr_intro[]=	strip_tags($item->find('p[class="snippet-content"]',0)->innertext);
//			$arr_icon[]	=	$link_icon;
		}
//		dump_data($arr_link, $arr_id, $arr_alias, $arr_title, $arr_intro);
		
		$reg_name	=	'/^([^\d]+\s)(\d+\.*.*?)$/ism';	
		$date = JFactory::getDate();
		$max_id	=	0;
		for ($i=0; $i<count($arr_link); $i++)
		{
			$article = new DIRsoftware();
			if ($max_id == 0) {
				$max_id	=	$article->getResult('max(id) as max','SiteID = '.$db->quote($siteID));
				if (!$max_id) {
					$max_id	=	93000000;
				}
			}

			if ($id = $article->checkExiting($arr_id[$i])) {
				$content->id		=	$id;
			}else {
				$max_id++;
				$content->id		=	$max_id;
				$content->state 	= 	0;
			}

			$content = new stdClass();

			$content->id		=	$max_id;
			$content->pid		=	$arr_id[$i];
			$content->sid		=	1;
//			$content->Icon		=	$arr_icon[$i];			
//			$content->ProductName_alias = 	$arr_alias[$i];
			$content->SourceURL 		=	$arr_link[$i];
			$content->firstExtractionTime		= 	$date->toMySQL();
			$content->latestExtractionTime		= 	$date->toMySQL();
			$content->SiteID 			= 	$siteID;
			$content->SiteName 			= 	$SiteName;			
			$content->ProductName 		= 	$arr_title[$i];
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
		
		// process page		
		$str_page	=	'';
		if ($paging		=	$html_free->find('div[class="num-pagination-control"]',0)) {
			$page[0]	=	$page[0] + 1;
			if ($paging->find('div[data-pageid="'.$page[0].'"]')) {
				$start_free	=	strip_tags($paging->find('div[data-pageid="'.$page[0].'"]')->innertext);
				if ($page[0] != $start_free) {
						$page[0]	=	0;
						$getold[0]	=	0;
					}
			}
		}else {
			$page[0]	=	0;
			$getold[0]	=	0;
		}
		

		if ($paging	=	$html_paid->find('div[class="num-pagination-control"]',0)) {
			$page[1]	=	$page[1] + 1;
			if ($paging->find('div[data-pageid="'.$page[1].'"]')) {
				$start_paid	=	strip_tags($paging->find('div[data-pageid="'.$page[1].'"]')->innertext);
				if ($page[1] != $start_paid) {
						$page[1]	=	0;
						$getold[1]	=	0;
					}
			}
		}else {
			$page[1]	=	0;
			$getold[1]	=	0;
		}		
		$obj_return	=	new stdClass();	
		return $cid;
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
			$arr_post['task']		=	'android.getDetail';
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
		
//		$SourceURL	=	'https://market.android.com/details?id=com.halfbrick.fruitninjafree&feature=apps_topselling_free';
		echo $SourceURL;		
		
		echo '<hr />';
		$obj_article 	=	new DIRsoftware();
		$obj_content	=	$obj_article->get($content_id);
		$date = JFactory::getDate();
		
		$root	=	'https://market.android.com';
		$href	=	new href();		
				
		$browser	=	new phpWebHacks();
		$response	=	$browser->get($SourceURL);
		
		$html		=	loadHtmlString($response);
		
		$check_id	=	'/\/([^\/]+)\/(\d+)\/$/ism';
		$reg_pid	=	'/data-url="([^"]*\/(\d+)\/)"/ism';
		$article	=	new stdClass();
		///////////////////////////////////////////////////////////
		////	GET OTHER VERSION	///////////////////////////////
		$arr_link	=	array();
		$arr_title	=	array();
		$arr_id		=	array();
		$arr_alias	=	array();		
		
		///////////////////////////////////////////////////////////
		////	GET INFO ///////////////////////////////
		print_debug(2,'BEGIN get info');
		$desc		=	$html->find('div[id="doc-description-container"]',0)->innertext;
		
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
		
		$link_icon	=	$href->process_url($html->find('div[class="doc-banner-icon"]',0)->children(0)->src, $root);
		
		
		if ($link_icon = mosGetOneImages($pid, $siteID, $image_name, $link_icon, $patch_icon, '#__smedia2011a', $root_icon, 3)) {
			$article->Icon	=	$link_icon;
		}
		
		print_debug(2,'END get icon');
		$checkSum	=	'';
		///////////////////////////////////////////////////////////
		////	GET DOC-METADATA	///////////////////////////////		
		if ($metadata = $html->find('div[class="doc-metadata"]',0)) {
			
			print_debug(2,'BEGIN get doc-metadata');
			
			$time	=	trim(strip_tags($metadata->find('time[itemprop="datePublished"]',0)->innertext));
			
			$time	=	strtotime($time);
			$time	=	date('Y-m-d H:i:s',$time); 

			$article->Add_Date 	 	=	$time;
			$article->softwareVersion 	=	trim(strip_tags($metadata->find('dd[itemprop="softwareVersion"]',0)->innertext));
			
			$article->Requirement 	=	'Android: '.trim(strip_tags($metadata->find('dt[itemprop="operatingSystems"]',0)->next_sibling ()->innertext));
			
			$article->FileSize 	=	intval(trim(strip_tags($metadata->find('dd[itemprop="fileSize"]',0)->innertext)));
			$article->FileSize 	=	intval(trim(strip_tags($metadata->find('dd[itemprop="fileSize"]',0)->innertext)));
			$article->FileSize	=	convert_file_size($article->FileSize,'MB','B');
			
			$article->USDPrice 	=	(trim(strip_tags($metadata->find('dd[itemprop="offers"]',0)->innertext)));			
			
			$article->latestExtractionTime	=	$date->toMySQL();
			$article->DownloadURL	=	$date->toMySQL();
			print_debug(2,'END get doc-metadata');
		}
		
		///////////////////////////////////////////////////////////
		////	GET VERTION HISTORY	///////////////////////////////
		$article->version_history	=	trim($html->find('div[class="doc-whatsnew-container"]',0)->innertext);

		{
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
			$items	=	$html->find('div[class="screenshot-carousel-content-container"]',0)->find('img');
			$arr_scr	=	array();		
			$image_prefix	=	$href->take_file_name($article->ProductName . ' ' .$article->version).'-'.date("Y",strtotime($article->Add_Date)).'-'.date("m",strtotime($article->Add_Date));	
			
			$path_image	=	$arr_param['patch_image'].DS.date("Y",strtotime($article->Add_Date)).DS.date("m",strtotime($article->Add_Date));
			$root_image	=	$arr_param['root_image'].'/'.date("Y",strtotime($article->Add_Date)).'/'.date("m",strtotime($article->Add_Date));	
			
			for ($i=0; $i<count($items); $i++)
			{
				$number	=	count($arr_images)+1;
				$image_name	=	$image_prefix.'-'.$number;
				
				$item = $items[$i];				
				$link_sc	=	$item->src;
				$arr_scr[]	=	$link_sc;
				$arr_images[]	=	$link_sc;
				mosGetOneImages($pid, $siteID, $image_name, $link_sc, $path_image, '#__smedia2011a', $root_image, 1);			
				$arr_Images[]	=	1;				
			}
			print_debug(2,'END get screenshot');
		}		
		
		$query	=	'';
		if (preg_match('/id=([^=&]+)&/ism', $SourceURL, $matches)) {
			$query	=	$matches[1];
		}else {
			preg_match('/id=([^=&]+)$/ism', $SourceURL, $matches);
			$query	=	$matches[1];
		}
		
		$article->DownloadURL	=	"https://market.android.com/details?id=$query";
		$article->state	=	1;
		///////////////////////////////////////////////////////////
		////	STORE ARTICLE	///////////////////////////////	
		print_debug(2,'BEGIN store article');
			$obj_article->save($article);
		print_debug(2,'END store article');			
	}
}