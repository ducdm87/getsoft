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

class DIRModelMacupdate extends JModel{	
	
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
				FROM `#__software_category_macupdate`
				WHERE id = $cat_id";
			$db->setQuery($query);
			$db->loadObject($obj);
			
			$arr_obj[]	=	$obj;
			$arr_obj[]	=	$obj;
		}else {
			$query = "SELECT * ".
				" FROM `#__software_category_macupdate`".
				" WHERE `id` = 1 ".
				" ORDER BY `last_run` LIMIT 0,2 ";
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
		global $arrErr;
		$db		=	JFactory::getDBO();
		$root	=	'http://www.macupdate.com/explore/mac/';
		$href	=	new href();
//		http://www.macupdate.com/explore/mac/
//		http://www.macupdate.com/explore/mac/recent/0
//		http://www.macupdate.com/explore/mac/recent/160

		$array_param	=	array('_referer'=>$root);
		$start	=	($page-1)*40;
		
		$browser	=	new phpWebHacks();
//		$browser->setParam($array_param);
		$link	=	$link.'recent/'.$start;
		echo $link;
		echo '<hr />';

		$response	=	$browser->POST($link);
				
		$html		=	loadHtmlString($response);
		if ($listingarea	=	$html->find('div[id="listingarea"]',0)) {
			
		}else {
			$listingarea	=	$html;
		}		
		
		$arr_link_article	=	array();		
		$arr_link			=	array();
		$arr_id				=	array();
		$arr_title			=	array();
		$arr_alias			=	array();
		$arr_shortdes		=	array();

	//	/app/mac/41222/play-by-play
		$reg_alias = '/mac\/([^\/]+)\/([^\/]+)$/ism';
		
		// find catNews
		if ($items	=	$listingarea->find('div[class="appinfo"]')) {
			for ($i=0; $i<count($items); $i++)
			{
				$item			= 	$items[$i];
				$link			=	$item->find('a[class="titlelink"]',0)->href;
				$link			=	str_replace('../','',$href->process_url($link,$root));
				
				if (preg_match($reg_alias, $link, $matches)) {					
					$arr_link[]		=	$link;
					$arr_id[]		=	$matches[1];
					$arr_alias[]	=	$matches[2];
					$arr_title[]	=	strip_tags($item->find('a[class="titlelink"]',0)->innertext);
					$arr_shortdes[]	=	$item->find('span[class="shortdes"]',0)->innertext;
				}
			}
		}
//		dump_data($arr_link,$arr_id,$arr_alias,$arr_title,$arr_shortdes);

		$date = JFactory::getDate();
		$max_id	=	0;
		for ($i=0; $i<count($arr_link); $i++)
		{
			$article = new DIRsoftware();
			if ($max_id == 0) {
				$max_id	=	$article->getResult('max(id) as max','SiteID = '.$db->quote($siteID));
				if (!$max_id) {
					$max_id	=	100000000;
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
			$content->ProductName_alias		=	$arr_alias[$i];
//			$content->Icon		=	$arr_icon[$i];			
			$content->ProductName_alias = 	$arr_alias[$i];
			$content->SourceURL 		=	$arr_link[$i];
			$content->firstExtractionTime		= 	$date->toMySQL();
			$content->latestExtractionTime		= 	$date->toMySQL();
			$content->SiteID 			= 	$siteID;
			$content->SiteName 			= 	$SiteName;			
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
		
//		if ($paging = $listingarea->find('span[id="nextpage"]',0)) {
//			$link_href	=	trim($paging->find('a[class="listnavarrow"]',0)->href);
//			if (preg_match('/\/(\d+)$/ism',$link_href, $matches)) {				
//				$page = intval(intval($matches[1])/40);
//			}else {
//				$page = 0;
//			}
//		}else {
//			$page = 0;
//		}

		if (!count($items)) {
			$page = 0;
		}
		
		return true;
	}

	function getContent($SiteID, & $numbercontent, & $total = 0)
	{
		$software 		=	new DIRsoftware();

		$db			=	JFactory::getDBO();
		$w			=	'state <1 and SiteID = ' . $db->quote($SiteID);
//		$w			=	'id = 100000070 ';
		
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
			$begin		=	md5('BEGIN_GET_CONTENT_MU10');
			$end		=	md5('END_GET_CONTENT_MU10');
		
			$url		=	JRoute::_(JURI::root()."index.php?option=$option");	
			$arr_post	=	array();
			$arr_post['task']		=	'macupdate.getDetail';
			$arr_post['content_id']	=	$list_content[$i]->id;
			$arr_post['pid']		=	$list_content[$i]->pid;
			$arr_post['content_title']		=	$list_content[$i]->ProductName.' '.$list_content[$i]->version;
			$arr_post['SourceURL']	=	$list_content[$i]->SourceURL;
			$arr_post['begin_get_content']	=	$begin;
			$arr_post['end_get_content']	=	$end;		

			echo $url;		
			$a	=	array();
			foreach ($arr_post as $k=>$v) {
				$a[]	=	"$k=$v";
			}
			echo '<br /> <hr />';
			echo implode('&',$a);
			die();

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
		die();
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
	function getDetail($content_id, $SourceURL, $pid, $content_title, $siteID	=	'mu10', $SiteName = 'macupdate.com', $arr_param)
	{
//		$SourceURL	=	'http://www.driversdown.com/drivers/nVIDIA-nForce-680i-780i-SLI-nForce-System-Tools-6.00-Windows-XP%28x32-x64%29-Vista%28x32-x64%29_84567.shtml';
		echo '<hr />';
		echo $SourceURL;
		echo '<hr />';
		
		$db		=	JFactory::getDBO();
		$obj_article 	=	new DIRsoftware();
		
		$obj_content	=	$obj_article->get($content_id);
		var_dump($obj_content);
		die;
		$date = JFactory::getDate();
		
		$root	=	'http://www.macupdate.com';
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
		
		$detailtable	=	$html->find('table[id="detailtable"]',0)->find('tr');
		for ($i=0; $i< count($detailtable); $i++)
		{
			$a1		=	$detailtable[$i]->find('span[class="infoheading"]',0);
			$a2		=	$detailtable[$i]->find('span[class="infoheadingtab"]',0);
			$tit	=	$a1?$a1:($a2?$a2:false);
			if (!$tit or ! $detailtable[$i]->find('span[class="inforight"]',0)) {
				continue;
			}
			$tit	=	$tit->innertext;			
			$info	=	$detailtable[$i]->find('span[class="inforight"]',0);
			if (preg_match('/Version Downloads:/ism',$tit)) {
				$article->TotalDownload	=	$info->innertext;
				continue;
			}
			if (preg_match('/Date:/ism',$tit)) {						
				$time		=	strtotime(trim($info->innertext));
				$time 		=	date('Y-m-d H:i:s',$time); 
				$article->Add_Date	=	$time;
				continue;
			}
			if (preg_match('/Type:/ism',$tit)) {
				$info		=	$info->find('a',1)->innertext;				
				$query	=	'SELECT * FROM `#__software_category_macupdate` WHERE title LIKE '.$db->quote("%$info%");
				$db->setQuery($query);
				$obj	=	$db->loadObject();
				$article->CategoryName 	= 	$info;
				if ($obj->catid == 0) {
					$article->CategoryID 	= 	$obj->id;
				}else {
					$article->SectionID		= 	$obj->secid;
					$article->CategoryID	= 	$obj->catid;
				}
				continue;
			}
		}

		$infos		=	$html->find('div[id="appinfodescr"]',0);
		$infos		=	removeTag($infos, 'script');
		
		if ($appcat	=	$html->find('span[id="appcat"]',0)) {
//			cathead			
			$article->License		=	$appcat->innertext;	
		}		
		if ($appprice	=	$html->find('span[id="appprice"]',0)) {
//			cathead			
			preg_match('/\(\$([^\)]+)\)/ism',$appprice->innertext,$mathces);			
			$article->USDPrice		=	$mathces[1];	
		}		
		if ($req	=	$infos->find('div[id="reqs"]',0)) {
//			cathead
			$req->find('span[class="cathead"]',0)->outertext	=	'';
			$article->Requirement		=	$req->innertext;	
		}		
		$article->ProductName	=	$obj_content->ProductName;
		$article->version		=	$obj_content->version;
		$article->ShortDesc		=	$obj_content->ShortDesc;
		if ($link_download = $html->find('a[id="downloadlink"]',0)) {
			
			$link_download	=	$link_download->href;			
			$article->DestURL		=	$href->process_url($link_download, $root);
			$article->DownloadURL	=	$href->process_url($link_download, $root);
		
		}
	
		if ($version_history	=	$infos->find('div[id="whatsnew"]',0)) {
			$version_history->find('span[class="cathead"]',0)->outertext	=	'';
			$article->version_history		=	$version_history->innertext;	
		}
		if ($desc	=	$infos->find('div[id="desc"]',0)->innertext) {
			$article->LongDesc	=	trim(($desc));
			$article->LongDesc	=	preg_replace('/<span[^>]*>\s*<\/span>/ism','',$article->LongDesc);
			$article->LongDesc	=	trim(preg_replace('/(<br[^>]*>\s*){2,}/ism','<br />',$article->LongDesc));
		}
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
		
		$link_icon	=	$href->process_url($html->find('img[id="appiconinfo"]',0)->src, $root);
		
		if ($link_icon = mosGetOneImages($pid, $siteID, $image_name, $link_icon, $patch_icon, '#__smedia2011a', $root_icon, 3)) {
			$article->Icon	=	$link_icon;
		}		
		print_debug(2,'END get icon');

		if ($rate	=	$html->find('div[id="smallstarstop"]',0)) {
			$rate->find('div[id="userratingtop"]',0)->outertext = '';
			$imgs	=	$rate->find('a img');
			$number	=	0;
			for ($i=0; $i<count($imgs); $i++)
			{
				if (preg_match('/smalllight/ism',$imgs[$i]->src)) {
					break;
				}
				$number ++ ;
			}
			
			if ($count = $html->find('span[id="smalcount"]',0)) {
				$count	=	$count->find('span[itemprop="count"]',0)->innertext;
				$article->Votes	=	$count;
			}
//			percent
			$article->UserRating	=	$number * 10;			
		}

		$checkSum	=	'';
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
		
		$article->state	=	1;
		$article->latestExtractionTime		= 	$date->toMySQL();
		$article->PageHTML 	 =  $response;
		///////////////////////////////////////////////////////////
		////	STORE ARTICLE	///////////////////////////////
		print_debug(2,'BEGIN store article');
			$obj_article->save($article);
		print_debug(2,'END store article');
	}
	
	function getFile($siteID = 'mu10', $patch_file = 'media/soft_driver/mu10', $totalError = 0)
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
    
		$SiteName = 'macupdate.com';  
//		$path_save ='D:\\wamp\\www\\software_driver\\media\soft_driver\\fhp100';
		
		//$SiteName = 'Esellerate';  
		//$path_save ='C:\\ws\\downloadfile\\esl\\';
		
		$database	=	JFactory::getDBO();
		$SiteID 	=	'dd95';
		
		$dbname='software_driver';
		$table_source='#__software2011a';
		
		$dbname_save='software_driver';
		$table_save='#__download_cdn2011';
		
		$query = "SELECT max(PA_ID) FROM  $dbname_save.`$table_save` WHERE `SiteID`=". $database->quote($SiteID);
		$database->setQuery($query);
		
		$maxid = $database->loadResult();
		if (!isset ($maxid)) $maxid = 0;

		$type_get	=	JRequest::getVar('type_get',1);
		$limit		=	2;
		
		if ($type_get == 1) {
/*		get new soft	*/
			$query	=	"SELECT id, SourceURL, Add_Date, pid, DestURL, DownloadURL ".
							"FROM $dbname.`$table_source` ".
							"WHERE `SiteID`=". $database->quote($SiteID).
								" AND id> $maxid ".
								" AND state = 1 ".
							" ORDER BY id ".
							" LIMIT 0,$limit"; // AND id> $maxid 	
		}elseif ($type_get == 2)
		{
/*		try get soft is wrong	*/
			$query	=	"SELECT id, SourceURL, Add_Date, pid, DestURL, DownloadURL ".
								"FROM $dbname.`$table_source` ".
								"WHERE `SiteID`=". $database->quote($SiteID).
										"  AND id IN (SELECT PA_ID ".
														" FROM  $dbname.`$table_save` ".
														" WHERE `SiteID`=". $database->quote($SiteID).
															"  AND DownloadState<>200)".
								" ORDER BY id ".
								" LIMIT 0,$limit"; // AND id> $maxid 	
		}elseif ($type_get == 3)
		{
/*		check soft	*/
			$query	=	"SELECT t1.id,t1.pid,t1.DestURL,t1.DownloadURL ".
							"FROM $dbname.`$table_source` t1 ".
							"WHERE `SiteID`=". $database->quote($SiteID).
								"  AND t1.id NOT IN (SELECT PA_ID ".
														"FROM $dbname_save.`$table_save` ".
														" WHERE `SiteID`=". $database->quote($SiteID).") ".
								" AND state = 1 ".
							"ORDER BY t1.id limit 0,$limit"; // AND t1.id> $maxid id not in (SELECT PA_ID FROM $dbname_save.`$table_save` ) AND	
		}
		
		$query1	=	"SELECT count(*) FROM  $dbname.`$table_save` WHERE `SiteID`=". $database->quote($SiteID)."  AND DownloadState<>200";
		$database->setQuery($query1);
		$totalError	=	$database->loadResult();
		
		$database->setQuery( $query );
		echo $database->getQuery();
		echo '<hr />';
		$rows		=	$database->loadObjectList(); 
		
		$countrow 	= count($rows);
		 print_debug(1,'Step 0: '.$countrow);
//		echo $countrow;
//		die;
		//                    $database->query() or die($database->stderr());  
		IF ($countrow>0) {
		    
		    foreach ($rows as $row) 
		    {
		    	echo '<br />'.date('Y-m-d H:i:s').' ['.$row->id.']'.$row->SourceURL;

		    	$SourceURL 		=	urldecode($row->SourceURL);		    	
		        $downloadpage 	=	urldecode($row->DownloadURL );
		        if ($downloadpage) {
		        	$SourceURL	=	$downloadpage;
		        }
		       print_debug(1,'Step 0: '.$SourceURL);
		        if (strpos($SourceURL,'/drivers/')!==false){
		        	print_debug(1,'Step 0a: get link to page download.');
		        	$browser	=	new phpWebHacks();
		        	$response	=	$browser->get($SourceURL);
		        	$html		=	loadHtmlString($response);
		        	
		        	$cnnT1cCol1	=	$html->find('div[id="cnnT1cCol1"]',0);
		        	$downadress	=	$cnnT1cCol1->find('div[class="downadress"]',0)->children(0)->onclick;
					if (preg_match('/\(\'(.*?)\'\)/ism', $downadress, $mathces)) {
						$downloadpage	=	str_replace('../','',$href->process_url($mathces[1], 'http://www.driversdown.com/'));
						$SourceURL		=	$downloadpage;
					}
		        }			    		   
			    print_debug(1,'Step 1: '.$SourceURL);			    

		        $PA_ID	=	$row->id;
		        $PA_ID2	=	$row->id  + 1234 ;
		         
		        if (strpos($SourceURL,'?ID=')!==false)
		        {
		            $SourceURL_c=str_replace(' ','%20',$SourceURL);		           
		            
		            $ch = curl_init($SourceURL_c);
		            curl_setopt_array( $ch, $curl_options_header );
		            $content = curl_exec ($ch);

		            $header_items=curl_getinfo($ch);
		            curl_close ($ch);

		            $header_items['content_type']=trim($header_items['content_type']);
		            print_debug(1,'Step 2: ');
		            if (DEBUG)
		    			echo '<br/>'. print_r($header_items);

		    		$arr_link	=	array();
		    		if (strpos($header_items['content_type'],'text/html')===false AND $header_items['content_type']!=='')
		            {// khong fai la trang text/html
		            	print_debug(1,'Step 2a0 ');
		                $finaldownloadURL=$header_items['url'];
		            } else if (strpos($header_items['content_type'],'text/html')!==false | $header_items['content_type']=='')
		            {
		            	print_debug(1,'Step 2a1 '.$SourceURL);

                        $SourceURL_c=str_replace(' ','%20',$SourceURL_c);
                        $ch = curl_init($SourceURL_c);

                        curl_setopt_array( $ch, $curl_options_html );
                        $content = curl_exec ($ch);
                       
                        $header_items=curl_getinfo($ch);
                        curl_close ($ch); 
                        $html	=	loadHtmlString($content);
                        $dlbox	=	$html->find('div[class="zhongjian"]',0);
                        $arr_link	=	getDownloadPage($dlbox->innertext);
//                        $SourceURL	=	$href->process_url($SourceURL,$SourceURL_c);

		             }
		            $success	=	false;
		            print_debug(1,'Step 3 . start download. Total link: '.count($arr_link));
					for ($i=0; $i<count($arr_link); $i++)
					{
//		http://www.filehippo.com/download/file/cad237331a401a5198db9fb2873724c412a65d13be7214153fea6046bf0509fd/
						$finaldownloadURL	=	$arr_link[$i];
						if (strpos($finaldownloadURL,'driversdown.com/')!==false)
			            {
			            	print_debug(2,'Step 3a ['.$i.']'.$finaldownloadURL);
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

							//******//
							$filename_save	=	$filename	=	'tmp_file';
							//*//

			             	print_debug(2,'Step 3b: BEGIN get file: '.$path_save);
			             		$downloadInfo	=	downloadFile( $finaldownloadURL,$path_save.DS,$filename_save);
			             	if (DEBUG) 
			             		print_r($downloadInfo);
			             		
			             	print_debug(2,'Step 3b: END get file: '.$path_save);

			               if ($downloadInfo['http_code']<>200)
			               		$downloadInfo	=	downloadFile( $finaldownloadURL,$path_save,$filename_save);

			               if ($downloadInfo['http_code']<>200)
			               	continue;

			               if ($downloadInfo['http_code'] == 200)
			               {
			               		print_debug(2,'Step 3c: success width link: '.($i++));
			               		$i	=	count($arr_link);
			               		$success	=	true;

			               		print_debug(2,'Step 3d '.$downloadInfo['url']);
			               		$filename	=	getDownloadFilename($downloadInfo['url']);
			               		//******//
			               		$_filename	=	$PA_ID2.'-'.$filename;

			               		print_debug(2,'Step 3f: rename file: '.$filename_save.' => '.$_filename);
								if (!JFile::move($path_save.DS.$filename_save,$path_save.DS.$_filename)) {
									echo 'ERRORR. not rename file from '.$filename_save.' => '.$_filename;
									die;
								}

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
								print_debug(2,'Step 3g, update database; '.$filename);

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
			            	   }
			              }
					}

					if ($success == false) {
						 $SourceURL2			=	mysql_escape_string($SourceURL);
	                    $download_finalurl2	=	mysql_escape_string($finaldownloadURL);
	                    $download_content_type2	=	mysql_escape_string($header_items['content_type']);
	                 	$query="REPLACE INTO $dbname_save.`$table_save` ( id,wsid,`PA_ID`, `SiteID`, `SiteName`, `SourceURL`, `FinalDownloadURL`, `ContentType`, `TotalTime`, `SizeDownload`, `SpeedDownload`, `DownloadContentLength`, `OriginalFileName`, `FileName`, `Path`, `DownloadState`, `Approved`, `DownloadDate`) VALUES ( $PA_ID2,$PA_ID,$PA_ID, '$SiteID', '$SiteName', '$SourceURL2','$download_finalurl2', '$download_content_type2', '', '', '', '', '','', '', '1', '0',now())" ;
	                  	$database->setQuery($query);
	                    $database->query() or die($database->stderr());
					}
		    	}
		    	echo '<hr />';
			}
		}
		return $countrow;
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