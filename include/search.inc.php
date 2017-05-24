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
defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

function references_search($queryarray, $andor, $limit, $offset, $userid)
{
    global $xoopsDB;
    include XOOPS_ROOT_PATH . '/modules/references/include/common.php';

    // Recherche dans les articles
    $sql = 'SELECT article_id, article_title, article_text, article_timestamp, article_author FROM ' . $xoopsDB->prefix('references_articles') . ' WHERE (article_online = ' . REFERENCES_STATUS_ONLINE . ')';
    // Permissions
    $handlers    = references_handler::getInstance();
    $permissions = $handlers->h_references_articles->getPermissionsCategoriesCriteria()->renderWhere();
    $sql .= ' AND (' . trim(str_replace('WHERE ', '', $permissions)) . ')';
    // *****

    // Auteur
    if ($userid != 0) {
        $sql .= ' AND (article_author = ' . $userid . ') ';
    }

    $tmpObject = new references_articles();
    $datas     = $tmpObject->getVars();
    $tblFields = array();
    $cnt       = 0;
    foreach ($datas as $key => $value) {
        if ($value['data_type'] == XOBJ_DTYPE_TXTBOX || $value['data_type'] == XOBJ_DTYPE_TXTAREA) {
            if ($cnt == 0) {
                $tblFields[] = $key;
            } else {
                $tblFields[] = ' OR ' . $key;
            }
            ++$cnt;
        }
    }

    $count = count($queryarray);
    $more  = '';
    if (is_array($queryarray) && $count > 0) {
        $cnt = 0;
        $sql .= ' AND (';
        $more = ')';
        foreach ($queryarray as $oneQuery) {
            $sql .= '(';
            $cond = " LIKE '%" . $oneQuery . "%' ";
            $sql .= implode($cond, $tblFields) . $cond . ')';
            ++$cnt;
            if ($cnt != $count) {
                $sql .= ' ' . $andor . ' ';
            }
        }
    }
    $sql .= $more . ' ORDER BY article_timestamp DESC';
    $i      = 0;
    $ret    = array();
    $myts   = MyTextSanitizer::getInstance();
    $result = $xoopsDB->query($sql, $limit, $offset);
    while ($myrow = $xoopsDB->fetchArray($result)) {
        $ret[$i]['image'] = 'assets/images/newspaper.png';
        $ret[$i]['link']  = REFERENCES_URL . 'reference.php?article_id=' . $myrow['article_id'];
        $ret[$i]['title'] = $myts->htmlSpecialChars($myrow['article_title']);
        $ret[$i]['time']  = $myrow['article_timestamp'];
        $ret[$i]['uid']   = $myrow['article_author'];
        ++$i;
    }

    return $ret;
}
