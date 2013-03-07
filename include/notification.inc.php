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
 * Gestion des notifications
 */
function references_notify_iteminfo($category, $item_id)
{
	global $xoopsModule, $xoopsModuleConfig, $xoopsConfig;
	$item_id = intval($item_id);

	if (empty($xoopsModule) || $xoopsModule->getVar('dirname') != 'references') {
		$module_handler =& xoops_gethandler('module');
		$module =& $module_handler->getByDirname('references');
		$config_handler =& xoops_gethandler('config');
		$config =& $config_handler->getConfigsByCat(0,$module->getVar('mid'));
	} else {
		$module =& $xoopsModule;
		$config =& $xoopsModuleConfig;
	}

	if ($category == 'global') {
		$item['name'] = '';
		$item['url'] = '';
		return $item;
	}

	if ($category == 'new_article') {
		include REFERENCES_PATH.' include/common.php';
		$article = null;
		$article = $h_references_articles->get($item_id);
		if(is_object($article)) {
			$item['name'] = $article->getVar('article_title');
			$item['url'] = REFERENCES_URL;
		}
		return $item;
	}
}
?>