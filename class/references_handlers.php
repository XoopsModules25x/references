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
 * Chargement des handlers utilisés par le module
 */
class references_handler
{
	public $h_references_articles = null;
	public $h_references_categories = null;
	private static $instance = false;

    /**
     * Singleton
     */
	private function __construct()
	{
		$handlersNames = array('references_articles', 'references_categories');
		foreach($handlersNames as $handlerName) {
			$internalName = 'h_'.$handlerName;
			$this->$internalName = xoops_getmodulehandler($handlerName, REFERENCES_DIRNAME);
		}
	}

    /**
     * Retourne l'instance unique de la clanss
     *
     * @return object
     */
	public static function getInstance()
	{
		if (!self::$instance instanceof self) {
      		self::$instance = new self;
		}
		return self::$instance;
	}
}
?>