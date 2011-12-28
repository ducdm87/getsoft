<?php 
// no direct access
//defined( '_VALID_MOS' ) or die( 'Restricted access' );
mb_internal_encoding('UTF-8');
// clean number and some others
function SoftwareTitle1 ($stitle) {

    $subfixs2 = array (' 1992',' 1993',' 1994',' 1995',' 1996',' 1997',' 1998',' 1999',' 2000',' 2001',' 2002',' 2003',' 2004',' 2005',' 2006',' 2007',' 2008',' 2009',' 2010',' 2011',' 2012');
    $subfixs3 = array ('3d');    
//        $subfixs2 = array ();
   $s_year='';
   $s_version='';
   $s_version_l='';   
   $s_version_n='';  
    
     

 //   do {
        $stitle_temp=$stitle;
        if (!in_array(mb_substr($stitle_temp, -5,5),$subfixs2)) {          
       
            $lastword2=1;
            $lastword3=1;
                        
            $last_space=mb_strrpos($stitle_temp,' ');
            $pre_char=mb_substr($stitle_temp,$last_space+1,1);
            $lastword = mb_substr($stitle_temp,$last_space+2); 
            $lastword1 = mb_substr($stitle_temp,$last_space+1);             
            $last_char = mb_strtolower(mb_substr($lastword,-1,1));
                    
            if (!ctype_alpha($pre_char) | $pre_char =='v' | $pre_char =='V') {
                
                $last_char = mb_substr($lastword,-1,1); 
 

                     if (!ctype_alpha($last_char) & ($pre_char =='v' | $pre_char =='V')) {                                      
                        $lastword2 =trim(rtrim($lastword,"0123456789-+=\$\£\¥\₩\€\元\₭₫\б,./\?;'[]\\`~@#%^&*(){}|:\"<>"));                   
                        if (strlen($lastword2)==0) {
                            $stitle=substr_replace ($stitle,'', $last_space+1,1);
                            $stitle=trim(rtrim($stitle,"0123456789-+=\$\£\¥\₩\€\元\₭₫\б,./\?;'[]\\`~@#%^&*(){}|:\"<>")); 
                         
                            $s_version= $lastword." ".$s_version;  
                            $s_version_n= $lastword." ".$s_version_n;                                                   
                        }                                   
                      
                      } else if (!ctype_alpha($last_char) & ($pre_char <>'v' & $pre_char <>'V')) {
                            $lastword2 =trim(rtrim($lastword1,"0123456789-+=\$\£\¥\₩\€\元\₭₫\б,./\?;'[]\\`~@#%^&*(){}|:\"<>"));                           
                        if (strlen($lastword2)==0) {                          
                            $stitle=trim(rtrim($stitle,"0123456789-+=\$\£\¥\₩\€\元\₭₫\б,./\?;'[]\\`~@#%^&*(){}|:\"<>"));
                            $s_version_l= $lastword1;                                
                            $s_version= $lastword1." ".$s_version;  
                            $s_version_n= $lastword1." ".$s_version_n; 
                        }
                                      
                      } else if (ctype_alpha($last_char) & ($pre_char <>'v' & $pre_char <>'V')) {
                          
                          if (!in_array(mb_strtolower($lastword1),$subfixs3)) {
                             $lastword1b=substr_replace ($lastword1,'', -1,1); 
                             $lastword3 =trim(rtrim($lastword1b,"0123456789-+=\$\£\¥\₩\€\元\₭₫\б,./\?;'[]\\`~@#%^&*(){}|:\"<>"));               
                             if (strlen($lastword3)==0) {
                                $stitle=substr_replace ($stitle,'', -1,1); 
                                $stitle=trim(rtrim($stitle,"0123456789-+=\$\£\¥\₩\€\元\₭₫\б,./\?;'[]\\`~@#%^&*(){}|:\"<>"));                                 
                                $s_version_n= $lastword1b." ".(ord($last_char)-96)." ".$s_version_n; 
                                $s_version= $lastword1." ".$s_version;
                                $s_version_l= $lastword1;                                                                                            
                            }                              
                          }                      
                      
                      } else if (ctype_alpha($last_char) & ($pre_char =='v' | $pre_char =='V')) {
                          
                             $lastwordb=substr_replace ($lastword,'', -1,1); 
                             $lastword4 =trim(rtrim($lastwordb,"0123456789-+=\$\£\¥\₩\€\元\₭₫\б,./\?;'[]\\`~@#%^&*(){}|:\"<>"));               
                             if (strlen($lastword4)==0) {
                                $stitle=substr_replace ($stitle,'', -1,1); 
                                 $stitle=substr_replace ($stitle,'', $last_space+1,1);                                
                                 $stitle=trim(rtrim($stitle,"0123456789-+=\$\£\¥\₩\€\元\₭₫\б,./\?;'[]\\`~@#%^&*(){}|:\"<>"));                                 
                                $s_version_n= $lastwordb." ".(ord($last_char)-96)." ".$s_version_n; 
                                $s_version= $lastword." ".$s_version;                                                        
                            }                         
                      
                      }                                     
                
            } 
                    
        } else {
            $s_year=trim(mb_substr($stitle_temp, -5,5));
            $stitle = preg_replace('/( build )(\d+)$/i',"$1 0.$2",$stitle);
                                  
        } 
        
  //  } while ($stitle_temp<>$stitle);

    return array($stitle,$s_version,$s_year,$s_version_n,$s_version_l); 
}

// clean number and some others plus build, version
function SoftwareTitle2 ($stitle) {
 //   $stitle='WebPod Studio 3D Pro 1.0 RC8';
 $stitle =html_entity_decode($stitle);
 
$stitle = ltrim($stitle,'`-^__-&');
 
$stitle = preg_replace('/(.*?)(\()(for .*?)(\))(.*)/i',"$1 $3 $5",$stitle);

$stitle = preg_replace('/\s+/i'," ",$stitle);

$stitle = str_replace('service-pack','service pack',$stitle);
$stitle = str_replace('hot-fix','hotfix',$stitle);
$stitle = str_replace('Hotfix Patch','hotfix',$stitle);

$stitle = preg_replace('/( build)(\d+)/i',"build $2",$stitle);
$stitle = preg_replace('/( build[-:#])(\d+)/i',"build $2",$stitle);
$stitle = preg_replace('/( build )([#a-z])(\d+)/i',"build $3",$stitle);
$stitle = preg_replace('/(\d+)([#a-z])(\d+)$/i',"$1$2 $3",$stitle);

$stitle = preg_replace('/(\d+)\s*(final)$/i',"$1 final version",$stitle);
$stitle = preg_replace('/(\d+)\s*(stable)$/i',"$1 stable version",$stitle);
$stitle = preg_replace('/(\d+)\s*(update)$/i',"$1 updated patch",$stitle);
$stitle = preg_replace('/(\d+)\s*(patch)$/i',"$1 updated patch",$stitle);
//release candidate hotfix update patch  pack service pack sp
$stitle = preg_replace('/(\d+)(beta|alpha|pre|release|rc|hotfix|update|patch|pack|service|sp)/i',"$1 $2",$stitle);
$stitle = preg_replace('/([ (-])(beta|alpha|release|release candidate|rc|hotfix|update|patch|pack|sp)(\d+)/i',"$1$2 $3",$stitle);

$stitle = preg_replace('/( kb )(\d+)/i'," KB$2",$stitle);
// KB 911996

$s_version ='';
$s_year='';
$s_edition =''; 
$s_release='';
$s_build='';
$s_vindex='';
$s_version_n ='';
$s_version_l='';

$subfixs= array (
                  'network edition',
                  'small business edition',
                'corporate edition',
                'developer team edition',
                'educational edition',
                'terminal server edition',
                
                  'unlimited site edition',
                  'site edition',
                  'professional edition',
                  'pro edition',
                  'standard edition',
                  'business edition',
                  'business basic edition',
                  'business premium edition',
                  'enterprise edition',
                  'single license',
                  'single user edition',
                  'single-user edition',
                  'personal edition',                
                  'student edition',
                  'academic edition',
                  'desktop edition',
                  'corperate edition',
                  'full edition',
                  'family edition',
                  'home edition',
                  'home basic edition',
                  'home premium edition',
                  'industry edition',
                  'commercial edition',
                  'faculty edition',
                  'advanced edition',
                  'premier edition',
                  'premium edition',
                  'ultimate edition',
                  'special edition',
                  'limited edition',
                  'basic edition',
                  'trial edition',
                  'demo edition',
                  'deluxe edition',
                  'ultra edition',
                  'gold edition',
                  'platinum edition',
                  'silver edition',
                  'full edition',
                  'lite edition',
                  'teacher edition',
                  'school edition',
                  'unlimited edition',
                  'retail edition',
                  'express edition',
                  'free edition',
                  'single domain edition',
                  'web edition',
                  'data center edition',
                  'unlimited servers edition',
                  'servers edition',
                  'single developer edition',
                  'developer edition',
                  'designer edition',
                  'webmaster edition',
                  'worldwide edition',
                  'unlimited site license',
                  'one year license',
                  'two year license',
                  'three year license', 
                   '1 year license',
                  '3 year license',
                  '3 year license',                                   
                  'lifetime license',
                  'life-time license', 
                   '100 user license' ,                 
                  '50 user license',
                  '20 user license',
                  '10 user license', 
                  '5 user license', 
                  '3 user license',
                  '2 user license',                                                                        
                  '1 user license',
                  '100-user license' ,                 
                  '50-user license',
                  '20-user license',
                  '10-user license', 
                  '5-user license', 
                  '3-user license',
                  '2-user license',                                                                        
                  '1-user license', 
                   '100 computer license' ,                 
                  '50 computer license',
                  '20 computer license',
                  '10 computer license', 
                  '5 computer license', 
                  '3 computer license',
                  '2 computer license',                                                                        
                  '1 computer license',
                  '100-computer license' ,                 
                  '50-computer license',
                  '20-computer license',
                  '10-computer license', 
                  '5-computer license', 
                  '3-computer license',
                  '2-computer license',                                                                        
                  '1-computer license',
                    '100 pc license' ,                 
                  '50 pc license',
                  '20 pc license',
                  '10 pc license', 
                  '5 pc license', 
                  '3 pc license',
                  '2 pc license',                                                                        
                  '1 pc license',
                  '100-pc license' ,                 
                  '50-pc license',
                  '20-pc license',
                  '10-pc license', 
                  '5-pc license', 
                  '3-pc license',
                  '2-pc license',                                                                        
                  '1-pc license',                 
                  'site license for school',
                  '1 year package license',
                  '2 year package license',
                  '3 year package license',                   
                  'network license',
                  'additional license',
                  'source code license',
                  'small business license',
                'single developer license',
                'unlimited usage license',
                'commercial redistribution license',
                'commercial distribution license',
                'unlimited distribution license',
                'unlimited redistribution license',
                'redistribution license',
                'distribution license',
                'unlimited corporate license',
                'corporate license',
                'developer team license',
                'educational site license',
                'terminal server license',
                'unlimited client license',                                             
                  'enterprise site license',                  
                  'site license',
                  'professional license',
                  'pro license',
                  'standard license',
                  'business license',
                  'business basic license',
                  'business premium license',
                  'enterprise license',
                  'single license',
                  'single user license',
                  'single-user license',
                  'personal license',                
                  'student license',
                  'academic license',
                  'desktop license',
                  'corperate license',
                  'full license',
                  'family license',
                  'home license',
                  'home basic license',
                  'home premium license',
                  'industry license',
                  'commercial license',
                  'faculty license',
                  'advanced license',
                  'premier license',
                  'premium license',
                  'special license', 
                  'limited license',                 
                  'ultimate license',
                  'basic license',
                  'trial license',
                  'demo license',
                  'deluxe license',
                  'ultra license',
                  'gold license',
                  'platinum license',
                  'silver license',
                  'full license',
                  'lite license',
                  'teacher license',
                  'school license',
                  'unlimited license',
                  'retail license',
                  'express license',
                  'free license',
                  'single domain license',
                  'web license',
                  'data center license',
                  'unlimited servers license',
                  'servers license',
                  'single developer license',
                  'developer license',
                  'designer license',
                  'webmaster license',
                  'network version',
                  'multi domain license',
                   'multi-domain license', 
                   'unlimited-domain license',  
                   'unlimited domain license',
                   'single-domain license',  
                   'single domain license',                      
                   '1-domain license',  
                   '1 domain license',                                                       
                  'small business version',
                  'corporate version',
                  'developer team version',
                   'educational version',
                    'terminal server version',
                  'worldwide edition',
                   'unlimited site version',
                  'site version',
                  'professional version',
                  'pro version',
                  'standard version',
                  'business version',
                  'business basic version',
                  'business premium version',
                  'enterprise version',
                  'single license',
                  'single user version',
                  'single-user version',
                  'personal version',                
                  'student version',
                  'academic version',
                  'desktop version',
                  'corperate version',
                  'full version',
                  'family version',
                  'home version',
                  'home basic version',
                  'home premium version',
                  'industry version',
                  'commercial version',
                  'faculty version',
                  'advanced version',
                  'premier version',
                  'premium version',
                  'special version',
                  'limited version',                  
                  'ultimate version',
                  'basic version',
                  'trial version',
                  'demo version',
                  'deluxe version',
                  'ultra version',
                  'gold version',
                  'platinum version',
                  'silver version',
                  'full version',
                  'lite version',
                  'teacher version',
                  'school version',
                  'unlimited version',
                  'retail version',
                  'express version',
                  'free version',
                  'single domain version',
                  'web version',
                  'data center version',
                  'unlimited servers version',
                  'servers version',
                  'single developer version',
                  'developer version',
                  'designer version',
                  'webmaster version', 
                  'worldwide edition',
                  'for single user', 
                  'single user',                                                
                  'version',
                  'ver.',
                  'lite',
                  'std',
                  'pro',                  
                  'professional',                                               
                  'trial',
                  'demo',
                  'unlimited' ,
                  '16-bit edition',                  
                  '32-bit edition',
                  '64-bit edition',
                  '16bit edition',                  
                  '32bit edition',
                  '64bit edition',
                  'x86 edition',
                  'x64 edition', 
                  '16-bit version',                  
                  '32-bit version',
                  '64-bit version',
                  '16bit version',                  
                  '32bit version',
                  '64bit version',
                  'x86 version',
                  'x64 version',                                     
                  '(16-bit)',                  
                  '(32-bit',
                  '(64-bit)',
                  '(16bit)',                  
                  '(32bit)',
                  '(64bit)',
                 '(x86)',
                 '(x64)'
                                                                 
                  );
 
/* more
for windows
for mac ...

*/

$subfix_versions = array ('public pre-alpha' => 2,
                  'public pre alpha' => 2,                   
                  'public alpha' => 4,                 
                  'pre-alpha' => 1,
                  'pre alpha' => 1, 
                  'alpha preview'  => 4,                                                                                                              
                  'alpha'  => 4, 
                  'public pre-beta'  => 12,
                  'public pre beta'  => 12,                                   
                  'public beta' => 1,
                  'pre-beta' => 10, 
                  'pre beta' => 10,
                  'beta preview' => 15,
                  'beta' => 15,                                    
                  'pre-release candidate 1' => 21,
                  'pre-release candidate 2' => 22,                  
                  'pre-release candidate 3' => 23,                  
                  'pre-release candidate 4' => 24,                  
                  'pre-release candidate 5' => 25,
                  'pre-release candidate 6' => 26,
                  'pre-release candidate 7' => 27,                  
                  'pre-release candidate 8' => 28,
                  'pre-release candidate 9' => 29,
                  'pre-release candidate 10' => 30,                                    
                  'pre-release candidate' => 21,                  
                  'pre-release 1' => 21,
                  'pre-release 2' => 22,                  
                  'pre-release 3' => 23,                  
                  'pre-release 4' => 24,                  
                  'pre-release 5' => 25,
                  'pre-release 6' => 26,
                  'pre-release 7' => 27,                  
                  'pre-release 8' => 28,
                  'pre-release 9' => 29,
                  'pre-release 10' => 30,                    
                  'pre-release' => 21,                  
                  'release candidate 1' => 21,
                  'release candidate 2' => 22,                  
                  'release candidate 3' => 23,                  
                  'release candidate 4' => 24,                  
                  'release candidate 5' => 25,
                  'release candidate 6' => 26,
                  'release candidate 7' => 27,                  
                  'release candidate 8' => 28,
                  'release candidate 9' => 29,
                  'release candidate 10' => 30,                    
                  'release candidate' => 21,
                  'service release 1' => 61,
                  'service release 2' => 62,                  
                  'service release 3' => 63,                  
                  'service release 4' => 64,                  
                  'service release 5' => 65,
                  'service release 6' => 66,
                  'service release 7' => 67,                  
                  'service release 8' => 68,
                  'service release 9' => 69,
                  'service release 10' => 70,
                  'service release 11' => 71,                  
                  'service release 12' => 72,                  
                  'service release' => 60,                   
                  'release 1' => 31,
                  'release 2' => 32,                  
                  'release 3' => 33,                  
                  'release 4' => 34,                  
                  'release 5' => 35,
                  'release 6' => 36,
                  'release 7' => 37,                  
                  'release 8' => 38,                  
                  'release 9' => 39,
                  'release 10' => 40,                                                     
                  'rc 1' => 21,                  
                  'rc 2' => 22,                  
                  'rc 3' => 23,
                  'rc 4' => 24,                                                      
                  'rc 5' => 25,
                  'rc 6' => 26, 
                  'rc 7' => 27,
                  'rc 8' => 28,                                                      
                  'rc 9' => 29,
                  'rc 10' => 30,                                   
                  'rc' => 21, 
                  'official release' => 40 ,
                  'final version' => 40 ,
                  'stable version' => 40 ,                                     
                  'public release' => 41 ,                                                                                                           
                  'hotfix 1' => 51 ,
                  'hotfix 2' => 52,                  
                  'hotfix 3' => 53,                  
                  'hotfix 4' => 54,                  
                  'hotfix 5' => 55,
                  'hotfix 6' => 56,
                  'hotfix 7' => 57,                  
                  'hotfix 8' => 58,
                  'hotfix 9' => 59,
                  'hotfix 10' => 59.1,
                  'hotfix 11' => 59.2,                  
                  'hotfix 12' => 59.3,                  
                  'hotfix' => 50, 
                  'update patch' => 60, 
                  'updated patch' => 60,
                  'update pack' => 60, 
                  'updated pack' => 60,                                                                                                                          
                  'service pack 1' => 61,
                  'service pack 2' => 62,                  
                  'service pack 3' => 63,                  
                  'service pack 4' => 64,                  
                  'service pack 5' => 65,
                  'service pack 6' => 66,
                  'service pack 7' => 67,                  
                  'service pack 8' => 68,
                  'service pack 9' => 69,
                  'service pack 10' => 70,
                  'service pack 11' => 71,                  
                  'service pack 12' => 72,                  
                  'service pack' => 60,                 
                  'sp 1' => 61,
                  'sp 2' => 62,                  
                  'sp 3' => 63,                  
                  'sp 4' => 64,                  
                  'sp 5' => 65,
                  'sp 6' => 66,
                  'sp 7' => 67,                  
                  'sp 8' => 68,
                  'sp 9' => 69,
                  'sp 10' => 70,
                  'sp 11' => 71,                  
                  'sp 12' => 72,                                                                                         
                  'sp' => 60

);

$subfix_builds = array ('build');
 
    do {
        $stitle_temp=$stitle;
       
         if ( $stitle_temp==$stitle) {         
               foreach ($subfix_builds as $subfix_build)  {
                 $subfix_length=mb_strlen($subfix_build );
                   if (mb_strtolower(mb_substr($stitle, -$subfix_length,$subfix_length))===$subfix_build)
                   {
                       $stitle = substr_replace ($stitle,'', -$subfix_length);
                       $s_build = $s_version_l;
                       $stitle = trim($stitle); 
                   }         
               }
        }       
          
        if ( $stitle_temp==$stitle) {
                foreach ($subfix_versions as $subfix => $subfix_version_number)  {
                 $subfix_length=mb_strlen($subfix);
                   if (mb_strtolower(mb_substr($stitle, -$subfix_length-1,$subfix_length+1))===" $subfix" | mb_strtolower(mb_substr($stitle, -$subfix_length-1,$subfix_length+1))==="-$subfix" | mb_strtolower(mb_substr($stitle, -$subfix_length-2,$subfix_length+2))==="($subfix)")                   
                   {
                       $stitle = substr_replace ($stitle,'', -$subfix_length);
                       $stitle = trim($stitle);
                       $s_release = $subfix;
                       $s_version = $s_version.' '.$subfix; 
                       $s_version_n = $s_version_n.' '.$subfix_version_number;               
                   }         
               }    
            
        }
               
        if ( $stitle_temp==$stitle) {         
               foreach ($subfixs as $subfix)  {
                 $subfix_length=mb_strlen($subfix );
                   if (mb_strtolower(mb_substr($stitle, -$subfix_length-1,$subfix_length+1))===" $subfix" | mb_strtolower(mb_substr($stitle, -$subfix_length-1,$subfix_length+1))==="-$subfix" | mb_strtolower(mb_substr($stitle, -$subfix_length-2,$subfix_length+2))==="($subfix)")
                   {
                       $stitle = substr_replace ($stitle,'', -$subfix_length);
                       $s_edition = $subfix;
                       $stitle = trim($stitle);
                   }         
               }
        }
              
          if ( $stitle_temp==$stitle) {     
            $stitle = preg_replace('/(.*)(\(.*\))$/',"$1",$stitle);                                                 
        } 
                    
         if ( $stitle_temp==$stitle) {   
               $stitle_array = SoftwareTitle1 ($stitle);       
               $stitle = $stitle_array[0];       
               $s_version = $stitle_array[1].' '.$s_version;                   
               $s_year = $stitle_array[2]; 
               $s_version_n = $stitle_array[3].' '.$s_version_n;
               $s_version_l = $stitle_array[4];                                  
        } 
        

        
         if ( $stitle_temp==$stitle) {                    
             $stitle=trim(rtrim($stitle,"-=\$\£\¥\₩\€\元\₭₫\б,/\?;[]\\`~%^&{}|:<>"));   
         }
                
    } while ($stitle_temp<>$stitle);                

    $stitle=trim(rtrim($stitle,"-=\$\£\¥\₩\€\元\₭₫\б,/\?;[]\\`~%^&{}|:<>"));
    
    $s_version_n=trim($s_version_n," -+=\$\£\¥\₩\€\元\₭₫\б,./\?;'[]\\`~@#%^&*(){}|:\"<>");
    $s_version=trim($s_version," -+=\$\£\¥\₩\€\元\₭₫\б,./\?;'[]\\`~@#%^&*(){}|:\"<>");
    $s_build=trim($s_build," -+=\$\£\¥\₩\€\元\₭₫\б,./\?;'[]\\`~@#%^&*(){}|:\"<>");   
        
// calculating vindex 
  $s_version_n = trim ($s_version_n);
  $s_version_n = str_replace(',','.',$s_version_n);
  $version_length=strlen($s_version_n);
  $first_dot=0;
  
     if ($version_length >0) {
          $v_nummber_part='';
          $v_array=array();        
          for ($i=0;$i<$version_length;$i++) {
                if(is_numeric($s_version_n[$i]) | ($s_version_n[$i]=='.' & $first_dot==0)) {                	
                    $v_nummber_part.=$s_version_n[$i]; 
                    if ($s_version_n[$i]=='.') $first_dot=1;
                         
                } else if ($v_nummber_part <>''){                	
                    $v_array[]=$v_nummber_part;
                    $v_nummber_part='';       
                }                	
          }
          
            if ($v_nummber_part <>''){
                    $v_array[]=$v_nummber_part;
                    $v_nummber_part='';       
            }
                 
 //    print_r($v_array);
     $v_count=count($v_array);
     if ($v_count >0) {
         $divident=1;
         
         // fix if rist part is high number many chars
         $v_array1=$v_array;
         $v_array1[0]=0;
 /*       
         if (max($v_array1) <1000) {
       $divident_c = 1000;} else {$divident_c =pow(10,(strlen(ceil(max($v_array)))));}
*/

        $divident_c = 1000;


      
         if ($first_dot==1) {            
             $divident=$divident*(pow(10,(strlen($v_array[0]) - strpos($v_array[0],'.'))));
         }
 //        echo $divident;
         
         if ($v_count==1 & $s_release<>'') {
         $v_array[0]=$v_array[0]/$divident_c;
             
         }
         $s_vindex=$v_array[0];
         
         if ($v_count >1 ) {
             for ($i=1;$i<$v_count;$i++) {
                 
          if (strlen($v_array[$i]) <4) 
          {     	$divident_c = 1000;        } 
          else { $divident_c =pow(10,(strlen($v_array[$i] ))); }
                         
             $divident=$divident*$divident_c;
             $s_vindex=$s_vindex+($v_array[$i]/$divident);
             }
         }
     }
     } 
      
    return array($stitle,$s_version,$s_year,$s_edition,$s_release,$s_version_n,$s_build,$s_vindex);            
}

function what_site_domain($id,$catid,$id2,$id_hec) {
global $database;    
    $sitedomain='wareseeker.com';
    if ($id2>0 ) {
        $query = "select CategoryID from software_affiliate.a_dir_links_archiveall where id = $id2";
        $database->setQuery( $query );
        $catid=$database->LoadResult();
    } else if ($id_hec<>0) {
        $query = "select CategoryID from software_affiliate.a_dir_links_archiveall where id_hec = '$id_hec'";
        $database->setQuery( $query );
        $catid=$database->LoadResult();         
    } else if ($catid ==0) {
       $catid=round($id/1000);       
    }

    if ($catid >0) {
            if ($catid>0 && $catid<10000) {
            $sitedomain='wareseeker.com';
        } else if ($catid>=10000 && $catid<20000) {
            $sitedomain='mac.wareseeker.com';       
        } else if ($catid>=20000 && $catid<30000) {
            $sitedomain='pda.wareseeker.com';
        } else if ($catid>=30000 && $catid<40000) {
            $sitedomain='driver.wareseeker.com';
        } else if ($catid>=40000 && $catid<50000) {
            $sitedomain='linux.wareseeker.com';
        } else if ($catid>=50000 && $catid<60000) {
            $sitedomain='script.wareseeker.com';
        } 
    }
    return $sitedomain;       
} 

function wsid_to_hex($id)
{    
    $part1 = dechex($id + 39762);
    $part2 = substr(dechex($id + 2124), -3); //last three characters
    return $part1.$part2;
}

function creat_url_alias($title)
{
    //  variable for creating alias
/**
 * UTF-8 lookup table for lower case accented letters
 *
 * This lookuptable defines replacements for accented characters from the ASCII-7
 * range. This are lower case letters only.
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 * @see    utf8_deaccent()
 */
if(empty($UTF8_LOWER_ACCENTS)) {
$UTF8_LOWER_ACCENTS = array(
  'à' => 'a', 'ô' => 'o', 'ď' => 'd', 'ḟ' => 'f', 'ë' => 'e', 'š' => 's', 'ơ' => 'o',
  'ß' => 'ss', 'ă' => 'a', 'ř' => 'r', 'ț' => 't', 'ň' => 'n', 'ā' => 'a', 'ķ' => 'k',
  'ŝ' => 's', 'ỳ' => 'y', 'ņ' => 'n', 'ĺ' => 'l', 'ħ' => 'h', 'ṗ' => 'p', 'ó' => 'o',
  'ú' => 'u', 'ě' => 'e', 'é' => 'e', 'ç' => 'c', 'ẁ' => 'w', 'ċ' => 'c', 'õ' => 'o',
  'ṡ' => 's', 'ø' => 'o', 'ģ' => 'g', 'ŧ' => 't', 'ș' => 's', 'ė' => 'e', 'ĉ' => 'c',
  'ś' => 's', 'î' => 'i', 'ű' => 'u', 'ć' => 'c', 'ę' => 'e', 'ŵ' => 'w', 'ṫ' => 't',
  'ū' => 'u', 'č' => 'c', 'ö' => 'oe', 'è' => 'e', 'ŷ' => 'y', 'ą' => 'a', 'ł' => 'l',
  'ų' => 'u', 'ů' => 'u', 'ş' => 's', 'ğ' => 'g', 'ļ' => 'l', 'ƒ' => 'f', 'ž' => 'z',
  'ẃ' => 'w', 'ḃ' => 'b', 'å' => 'a', 'ì' => 'i', 'ï' => 'i', 'ḋ' => 'd', 'ť' => 't',
  'ŗ' => 'r', 'ä' => 'ae', 'í' => 'i', 'ŕ' => 'r', 'ê' => 'e', 'ü' => 'ue', 'ò' => 'o',
  'ē' => 'e', 'ñ' => 'n', 'ń' => 'n', 'ĥ' => 'h', 'ĝ' => 'g', 'đ' => 'd', 'ĵ' => 'j',
  'ÿ' => 'y', 'ũ' => 'u', 'ŭ' => 'u', 'ư' => 'u', 'ţ' => 't', 'ý' => 'y', 'ő' => 'o',
  'â' => 'a', 'ľ' => 'l', 'ẅ' => 'w', 'ż' => 'z', 'ī' => 'i', 'ã' => 'a', 'ġ' => 'g',
  'ṁ' => 'm', 'ō' => 'o', 'ĩ' => 'i', 'ù' => 'u', 'į' => 'i', 'ź' => 'z', 'á' => 'a',
  'û' => 'u', 'þ' => 'th', 'ð' => 'dh', 'æ' => 'ae', 'µ' => 'u', 'ĕ' => 'e',
); 
}

/**
 * Romanization lookup table
 *
 * This lookup tables provides a way to transform strings written in a language
 * different from the ones based upon latin letters into plain ASCII.
 *
 * Please note: this is not a scientific transliteration table. It only works
 * oneway from nonlatin to ASCII and it works by simple character replacement
 * only. Specialities of each language are not supported.
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 * @author Vitaly Blokhin <vitinfo@vitn.com>
 * @link   http://www.uconv.com/translit.htm
 * @author Bisqwit <bisqwit@iki.fi>
 * @link   http://kanjidict.stc.cx/hiragana.php?src=2
 * @link   http://www.translatum.gr/converter/greek-transliteration.htm
 * @link   http://en.wikipedia.org/wiki/Royal_Thai_General_System_of_Transcription
 * @link   http://www.btranslations.com/resources/romanization/korean.asp
 * @author Arthit Suriyawongkul <arthit@gmail.com>
 * @author Denis Scheither <amorphis@uni-bremen.de>
 */

if(empty($UTF8_ROMANIZATION)) {
    $UTF8_ROMANIZATION = array(
  // scandinavian - differs from what we do in deaccent
  'å'=>'a','Å'=>'A','ä'=>'a','Ä'=>'A','ö'=>'o','Ö'=>'O', 

  //russian cyrillic
  'а'=>'a','А'=>'A','б'=>'b','Б'=>'B','в'=>'v','В'=>'V','г'=>'g','Г'=>'G',
  'д'=>'d','Д'=>'D','е'=>'e','Е'=>'E','ё'=>'jo','Ё'=>'Jo','ж'=>'zh','Ж'=>'Zh',
  'з'=>'z','З'=>'Z','и'=>'i','И'=>'I','й'=>'j','Й'=>'J','к'=>'k','К'=>'K',
  'л'=>'l','Л'=>'L','м'=>'m','М'=>'M','н'=>'n','Н'=>'N','о'=>'o','О'=>'O',
  'п'=>'p','П'=>'P','р'=>'r','Р'=>'R','с'=>'s','С'=>'S','т'=>'t','Т'=>'T',
  'у'=>'u','У'=>'U','ф'=>'f','Ф'=>'F','х'=>'x','Х'=>'X','ц'=>'c','Ц'=>'C',
  'ч'=>'ch','Ч'=>'Ch','ш'=>'sh','Ш'=>'Sh','щ'=>'sch','Щ'=>'Sch','ъ'=>'',
  'Ъ'=>'','ы'=>'y','Ы'=>'Y','ь'=>'','Ь'=>'','э'=>'eh','Э'=>'Eh','ю'=>'ju',
  'Ю'=>'Ju','я'=>'ja','Я'=>'Ja',
  // Ukrainian cyrillic
  'Ґ'=>'Gh','ґ'=>'gh','Є'=>'Je','є'=>'je','І'=>'I','і'=>'i','Ї'=>'Ji','ї'=>'ji',
  // Georgian
  'ა'=>'a','ბ'=>'b','გ'=>'g','დ'=>'d','ე'=>'e','ვ'=>'v','ზ'=>'z','თ'=>'th',
  'ი'=>'i','კ'=>'p','ლ'=>'l','მ'=>'m','ნ'=>'n','ო'=>'o','პ'=>'p','ჟ'=>'zh',
  'რ'=>'r','ს'=>'s','ტ'=>'t','უ'=>'u','ფ'=>'ph','ქ'=>'kh','ღ'=>'gh','ყ'=>'q',
  'შ'=>'sh','ჩ'=>'ch','ც'=>'c','ძ'=>'dh','წ'=>'w','ჭ'=>'j','ხ'=>'x','ჯ'=>'jh',
  'ჰ'=>'xh',
  //Sanskrit
  'अ'=>'a','आ'=>'ah','इ'=>'i','ई'=>'ih','उ'=>'u','ऊ'=>'uh','ऋ'=>'ry',
  'ॠ'=>'ryh','ऌ'=>'ly','ॡ'=>'lyh','ए'=>'e','ऐ'=>'ay','ओ'=>'o','औ'=>'aw',
  'अं'=>'amh','अः'=>'aq','क'=>'k','ख'=>'kh','ग'=>'g','घ'=>'gh','ङ'=>'nh',
  'च'=>'c','छ'=>'ch','ज'=>'j','झ'=>'jh','ञ'=>'ny','ट'=>'tq','ठ'=>'tqh',
  'ड'=>'dq','ढ'=>'dqh','ण'=>'nq','त'=>'t','थ'=>'th','द'=>'d','ध'=>'dh',
  'न'=>'n','प'=>'p','फ'=>'ph','ब'=>'b','भ'=>'bh','म'=>'m','य'=>'z','र'=>'r',
  'ल'=>'l','व'=>'v','श'=>'sh','ष'=>'sqh','स'=>'s','ह'=>'x',
  //Hebrew
  'א'=>'a', 'ב'=>'b','ג'=>'g','ד'=>'d','ה'=>'h','ו'=>'v','ז'=>'z','ח'=>'kh','ט'=>'th',
  'י'=>'y','ך'=>'h','כ'=>'k','ל'=>'l','ם'=>'m','מ'=>'m','ן'=>'n','נ'=>'n',
  'ס'=>'s','ע'=>'ah','ף'=>'f','פ'=>'p','ץ'=>'c','צ'=>'c','ק'=>'q','ר'=>'r',
  'ש'=>'sh','ת'=>'t',
  //Arabic
  'ا'=>'a','ب'=>'b','ت'=>'t','ث'=>'th','ج'=>'g','ح'=>'xh','خ'=>'x','د'=>'d',
  'ذ'=>'dh','ر'=>'r','ز'=>'z','س'=>'s','ش'=>'sh','ص'=>'s\'','ض'=>'d\'',
  'ط'=>'t\'','ظ'=>'z\'','ع'=>'y','غ'=>'gh','ف'=>'f','ق'=>'q','ك'=>'k',
  'ل'=>'l','م'=>'m','ن'=>'n','ه'=>'x\'','و'=>'u','ي'=>'i',

  // Japanese characters  (last update: 2008-05-09)
  
  // Japanese hiragana

  // 3 character syllables, っ doubles the consonant after
  'っちゃ'=>'ccha','っちぇ'=>'cche','っちょ'=>'ccho','っちゅ'=>'cchu',
  'っびゃ'=>'bbya','っびぇ'=>'bbye','っびぃ'=>'bbyi','っびょ'=>'bbyo','っびゅ'=>'bbyu',
  'っぴゃ'=>'ppya','っぴぇ'=>'ppye','っぴぃ'=>'ppyi','っぴょ'=>'ppyo','っぴゅ'=>'ppyu',
  'っちゃ'=>'ccha','っちぇ'=>'cche','っち'=>'cchi','っちょ'=>'ccho','っちゅ'=>'cchu',
  // 'っひゃ'=>'hya','っひぇ'=>'hye','っひぃ'=>'hyi','っひょ'=>'hyo','っひゅ'=>'hyu',
  'っきゃ'=>'kkya','っきぇ'=>'kkye','っきぃ'=>'kkyi','っきょ'=>'kkyo','っきゅ'=>'kkyu',
  'っぎゃ'=>'ggya','っぎぇ'=>'ggye','っぎぃ'=>'ggyi','っぎょ'=>'ggyo','っぎゅ'=>'ggyu',
  'っみゃ'=>'mmya','っみぇ'=>'mmye','っみぃ'=>'mmyi','っみょ'=>'mmyo','っみゅ'=>'mmyu',
  'っにゃ'=>'nnya','っにぇ'=>'nnye','っにぃ'=>'nnyi','っにょ'=>'nnyo','っにゅ'=>'nnyu',
  'っりゃ'=>'rrya','っりぇ'=>'rrye','っりぃ'=>'rryi','っりょ'=>'rryo','っりゅ'=>'rryu',
  'っしゃ'=>'ssha','っしぇ'=>'sshe','っし'=>'sshi','っしょ'=>'ssho','っしゅ'=>'sshu',

  // seperate hiragana 'n' ('n' + 'i' != 'ni', normally we would write "kon'nichi wa" but the apostrophe would be converted to _ anyway)
  'んあ'=>'n_a','んえ'=>'n_e','んい'=>'n_i','んお'=>'n_o','んう'=>'n_u',
  'んや'=>'n_ya','んよ'=>'n_yo','んゆ'=>'n_yu',

   // 2 character syllables - normal
  'ふぁ'=>'fa','ふぇ'=>'fe','ふぃ'=>'fi','ふぉ'=>'fo',
  'ちゃ'=>'cha','ちぇ'=>'che','ち'=>'chi','ちょ'=>'cho','ちゅ'=>'chu',
  'ひゃ'=>'hya','ひぇ'=>'hye','ひぃ'=>'hyi','ひょ'=>'hyo','ひゅ'=>'hyu',
  'びゃ'=>'bya','びぇ'=>'bye','びぃ'=>'byi','びょ'=>'byo','びゅ'=>'byu',
  'ぴゃ'=>'pya','ぴぇ'=>'pye','ぴぃ'=>'pyi','ぴょ'=>'pyo','ぴゅ'=>'pyu',
  'きゃ'=>'kya','きぇ'=>'kye','きぃ'=>'kyi','きょ'=>'kyo','きゅ'=>'kyu',
  'ぎゃ'=>'gya','ぎぇ'=>'gye','ぎぃ'=>'gyi','ぎょ'=>'gyo','ぎゅ'=>'gyu',
  'みゃ'=>'mya','みぇ'=>'mye','みぃ'=>'myi','みょ'=>'myo','みゅ'=>'myu',
  'にゃ'=>'nya','にぇ'=>'nye','にぃ'=>'nyi','にょ'=>'nyo','にゅ'=>'nyu',
  'りゃ'=>'rya','りぇ'=>'rye','りぃ'=>'ryi','りょ'=>'ryo','りゅ'=>'ryu',
  'しゃ'=>'sha','しぇ'=>'she','し'=>'shi','しょ'=>'sho','しゅ'=>'shu',
  'じゃ'=>'ja','じぇ'=>'je','じょ'=>'jo','じゅ'=>'ju',
  'うぇ'=>'we','うぃ'=>'wi',
  'いぇ'=>'ye',

  // 2 character syllables, っ doubles the consonant after
  'っば'=>'bba','っべ'=>'bbe','っび'=>'bbi','っぼ'=>'bbo','っぶ'=>'bbu',
  'っぱ'=>'ppa','っぺ'=>'ppe','っぴ'=>'ppi','っぽ'=>'ppo','っぷ'=>'ppu',
  'った'=>'tta','って'=>'tte','っち'=>'cchi','っと'=>'tto','っつ'=>'ttsu',
  'っだ'=>'dda','っで'=>'dde','っぢ'=>'ddi','っど'=>'ddo','っづ'=>'ddu',
  'っが'=>'gga','っげ'=>'gge','っぎ'=>'ggi','っご'=>'ggo','っぐ'=>'ggu',
  'っか'=>'kka','っけ'=>'kke','っき'=>'kki','っこ'=>'kko','っく'=>'kku',
  'っま'=>'mma','っめ'=>'mme','っみ'=>'mmi','っも'=>'mmo','っむ'=>'mmu',
  'っな'=>'nna','っね'=>'nne','っに'=>'nni','っの'=>'nno','っぬ'=>'nnu',
  'っら'=>'rra','っれ'=>'rre','っり'=>'rri','っろ'=>'rro','っる'=>'rru',
  'っさ'=>'ssa','っせ'=>'sse','っし'=>'sshi','っそ'=>'sso','っす'=>'ssu',
  'っざ'=>'zza','っぜ'=>'zze','っじ'=>'jji','っぞ'=>'zzo','っず'=>'zzu',
  
  // 1 character syllabels
  'あ'=>'a','え'=>'e','い'=>'i','お'=>'o','う'=>'u','ん'=>'n',
  'は'=>'ha','へ'=>'he','ひ'=>'hi','ほ'=>'ho','ふ'=>'fu',
  'ば'=>'ba','べ'=>'be','び'=>'bi','ぼ'=>'bo','ぶ'=>'bu',
  'ぱ'=>'pa','ぺ'=>'pe','ぴ'=>'pi','ぽ'=>'po','ぷ'=>'pu',
  'た'=>'ta','て'=>'te','ち'=>'chi','と'=>'to','つ'=>'tsu',
  'だ'=>'da','で'=>'de','ぢ'=>'di','ど'=>'do','づ'=>'du',
  'が'=>'ga','げ'=>'ge','ぎ'=>'gi','ご'=>'go','ぐ'=>'gu',
  'か'=>'ka','け'=>'ke','き'=>'ki','こ'=>'ko','く'=>'ku',
  'ま'=>'ma','め'=>'me','み'=>'mi','も'=>'mo','む'=>'mu',
  'な'=>'na','ね'=>'ne','に'=>'ni','の'=>'no','ぬ'=>'nu',
  'ら'=>'ra','れ'=>'re','り'=>'ri','ろ'=>'ro','る'=>'ru',
  'さ'=>'sa','せ'=>'se','し'=>'shi','そ'=>'so','す'=>'su',
  'わ'=>'wa','を'=>'wo',
  'ざ'=>'za','ぜ'=>'ze','じ'=>'ji','ぞ'=>'zo','ず'=>'zu',
  'や'=>'ya','よ'=>'yo','ゆ'=>'yu',
  // old characters
  'ゑ'=>'we','ゐ'=>'wi',

  //  convert what's left (probably only kicks in when something's missing above)
  // 'ぁ'=>'a','ぇ'=>'e','ぃ'=>'i','ぉ'=>'o','ぅ'=>'u',
  // 'ゃ'=>'ya','ょ'=>'yo','ゅ'=>'yu',

  // never seen one of those (disabled for the moment)
  // 'ヴぁ'=>'va','ヴぇ'=>'ve','ヴぃ'=>'vi','ヴぉ'=>'vo','ヴ'=>'vu',
  // 'でゃ'=>'dha','でぇ'=>'dhe','でぃ'=>'dhi','でょ'=>'dho','でゅ'=>'dhu',
  // 'どぁ'=>'dwa','どぇ'=>'dwe','どぃ'=>'dwi','どぉ'=>'dwo','どぅ'=>'dwu',
  // 'ぢゃ'=>'dya','ぢぇ'=>'dye','ぢぃ'=>'dyi','ぢょ'=>'dyo','ぢゅ'=>'dyu',
  // 'ふぁ'=>'fwa','ふぇ'=>'fwe','ふぃ'=>'fwi','ふぉ'=>'fwo','ふぅ'=>'fwu',
  // 'ふゃ'=>'fya','ふぇ'=>'fye','ふぃ'=>'fyi','ふょ'=>'fyo','ふゅ'=>'fyu',
  // 'すぁ'=>'swa','すぇ'=>'swe','すぃ'=>'swi','すぉ'=>'swo','すぅ'=>'swu',
  // 'てゃ'=>'tha','てぇ'=>'the','てぃ'=>'thi','てょ'=>'tho','てゅ'=>'thu',
  // 'つゃ'=>'tsa','つぇ'=>'tse','つぃ'=>'tsi','つょ'=>'tso','つ'=>'tsu',
  // 'とぁ'=>'twa','とぇ'=>'twe','とぃ'=>'twi','とぉ'=>'two','とぅ'=>'twu',
  // 'ヴゃ'=>'vya','ヴぇ'=>'vye','ヴぃ'=>'vyi','ヴょ'=>'vyo','ヴゅ'=>'vyu',
  // 'うぁ'=>'wha','うぇ'=>'whe','うぃ'=>'whi','うぉ'=>'who','うぅ'=>'whu',
  // 'じゃ'=>'zha','じぇ'=>'zhe','じぃ'=>'zhi','じょ'=>'zho','じゅ'=>'zhu',
  // 'じゃ'=>'zya','じぇ'=>'zye','じぃ'=>'zyi','じょ'=>'zyo','じゅ'=>'zyu',

  // 'spare' characters from other romanization systems
  // 'だ'=>'da','で'=>'de','ぢ'=>'di','ど'=>'do','づ'=>'du',
  // 'ら'=>'la','れ'=>'le','り'=>'li','ろ'=>'lo','る'=>'lu',
  // 'さ'=>'sa','せ'=>'se','し'=>'si','そ'=>'so','す'=>'su',
  // 'ちゃ'=>'cya','ちぇ'=>'cye','ちぃ'=>'cyi','ちょ'=>'cyo','ちゅ'=>'cyu',
  //'じゃ'=>'jya','じぇ'=>'jye','じぃ'=>'jyi','じょ'=>'jyo','じゅ'=>'jyu',
  //'りゃ'=>'lya','りぇ'=>'lye','りぃ'=>'lyi','りょ'=>'lyo','りゅ'=>'lyu',
  //'しゃ'=>'sya','しぇ'=>'sye','しぃ'=>'syi','しょ'=>'syo','しゅ'=>'syu',
  //'ちゃ'=>'tya','ちぇ'=>'tye','ちぃ'=>'tyi','ちょ'=>'tyo','ちゅ'=>'tyu',
  //'し'=>'ci',,い'=>'yi','ぢ'=>'dzi',
  //'っじゃ'=>'jja','っじぇ'=>'jje','っじ'=>'jji','っじょ'=>'jjo','っじゅ'=>'jju',


  // Japanese katakana

  // 4 character syllables: ッ doubles the consonant after, ー doubles the vowel before (usualy written with macron, but we don't want that in our URLs)
  'ッビャー'=>'bbyaa','ッビェー'=>'bbyee','ッビィー'=>'bbyii','ッビョー'=>'bbyoo','ッビュー'=>'bbyuu',
  'ッピャー'=>'ppyaa','ッピェー'=>'ppyee','ッピィー'=>'ppyii','ッピョー'=>'ppyoo','ッピュー'=>'ppyuu',
  'ッキャー'=>'kkyaa','ッキェー'=>'kkyee','ッキィー'=>'kkyii','ッキョー'=>'kkyoo','ッキュー'=>'kkyuu',
  'ッギャー'=>'ggyaa','ッギェー'=>'ggyee','ッギィー'=>'ggyii','ッギョー'=>'ggyoo','ッギュー'=>'ggyuu',
  'ッミャー'=>'mmyaa','ッミェー'=>'mmyee','ッミィー'=>'mmyii','ッミョー'=>'mmyoo','ッミュー'=>'mmyuu',
  'ッニャー'=>'nnyaa','ッニェー'=>'nnyee','ッニィー'=>'nnyii','ッニョー'=>'nnyoo','ッニュー'=>'nnyuu',
  'ッリャー'=>'rryaa','ッリェー'=>'rryee','ッリィー'=>'rryii','ッリョー'=>'rryoo','ッリュー'=>'rryuu',
  'ッシャー'=>'sshaa','ッシェー'=>'sshee','ッシー'=>'sshii','ッショー'=>'sshoo','ッシュー'=>'sshuu',
  'ッチャー'=>'cchaa','ッチェー'=>'cchee','ッチー'=>'cchii','ッチョー'=>'cchoo','ッチュー'=>'cchuu',
  'ッティー'=>'ttii',
  'ッヂィー'=>'ddii',
  
  // 3 character syllables - doubled vowels
  'ファー'=>'faa','フェー'=>'fee','フィー'=>'fii','フォー'=>'foo',
  'フャー'=>'fyaa','フェー'=>'fyee','フィー'=>'fyii','フョー'=>'fyoo','フュー'=>'fyuu',
  'ヒャー'=>'hyaa','ヒェー'=>'hyee','ヒィー'=>'hyii','ヒョー'=>'hyoo','ヒュー'=>'hyuu',
  'ビャー'=>'byaa','ビェー'=>'byee','ビィー'=>'byii','ビョー'=>'byoo','ビュー'=>'byuu',
  'ピャー'=>'pyaa','ピェー'=>'pyee','ピィー'=>'pyii','ピョー'=>'pyoo','ピュー'=>'pyuu',
  'キャー'=>'kyaa','キェー'=>'kyee','キィー'=>'kyii','キョー'=>'kyoo','キュー'=>'kyuu',
  'ギャー'=>'gyaa','ギェー'=>'gyee','ギィー'=>'gyii','ギョー'=>'gyoo','ギュー'=>'gyuu',
  'ミャー'=>'myaa','ミェー'=>'myee','ミィー'=>'myii','ミョー'=>'myoo','ミュー'=>'myuu',
  'ニャー'=>'nyaa','ニェー'=>'nyee','ニィー'=>'nyii','ニョー'=>'nyoo','ニュー'=>'nyuu',
  'リャー'=>'ryaa','リェー'=>'ryee','リィー'=>'ryii','リョー'=>'ryoo','リュー'=>'ryuu',
  'シャー'=>'shaa','シェー'=>'shee','シー'=>'shii','ショー'=>'shoo','シュー'=>'shuu',
  'ジャー'=>'jaa','ジェー'=>'jee','ジー'=>'jii','ジョー'=>'joo','ジュー'=>'juu',
  'スァー'=>'swaa','スェー'=>'swee','スィー'=>'swii','スォー'=>'swoo','スゥー'=>'swuu',
  'デァー'=>'daa','デェー'=>'dee','ディー'=>'dii','デォー'=>'doo','デゥー'=>'duu',
  'チャー'=>'chaa','チェー'=>'chee','チー'=>'chii','チョー'=>'choo','チュー'=>'chuu',
  'ヂャー'=>'dyaa','ヂェー'=>'dyee','ヂィー'=>'dyii','ヂョー'=>'dyoo','ヂュー'=>'dyuu',
  'ツャー'=>'tsaa','ツェー'=>'tsee','ツィー'=>'tsii','ツョー'=>'tsoo','ツー'=>'tsuu',
  'トァー'=>'twaa','トェー'=>'twee','トィー'=>'twii','トォー'=>'twoo','トゥー'=>'twuu',
  'ドァー'=>'dwaa','ドェー'=>'dwee','ドィー'=>'dwii','ドォー'=>'dwoo','ドゥー'=>'dwuu',
  'ウァー'=>'whaa','ウェー'=>'whee','ウィー'=>'whii','ウォー'=>'whoo','ウゥー'=>'whuu',
  'ヴャー'=>'vyaa','ヴェー'=>'vyee','ヴィー'=>'vyii','ヴョー'=>'vyoo','ヴュー'=>'vyuu',
  'ヴァー'=>'vaa','ヴェー'=>'vee','ヴィー'=>'vii','ヴォー'=>'voo','ヴー'=>'vuu',
  'ウェー'=>'wee','ウィー'=>'wii',
  'イェー'=>'yee',
  'ティー'=>'tii',
  'ヂィー'=>'dii',

  // 3 character syllables - doubled consonants
  'ッビャ'=>'bbya','ッビェ'=>'bbye','ッビィ'=>'bbyi','ッビョ'=>'bbyo','ッビュ'=>'bbyu',
  'ッピャ'=>'ppya','ッピェ'=>'ppye','ッピィ'=>'ppyi','ッピョ'=>'ppyo','ッピュ'=>'ppyu',
  'ッキャ'=>'kkya','ッキェ'=>'kkye','ッキィ'=>'kkyi','ッキョ'=>'kkyo','ッキュ'=>'kkyu',
  'ッギャ'=>'ggya','ッギェ'=>'ggye','ッギィ'=>'ggyi','ッギョ'=>'ggyo','ッギュ'=>'ggyu',
  'ッミャ'=>'mmya','ッミェ'=>'mmye','ッミィ'=>'mmyi','ッミョ'=>'mmyo','ッミュ'=>'mmyu',
  'ッニャ'=>'nnya','ッニェ'=>'nnye','ッニィ'=>'nnyi','ッニョ'=>'nnyo','ッニュ'=>'nnyu',
  'ッリャ'=>'rrya','ッリェ'=>'rrye','ッリィ'=>'rryi','ッリョ'=>'rryo','ッリュ'=>'rryu',
  'ッシャ'=>'ssha','ッシェ'=>'sshe','ッシ'=>'sshi','ッショ'=>'ssho','ッシュ'=>'sshu',
  'ッチャ'=>'ccha','ッチェ'=>'cche','ッチ'=>'cchi','ッチョ'=>'ccho','ッチュ'=>'cchu',
  'ッティ'=>'tti',
  'ッヂィ'=>'ddi',

  // 3 character syllables - doubled vowel and consonants
  'ッバー'=>'bbaa','ッベー'=>'bbee','ッビー'=>'bbii','ッボー'=>'bboo','ッブー'=>'bbuu',
  'ッパー'=>'ppaa','ッペー'=>'ppee','ッピー'=>'ppii','ッポー'=>'ppoo','ップー'=>'ppuu',
  'ッケー'=>'kkee','ッキー'=>'kkii','ッコー'=>'kkoo','ックー'=>'kkuu','ッカー'=>'kkaa',
  'ッガー'=>'ggaa','ッゲー'=>'ggee','ッギー'=>'ggii','ッゴー'=>'ggoo','ッグー'=>'gguu',
  'ッマー'=>'maa','ッメー'=>'mee','ッミー'=>'mii','ッモー'=>'moo','ッムー'=>'muu',
  'ッナー'=>'nnaa','ッネー'=>'nnee','ッニー'=>'nnii','ッノー'=>'nnoo','ッヌー'=>'nnuu',
  'ッラー'=>'rraa','ッレー'=>'rree','ッリー'=>'rrii','ッロー'=>'rroo','ッルー'=>'rruu',
  'ッサー'=>'ssaa','ッセー'=>'ssee','ッシー'=>'sshii','ッソー'=>'ssoo','ッスー'=>'ssuu',
  'ッザー'=>'zzaa','ッゼー'=>'zzee','ッジー'=>'jjii','ッゾー'=>'zzoo','ッズー'=>'zzuu',
  'ッター'=>'ttaa','ッテー'=>'ttee','ッチー'=>'chii','ットー'=>'ttoo','ッツー'=>'ttsuu',
  'ッダー'=>'ddaa','ッデー'=>'ddee','ッヂー'=>'ddii','ッドー'=>'ddoo','ッヅー'=>'dduu',

  // 2 character syllables - normal
  'ファ'=>'fa','フェ'=>'fe','フィ'=>'fi','フォ'=>'fo','フゥ'=>'fu',
  // 'フャ'=>'fya','フェ'=>'fye','フィ'=>'fyi','フョ'=>'fyo','フュ'=>'fyu',
  'フャ'=>'fa','フェ'=>'fe','フィ'=>'fi','フョ'=>'fo','フュ'=>'fu',
  'ヒャ'=>'hya','ヒェ'=>'hye','ヒィ'=>'hyi','ヒョ'=>'hyo','ヒュ'=>'hyu',
  'ビャ'=>'bya','ビェ'=>'bye','ビィ'=>'byi','ビョ'=>'byo','ビュ'=>'byu',
  'ピャ'=>'pya','ピェ'=>'pye','ピィ'=>'pyi','ピョ'=>'pyo','ピュ'=>'pyu',
  'キャ'=>'kya','キェ'=>'kye','キィ'=>'kyi','キョ'=>'kyo','キュ'=>'kyu',
  'ギャ'=>'gya','ギェ'=>'gye','ギィ'=>'gyi','ギョ'=>'gyo','ギュ'=>'gyu',
  'ミャ'=>'mya','ミェ'=>'mye','ミィ'=>'myi','ミョ'=>'myo','ミュ'=>'myu',
  'ニャ'=>'nya','ニェ'=>'nye','ニィ'=>'nyi','ニョ'=>'nyo','ニュ'=>'nyu',
  'リャ'=>'rya','リェ'=>'rye','リィ'=>'ryi','リョ'=>'ryo','リュ'=>'ryu',
  'シャ'=>'sha','シェ'=>'she','ショ'=>'sho','シュ'=>'shu',
  'ジャ'=>'ja','ジェ'=>'je','ジョ'=>'jo','ジュ'=>'ju',
  'スァ'=>'swa','スェ'=>'swe','スィ'=>'swi','スォ'=>'swo','スゥ'=>'swu',
  'デァ'=>'da','デェ'=>'de','ディ'=>'di','デォ'=>'do','デゥ'=>'du',
  'チャ'=>'cha','チェ'=>'che','チ'=>'chi','チョ'=>'cho','チュ'=>'chu',
  // 'ヂャ'=>'dya','ヂェ'=>'dye','ヂィ'=>'dyi','ヂョ'=>'dyo','ヂュ'=>'dyu',
  'ツャ'=>'tsa','ツェ'=>'tse','ツィ'=>'tsi','ツョ'=>'tso','ツ'=>'tsu',
  'トァ'=>'twa','トェ'=>'twe','トィ'=>'twi','トォ'=>'two','トゥ'=>'twu',
  'ドァ'=>'dwa','ドェ'=>'dwe','ドィ'=>'dwi','ドォ'=>'dwo','ドゥ'=>'dwu',
  'ウァ'=>'wha','ウェ'=>'whe','ウィ'=>'whi','ウォ'=>'who','ウゥ'=>'whu',
  'ヴャ'=>'vya','ヴェ'=>'vye','ヴィ'=>'vyi','ヴョ'=>'vyo','ヴュ'=>'vyu',
  'ヴァ'=>'va','ヴェ'=>'ve','ヴィ'=>'vi','ヴォ'=>'vo','ヴ'=>'vu',
  'ウェ'=>'we','ウィ'=>'wi',
  'イェ'=>'ye',
  'ティ'=>'ti',
  'ヂィ'=>'di',

  // 2 character syllables - doubled vocal
  'アー'=>'aa','エー'=>'ee','イー'=>'ii','オー'=>'oo','ウー'=>'uu',
  'ダー'=>'daa','デー'=>'dee','ヂー'=>'dii','ドー'=>'doo','ヅー'=>'duu',
  'ハー'=>'haa','ヘー'=>'hee','ヒー'=>'hii','ホー'=>'hoo','フー'=>'fuu',
  'バー'=>'baa','ベー'=>'bee','ビー'=>'bii','ボー'=>'boo','ブー'=>'buu',
  'パー'=>'paa','ペー'=>'pee','ピー'=>'pii','ポー'=>'poo','プー'=>'puu',
  'ケー'=>'kee','キー'=>'kii','コー'=>'koo','クー'=>'kuu','カー'=>'kaa',
  'ガー'=>'gaa','ゲー'=>'gee','ギー'=>'gii','ゴー'=>'goo','グー'=>'guu',
  'マー'=>'maa','メー'=>'mee','ミー'=>'mii','モー'=>'moo','ムー'=>'muu',
  'ナー'=>'naa','ネー'=>'nee','ニー'=>'nii','ノー'=>'noo','ヌー'=>'nuu',
  'ラー'=>'raa','レー'=>'ree','リー'=>'rii','ロー'=>'roo','ルー'=>'ruu',
  'サー'=>'saa','セー'=>'see','シー'=>'shii','ソー'=>'soo','スー'=>'suu',
  'ザー'=>'zaa','ゼー'=>'zee','ジー'=>'jii','ゾー'=>'zoo','ズー'=>'zuu',
  'ター'=>'taa','テー'=>'tee','チー'=>'chii','トー'=>'too','ツー'=>'tsuu',
  'ワー'=>'waa','ヲー'=>'woo',
  'ヤー'=>'yaa','ヨー'=>'yoo','ユー'=>'yuu',
  'ヵー'=>'kaa','ヶー'=>'kee',
  // old characters
  'ヱー'=>'wee','ヰー'=>'wii',

  // seperate katakana 'n'
  'ンア'=>'n_a','ンエ'=>'n_e','ンイ'=>'n_i','ンオ'=>'n_o','ンウ'=>'n_u',
  'ンヤ'=>'n_ya','ンヨ'=>'n_yo','ンユ'=>'n_yu',

  // 2 character syllables - doubled consonants
  'ッバ'=>'bba','ッベ'=>'bbe','ッビ'=>'bbi','ッボ'=>'bbo','ッブ'=>'bbu',
  'ッパ'=>'ppa','ッペ'=>'ppe','ッピ'=>'ppi','ッポ'=>'ppo','ップ'=>'ppu',
  'ッケ'=>'kke','ッキ'=>'kki','ッコ'=>'kko','ック'=>'kku','ッカ'=>'kka',
  'ッガ'=>'gga','ッゲ'=>'gge','ッギ'=>'ggi','ッゴ'=>'ggo','ッグ'=>'ggu',
  'ッマ'=>'ma','ッメ'=>'me','ッミ'=>'mi','ッモ'=>'mo','ッム'=>'mu',
  'ッナ'=>'nna','ッネ'=>'nne','ッニ'=>'nni','ッノ'=>'nno','ッヌ'=>'nnu',
  'ッラ'=>'rra','ッレ'=>'rre','ッリ'=>'rri','ッロ'=>'rro','ッル'=>'rru',
  'ッサ'=>'ssa','ッセ'=>'sse','ッシ'=>'sshi','ッソ'=>'sso','ッス'=>'ssu',
  'ッザ'=>'zza','ッゼ'=>'zze','ッジ'=>'jji','ッゾ'=>'zzo','ッズ'=>'zzu',
  'ッタ'=>'tta','ッテ'=>'tte','ッチ'=>'cchi','ット'=>'tto','ッツ'=>'ttsu',
  'ッダ'=>'dda','ッデ'=>'dde','ッヂ'=>'ddi','ッド'=>'ddo','ッヅ'=>'ddu',

  // 1 character syllables
  'ア'=>'a','エ'=>'e','イ'=>'i','オ'=>'o','ウ'=>'u','ン'=>'n',
  'ハ'=>'ha','ヘ'=>'he','ヒ'=>'hi','ホ'=>'ho','フ'=>'fu',
  'バ'=>'ba','ベ'=>'be','ビ'=>'bi','ボ'=>'bo','ブ'=>'bu',
  'パ'=>'pa','ペ'=>'pe','ピ'=>'pi','ポ'=>'po','プ'=>'pu',
  'ケ'=>'ke','キ'=>'ki','コ'=>'ko','ク'=>'ku','カ'=>'ka',
  'ガ'=>'ga','ゲ'=>'ge','ギ'=>'gi','ゴ'=>'go','グ'=>'gu',
  'マ'=>'ma','メ'=>'me','ミ'=>'mi','モ'=>'mo','ム'=>'mu',
  'ナ'=>'na','ネ'=>'ne','ニ'=>'ni','ノ'=>'no','ヌ'=>'nu',
  'ラ'=>'ra','レ'=>'re','リ'=>'ri','ロ'=>'ro','ル'=>'ru',
  'サ'=>'sa','セ'=>'se','シ'=>'shi','ソ'=>'so','ス'=>'su',
  'ザ'=>'za','ゼ'=>'ze','ジ'=>'ji','ゾ'=>'zo','ズ'=>'zu',
  'タ'=>'ta','テ'=>'te','チ'=>'chi','ト'=>'to','ツ'=>'tsu',
  'ダ'=>'da','デ'=>'de','ヂ'=>'di','ド'=>'do','ヅ'=>'du',
  'ワ'=>'wa','ヲ'=>'wo',
  'ヤ'=>'ya','ヨ'=>'yo','ユ'=>'yu',
  'ヵ'=>'ka','ヶ'=>'ke',
  // old characters
  'ヱ'=>'we','ヰ'=>'wi',

  //  convert what's left (probably only kicks in when something's missing above)
  'ァ'=>'a','ェ'=>'e','ィ'=>'i','ォ'=>'o','ゥ'=>'u',
  'ャ'=>'ya','ョ'=>'yo','ュ'=>'yu',

  // special characters
  '・'=>'_','、'=>'_',
  'ー'=>'_', // when used with hiragana (seldom), this character would not be converted otherwise

  // 'ラ'=>'la','レ'=>'le','リ'=>'li','ロ'=>'lo','ル'=>'lu',
  // 'チャ'=>'cya','チェ'=>'cye','チィ'=>'cyi','チョ'=>'cyo','チュ'=>'cyu',
  //'デャ'=>'dha','デェ'=>'dhe','ディ'=>'dhi','デョ'=>'dho','デュ'=>'dhu',
  // 'リャ'=>'lya','リェ'=>'lye','リィ'=>'lyi','リョ'=>'lyo','リュ'=>'lyu',
  // 'テャ'=>'tha','テェ'=>'the','ティ'=>'thi','テョ'=>'tho','テュ'=>'thu',
  //'ファ'=>'fwa','フェ'=>'fwe','フィ'=>'fwi','フォ'=>'fwo','フゥ'=>'fwu',
  //'チャ'=>'tya','チェ'=>'tye','チィ'=>'tyi','チョ'=>'tyo','チュ'=>'tyu',
  // 'ジャ'=>'jya','ジェ'=>'jye','ジィ'=>'jyi','ジョ'=>'jyo','ジュ'=>'jyu',
  // 'ジャ'=>'zha','ジェ'=>'zhe','ジィ'=>'zhi','ジョ'=>'zho','ジュ'=>'zhu',
  //'ジャ'=>'zya','ジェ'=>'zye','ジィ'=>'zyi','ジョ'=>'zyo','ジュ'=>'zyu',
  //'シャ'=>'sya','シェ'=>'sye','シィ'=>'syi','ショ'=>'syo','シュ'=>'syu',
  //'シ'=>'ci','フ'=>'hu',シ'=>'si','チ'=>'ti','ツ'=>'tu','イ'=>'yi','ヂ'=>'dzi',

  // "Greeklish"
  'Γ'=>'G','Δ'=>'E','Θ'=>'Th','Λ'=>'L','Ξ'=>'X','Π'=>'P','Σ'=>'S','Φ'=>'F','Ψ'=>'Ps',
  'γ'=>'g','δ'=>'e','θ'=>'th','λ'=>'l','ξ'=>'x','π'=>'p','σ'=>'s','φ'=>'f','ψ'=>'ps',

  // Thai
  'ก'=>'k','ข'=>'kh','ฃ'=>'kh','ค'=>'kh','ฅ'=>'kh','ฆ'=>'kh','ง'=>'ng','จ'=>'ch',
  'ฉ'=>'ch','ช'=>'ch','ซ'=>'s','ฌ'=>'ch','ญ'=>'y','ฎ'=>'d','ฏ'=>'t','ฐ'=>'th',
  'ฑ'=>'d','ฒ'=>'th','ณ'=>'n','ด'=>'d','ต'=>'t','ถ'=>'th','ท'=>'th','ธ'=>'th',
  'น'=>'n','บ'=>'b','ป'=>'p','ผ'=>'ph','ฝ'=>'f','พ'=>'ph','ฟ'=>'f','ภ'=>'ph',
  'ม'=>'m','ย'=>'y','ร'=>'r','ฤ'=>'rue','ฤๅ'=>'rue','ล'=>'l','ฦ'=>'lue',
  'ฦๅ'=>'lue','ว'=>'w','ศ'=>'s','ษ'=>'s','ส'=>'s','ห'=>'h','ฬ'=>'l','ฮ'=>'h',
  'ะ'=>'a','ั'=>'a','รร'=>'a','า'=>'a','ๅ'=>'a','ำ'=>'am','ํา'=>'am',
  'ิ'=>'i','ี'=>'i','ึ'=>'ue','ี'=>'ue','ุ'=>'u','ู'=>'u',
  'เ'=>'e','แ'=>'ae','โ'=>'o','อ'=>'o',
  'ียะ'=>'ia','ีย'=>'ia','ือะ'=>'uea','ือ'=>'uea','ัวะ'=>'ua','ัว'=>'ua',
  'ใ'=>'ai','ไ'=>'ai','ัย'=>'ai','าย'=>'ai','าว'=>'ao',
  'ุย'=>'ui','อย'=>'oi','ือย'=>'ueai','วย'=>'uai',
  'ิว'=>'io','็ว'=>'eo','ียว'=>'iao',
  '่'=>'','้'=>'','๊'=>'','๋'=>'','็'=>'',
  '์'=>'','๎'=>'','ํ'=>'','ฺ'=>'',
  'ๆ'=>'2','๏'=>'o','ฯ'=>'-','๚'=>'-','๛'=>'-', 
    '๐'=>'0','๑'=>'1','๒'=>'2','๓'=>'3','๔'=>'4',
  '๕'=>'5','๖'=>'6','๗'=>'7','๘'=>'8','๙'=>'9',

  // Korean
  'ㄱ'=>'k','ㅋ'=>'kh','ㄲ'=>'kk','ㄷ'=>'t','ㅌ'=>'th','ㄸ'=>'tt','ㅂ'=>'p',
  'ㅍ'=>'ph','ㅃ'=>'pp','ㅈ'=>'c','ㅊ'=>'ch','ㅉ'=>'cc','ㅅ'=>'s','ㅆ'=>'ss',
  'ㅎ'=>'h','ㅇ'=>'ng','ㄴ'=>'n','ㄹ'=>'l','ㅁ'=>'m', 'ㅏ'=>'a','ㅓ'=>'e','ㅗ'=>'o',
  'ㅜ'=>'wu','ㅡ'=>'u','ㅣ'=>'i','ㅐ'=>'ay','ㅔ'=>'ey','ㅚ'=>'oy','ㅘ'=>'wa','ㅝ'=>'we',
  'ㅟ'=>'wi','ㅙ'=>'way','ㅞ'=>'wey','ㅢ'=>'uy','ㅑ'=>'ya','ㅕ'=>'ye','ㅛ'=>'oy',
  'ㅠ'=>'yu','ㅒ'=>'yay','ㅖ'=>'yey',
);
}

    
$conversions1=Array(
    'À'=>'A','Á'=>'A','Â'=>'A','Ã'=>'A','Ä'=>'A','Å'=>'A',
    'Ç'=>'C',
    'È'=>'E','É'=>'E','Ê'=>'E','Ë'=>'E',
    'Ì'=>'I','Í'=>'I','Î'=>'I','Ï'=>'I',
    'Ð'=>'D',
    'Ñ'=>'N',
    'Ò'=>'O','Ó'=>'O','Ô'=>'O','Õ'=>'O','Ö'=>'O','Ø'=>'O',
    'Ù'=>'U','Ú'=>'U','Û'=>'U','Ü'=>'U',
    'Ý'=>'Y','Þ'=>'Y',
    'à'=>'a','á'=>'a','â'=>'a','ã'=>'a','ä'=>'a','å'=>'a',
    'ç'=>'c',
    'è'=>'e','é'=>'e','ê'=>'e','ë'=>'e',
    'ì'=>'i','í'=>'i','î'=>'i','ï'=>'i',
    'ð'=>'d',
    'ñ'=>'n',
    'ò'=>'o','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'o','ø'=>'o',
    'ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u',
    'ý'=>'y','þ'=>'y','ÿ'=>'y',
    );

$conversions2=Array(
    'Æ'=>'AE',
    'ß'=>'SS',
    'æ'=>'ae',
    );
    
$conversions3=Array(
    'A^'=>'A',
    'E^'=>'E',
    'I^'=>'I',
    'O^'=>'O',
    'U^'=>'U',
    'Y^'=>'Y',
    'a^'=>'a',
    'e^'=>'e',
    'i^'=>'i',
    'o^'=>'o',
    'u^'=>'u',
    'y^'=>'y'
    );
   
   
 $titlealias=strtr($title,array_merge($UTF8_ROMANIZATION,$UTF8_LOWER_ACCENTS,$conversions1,$conversions2,$conversions3));    
    $titlealias = mb_strtolower($titlealias,"UTF-8");    
    $titlealias = preg_replace('/[^a-z0-9.]+/i', '-', $titlealias);
    $titlealias = str_replace(' ', '-', $titlealias);
    $titlealias = preg_replace('/\-+/', '-', $titlealias);
    $titlealias = trim($titlealias, '-');
    $titlealias = trim($titlealias);
    return $titlealias;
}

//CND related function
function convert_file_size($unit,$from,$to)
{
    if ($unit && $from && $to) {
        $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');

        list($pos1) = array_keys($sizes,strtoupper($from));
        list($pos2) = array_keys($sizes,strtoupper($to));

        $up = $pos1 < $pos2 ? true : false;

        for($i = $pos1; $i != $pos2; ($up ? $i++ : $i--))
        {
            if($up) { $unit = $unit / 1024; }
            else { $unit = $unit * 1024; }
        }        
        
        return $unit;
    } else {
        return false;        
    }  

}

function take_file_size_unit ($string) {
    $file_size=trim($string,"\$\£\¥\₩\€\元\₭₫\бQWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm,./\?;'[]\\`~!@#%^&*(){}|:\"<>");
    $file_size=trim($file_size);
    $file_size=floatval($file_size);
    $string=str_replace('bytes','B',$string);    
    $unit =trim($string,"0123456789\$\£\¥\₩\€\元\₭₫\б1,./\?;'[]\\`~!@#%^&*(){}|:\"<>");    
    $unit=trim($unit); 
    if ( $file_size && $unit) {
        $unit_array = array(
          'K' => 'KB',
          'M' => 'MB', 
          'G' => 'GB',  
          'T' => 'TB', 
          'P' => 'PB',
          'k' => 'KB',
          'm' => 'MB', 
          'g' => 'GB',  
          't' => 'TB', 
          'p' => 'PB'                      
          );
        if (mb_strlen($unit)==1) {
           $unit=strtr( $unit,$unit_array);        
        }    
        return array($file_size,$unit);       
    } else {
       
    }

        

}

function convert_file_size_to_bytes ($string)
{
    if ($string) {
        $file_size_array = take_file_size_unit($string);
        IF ($file_size_array ) {
            $result= round(convert_file_size( $file_size_array[0], $file_size_array[1],'B'),0);  
            return $result;                         
        } ELSE {
        return false;             
        }     
    } ELSE {
        return false;           
    }  
}

function convert_file_size_to_string ($filesize)
{
    $result='';
    $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    $filesize_unit=1024;
    if ($filesize>0) {
        for($i = 0; $i < count($sizes); $i++)
        {
            if ($filesize>=$filesize_unit) {
                $filesize=$filesize/$filesize_unit;
            } else {
                $filesize=round($filesize,1);
                $result= strval($filesize). $sizes[$i]; 
                break;         
            }
            
        }    
    }

    return $result;
}

function convert_license ($string)
{
    $string=mb_strtolower($string);
    $license_type_array=array (
    'free' => 'Freeware',
    'trial' => 'Free to try',
     'free to try' => 'Trial',        
    'purchase' => 'Commercial',
    'updates' => 'update/patch',
    'update' => 'update/patch',
    
    
    );
    
    $temp=explode(';',$string);
    $license_temp='';   
    $price_temp='';
    
    if (count($temp)>1) {
    $license_temp=trim($temp[0]);        
        
        $price_temp = trim($temp[1]);
        $pattern = "/(us|)([\$\£\¥\₩\€\元\₭₫\б]\d+.*?)( .*)/i";
        if (preg_match($pattern,$price_temp, $result)) {
            $price_temp = trim($result[2]);
            if (mb_strtolower($license_temp)=='free') $license_temp='trial';
            if (mb_strtolower($license_temp)=='update') $license_temp='purchase';            
        }               
    } else {
            $license_temp=trim($string); 
    }
    
    if (isset($license_type_array[$license_temp])) $license_temp=$license_type_array[$license_temp];
    
     return array($license_temp,$price_temp);   
}

function convert_platform ($string) {
    $platform_array =array(
    'Windows'=>'Windows',
'Windows 3.x'=>'Windows 3.x',
'Windows 95'=>'Windows 95',
'Windows 98'=>'Windows 98',
'Windows Me'=>'Windows Me',
'Windows NT'=>'Windows NT',
'Windows NT 3'=>'Windows NT',
'Windows NT 4'=>'Windows NT',
'Windows NT 4 SP 1'=>'Windows NT',
'Windows NT 4 SP 2'=>'Windows NT',
'Windows NT 4 SP 3'=>'Windows NT',
'Windows NT 4 SP 4'=>'Windows NT',
'Windows NT 4 SP 5'=>'Windows NT',
'Windows NT 4 SP 6'=>'Windows NT',
'Windows 2000'=>'Windows 2000',
'Windows 2000 Professional'=>'Windows 2000',
'Windows 2000 Server'=>'Windows 2000',
'Windows 2000 Advanced Server'=>'Windows 2000',
'Windows 2000 SP 1'=>'Windows 2000',
'Windows 2000 SP 2'=>'Windows 2000',
'Windows 2000 SP 3'=>'Windows 2000',
'Windows 2000 SP 4'=>'Windows 2000',
'Windows XP'=>'Windows XP',
'Windows XP Home Edition'=>'Windows XP',
'Windows XP Professional'=>'Windows XP',
'Windows XP SP 1'=>'Windows XP',
'Windows XP SP 2'=>'Windows XP',
'Windows XP 64-bit'=>'Windows XP 64-bit',
'Windows XP 64-bit SP 1'=>'Windows XP  64-bit',
'Windows XP 64-bit SP 2'=>'Windows XP 64-bit',
'Windows XP Itanium 64-bit'=>'Windows XP 64-bit',
'Windows XP Itanium 64-bit SP 1'=>'Windows XP 64-bit',
'Windows XP Itanium 64-bit SP 2'=>'Windows XP 64-bit',
'Windows XP 32-bit'=>'Windows XP 32-bit',
'Windows XP Media Center Edition'=>'Windows XP Media Center Edition',
'Windows XP Media Center Edition 2003'=>'Windows XP Media Center Edition',
'Windows XP Media Center Edition 2004'=>'Windows XP Media Center Edition',
'Windows XP Media Center Edition 2005'=>'Windows XP Media Center Edition 2005',
'Windows XP Tablet PC Edition'=>'Windows XP Tablet PC Edition',
'Windows XP Tablet PC Edition 2005'=>'Windows XP Tablet PC Edition 2005',
'Windows 2003'=>'Windows Server 2003',
'Windows 2003 SP 1'=>'Windows Server 2003',
'Windows 2003 64-bit'=>'Windows Server 2003 64-bit',
'Windows 2003 64-bit SP 1'=>'Windows Server 2003 64-bit',
'Windows 2003 Itanium 64-bit'=>'Windows Server 2003 64-bit',
'Windows 2003 Itanium 64-bit SP 1'=>'Windows Server 2003 64-bit',
'Windows 2003 32-bit'=>'Windows Server 2003 32-bit',
'Windows Server 2003 x86 R2'=>'Windows Server 2003 32-bit',
'Windows Server 2003 x64 R2'=>'Windows Server 2003 64-bit',
'Windows Vista'=>'Windows Vista',
'Windows Vista Home Basic'=>'Windows Vista',
'Windows Vista Home Premium'=>'Windows Vista',
'Windows Vista Business'=>'Windows Vista',
'Windows Vista Enterprise'=>'Windows Vista',
'Windows Vista Ultimate'=>'Windows Vista',
'Windows Vista 32-bit'=>'Windows Vista 32-bit',
'Windows Vista AMD 64-bit'=>'Windows Vista 64-bit',
'Windows Vista Itanium 64-bit'=>'Windows Vista 64-bit',
'Windows Server 2008'=>'Windows Server 2008',
'Windows Server 2008 Standard'=>'Windows Server 2008',
'Windows Server 2008 Enterprise'=>'Windows Server 2008',
'Windows Server 2008 Datacenter'=>'Windows Server 2008',
'Windows Web Server 2008'=>'Windows Server 2008',
'Windows Server 2008 Itanium'=>'Windows Server 2009',
'Windows HPC Server 2008'=>'Windows Server 2008',
'Windows Server 2008 x86'=>'Windows Server 2008 32-bit',
'Windows Server 2008 x64'=>'Windows Server 2008 64-bit',
'Macintosh'=>'Macintosh',
'Mac OS Classic'=>'Mac OS Classic',
'Mac OS X 10.0'=>'Mac OS X',
'Mac OS X 10.0 Server'=>'Mac OS X Server',
'Mac OS X 10.1'=>'Mac OS X',
'Mac OS X 10.1 Server'=>'Mac OS X',
'Mac OS X 10.2'=>'Mac OS X',
'Mac OS X 10.2 Server'=>'Mac OS X Server',
'Mac OS X 10.3'=>'Mac OS X',
'Mac OS X 10.3 Server'=>'Mac OS X Server',
'Mac OS X 10.3.9'=>'Mac OS X',
'Mac OS X 10.4'=>'Mac OS X',
'Mac OS X 10.4 PPC'=>'Mac OS X PPC',
'Mac OS X 10.4 Intel'=>'Mac OS X Intel',
'Mac OS X 10.4 Server'=>'Mac OS X Server',
'Mac OS X 10.5'=>'Mac OS X',
'Mac OS X 10.5 PPC'=>'Mac OS X PPC',
'Mac OS X 10.5 Intel'=>'Mac OS X Intel',
'Mac OS X 10.5 Server'=>'Mac OS X Server',
'Mobile'=>'Mobile',
'Windows Mobile'=>'Windows Mobile',
'Palm'=>'Palm',
'iPhone'=>'iPhone',
'BlackBerry'=>'BlackBerry',
'Symbian'=>'Symbian',
'Linux'=>'Linux',
'Java'=>'Java',
'Windows Mobile'=>'Windows Mobile',
'Pocket PC 2000'=>'Pocket PC 2000',
'Pocket PC 2002'=>'Pocket PC 2002',
'Windows Mobile 2003'=>'Windows Mobile 2003',
'Windows Mobile 2003 Phone Edition'=>'Windows Mobile 2003',
'Windows Mobile 2003 SE'=>'Windows Mobile 2003',
'Windows Mobile 5.x'=>'Windows Mobile 5.x',
'Windows Mobile 6.x'=>'Windows Mobile 6.x',
'Palm OS 1.x'=>'Palm OS 1.x',
'Palm OS 2.x'=>'Palm OS 2.x',
'Palm OS 3.x'=>'Palm OS 3.x',
'Palm OS 4.x'=>'Palm OS 4.x',
'Palm OS 5.x'=>'Palm OS 5.x',
'iPhone Webapp'=>'iPhone',
'iPhone OS 1.x'=>'iPhone OS 1.x',
'iPhone OS 2.x'=>'iPhone OS 2.x',
'BlackBerry OS 1.x'=>'BlackBerry OS 1.x',
'BlackBerry OS 2.x'=>'BlackBerry OS 2.x',
'BlackBerry OS 3.x'=>'BlackBerry OS 3.x',
'BlackBerry OS 4.x'=>'BlackBerry OS 4.x',
'Symbian OS 6.x'=>'Symbian OS 6.x',
'Symbian OS 7.x'=>'Symbian OS 7.x',
'Symbian OS 8.x'=>'Symbian OS 8.x',
'Symbian OS 9.x'=>'Symbian OS 9.x',
'Symbian UIQ 2.x'=>'Symbian UIQ 2.x',
'Symbian UIQ 3.x'=>'Symbian UIQ 3.x',
'Webware'=>'Website',
'Windows XP AMD 64-bit'=>'Windows XP  64-bit',
'Windows 2003 AMD 64-bit'=>'Windows Server 2003 64-bit',
'Android'=>'Android',
'Windows 7'=>'Windows 7',
'Windows 7 32-bit'=>'Windows 7 32-bit',
'Windows 7 64-bit'=>'Windows 7 64-bit',
'Windows Vista 64-bit'=>'Windows Vista 64-bit',
'iPhone OS 3.x'=>'iPhone OS 3.x' );

$platform_array_new	=	array();

foreach ($platform_array as $k=>$v) {
	$k	=	strtolower($k);
	$platform_array_new[$k]	=	$v;
}
$cdn_platform_array=explode(',',$string);
$cdn_platform_array_new=array();
foreach ($cdn_platform_array as $cdn_platform) {
    $cdn_platform=trim($cdn_platform);
    if (isset($platform_array_new[strtotime($cdn_platform)])) {
        $cdn_platform_array_new[]=$platform_array[$cdn_platform];
    } else {
        $cdn_platform_array_new[]=$cdn_platform;        
    }  
}

    $cdn_platform_array_new=array_unique($cdn_platform_array_new);
    
    $platform_new=implode(', ',$cdn_platform_array_new);
    return $platform_new;
}

function strip_html_tags( $text )
{
    // PHP's strip_tags() function will remove tags, but it
    // doesn't remove scripts, styles, and other unwanted
    // invisible text between tags.  Also, as a prelude to
    // tokenizing the text, we need to insure that when
    // block-level tags (such as <p> or <div>) are removed,
    // neighboring words aren't joined.
    $text = preg_replace(
        array(
            // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',

            '@<area[^>]*?.*?</area>@siu',
            '@<map[^>]*?.*?</map>@siu',
            '@<marquee[^>]*?.*?</marquee>@siu',
            '@<menu[^>]*?.*?</menu>@siu',
            '@<select[^>]*?.*?</select>@siu',
            '@<textarea[^>]*?.*?</textarea>@siu',

            // Add line breaks before & after blocks
            '@<((br)|(hr))@iu',
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            ' ', ' ', ' ', ' ', ' ', ' ',            
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
        ),
        $text );

    // Remove all remaining tags and comments and return.
//    return strip_tags( $text );
   return $text;
}

function make_intro($string,$maxchar=200) {
//    $maxchar=200;
    $string=str_replace('<',' <',$string);
    $string=strip_html_tags($string);
    $string=strip_tags( $string );
    $string = html_entity_decode( $string, ENT_QUOTES, "UTF-8" ); 
    $string=htmlspecialchars_decode($string);
    $string=str_replace("\r"," ",$string);
    $string=str_replace("\n"," ",$string);
    $string=str_replace("\t"," ",$string);
    $string = preg_replace('/ {2,}/', " ", $string);

    $string=trim($string);
    $stringlength=mb_strlen($string);
    if ($stringlength>$maxchar) {
      $string = mb_substr($string,0,$maxchar);
      $stringlength=$maxchar;  
    }

    $pos1=mb_strrpos($string,'. ');

    if (!$pos1 | $pos1 < ($stringlength/2)) {
        $pos1 = mb_strrpos($string,' '); 
    }

    if (!$pos1) $pos1=$stringlength;
    
    $string = mb_substr($string,0, $pos1);
    
    return $string;
}
//
//function remove_copy_prefix ($string) {
//        $patern1="/Copy \(\d+\) of /";
//        $name1=preg_replace($patern1,'',$string);
//        $patern2="/Copy of/";
//        $name1=preg_replace($patern2,'',$name1);
//        $patern3="/Copy-\(\d+\)-of-/";
//        $name1=preg_replace($patern3,'',$name1);
//        
//        $name1=str_ireplace('.exe',' ',$name1);
//        $name1=str_ireplace('.zip',' ',$name1);
//        $name1=str_ireplace('.rar',' ',$name1);
//        $name1=str_ireplace('.cab',' ',$name1);
//        $name1=str_ireplace('.tar',' ',$name1);
//        $name1=str_ireplace('  ',' ',$name1);
//        $name1=str_ireplace('  ',' ',$name1);
//        $name1=trim($name1);
//        return $name1;
//}