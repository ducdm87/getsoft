<?php


class DirDownload_cdn2011 extends JObject
{
	public $_row = null;
	
	function __construct()
	{
		global $mainframe;
		JTable::addIncludePath(JPATH_COMPONENT_SITE.DS.'tables');
		$this->_row =& JTable::getInstance('Download_cdn2011','Table');
		parent::__construct();
	}
	function save(& $content)
	{
		global $mainframe;		
		if (isset($content->id) and $content->id) {				
			$this->_row->load($content->id);				
		}elseif (isset($content->PA_ID) and $content->PA_ID)
		{
			$db = JFactory::getDBO();
			
			$query = 'SELECT * FROM `#__download_cdn2011` where PA_ID = '.$db->quote($content->PA_ID);
			$db->setQuery($query);
			if ($obj = $db->loadObject()) {
				$this->_row->bind($obj);
			}
		}

		$this->_row->bind($content);		
		$this->_row->store();	
		$content	=	$this->_row;

		return  $this->_row;
	}
	function get($id = null)
	{
		$this->_row->load($id);
		$content	=	$this->_row;
		return $content;
	}
	function getList($select = 'id,SourceURL', $where ="1 = 1", $limitstart = 0, $number = null, $orderBy = null, $order_dir = 'ASC')
	{
		global $mainframe;
		$db = JFactory::getDBO();
		$limit = $order = '';		
		
		if ($number) {
			$limit = " LIMIT $limitstart,$number ";
		}
		if ($orderBy) {
			$order = " ORDER BY $orderBy $order_dir ";
		}
		$query = 'SELECT '.$select.' FROM `#__download_cdn2011` WHERE '. $where.$order.$limit;
		$db->setQuery($query);		
		return  $db->loadObjectList();
	}
}