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
class referencesNewreferenceAction extends references_action
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
		$events[] = array(references_plugins::EVENT_ON_REFERENCE_CREATE,
									references_plugins::EVENT_PRIORITY_1,
									basename(__FILE__),
									__CLASS__,
									'fireNewReference');
		return $events;
	}

	/**
	 * M�thode appel�e pour indiquer qu'une nouvelle r�f�rence a �t� cr��e
	 *
	 * @param object $parameters	La r�f�rence qui vient d'�tre publi�e
	 * @return void
	 */
	function fireNewReference($parameters)
	{
		$article = $parameters['reference'];
		$notification_handler = xoops_gethandler('notification');
		$articleForTemplate = array();
		$originalArticle = $article->toArray('n');

		foreach($originalArticle as $key => $value) {
			@$articleForTemplate[strtoupper($key)] = strip_tags($value);
		}
		$articleForTemplate['REFERENCES_URL'] = $article->getUrl();
		$articleForTemplate['ARTICLE_SHORT_TEXT'] = references_utils::truncate_tagsafe($article->getVar('article_text'), REFERENCES_SHORTEN_TEXT);
		$notification_handler->triggerEvent('global', 0, 'new_article', $articleForTemplate);
	}
}
?>