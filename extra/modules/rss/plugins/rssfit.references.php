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
if( !defined('RSSFIT_ROOT_PATH') ){ exit(); }
class RssfitReferences {
	var $dirname = 'references';
	var $modname;
	var $grab;

	function RssfitReferences(){
	}

	function loadModule(){
		$mod =& $GLOBALS['module_handler']->getByDirname($this->dirname);
		if( !$mod || !$mod->getVar('isactive') ){
			return false;
		}
		$this->modname = $mod->getVar('name');
		return $mod;
	}

	function &grabEntries(&$obj){
		$ret = false;
		require XOOPS_ROOT_PATH.'/modules/references/include/common.php';
		$start = 0;
		$limit= $this->grab;
		$items = $h_references_articles->getRecentArticles($start, $limit);
		$i = 0;
        $categories = $h_references_categories->getCategories();

		if( false != $items && count($items) > 0 ){
			foreach($items as $item) {
				$ret[$i]['link'] = $item->getUrl();
				$ret[$i]['title'] = $item->getVar('article_title');
				$ret[$i]['timestamp'] = $item->getVar('article_timestamp');
				$ret[$i]['description'] = references_utils::truncate_tagsafe($item->getVar('article_text'), REFERENCES_SHORTEN_TEXT);
				$categoryId = $item->getVar('article_category_id');
				$ret[$i]['category'] = isset($categories[$categoryId]) ? $categories[$categoryId]->getVar('category_title') : '';
				$ret[$i]['domain'] = XOOPS_URL.'/modules/'.$this->dirname.'/';
				$i++;
			}
		}
		return $ret;
	}
}
?>