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
 * Param�tres Twitter
 */

// Login Twitter
define("REFERENCES_TWITTER_USERNAME", "");
// Mot de passe Twitter
define("REFERENCES_TWITTER_PASSWORD", "");

// Param�trage � utiliser pour cr�er le texte qui indique la cr�ation d'un nouveau produit. [itemname] = nom de l'�l�ment, [url] = url cr��e avec bit.ly
define("REFERENCES_TWITTER_NEW_REFERENCE_INTRO", "Nouvelle r�f�rence [itemname] [url]");
// Param�trage � utiliser pour cr�er le texte qui indique la cr�ation d'une nouvelle cat�gori [itemname] = nom de l'�l�ment, [url] = url cr��e avec bit.ly
// Mettre � blanc "", pour ne pas lancer de notification
define("REFERENCES_TWITTER_NEW_CATEGORY_INTRO", "Nouvelle cat�gorie [itemname] [url]");


// Longueur que le texte ne doit pas d�passer
define("REFERENCES_TWITTER_TWIT_MAX_LENGTH", 140);

/**
 * Param�tres bit.ly
 */
define("REFERENCES_BITLY_LOGIN", "");
define("REFERENCES_BITLY_API_KEY", "");
?>