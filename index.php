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

/*
 * Page d'index du module, liste des articles
 */
require 'header.php';
$xoopsOption['template_main'] = 'references_index.html';
require XOOPS_ROOT_PATH.'/header.php';
require_once REFERENCES_PATH.'class/registryfile.php';

$limit = references_utils::getModuleOption('items_index_page');
$start = $index = 0;
$article_id = isset($_GET['article_id']) ? intval($_GET['article_id']) : 0;	// Utiliser pour afficher un article particulier

// Texte à afficher sur la page d'accueil
$registry = new references_registryfile();
$xoopsTpl->assign('welcomeMsg', nl2br($registry->getfile(REFERENCES_TEXTFILE1)));
$xoopsTpl->assign('use_rss', references_utils::getModuleOption('use_rss'));

// MooTools
$xoTheme->addScript(REFERENCES_JS_URL.'js/mootools.js');
$xoTheme->addScript(REFERENCES_JS_URL.'js/mootools-1.2-more.js');

if (isset($xoopsConfig) && file_exists(REFERENCES_PATH.'language/'.$xoopsConfig['language'].'/slimbox.js')) {
	$xoTheme->addScript(REFERENCES_URL.'language/'.$xoopsConfig['language'].'/slimbox.js');
} else {
	$xoTheme->addScript(REFERENCES_JS_URL.'js/slimbox.js');
}

$categories = $h_references_categories->getCategories();
$categoriesSelect = $h_references_categories->getCategoriesSelect();
$xoopsTpl->assign('categoriesSelect', $categoriesSelect);
$categoriesForTemplate = array();

//$xoTheme->addScript(REFERENCES_JS_URL.'js/accordion.js');
$xoTheme->addStylesheet(REFERENCES_JS_URL.'css/slimbox.css');
$xoTheme->addStylesheet(REFERENCES_JS_URL.'css/accordion.css');

// ****************************************************************************************************************************
$xoopsTpl->assign('thumbsWidth', references_utils::getModuleOption('thumbs_width'));
$xoopsTpl->assign('thumbsHeight', references_utils::getModuleOption('thumbs_height'));

$lastTitle = $lastKeywords = '';
$refFounded = false;
$mostRecentReferenceDate = 0;
if ($limit > 0 ) {
    $uniqueCategories = $h_references_articles->getDistinctCategoriesIds();
    foreach($uniqueCategories as $categoryId) {
        $items = array();
        $items = $h_references_articles->getRecentArticles($start, $limit, references_utils::getModuleOption('sort_field'), references_utils::getModuleOption('sort_order'), true, $categoryId);
        $categoryTitle = isset($categories[$categoryId]) ? $categories[$categoryId]->getVar('category_title') : '';
        $categoryWeight = isset($categories[$categoryId]) ? $categories[$categoryId]->getVar('category_weight') : 0;
        if(count($items) > 0) {
            foreach($items as $item) {
                $articleData = array();
                $articleData = $item->toArray();
                if($item->getVar('article_id') == $article_id) {
                    $xoopsTpl->assign('defaultArticle', $index);
                    $refFounded = true;
                }
                $index++;
                $articleData['article_category_id'] = $categoryId;
                $articleData['article_category_title'] = $categoryTitle;
                $articleData['article_category_weight'] = $categoryWeight;
                $categoriesForTemplate[$categoryWeight.'-'.$categoryId]['articles'][] = $articleData;
                $categoriesForTemplate[$categoryWeight.'-'.$categoryId]['categoryTitle']= $categoryTitle;
                $categoriesForTemplate[$categoryWeight.'-'.$categoryId]['categoryId']= $categoryId;
    	        if($item->getVar('article_timestamp') > $mostRecentReferenceDate) {
    	            $mostRecentReferenceDate = $item->getVar('article_timestamp');
            	    $lastTitle = strip_tags($item->getVar('article_title', 'n')).', '.$item->getVar('article_date');
    	            $lastKeywords = strip_tags($item->getVar('article_text', 'n'));
    	        }
            }
        }
    }
    if(!$refFounded) {
    	$xoopsTpl->assign('defaultArticle', 0);
    }
    if(count($categoriesForTemplate) > 0) {
        ksort($categoriesForTemplate);
    }
    $xoopsTpl->assign('categories', $categoriesForTemplate);
}

$xoopsTpl->assign('isAdmin', references_utils::isAdmin());
$metaTitle = $lastTitle.' - '.$xoopsModule->name();
$metaKeywords = references_utils::createMetaKeywords($lastTitle.' '.$lastKeywords);

references_utils::setMetas($metaTitle, $metaTitle, $metaKeywords);
require XOOPS_ROOT_PATH.'/footer.php';
?>