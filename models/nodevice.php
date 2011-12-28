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

class DIRModelNodevice extends JModel{	
	
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
				FROM `#__software_category_nodevice`
				WHERE id = $cat_id";
			$db->setQuery($query);
			$obj	=	$db->loadObject();
			
			$arr_obj[]	=	$obj;
			$arr_obj[]	=	$obj;
		}else {
			$query = "SELECT *
				FROM `#__software_category_nodevice`
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
	
	function getListContent($link,& $page = 1, $cid, $secid, $catid = 0, $cat_parent, $siteID = 'dd95', $SiteName ='driversdown.com')
	{
//		$link	=	'http://www.nodevice.com/driver/company/Dell/Laptop.html';
		global $arrErr;
		$db		=	JFactory::getDBO();
		$root	=	'http://www.nodevice.com/';
		$href	=	new href();
//		driverslist/s_14_1.shtml
		if ($page>1) {
				$link	=	preg_replace('/\.html/ism',"/page$page.html", $link);	
		}
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
	//	/driver/CREA1-B/get56813.html
		$reg_alias = '/\/driver\/([^\/\.]+)\/get(\d+)\.html/ism';
		
		$drivers_list	=	$html->find('table[class="drivers_list"]');
		for ($i=0; $i<count($drivers_list); $i++)
		{	
			$items	=	$drivers_list[$i]->find('tr');
			for ($j=0; $j<count($items); $j++)
			{
				$item	=	$items[$j];
				if ($item->find('th')) {
					continue;			
				}

				$link			=	$href->process_url($item->children(1)->children(0)->href,$root);				
				if (preg_match($reg_alias, $link, $matches)) {
					$arr_link[]	=	$link;
					$arr_id[]	=	$matches[2];
					$arr_alias[]=	$matches[1];
					$arr_title[]=	strip_tags($item->children(1)->children(0)->innertext);
				}
			}
		}		
		
		$date = JFactory::getDate();
		$max_id	=	0;
		for ($i=0; $i<count($arr_link); $i++)
		{
			$article = new DIRsoftware();
			if ($max_id == 0) {
				$max_id	=	$article->getResult('max(id) as max','SiteID = '.$db->quote($siteID));
				if (!$max_id) {
					$max_id	=	96000000;
				}
			}
			$content = new stdClass();

			if ($id = $article->checkExiting($arr_id[$i])) {
				$content->id		=	$id;
			}else {
				$max_id++;
				$content->id		=	$max_id;
				$content->state 	= 	0;
			}

			$content->pid		=	$arr_id[$i];
			$content->sid		=	1;
			$content->ProductName_alias = 	$arr_alias[$i];
			$content->SourceURL 		=	$arr_link[$i];
			$content->firstExtractionTime		= 	$date->toMySQL();
			$content->latestExtractionTime		= 	$date->toMySQL();
			$content->SiteID 			= 	$siteID;
			$content->SiteName 			= 	$SiteName;			
			$content->ProductName 		= 	$arr_title[$i];
			
			if ($catid == 0) {
				$content->CategoryID 	= 	$cid;
			}else {
				$content->SectionID		= 	$secid;
				$content->CategoryID	= 	$cid;
			}
			
			$article->save($content);	
		}
$paging = $html->find('div[class="pages"] div[class="numbers"]',0);

		if ($paging = $html->find('div[class="pages"] div[class="numbers"]',0)) {			
			$p1	=	$page + 1;
			$paging	=	$paging->innertext;
//			<a href="/driver/company/Dell/Laptop/page3.html">3</a>
			$str_reg	=	'/<a[^>]*\/page'.$p1.'\.html[^>]*>/ism';

			if (!preg_match($str_reg, $paging)) {
				$page = 0;	
			}			
		}else {
			$page = 0;
		}

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
			$begin		=	md5('BEGIN_GET_CONTENT_ND96');
			$end		=	md5('END_GET_CONTENT_ND96');
		
			$url		=	JRoute::_(JURI::root()."index.php?option=$option");	
			$arr_post	=	array();
			$arr_post['task']		=	'nodevice.getDetail';
			$arr_post['content_id']	=	$list_content[$i]->id;
			$arr_post['pid']		=	$list_content[$i]->pid;
			$arr_post['content_title']		=	$list_content[$i]->ProductName.' '.$list_content[$i]->version;
			$arr_post['SourceURL']	=	$list_content[$i]->SourceURL;
			$arr_post['begin_get_content']	=	$begin;
			$arr_post['end_get_content']	=	$end;		
//
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
				$message	=	'ERROR_GET_CONTENT_ND96| #123 API false '.$list_content[$i]->SourceURL.' '.$info;
				JError::raiseWarning('c',$message);				
				continue;
			}
			if (stristr($info,'ERROR_GET_CONTENT_ND96')) {
				$message	=	'ERROR_GET_CONTENT_ND96| '.$info;
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
	function getDetail($content_id, $SourceURL, $pid, $content_title, $SiteID	=	'dd95', $SiteName = 'nodevice.com', $arr_param)
	{
//		$SourceURL	=	'http://www.driversdown.com/drivers/nVIDIA-nForce-680i-780i-SLI-nForce-System-Tools-6.00-Windows-XP%28x32-x64%29-Vista%28x32-x64%29_84567.shtml';
		echo $SourceURL;
		echo '<hr />';
		
		$obj_article 	=	new DIRsoftware();
		
		$obj_content	=	$obj_article->get($content_id);
		$date = JFactory::getDate();
		
		$root	=	'http://www.nodevice.com/';
		$href	=	new href();		
				
		$browser	=	new phpWebHacks();
		$response	=	$browser->get($SourceURL);
		$html		=	loadHtmlString($response);
		
		$main_col	=	$html->find('div[class="main_col"]',0);


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
		$article->Add_Date	=	0;
		if ($main_col->find('div[class="info"]',0)) {
			$infos		=	$main_col->find('div[class="info"]',0)->find('li');
			for ($i=1; $i<count($infos); $i++)
			{
				$info	=	$infos[$i];
				$inner	=	$info->outertext;
	//			<li class=""><b>Size:</b> 5.16 Mb</li>
	
				if (strpos($inner,'</li>') ==false ) {
					$inner	=	$inner.'</li>';
				}
				if (preg_match('/<b[^>]*>Size:\s*<\/b>\s*([\d\.]+)\s*(B|KB|MB|GB|TB|PB)/ism',$inner, $mathces))
				{
					$article->FileSize 		=	convert_file_size($mathces[1],$mathces[2],'B');				
					continue;
				}
	//			Version
				if (preg_match('/<b[^>]*>Version:\s*<\/b>\s*(.*?)\s*<\/li>/ism',$inner, $mathces))
				{
					$article->version	=	$mathces[1];
					continue;
				}
	//			Date added
				if (preg_match('/<b[^>]*>Date added:\s*<\/b>\s*(.*?)\s*<\/li>/ism',$inner, $mathces))
				{
	//				21-Jun-2009, 10:11
					if ($mathces[1]) {
						$time 	 				=	strtotime(trim(strip_tags($mathces[1])));
						$time 					=	date('Y-m-d H:i:s',$time); 
						$article->Add_Date 	 	=	$time;
					}					
					continue;
				}
	//			Operation 
				if (preg_match('/<b[^>]*>Operation system:\s*<\/b>\s*(.*?)\s*<\/li>/ism',$inner, $mathces))
				{
					$article->Requirement 	=	$mathces[1];
					continue;
				}
	//			Manufacturer 
				if (preg_match('/<b[^>]*>Manufacturer:\s*<\/b>\s*(.*?)\s*<\/li>/ism',$inner, $mathces))
				{
					$article->VendorName 	=	$mathces[1];
					continue;
				}
	//			MD5 
				if (preg_match('/<b[^>]*>MD5:\s*<\/b>\s*(.*?)\s*<\/li>/ism',$inner, $mathces))
				{
	//				$inner	=	$this->removeTag($inner,'a');
	//				if (preg_match('/<b[^>]*>Operation system:\s*<\/b>\s*(.*?)\s*<\/li>/ism',$inner, $mathces))
					$article->md5 	=	$mathces[1];
					continue;
				}
			}
		}

		if ($article->Add_Date == 0) {
				$article->Add_Date	=	$date->toMySQL();
			}	

		$article->ProductName	=	$obj_content->ProductName;
		
		if ($desc = $main_col->find('div[class="desc"]',0)) {			
			$desc	=	$desc->innertext;
		}

		$article->ShortDesc  =  '';
		$article->LongDesc   =  '';
		
		$checkSum	=	'';
		///////////////////////////////////////////////////////////
		////	GET IMAGES	///////////////////////////////////////
		$arr_images	=	array();
		print_debug(2,'BEGIN get Images');
			mosGetImages($article, $root, $arr_images, $arr_param['patch_image'], $arr_param['root_image']);
		print_debug(2,'END get Images');

		///////////////////////////////////////////////////////////
		////	STORE IMAGES	//////////////////////////////////
		print_debug(2,'BEGIN store Images');
		for ($i=0; $i<count($arr_images); $i++)
		{
			$image	=	$arr_images[$i];
			$image->siteID 	= $SiteID;
			$image->type	= 2;
			$image->pid		= $pid;
			mosInsertImage($image);
		}
		print_debug(2,'END store Images');
		
		///////////////////////////////////////////////////////////
			////	GET SCREENSHOT	///////////////////////////////	
			print_debug(2,'BEGIN get screenshot');
			if ($item	=	$html->find('a[class="preview"]',0)) {
				$arr_scr	=	array();
				$image_prefix	=	$href->take_file_name($article->ProductName . ' ' .$article->version).'-'.date("Y",strtotime($article->Add_Date)).'-'.date("m",strtotime($article->Add_Date));	
				
				$path_image	=	$arr_param['patch_image'].DS.date("Y",strtotime($article->Add_Date)).DS.date("m",strtotime($article->Add_Date));
				$root_image	=	$arr_param['root_image'].'/'.date("Y",strtotime($article->Add_Date)).'/'.date("m",strtotime($article->Add_Date));
				
					$number	=	count($arr_images)+1;
					$image_name	=	$image_prefix.'-'.$number;
					$link_sc	=	$href->process_url($item->href, $root);					
					mosGetOneImages($pid, $SiteID, $image_name, $link_sc, $path_image, '#__smedia2011a', $root_image, 1);
					$arr_Images[]	=	1;
				
			}			
			print_debug(2,'END get screenshot');
		
		$article->state	=	1;
		
		$article->PageHTML 	 =  $response;
		
			$obj	=	new stdClass();
			$obj->SiteID	=	$SiteID;
			$obj->VendorName	=	$article->VendorName;
			$obj->VendorSupportEmail	=	'';
			$obj->VendorHomepageURL	=	'';
			$obj->VendorAurl	=	'';				
			$obj->latestExtractionTime	=	$date->toMySQL();

			$article->VendorID = processVendor($obj);
			
		///////////////////////////////////////////////////////////
		////	STORE ARTICLE	///////////////////////////////
		print_debug(2,'BEGIN store article');
			$obj_article->save($article);
		print_debug(2,'END store article');
	}	
}


	function removeTag($input, $tagName)
	{
		if (is_string($input)) {
			$output	=	preg_replace('/<'.$tagName.'[^>]*>.*?<\/'.$tagName.'>/ism','',$input);
			return $output;
		}elseif(is_object($input)) {
			$arr_tag	=	$input->find("$tagName");
			for ($i=0; $i< count($arr_tag); $i++)
			{
				$arr_tag[$i]->outertext	=	'';
			}
			return $input;
		}
	}