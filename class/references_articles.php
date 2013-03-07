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

define("REFERENCES_STATUS_ONLINE", 1);	// Articles en ligne
define("REFERENCES_STATUS_OFFLINE", 0);	// Articles hors ligne


class references_articles extends references_Object
{
	function __construct()
	{
		$this->initVar('article_id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('article_category_id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('article_timestamp', XOBJ_DTYPE_INT, null, false);
		$this->initVar('article_weight', XOBJ_DTYPE_INT, null, false);
		$this->initVar('article_date', XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('article_title', XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('article_text', XOBJ_DTYPE_TXTAREA, null, false);
		$this->initVar('article_externalurl', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture1', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture1_text', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture2', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture2_text', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture3', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture3_text', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture4', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture4_text', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture5', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture5_text', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture6', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture6_text', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture7', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture7_text', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture8', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture8_text', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture9', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture9_text', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture10', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('article_picture10_text', XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('article_author', XOBJ_DTYPE_INT, null, false);
		$this->initVar('article_online', XOBJ_DTYPE_INT, null, false);
		$this->initVar('article_attached_file', XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('article_readmore', XOBJ_DTYPE_TXTAREA, null, false);
				
        // Pour autoriser le html
		$this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
	}


	/**
	 * Indique si l'article en cours est visible
	 *
	 * @return boolean
	 */
	function isArticleOnline()
	{
		return $this->getVar('article_online') == REFERENCES_STATUS_ONLINE ? true : false;
	}

    /**
     * Retourne une image qui indique si l'article est en ligne ou pas
	 *
     * @return string
     */
	function getOnlinePicture()
	{
        if($this->isArticleOnline()) {
            return REFERENCES_IMAGES_URL.'status_online.png';
        } else {
            return REFERENCES_IMAGES_URL.'status_offline.png';
        }
	}

	/**
	 * Retourne la chaine de caract�res qui peut �tre utilis�e dans l'attribut href d'une balise html A.
	 *
	 * @return string
	 */
	function getHrefTitle()
	{
		return references_utils::makeHrefTitle($this->getVar('article_title'));
	}


	/**
	 * Indique si une image de l'article existe
	 *
	 * @param integer	$indice	L'indice de l'image recherch�e
	 * @return boolean	Vrai si l'image existe sinon faux
	 */
	function pictureExists($indice)
	{
		$return = false;
		$fieldName = 'article_picture'.$indice;
		if(xoops_trim($this->getVar($fieldName)) != '' && file_exists(references_utils::getModuleOption('images_path').DIRECTORY_SEPARATOR.$this->getVar($fieldName))) {
			$return = true;
		}
		return $return;
	}

	/**
	 * Supprime l'image associ�e � un article
	 *
	 * @param integer	$indice	L'indice de l'image recherch�e
	 * @return void
	 */
	function deletePicture($indice)
	{
	    $fieldName = 'article_picture'.$indice;
		if($this->pictureExists($indice)) {
			@unlink(references_utils::getModuleOption('images_path').references_utils::getModuleOption('images_path').$this->getVar($fieldName));
		}
		$this->setVar($fieldName, '');
	}

	/**
	 * Retourne l'URL de l'image de l'article courant
	 *
	 * @param integer	$indice	L'indice de l'image recherch�e
	 * @return string	L'URL
	 */
	function getPictureUrl($indice)
	{
	    $fieldName = 'article_picture'.$indice;
		if(xoops_trim($this->getVar($fieldName)) != '' && $this->pictureExists($indice)) {
			return references_utils::getModuleOption('images_url').'/'.$this->getVar($fieldName);
		} else {
			return REFERENCES_IMAGES_URL.'blank.gif';
		}
	}

	/**
	 * Retourne le chemin de l'image de l'article courante
	 *
	 * @param integer	$indice	L'indice de l'image recherch�e
	 * @return string	Le chemin
	 */
	function getPicturePath($indice)
	{
	    $fieldName = 'article_picture'.$indice;
		if(xoops_trim($this->getVar($fieldName)) != '') {
			return references_utils::getModuleOption('images_path').DIRECTORY_SEPARATOR.$this->getVar($fieldName);
		} else {
			return '';
		}
	}

	/**
	 * Indique si la vignette de l'image de l'article existe
	 *
	 * @param integer	$indice	L'indice de l'image recherch�e
	 * @return boolean	Vrai si l'image existe sinon faux
	 */
	function thumbExists($indice)
	{
	    $fieldName = 'article_picture'.$indice;
		$return = false;
		if(xoops_trim($this->getVar($fieldName)) != '' && file_exists(references_utils::getModuleOption('images_path').DIRECTORY_SEPARATOR.REFERENCES_THUMBS_PREFIX.$this->getVar($fieldName))) {
			$return = true;
		}
		return $return;
	}

	/**
	 * Retourne l'URL de la vignette de l'article
	 *
	 * @param integer	$indice	L'indice de l'image recherch�e
	 * @return string	L'URL
	 */
	function getThumbUrl($indice)
	{
	    $fieldName = 'article_picture'.$indice;
		if(xoops_trim($this->getVar($fieldName)) != '') {
			return references_utils::getModuleOption('images_url').'/'.REFERENCES_THUMBS_PREFIX.$this->getVar($fieldName);
		} else {
			return REFERENCES_IMAGES_URL.'blank.gif';
		}
	}

	/**
	 * Retourne le chemin de la vignette de l'annonce
	 *
	 * @param integer	$indice	L'indice de l'image recherch�e
	 * @return string	L'URL
	 */
	function getThumbPath($indice)
	{
	    $fieldName = 'article_picture'.$indice;
		if(xoops_trim($this->getVar($fieldName)) != '') {
			return references_utils::getModuleOption('images_path').DIRECTORY_SEPARATOR.REFERENCES_THUMBS_PREFIX.$this->getVar($fieldName);
		} else {
			return '';
		}
	}

	/**
	 * Indique si le fichier attach� � un article existe
	 *
	 * @return boolean
	 */
	function attachmentExists()
	{
		$return = false;
		if(xoops_trim($this->getVar('article_attached_file')) != '' && file_exists(references_utils::getModuleOption('attached_path').DIRECTORY_SEPARATOR.$this->getVar('article_attached_file'))) {
			$return = true;
		}
		return $return;
	}

	/**
	 * Retourne l'URL du fichier attach�
	 *
	 * @return string	L'url du fichier attach� sinon une chaine vide
	 */
	function getAttachmentUrl()
	{
		if(xoops_trim($this->getVar('article_attached_file')) != '' && $this->attachmentExists()) {
			return references_utils::getModuleOption('attached_url').'/'.$this->getVar('article_attached_file');
		} else {
			return '';
		}
	}

	/**
	 * Retourne le chemin du fichier attach�
	 *
	 * @return string	Le chemin du fichier attach� sinon une chaine vide
	 */
	function getAttachmentPath()
	{
		if(xoops_trim($this->getVar('article_attached_file')) != '' && $this->attachmentExists()) {
			return references_utils::getModuleOption('attached_path').DIRECTORY_SEPARATOR.$this->getVar('article_attached_file');
		} else {
			return '';
		}
	}

	/**
	 * Supprime le fichier attach� � un article
	 */
	function deleteAttachment()
	{
		if($this->attachmentExists()) {
			@unlink(references_utils::getModuleOption('attached_path').DIRECTORY_SEPARATOR.$this->getVar('article_attached_file'));
		}
		$this->setVar('article_attached_file', '');
	}

	/**
	 * Supprime la miniature associ�e � l'article
	 *
	 * @param integer	$indice	L'indice de l'image recherch�e
	 * @return void
	 */
	function deleteThumb($indice)
	{
	    $fieldName = 'article_picture'.$indice;
		if($this->thumbExists($indice)) {
			@unlink(references_utils::getModuleOption('images_path').DIRECTORY_SEPARATOR.REFERENCES_THUMBS_PREFIX.$this->getVar($fieldName));
		}
	}

	/**
	 * Supprime l'image (et vignette) d'un article (raccourcis)
	 *
	 * @return void
	 */
	function deletePicturesAndThumbs()
	{
	    for($i=1; $i<=10; $i++) {
		    $this->deleteThumb($i);
		    $this->deletePicture($i);
	    }
	}

    /**
     * Retourne le timestamp de cr�ation format�
     *
     * @return string
     */
	function getFormatedTimeStamp()
    {
        return formatTimestamp($this->getVar('article_timestamp'), 's');
    }

    /**
     * Indique s'il existe au moins une image pour l'article
     *
     * @return boolean
     */
    function isThereAPicture()
    {
        for($i=1; $i<=10; $i++) {
            if($this->pictureExists($i)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Inidique s'il existe au moins une vignette pour l'article
     *
     * @return boolean
     */
    function isThereAThumb()
    {
        for($i=1; $i<=10; $i++) {
            if($this->thumbExists($i)) {
                return true;
            }
        }
        return false;
    }

	/**
	 * Retourne l'url pour atteindre l'�l�ment
	 *
	 * @return string
	 */
    function getUrl($shortVersion = false)
    {
    	if(!$shortVersion) {
    		return REFERENCES_URL.'reference.php?article_id='.$this->getVar('article_id');
    	} else {
    		return 'reference.php?article_id='.$this->getVar('article_id');
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
        $hrefTitle = $this->getHrefTitle();
		$ret['article_href_title'] = $hrefTitle;
        $ret['article_url'] = $this->getUrl();
		// Indique si une annonce est expir�e
		$ret['article_is_online'] = $this->isArticleOnline();
        if($this->isThereAPicture()) {
            $ret['article_picture_exists'] = true;
            for($i=1; $i<=10; $i++) {
                if($this->pictureExists($i)) {
                    $ret['article_picture_url'.$i] = $this->getPictureUrl($i);
                    $ret['article_picture_path'.$i] = $this->getPicturePath($i);
                    $ret['article_pictures_urls'][] = $this->getPictureUrl($i);
                    $ret['article_pictures_paths'][] = $this->getPicturePath($i);
                    $fieldName = 'article_picture'.$i.'_text';
                    if(xoops_trim($this->getVar($fieldName)) != '') {
                        $ret['article_pictures_texts'][] = references_utils::makeHrefTitle($this->getVar($fieldName));
                    } else {
                        $ret['article_pictures_texts'][] = $hrefTitle;
                    }
                }
            }
        } else {
            $ret['article_picture_exists'] = false;
        }
        $ret['article_short_text'] = references_utils::truncate_tagsafe($this->getVar('article_text'), REFERENCES_SHORTEN_TEXT);

		if($this->attachmentExists()) {
			$ret['article_attachment_exists'] = true;
			$ret['article_attachment_url'] = $this->getAttachmentUrl();
			$ret['article_attachment_path'] = $this->getAttachmentPath();
		} else {
			$ret['attachment_exists'] = false;
		}

		if($this->isThereAThumb()) {
			$ret['article_thumb_exists'] = true;
			for($i=1; $i<=10; $i++) {
			    if($this->thumbExists($i)) {
			        $ret['article_thumb_url'.$i] = $this->getThumbUrl($i);
			        $ret['article_thumb_path'.$i] = $this->getThumbPath($i);
                    $ret['article_thumbs_urls'][] = $this->getThumbUrl($i);
                    $ret['article_thumbs_paths'][] = $this->getThumbPath($i);
			    }
			}
		} else {
            $ret['article_thumb_exists'] = false;
		}

		$ret['article_timestamp_formated'] = $this->getFormatedTimeStamp();
		return $ret;
    }
}



class ReferencesReferences_articlesHandler extends references_XoopsPersistableObjectHandler
{
	function __construct($db)
	{	//							Table		            Classe		          Id			Descr.
		parent::__construct($db, 'references_articles', 'references_articles', 'article_id', 'article_title');
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
		if(is_array($permissions) && array_key_exists($permissionsType,$permissions)) {
			return $permissions[$permissionsType];
		}
		$categories = array();
   		$currentModule = references_utils::getModule();
   		$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
   		$gperm_handler = xoops_gethandler('groupperm');
   		$categories = $gperm_handler->getItemIds($permissionsType, $groups, $currentModule->getVar('mid'));
   		if(is_array($categories) && count($categories) > 0) {
			$permissions[$permissionsType] = new Criteria('article_category_id', '('.implode(',', $categories).')', 'IN');
   		} else {	// Ne peut rien voir
   			$permissions[$permissionsType] = new Criteria('article_category_id', '0', '=');
   		}
    	return $permissions[$permissionsType];
	}

    /**
     * Retourne les articles r�cents en tenant compte des permissions de consultation sur les cat�gories
     *
     * @param integer $start		Indice de d�but
     * @param integer $limit		Nombre d'objets � renvoyer
     * @param string $sort			Zone de tri
     * @param string $order			Sens du tri
     * @param boolean $onlyOnline	Uniquement les articles en ligne ?
     * @param integer $categoryId	Identifiant d'une cat�gorie � laquelle se limiter
     * @return array	Objets de type references_articles
     */
	function getRecentArticles($start = 0, $limit = 0, $sort = 'article_timestamp', $order= 'DESC', $onlyOnline = true, $categoryId = 0)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('article_id', 0, '<>'));
        $criteria->add($this->getPermissionsCategoriesCriteria());
        if($onlyOnline) {
            $criteria->add(new Criteria('article_online', REFERENCES_STATUS_ONLINE, '='));
        }
        if(is_array($categoryId) && count($categoryId) > 0) {
            $criteria->add(new Criteria('article_category_id', '('.implode(',', $categoryId).')', 'IN'));
        } elseif($categoryId > 0) {
            $criteria->add(new Criteria('article_category_id', $categoryId, '='));
        }
        $criteria->setStart($start);
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $criteria->setLimit($limit);
        return $this->getObjects($criteria);
    }


    /**
     * Retourne le nombre total d'articles en ligne
     *
     * @return integer
     */
    function getOnlineArticlesCount()
    {
        return $this->getCount(new Criteria('article_online', REFERENCES_STATUS_ONLINE, '='));
    }

	/**
	 * Notification de la publication d'une nouvelle r�f�rence
	 *
	 * @param object $article	L'annonce pour laquelle on fait la notification
	 */
	function notifyNewArticle(references_articles $article)
	{
		$plugins = references_plugins::getInstance();
		$plugins->fireAction(references_plugins::EVENT_ON_REFERENCE_CREATE, new references_parameters(array('reference' => $article)));
		return true;
	}

    /**
     * Effectue la suppression d'un article (et de ses images et fichier attach�)
     *
     * @param references_articles $article	L'article � supprimer
     * @return boolean	Le r�sultat de la suppression
     */
	function deleteArticle(references_articles $article)
	{
	    $article->deletePicturesAndThumbs();
	    $article->deleteAttachment();
	    return $this->delete($article, true);
	}

    /**
     * Retourne la liste des cat�gories uniques utilis�es par les r�f�rences
     *
     */
	function getDistinctCategoriesIds()
	{
	    $ret = array();
	    $sql = 'SELECT distinct(article_category_id) FROM '.$this->table;
	    $sql .= ' '.$this->getPermissionsCategoriesCriteria()->renderWhere();	// Permissions
	    $sql .= ' GROUP BY article_category_id ORDER BY article_category_id';
	    $result = $this->db->query($sql);
	    if(!$result) {
	        return $ret;
	    }
	    while ($row = $this->db->fetchArray($result)) {
	        $ret[$row['article_category_id']] = $row['article_category_id'];
	    }
		return $ret;
	}

    /**
     * Passe un article en ligne
     *
     * @param references_articles $article
     * @return boolean
     */
	function onlineArticle(references_articles $article)
	{
	    $article->setVar('article_online', REFERENCES_STATUS_ONLINE);
	    return $this->insert($article, true);
	}

    /**
     * Passe un article hors ligne
     *
     * @param references_articles $article
     * @return boolean
     */
	function offlineArticle(references_articles $article)
	{
	    $article->setVar('article_online', REFERENCES_STATUS_OFFLINE);
	    return $this->insert($article, true);
	}

	/**
	 * Indique si une r�f�rence est visible d'un utilisateur
	 *
	 * @param object $article		L'article � controler
	 * @param integer $uid			L'id de l'utilisateur � controler
	 * @return boolean
	 */
	function userCanSeeReference(references_articles $article, $uid = 0)
	{
		global $xoopsUser;
		if($uid == 0) {
			$uid = references_utils::getCurrentUserID();
		}
   		$currentModule = references_utils::getModule();
   		$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
   		$gperm_handler = xoops_gethandler('groupperm');
		return $gperm_handler->checkRight(REFERENCES_PERM_READ, $article->article_category_id, references_utils::getMemberGroups($uid), $currentModule->getVar('mid'));
	}
	

	/**
	 * Remonte un article d'un cran dans l'ordre global des articles
	 * 
	 * @param integer $currentId		L'ID de l'article courant dont on souhaite remonter l'ordre
	 * @param integer $currentOrder		L'ordre de l'�l�ment courant � remonter
	 * @return void
	 */
	function moveUp($currentId, $currentOrder)
	{
		$sql_plus = 'SELECT article_id FROM '.$this->table.' WHERE article_weight = '.(intval($currentOrder)-1);
		$res_plus = $this->db->query($sql_plus);
		if($this->db->getRowsNum($res_plus) == 0) {
			return;
		}
		$row_plus = $this->db->fetchArray($res_plus);
		
		$upd1= 'UPDATE '.$this->table.' SET article_weight = (article_weight + 1) WHERE article_id = '.$row_plus['article_id'];
		$this->db->queryF($upd1);
		
		$upd2= 'UPDATE '.$this->table.' SET article_weight = (article_weight - 1) WHERE article_id = '.intval($currentId);
		$this->db->queryF($upd2);
		$this->forceCacheClean();
	}
	
	
	/**
	 * Descend un article d'un cran dans l'ordre global des articles
	 * 
	 * @param integer $currentId		L'Id de l'article courant dont on souhaite descendre l'ordre
	 * @param integer $currentOrder		L'orde de l'�l�ment courant � remonter
	 * @return void
	 */
	function moveDown($currentId, $currentOrder)
	{
		$sql_moins = 'SELECT article_id FROM '.$this->table.' WHERE article_weight = '.(intval($currentOrder)+1);
		$res_moins = $this->db->query($sql_moins);
		if($this->db->getRowsNum($res_moins) == 0) {
			return;
		}
		
		$row_moins = $this->db->fetchArray($res_moins);
		$upd1= 'UPDATE '.$this->table.' SET article_weight = (article_weight - 1) WHERE article_id = '.$row_moins['article_id'];
		$this->db->queryF($upd1);
		
		$upd2= 'UPDATE '.$this->table.' SET article_weight =( article_weight + 1) WHERE article_id = '.intval($currentId);
		$this->db->queryF($upd2);
		$this->forceCacheClean();
	}
}
?>