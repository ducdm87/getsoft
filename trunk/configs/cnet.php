<?php

/**
 * *	thoi gian cho moi lan chay lay bai
 */
$time_exp	=	60;
/**
 *		so luong bai can lay cho moi lan
 */
$numbercontent	=	5;
/**
 * - true: lấy lại bài viết
 * - false: b�? qua bài viết đã lấy
 */
$get_existing	= false;
/**
 * - true: lấy nhi�?u category. sử dụng cho lần chạy đầu tiên. lấy vét cạn
 * - false: chỉ lấy bài viết mới của category chạy sớm nhất. khi đặt cronjob
 */
$get_multicat		= true;
/**
 * : 	đư�?ng dẫn đến thư mục ảnh
 */
$root_icon		=	'/media/icons/cn92';
/**
 * địa chỉ tới nơi chứa ảnh vd
 */
$patch_icon		=	'media/icons/cn92';
/**
 * : 	đư�?ng dẫn đến thư mục ảnh
 */
$root_image		=	'/media/images/cn92';
/**
 * địa chỉ tới nơi chứa ảnh vd
 */
$patch_image	=	'media/images/cn92';
/**
 * : 	đư�?ng dẫn đến thư mục ảnh
 */
$root_file		=	'/media/soft_driver/cn92';
/**
 * địa chỉ tới nơi chứa ảnh vd
 */
$patch_file	=	'media/soft_driver/cn92';


/**
 * 
 */
$SiteID 		=	'cn92';
$SiteName 		=	'cnet.com';

// FOR LOGIN
$link_login		=	'http://download.cnet.com/8750-4_4-0.html';

$user_cnet		=	'ducdm87@gmail.com';

$pass_cnet		=	'123456789';

// FOR DATABASE
$host			=	'localhost';
$user			=	'root';
$password		=	'';
$dbname			=	'joomla_wse';
$prefix			=	'';
$table_name		=	'dir_download_cdn_live';
$dbtype			=	'mysql';