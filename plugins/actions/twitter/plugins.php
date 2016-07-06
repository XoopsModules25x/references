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
 * @copyright         Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * @license           http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package           references
 * @author            Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * ****************************************************************************
 */

/**
 * Plugin chargé de publier sur twitter des messages pour indiquer :
 * 1/ La création d'un nouveau produit
 * 2/ La publication d'un nouvel article
 *
 * @since 1.81
 */
class referencesTwitterAction extends references_action
{
    public function registerEvents()
    {
        /**
         * La liste des évènements traités par le plugin se présente sous la forme d'un tableau compposé comme ceci :
         *
         * Indice    Signification
         * ----------------------
         *    0        Evènement sur lequel se raccrocher (voir class/references_plugins.php::EVENT_ON_PRODUCT_CREATE
         *    1        Priorité du plugin (de 1 à 5)
         *    2        Script Php à inclure
         *    3        Classe à instancier
         *    4        Méthode à appeler
         */
        $events   = array();
        $events[] = array(
            references_plugins::EVENT_ON_REFERENCE_CREATE,
            references_plugins::EVENT_PRIORITY_1,
            basename(__FILE__),
            __CLASS__,
            'fireNewReference'
        );
        $events[] = array(
            references_plugins::EVENT_ON_CATEGORY_CREATE,
            references_plugins::EVENT_PRIORITY_1,
            basename(__FILE__),
            __CLASS__,
            'fireNewCategory'
        );
        return $events;
    }

    /**
     * Méthode générique chargée d'envoyer un texte sur un compte twitter avec une url
     *
     * @param string $textToSend Le texte à envoyer
     * @param string $mask       Le masque à utiliser
     * @param string $elementUrl L'url de l'élément concerné
     * @return string                Le texte qui a été envoyé à twitter
     */
    private function sendTextToTwitter($textToSend, $mask, $elementUrl)
    {
        if (!defined('REFERENCES_TWITTER_PLUGIN_PATH')) {
            define('REFERENCES_TWITTER_PLUGIN_PATH', REFERENCES_PLUGINS_PATH . 'actions' . DIRECTORY_SEPARATOR . 'twitter' . DIRECTORY_SEPARATOR);
        }
        require_once REFERENCES_TWITTER_PLUGIN_PATH . 'config.php';
        //require_once REFERENCES_TWITTER_PLUGIN_PATH.'twitter.php';
        require_once REFERENCES_TWITTER_PLUGIN_PATH . 'Twitter.class.php';
        require_once REFERENCES_TWITTER_PLUGIN_PATH . 'bitly.class.php';
        if (REFERENCES_BITLY_LOGIN == '') {
            return '';
        }
        $sentText    = '';
        $bitly       = new Bitly(REFERENCES_BITLY_LOGIN, REFERENCES_BITLY_API_KEY);
        $shortUrl    = $bitly->shortenSingle($elementUrl);
        $searches    = array('[itemname]', '[url]');
        $replaces    = array($textToSend, $shortUrl);
        $sentText    = str_replace($searches, $replaces, $mask);
        $totalLength = strlen($sentText);
        if ($totalLength > REFERENCES_TWITTER_TWIT_MAX_LENGTH) {
            $tooLongOf = $totalLength - REFERENCES_TWITTER_TWIT_MAX_LENGTH;
            $searches  = array('[itemname]', '[url]');
            $replaces  = array(substr($textToSend, 0, strlen($textToSend) - $tooLongOf), $shortUrl);
            $sentText  = str_replace($searches, $replaces, $mask);
        }
        if (trim($sentText) != '') {
            //          $twitter = new Twitter(REFERENCES_TWITTER_USERNAME, REFERENCES_TWITTER_PASSWORD);
            //          $twitter->setUserAgent('references');
            //          $twitter->updateStatus($sentText);
            $tweet = new Twitter(REFERENCES_TWITTER_USERNAME, REFERENCES_TWITTER_PASSWORD);
            $tweet->update($sentText);
        }
        return $sentText;
    }

    /**
     * Méthode appelée pour indiquer qu'une nouvelle référence a été publié
     *
     * @param object $parameters La référence qui vient d'être publiée
     * @return void
     */
    public function fireNewReference($parameters)
    {
        if (!defined('REFERENCES_TWITTER_PLUGIN_PATH')) {
            define('REFERENCES_TWITTER_PLUGIN_PATH', REFERENCES_PLUGINS_PATH . 'actions' . DIRECTORY_SEPARATOR . 'twitter' . DIRECTORY_SEPARATOR);
        }
        require_once REFERENCES_TWITTER_PLUGIN_PATH . 'config.php';
        $reference = $parameters['reference'];
        $this->sendTextToTwitter(utf8_encode($reference->getVar('article_title', 'n')), utf8_encode(REFERENCES_TWITTER_NEW_REFERENCE_INTRO), $reference->getUrl());
    }

    /**
     * Méthode appelée pour indiquer qu'une nouvelle catégorie de références a été créée
     *
     * @param object $parameters La catégorie qui vient d'être publiée
     * @return void
     */
    public function fireNewCategory($parameters)
    {
        if (!defined('REFERENCES_TWITTER_PLUGIN_PATH')) {
            define('REFERENCES_TWITTER_PLUGIN_PATH', REFERENCES_PLUGINS_PATH . 'actions' . DIRECTORY_SEPARATOR . 'twitter' . DIRECTORY_SEPARATOR);
        }
        require_once REFERENCES_TWITTER_PLUGIN_PATH . 'config.php';
        if (trim(REFERENCES_TWITTER_NEW_CATEGORY_INTRO) != '') {
            $category = $parameters['category'];
            $this->sendTextToTwitter(utf8_encode($category->getVar('category_title', 'n')), utf8_encode(REFERENCES_TWITTER_NEW_CATEGORY_INTRO), $category->getUrl());
        }
    }
}
