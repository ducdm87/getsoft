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

class DIRModelCategory extends JModel{	
	
	var $_data 		=	array();
	var $ext		=	null;
	var $name		=	null;
	var $_config	=	null;
	
	function __construct($id=null)
	{
		
		parent::__construct();
	}
	
	public function getFilehippo(){
		$link = 'http://www.filehippo.com/';
		$db			=&	JFactory::getDBO();
		$browser 	= new phpWebHacks();
		$response 	= $browser->get($link);
		$html 		=	loadHtmlString($response);
		$arr_cat 	=	array();
		$href		=	new href();
		
		if ($boxes		=	$html->find('div[class="box-three"]')) {
			$arr_box = array();			
			for ($i=0; $i<count($boxes); $i++)
			{
				$box = $boxes[$i]->find('div[class="box"]');
				$arr_box = array_merge($arr_box,$box);				
			}
			$parent = 0;
			for ($i=0; $i<count($arr_box); $i++)
			{
				$box = $arr_box[$i];
				
				$obj_cat_p	=	new stdClass();
				$obj_cat_p->link	=	$href->process_url($box->find('h2',0)->children(0)->href,$link);
				$obj_cat_p->title	=	strip_tags($box->find('h2',0)->children(0)->innertext);
				$obj_cat_p->parent	=	-1;
				$arr_cat[]		=	$obj_cat_p;
				$parent			=	count($arr_cat) - 1;
				
				$subCat 	= $browser->get($obj_cat_p->link);
				$html_sub	=	loadHtmlString($subCat);
				if ($subcatbar = $html_sub->find('div[class="subcatbar"]',0)) {						
					$items = $subcatbar->find('div[class="catlink"]');
					for ($j=0; $j<count($items); $j++)
					{
						$item = $items[$j];
						$obj_cat_s	=	new stdClass();
						$obj_cat_s->link	=	$href->process_url($item->children(0)->href,$link);
						$obj_cat_s->title	=	strip_tags($item->children(0)->innertext);
						$obj_cat_s->parent	=	$parent;
						$arr_cat[]		=	$obj_cat_s;
					}
				}				
			}					
		}
		$reg_alias = '/\/([^\/]+)\/$/ism';
		$arr_parent	=	array();
		for ($i=0; $i<count($arr_cat); $i++)
		{
			$menu 	= $arr_cat[$i];
			$parent	=	0;
			if ($menu->parent != -1) 
				$parent	=	intval($arr_parent[$menu->parent]);			
			
			preg_match($reg_alias,$menu->link,$mathces);
			
			$query	=	'INSERT INTO `#__software_category_hippo`'. 
					' SET title = ' . $db->quote($menu->title).
						', parent = '. $db->quote($parent).						
						', link = ' .$db->quote($menu->link).
						', alias_origional = ' .$db->quote($mathces[1]).
						', publish = 1' ;
			$db->setQuery ( $query );
			$db->query();
			if ($menu->parent == -1) 
				$arr_parent[$i]	=	mysql_insert_id();
		}
	}
	
	
	public function getAndroid(){
		$link = 'https://market.android.com/';
		$db			=&	JFactory::getDBO();
		$browser 	= new phpWebHacks();
		$response 	= $browser->get($link);
		$html 		=	loadHtmlString($response);
		$arr_cat 	=	array();
		$href		=	new href();
		
		if ($boxes		=	$html->find('div[class="padded-content3 app-home-nav"]')) {
			
			$parent = 0;
			for ($i=0; $i<count($boxes); $i++)
			{
				$box = $boxes[$i];
				
				$obj_cat_p	=	new stdClass();
				$obj_cat_p->link	=	$href->process_url($box->find('h2',0)->children(0)->href,$link);
				$obj_cat_p->title	=	strip_tags($box->find('h2',0)->children(0)->innertext);
				$obj_cat_p->parent	=	-1;
				$arr_cat[]		=	$obj_cat_p;
				$parent			=	count($arr_cat) - 1;
				
				$items	=	$box->find('li[class="category-item"]');
			
				for ($j=0; $j<count($items); $j++)
				{
					$item = $items[$j];
					$obj_cat_s	=	new stdClass();
					$obj_cat_s->link	=	$href->process_url($item->children(0)->href,$link);
					$obj_cat_s->title	=	strip_tags($item->children(0)->innertext);
					$obj_cat_s->parent	=	$parent;
					$arr_cat[]		=	$obj_cat_s;
				}
			}				
		}		
//		https://market.android.com/apps/GAME
		$reg_alias = '/\/([^\/]+)$/ism';
		$arr_parent	=	array();
		for ($i=0; $i<count($arr_cat); $i++)
		{
			$menu 	= $arr_cat[$i];
			$parent	=	0;
			if ($menu->parent != -1) 
				$parent	=	intval($arr_parent[$menu->parent]);			
			
			preg_match($reg_alias,$menu->link,$mathces);
			
			$query	=	'INSERT INTO `#__software_category_androi`'. 
					' SET title = ' . $db->quote($menu->title).
						', parent = '. $db->quote($parent).						
						', link = ' .$db->quote($menu->link).
						', alias_origional = ' .$db->quote($mathces[1]).
						', publish = 1' ;
			$db->setQuery ( $query );			
			$db->query();
			if ($menu->parent == -1) 
				$arr_parent[$i]	=	mysql_insert_id();
		}
	}
	
	public function getTucows(){
		$link = 'http://www.tucows.com/';
		$db			=&	JFactory::getDBO();
		$browser 	= new phpWebHacks();
		$response 	= $browser->get($link);
		$html 		=	loadHtmlString($response);
		$arr_cat 	=	array();
		$href		=	new href();
		echo  '<br />BEGIN GET DATA: '.date('Y-m-d H:i:s');	
		$arr_link	=	array();
//		$arr_link[]	=	'http://www.tucows.com/windows'; 	// 1291		56'
//		$arr_link[]	=	'http://www.tucows.com/mac'; 		// 352		26'
//		$arr_link[]	=	'http://www.tucows.com/linux'; 		// 249		17'
//		$arr_link[]	=	'http://www.tucows.com/web-20'; 	// 17		1.5'
		
		$arr_title	=	array();
//		$arr_title[]	=	'windows';
//		$arr_title[]	=	'mac';
//		$arr_title[]	=	'linux';
//		$arr_title[]	=	'web';

		$arr_cat	=	array();
		$current	=	0;
		for ($i=0; $i<count($arr_link); $i++)
		{
			$obj_cat	=	new stdClass();
			$obj_cat->link	=	$arr_link[$i];
			$obj_cat->title	=	$arr_title[$i];
			$obj_cat->parent	=	-1;
			$obj_cat->isparent	=	1;
			$this->_data[]		=	$obj_cat;
			$parent				=	count($this->_data) - 1;
			$this->tucows_sub($arr_link[$i], $parent);

		}
		echo  '<br />BEGIN INSERT: '.date('Y-m-d H:i:s');
		$arr_parent	=	array();
		for ($i=0; $i<count($this->_data); $i++)
		{
			$menu 	= $this->_data[$i];
			$parent	=	0;
			if ($menu->parent != -1) 
				$parent	=	intval($arr_parent[$menu->parent]);
			$publish	=	0;
			if ($menu->isparent == 0) 
				$publish	=	1;

			$query	=	'INSERT INTO `#__software_category_tucows`'. 
					' SET title = ' . $db->quote($menu->title).
						', parent = '. $db->quote($parent).
						', link = ' .$db->quote($menu->link).
						', publish = '.$publish ;
			$db->setQuery ( $query );
			$db->query();				
			$arr_parent[$i]	=	mysql_insert_id();
		}
		echo  '<br />FINISH: '.date('Y-m-d H:i:s');
		echo '<hr />Total '.count($this->_data);
		echo '<hr />';
	}
	// level 1
	function tucows_sub($link, $parent)
	{
		$browser	=	new phpWebHacks();
		$response	=	$browser->get($link);
		$html		=	loadHtmlString($response);

		if ($html_cat = $html->find('div[id="categories"]',0)) {
			$items	=	$html_cat->find('div[class="padBottom10"]');

			$href	=	new href();
			$isparent	=	1;
			for ($i=0; $i<count($items); $i++)
			{
				$item		=	$items[$i];
				$obj_cat	=	new stdClass();
				$obj_cat->link	=	$href->process_url($item->children(0)->href,$link);
				$obj_cat->title	=	strip_tags($item->children(0)->innertext);
				$obj_cat->parent	=	$parent;
				$obj_cat->isparent	=	0;
				$this->_data[]	=	$obj_cat;
				$current	=	count($this->_data) -1;
				
				$this->tucows_sub_2($obj_cat->link,$current, $isparent);
				$this->_data[$current]->isparent	=	$isparent;
			}
		}
		$html->clear();
		unset($browser);
		unset($response);
		unset($html);
	}

		// level 1
	function tucows_sub_2($link, $parent, & $check = 1)
	{
		$browser	=	new phpWebHacks();
		$response	=	$browser->get($link);
		$html		=	loadHtmlString($response);
		
		if ($html_cat = $html->find('div[id="categories"]',0)) {
			$check = 1;
			$items	=	$html_cat->find('div[class="padBottom10"]');
			$href	=	new href();
			$isparent	=	1;
			for ($i=0; $i<count($items); $i++)
			{
				$item		=	$items[$i];
				$obj_cat	=	new stdClass();
				$obj_cat->link	=	$href->process_url($item->children(0)->href,$link);
				$obj_cat->title	=	strip_tags($item->children(0)->innertext);
				$obj_cat->parent	=	$parent;
				$obj_cat->isparent	=	0;
				$this->_data[]	=	$obj_cat;
				$current	=	count($this->_data) -1;
				
				$this->tucows_sub_3($obj_cat->link,$current, $isparent);
				$this->_data[$current]->isparent	=	$isparent;
			}
		}else {
			$check = 0;
		}

		$html->clear();
		unset($browser);
		unset($response);
		unset($html);
	}
			// level 3
	function tucows_sub_3($link, $parent, & $check = 1)
	{
		$browser	=	new phpWebHacks();
		$response	=	$browser->get($link);
		$html		=	loadHtmlString($response);
		
		if ($html_cat = $html->find('div[id="categories"]',0)) {
			$check = 1;
			$items	=	$html_cat->find('div[class="padBottom10"]');
			$href	=	new href();
			$isparent	=	1;
			for ($i=0; $i<count($items); $i++)
			{
				$item		=	$items[$i];
				$obj_cat	=	new stdClass();
				$obj_cat->link	=	$href->process_url($item->children(0)->href,$link);
				$obj_cat->title	=	strip_tags($item->children(0)->innertext);
				$obj_cat->parent	=	$parent;
				$obj_cat->isparent	=	0;
				$this->_data[]	=	$obj_cat;
				$current	=	count($this->_data) -1;
				$this->tucows_sub_4($obj_cat->link,$current, $isparent);
				$this->_data[$current]->isparent	=	$isparent;
			}
		}else {
			$check = 0;
		}		
		$html->clear();
		unset($browser);
		unset($response);
		unset($html);
	}
	
	function tucows_sub_4($link, $parent, & $check = 1)
	{
		$browser	=	new phpWebHacks();
		$response	=	$browser->get($link);
		$html		=	loadHtmlString($response);
		
		if ($html_cat = $html->find('div[id="categories"]',0)) {
			$check = 1;
			$items	=	$html_cat->find('div[class="padBottom10"]');
			$href	=	new href();
			for ($i=0; $i<count($items); $i++)
			{
				$item		=	$items[$i];
				$obj_cat	=	new stdClass();
				$obj_cat->link	=	$href->process_url($item->children(0)->href,$link);
				$obj_cat->title	=	strip_tags($item->children(0)->innertext);
				$obj_cat->parent	=	$parent;
				$obj_cat->isparent	=	0;
				$this->_data[]	=	$obj_cat;
				$current	=	count($this->_data) -1;
			}
		}else {
			$check = 0;
		}
		$html->clear();
		unset($browser);
		unset($response);
		unset($html);
	}
	
	function getDriversdown()
	{
		$link = 'http://www.driversdown.com/';
		$db			=&	JFactory::getDBO();
		$browser 	= new phpWebHacks();
		$response 	= $browser->get($link);
		$html 		=	loadHtmlString($response);
		$arr_cat 	=	array();
		$href		=	new href();
		
		if ($boxes		=	$html->find('div[class="cnnHeaderBot"]',0)) {
			$boxes->find('div[class="cnnHeadColRight"]',0)->outertext = '';
			$boxes->find('span[class="cnnGlobalHeaderHotTopic"]',0)->outertext = '';

			$boxes	=	$boxes->find('a');
			$parent = 0;
			
			for ($i=1; $i<count($boxes) - 2; $i++)
			{
				$box = $boxes[$i];
				
				$obj_cat_p	=	new stdClass();
				$obj_cat_p->link	=	$href->process_url($box->href,$link);
				$obj_cat_p->title	=	strip_tags($box->innertext);
				$obj_cat_p->parent	=	-1;
				$arr_cat[]		=	$obj_cat_p;
				$parent			=	count($arr_cat) - 1;

				$response_sub	=	$browser->get($obj_cat_p->link);
				
				$html_sub	=	loadHtmlString($response_sub);
				
				$control	=	$html_sub->find('div[id="cnnT1cCol1"]',0);
				$items		=	$control->find('a');
				for ($j=0; $j<count($items); $j++)
				{
					$item = $items[$j];
					$obj_cat_s	=	new stdClass();
					$obj_cat_s->link	=	str_replace('../','',$href->process_url($item->href,$link));
					$obj_cat_s->title	=	strip_tags($item->innertext);
					$obj_cat_s->parent	=	$parent;
					$arr_cat[]		=	$obj_cat_s;
				}
			}
		}
//		http://www.driversdown.com/driverslist/s_295_1.shtml
		$reg_id = '/\/(s|r)\_(\d+)\_\d\.shtml$/ism';
		
		$arr_parent	=	array();
		for ($i=0; $i<count($arr_cat); $i++)
		{
			$menu 	= $arr_cat[$i];
			$parent	=	0;
			$publish=	0;
			if ($menu->parent != -1)
			{
				$parent		=	intval($arr_parent[$menu->parent]);
				$publish	=	1;
			}
			
			preg_match($reg_id,$menu->link,$mathces);
			
			$query	=	'INSERT INTO `#__software_category_driversdown`'. 
					' SET title = ' . $db->quote($menu->title).
						', parent = '. $db->quote($parent).						
						', link = ' .$db->quote($menu->link).
						', id_origional = ' .$db->quote($mathces[2]).
						', publish = '. $publish ;
			$db->setQuery ( $query );
			$db->query();
			if ($menu->parent == -1) 
				$arr_parent[$i]	=	mysql_insert_id();
		}
	}
	
	function getNodevice()
	{
		$link = 'http://www.nodevice.com/';
		$db			=&	JFactory::getDBO();
		$browser 	= new phpWebHacks();
		$response 	= $browser->get($link);
		$html 		=	loadHtmlString($response);
		$arr_cat 	=	array();
		$href		=	new href();

		if ($boxes	=	$html->find('div[class="categories_list"]',0)) {
			$boxes	=	$boxes->find('li');
					
			$parent = 0;
//			count($boxes
			for ($i=0; $i<count($boxes); $i++)
			{
				$box = $boxes[$i];
				
				$obj_cat_p	=	new stdClass();
				$obj_cat_p->link	=	$href->process_url($box->children(0)->href,$link);
				$obj_cat_p->title	=	strtolower(trim(strip_tags($box->children(0)->innertext)));
				$obj_cat_p->parent	=	-1;
				$arr_cat[]		=	$obj_cat_p;
				$parent			=	count($arr_cat) - 1;

				$response_sub	=	$browser->get($obj_cat_p->link);
				
				$html_sub	=	loadHtmlString($response_sub);
				
				$browse	=	$html_sub->find('div[class="browse"]',0);
				$items		=	$browse->find('li');
				for ($j=0; $j<count($items); $j++)
				{
					$item = $items[$j];
					$obj_cat_s	=	new stdClass();
					$obj_cat_s->link	=	str_replace('../','',$href->process_url($item->children(0)->href,$link));
					$obj_cat_s->title	=	strtolower(trim(strip_tags($item->children(0)->innertext)));
					
					$obj_cat_s->parent	=	$parent;
					$arr_cat[]		=	$obj_cat_s;
				}
			}
		}

//		ttp://www.nodevice.com/driver/category/bios.html
//		http://www.nodevice.com/driver/company/2wire/bios.html
		$reg_id = '/\/driver\/[^\/]+\/([^\/\.]+)/ism';
		
		$arr_parent	=	array();
		for ($i=0; $i<count($arr_cat); $i++)
		{
			$menu 	= $arr_cat[$i];
			$parent	=	0;
			$publish=	0;
			if ($menu->parent != -1)
			{
				$parent		=	intval($arr_parent[$menu->parent]);
				$publish	=	1;
			}
			
			preg_match($reg_id,$menu->link,$mathces);
			
			$query	=	'INSERT INTO `#__software_category_nodevice`'. 
					' SET title = ' . $db->quote(trim(str_replace('driver','',$menu->title))).
						', parent = '. $db->quote($parent).						
						', link = ' .$db->quote($menu->link).
						', alias_origional = ' .$db->quote($mathces[1]).
						', publish = '. $publish ;
			$db->setQuery ( $query );
			$db->query();
			if ($menu->parent == -1) 
				$arr_parent[$i]	=	mysql_insert_id();
		}
	}
	
	function getInfomer($title, $link = '')
	{
		if (!$link) {
			$link	=	'http://software.informer.com/';
		}		
		$db			=&	JFactory::getDBO();
		$browser 	= new phpWebHacks();
		$response 	= $browser->get($link);
		$html 		=	loadHtmlString($response);
		$arr_cat 	=	array();
		$href		=	new href();
		
		$obj_cat_p	=	new stdClass();
		$obj_cat_p->link	=	$link;
		$obj_cat_p->title	=	strtolower(trim($title));
		$obj_cat_p->parent	=	-1;
		$arr_cat[]		=	$obj_cat_p;
		
		if ($boxes	=	$html->find('div[class="categories block"]',0)) {
			$items	=	$boxes->find('a');					
			$parent = 0;
			for ($i=0; $i<count($items); $i++)
			{
				$item = $items[$i];
				
				$obj_cat_p	=	new stdClass();
				$obj_cat_p->link	=	$href->process_url($item->href,$link);
				$obj_cat_p->title	=	strtolower(trim(str_replace('&amp;','&',strip_tags($item->innertext))));
				$obj_cat_p->parent	=	0;
				$arr_cat[]		=	$obj_cat_p;
				$parent			=	count($arr_cat) - 1;

				$response_sub	=	$browser->get($obj_cat_p->link);

				$html_sub	=	loadHtmlString($response_sub);

				$sitems	=	$html_sub->find('td[class="subcat"]');				
				for ($j=0; $j<count($sitems); $j++)
				{
					$sitem = $sitems[$j]->find('a',0);
					$obj_cat_s	=	new stdClass();
					$obj_cat_s->link	=	str_replace('../','',$href->process_url($sitem->href,$link));
					$obj_cat_s->title	=	strtolower(trim(str_replace('&amp;','&',strip_tags($sitem->innertext))));					
					$obj_cat_s->parent	=	$parent;
					$arr_cat[]		=	$obj_cat_s;
				}
			}
		}

//		ttp://www.nodevice.com/driver/category/bios.html
//		http://www.nodevice.com/driver/company/2wire/bios.html
//		$reg_id = '/\/driver\/[^\/]+\/([^\/\.]+)/ism';
		
		$arr_parent	=	array();
		for ($i=0; $i<count($arr_cat); $i++)
		{
			$menu 	= $arr_cat[$i];
			$parent	=	0;
			$publish=	0;
			if ($menu->parent != -1)
			{
				$parent		=	intval($arr_parent[$menu->parent]);
				$publish	=	1;
			}
			if ($menu->parent == 0)
				$publish	=	0;

//			preg_match($reg_id,$menu->link,$mathces);
			
			$query	=	'INSERT INTO `#__software_category_informer`'. 
					' SET title = ' . $db->quote(trim(str_replace('driver','',$menu->title))).
						', parent = '. $db->quote($parent).						
						', link = ' .$db->quote($menu->link).
//						', alias_origional = ' .$db->quote($mathces[1]).
						', publish = '. $publish ;
			$db->setQuery ( $query );
			$db->query();
//			if ($menu->parent == -1) 
			$arr_parent[$i]	=	mysql_insert_id();
		}
	}
	
	function getMacupdate($link = '')
	{
		$link	=	'http://www.macupdate.com';
	
		$db			=&	JFactory::getDBO();
		$query	=	'SELECT * FROM `#__software_category_macupdate` WHERE parent <> 0';
		$db->setQuery($query);
		$cats	=	$db->loadObjectList();
		
		$href		=	new href();
		$arr_cat 	=	array();
		$browser 	= new phpWebHacks();
		for ($i=0; $i< count($cats); $i++)
		{
			$cat	=	$cats[$i];
			$response 	= $browser->get($cat->link);
			$html 		=	loadHtmlString($response);
			if (!$right_sidebar	=	$html->find('div[id="right_sidebar"]',0)) {
				echo $i;
				var_dump($cat);
				echo '<hr />';
				continue;
			}
			$listChild	=	$right_sidebar->children();
			$items		=	null;

			for ($j=0; $j<count($listChild); $j++)
			{
				if (!$listChild[$j]->children(0)) {
					continue;
				}
				if (!$title	=	$listChild[$j]->children(0)->innertext) {
					continue;
				}
				if (strtolower($title) == strtolower($cat->title)) {					
					$items	=	$listChild[$j]->find('a');
					break;		
				}
			}
			if (!$items) {
				die();
				continue;
			}

			for ($j=0; $j<count($items); $j++)
			{
				$obj_cat_s	=	new stdClass();
				$obj_cat_s->title	=	$items[$j]->innertext;
				$obj_cat_s->link	=	$items[$j]->href;
				$obj_cat_s->parent 	=	$cat->id;
				$obj_cat_s->alias_origional 	=	$cat->alias_origional.'/'.strtolower($obj_cat_s->title);
			
				$query	=	'INSERT INTO `#__software_category_macupdate`'. 
					' SET title = ' . $db->quote(trim($obj_cat_s->title)).
						', parent = '. $db->quote($obj_cat_s->parent).
						', link = ' .$db->quote($obj_cat_s->link).
						', alias_origional = ' .$db->quote($obj_cat_s->alias_origional).
						', publish = 1';
				$db->setQuery ( $query );
				$db->query();
			}			
		}
	}
}