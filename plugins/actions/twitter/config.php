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
 * Paramètres Twitter
 */

// Login Twitter
define("REFERENCES_TWITTER_USERNAME", "");
// Mot de passe Twitter
define("REFERENCES_TWITTER_PASSWORD", "");

// Paramétrage à utiliser pour créer le texte qui indique la création d'un nouveau produit. [itemname] = nom de l'élément, [url] = url créée avec bit.ly
define("REFERENCES_TWITTER_NEW_REFERENCE_INTRO", "Nouvelle référence [itemname] [url]");
// Paramétrage à utiliser pour créer le texte qui indique la création d'une nouvelle catégori [itemname] = nom de l'élément, [url] = url créée avec bit.ly
// Mettre à blanc "", pour ne pas lancer de notification
define("REFERENCES_TWITTER_NEW_CATEGORY_INTRO", "Nouvelle catégorie [itemname] [url]");


// Longueur que le texte ne doit pas dépasser
define("REFERENCES_TWITTER_TWIT_MAX_LENGTH", 140);

/**
 * Paramètres bit.ly
 */
define("REFERENCES_BITLY_LOGIN", "");
define("REFERENCES_BITLY_API_KEY", "");
?>