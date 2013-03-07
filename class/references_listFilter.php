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
 * @author 			Hervé Thouzard of Instant Zero
 * @link 			http://xoops.instant-zero.com/
 *
 * Version : $Id:
 * ****************************************************************************
 */

/**
 *
 * A class to manage filters in data tables
 *
 * @copyright       Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author 			Hervé Thouzard of Instant Zero
 * @link 			http://xoops.instant-zero.com/
 * @version 		1.4
 *
 * Note : La classe dépend de la classe utilitaire et du fichier config.php pour l'option "REFERENCES_EXACT_SEARCH"
 *
 * @todo Ajouter un paramètre pour spécifier une méthode à appeler (dans le handler de données) pour récupérer les listes de données (pour les champs de type select)
 */
class references_listFilter
{
	const FILTER_DATA_TEXT = 1;
	const FILTER_DATA_NUMERIC = 2;

	const FILTER_FIELD_TEXT = 1;
	const FILTER_FIELD_SELECT = 2;
	const FILTER_FIELD_SELECT_YES_NO = 3;

    /**
     * Préfixe des variables de sélection
     */
    const PREFIX = 'filter_';

    /**
     * Nom du module (utilisé pour la session)
     */
    const MODULE_NAME = 'references';

    /**
     * Contient toutes les variables participant au filtre (avec leur type et description)
     *
     * @var array
     */
    private $vars = array();

    /**
     * Handler des données
     *
     * @var reference
     */
    private $handler = null;

    /**
     * Nom de la zone d'opération (par exemple op=products)
     *
     * @var string
     */
    private $op = '';

    /**
     * action de l'opération en cour (par exemple op=products)
     *
     * @var string
     */
    private $operationName = '';

    /**
     * Nombre maximum d'éléments pas page
     *
     * @var intger
     */
    private $limit = 10;

    /**
     * Nom de la variable start
     *
     * @var string
     */
    private $startName = '';

    /**
     * Critère {@link Criteria} servant à choisir les données
     *
     * @var object
     */
    private $criteria = null;

    /**
     * Indique s'il y a un nouveau critère
     *
     * @var boolean
     */
    private $newFilter = false;

    /**
     * Indique si la méthode filter() a été appelée
     *
     * @var boolean
     */
    private $isInitialized = false;

    /**
     * Zone de tri pour retourner les données (dans getObjects)
     *
     * @var string
     */
    private $sortField = '';

    /**
     * Sens de tri
     *
     * @var string
     */
    private $sortOrder = '';

    /**
     * Liste de critères par défaut
     *
     * @var array
     */
    private $defaultCriterias = array();

    /**
     * L'url complète du script qui appelle
     *
     * @var string
     */
    private $baseUrl = '';

	/**
	 * Indique si on conserve en session la position de départ
	 *
	 * @var boolean
	 */
    private $keepStart = true;

    /**
     * Indique si l'autocomplétion est activée
     *
     * @var boolean
     */
    private $hasAutoComplete = false;

	/**
	 * Url du dossier js dans le module
	 *
	 * @var string
	 */
    private $jsFolderUrl = '';

    /**
     * Paramètres additionnels à ajouter aux paramètres du pager
     * @var array
     */
    private $additionnalPagerParameters = array();

	/**
	 * Paramètres additionnels à ajouter au bouton permettant de supprimer le filtre en cours
	 * @var array
	 */
    private $additionnalClearButtonParameters = array();

	/**
	 * Tableau des champs triables [clé] = nom du champ dans la base, valeur = libellé
	 * @var array
	 */
    private $sortFields = array();

	/**
	 * Initialise les paramètres avec des valeurs par défaut
	 */
    private function setDefaultValues()
    {
        $this->handler = null;
        $this->op = '';
        $this->limit = 10;
        $this->startName = 'start';
        $this->operationName = 'op';
        $this->sortField = '';
        $this->sortOrder = 'asc';
        $this->baseUrl = '';
        $this->keepStart = true;
        $this->hasAutoComplete = false;
        $this->jsFolderUrl = '';
        $this->additionnalPagerParameters = array();
        $this->additionnalClearButtonParameters = array();
        $this->sortFields = array();	// Les champs qui peuvent être utilisés pour trier
    }

    /**
     * Initialisation des données, handler et opération courante dans l'appelant
     *
     * @param mixed $handler		Soit une référence au handler de données soit un tableau qui contient toutes les options (auxquel cas les autres paramètres sont inutiles)
     * @param string $operation		Opération courante dans l'appelant
     * @param string $startName		Nom du paramètre start
     * @param integer $limit		Nombre maximum d'éléments par page
     * @param string $baseUrl		L'url complète du script appelant
     * @param string $sortField		Zone de tri
     * @param string $sortOrder		Sens de tri
     * @param boolean $keepStart	Indique si on conserve la position de départ
     * @package string $jsFolderUrl	Url du répertoire qui contient les scripts Javascript
     */
    function __construct($handler, $operationName = 'op', $operation = '', $startName = 'start', $limit = 10, $baseUrl = '', $sortField = '', $sortOrder = 'asc', $keepStart = true, $jsFolderUrl = '')
    {
    	$this->setDefaultValues();
    	if(!is_array($handler)) {
    	    $this->handler = $handler;
	        $this->op = $operation;
        	$this->limit = $limit;
        	$this->startName = $startName;
        	$this->operationName = $operationName;
        	$this->sortField = $sortField;
        	$this->sortOrder = $sortOrder;
        	$this->baseUrl = $baseUrl;
        	$this->keepStart = $keepStart;
        	$this->jsFolderUrl = $jsFolderUrl;
    	} else {
    		foreach($handler as $key => $value) {
    			$this->$key = $value;
    		}
    	}
    }

	/**
	 * Donne à la classe le nom des champs sur lesquels on peut faire le tri
	 *
	 * @param array $fields		[clé] = nom du champ dans la base, valeur = libellé
	 * @return object
	 */
    function setSortFields($fields)
    {
		$this->sortFields = $fields;
    }

	/**
	 * Retourne les noms à utiliser pour les champs de tri (sélecteur de champ et ordre de tri)
	 *
	 * @return array	[0] = Nom du sélecteur de champs, [1] = Nom du sélecteur pour le sens du tri
	 */
    private function getSortPlaceHolderNames()
    {
    	return array(self::PREFIX.'sortFields', self::PREFIX.'sortOrder');
    }

	/**
	 * Retourne 2 sélecteurs html pour choisir la zone de tri et le sens du tri
	 *
	 * @return string
	 */
    function getSortPlaceHolderHtmlCode()
    {
    	$sortNames = $this->getSortPlaceHolderNames();
    	$sortFieldsHtml = references_utils::htmlSelect($sortNames[0], $this->sortFields, $this->sortField, false);
    	$sortOrderHtml = references_utils::htmlSelect($sortNames[1], array('asc' => _MD_REFERENCES_ASC, 'desc' => _MD_REFERENCES_DESC), $this->sortOrder, false);
    	return _MD_REFERENCES_SORT_BY.' '.$sortFieldsHtml.' '.$sortOrderHtml;
    }

	/**
	 * Permet de valoriser une option directement tout en chainant
	 *
	 * @param string $optionName
	 * @param mixed $optionValue
	 * @return object
	 */
    public function setOption($optionName, $optionValue)
    {
    	$this->$optionName = $optionValue;
    	return $this;
    }

    /**
     * Ajoute un nouveau critère par défaut à la liste des critères par défaut
     *
     * @param Criteria $criteria
     */
    function addDefaultCriteria(Criteria $criteria)
    {
        $this->defaultCriterias[] = $criteria;
    }

    /**
     * Retourne une valeur d'un tableau ou null si l'index n'existe pas
     *
     * @param array $array			Le tableau à traiter
     * @param string $index			L'index recherché
     * @param mixed $defaultValue	La valeur par défaut
     * @return mixed
     */
    private function getArrayValue($array, $index, $defaultValue = false)
    {
    	if($index == 'autoComplete' && isset($array[$index]) && isset($array[$index]) == true) {
    		$this->hasAutoComplete = true;	// On en profite pour vérifier si un champ utilise l'autocomplétion
    	}
        return isset($array[$index]) ? $array[$index] : $defaultValue;
    }

    /**
	 * Permet de faire l'autocomplétion d'un champ
	 *
	 * @param string $query
	 * @param string $fieldName
	 * @return string
	 */
    function autoComplete($query, $limit, $fieldName)
    {
    	$return = '';
    	if(!$this->hasAutoComplete) {	// Si aucun champ n'est en autocomplétion, c'est pas la peine d'aller plus loin
    		return $return;
    	}
    	if(isset($this->vars[$fieldName])) {	// On vérifie que le champ demandé est bien en autocomplétion
			if($this->vars[$fieldName]['autoComplete'] == true) {
				if($this->vars[$fieldName]['dataType'] == self::FILTER_DATA_TEXT) {
					$criteria = new Criteria($fieldName, $query.'%', 'LIKE');
				}
				$criteria->setLimit(intval($limit));
				$ret = $this->handler->getObjects($criteria);

				if(count($ret) > 0) {
					foreach($ret as $object) {
						$return .= $object->getVar($fieldName, 'n')."\n";
					}
				}
			}
    	}
    	return $return;
    }

	/**
	 * Retourne le code Javascript à utiliser pour initialiser l'auto complétion (et donc à coller dans le code html)
	 *
	 * @param boolean $jqueryAlreadyLoaded	Indique si jQuery est déjà chargé par l'appelant, auquel cas rien ne sert de le recharger
	 * @return string
	 */
    function getJavascriptInitCode($jqueryAlreadyLoaded = false)
    {
    	$return = '';
		if(!$this->hasAutoComplete) {
			return $return;
		}
		$return = '';
		$return .= "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" title=\"Style sheet\" href=\"".$this->jsFolderUrl."autocomplete/jquery.autocomplete.css\" />\n";
		if(!$jqueryAlreadyLoaded) {
			$return .= "<script type=\"text/javascript\" src=\"".$this->jsFolderUrl."jquery/jquery.js\"></script>\n";
		}
		$return .= "<script type=\"text/javascript\" src=\"".$this->jsFolderUrl."noconflict.js\"></script>\n";
		$return .= "<script type=\"text/javascript\" src=\"".$this->jsFolderUrl."autocomplete/jquery.autocomplete.min.js\"></script>\n";
		$return .= "<script type=\"text/javascript\">\n";
		$return .= "jQuery(function($) {\n";
		$return .= "var url='".$this->baseUrl."';\n";	// TODO: Supprimer "var" car cela limite sa portée !
		$return .= "var handlerName='".$this->handler->className."';\n";

		foreach($this->vars as $fieldName => $parameters) {
			if($parameters['autoComplete'] == true) {
				$field = self::PREFIX.$fieldName;
				$return .= "$('#".$field."').autocomplete(url, {\n";
   				$return .= "extraParams: {\n";
       			$return .= "	field: '".$fieldName."',\n";
       			$return .= "	op: 'autocomplete',\n";
       			$return .= "	handler: handlerName\n";
   				$return .= "}\n";
				$return .= "});\n";
			}
		}
		$return .= "});\n";
		$return .= "</script>\n";
		return $return;
    }

    /**
     * Initialisation des données du filtre
     * Permet d'indiquer quelles sont les zones sur lesquelles on effectue des filtres ainsi que leur type
     *
     * @param string $fieldName		Le nom du champs dans la table
     * @param array $parameters		Les paramètres de la zone sous la forme d'un tableau :
     * 		[dataType]		Entier représentant le type de donnée (numérique ou chaine)
     * 		[fieldType]		Le type de zone de saisie (zone de texte, liste déroulante, liste déroulante Oui/Non)
     * 		[values]		La ou les valeurs de la zone de saisie (utilisé dans le cas d'un select)
     * 		[size]			Largeur d'affichage pour les textbox
     * 		[maxLength]		Largeur maximale pour les textbox
     * 		[withNull]		Dans le cas des listes déroulantes, indique s'il faut une valeur nulle
     * 		[minusOne]		Indique s'il faut retrancher 1 à la valeur saisie récupérée (cas classique des listes Oui/Non avec une valeur nulle)
     * 		[style]			Dans le cas des liste déroulante, le style à appliquer à la liste
     * 		[data]			A ne pas renseigner, contient la valeur saisie par l'utilisateur
     * 		[operator]		Opérateur de comparaison pour le Criteria
     * 		[autoComplete]  Indique si on active l'auto complétion sur le champs
     * @return object				L'objet courant pour pouvoir chainer
     */
    function initFilter($fieldName, $parameters)
    {
    	// Tableau des valeurs attendues avec leur valeur par défaut
        $indexNames = array('dataType' => self::FILTER_DATA_TEXT, 'fieldType' => self::FILTER_FIELD_TEXT, 'values' => null, 'withNull' => false, 'size' => 5, 'maxLength' => 255, 'minusOne' => false, 'data' => null, 'style' => '', 'operator' => '=', 'autoComplete' => false);
		$data = array();
        foreach($indexNames as $indexName => $defaultValue) {
            $data[$indexName] = $this->getArrayValue($parameters, $indexName, $defaultValue);
        }
        $this->vars[$fieldName] = $data;
        return $this;
    }

    /**
     * Retourne le nom du tableau à utiliser pour la session
     * @note : Le nom de la session est composé de : nom du module_nom du handler, par exemple references_references_articles
     *
     * @return string
     */
    private function getSessionName()
    {
        return self::MODULE_NAME.'_'.$this->handler->table;
    }

    /**
	 * Retourne le nom de la clé à utiliser pour la conservation du start en session
	 *
	 * @return string
	 */
    private function getStartSessionName()
    {
    	return self::getSessionName().'_start';
    }

    /**
     * Réinitialisation des données avant traitement
     *
     * @return void
     */
    private function reinitializeFieldsValue()
    {
        foreach($this->vars as $fieldName => $fieldProperties) {
            if($fieldProperties['dataType'] == self::FILTER_DATA_NUMERIC) {    // Zone numérique
                $fieldProperties['data'] = 0;
            } else {
                $fieldProperties['data'] = '';
            }
            $this->vars[$fieldName] = $fieldProperties;
        }
    }

    /**
     * Ajoute les critères par défaut au critère général
     *
     * @return void
     */
    private function addDefaultCriterias()
    {
        if(is_array($this->defaultCriterias) && count($this->defaultCriterias) > 0) {
            foreach($this->defaultCriterias as $criteria) {
                $this->criteria->add($criteria);
            }
        }
        return $this;
    }

	/**
	 * Indique s'il y a des champs de tri
	 *
	 * @return boolean
	 */
    private function areThereSortFields()
    {
    	$return = false;
    	if(is_array($this->sortFields) && count($this->sortFields) > 0) {
    		$return = true;
    	}
    	return $return;
    }

	/**
	 * Indique si le nom du champ passé en paramètre fait partie de la liste des champs "triables"
	 *
	 * @param string $fieldName
	 * @return boolean
	 */
    private function isInSortFieldsList($fieldName)
    {
    	return array_key_exists($fieldName, $this->sortFields);
    }

	/**
	 * Indique si le sens de tri passé en paramètre fait partie de la liste autorisée
	 *
	 * @param string $order
	 * @return boolean
	 */
    private function isInSortOrderList($order)
    {
		return in_array($order, array('asc', 'desc'));
    }

	/**
	 * Récupère, depuis la requête d'entrée, la zone de tri à utiliser et le sens du tri et place l'information en cookie
	 * pour que lorsque l'utilisateur se reconnecte, il retrouve ses informations de tri
	 *
	 * @return void
	 */
    private function setSortFieldsFromRequest()
    {
    	if(!$this->areThereSortFields()) {	// S'il n'y a pas de champs triables, on laisse tomber
    		return;
    	}
    	$sortField = $sortOrder = '';
    	$cookieName = $this->getSessionName();
		$orderFieldsNames = $this->getSortPlaceHolderNames();
		if(isset($_REQUEST[$orderFieldsNames[0]]) && isset($_REQUEST[$orderFieldsNames[1]])) {
			$sortField = $_REQUEST[$orderFieldsNames[0]];
			$sortOrder = $_REQUEST[$orderFieldsNames[1]];
		} else {
			if(isset($_SESSION[$cookieName.'_sortField'])) {
				$sortField = $_SESSION[$cookieName.'_sortField'];
			}
			if(isset($_SESSION[$cookieName.'_sortOrder'])) {
				$sortOrder = $_SESSION[$cookieName.'_sortOrder'];
			}
		}

		if($this->isInSortFieldsList($sortField) && $this->isInSortOrderList($sortOrder)) {
			$this->sortField = $sortField;
			$this->sortOrder = $sortOrder;
		}
		if(trim($sortField) != '' && trim($sortOrder) != '') {
			$_SESSION[$cookieName.'_sortField'] = $sortField;
			$_SESSION[$cookieName.'_sortOrder'] = $sortOrder;
		}
    }

	/**
	 * Réinitialisation des données avant traitement
	 *
	 * @return void
	 */
    private function setupFilter()
    {
		$this->reinitializeFieldsValue();
		$this->newFilter = false;
		$this->isInitialized = true;
		$this->criteria = new CriteriaCompo();
		$this->criteria->add(new Criteria($this->handler->keyName, 0, '<>'));
		$this->addDefaultCriterias();
    }

	/**
	 * RAZ des données du filtre si cela a été demandé dans la requête
	 *
	 * @return void
	 */
    private function isSetCleanFilter()
    {
        if(isset($_REQUEST['cleanFilter'])) {
        	$this->setStartInSession(0);
            unset($_SESSION[$this->getSessionName()]);
        }
    }

	/**
	 * Retourne le critère de filtrage courant
	 *
	 * @return object
	 */
    function getCriteria()
	{
		return $this->criteria;
	}

    /**
     * Méthode à appeler juste après le constructeur pour qu'elle récupère les données saisies
     *
     * @return object	L'objet courant pour pouvoir chainer
     */
    function filter()
    {
    	$this->setupFilter();				// Réinitialisations
		$ts = &MyTextSanitizer::getInstance();
		$this->setSortFieldsFromRequest();	// On récupère la zone de tri éventuellement passée dans la requête
		$this->isSetCleanFilter();

        foreach($this->vars as $fieldName => $fieldProperties) {
            // On commence par récupérer toutes les valeurs
            $formFieldName = self::PREFIX.$fieldName;    // "filter_website_id" par exemple
            $fieldProperties['data'] = null;	// Valeur par défaut
            if(isset($_REQUEST[$formFieldName])) {
                if($fieldProperties['dataType'] == self::FILTER_DATA_NUMERIC) {    // Zone numérique
                    if(intval($_REQUEST[$formFieldName]) != 0) {
                        $fieldProperties['data'] = intval($_REQUEST[$formFieldName]);
                        if(!$fieldProperties['minusOne']) {
                            $this->criteria->add(new Criteria($fieldName, $fieldProperties['data'], $fieldProperties['operator']));
                        } else {
                            $this->criteria->add(new Criteria($fieldName, $fieldProperties['data'] - 1, $fieldProperties['operator']));
                        }
                        $this->newFilter = true;
                    }
                } else {    // Zone texte
                    if(trim($_REQUEST[$formFieldName]) != '') {
                        $fieldProperties['data'] = $_REQUEST[$formFieldName];
                        if(!REFERENCES_EXACT_SEARCH) {
                        	$this->criteria->add(new Criteria($fieldName, '%'.$ts->addSlashes($fieldProperties['data']).'%', 'LIKE'));
                        } else {
                        	$this->criteria->add(new Criteria($fieldName, $ts->addSlashes($fieldProperties['data']).'%', 'LIKE'));
                        }
                        $this->newFilter = true;
                    }
                }
            }
            $this->vars[$fieldName] = $fieldProperties;
        }

        if($this->newFilter) {
        	$this->setStartInSession(0);
        }

        // Récupération des donées de la session s'il n'y a pas eu de filtre(s)
		if(!$this->newFilter && isset($_SESSION[$this->getSessionName()])) {
			$sessionFilterData = unserialize($_SESSION[$this->getSessionName()]);
			if(isset($sessionFilterData['criteria']) && is_object($sessionFilterData['criteria'])) {
				$this->criteria = $sessionFilterData['criteria'];
				unset($sessionFilterData['criteria']);
			}
			foreach($this->vars as $fieldName => $fieldProperties) {
				if(isset($sessionFilterData[$fieldName])) {
                	$fieldProperties['data'] = $sessionFilterData[$fieldName];
				}
				$this->vars[$fieldName] = $fieldProperties;
			}
			unset($_SESSION[$this->getSessionName()]);
		}

		// Mise en place des données dans la session
		$dataForSession = array();
		$dataForSession['criteria'] = $this->criteria;
		foreach($this->vars as $fieldName => $fieldProperties) {
            $dataForSession[$fieldName] = $fieldProperties['data'];
		}
		$_SESSION[$this->getSessionName()] = serialize($dataForSession);
		return $this;
    }

    /**
     * Retourne le nombre d'enregistrement en fonction des critères courants
     *
     * @return integer
     */
    function getCount()
    {
        if(!$this->isInitialized) {
            $this->filter();
        }
        return $this->handler->getCount($this->criteria);
    }

    /**
	 * Conserve la valeur de start en session
	 *
	 * @param integer $start
	 * @return void
	 */
    private function setStartInSession($start)
    {
        if($this->keepStart) {
        	$startSessionName = $this->getStartSessionName();
        	$_SESSION[$startSessionName] = intval($start);
        }
    }

	/**
	 * Retourne la valeur de ?start=x
	 *
	 * @return integer
	 */
    private function getStartValue()
    {
    	$start = 0;
        if(isset($_REQUEST[$this->startName])) {
        	$start = intval($_REQUEST[$this->startName]);
        } elseif($this->keepStart) {
        	$startSessionName = $this->getStartSessionName();
        	if(isset($_SESSION[$startSessionName])) {
        		$start = intval($_SESSION[$startSessionName]);
        	}
        }
        // Mise en session
        $this->setStartInSession($start);
        return $start;
    }

	/**
	 * Permet d'ajouter un paramètre supplémentaire au pager
	 *
	 * @param string $key
	 * @param string $value
	 * @return object
	 */
    function addAdditionnalParameterToPager($key, $value = '')
    {
   		$this->additionnalPagerParameters[$key] = $value;
   		return $this;
    }

	/**
	 * Permet d'ajouter un paramètre supplémentaire au bouton permettant de supprimer le filtre
	 *
	 * @param string $key
	 * @param string $value
	 * @return object
	 */
    function addAdditionnalParameterToClearButton($key, $value = '')
    {
   		$this->additionnalClearButtonParameters[$key] = $value;
   		return $this;
    }

	/**
	 * Permet d'ajouter des paramètres supplémentaires au pager
	 *
	 * @param string $key
	 * @param string $value
	 * @return object
	 */
    function addAditionnalArrayParametersToPager($array)
    {
    	if(count($array) > 0) {
    		foreach($array as $key => $value) {
    			$this->addAdditionnalParameterToPager($key, $value);
    		}
    	}
   		return $this;
    }

    /**
     * Retourne le pager à utiliser
     *
     * @return mixed	Null s'il n'y a pas lieu d'y avoir un pager, sinon un objet de type {@link XoopsPageNav}
     */
    function getPager()
    {
        if(!$this->isInitialized) {
            $this->filter();
        }
        require_once XOOPS_ROOT_PATH.'/class/pagenav.php';
        $itemsCount = $this->getCount();
        $queryString = array();
        if(trim($this->op) != '') {
            $queryString[$this->operationName] = $this->op;
        }
        $pagenav = null;

        if($itemsCount > $this->limit) {
            foreach($this->vars as $fieldName => $fieldProperties) {
                $formFieldName = self::PREFIX.$fieldName;    // "filter_website_id" par exemple
                $queryString[$formFieldName] = $fieldProperties['data'];
            }
            // Ajout des paramètres supplémentaires éventuels
            if(count($this->additionnalPagerParameters) > 0) {
            	foreach($this->additionnalPagerParameters as $key => $value) {
            		$queryString[$key] = $value;
            	}
            }
            $start = $this->getStartValue();
			$pagenav = new XoopsPageNav($itemsCount, $this->limit, $start, $this->startName, http_build_query($queryString));
        }
        return $pagenav;
    }

    /**
     * Retourne une liste d'objets en fonction des critères définis
     *
     * @return array
     */
    function getObjects()
    {
        if(!$this->isInitialized) {
            $this->filter();
        }
        $start = $this->getStartValue();
        $limit = $this->limit;
        $criteria = $this->criteria;
        $criteria->setStart($start);
        $criteria->setLimit($limit);

        $criteria->setOrder($this->sortOrder);
        if(trim($this->sortField) != '') {
            $criteria->setSort($this->sortField);
        } elseif(trim($this->handler->identifierName) != '') {
             $criteria->setSort($this->handler->identifierName);
        }
        return $this->handler->getObjects($this->criteria);
    }

    /**
     * Retourne la zone html à utiliser pour créer la zone de filtre (avec sa valeur saisie si c'est le cas)
     *
     * @param string $fieldName	La zone de saisie dont on veut récupérer le code html
     * @return string
     */
    function getFilterField($fieldName)
    {
        $html = '';
        if(!$this->isInitialized) {
            $this->filter();
        }
        if(!isset($this->vars[$fieldName])) {
            trigger_error("Error, unknow field");
            return $html;
        }
        $fieldData = $this->vars[$fieldName];
        $htmlFieldName = self::PREFIX.$fieldName;

        switch ($fieldData['fieldType']) {
        	case self::FILTER_FIELD_TEXT:    // Zone de texte
        	    $ts = &MyTextSanitizer::getInstance();
        	    $html = "<input type='text' name='$htmlFieldName' id='$htmlFieldName' size='".$fieldData['size']."' maxlength='".$fieldData['maxLength']."' value='".$ts->htmlSpecialChars($fieldData['data'])."' />";
            	break;

        	case self::FILTER_FIELD_SELECT;     // Select
        	    $style = '';
        	    if(isset($fieldData['style']) && trim($fieldData['style']) != '') {
        	        $style = $fieldData['style'];
        	    }
        	    $html = references_utils::htmlSelect($htmlFieldName, $fieldData['values'], $fieldData['data'], $fieldData['withNull'], $style);
            	break;

        	case self::FILTER_FIELD_SELECT_YES_NO:    // Select de type Oui/Non
        	    $html = references_utils::htmlSelect($htmlFieldName, array(2 => _YES, 1 => NO), $fieldData['data'], $fieldData['withNull']);
        	    break;
        }
        return $html;
    }

	/**
	 * Assigne toutes les zones de filtre à un template
	 *
	 * @param object $xoopsTpl
	 * @param boolean $asArray	Est-ce qu'il faut placer le résultat dans un tableau ou assigner par nom de zone de filtre
	 * @param string $arrayName	Le nom du tableau à utiliser
	 * @return void
	 */
    function assignFilterFieldsToTemplate(&$xoopsTpl, $asArray = true, $arrayName = 'filterFields')
    {
    	$fields = array_keys($this->vars);
		foreach($fields as $field) {
			if(!$asArray) {
				$xoopsTpl->assign($field, $this->getFilterField($field));
			} else {
				$xoopsTpl->append($arrayName, $this->getFilterField($field));
			}
		}
    }

    /**
     * Retourne le bouton utilisé pour supprimer le filtre en cours
     *
     * @return string
     */
    function getClearFilterbutton()
    {
    	$queryString = array();
    	$queryString[$this->operationName] = $this->op;
		if(count($this->additionnalClearButtonParameters) > 0) {
			foreach($this->additionnalClearButtonParameters as $key => $value) {
				$queryString[$key] = $value;
			}
		}
    	$queryString['cleanFilter'] = '1';
        $baseurl = $this->baseUrl;
        return "&nbsp;&nbsp;<a href='$baseurl?".http_build_query($queryString)."' title='"._MD_REFERENCES_CLEAN_FILTER."'><img align='top' src='../images/clear_left.png' alt='"._MD_REFERENCES_CLEAN_FILTER."' /></a>";
    }

	/**
	 * Retourne le bouton permettant de lancer le filtrage
	 *
	 * @param string $description	Texte à faire apparaître sur le bouton
	 * @param array $additionnals	Champs supplémentaires à faire apparaître avec le bouton
	 * @return string
	 */
    function getGoButton($description = _GO, $additionnals = null)
    {
        $html = '';
        if(trim($this->operationName) != '' && trim($this->op) != '') {
            $html .= "<input type='hidden' name='".$this->operationName."' id='".$this->operationName."' value='".$this->op."' />";
        }
        if(!is_null($additionnals)) {
        	foreach($additionnals as $key => $value) {
        		$html .= "<input type='hidden' name='".$key."' id='".$key."' value='".$value."' />";
        	}
        }
        $html .= "<input type='submit' name='btngo' id='btngo' value='$description' />";
        return $html;
    }

	/**
	 * Retourne le nom de la zone utilisée pour trier les données
	 *
	 * @return string
	 */
    function getSortField()
    {
    	return $this->sortField;
    }

	/**
	 * Retourne le sens de tri utilisé pour trier les données
	 *
	 * @return string
	 */
    function getSortOrder()
    {
    	return $this->sortOrder();
    }

	/**
	 * Retourne la valeur d'un champ
	 *
	 * @param string $fieldName
	 * @return mixed	La valeur du champ ou null si on ne trouve pas la zone
	 */
    function getFieldValue($fieldName)
    {
    	$ret = null;
    	if(isset($this->vars[$fieldName])) {
    		$ret = $this->vars[$fieldName]['data'];
    	}
    	return $ret;
    }
}
?>