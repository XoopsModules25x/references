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
 * Affiche x articles au hasard
 *
 * @param array $options indice 0 = nombre d'articles à afficher
 * @return array Le contenu du bloc
 */
function b_references_random_news_show($options)
{
    global $xoopsConfig;
    require XOOPS_ROOT_PATH . '/modules/references/include/common.php';
    $block = array();
    $start = 0;
    $limit = (int)$options[0];
    if ($limit > 0) {
        $items = array();
        $h_references_articles->setCachingOptions(array('cacheDir' => REFERENCES_CACHE_PATH, 'caching' => false, 'lifeTime' => null, 'automaticSerialization' => true, 'fileNameProtection' => false));
        $categories = array();
        if (is_array($options) && count($options) > 1) {
            $categories = array_slice($options, 1);
        }
        $items = $h_references_articles->getRecentArticles($start, $limit, 'RAND(), NOW()', 'DESC', true, $categories);
        $h_references_articles->setCachingOptions(array('cacheDir' => REFERENCES_CACHE_PATH, 'caching' => true, 'lifeTime' => null, 'automaticSerialization' => true, 'fileNameProtection' => false));
        if (count($items) > 0) {
            foreach ($items as $item) {
                $block['block_random_news'][] = $item->toArray();
            }
        }
    }

    return $block;
}

/**
 * Paramètres du bloc
 *
 * @param array $options indice 0 = nombre d'articles à afficher
 * @return string
 */
function b_references_random_news_edit($options)
{
    global $xoopsConfig;
    include XOOPS_ROOT_PATH . '/modules/references/include/common.php';
    $handlers   = references_handler::getInstance();
    $categories = $handlers->h_references_categories->getListArray();
    $form       = '';
    $form .= "<table border='0'>";
    $form .= '<tr><td>' . _MB_REFERENCES_ITEMS_COUNT . "</td><td><input type='text' name='options[]' id='options' value='" . $options[0] . "' /></td></tr>\n";
    $form .= '<tr><td>' . _MB_REFERENCES_CATEGORIES . "</td><td><select name='options[]' multiple='multiple'>";
    $size = count($options);
    foreach ($categories as $Idcategory => $categoryName) {
        $sel = '';
        for ($i = 1; $i < $size; ++$i) {
            if ($options[$i] == $Idcategory) {
                $sel = " selected='selected'";
            }
        }
        $form .= "<option value='$Idcategory' $sel>$categoryName</option>";
    }
    $form .= "</select></td></tr>\n";
    $form .= '</table>';

    return $form;
}
