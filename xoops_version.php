<?php
/**
 * ****************************************************************************
 * references - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         references
 * @author 			Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * Version : $Id:
 * ****************************************************************************
 */

if (!defined('XOOPS_ROOT_PATH')) {
	die('XOOPS root path not defined');
}

$modversion['name'] = _MI_REFERENCES_NAME;
$modversion['version'] = 1.8;
$modversion['description'] = _MI_REFERENCES_DESC;
$modversion['credits'] = "Burning, Agence pour le Développement de l'Ecotourisme";
$modversion['author'] = "Hervé Thouzard of Instant Zero";
$modversion['license'] = 'Commercial';
$modversion['official'] = 0;
$modversion['image'] = "images/references.png";
$modversion['dirname'] = 'references';

// ****************************************************************************
// Tables *********************************************************************
// ****************************************************************************
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'][0] = 'references_articles';
$modversion['tables'][1] = 'references_categories';

// ****************************************************************************
// Admin Menu *****************************************************************
// ****************************************************************************
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

// ****************************************************************************
// Blocks *********************************************************************
// ****************************************************************************
$cptb = 0;

/**
 * Latest references
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'references_last_news.php';
$modversion['blocks'][$cptb]['name'] = _MI_REFERENCES_BNAME1;
$modversion['blocks'][$cptb]['description'] = "";
$modversion['blocks'][$cptb]['show_func'] = 'b_references_last_news_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_references_last_news_edit';
$modversion['blocks'][$cptb]['options'] = "10|";	// Nombre d'articles à afficher, catégories à afficher
$modversion['blocks'][$cptb]['template'] = 'references_block_last_new.html';

/**
 * Random reference
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'references_random_news.php';
$modversion['blocks'][$cptb]['name'] = _MI_REFERENCES_BNAME2;
$modversion['blocks'][$cptb]['description'] = '';
$modversion['blocks'][$cptb]['show_func'] = 'b_references_random_news_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_references_random_news_edit';
$modversion['blocks'][$cptb]['options'] = "5|";	// Nombre d'articles, catégories à afficher
$modversion['blocks'][$cptb]['template'] = 'references_block_random_news.html';

/*
 * $options:
 *					$options[0] - number of tags to display
 *					$options[1] - time duration, in days, 0 for all the time
 *					$options[2] - max font size (px or %)
 *					$options[3] - min font size (px or %)
 */
$modversion['blocks'][]	= array(
	'file'			=> 'references_block_tag.php',
	'name'			=> _MI_REFERENCES_BNAME3,
	'description'	=> 'Show tag cloud',
	'show_func'		=> 'references_tag_block_cloud_show',
	'edit_func'		=> 'references_tag_block_cloud_edit',
	'options'		=> '100|0|150|80',
	'template'		=> 'references_tag_block_cloud.html'
	);

/*
 * $options:
 *					$options[0] - number of tags to display
 *					$options[1] - time duration, in days, 0 for all the time
 *					$options[2] - sort: a - alphabet; c - count; t - time
 */
$modversion['blocks'][]	= array(
	'file'			=> 'references_block_tag.php',
	'name'			=> _MI_REFERENCES_BNAME4,
	'description'	=> 'Show top tags',
	'show_func'		=> 'references_tag_block_top_show',
	'edit_func'		=> 'references_tag_block_top_edit',
	'options'		=> '50|30|c',
	'template'		=> 'references_tag_block_top.html'
);


// ****************************************************************************
// Templates ******************************************************************
// ****************************************************************************
$cptt = 0;

$cptt++;
$modversion['templates'][$cptt]['file'] = 'references_index.html';
$modversion['templates'][$cptt]['description'] = "Module index";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'references_print.html';
$modversion['templates'][$cptt]['description'] = "Printable version";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'references_rss.html';
$modversion['templates'][$cptt]['description'] = "RSS Feed";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'references_category.html';
$modversion['templates'][$cptt]['description'] = "Category's references";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'references_reference.html';
$modversion['templates'][$cptt]['description'] = "A reference";


// ****************************************************************************
// Menu(s) ********************************************************************
// ****************************************************************************
$modversion['hasMain'] = 1;

$cptm = 0;

require_once XOOPS_ROOT_PATH.'/modules/references/class/references_utils.php';
if(references_utils::getModuleOption('use_rss')) {
	$cptm++;
	$modversion['sub'][$cptm]['name'] = _MI_REFERENCES_SMENU1;
	$modversion['sub'][$cptm]['url'] = "rss.php";
}

// ****************************************************************************
// Recherche ******************************************************************
// ****************************************************************************
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = 'include/search.inc.php';
$modversion['search']['func'] = 'references_search';


// ****************************************************************************
// Config Settings ************************************************************
// ****************************************************************************
$cpto = 0;

/**
 * Items per page on the module's index page
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'items_index_page';
$modversion['config'][$cpto]['title'] = '_MI_REFERENCES_OPTION1';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = '10';

/**
 * Items per page in the module's administration
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'items_admin_page';
$modversion['config'][$cpto]['title'] = '_MI_REFERENCES_OPTION11';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = '10';

/**
 * Editor to use
 */
if(!defined("REFERENCES_MAINTAIN")) {
	$cpto++;
	$modversion['config'][$cpto]['name'] = 'form_options';
	$modversion['config'][$cpto]['title'] = "_MI_REFERENCES_OPTION2";
	$modversion['config'][$cpto]['description'] = '';
	$modversion['config'][$cpto]['formtype'] = 'select';
	$modversion['config'][$cpto]['valuetype'] = 'text';
	$modversion['config'][$cpto]['default'] = 'dhtmltextarea';
	xoops_load('xoopseditorhandler');
	$editor_handler = XoopsEditorHandler::getInstance();
	$modversion['config'][$cpto]['options'] = array_flip($editor_handler->getList());
}


/**
 * Thumbs width
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'thumbs_width';
$modversion['config'][$cpto]['title'] = '_MI_REFERENCES_OPTION3';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = '100';

/**
 * Thumbs height
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'thumbs_height';
$modversion['config'][$cpto]['title'] = '_MI_REFERENCES_OPTION4';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = '80';

/**
 * Thumbs width
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'images_width';
$modversion['config'][$cpto]['title'] = '_MI_REFERENCES_OPTION15';
$modversion['config'][$cpto]['description'] = '_MI_REFERENCES_OPTION17';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = '800';

/**
 * Thumbs height
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'images_height';
$modversion['config'][$cpto]['title'] = '_MI_REFERENCES_OPTION16';
$modversion['config'][$cpto]['description'] = '_MI_REFERENCES_OPTION17';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = '600';

/**
 * Folder (path) where to save pictures
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'images_path';
$modversion['config'][$cpto]['title'] = '_MI_REFERENCES_OPTION5';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = XOOPS_UPLOAD_PATH;

/**
 * Folder (url) where to save pictures
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'images_url';
$modversion['config'][$cpto]['title'] = '_MI_REFERENCES_OPTION6';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = XOOPS_UPLOAD_URL;

/**
 * Folder (path) where to save attached files
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'attached_path';
$modversion['config'][$cpto]['title'] = '_MI_REFERENCES_OPTION9';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = XOOPS_UPLOAD_PATH;

/**
 * Folder (url) where to save attached files
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'attached_url';
$modversion['config'][$cpto]['title'] = '_MI_REFERENCES_OPTION10';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = XOOPS_UPLOAD_URL;

/**
 * Use RSS ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'use_rss';
$modversion['config'][$cpto]['title'] = '_MI_REFERENCES_OPTION14';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * RSS cache time
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'rss_cache_time';
$modversion['config'][$cpto]['title'] = '_MI_REFERENCES_OPTION8';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 3600;

/**
 * Mime Types
 * Default values : Web pictures (png, gif, jpeg), zip, pdf, gtar, tar, pdf
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'mimetypes';
$modversion['config'][$cpto]['title'] = '_MI_REFERENCES_OPTION12';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textarea';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = "image/gif\nimage/jpeg\nimage/pjpeg\nimage/x-png\nimage/png\napplication/x-zip-compressed\napplication/zip\napplication/pdf\napplication/x-gtar\napplication/x-tar";

/**
 * MAX Filesize Upload in kilo bytes
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'maxuploadsize';
$modversion['config'][$cpto]['title'] = '_MI_REFERENCES_OPTION13';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1048576;

/**
 * Use the TAG system ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'use_tags';
$modversion['config'][$cpto]['title'] = '_MI_REFERENCES_USE_TAGS';
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;

/**
 * Zone à utiliser pour trier les références
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'sort_field';
$modversion['config'][$cpto]['title'] = "_MI_REFERENCES_SORT_FIELD";
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'select';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['options'] = array(
											_MI_REFERENCES_SORT_DATE => 'article_timestamp',
											_MI_REFERENCES_SORT_WEIGHT => 'article_weight'
											);
$modversion['config'][$cpto]['default'] = 'article_timestamp';

/**
 * Sens du tri
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'sort_order';
$modversion['config'][$cpto]['title'] = "_MI_REFERENCES_SORT_ORDER";
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'select';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['options'] = array(
											_MI_REFERENCES_SORT_ASC => 'ASC',
											_MI_REFERENCES_SORT_DESC => 'DESC'
											);
$modversion['config'][$cpto]['default'] = 'DESC';


/**
 * Zone à utiliser pour trier les références dans l'admin
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'admin_sort_field';
$modversion['config'][$cpto]['title'] = "_MI_REFERENCES_SORT_FIELD_ADMIN";
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'select';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['options'] = array(
		_MI_REFERENCES_SORT_DATE => 'article_timestamp',
		_MI_REFERENCES_SORT_WEIGHT => 'article_weight',
		_MI_REFERENCES_SORT_TITLE => 'article_title'
);
$modversion['config'][$cpto]['default'] = 'article_weight';

/**
 * Sens du tri
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'admin_sort_order';
$modversion['config'][$cpto]['title'] = "_MI_REFERENCES_SORT_ORDER";
$modversion['config'][$cpto]['description'] = '';
$modversion['config'][$cpto]['formtype'] = 'select';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['options'] = array(
		_MI_REFERENCES_SORT_ASC => 'ASC',
		_MI_REFERENCES_SORT_DESC => 'DESC'
);
$modversion['config'][$cpto]['default'] = 'ASC';


// ****************************************************************************
// Comments *******************************************************************
// ****************************************************************************
$modversion['hasComments'] = 0;


// ****************************************************************************
// Notifications **************************************************************
// ****************************************************************************
$modversion['hasNotification'] = 1;
$modversion['notification']['lookup_file'] = 'include/notification.inc.php';
$modversion['notification']['lookup_func'] = 'references_notify_iteminfo';

$modversion['notification']['category'][1]['name'] = 'global';
$modversion['notification']['category'][1]['title'] = _MI_REFERENCES_GLOBAL_NOTIFY;
$modversion['notification']['category'][1]['description'] = _MI_REFERENCES_GLOBAL_NOTIFYDSC;
$modversion['notification']['category'][1]['subscribe_from'] = array('index.php', 'category.php', "reference.php");

$modversion['notification']['event'][2]['name'] = 'new_article';
$modversion['notification']['event'][2]['category'] = 'global';
$modversion['notification']['event'][2]['title'] = _MI_REFERENCES_GLOBAL_NEWARTICLE_NOTIFY;
$modversion['notification']['event'][2]['caption'] = _MI_REFERENCES_GLOBAL_NEWARTICLE_NOTIFYCAP;
$modversion['notification']['event'][2]['description'] = _MI_REFERENCES_GLOBAL_NEWARTICLE_NOTIFYDSC;
$modversion['notification']['event'][2]['mail_template'] = 'global_newarticle_notify';
$modversion['notification']['event'][2]['mail_subject'] = _MI_REFERENCES_GLOBAL_NEWARTICLE_NOTIFYSBJ;

$modversion['notification']['event'][3]['name'] = 'new_category';
$modversion['notification']['event'][3]['category'] = 'global';
$modversion['notification']['event'][3]['title'] = _MI_REFERENCES_GLOBAL_NEWCATEG_NOTIFY;
$modversion['notification']['event'][3]['caption'] = _MI_REFERENCES_GLOBAL_NEWCATEG_NOTIFYCAP;
$modversion['notification']['event'][3]['description'] = _MI_REFERENCES_GLOBAL_NEWCATEG_NOTIFYDSC;
$modversion['notification']['event'][3]['mail_template'] = 'global_newcategory_notify';
$modversion['notification']['event'][3]['mail_subject'] = _MI_REFERENCES_GLOBAL_NEWCATEG_NOTIFYSBJ;
?>