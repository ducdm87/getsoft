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

class DIRModelCnet extends JModel{	
	
	var $_data 		=	null;
	var $ext		=	null;
	var $name		=	null;
	var $_config	=	null;
	
	function __construct($id=null)
	{
		
		parent::__construct();
	}
	function getFile($db, $user_cnet, $pass_cnet, $link_login, $table_name, $limit)
	{
		if (!$db) {
			$db	=	JFactory::getDBO();
		}
//		$query	=	"SELECT * FROM $table_name WHERE  id= 9 LIMIT 0,$limit";
		$query	=	"SELECT * FROM $table_name WHERE DownloadState <> 200 and Note = 0  LIMIT 0,$limit";
		$db->setQuery($query);
		
		$arr_object	=	$db->loadObjectList();
		
		$path_file	=	JPATH_COMPONENT_SITE.DS.'models'.DS.'data'.DS.'cnet_cookies.txt';

		   $curl_options_header = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => true,    // don't return headers
        CURLOPT_NOBODY         => true,
        CURLOPT_CUSTOMREQUEST  => 'HEAD',
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 10,      // timeout on connect
        CURLOPT_TIMEOUT        => 10,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        CURLOPT_COOKIEJAR      => $path_file,      // path to a file on your server
        CURLOPT_COOKIEFILE      => $path_file,      // path to a file on your server
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
        CURLOPT_CONNECTTIMEOUT => 10,      // timeout on connect
        CURLOPT_TIMEOUT        => 10,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        CURLOPT_COOKIEJAR      => $path_file,      // path to a file on your server
        CURLOPT_COOKIEFILE      => $path_file,      // path to a file on your server
    );  
//		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  $data = "foo=bar";
		$browser	=	new phpWebHacks();
		$user_browser	=	$this->getBrowser($user_cnet, $pass_cnet, $link_login);
		$reg_link		=	'/\/3001([^\/\?]+)\?/ism';
		for ($i=0; $i<count($arr_object); $i++)
		{
			echo '<hr />';			
			$object	=	$arr_object[$i];
			$note	=	intval($object->Note) - 1;
			print_debug(1,'Step 1: Update row['.$i.']: '.$object->id);
			$query	=	"UPDATE $table_name SET Note = $note WHERE id = $object->id";
			$db->setQuery($query);
			$db->query();
			
			$SourceURL	=	$object->SourceURL;
			$FinalDownloadURL	=	$object->FinalDownloadURL;
			$Path		=	$object->Path;
			if (!JFolder::exists($Path)) {
				JFolder::create($Path);
			}
			$FileName	=	$object->FileName;
			
//			  $ch 				=	curl_init($FinalDownloadURL);		
//			  curl_setopt_array( $ch, $curl_options_html );
//			  $content 			=	curl_exec ($ch);
//			  $header_items		=	curl_getinfo($ch);
//			  $size_download	=	$header_items['size_download'];
			  print_debug(1,'Step 2: check file size download.  '.$object->id);
			
		  		if (preg_match($reg_link,$SourceURL,$mathces)) 
		  		{
		  			$SourceURL	=	"http://download.cnet.com/alias-for-soft/3000$mathces[1]?tag=rbxcrdl1";
				  	
		  			print_debug('3','Begin: Get link by user. '. $SourceURL);
			  		
		  			$response	=	$user_browser->get($SourceURL);
		  			
			  		$html		=	loadHtmlString($response);
			  		
					print_r($user_browser->get_head());
					
			  		if ($html->find('a[id="loggedInUserDlLink"]',0)) 
				  	{
				  		print_debug('4','Step 2a: must to login');
				  		$SourceURL	=	$html->find('a[id="loggedInUserDlLink"]',0)->href;				  		
	               	}else if ($downloadNow = $html->find('div[class="downloadNow"]',0)) 
	               	{
	               		print_debug('4','Step 2b: anyone');
	               		$SourceURL	=	$downloadNow->find('a',0)->href;	
	               	}
	               	
	              print_debug('3','Begin get info url, get FinalDownloadURL');
					$ch 				=	curl_init($SourceURL);
						curl_setopt_array( $ch, $curl_options_html );
						$content 			=	curl_exec ($ch);
						$header_items		=	curl_getinfo($ch);					
				  		$FinalDownloadURL	=	getMetaRedirect($content);
				  	curl_close($ch);
				  	echo '<br />';
				  	print_r($header_items);
				  print_debug('3','End get info url');
				  	
					$SourceURL			=	$header_items['url'];
					print_debug('3','SourceURL change to '. $SourceURL);
					
					print_debug('3','FinalDownloadURL: '. $FinalDownloadURL);
	//					  $size_download	=	 $header_items['size_download'];
	//					  if ($size_download == 0 or ($size_download < 450 and $size_download > 440)) {
	//					  	continue;
	//					  }	

					print_debug(3,'Step 3: BEGIN get file'.$Path);
						  $downloadInfo	=	downloadFile( $FinalDownloadURL,$Path.DS,$FileName);
						  
						print_r($downloadInfo);
						if ($downloadInfo['http_code']<>200)
		               		$downloadInfo	=	downloadFile( $FinalDownloadURL,$Path.DS,$FileName);
		               		
					print_debug(3,'Step 3: END get file '. $FileName);
					print_r($downloadInfo);
					
						if ($downloadInfo['http_code'] == 200)
		               	{
		               		$download_content_type	=	$downloadInfo['content_type'];
							$download_finalurl		=	$downloadInfo['url'];
							$download_state			=	$downloadInfo['http_code'];
							$download_total_time	=	$downloadInfo['total_time'];
							$download_size			=	$downloadInfo['size_download'];
							$download_speed			=	$downloadInfo['speed_download'];
							$download_content_length=	$downloadInfo['download_content_length'];
							
							$SourceURL2			=	mysql_escape_string($SourceURL);
							$download_content_type2 =	mysql_escape_string($download_content_type);
							$download_finalurl2	=	mysql_escape_string($download_finalurl);

					print_debug(3,'Step 4: Update database');
					
			                $query="UPDATE `$table_name`".
			                		" SET `SourceURL` = ".$db->quote($SourceURL2).
				                		", `FinalDownloadURL` = ".$db->quote($download_finalurl2).
				                		", `ContentType` = ".$db->quote($download_content_type2).
				                		", `TotalTime` = ".$db->quote($download_total_time).
				                		", `SizeDownload` = ".$db->quote($download_size).
				                		", `SpeedDownload` = ".$db->quote($download_speed).
				                		", `DownloadContentLength` = ".$db->quote($download_content_length).
				                		", `DownloadState` =".$db->quote($download_state).
				                		", `Note` = 1".
				                		", `DownloadDate` = now() ".
				                	" WHERE id = $object->id" ;	
			                	               	
		                    $db->setQuery($query);
		                    $db->query() or die($db->stderr());
		               	}
		  		}else {
		  			
		  		}
			  		
//	  				if ($is_success == false)
//	  				{
//           				 $query="UPDATE `$table_name`".
//                					" SET `Note` = ".$db->quote('Not get').
//	                						", `DownloadDate` = now() ".
//	                				"WHERE id = $object->id" ;	
//                			
//	                    $db->setQuery($query);
//	                    $db->query() or die($db->stderr());
//               		}
//			http://download.cnet.com/SpotFTP/3000-2160_4-10610382.html?tag=rbxcrdl1
//			http://download.cnet.com/3001-2160_4-10610382.html?spi=5af7ed3d1a3bd4060a96cb398835881b
		}
	}
	
	
	function getBrowser($user_cnet, $pass_cnet, $link_login)
	{
		$browser	=	new phpWebHacks();
		$arr_post	=	array();
		$arr_post['appId']	=	'135';
		$arr_post['email']	=	$user_cnet;
		$arr_post['password']	=	$pass_cnet;
		$arr_post['rememberMe']	=	'true';
		$arr_post['resource']	=	'rps-authenticate';
		$arr_post['viewType']	=	'json';
		$response	=	$browser->post($link_login, $arr_post);
		$response	=	preg_replace('/"@([^"]+)"/','"$1"',$response);
		$response	=	str_replace('$','value',$response);		
		$abc	=	json_decode($response);
		
		$purs_1	=	$abc->RpsResponse->User->Messages[0]->Message[0]->value;
		$surs_1	=	$abc->RpsResponse->User->Messages[0]->Message[1]->value;

		$arr_cookie	=	array();
		$arr_cookie[]	=	"curs_gigya_appid=287";
		$arr_cookie[]	=	"purs_1=$purs_1";
		$arr_cookie[]	=	"surs_1=$surs_1";
		$browser->setCookies($arr_cookie, $link_login);
		
//		$response	=	$browser->get('http://download.cnet.com/SpotFTP/3000-2160_4-10610382.html?tag=rbxcrdl1');
//		$html	=	loadHtmlString($response);
//		$href	=	$html->find('a[id="loggedInUserDlLink"]',0)->href;
		return $browser;
	}	
}