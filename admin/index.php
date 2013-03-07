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
 * @author 			Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * Version : $Id:
 * ****************************************************************************
 */

require_once '../../../include/cp_header.php';
require_once '../include/common.php';

require_once REFERENCES_PATH.'admin/functions.php';
require_once XOOPS_ROOT_PATH.'/class/pagenav.php';
require_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
require_once REFERENCES_PATH.'class/references_listFilter.php';
require_once REFERENCES_PATH.'admin/dbupdate.php';

if(!isset($op)) {
	$op = 'default';
}

if (isset($_POST['op'])) {
	$op = $_POST['op'];
} else {
	if ( isset($_GET['op'])) {
    	$op = $_GET['op'];
	}
}

// Vérification de l'existence et de l'état d'écriture des différents répertoire de stockage et de cache
references_utils::prepareFolder(REFERENCES_CACHE_PATH);
references_utils::prepareFolder(references_utils::getModuleOption('attached_path'));
references_utils::prepareFolder(references_utils::getModuleOption('images_path'));

// Lecture de certains paramètres de l'application ********************************************************************
$limit = references_utils::getModuleOption('items_admin_page');	// Nombre maximum d'éléments à afficher
$baseurl = REFERENCES_URL.'admin/'.basename(__FILE__);	// URL de ce script
$conf_msg = references_utils::javascriptLinkConfirm(_AM_REFERENCES_CONF_DELITEM);
$defaultSortField = references_utils::getModuleOption('admin_sort_field');
$defaultSortOrder = references_utils::getModuleOption('admin_sort_order');

$thumbs_width = references_utils::getModuleOption('thumbs_width');
$thumbs_height = references_utils::getModuleOption('thumbs_height');
$destname = '';
$handlers = references_handler::getInstance();

/**
 * Affichage du pied de page de l'administration
 *
 * PLEASE, KEEP THIS COPYRIGHT *INTACT* !
 */
function show_footer()
{
	echo "<br /><br /><div align='center'><a href='http://www.instant-zero.com' target='_blank' title='Instant Zero'><img src='../images/instantzero.gif' alt='Instant Zero' /></a></div>";
}

references_utils::loadLanguageFile('modinfo.php');
references_utils::loadLanguageFile('main.php');

// ******************************************************************************************************************************************
// **** Main ********************************************************************************************************************************
// ******************************************************************************************************************************************
switch ($op) {

	// ****************************************************************************************************************
	case 'default':	// Gestion des articles
	case 'articles':
	// ****************************************************************************************************************
        xoops_cp_header();
        references_adminMenu(0);
		$objet = 'articles';
		$items = array();
		if(isset($_GET['move'])) {
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			$ordre = isset($_GET['ordre']) ? intval($_GET['ordre']) : 0;
			if($_GET['move'] == 'up' && $id > 0) {
				$handlers->h_references_articles->moveUp($id, $ordre);
			}
			if($_GET['move'] == 'down' && $id > 0) {
				$handlers->h_references_articles->moveDown($id, $ordre);
			}
		}
		$form = "<form method='post' action='$baseurl' name='frmadd$objet' id='frmadd$objet'><input type='hidden' name='op' id='op' value='add$objet' /><input type='submit' name='btngo' id='btngo' value='"._AM_REFERENCES_ADD_ITEM."' /></form>";
		echo $form;
		$categoriesList = $h_references_categories->getListArray();
		$upText = _AM_REFERENCES_UP;
		$downText = _AM_REFERENCES_DOWN;
		$upImg = "<a href='$baseurl?op=articles&move=up&id=%d&ordre=%d' title=\"$upText\"><img src='".REFERENCES_IMAGES_URL."up.png' alt=\"$upText\" /></a>";
		$downImg = "<a href='$baseurl?op=articles&move=down&id=%d&ordre=%d' title=\"$downText\"><img src='".REFERENCES_IMAGES_URL."down.png' alt=\"$downText\" /></a>";
		
		$referencesFilter = new references_listFilter($h_references_articles, 'op', 'articles', 'start', $limit, $baseurl, $defaultSortField, $defaultSortOrder, true, REFERENCES_JS_URL);
		$referencesFilter->initFilter('article_id', array('dataType' => references_listFilter::FILTER_DATA_NUMERIC, 'fieldType' => references_listFilter::FILTER_FIELD_TEXT, 'size' => 5, 'maxLength' => 10));
		$referencesFilter->initFilter('article_title', array('dataType' => references_listFilter::FILTER_DATA_TEXT, 'fieldType' => references_listFilter::FILTER_FIELD_TEXT, 'size' => 35, 'maxLength' => 255, 'autoComplete' => true));
		$referencesFilter->initFilter('article_weight', array('dataType' => references_listFilter::FILTER_DATA_NUMERIC, 'fieldType' => references_listFilter::FILTER_FIELD_TEXT, 'size' => 5, 'maxLength' => 10));
		
		$referencesFilter->initFilter('article_category_id', array('dataType' => references_listFilter::FILTER_DATA_NUMERIC, 'fieldType' => references_listFilter::FILTER_FIELD_SELECT, 'values' => $categoriesList, 'withNull' => true, 'style' => "width: 170px; max-width: 170px;"));
		$referencesFilter->initFilter('article_online', array('dataType' => references_listFilter::FILTER_DATA_NUMERIC, 'fieldType' => references_listFilter::FILTER_FIELD_SELECT, 'values' => array(2 => _YES, 1=> _NO), 'withNull' => true, 'minusOne' => true));
		$sortFields = array('article_id' => _AM_REFERENCES_ID, 'article_title' => _AM_REFERENCES_TITLE, 'article_weight' => _AM_REFERENCES_WEIGHT,  'article_category_id' => _AM_REFERENCES_CATEGORY, 'article_online' => _AM_REFERENCES_ONLINE);
		$referencesFilter->setSortFields($sortFields);

        $referencesFilter->filter();
		$itemsCount = $referencesFilter->getCount();
		references_utils::htitle(_MI_REFERENCES_ADMENU0.' ('.$itemsCount.')', 4);

		if($itemsCount > $limit) {
			$pagenav = $referencesFilter->getPager();
		}

		$items = $referencesFilter->getObjects();
		$visibleCountItems = count($items);
		$counter = 0; 
        $categories = $h_references_categories->getListArray();
		echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
		echo "<tr>\n";
		echo "<form method='post' action='$baseurl'>\n";
		echo "<td colspan='4' align='right'>".$referencesFilter->getSortPlaceHolderHtmlCode();
		echo $referencesFilter->getClearFilterbutton();
		echo "</td>\n";
		echo "<td colspan='2' align='right'>";
		if(isset($pagenav) && is_object($pagenav)) {
			echo $pagenav->renderNav();
		}
		echo "</td>\n</tr>\n";

		echo "<th align='center'>"._AM_REFERENCES_ID."</th><th align='center'>"._AM_REFERENCES_TITLE."</th><th align='center'>"._AM_REFERENCES_WEIGHT."</th><th align='center'>"._AM_REFERENCES_CATEGORY."</th><th align='center'>"._AM_REFERENCES_ONLINE."</th><th align='center'>"._AM_REFERENCES_MANUAL_DATE."</th><th align='center'>"._AM_REFERENCES_ACTION."</th></tr>";
		// Filtres ****************************************
		echo "<tr>\n";
		echo "<th align='center'>".$referencesFilter->getFilterField('article_id')."</th>\n";
		echo "<th align='center'>".$referencesFilter->getFilterField('article_title')."</th>\n";
		echo "<th align='center'>".$referencesFilter->getFilterField('article_weight')."</th>\n";
		echo "<th align='center'>".$referencesFilter->getFilterField('article_category_id')."</th>\n";
		echo "<th align='center'>".$referencesFilter->getFilterField('article_online')."</th>\n";
		echo "<th align='center'>&nbsp;</th>\n";
		echo "<th align='center'>".$referencesFilter->getGoButton()."</th></form></tr>\n";
        // ************************************************
		$class = '';
		foreach ($items as $item) {
			$counter++;
			$class = ($class == 'even') ? 'odd' : 'even';
			$id = $item->getVar('article_id');
			$action_edit = "<a href='$baseurl?op=edit".$objet."&id=".$id."' title='"._EDIT."'>".$icones['edit'].'</a>';
			$action_delete = "<a href='$baseurl?op=delete".$objet."&id=".$id."' title='"._DELETE."'".$conf_msg.">".$icones['delete'].'</a>';
            $category = isset($categories[$item->getVar('article_category_id')]) ? $categories[$item->getVar('article_category_id')] : '';
            $up = $down = '';
			echo "<tr class='".$class."'>\n";
				$ordre = $item->getVar('article_weight');
				if($counter == 1 && $visibleCountItems > 1) { // Premier élément
					$down = sprintf($downImg, $id, $ordre);
				}
				if($counter == $visibleCountItems && $visibleCountItems > 1) { // Dernier élément
					$up = sprintf($upImg, $id, $ordre);
				}
				if($counter > 1 & $counter < $visibleCountItems && $visibleCountItems > 1) { // Element dans le milieu
					$up = sprintf($upImg, $id, $ordre);
					$down = sprintf($downImg, $id, $ordre);
				}				
				
			    echo "<td align='center'>".$id."</td>";
			    echo "<td align='left'><a target='_blank' href='".$item->getUrl()."'>".$item->getVar('article_title')."</a></td>";
			    echo "<td align='center'>".$item->getVar('article_weight')." $up $down</td>";
			    echo "<td align='center'>".$category."</td>";
			    if($item->isArticleOnline()) {
			        $statusLink = "<a href='$baseurl?op=offline&id=$id' title='"._AM_REFERENCES_GO_OFFLINE."'><img src='".REFERENCES_IMAGES_URL."status_online.png' alt='"._AM_REFERENCES_GO_OFFLINE."' /></a>";
			    } else {
			        $statusLink = "<a href='$baseurl?op=online&id=$id' title='"._AM_REFERENCES_GO_ONLINE."'><img src='".REFERENCES_IMAGES_URL."status_offline.png' alt='"._AM_REFERENCES_GO_ONLINE."' /></a>";
			    }
			    echo "<td align='center'>".$statusLink."</td>";
			    echo "<td align='center'>".$item->getVar('article_date')."</td>";
			    echo "<td align='center'>".$action_edit.' '.$action_delete."</td>\n";
			echo "<tr>\n";
		}
		$class = ($class == 'even') ? 'odd' : 'even';
		echo "<tr class='".$class."'>\n";
		echo "<td colspan='7' align='center'>".$form."</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo $referencesFilter->getJavascriptInitCode();
		if(isset($pagenav) && is_object($pagenav)) {
			echo "<div align='center'>".$pagenav->renderNav()."</div>";
		}
		echo "<br /><br />\n";
        show_footer();
		break;

	// ****************************************************************************************************************
	case 'addarticles':	    // Ajout d'un article
	case 'editarticles':	// Edition d'un article
	// ****************************************************************************************************************
        xoops_cp_header();
        references_adminMenu(0);
        $object = 'articles';
		if($op == 'edit'.$object) {
			$title = _AM_REFERENCES_EDIT_ARTICLE;
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			if(empty($id)) {
				references_utils::redirect(_AM_REFERENCES_ERROR_1, $baseurl, 5);
			}
			// Item exits ?
			$item = null;
			$item = $h_references_articles->get($id);
			if(!is_object($item)) {
				references_utils::redirect(_AM_REFERENCES_NOT_FOUND, $baseurl, 5);
			}
			$edit = true;
			$label_submit = _AM_REFERENCES_MODIFY;
		} else {
			$title = _AM_REFERENCES_ADD_ARTICLE;
			$item = $h_references_articles->create(true);
			$item->setVar('article_online', true);
			if(REFERENCES_AUTO_FILL_MANUAL_DATE) {
			    $item->setVar('article_date', formatTimestamp(time(), 's'));
			}
			$label_submit = _AM_REFERENCES_ADD;
			$edit = false;
		}
		$sform = new XoopsThemeForm($title, 'frmadd'.$object, $baseurl);
		$sform->setExtra('enctype="multipart/form-data"');
		$sform->addElement(new XoopsFormHidden('op', 'saveedit'.$object));
		$sform->addElement(new XoopsFormHidden('article_id', $item->getVar('article_id')));
		$categories = $h_references_categories->getListArray();
		$categoriesList = new XoopsFormSelect(_AM_REFERENCES_CATEGORY, 'article_category_id', $item->getVar('article_category_id', 'e'));
		$categoriesList->addOptionArray($categories);
		$sform->addElement($categoriesList, true);

        $sform->addElement(new XoopsFormText(_AM_REFERENCES_TITLE,'article_title', 50, 255, $item->getVar('article_title', 'e')), true);
        $sform->addElement(new XoopsFormRadioYN(_AM_REFERENCES_ONLINE, 'article_online', $item->getVar('article_online', 'e')), true);
        $sform->addElement(new XoopsFormText(_AM_REFERENCES_MANUAL_DATE,'article_date', 30, 30, $item->getVar('article_date', 'e')), false);
        $sform->addElement(new XoopsFormTextDateSelect(_AM_REFERENCES_DATE, 'article_timestamp', 15, $item->getVar('article_timestamp', 'e')));
        $sform->addElement(new XoopsFormText(_AM_REFERENCES_WEIGHT, 'article_weight', 5, 5, $item->getVar('article_weight', 'e')), false);
        $sform->addElement(new XoopsFormText(_AM_REFERENCES_URL, 'article_externalurl', 50, 255, $item->getVar('article_externalurl', 'e')), false);

		$editor = references_utils::getWysiwygForm(_AM_REFERENCES_TEXT, 'article_text', $item->getVar('article_text','e'), 15, 60, 'article_text_hidden');
		if($editor) {
			$sform->addElement($editor, false);
		}

		$editor1 = references_utils::getWysiwygForm(_AM_REFERENCES_TEXT_MORE, 'article_readmore', $item->getVar('article_readmore','e'), 15, 60, 'article_readmore_hidden');
		if($editor1) {
			$sform->addElement($editor1, false);
		}
		
        if(references_utils::getModuleOption('use_tags') && references_utils::tagModuleExists()) {
            require_once XOOPS_ROOT_PATH.'/modules/tag/include/formtag.php';
            $sform->addElement(new XoopsFormTag('item_tag', 60, 255, $item->getVar('article_id'), 0));
        }
        // Images
		for($i=1; $i<=10; $i++) {
		    if( $op == 'edit'.$object && $item->pictureExists($i) ) {
    			$pictureTray = new XoopsFormElementTray(_AM_REFERENCES_CURRENT_PICTURE.' '.$i ,'<br />');
			    $pictureTray->addElement(new XoopsFormLabel('', "<img src='".$item->getPictureUrl($i)."' alt='' border='0' />"));
			    $deleteCheckbox = new XoopsFormCheckBox('', 'delpicture'.$i);
			    $deleteCheckbox->addOption(1, _DELETE);
			    $pictureTray->addElement($deleteCheckbox);
   			    $sform->addElement($pictureTray);
			    unset($pictureTray, $deleteCheckbox);
		    }
		    $sform->addElement(new XoopsFormFile(_AM_REFERENCES_IMAGE.' '.$i, 'attachedfile'.$i, references_utils::getModuleOption('maxuploadsize')), false);
		    $fieldName = 'article_picture'.$i.'_text';
		    $sform->addElement(new XoopsFormText(_AM_REFERENCES_PICTURE_TEXT.' '.$i, $fieldName, 50, 255, $item->getVar($fieldName)), false);
		}

		// Fichier attaché
		if( $op == 'edit'.$object && $item->attachmentExists()) {
			$attachedTray = new XoopsFormElementTray(_AM_REFERENCES_ATTACHED_FILE,'<br />');
			$attachedTray->addElement(new XoopsFormLabel('', "<a href='".$item->getAttachmentUrl()."' target='_blank'>".$item->getVar('article_attached_file')."</a>"));
			$deleteCheckbox = new XoopsFormCheckBox('', 'delattach');
			$deleteCheckbox->addOption(1, _DELETE);
			$attachedTray->addElement($deleteCheckbox);
			$sform->addElement($attachedTray);
			unset($attachedTray, $deleteCheckbox);
		}
		$sform->addElement(new XoopsFormFile(_AM_REFERENCES_ATTACHED_FILE , 'article_attached_file', references_utils::getModuleOption('maxuploadsize')), false);

		$button_tray = new XoopsFormElementTray('' ,'');
		$submit_btn = new XoopsFormButton('', 'post', $label_submit, 'submit');
		$button_tray->addElement($submit_btn);
		$sform->addElement($button_tray);
		$sform = references_utils::formMarkRequiredFields($sform);
		$sform->display();
		show_footer();
		break;


	// ****************************************************************************************************************
	case 'saveeditarticles':	// Sauvegarde d'un article
	// ****************************************************************************************************************
		xoops_cp_header();
		$id = isset($_POST['article_id']) ? intval($_POST['article_id']) : 0;
		$opRedirect = 'articles';
		if(!empty($id)) {
			$edit = true;
			$item = $h_references_articles->get($id);
			if(!is_object($item)) {
				references_utils::redirect(_AM_REFERENCES_NOT_FOUND, $baseurl, 5);
			}
			$item->unsetNew();
		} else {
			$edit = false;
			$item= $h_references_articles->create(true);
		}

		$item->setVars($_POST);

		// Images
		for($i=1; $i<=10; $i++) {
		    if(isset($_POST['delpicture'.$i]) && intval($_POST['delpicture'.$i]) == 1) {
    			$item->deletePicture($i);
			    $item->setVar('article_picture'.$i, '');
		    }

		    // Upload de l'image et création de la vignette
		    $destname = '';
		    $return = references_utils::uploadFile($i-1, references_utils::getModuleOption('images_path'));
		    if($return === true) {
		        if(references_utils::getModuleOption('images_width') > 0 && references_utils::getModuleOption('images_height') > 0) {
                    references_utils::createThumb(references_utils::getModuleOption('images_path').'/'.basename($destname), references_utils::getModuleOption('images_path').'/'.basename($destname), references_utils::getModuleOption('images_width'), references_utils::getModuleOption('images_height'), true);
		        }
    			$newDestName = references_utils::getModuleOption('images_path').DIRECTORY_SEPARATOR.REFERENCES_THUMBS_PREFIX.basename($destname);
			    $retval = references_utils::resizePicture(references_utils::getModuleOption('images_path').'/'.basename($destname), $newDestName, $thumbs_width, $thumbs_height, true);
			    if($retval == 1 || $retval == 3) {
    				$item->setVar('article_picture'.$i, $destname);
			    }
		    } else {
    			if($return !== false) {
				    echo $return;
			    }
		    }
		}

		$timestamp = mktime(0,0,0,intval(substr($_POST['article_timestamp'], 5, 2)), intval(substr($_POST['article_timestamp'], 8, 2)), intval(substr($_POST['article_timestamp'], 0, 4)));
        $item->setVar('article_timestamp', $timestamp);

		if(!$edit) {
			$item->setVar('article_author', references_utils::getCurrentUserID());
		}

		// Suppression éventuelle du fichier attaché
		if(isset($_POST['delattach']) && intval($_POST['delattach']) == 1) {
			$item->deleteAttachment();
		}

		$destname = '';
		// Upload de la pièce jointe
		$return = references_utils::uploadFile(10, references_utils::getModuleOption('attached_path'));
		if($return === true) {
			$item->setVar('article_attached_file', $destname);
		} else {
			if($return !== false) {
				echo $return;
			}
		}

		$res = $h_references_articles->insert($item);
		if($res) {
		    if(references_utils::getModuleOption('use_tags') && references_utils::tagModuleExists()) {
                $tag_handler = xoops_getmodulehandler('tag', 'tag');
                $tag_handler->updateByItem($_POST['item_tag'], $item->getVar('article_id'), $xoopsModule->getVar('dirname'), 0);
		    }
			if(!$edit) {
				$h_references_articles->notifyNewArticle($item);
			}
			references_utils::updateCache();
			references_utils::redirect(_AM_REFERENCES_SAVE_OK, $baseurl.'?op='.$opRedirect, 2);
		} else {
			references_utils::redirect(_AM_REFERENCES_SAVE_PB, $baseurl.'?op='.$opRedirect,5);
		}
		break;

    // ****************************************************************************************************************
	case 'offline':    // Mise hors ligne d'un article
    case 'online':    // Mise en ligne d'un article
    // ****************************************************************************************************************
        xoops_cp_header();
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if(empty($id)) {
			references_utils::redirect(_AM_REFERENCES_ERROR_1, $baseurl, 5);
		}
		$opRedirect = 'articles';
		$item = null;
		$item = $h_references_articles->get($id);
		if(is_object($item)) {
		    if($op == 'offline') {
		        $res = $h_references_articles->offlineArticle($item);
		    } else {
		        $res = $h_references_articles->onlineArticle($item);
		    }
			if($res) {
				references_utils::updateCache();
				references_utils::redirect(_AM_REFERENCES_SAVE_OK, $baseurl.'?op='.$opRedirect,2);
			}
		}
		references_utils::redirect(_AM_REFERENCES_NOT_FOUND, $baseurl.'?op='.$opRedirect,5);
        break;

	// ****************************************************************************************************************
	case 'deletearticles':	// Suppression d'un article
	// ****************************************************************************************************************
        xoops_cp_header();
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if(empty($id)) {
			references_utils::redirect(_AM_REFERENCES_ERROR_1, $baseurl, 5);
		}
		$opRedirect = 'articles';
		$item = null;
		$item = $h_references_articles->get($id);
		if(is_object($item)) {
			$res = $h_references_articles->deleteArticle($item, true);
			if($res) {
				references_utils::updateCache();
				references_utils::redirect(_AM_REFERENCES_SAVE_OK, $baseurl.'?op='.$opRedirect,2);
			}
		}
		references_utils::redirect(_AM_REFERENCES_NOT_FOUND, $baseurl.'?op='.$opRedirect,5);
		break;


	// ****************************************************************************************************************
	case 'instant-zero';	// Publicité
	// ****************************************************************************************************************
        xoops_cp_header();
        references_adminMenu(4);
		echo "<iframe src='http://www.instant-zero.com/modules/liaise/?form_id=2' width='100%' height='600' frameborder='0'></iframe>";
		show_footer();
		break;

	// ****************************************************************************************************************
	case 'perms';	// Permissions
	// ****************************************************************************************************************
        xoops_cp_header();
        references_adminMenu(3);
		require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';
		$categories = $handlers->h_references_categories->getCategories();
		$permissionsForm = new XoopsGroupPermForm(_AM_REFERENCES_VIEWFORM, $xoopsModule->getVar('mid'), REFERENCES_PERM_READ, _AM_REFERENCES_VIEWFORM_DESC, 'admin/index.php?op=perms', 'true');
		foreach($categories as $category) {
			$permissionsForm->addItem($category->category_id, $category->category_title, 0);
		}
		echo $permissionsForm->render();
		echo "<br /><br /><br /><br />\n";
		unset($permissionsForm);
		show_footer();
		break;

	// ****************************************************************************************************************
	case 'texts':	// Gestion des textes
	// ****************************************************************************************************************
        xoops_cp_header();
        references_adminMenu(2);
		require_once REFERENCES_PATH.'class/registryfile.php';
		$registry = new references_registryfile();

		$sform = new XoopsThemeForm(_MI_REFERENCES_ADMENU1, 'frmatxt', $baseurl);
		$sform->addElement(new XoopsFormHidden('op', 'savetexts'));
		// Texte à afficher sur la page d'index du module
		$editor1 = references_utils::getWysiwygForm(_AM_REFERENCES_TEXT1, 'text1', $registry->getfile(REFERENCES_TEXTFILE1), 5, 60, 'hometext1_hidden');
		if($editor1) {
			$sform->addElement($editor1, false);
		}
		$button_tray = new XoopsFormElementTray('' ,'');
		$submit_btn = new XoopsFormButton('', 'post', _AM_REFERENCES_MODIFY, 'submit');
		$button_tray->addElement($submit_btn);
		$sform->addElement($button_tray);
		$sform = references_utils::formMarkRequiredFields($sform);
		$sform->display();
		show_footer();
		break;


	// ****************************************************************************************************************
	case 'savetexts':		// Sauvegarde des textes
	// ****************************************************************************************************************
		xoops_cp_header();
	    require_once REFERENCES_PATH.'class/registryfile.php';
		$registry = new references_registryfile();
		$myts = &MyTextSanitizer::getInstance();
		$registry->savefile($myts->stripSlashesGPC($_POST['text1']), REFERENCES_TEXTFILE1);
		references_utils::updateCache();
		references_utils::redirect(_AM_REFERENCES_SAVE_OK, $baseurl.'?op=texts', 2);
		break;

	// ****************************************************************************************************************
	case 'maintain':	// Maintenance des tables
	// ****************************************************************************************************************
    	xoops_cp_header();
    	references_adminMenu();
		references_utils::maintainTablesCache();
		references_utils::redirect(_AM_REFERENCES_SAVE_OK, $baseurl, 2);
    	break;

	// ****************************************************************************************************************
    case 'categories':    // Gestion des catégories
    // ****************************************************************************************************************
        xoops_cp_header();
        references_adminMenu(1);
        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
		$objet = 'categories';
		$items = array();
		$form = "<form method='post' action='$baseurl' name='frmadd$objet' id='frmadd$objet'><input type='hidden' name='op' id='op' value='add$objet' /><input type='submit' name='btngo' id='btngo' value='"._AM_REFERENCES_ADD_ITEM."' /></form>";
		echo $form;
		references_utils::htitle(_MI_REFERENCES_ADMENU2, 4);

		$itemsCount = $h_references_categories->getCount();
		if($itemsCount > $limit) {
			$pagenav = new XoopsPageNav($itemsCount, $limit, $start, 'start');
		}

		$items = $h_references_categories->getCategories($start, $limit);
		if(isset($pagenav) && is_object($pagenav)) {
			echo "<div align='right'>".$pagenav->renderNav().'</div>';
		}
		echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";

		echo "<tr><th align='center'>"._AM_REFERENCES_ID."</th><th align='center'>"._AM_REFERENCES_TITLE."</th><th align='center'>"._AM_REFERENCES_CATEGORY_WEIGHT."</th><th align='center'>"._AM_REFERENCES_ACTION."</th></tr>";

		$class = '';
		foreach ($items as $item) {
			$class = ($class == 'even') ? 'odd' : 'even';
			$id = $item->getVar('category_id');
			$action_edit = "<a href='$baseurl?op=edit".$objet."&id=".$id."' title='"._EDIT."'>".$icones['edit'].'</a>';
			$action_delete = "<a href='$baseurl?op=delete".$objet."&id=".$id."' title='"._DELETE."'".$conf_msg.">".$icones['delete'].'</a>';

			echo "<tr class='".$class."'>\n";
			    echo "<td align='center'>".$id."</td>";
			    echo "<td align='left'><a target='_blank' href='".$item->getUrl()."'>".$item->getVar('category_title')."</a></td>";
			    echo "<td align='right'>".$item->getVar('category_weight')."</td>";
			    echo "<td align='center'>".$action_edit.' '.$action_delete."</td>\n";
			echo "<tr>\n";
		}
		$class = ($class == 'even') ? 'odd' : 'even';
		echo "<tr class='".$class."'>\n";
		echo "<td colspan='4' align='center'>".$form."</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		if(isset($pagenav) && is_object($pagenav)) {
			echo "<div align='center'>".$pagenav->renderNav()."</div>";
		}
		echo "<br /><br />\n";
        show_footer();
		break;

    // ****************************************************************************************************************
	case 'addcategories':	    // Ajout d'une catégorie
	case 'editcategories':	    // Edition d'une categories
	// ****************************************************************************************************************
        xoops_cp_header();
        references_adminMenu(1);
        $object = 'categories';
		if($op == 'edit'.$object) {
			$title = _AM_REFERENCES_EDIT_CATEGORY;
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			if(empty($id)) {
				references_utils::redirect(_AM_REFERENCES_ERROR_1, $baseurl, 5);
			}
			// Item exits ?
			$item = null;
			$item = $h_references_categories->get($id);
			if(!is_object($item)) {
				references_utils::redirect(_AM_REFERENCES_NOT_FOUND, $baseurl, 5);
			}
			$edit = true;
			$label_submit = _AM_REFERENCES_MODIFY;
		} else {
			$title = _AM_REFERENCES_ADD_CATEGORY;
			$item = $h_references_categories->create(true);
			$label_submit = _AM_REFERENCES_ADD;
			$edit = false;
		}
		$sform = new XoopsThemeForm($title, 'frmadd'.$object, $baseurl);
		$sform->addElement(new XoopsFormHidden('op', 'saveedit'.$object));
		$sform->addElement(new XoopsFormHidden('category_id', $item->getVar('category_id')));
        $sform->addElement(new XoopsFormText(_AM_REFERENCES_TITLE,'category_title', 50, 255, $item->getVar('category_title', 'e')), true);
        $sform->addElement(new XoopsFormText(_AM_REFERENCES_CATEGORY_WEIGHT,'category_weight', 10, 10, $item->getVar('category_weight', 'e')), true);
		$editor = references_utils::getWysiwygForm(_AM_REFERENCES_DESCRIPTION, 'category_description', $item->getVar('category_description','e'), 15, 60, 'category_description_hidden');
		if($editor) {
			$sform->addElement($editor, false);
		}

		// Permissions
    	$membersHandler = & xoops_gethandler('member');
    	$allGroupsList = &$membersHandler->getGroupList();
    	$permHandler = &xoops_gethandler('groupperm');
    	$allGroupsIds = array_keys($allGroupsList);

		$groupsIds = array();
    	if($edit) {
	    	$groupsIds = $permHandler->getGroupIds(REFERENCES_PERM_READ, $item->getVar('category_id'), $xoopsModule->getVar('mid'));
    		$groupsIds = array_values($groupsIds);
    		$groupsThatCanViewCheckbox = new XoopsFormCheckBox(_AM_REFERENCES_VIEWFORM, 'groups_references_can_view[]', $groupsIds);
    	} else {
	    	$groupsThatCanViewCheckbox = new XoopsFormCheckBox(_AM_REFERENCES_VIEWFORM, 'groups_references_can_view[]', $allGroupsIds);
    	}
    	$groupsThatCanViewCheckbox->addOptionArray($allGroupsList);
    	$sform->addElement($groupsThatCanViewCheckbox);
		// *****

		$button_tray = new XoopsFormElementTray('' ,'');
		$submit_btn = new XoopsFormButton('', 'post', $label_submit, 'submit');
		$button_tray->addElement($submit_btn);
		$sform->addElement($button_tray);
		$sform = references_utils::formMarkRequiredFields($sform);
		$sform->display();
		show_footer();
		break;


	// ****************************************************************************************************************
	case 'saveeditcategories':	// Sauvegarde d'une catégorie
	// ****************************************************************************************************************
		xoops_cp_header();
		$id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
		$opRedirect = 'categories';
		if(!empty($id)) {
			$edit = true;
			$item = $h_references_categories->get($id);
			if(!is_object($item)) {
				references_utils::redirect(_AM_REFERENCES_NOT_FOUND, $baseurl, 5);
			}
			$item->unsetNew();
		} else {
			$edit = false;
			$item= $h_references_categories->create(true);
		}

		$item->setVars($_POST);

		$res = $h_references_categories->insert($item);
		if($res) {
			// Permissions
			// Suppression des permissions actuelles
			$gperm_handler = &xoops_gethandler('groupperm');
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria('gperm_itemid', $item->category_id, '='));
			$criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid'),'='));
			$criteria->add(new Criteria('gperm_name', REFERENCES_PERM_READ, '='));
			$gperm_handler->deleteAll($criteria);
			// Sauvegarde des nouvelles permissions, si elles existente
			if(isset($_POST['groups_references_can_view'])) {
				foreach($_POST['groups_references_can_view'] as $groupId) {
					$gperm_handler->addRight(REFERENCES_PERM_READ, $item->category_id, $groupId, $xoopsModule->getVar('mid'));
				}
			}
			// ****
			if(!$edit) {
				$h_references_categories->notifyNewCategory($item);
			}
			references_utils::updateCache();
			references_utils::redirect(_AM_REFERENCES_SAVE_OK, $baseurl.'?op='.$opRedirect, 2);
		} else {
			references_utils::redirect(_AM_REFERENCES_SAVE_PB, $baseurl.'?op='.$opRedirect,5);
		}
		break;


	// ****************************************************************************************************************
	case 'deletecategories':	// Suppression d'une catégorie
	// ****************************************************************************************************************
        xoops_cp_header();
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if(empty($id)) {
			references_utils::redirect(_AM_REFERENCES_ERROR_1, $baseurl, 5);
		}
		$opRedirect = 'categories';
		$item = null;
		$item = $h_references_categories->get($id);
		if(is_object($item)) {
			$res = $h_references_categories->delete($item, true);
			if($res) {
				references_utils::updateCache();
				references_utils::redirect(_AM_REFERENCES_SAVE_OK, $baseurl.'?op='.$opRedirect,2);
			}
		}
		references_utils::redirect(_AM_REFERENCES_NOT_FOUND, $baseurl.'?op='.$opRedirect,5);
		break;

	// ****************************************************************************************************************
	case 'autocomplete':	// Ajax, autocomplétion
	// ****************************************************************************************************************
        if(!isset($xoopsUser) || !is_object($xoopsUser)) {
        	exit;
        }
        if(!references_utils::isAdmin()) {
        	exit;
        }
		error_reporting(0);
        @$xoopsLogger->activated = false;
        $handler = isset($_REQUEST['handler']) ? $_REQUEST['handler'] : '';
        if($handler != '') {
        	switch ($handler) {
        		case 'references_articles':
					$referencesFilter = new references_listFilter($h_references_articles, 'op', 'articles', 'start', $limit, $baseurl, 'article_title', 'ASC', true, REFERENCES_JS_URL);
					$referencesFilter->initFilter('article_title', array('dataType' => references_listFilter::FILTER_DATA_TEXT, 'fieldType' => references_listFilter::FILTER_FIELD_TEXT, 'size' => 35, 'maxLength' => 255, 'autoComplete' => true));
					echo utf8_encode($referencesFilter->autoComplete($_REQUEST['q'], $_REQUEST['limit'], $_REQUEST['field']));
					break;
        	}
        }
        exit;
        break;
}
xoops_cp_footer();
?>