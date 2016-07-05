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
$wasUpdated = false;

// Nouveau champ article_attached_file_description dans references_articles
$tableName = $xoopsDB->prefix('references_articles');
if (!references_utils::fieldExists('article_weight', $tableName)) {
    references_utils::addField('`article_weight` MEDIUMINT( 8 ) UNSIGNED NOT NULL AFTER `article_timestamp`', $tableName);
    $xoopsDB->queryF('ALTER TABLE ' . $tableName . ' ADD INDEX ( `article_weight` )');
    $wasUpdated = true;
}

if (!references_utils::fieldExists('article_readmore', $tableName)) {
    references_utils::addField('article_readmore TEXT NOT NULL', $tableName);
    $wasUpdated = true;
}

$tableName = $xoopsDB->prefix('references_categories');
if (!references_utils::fieldExists('category_description', $tableName)) {
    references_utils::addField('category_description TEXT NOT NULL', $tableName);
    $wasUpdated = true;
}

if ($wasUpdated) {
    $op = 'maintain';
}
