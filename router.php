<?php
/**
 * @version	$Id: router.php $
 * @package	F5Gallery
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	Commercial
 */
function get_softBuildRoute(&$query)
{
	$segments = array();
	
	// get a menu item based on Itemid or currently active
	$menu = &JSite::getMenu();
	if (empty($query['Itemid'])) {
		$menuItem = &$menu->getActive();
	} else {
		$menuItem = &$menu->getItem($query['Itemid']);
	}
	$mView	= (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
	$mCatid	= (empty($menuItem->query['category'])) ? null : $menuItem->query['category'];
			
	if($mView == 'gallery')
	{ 
		if(isset($query['view']) && $query['view'] == 'image')
		{			
			$segments[] = JText::_('image-info');
			unset($query['view']);
		}
	}
	
	if(isset($query['task']))
	{
		switch ($query['task'])
		{
			case 'image.display':
			{
				$string=JText::_('images');
				$segments[]=$string;
				unset($query['task']);
				break;
			}
			case 'image.display_thumb':
			{
				$string=JText::_('thumbs');
				$segments[]=$string;
				unset($query['task']);
				break;
			}
			case 'image.display_small_thumb':
			{
				$string=JText::_('small-thumbs');
				$segments[]=$string;
				unset($query['task']);
				break;
			}
			case 'image.report':
			{
				$string=JText::_('report-image');
				$segments[]=$string;
				unset($query['task']);
				break;
			}
		}
	}
	
	if(!empty($query['img']))
	{	
		$img = $query['img'];
		$arr_img = explode(':', $img);
		$str_img = '';
		$str_img .= empty($arr_img[0]) ? '' : intval($arr_img[0]);
		$str_img .= empty($arr_img[1]) ? '' : '-'.$arr_img[1];
		
		$segments[] = $str_img;
		unset($query['img']);
	}
		
	return $segments;
}

function get_softParseRoute(&$segments)
{	
	$vars = array();
	
	//Get the active menu item
	$menu =& JSite::getMenu();
	$item =& $menu->getActive();

	// Count route segments
	$count = count($segments);
	
	if ($count == 2) {
		$segments[0] = str_replace(':', '-', $segments[0]);
	}
	
	$vars['img'] = empty($segments[$count - 1]) ? null : intval($segments[$count - 1]);

	switch ($segments[0]) {
		case 'image-info':
		{
			$vars['view']='image';
			break;
		}
		case 'images': 
		{
			$vars['task']='image.display';
			break;
		}	
		case 'thumbs': 
		{
			$vars['task']='image.display_thumb';
			break;
		}
		case 'small-thumbs': 
		{
			$vars['task']='image.display_small_thumb';
			break;
		}
		case 'report-image': 
		{
			$vars['task']='image.report';
			break;
		}
		default:
		{
			$vars['view']='image';
		}
	}

	return $vars;
}
