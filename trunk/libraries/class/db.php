<?php
/**
 * YOS News Crawler Component
 *
 * @package		yos_news_crawler
 * @subpackage	CMS
 * @link		http://yopensource.com
 * @author		yopensource
 * @copyright 	yopensource (yopensource@gmail.com)
 * @license		Commercial
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
class dbo extends JObject {
	//object article
	var $driver		=	null;
	var $host		=	null;
	var $user		=	null;
	var $password	=	null;
	var $database	=	null;
	var $prefix		=	null;
	
	public $_db		=	null;
	
	public function __construct()
	{			
	}
	
	function getDBO($host, $user, $password, $dbname, $prefix, $dbtype)
	{	
		$option_ 	= array(); //prevent problems 
		$option_['driver']   = $dbtype;       	// Database driver name
		$option_['host']     = $host;    		// Database host name
		$option_['user']     = $user;  	    	// User for database authentication
		$option_['password'] = $password;  		// Password for database authentication
		$option_['database'] = $dbname;  	  	// Database name
		$option_['prefix']   = $prefix;          // Database prefix (may be empty)
		if ( empty($option_['user']) || empty($option_['database']) || empty($option_['driver']) || empty($option_['host']))
		{
			$config	=	JFactory::getConfig();
			
			$option_['driver']   = $config->getValue('dbtype');       	// Database driver name
			$option_['host']     = $config->getValue('host');    		// Database host name
			$option_['user']     = $config->getValue('user');  	    	// User for database authentication
			$option_['password'] = $config->getValue('password');  		// Password for database authentication
			$option_['database'] = $config->getValue('db');  	  	// Database name		
		}
		
		$db_	= &JDatabase::getInstance( $option_ );
		if (!empty($db_->message)) {
				$this->msg = JText::sprintf($db_->message);
				return false;
			}	
		$this->_dbo=$db_;
		return $this->_dbo;
	}
}