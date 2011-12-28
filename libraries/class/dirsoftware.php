<?php


class DIRsoftware extends JObject
{
	public $_row = null;
	
	function __construct()
	{
		global $mainframe;
		JTable::addIncludePath(JPATH_COMPONENT_SITE.DS.'tables');
		$this->_row =& JTable::getInstance('software2011a','Table');
		parent::__construct();
	}
	function save(& $content)
	{
		global $mainframe;
		$db	=	JFactory::getDBO();	
		if (isset($content->id) and $content->id) {				
			$this->_row->load($content->id);				
		}

		$this->_row->bind($content);
		$fmtsql = "INSERT INTO `#__software2011a` SET %s ON DUPLICATE KEY UPDATE  %s  ";
		$insert = array();
		$update = array();
		foreach (get_object_vars( $this->_row ) as $k => $v) {
			if (is_array($v) or is_object($v) or $v === NULL) {
				continue;
			}
			if ($k[0] == '_') { // internal field
				continue;
			}		
			$insert[] = $db->NameQuote( $k ).' = '.$db->Quote( $v );
			if ($k != 'id' and $k != 'pid') {
				$update[] = $db->NameQuote( $k ).' = '.$db->Quote( $v );
			}		
		}
		$db->setQuery( sprintf( $fmtsql, implode( ",", $insert ) ,  implode( ",", $update ) ) );
		
		if (!$db->query()) {			
			return false;
		}
		$this->_row->load($content->id);
		$content	=	$this->_row;
		return  $this->_row;
	}
	
	function get($id = null)
	{
		$this->_row->load($id);
		$content	=	$this->_row;
		return $content;
	}
	function checkExiting($pid)
	{
		$db = JFactory::getDBO();
			
		$query = 'SELECT id FROM `#__software2011a` where pid = '.$db->quote($pid);
		$db->setQuery($query);	
		if ($id = $db->loadResult()) {
			return $id;
		}
		return false;
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
		$query = 'SELECT '.$select.' FROM `#__software2011a` WHERE '. $where.$order.$limit;
		$db->setQuery($query);		
		return  $db->loadObjectList();
	}
	function getObj($select = 'id,SourceURL', $where ="1 = 1")
	{
		global $mainframe;
		$db = JFactory::getDBO();		
		
		$query = 'SELECT '.$select.' FROM `#__software2011a` WHERE '. $where;
		$db->setQuery($query);		
		return  $db->loadObject();
	}
	function getResult($select = 'id,SourceURL', $where ="1 = 1")
	{
		global $mainframe;
		$db = JFactory::getDBO();		
		
		$query = 'SELECT '.$select.' FROM `#__software2011a` WHERE '. $where;
		$db->setQuery($query);		
		return  $db->loadResult();		
	}
	function getCount($where ="1 = 1")
	{
		global $mainframe;
		$db = JFactory::getDBO();
		$limit = $order = '';		
		
		$query = 'SELECT count(*) FROM `#__software2011a` WHERE '. $where;
		$db->setQuery($query);
			
		return  $db->loadResult();
	}
}