<?php
function mosGetImages($obj_content,$root, & $arr_Images , $path_image , $link_image, $param_getimage = null)
{	
	if ($param_getimage == null) {
		$param_getimage = array();
	}
	$date	=	$obj_content->Add_Date;

	if (!is_dir($path_image)) {
		mkdir($path_image);
	}
	
	$path_image	.=	DS.date("Y",strtotime($date));
	if (!is_dir($path_image)) {
		mkdir($path_image);		
	}	
	$path_image	.=	DS.date("m",strtotime($date));
	if (!is_dir($path_image)) {
		mkdir($path_image);
	}
	$link_image	.=	'/'.date("Y",strtotime($date)).'/'.date("m",strtotime($date)).'/';
	$href	=	new href();
	$image_prefix	=	$href->take_file_name($obj_content->ProductName . ' ' .$obj_content->version).'-'.date("Y",strtotime($date)).'-'.date("m",strtotime($date));	
	
	$obj_content->ShortDesc		=	mosProcessImages($obj_content->ShortDesc,$root, $arr_Images, $path_image,$image_prefix,$link_image, $param_getimage);		
	$obj_content->LongDesc		=	mosProcessImages($obj_content->LongDesc,$root, $arr_Images, $path_image,$image_prefix,$link_image, $param_getimage);
	return true;
}

function mosProcessImages($content,$root,& $arr_Images, $path_image, $image_prefix,$link_image, $param_getimage = array())
{	
	//find all href value in a tag (with ")
	$href	=	new href();
	if(preg_match_all('/<img.*?(src=([^"\'].*?))(\s|\/>|>)/im', $content, $matches)){
		for ($i = 0; $i < count($matches[1]); $i++){
			$matches[2][$i]	=	$href->process_url($matches[2][$i],$root);
			$content = str_replace($matches[1][$i], 'src="'. $matches[2][$i] .'"', $content);
		}
	}
	//find all href value in a tag (with ')
	if(preg_match_all('/<img.*?(src=\'(.*?)\')/im', $content, $matches)){
		for ($i = 0; $i < count($matches[1]); $i++){
			$matches[2][$i]	=	$href->process_url($matches[2][$i],$root);
			$content= str_replace($matches[1][$i], 'src="'. $matches[2][$i] .'"', $content);
		}
	}
	
	if(preg_match_all('/<img.*?(src="(.*?)")/ism', $content, $matches)){
		
		for ($i = 0; $i < count($matches[1]); $i++){
			
			$matches[2][$i]	=	$href->process_url($matches[2][$i],$root);
			$number	=	count($arr_Images)+1;
			$image_name	=	$image_prefix.'-'.$number;
			$obj_get_image	=	new vov_Get_Image($matches[2][$i],$path_image);
			if (!$response = $obj_get_image->get_image($image_name, $param_getimage)) {
//				$content = str_replace($matches[0][$i], '', $content);
				continue;
			}
			$_link_image	=	$link_image.$response->file_name;
			$obj_image	=	new stdClass();
			$obj_image->media_url	=	$_link_image;
			$obj_image->SourceURL	=	$matches[2][$i];
			$obj_image->Size		=	filesize($path_image.DS.$image_name.'.'.$response->file_type);
			$obj_image->FileName	=	$response->file_name;
			$obj_image->path		=	$path_image;
			$obj_image->FileType	=	$response->file_type;
			$obj_image->MediaType	=	'image';
			$arr_Images[]			=	$obj_image;					
			
			$content = str_replace($matches[1][$i], 'src="'. $_link_image .'"', $content);
			// insert image
			
		}
	}
	return $content;
}

function mosGetOneImages($pid, $SiteID, $title, $SourceURL, $path_image, $tblName, & $link_image, $type)
{	
	$db	=	JFactory::getDBO();
	$href	=	new href();
	$date = JFactory::getDate();
	$w_s = parse_url($SourceURL);
	$w_r = parse_url(JURI::root());
	
	if (isset($w_s['host']) and $w_s['host'] != $w_r['host']) {
		$query	=	"SELECT id,pid,FileName,path,FileType FROM $tblName WHERE SourceURL = ". $db->quote($SourceURL);
	}else {
		return false;
	}	
	
	$db->setQuery($query);
	$needGet	=	true;
	// check file
	$image_name		=	'';
	if ($obj_img = $db->loadObject()) {
		$path_image	=	$obj_img->path;
		$path_1		=	$obj_img->path.DS.$obj_img->FileName;
		$file_name	=	$obj_img->FileName;
		$file_type	=	$obj_img->FileType;
		if (JFile::exists($path_1)) {			
			$link_image	=	$link_image.'/'.$obj_img->FileName;
			$needGet	=	false;			
		}
		$image_name	=	$obj_img->FileName;		
//		$pid	=	$obj_img->pid;
	}else {		
		$image_name	=	$href->take_file_name($title);
	}
	
	if ($needGet == true) {
		$obj_get_image	=	new vov_Get_Image($SourceURL,$path_image);
		$image_name		=	str_replace('.','',$image_name);
		if (!$response 	= $obj_get_image->get_image($image_name)) {
			return false;
		}
		$link_image	=	$link_image.'/'.$response->file_name;
		$path_1		=	$response->file_stored;
		$file_name	=	$response->file_name;
		$file_type	=	$response->file_type;
	}
	
	$obj_image = new stdClass();
		
	$obj_image->SiteID		= 	$SiteID;
	$obj_image->pid			= 	$pid;
	$obj_image->SourceURL		= 	$SourceURL;
	$obj_image->Title			= 	$title;
	$obj_image->Title			= 	$title;
	$obj_image->path			= 	$path_image;
	$obj_image->Size			= 	filesize($path_1);
	$obj_image->FileName		= 	$file_name;
	$obj_image->FileType		= 	$file_type;
	$obj_image->type			= 	$type;
	$obj_image->state			= 	1;
		
	mosInsertImage($obj_image);
	
	return $link_image;
}

function mosInsertImage($obj_image)
{
	$db	=	JFactory::getDBO();	
	$date = JFactory::getDate();
	
	JTable::addIncludePath(JPATH_COMPONENT_SITE.DS.'tables');
	$row =& JTable::getInstance('smedia2011a','Table');
	
	$row->firstExtractionTime		= 	$date->toMySQL();
	if (isset($obj_image->id) and $obj_image->id) {
		$row->load($id);
	}
	$row->latestExtractionTime		= 	$date->toMySQL();	
	$row->bind($obj_image);	
	$row->store();
}