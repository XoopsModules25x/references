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
 * @author          Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * ****************************************************************************
 */

/**
 * Affichage des références d'une catégorie
 *
 * @param integer $category_id Le numéro de la catégorie
 */
require __DIR__ . '/header.php';
$xoopsOption['template_main'] = 'references_category.tpl';
require XOOPS_ROOT_PATH . '/header.php';

$baseurl = REFERENCES_URL . basename(__FILE__);
$limit   = references_utils::getModuleOption('items_index_page');
$start   = $index = 0;

$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
if ($category_id == 0) {
    header('Location: index.php', true, 301);
    exit;
    //references_utils::redirect('', 'index.php', 0);
}
// Chargement de la catégorie
$category = null;
$category = $h_references_categories->get($category_id);
if (!is_object($category)) {
    references_utils::redirect(_MD_REFERENCES_ERROR2, 'index.php', 4);
}

// Vérification des permissions
$handlers = references_handler::getInstance();
if (!$handlers->h_references_categories->userCanSeeCategory($category)) {
    references_utils::redirect(_NOPERM, 'index.php', 4);
}

$xoopsTpl->assign('category', $category->toArray());
$xoopsTpl->assign('defaultArticle', 0);
// Breadcrumb
$breadcrumb = array($baseurl => $category->getVar('category_title'));
$xoopsTpl->assign('breadcrumb', references_utils::breadcrumb($breadcrumb));

// Texte à afficher sur la page d'accueil
$xoopsTpl->assign('use_rss', references_utils::getModuleOption('use_rss'));

// MooTools
$xoTheme->addScript(REFERENCES_JS_URL . 'js/mootools.js');
$xoTheme->addScript(REFERENCES_JS_URL . 'js/mootools-1.2-more.js');
if (isset($xoopsConfig) && file_exists(REFERENCES_PATH . 'language/' . $xoopsConfig['language'] . '/slimbox.js')) {
    $xoTheme->addScript(REFERENCES_URL . 'language/' . $xoopsConfig['language'] . '/slimbox.js');
} else {
    $xoTheme->addScript(REFERENCES_JS_URL . 'js/slimbox.js');
}

$categoriesSelect = $h_references_categories->getCategoriesSelect('categoriesSelect', $category->getVar('category_id'));
$xoopsTpl->assign('categoriesSelect', $categoriesSelect);
$xoTheme->addStylesheet(REFERENCES_JS_URL . 'css/slimbox.css');
$xoTheme->addStylesheet(REFERENCES_JS_URL . 'css/accordion.css');

// ****************************************************************************************************************************
$xoopsTpl->assign('thumbsWidth', references_utils::getModuleOption('thumbs_width'));
$xoopsTpl->assign('thumbsHeight', references_utils::getModuleOption('thumbs_height'));
if ($limit > 0) {
    $items          = array();
    $items          = $h_references_articles->getRecentArticles($start, $limit, references_utils::getModuleOption('sort_field'), references_utils::getModuleOption('sort_order'), true, $category_id);
    $categoryTitle  = $category->getVar('category_title');
    $categoryWeight = $category->getVar('category_weight');
    if (count($items) > 0) {
        foreach ($items as $item) {
            $xoopsTpl->append('articles', $item->toArray());
        }
    }
}
$xoopsTpl->assign('isAdmin', references_utils::isAdmin());
$title = $category->getVar('category_title', 'n') . ' - ' . $xoopsModule->name();
references_utils::setMetas($title, $title);
require XOOPS_ROOT_PATH . '/footer.php';
