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

/**
 * Plugin chargé de notifier de la création d'une nouvelle catégorie
 *
 * @since 1.72
 */
class referencesNewreferenceAction extends references_action
{
	public static function registerEvents()
	{
		/**
		 * La liste des évènements traités par le plugin se présente sous la forme d'un tableau compposé comme ceci :
		 *
		 * Indice	Signification
		 * ----------------------
		 *	0		Evènement sur lequel se raccrocher (voir class/references_plugins.php::EVENT_ON_PRODUCT_CREATE
		 *	1		Priorité du plugin (de 1 à 5)
		 *	2		Script Php à inclure
		 *	3		Classe à instancier
		 *	4		Méthode à appeler
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
	 * Méthode appelée pour indiquer qu'une nouvelle référence a été créée
	 *
	 * @param object $parameters	La référence qui vient d'être publiée
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