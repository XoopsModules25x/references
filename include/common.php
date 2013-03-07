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
if (!defined("XOOPS_ROOT_PATH")) {
 	die("XOOPS root path not defined");
}

if( !defined("REFERENCES_DIRNAME") ) {
	define("REFERENCES_DIRNAME", 'references');
	define("REFERENCES_URL", XOOPS_URL.'/modules/'.REFERENCES_DIRNAME.'/');
	define("REFERENCES_PATH", XOOPS_ROOT_PATH.'/modules/'.REFERENCES_DIRNAME.'/');
	define("REFERENCES_IMAGES_URL", REFERENCES_URL.'images/');		// Les images du module (l'url)
	define("REFERENCES_IMAGES_PATH", REFERENCES_PATH.'images/');	// Les images du module (le chemin)
	define("REFERENCES_CLASS_PATH", REFERENCES_PATH.'class/');
	define("REFERENCES_PLUGINS_PATH", REFERENCES_PATH.'plugins/');
	define("REFERENCES_PLUGINS_URL", REFERENCES_URL.'plugins/');
	define("REFERENCES_JS_URL", REFERENCES_URL.'js/');
	define("REFERENCES_TEXTFILE1", 'references_index.txt');
	define("REFERENCES_PERM_READ", 'references_view');	// Nom de la permission de lire
}

// Chargement des handler et des autres classes
require REFERENCES_PATH.'config.php';

// La classe param�tres
require_once REFERENCES_CLASS_PATH.'references_parameters.php';

// Les classes pour les plugins
require_once REFERENCES_CLASS_PATH.'references_plugins.php';	// Classe principale
require_once REFERENCES_PLUGINS_PATH.'models'.DIRECTORY_SEPARATOR.'references_action.php';
require_once REFERENCES_PLUGINS_PATH.'models'.DIRECTORY_SEPARATOR.'references_filter.php';

require_once REFERENCES_CLASS_PATH.'references_utils.php';
require_once REFERENCES_CLASS_PATH.'PEAR.php';
require_once REFERENCES_CLASS_PATH.'references_handlers.php';

$h_references_articles = xoops_getmodulehandler('references_articles', REFERENCES_DIRNAME);
$h_references_categories = xoops_getmodulehandler('references_categories', REFERENCES_DIRNAME);
$destname = '';

// D�finition des images
if( !defined("_REFERENCES_EDIT")) {
	global $xoopsConfig;
	if (isset($xoopsConfig) && file_exists(REFERENCES_PATH.'language/'.$xoopsConfig['language'].'/main.php')) {
		require REFERENCES_PATH.'language/'.$xoopsConfig['language'].'/main.php';
	} else {
		require REFERENCES_PATH.'language/english/main.php';
	}

	$icones = array(
		'edit' => "<img src='". REFERENCES_IMAGES_URL ."edit.gif' alt='" . _REFERENCES_EDIT . "' align='middle' />",
		'delete' => "<img src='". REFERENCES_IMAGES_URL ."delete.gif' alt='" . _REFERENCES_DELETE. "' align='middle' />"
	);
}
?>