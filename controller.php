<?php
/**
 * @version	$Id: controller.php $
 * @package	get_soft
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	Commercial
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

// Set the table directory
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

if (!class_exists('loadHtml')) 
{
	require('libraries/helpers/parse.php');
}
if (!class_exists('QRequest')) {
	require_once('libraries/helpers/request.php')	;
	
}
if (!class_exists('phpWebHacks')) {
	require_once('libraries/helpers/phpWebHacks.php')	;
	
}
if (!class_exists('buildTree')) {
	require_once('libraries/helpers/buildTree.php')	;
	
}
if (!class_exists('href')) {
	require_once('libraries/helpers/href.php')	;
	
}
if (!class_exists('DIRsoftware')) {
	require_once('libraries/class/dirsoftware.php');	
}
if (!class_exists('DirDownload_cdn2011')) {
	require_once('libraries/class/dirdownload_cdn2011.php');	
}
if (!class_exists('AutoCutText')) {
	require_once('libraries/helpers/autocuttext.php');	
}
if (!class_exists('vov_Get_Image')) {
	require_once('libraries/helpers/get_image.php');
}
require_once('libraries/helpers/supportwse.php');
require_once('libraries/helpers/utility.php');
require_once('libraries/class/tidy_clean.php');
require_once('libraries/class/images.php');
require_once('libraries/class/db.php');
require_once('libraries/lib.php');
require_once('libraries/db/support.php');



/**
 * Contact Component Controller
 *
 * @static
 * @package		Joomla
 * @subpackage	amMap
 * @since 1.5
 */
class DIRController extends JController {
	
	var $name		=	'LCS-8144';
	var $id			=	'47732';
	var $cookie		=	'c344d25e3f969e4a9225776f25dde4da';
	var $captchar	=	'54BJ33';
	function __construct()
	{
		
		$cookie	=	JRequest::getVar('cookie','');
		if ($cookie) {
			$this->cookie	=	$cookie;
		}
		$captchar	=	JRequest::getVar('captchar','');
		if ($captchar) {
			$this->captchar	=	$captchar;
		}
		parent::__construct();
	}
	
	function display(){
		global $mainframe;		
	}	
	function viewNo()
	{
		$file_name	=	time();		
		$tmp_path	=	'media/soft_driver/captchar';
		
		$link_soft	=	'http://www.nodevice.com/driver/'.$this->name.'/get'.$this->id.'.html';
		$link_captchar	=	'http://www.nodevice.com/verimg.php?'.$file_name;		
		$browser	=	new phpWebHacks();
		$browser->get($link_soft);
		
		$response = $browser->get($link_captchar);
		if (!$response) {
			JError::raiseNotice('c','[vov_Get_Image] '.$browser->getErrors());
			return false;
		}
		$page_header = $browser->get_head();
		$file_stored = $tmp_path.DS.$file_name.'.png';		
		if(!file_put_contents($file_stored, $response)){
			JError::raiseNotice('c','[vov_Get_Image] Cannot write file: '.$file_stored);
			return false;
		}
		
		$cookies	=	$browser->get_Cookies($link_captchar);
		var_dump($cookies);
		$link_captchar	=	JURI::root().'media/soft_driver/captchar/'.$file_name.'.png';
		$uri		=& JFactory::getURI();
		
			?>
				<form name="form_vercode" method="POST" action="index.php?option=com_get_soft">				
					<p>Please, enter verification code:</p><p>
						<img width="110" height="36" onclick="" id="img_ver" alt="Download" src="<?php echo $link_captchar; ?>">						
					</p>
					<p><input type="text" class="ver_code" name="verstr"></p>
					<input type="hidden" name="task" value="getNo" />
					<button type="submit"><span><span><span>Download</span></span></span></button>					
				</form>
			<?php

die;
	}
	
	function getNo()
	{
			$browser	=	new phpWebHacks();
			$link		=	'http://www.nodevice.com/driver/download.html#body';
			
			$arr_post		=	array();
			$arr_post['id']	=	$this->id;
			$arr_post['name']	=	$this->name;
			$arr_post['verstr']	=	$this->captchar;
			
			$arr_cookie	=	array();
			$arr_cookie[]	=	"PHPSESSID=".$this->cookie;
			$browser->setCookies($arr_cookie, $link);
			$response	=	$browser->post($link,$arr_post);
			echo $response;
			die;
	}
	
	
	
	function sendMail()	
	{
		$MailFrom       = "ducdm87@gmail.com";
		$FromName       = "vietbao";
		$to  		 = $_POST['email'];
		$subject  	 = 'quang cao';
		$content       = $_POST['content'];
		$tel        = $_POST['tel'];
		$company    = $_POST['company'];
		$headers    = $_POST['email'];
		
		  	$body = "Email: ".$to."<br>";
		    $body .= "Tiêu đề : ".$subject."<br>";
		    $body .= "Điện thoại: ".$tel ."<br>";
		    $body .= "Tên công ty: ".$company."<br>";
		    $body .= "Nội dung: ".$content."<br>";		   
		
		$mail=JFactory::getMailer(); 
		$mail->addRecipient($to); 
		$mail->setSender( array($MailFrom, $FromName ) ); 
		$mail->setSubject( $FromName.': '.$subject );   
		$mail->setBody( $body );  
		$mail->IsHTML(1);   
		$sent = $mail->Send(); 
		echo 'successfull';
		die();
		
	}
}
?>