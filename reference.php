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
 * @param integer $article_id Le numéro de la référence
 */
require __DIR__ . '/header.php';
$xoopsOption['template_main'] = 'references_reference.tpl';
require XOOPS_ROOT_PATH . '/header.php';

$baseurl   = REFERENCES_URL . basename(__FILE__);
$artice_id = isset($_GET['article_id']) ? (int)$_GET['article_id'] : 0;
if ($artice_id == 0) {
    references_utils::redirect(_MD_REFERENCES_ERROR4, 'index.php', 4);
}
$article = null;
$article = $h_references_articles->get($artice_id);
if (!is_object($article)) {
    references_utils::redirect(_MD_REFERENCES_ERROR4, 'index.php', 4);
}
// Vérification des permissions
$handlers = references_handler::getInstance();
if (!$handlers->h_references_articles->userCanSeeReference($article)) {
    references_utils::redirect(_NOPERM, 'index.php', 4);
}

// Chargement de la catégorie
$category = null;
$category = $h_references_categories->get($article->getVar('article_category_id'));
if (!is_object($category)) {
    references_utils::redirect(_MD_REFERENCES_ERROR2, 'index.php', 4);
}
$xoopsTpl->assign('category', $category->toArray());
$xoopsTpl->assign('article', $article->toArray());

$_SESSION['reference']['current_article'] = $article->getVar('article_title', 'n');

// Breadcrumb
$breadcrumb = array(
    $category->getUrl() => $category->getVar('category_title'),
    $baseurl            => $article->getVar('article_title')
);
$xoopsTpl->assign('breadcrumb', references_utils::breadcrumb($breadcrumb));

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
$xoopsTpl->assign('isAdmin', references_utils::isAdmin());

// ****************************************************************************************************************************
$xoopsTpl->assign('thumbsWidth', references_utils::getModuleOption('thumbs_width'));
$xoopsTpl->assign('thumbsHeight', references_utils::getModuleOption('thumbs_height'));

$metaTitle    = $article->getVar('article_title', 'n') . ' - ' . $xoopsModule->name();
$metaKeywords = references_utils::createMetaKeywords($article->getVar('article_text', 'n'));
references_utils::setMetas($metaTitle, $metaTitle, $metaKeywords);
require XOOPS_ROOT_PATH . '/footer.php';
