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
function b_sitemap_references(){
	require XOOPS_ROOT_PATH.'/modules/references/include/common.php';
	$sitemap = array();
	$categories = $h_references_categories->getCategories();
	$i = 0;
	foreach($categories as $category) {
    	$sitemap['parent'][$i]['id'] = $category->getVar('category_id');
		$sitemap['parent'][$i]['title'] = $category->getVar('category_title');
		$sitemap['parent'][$i]['url'] = $category->getUrl(true);
		$i++;
	}
	return $sitemap;
}
?>