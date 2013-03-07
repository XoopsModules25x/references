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
 * Plugin charg� de publier sur twitter des messages pour indiquer :
 * 1/ La cr�ation d'un nouveau produit
 * 2/ La publication d'un nouvel article
 *
 * @since 1.81
 */
class referencesTwitterAction extends references_action
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
		$events[] = array(references_plugins::EVENT_ON_CATEGORY_CREATE,
									references_plugins::EVENT_PRIORITY_1,
									basename(__FILE__),
									__CLASS__,
									'fireNewCategory');
		return $events;
	}

	/**
	 * M�thode g�n�rique charg�e d'envoyer un texte sur un compte twitter avec une url
	 *
	 * @param string $textToSend	Le texte � envoyer
	 * @param string $mask			Le masque � utiliser
	 * @param string $elementUrl	L'url de l'�l�ment concern�
	 * @return string				Le texte qui a �t� envoy� � twitter
	 */
	private function sendTextToTwitter($textToSend, $mask, $elementUrl)
	{
		if(!defined("REFERENCES_TWITTER_PLUGIN_PATH")) {
			define("REFERENCES_TWITTER_PLUGIN_PATH", REFERENCES_PLUGINS_PATH.'actions'.DIRECTORY_SEPARATOR.'twitter'.DIRECTORY_SEPARATOR);
		}
		require_once REFERENCES_TWITTER_PLUGIN_PATH.'config.php';
		//require_once REFERENCES_TWITTER_PLUGIN_PATH.'twitter.php';
		require_once REFERENCES_TWITTER_PLUGIN_PATH.'Twitter.class.php';
		require_once REFERENCES_TWITTER_PLUGIN_PATH.'bitly.class.php';
		if(REFERENCES_BITLY_LOGIN == '') {
			return '';
		}
		$sentText = '';
		$bitly = new Bitly(REFERENCES_BITLY_LOGIN, REFERENCES_BITLY_API_KEY);
		$shortUrl = $bitly->shortenSingle($elementUrl);
		$searches = array('[itemname]', '[url]');
		$replaces = array($textToSend, $shortUrl);
		$sentText = str_replace($searches, $replaces, $mask);
		$totalLength = strlen($sentText);
		if($totalLength > REFERENCES_TWITTER_TWIT_MAX_LENGTH) {
			$tooLongOf = $totalLength - REFERENCES_TWITTER_TWIT_MAX_LENGTH;
			$searches = array('[itemname]', '[url]');
			$replaces = array(substr($textToSend, 0, strlen($textToSend) - $tooLongOf), $shortUrl);
			$sentText = str_replace($searches, $replaces, $mask);
		}
		if(trim($sentText) != '') {
//			$twitter = new Twitter(REFERENCES_TWITTER_USERNAME, REFERENCES_TWITTER_PASSWORD);
//			$twitter->setUserAgent('references');
//			$twitter->updateStatus($sentText);
			$tweet = new Twitter(REFERENCES_TWITTER_USERNAME, REFERENCES_TWITTER_PASSWORD);
			$tweet->update($sentText);
		}
		return $sentText;
	}

	/**
	 * M�thode appel�e pour indiquer qu'une nouvelle r�f�rence a �t� publi�
	 *
	 * @param object $parameters	La r�f�rence qui vient d'�tre publi�e
	 * @return void
	 */
	public function fireNewReference($parameters)
	{
		if(!defined("REFERENCES_TWITTER_PLUGIN_PATH")) {
			define("REFERENCES_TWITTER_PLUGIN_PATH", REFERENCES_PLUGINS_PATH.'actions'.DIRECTORY_SEPARATOR.'twitter'.DIRECTORY_SEPARATOR);
		}
		require_once REFERENCES_TWITTER_PLUGIN_PATH.'config.php';
		$reference = $parameters['reference'];
		self::sendTextToTwitter(utf8_encode($reference->getVar('article_title', 'n')), utf8_encode(REFERENCES_TWITTER_NEW_REFERENCE_INTRO), $reference->getUrl());
	}

	/**
	 * M�thode appel�e pour indiquer qu'une nouvelle cat�gorie de r�f�rences a �t� cr��e
	 *
	 * @param object $parameters	La cat�gorie qui vient d'�tre publi�e
	 * @return void
	 */
	function fireNewCategory($parameters)
	{
		if(!defined("REFERENCES_TWITTER_PLUGIN_PATH")) {
			define("REFERENCES_TWITTER_PLUGIN_PATH", REFERENCES_PLUGINS_PATH.'actions'.DIRECTORY_SEPARATOR.'twitter'.DIRECTORY_SEPARATOR);
		}
		require_once REFERENCES_TWITTER_PLUGIN_PATH.'config.php';
		if(trim(REFERENCES_TWITTER_NEW_CATEGORY_INTRO) != '') {
			$category = $parameters['category'];
			self::sendTextToTwitter(utf8_encode($category->getVar('category_title', 'n')), utf8_encode(REFERENCES_TWITTER_NEW_CATEGORY_INTRO), $category->getUrl());
		}
	}
}
?>