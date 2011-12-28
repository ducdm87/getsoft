<?php
/**
 * @version	$Id: view.html.php $
 * @package	F5Gallery
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	Commercial
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

// Set the table directory
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

class F5GViewGallery extends JView
{
	function display($tpl=null)
	{
		global $mainframe;
				
		$model	=& $this->getModel('gallery');
	
		$images	= $model->get_images();
		$pageNav = $model->getPagination();
		
		$cat_info = $model->get_cat_info();
		
		$config	= $model->getConfig();
		$list	= $model->getList();
		
		//set session viewed from gallery
		$model->set_gallery_session();
		
		//process layout
		if ($tpl === null) {
			$theme = $config->site_theme;
			$theme_file = dirname(__FILE__).DS.'tmpl'.DS.$this->getLayout().'_'.$theme.'.php';
			if (is_readable($theme_file)) {
				$tpl = $theme;
			}
		}
				
		$this->assignRef('images',$images );
		$this->assignRef('pageNav',	$pageNav);
		$this->assignRef('category', $cat_info);
		$this->assignRef('list',	$list);		
		$this->assignRef('config',	$config);
		
		
		parent::display($tpl);
	}
}

?>