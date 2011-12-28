<?php   
     /*
    This is usefull when you are downloading big files, as it
    will prevent time out of the script :
    */
    set_time_limit(0);
    ini_set('display_errors',true);//Just in case we get some errors, let us know....
    
    
$time2 = microtime();
$time2 = explode(" ", $time2);
$time2 = $time2[1] + $time2[0];
$start2 = $time2;
mb_internal_encoding('UTF-8');

function extractCustomHeader($start,$end,$header) {
        $pattern = '/'. $start .'(.*?)'. $end .'/';
        if (preg_match($pattern, $header, $result)) {
            return trim($result[1]);
        } else {
            return false;
        }
    }

//<META HTTP-EQUIV="Refresh" CONTENT="0; URL=http://software-files.download.com/sd/H5kVYggEF6gtL2jPbA4vGGP3tY0XJfwVolEqAx8CyYUXBvrbx31_nn-qjpHWWaRDv441EQX0YpbbADYczPZ-yIftGve-WNFx/software/10982174/10069553/3/MM80-E-217.exe?lop=link&ptype=1901&ontid=18509&siteId=4&edId=3&spi=92a65d3cfa628c41dc077db2b2013bce&pid=10982174&psid=10069553"/>  
////http://software-files.download.com/sd/3kKpHjj5MINaJO9ZNv16OyaS40ReHhaoN7HfDUY-e7OQGg7iMxqJFm5-iGzffPZOAr5UEqPsCDq2Mp3dzQEAdZyanktwib8J/software/11020373/10019223/3/avast_home_setup.exe?lop=link&ptype=1901&ontid=2239&siteId=4&edId=3&spi=4bef54d2490bd20f058a89fc58e693de&pid=11020373&psid=10019223
//<meta http-equiv="refresh" content="3; url=http://download1us.softpedia.com/dl/eb30391e04a804eff42bc57793b5495c/4d4eac2a/100036287/software/office/Outlook_Reminder.msi"><meta http-equiv="refresh" content="60; url=http://win.softpedia.com/">    <title>Download starting... - Softpedia</title> 

function getMetaRedirect0($body) {
    $start='\<META HTTP-EQUIV="Refresh" CONTENT="0; URL=';
    $end='"\/\>';
        $pattern = '/'. $start .'(.*?)'. $end .'/i';
        if (preg_match($pattern,$body, $result)) {
            return trim($result[1]);
        } else {
            return false;
        }
    }    
//<html><head><meta http-equiv='refresh' content='1;URL=http://www.softpedia.com/redir2.php?pid=400053677' /><title>Redirecting to correct download page...</title><body>Redirecting to correct download page...</body></head></html>
function getMetaRedirect($body) {
    $body=str_replace('"',"'",$body); 
    $start="\<meta http-equiv='refresh' content='\d+;[ |]URL=";
    $end="'";
        $pattern = '/'. $start .'(.*?)'. $end .'/i';
        if (preg_match($pattern,$body, $result)) {  
            return trim(trim(urldecode($result[1])," '\""));
        } else {
            return false;
        }
    }
     
/*                        <a href="http://download2us.softpedia.com/dl/f3618a2a1f299aff1934aa6d41eecc0a/4a2a86cd/400036300/mac/Security/iantivirus.pkg" title="Download iAntivirus from Softpedia Secure Download (US)" target="_blank">
                        Softpedia Secure Download (US)</b></a> <span class="download_smalltext">[OTHER]</span><br></p></td></tr><tr><td width="35" class="padding_topbottom5px"><span class="fontsize11"><a href="http://download.softpedia.ro/dl/f3618a2a1f299aff1934aa6d41eecc0a/4a2a86cd/400036300/mac/Security/iantivirus.pkg" target="_blank"><img border="0" src="/base_img/download_s_bullet.gif" width="31" height="24"></span></td><td><p class="fontsize14"><b><a href="http://download.softpedia.ro/dl/f3618a2a1f299aff1934aa6d41eecc0a/4a2a86cd/400036300/mac/Security/iantivirus.pkg" title="Download iAntivirus from Softpedia Secure Download (RO)" target="_blank">Softpedia Secure Download (RO)</b></a> 
                    

  <a href="http://download.softpedia.com/dl/3fd5f4d6ff4861bfc42e3fbb07adfae2/4a336875/100022555/software/other_tools/DaVinci.exe" target="_blank" rel="nofollow" title="Download Da Vinci Code generator from Softpedia Secure Download (US)">Softpedia Secure Download (US)</b></a> <span class="download_smalltext">[EXE]</span><br></p></td></tr>
  <tr><td width="35" class="padding_topbottom5px"><span class="fontsize11"><a href="http://download.softpedia.ro/dl/0ca42b757357f83e80608cc9bffa5216/4a336875/100022555/software/OTHERS/DaVinci.exe" target="_blank" rel="nofollow"><img border="0" src="/base_img/download_s_bullet.gif" width="31" height="24"></span></td><td><p class="fontsize14"><b>
  <a href="http://download.softpedia.ro/dl/0ca42b757357f83e80608cc9bffa5216/4a336875/100022555/software/OTHERS/DaVinci.exe" target="_blank" rel="nofollow" title="Download Da Vinci Code generator from Softpedia Secure Download (RO)">Softpedia Secure Download (RO)</b></a> <span class="download_smalltext">[EXE]</span><br></p></td></tr></table>                        <p class="fontsize11"><br><img src="/base_img/s_bullet.gif" style="margin-bottom: -3px;"> <span class="smalltext">Secure downloads are files hosted and checked by Softpedia </span></p                            
    <b><a href="http://download2us.softpedia.com/dl/6b809ac31ca8611a2058a9646e903145/48b5c003/600007945//download.softpedia.com/webscripts/php/typo3_src+dummy-4.2.0.zip" title="Download TYPO3 from Softpedia Mirror (US)" target="_blank">Softpedia Mirror (US)</b></a>                
                    * 
<a href="http://www.softpedia.com/dyn-postdownload.php?p=131714&amp;t=0&amp;i=1" title="Download Microsoft HPC Pack 2008 and HPC Pack 2008 R2 Tool Pack from HPC2008R2_SP1_x64">HPC2008R2_SP1_x64</a>
                    */                    
function getFinalDownloadURL($body) {
    if ($result	=	getMetaRedirect($body)) {
        $url =urldecode(trim(trim($result," '\"")));
        return $url;
    } else {
        return false;
    }       
} 

//	/download_avant_browser/download/fd84de7b84d5900e8de85796e8de757f/
function getDownloadPage($body) {
    $body=str_replace("'",'"',$body); 
    $start='<a href="';
    $end='"';
    $pattern = '.*'. $start .'(.*?\/download_[^\/]*\/download.*?)'. $end;
             
    if (mb_eregi($pattern,$body, $result)) {
        return trim(urldecode($result[1])," '\"");
    }else {
       return false;

    }
}

if (!function_exists('remove_copy_prefix')) {
	function remove_copy_prefix ($string) {
        $patern1="/Copy \(\d+\) of /";
        $name1=preg_replace($patern1,'',$string);
        $patern2="/Copy of/";
        $name1=preg_replace($patern2,'',$name1);
        $patern3="/Copy-\(\d+\)-of-/";
        $name1=preg_replace($patern3,'',$name1);
        return $name1;
	}
}        

    
//    6008/b4e99444cda04980ad3a031fd07bac09/absetup.exe
function getDownloadFilename($url) {
        $url=urldecode($url);
        $pattern = '/\/([^\/\.]+\.[^\/]+)$/ism';       
        if (preg_match($pattern,$url, $result)) {        
            return trim(remove_copy_prefix(str_replace('?','-',$result[1])));
        } else {
            return false;
        } 
    }
        
//Content-Disposition: attachment; fileName=1clickencryptsetup.exe
//function getDownloadFilename($header) {
//        $pattern = '/Content-Disposition:.*?attachment;.*?filename=(.*?)\n/i';
//        if (preg_match($pattern, $header, $result)) {
//            $filename=trim($result[1]," '\"\r\n\t");
//            $filename=trim($filename);
//            $filename=str_replace(' ','-',$filename);
////            $filename=str_replace('-trial ','-',$filename);
//            return $filename;
//        } else {
//            return false;
//        }
//    }         
    
function http_parse_headers2( $header )
    {
        $retVal = array();
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
        foreach( $fields as $field ) {
            if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
                $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
                if( isset($retVal[$match[1]]) ) {
                    $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
                } else {
                    $retVal[$match[1]] = trim($match[2]);
                }
            }
        }
        return $retVal;
    }    
    
function downloadFile( $url_to_download,$path_save,$filename_save) {
    $url_to_download=str_replace(' ','%20',$url_to_download);
    //*  
    $fp = fopen ($path_save.$filename_save, 'w+');//This is the file where we save the information
    $ch = curl_init($url_to_download);//Here is the file we are downloading
#    curl_setopt($ch, CURLOPT_TIMEOUT, 900);
#    curl_setopt($ch, CURLOPT_FILE, $fp);
#    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		
 	$curl_options_header = array(
	    CURLOPT_REFERER        =>"$url_to_download",    
	    CURLOPT_FILE  => $fp,
	   	CURLOPT_FOLLOWLOCATION => true,     // follow redirects
	//	CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11", // who am i
	   	CURLOPT_USERAGENT      => "Opera/9.60 (J2ME/MIDP; Opera Mini/4.1.11320/608; U; en) Presto/2.2.0", // who am i
	    CURLOPT_AUTOREFERER    => true,     // set referer on redirect
	    CURLOPT_CONNECTTIMEOUT => 3600,      // timeout on connect
	    CURLOPT_TIMEOUT        => 3600,      // timeout on response
	    CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	);
	
   
    
    curl_setopt_array( $ch, $curl_options_header);   
    curl_exec($ch);
    $downloadInfo=curl_getinfo($ch);  //print_r($downloadInfo); die();
    curl_close($ch);
    fclose($fp);  
    return $downloadInfo;
}    


?>

