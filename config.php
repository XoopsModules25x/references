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
 * @author          Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * ****************************************************************************
 */
if (!defined('REFERENCES_CACHE_PATH')) {
    // Le chemin du cache (il est conseillé de le mettre en dehors de la portée du web)
    define('REFERENCES_CACHE_PATH', XOOPS_UPLOAD_PATH . DIRECTORY_SEPARATOR . REFERENCES_DIRNAME . '_cache' . DIRECTORY_SEPARATOR);

    // Thumbs / Vignettes prefixe
    define('REFERENCES_THUMBS_PREFIX', 'thumb_');        // Thumbs prefix (when thumbs are automatically created)

    // Short text length / Longueur des textes raccourcis
    define('REFERENCES_SHORTEN_TEXT', '200');        // Characters count / Nombre de caract�res

    define('REFERENCES_METAGEN_MAX_KEYWORDS', 40);
    define('REFERENCES_METAGEN_KEYWORDS_ORDER', 0);

    // Automatically fill the manual date when creating a reference ? / Remplir automatiquement la date manuelle lorsqu'on crée une référence
    define('REFERENCES_AUTO_FILL_MANUAL_DATE', true);

    /**
     * Si la valeur est à false, alors dans les listes de recherche, côté admin, on recherche tout ce qui contient quelque chose.
     * Par exemple si on tape "a", le module trouvera "abajour" et "cartable".
     * Par contre si on met cette valeur à true alors le module cherchera tout ce qui commence par ...
     * Par exemple si on tape "a", lemodule trouvera "abajour" MAIS PAS "cartable"
     * PAR CONTRE "%a" trouvera tout ce qui contient la lettre a
     */
    define('REFERENCES_EXACT_SEARCH', true);
}
