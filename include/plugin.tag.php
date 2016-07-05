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
 * @param $items
 */

function references_tag_iteminfo(&$items)
{
    $items_id = array();
    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $items_id[] = (int)$item_id;
        }
    }
    include XOOPS_ROOT_PATH . '/modules/references/include/common.php';
    $items_obj = $h_references_articles->getItemsFromIds($items_id);

    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $item_obj                 =& $items_obj[$item_id];
            /** @noinspection SenselessCommaInArrayDefinitionInspection */
            $items[$cat_id][$item_id] = array(
                'title'   => $item_obj->getVar('article_title'),
                'uid'     => $item_obj->getVar('article_author'),
                'link'    => 'index.php',
                'time'    => $item_obj->getVar('article_timestamp'),
                'tags'    => '',
                'content' => '',
            );
        }
    }
    unset($items_obj);
}

/** Remove orphan tag-item links *
 * @param $mid
 */
function references_tag_synchronization($mid)
{
    global $xoopsDB;
    $item_handler_keyName = 'article_id';
    $item_handler_table   = $xoopsDB->prefix('references_articles');
    $link_handler         = xoops_getModuleHandler('link', 'tag');
    $where                = "($item_handler_table.article_online = 1)";
    $where1               = "($item_handler_table.article_online = 0)";

    /* clear tag-item links */
    if ($link_handler->mysql_major_version() >= 4):
        $sql = " DELETE FROM {$link_handler->table}" . '   WHERE ' . "       tag_modid = {$mid}" . '       AND ' . '       ( tag_itemid NOT IN ' . "           ( SELECT DISTINCT {$item_handler_keyName} " . "               FROM {$item_handler_table} " . "               WHERE $where" . '           ) '
               . '       )';
    else:
        $sql = " DELETE {$link_handler->table} FROM {$link_handler->table}" . "   LEFT JOIN {$item_handler_table} AS aa ON {$link_handler->table}.tag_itemid = aa.{$item_handler_keyName} " . '   WHERE ' . "       tag_modid = {$mid}" . '       AND ' . "       ( aa.{$item_handler_keyName} IS NULL"
               . "           OR $where1" . '       )';
    endif;
    if (!$link_handler->db->queryF($sql)) {
        trigger_error($link_handler->db->error());
    }
}
