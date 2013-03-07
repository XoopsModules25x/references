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
 * Class used for parameters passing to classes methods
 *
 * @copyright       Herv� Thouzard of Instant Zero (http://www.instant-zero.com)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         references
 * @author 			Herv� Thouzard of Instant Zero (http://www.instant-zero.com)
 * @version 1.0
 *
 */
class references_parameters extends ArrayObject
{
    /**
     * Permet de valoriser un indice de la classe comme si c'�tait une propri�t� de la classe
     *
     * @example $enregistrement->nom_du_champ = 'ma chaine'
     *
     * @param string $key	Le nom du champ � traiter
     * @param mixed $value	La valeur � lui attribuer
     * @return object
     */
    function __set($key, $value)
    {
		parent::offsetSet($key, $value);
		return $this;
    }

	/**
	 * Valorisation d'un indice de la classe en utilisant un appel de fonction bas� sur le principe suivant :
	 * 		$maClasse->setLimit(10);
	 * Il est possible de chainer comme ceci : $maClasse->setStart(0)->setLimit(10);
	 *
	 * @param string $method
	 * @param mixed $args
	 * @return object
	 */
    function __call($method, $args)
    {
		if(substr($method, 0, 3) == 'set') {
			parent::offsetSet(strtolower(substr($method, 3, 1)).substr($method, 4), $args[0]);
			return $this;
		} else {	// Affichage de la valeur
			return parent::offsetGet($method);
		}
    }

	/**
	 * M�thode qui essaye de faire la m�me chose que la m�thode extend() de jQuery
	 *
	 * On lui passe les valeurs par d�faut que l'on attend et la m�thode les compare avec les valeurs actuelles
	 * Si des valeurs manquent, elles sont ajout�es
	 *
	 * @param references_parameters $defaultValues
	 * @return references_parameters
	 */
	function extend(self $defaultValues)
	{
		$result = new self;
		$result = $this;
		foreach($defaultValues as $key => $value) {
			if(!isset($result[$key])) {
				$result[$key] = $value;
			}
		}
		return $result;
	}
}
?>