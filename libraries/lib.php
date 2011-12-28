<?php


	function modInsertSoft($pid, $link,$title,$icon,$alias, $siteID = 'fhp100', $SiteName = 'filehippo.com' , $preview = null)
	{
		$date = JFactory::getDate();
		
		$reg_name	=	'/^([^\d]+\s)(\d+\.*.*?)$/ism';		
		$article = new DIRarticle();		
		$content = new stdClass();	
		
		$content->SourceURL 				=	$link;
		$content->firstExtractionTime		= 	$date->toMySQL();
		$content->latestExtractionTime		= 	$date->toMySQL();
		$content->SiteID 			= 	$siteID;
		$content->SiteName 			= 	$SiteName;
		$content->state 			= 	0;
		$content->ProductName 		= 	$title;
		if (preg_match($reg_name, $title, $matches_name)) {
			$content->ProductName 		= 	trim($matches_name[1]);
			$content->version 			= 	trim($matches_name[2]);
		}
		$content->ProductName_alias = 	$alias;
		$content->PreviousVersions 	= 	$preview;
		$content->Icon				=	$icon;			
		$content->sid				=	1;
		$content->pid				=	$pid;
		$article->saveArticle($content);
		return true;
	}
	
	function modStoreScreenshots($pid,$link_sc, $SiteID, $title, $state = 0, $patch_image )
	{
		$db		=	JFactory::getDBO();
		$date = JFactory::getDate();
		
		$fmtsql = "INSERT INTO `#__smedia2011a` ".
					"SET `pid` = ". $db->quote($pid).
						", `SourceURL` = ". $db->quote($link_sc).
						", `SiteID` = ". $db->quote($SiteID).
						", `Title` = ". $db->quote($title).
						", `firstExtractionTime` = ". $db->quote($date->toMySQL()).
						", `latestExtractionTime` = ". $db->quote($date->toMySQL()).
						", `type` = 1".
						", `state` = ". $db->quote($state).
					" ON DUPLICATE KEY UPDATE  ".				
						"`SiteID` = ". $db->quote($SiteID).
						",`latestExtractionTime` = ". $db->quote($date->toMySQL()).
						", `type` = 1".
						", `state` = ". $db->quote($state).
						",`Title` = ". $db->quote($title);
		
		$db->setQuery( $fmtsql );
		
		if (!$db->query()) {
			$messege	=	$db->getQuery();
			JError::raiseNotice('500',$messege);
			return false;
		}
		return true;
	}