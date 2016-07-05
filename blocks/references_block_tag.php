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
 * @param $options
 * @return bool
 */
function references_tag_block_cloud_show($options)
{
    require XOOPS_ROOT_PATH . '/modules/references/include/common.php';
    if (!references_utils::tagModuleExists()) {
        return false;
    }
    require_once XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php';

    return tag_block_cloud_show($options, 'references');
}

function references_tag_block_cloud_edit($options)
{
    require XOOPS_ROOT_PATH . '/modules/references/include/common.php';
    if (!references_utils::tagModuleExists()) {
        return false;
    }
    require_once XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php';

    return tag_block_cloud_edit($options);
}

function references_tag_block_top_show($options)
{
    require XOOPS_ROOT_PATH . '/modules/references/include/common.php';
    if (!references_utils::tagModuleExists()) {
        return false;
    }
    require_once XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php';

    return tag_block_top_show($options, 'references');
}

function references_tag_block_top_edit($options)
{
    require XOOPS_ROOT_PATH . '/modules/references/include/common.php';
    if (!references_utils::tagModuleExists()) {
        return false;
    }
    require_once XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php';

    return tag_block_top_edit($options);
}
