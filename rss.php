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

/**
 * Flux RSS
 */
require 'header.php';
require_once XOOPS_ROOT_PATH.'/class/template.php';
error_reporting(0);
@$xoopsLogger->activated = false;

if(!references_utils::getModuleOption('use_rss')) {
    exit(_ERRORS);
}

if (function_exists('mb_http_output')) {
	mb_http_output('pass');
}
$charset = 'utf-8';
header ('Content-Type:text/xml; charset='.$charset);

$tpl = new XoopsTpl();
$tpl->xoops_setCaching(2);					                                    // 1 = Cache global, 2 = Cache individuel (par template)
$tpl->xoops_setCacheTime(references_utils::getModuleOption('rss_cache_time'));	// Temps de cache en secondes
$uid = references_utils::getCurrentUserID();
if (!$tpl->is_cached('db:references_rss.html', $uid)) {
	$categoryTitle = '';
	global $xoopsConfig;
	$sitename = htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES);
	$email = $xoopsConfig['adminmail'];
	$slogan = htmlspecialchars($xoopsConfig['slogan'], ENT_QUOTES);

	$tpl->assign('charset',$charset);
	$tpl->assign('channel_title', xoops_utf8_encode($sitename));
	$tpl->assign('channel_link', XOOPS_URL.'/');
	$tpl->assign('channel_desc', xoops_utf8_encode($slogan));
	$tpl->assign('channel_lastbuild', formatTimestamp(time(), 'rss'));
	$tpl->assign('channel_webmaster', xoops_utf8_encode($email));
	$tpl->assign('channel_editor', xoops_utf8_encode($email));
	$tpl->assign('channel_category', xoops_utf8_encode($categoryTitle));
	$tpl->assign('channel_generator', xoops_utf8_encode(references_utils::getModuleName()));
	$tpl->assign('channel_language', _LANGCODE);
	$tpl->assign('image_url', XOOPS_URL.'/images/logo.gif');
	$dimention = getimagesize(XOOPS_ROOT_PATH.'/images/logo.gif');
	if (empty($dimention[0])) {
		$width = 88;
	} else {
		$width = ($dimention[0] > 144) ? 144 : $dimention[0];
	}
	if (empty($dimention[1])) {
		$height = 31;
	} else {
		$height = ($dimention[1] > 400) ? 400 : $dimention[1];
	}
	$tpl->assign('image_width', $width);
	$tpl->assign('image_height', $height);
	$start = 0;
	$limit = references_utils::getModuleOption('nb_perpage');
    $items = array();

	$items = $h_references_articles->getRecentArticles($start, $limit);
	foreach($items as $item) {
		$titre = htmlspecialchars($item->getVar('article_title', 'n'),  ENT_QUOTES);
		$description = htmlspecialchars($item->getVar('article_text'), ENT_QUOTES);
		$link = REFERENCES_URL;
		$tpl->append('items', array('title' => xoops_utf8_encode($titre),
         							'link' => $link,
          							'guid' => $link,
          							'pubdate' => formatTimestamp($item->getVar('article_timestamp'), 'rss'),
          							'description' => xoops_utf8_encode($description)));
	}
}
$tpl->display('db:references_rss.html', $uid);
?>