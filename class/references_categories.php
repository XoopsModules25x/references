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
if (!defined('XOOPS_ROOT_PATH')) {
    die('XOOPS root path not defined');
}

require_once XOOPS_ROOT_PATH . '/kernel/object.php';
if (!class_exists('references_XoopsPersistableObjectHandler')) {
    require_once XOOPS_ROOT_PATH . '/modules/references/class/PersistableObjectHandler.php';
}

class references_categories extends references_Object
{
    public function __construct()
    {
        $this->initVar('category_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('category_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('category_weight', XOBJ_DTYPE_INT, null, false);
        $this->initVar('category_description', XOBJ_DTYPE_TXTAREA, null, false);
        // Pour autoriser le html
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
    }

    /**
     * Retourne la chaine de caractères qui peut être utilisée dans l'attribut href d'une balise html A.
     *
     * @return string
     */
    public function getHrefTitle()
    {
        return references_utils::makeHrefTitle($this->getVar('category_title'));
    }

    /**
     * Retourne l'url à utiliser pour atteindre la catégorie
     *
     * @param bool $shortVersion
     * @return string
     */
    public function getUrl($shortVersion = false)
    {
        if (!$shortVersion) {
            return REFERENCES_URL . 'category.php?category_id=' . $this->getVar('category_id');
        } else {
            return 'category.php?category_id=' . $this->getVar('category_id');
        }
    }

    /**
     * Retourne les éléments de l'annnonce formatés pour affichage
     *
     * @param string $format Format à utiliser
     * @return array
     */
    public function toArray($format = 's')
    {
        $ret                          = array();
        $ret                          = parent::toArray($format);
        $ret['category_href_title']   = $this->getHrefTitle();
        $ret['category_url_rewrited'] = $this->getUrl();

        return $ret;
    }
}

class ReferencesReferences_categoriesHandler extends references_XoopsPersistableObjectHandler
{
    public function __construct($db)
    {    //                         Table                   Classe                    Id                Descr.
        parent::__construct($db, 'references_categories', 'references_categories', 'category_id', 'category_title');
    }

    /**
     * Retourne le crit�re � utiliser pour voir les cat�gories en respectant les permissions
     *
     * @param  string $permissionsType Type de permission (pour l'instant permission de voir)
     * @return obejct de type Criteria
     */
    public function getPermissionsCategoriesCriteria($permissionsType = REFERENCES_PERM_READ)
    {
        global $xoopsUser;
        static $permissions = array();
        if (references_utils::isAdmin()) {
            return new Criteria('category_id', '0', '>');
        }
        if (is_array($permissions) && array_key_exists($permissionsType, $permissions)) {
            return $permissions[$permissionsType];
        }
        $categories    = array();
        $currentModule = references_utils::getModule();
        $groups        = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
        $gperm_handler = xoops_getHandler('groupperm');
        $categories    = $gperm_handler->getItemIds($permissionsType, $groups, $currentModule->getVar('mid'));
        if (is_array($categories) && count($categories) > 0) {
            $permissions[$permissionsType] = new Criteria('category_id', '(' . implode(',', $categories) . ')', 'IN');
        } else {    // Ne peut rien voir
            $permissions[$permissionsType] = new Criteria('category_id', '0', '=');
        }

        return $permissions[$permissionsType];
    }

    /**
     * Retourne les catégories
     *
     * @param integer $start Indice de début
     * @param integer $limit Nombre d'objets à renvoyer
     * @param  string $sort  Zone de tri
     * @param  string $order Sens du tri
     * @return array   Objets de type references_categories
     */
    public function getCategories($start = 0, $limit = 0, $sort = 'category_weight', $order = 'ASC')
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('category_id', 0, '<>'));
        $criteria->add($this->getPermissionsCategoriesCriteria());
        $criteria->setStart($start);
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $criteria->setLimit($limit);

        return $this->getObjects($criteria, true);
    }

    /**
     * Retourne la liste des catégories en tant que tableau
     * @param integer $start Position de départ
     * @param integer $limit Nombre maximum d'objets à retourner
     * @param  string $sort  ordre de tri
     * @param  string $order Sens du tri
     * @return array
     */
    public function getListArray($start = 0, $limit = 0, $sort = 'category_weight', $order = 'ASC')
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('category_id', 0, '<>'));
        $criteria->add($this->getPermissionsCategoriesCriteria());
        $criteria->setStart($start);
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $criteria->setLimit($limit);

        return $this->getList($criteria);
    }

    /**
     * Retourne le sélecteur à utiliser pour voir la liste des catégies
     *
     * @param string  $selectName Le nom du sélecteur
     * @param integer $selected   L'élément sélectionné
     * @return string
     */
    public function getCategoriesSelect($selectName = 'categoriesSelect', $selected = 0)
    {
        $categories = array();
        $ret        = '';
        $categories = $this->getListArray(0, 0, 'category_weight ASC, category_title', 'ASC');
        if (count($categories) == 0) {
            return $ret;
        }
        $jump  = REFERENCES_URL . 'category.php?category_id=';
        $extra = " onchange='location=\"" . $jump . "\"+this.options[this.selectedIndex].value'";

        return references_utils::htmlSelect($selectName, $categories, $selected, true, '', false, 1, $extra);
    }

    /**
     * Notification de la création d'une nouvelle catégorie
     *
     * @param object|references_categories $category
     * @return bool
     */
    public function notifyNewCategory(references_categories $category)
    {
        $plugins = references_plugins::getInstance();
        $plugins->fireAction(references_plugins::EVENT_ON_CATEGORY_CREATE, new references_parameters(array('category' => $category)));

        return true;
    }

    /**
     * Indique si une catégorie est visible d'un utilisateur
     *
     * @param object|references_categories $category La catégorie à controler
     * @param integer                      $uid      L'id de l'utilisateur à controler
     * @return bool
     */
    public function userCanSeeCategory(references_categories $category, $uid = 0)
    {
        global $xoopsUser;
        if ($uid == 0) {
            $uid = references_utils::getCurrentUserID();
        }
        $currentModule = references_utils::getModule();
        $groups        = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
        $gperm_handler = xoops_getHandler('groupperm');

        return $gperm_handler->checkRight(REFERENCES_PERM_READ, $category->category_id, references_utils::getMemberGroups($uid), $currentModule->getVar('mid'));
    }
}
