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
class references_registryfile
{
    public $filename;    // Nom du fichier � traiter

    /**
     * Access the only instance of this class
     *
     * @return object
     *
     * @static
     * @staticvar   object
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    public function __construct($fichier = null)
    {
        $this->setfile($fichier);
    }

    public function setfile($fichier = null)
    {
        if ($fichier) {
            $this->filename = XOOPS_UPLOAD_PATH . DIRECTORY_SEPARATOR . $fichier;
        }
    }

    public function getfile($fichier = null)
    {
        $fw = '';
        if (!$fichier) {
            $fw = $this->filename;
        } else {
            $fw = XOOPS_UPLOAD_PATH . DIRECTORY_SEPARATOR . $fichier;
        }
        if (file_exists($fw)) {
            return file_get_contents($fw);
        } else {
            return '';
        }
    }

    public function savefile($content, $fichier = null)
    {
        $fw = '';
        if (!$fichier) {
            $fw = $this->filename;
        } else {
            $fw = XOOPS_UPLOAD_PATH . DIRECTORY_SEPARATOR . $fichier;
        }
        if (file_exists($fw)) {
            @unlink($fw);
        }
        $fp = fopen($fw, 'w') || exit('Error, impossible to create the file ' . $this->filename);
        fwrite($fp, $content);
        fclose($fp);

        return true;
    }
}
