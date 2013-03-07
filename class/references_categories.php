<?php
/**
 * ****************************************************************************
 * references - MODULE FOR XOOPS
 * Copyright (c) Herv� Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Herv� Thouzard of Instant Zero (http://www.instant-zero.com)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         references
 * @author 			Herv� Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * Version : $Id:
 * ****************************************************************************
 */
if (!defined('XOOPS_ROOT_PATH')) {
	die("XOOPS root path not defined");
}

require_once XOOPS_ROOT_PATH.'/class/xoopsobject.php';
if (!class_exists('references_XoopsPersistableObjectHandler')) {
	require_once XOOPS_ROOT_PATH.'/modules/references/class/PersistableObjectHandler.php';
}

class references_categories extends references_Object
{
	function __construct()
	{
		$this->initVar('category_id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('category_title', XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('category_weight', XOBJ_DTYPE_INT, null, false);
		$this->initVar('category_description', XOBJ_DTYPE_TXTAREA, null, false);
        // Pour autoriser le html
		$this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
	}


	/**
	 * Retourne la chaine de caract�res qui peut �tre utilis�e dans l'attribut href d'une balise html A.
	 *
	 * @return string
	 */
	function getHrefTitle()
	{
		return references_utils::makeHrefTitle($this->getVar('category_title'));
	}

	/**
	 * Retourne l'url � utiliser pour atteindre la cat�gorie
	 *
	 * @return string
	 */
	function getUrl($shortVersion = false)
	{
		if(!$shortVersion) {
	    	return REFERENCES_URL.'category.php?category_id='.$this->getVar('category_id');
		} else {
			return 'category.php?category_id='.$this->getVar('category_id');
		}
	}


	/**
	 * Retourne les �l�ments de l'annnonce format�s pour affichage
	 *
	 * @param string $format	Format � utiliser
	 * @return array
	 */
	function toArray($format = 's')
    {
		$ret = array();
		$ret = parent::toArray($format);
		$ret['category_href_title'] = $this->getHrefTitle();
		$ret['category_url_rewrited'] = $this->getUrl();
		return $ret;
    }
}

class ReferencesReferences_categoriesHandler extends references_XoopsPersistableObjectHandler
{
	function __construct($db)
	{	//							Table		            Classe		              Id			    Descr.
		parent::__construct($db, 'references_categories', 'references_categories', 'category_id', 'category_title');
	}

	/**
	 * Retourne le crit�re � utiliser pour voir les cat�gories en respectant les permissions
	 *
	 * @param string $permissionsType	Type de permission (pour l'instant permission de voir)
	 * @return obejct de type Criteria
	 */
	function getPermissionsCategoriesCriteria($permissionsType = REFERENCES_PERM_READ)
	{
		global $xoopsUser;
		static $permissions = array();
		if(references_utils::isAdmin()) {
			return new Criteria('category_id', '0', '>');
		}
		if(is_array($permissions) && array_key_exists($permissionsType,$permissions)) {
			return $permissions[$permissionsType];
		}
		$categories = array();
   		$currentModule = references_utils::getModule();
   		$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
   		$gperm_handler = xoops_gethandler('groupperm');
   		$categories = $gperm_handler->getItemIds($permissionsType, $groups, $currentModule->getVar('mid'));
   		if(is_array($categories) && count($categories) > 0) {
			$permissions[$permissionsType] = new Criteria('category_id', '('.implode(',', $categories).')', 'IN');
   		} else {	// Ne peut rien voir
   			$permissions[$permissionsType] = new Criteria('category_id', '0', '=');
   		}
    	return $permissions[$permissionsType];
	}

    /**
     * Retourne les cat�gories
     *
     * @param integer $start		Indice de d�but
     * @param integer $limit		Nombre d'objets � renvoyer
     * @param string $sort			Zone de tri
     * @param string $order			Sens du tri
     * @return array	Objets de type references_categories
     */
	function getCategories($start = 0, $limit = 0, $sort = 'category_weight', $order= 'ASC')
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
	 * Retourne la liste des cat�gories en tant que tableau
	 * @param integer $start	Position de d�part
	 * @param integer $limit	Nombre maximum d'objets � retourner
	 * @param string $sort		ordre de tri
	 * @param string $order		Sens du tri
	 * @return array
	 */
    function getListArray($start = 0, $limit = 0, $sort = 'category_weight', $order= 'ASC')
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
     * Retourne le s�lecteur � utiliser pour voir la liste des cat�gies
     *
     * @param string $selectName	Le nom du s�lecteur
     * @param integer $selected		L'�l�ment s�lectionn�
     */
    function getCategoriesSelect($selectName = 'categoriesSelect', $selected = 0)
    {
    	$categories = array();
    	$ret = '';
    	$categories = $this->getListArray(0, 0, 'category_weight ASC, category_title', 'ASC');
    	if(count($categories) == 0) {
    		return $ret;
    	}
		$jump = REFERENCES_URL.'category.php?category_id=';
		$extra = " onchange='location=\"".$jump."\"+this.options[this.selectedIndex].value'";
    	return references_utils::htmlSelect($selectName, $categories, $selected, true, '', false, 1, $extra);
    }

	/**
	 * Notification de la cr�ation d'une nouvelle cat�gorie
	 *
	 * @param object $category
	 */
    function notifyNewCategory(references_categories $category)
    {
		$plugins = references_plugins::getInstance();
		$plugins->fireAction(references_plugins::EVENT_ON_CATEGORY_CREATE, new references_parameters(array('category' => $category)));
		return true;
	}

	/**
	 * Indique si une cat�gorie est visible d'un utilisateur
	 *
	 * @param object $category		La cat�gorie � controler
	 * @param integer $uid			L'id de l'utilisateur � controler
	 * @return boolean
	 */
	function userCanSeeCategory(references_categories $category, $uid = 0)
	{
		global $xoopsUser;
		if($uid == 0) {
			$uid = references_utils::getCurrentUserID();
		}
   		$currentModule = references_utils::getModule();
   		$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
   		$gperm_handler = xoops_gethandler('groupperm');
		return $gperm_handler->checkRight(REFERENCES_PERM_READ, $category->category_id, references_utils::getMemberGroups($uid), $currentModule->getVar('mid'));
	}
}
?>