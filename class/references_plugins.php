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
 * Gestion des plugins du module
 *
 */
class references_plugins
{
    /**
     * Dictionnaire des évènements
     */
    const EVENT_ON_REFERENCE_CREATE = 'onReferenceCreate';
    const EVENT_ON_CATEGORY_CREATE  = 'onCategoryCreate';

    // Pour limiter les dépendances
    const MODULE_DIRNAME = REFERENCES_DIRNAME;

    /**
     * Types d'évènements
     */
    const PLUGIN_ACTION = 0;
    const PLUGIN_FILTER = 1;

    /**
     * Nom du script Php inclut qui contient l'inscription des plugins
     */
    const PLUGIN_SCRIPT_NAME = 'plugins.php';

    /**
     * Dans le fichier Php qui contient l'inscription des plugins, méthode à appeler pour récupérer la liste des plugins
     */
    const PLUGIN_DESCRIBE_METHOD = 'registerEvents';

    /**
     * Nom de la variable de session qui contient la liste des plugins détachés
     */
    const PLUGIN_UNPLUG_SESSION_NAME = 'references_plugins';

    /**
     * Priorités des plugins
     * @var constant
     */
    const EVENT_PRIORITY_1 = 1;    // Priorité la plus haute
    const EVENT_PRIORITY_2 = 2;
    const EVENT_PRIORITY_3 = 3;
    const EVENT_PRIORITY_4 = 4;
    const EVENT_PRIORITY_5 = 5;    // Priorité la plus basse

    /**
     * Utilisé pour construire le nom de la classe
     */
    private $pluginsTypeLabel = array(self::PLUGIN_ACTION => 'Action', self::PLUGIN_FILTER => 'Filter');

    /**
     * Nom des classes qu'il faut étendre en tant que plugin
     */
    private $pluginsClassName = array(self::PLUGIN_ACTION => 'references_action', self::PLUGIN_FILTER => 'references_filter');

    /**
     * Nom de chacun des dossiers en fonction du type de plugin
     */
    private $pluginsTypesFolder = array(self::PLUGIN_ACTION => 'actions', self::PLUGIN_FILTER => 'filters');

    /**
     * Contient l'unique instance de l'objet
     * @var object
     */
    private static $instance = false;

    /**
     * Liste des évènements
     * @var array
     */
    private static $events = array();

    /**
     * Retourne l'instance unique de la classe
     *
     * @return object
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Chargement des 2 types de plugins
     *
     */
    private function __construct()
    {
        $this->events = array();
        $this->loadPlugins();
    }

    /**
     * Chargement des plugins (actions et filtres)
     * @return void
     */
    public function loadPlugins()
    {
        $this->loadPluginsFiles(REFERENCES_PLUGINS_PATH . $this->pluginsTypesFolder[self::PLUGIN_ACTION], self::PLUGIN_ACTION);
        $this->loadPluginsFiles(REFERENCES_PLUGINS_PATH . $this->pluginsTypesFolder[self::PLUGIN_FILTER], self::PLUGIN_FILTER);
    }

    /**
     * Vérifie que le fichier Php passé en paramètre contient bien une classe de filtre ou d'action et si c'est le cas, le charge dans la liste des plugins
     * @param string  $fullPathName Chemin complet vers le fichier (répertoire + nom)
     * @param integer $type         Type de plugin recherché (action ou filtre)
     * @param string  $pluginFolder Le nom du répertoire dans lequel se trouve le fichier (le "dernier nom")
     * @return void
     */
    private function loadClass($fullPathName, $type, $pluginFolder)
    {
        require_once $fullPathName;
        // Du style referencesRegionalizationFilter
        $className = self::MODULE_DIRNAME . ucfirst(strtolower($pluginFolder)) . $this->pluginsTypeLabel[$type];
        if (class_exists($className) && get_parent_class($className) == $this->pluginsClassName[$type]) {
            // TODO: Vérifier que l'évènement n'est pas déjà en mémoire
            $events = call_user_func(array($className, self::PLUGIN_DESCRIBE_METHOD));
            foreach ($events as $event) {
                $eventName                                         = $event[0];
                $eventPriority                                     = $event[1];
                $fileToInclude                                     = REFERENCES_PLUGINS_PATH . $this->pluginsTypesFolder[$type] . DIRECTORY_SEPARATOR . $pluginFolder . DIRECTORY_SEPARATOR . $event[2];
                $classToCall                                       = $event[3];
                $methodToCall                                      = $event[4];
                $this->events[$type][$eventName][$eventPriority][] = array('fullPathName' => $fileToInclude, 'className' => $classToCall, 'method' => $methodToCall);
            }
        }
    }

    /**
     * Part à la recherche d'un type de plugin dans les répertoires
     *
     * @param  string $path La racine
     * @param integer $type Le type de plugin recherché (action ou filtre)
     * @return void
     */
    private function loadPluginsFiles($path, $type)
    {
        $objects = new DirectoryIterator($path);
        foreach ($objects as $object) {
            if ($object->isDir() && !$object->isDot()) {
                $file = $path . DIRECTORY_SEPARATOR . $object->current() . DIRECTORY_SEPARATOR . self::PLUGIN_SCRIPT_NAME;
                if (file_exists($file)) {
                    $this->loadClass($file, $type, $object->current());
                }
            }
        }
    }

    /**
     * Déclenchement d'une action et appel des plugins liés
     *
     * @param string                       $eventToFire L'action déclenchée
     * @param object|references_parameters $parameters  Les paramètres à passer à chaque plugin
     * @return object L'objet lui même pour chaîner
     */
    public function fireAction($eventToFire, references_parameters $parameters = null)
    {
        if (!isset($this->events[self::PLUGIN_ACTION][$eventToFire])) {
            trigger_error(sprintf(_MD_REFERENCES_PLUGINS_ERROR_1, $eventToFire));

            return $this;
        }
        ksort($this->events[self::PLUGIN_ACTION][$eventToFire]);    // Tri par priorit�
        foreach ($this->events[self::PLUGIN_ACTION][$eventToFire] as $priority => $events) {
            foreach ($events as $event) {
                if ($this->isUnplug(self::PLUGIN_ACTION, $eventToFire, $event['fullPathName'], $event['className'], $event['method'])) {
                    continue;
                }
                require_once $event['fullPathName'];
                if (!class_exists($event['className'])) {
                    $class = new $event['className'];
                }
                if (!method_exists($event['className'], $event['method'])) {
                    continue;
                }
                call_user_func(array($event['className'], $event['method']), $parameters);
                unset($class);
            }
        }

        return $this;
    }

    /**
     * Déclenchement d'un filtre et appel des plugins liés
     *
     * @param string                       $eventToFire Le filtre appelé
     * @param object|references_parameters $parameters  Les paramètres à passer à chaque plugin
     * @return object Le contenu de l'objet passé en paramètre
     */
    public function fireFilter($eventToFire, references_parameters $parameters)
    {
        if (!isset($this->events[self::PLUGIN_FILTER][$eventToFire])) {
            trigger_error(sprintf(_MD_REFERENCES_PLUGINS_ERROR_1, $eventToFire));

            return $this;
        }
        ksort($this->events[self::PLUGIN_FILTER][$eventToFire]);    // Tri par priorité
        foreach ($this->events[self::PLUGIN_FILTER][$eventToFire] as $priority => $events) {
            foreach ($events as $event) {
                if ($this->isUnplug(self::PLUGIN_FILTER, $eventToFire, $event['fullPathName'], $event['className'], $event['method'])) {
                    continue;
                }
                require_once $event['fullPathName'];
                if (!method_exists($event['className'], $event['method'])) {
                    continue;
                }
                //if (!class_exists($event['className'])) {
                $class = new $event['className'];
                //}
                $class->$event['method']($parameters);
                //call_user_func(array($event['className'], $event['method']), $parameters);
                unset($class);
            }
        }

        if (!is_null($parameters)) {
            return $parameters;
        }
    }

    /**
     * Indique si un plugin s'est détaché d'un évènement particulier
     *
     * @param  integer $eventType
     * @param  string  $eventToFire
     * @param  string  $fullPathName
     * @param  string  $className
     * @param  string  $method
     * @return boolean
     */
    public function isUnplug($eventType, $eventToFire, $fullPathName, $className, $method)
    {
        $unplug = array();
        if (isset($_SESSION[self::PLUGIN_UNPLUG_SESSION_NAME])) {
            $unplug = $_SESSION[self::PLUGIN_UNPLUG_SESSION_NAME];
        } else {
            return false;
        }

        return isset($unplug[$eventType][$eventToFire][$fullPathName][$className][$method]);
    }

    /**
     * Permet à un plugin de se détacher d'un évènement
     *
     * @param  integer $eventType
     * @param  string  $eventToFire
     * @param  string  $fullPathName
     * @param  string  $className
     * @param  string  $method
     * @return void
     */
    public function unplugFromEvent($eventType, $eventToFire, $fullPathName, $className, $method)
    {
        $unplug = array();
        if (isset($_SESSION[self::PLUGIN_UNPLUG_SESSION_NAME])) {
            $unplug = $_SESSION[self::PLUGIN_UNPLUG_SESSION_NAME];
        }
        $unplug[$eventType][$eventToFire][$fullPathName][$className][$method] = true;
        $_SESSION[self::PLUGIN_UNPLUG_SESSION_NAME]                           = $unplug;
    }
}
