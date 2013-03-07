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

/**
 * Plugin charg� de notifier de la cr�ation d'une nouvelle cat�gorie
 *
 * @since 1.72
 */
class referencesNewcategoryAction extends references_action
{
	public static function registerEvents()
	{
		/**
		 * La liste des �v�nements trait�s par le plugin se pr�sente sous la forme d'un tableau comppos� comme ceci :
		 *
		 * Indice	Signification
		 * ----------------------
		 *	0		Ev�nement sur lequel se raccrocher (voir class/references_plugins.php::EVENT_ON_PRODUCT_CREATE
		 *	1		Priorit� du plugin (de 1 � 5)
		 *	2		Script Php � inclure
		 *	3		Classe � instancier
		 *	4		M�thode � appeler
		 */
		$events = array();
		$events[] = array(references_plugins::EVENT_ON_CATEGORY_CREATE,
									references_plugins::EVENT_PRIORITY_1,
									basename(__FILE__),
									__CLASS__,
									'fireNewCategory');
		return $events;
	}

	/**
	 * M�thode appel�e pour indiquer qu'une nouvelle cat�gorie de r�f�rences a �t� cr��e
	 *
	 * @param object $parameters	La cat�gorie qui vient d'�tre publi�e
	 * @return void
	 */
	function fireNewCategory($parameters)
	{
		$category = $parameters['category'];
		$notification_handler = xoops_gethandler('notification');
		$params = array();
		$params['CATEGORY_URL'] = $category->getUrl();
		$params['CATEGORY_NAME'] = $category->getVar('category_title');
		$notification_handler->triggerEvent('global', 0, 'new_category', $params);
	}
}
?>