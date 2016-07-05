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

/**
 * Plugin chargé de notifier de la création d'une nouvelle catégorie
 *
 */
class referencesNewcategoryAction extends references_action
{
    public function registerEvents()
    {
        /**
         * La liste des évènements traités par le plugin se présente sous la forme d'un tableau compposé comme ceci :
         *
         * Indice   Signification
         * ----------------------
         *    0        Evènement sur lequel se raccrocher (voir class/references_plugins.php::EVENT_ON_PRODUCT_CREATE
         *    1        Priorité du plugin (de 1 à 5)
         *    2        Script Php à inclure
         *    3        Classe à instancier
         *    4        Méthode à appeler
         */
        $events   = array();
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
     * Méthode appelée pour indiquer qu'une nouvelle catégorie de références a été créée
     *
     * @param object $parameters La catégorie qui vient d'être publiée
     * @return void
     */
    public function fireNewCategory($parameters)
    {
        $category                = $parameters['category'];
        $notification_handler    = xoops_getHandler('notification');
        $params                  = array();
        $params['CATEGORY_URL']  = $category->getUrl();
        $params['CATEGORY_NAME'] = $category->getVar('category_title');
        $notification_handler->triggerEvent('global', 0, 'new_category', $params);
    }
}
