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
 * A set of useful and common functions
 *
 * @package       references
 * @author        Hervé Thouzard - Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
 *
 * Note: You should be able to use it without the need to instanciate it.
 *
 */

use WideImage\WideImage;

if (!defined('XOOPS_ROOT_PATH')) {
    die('XOOPS root path not defined');
}

class references_utils
{
    // Pour la portabilité de module à module
    const MODULE_NAME    = 'references';
    const MODULE_DIRNAME = REFERENCES_DIRNAME;
    const MODULE_PATH    = REFERENCES_PATH;
    const MODULE_URL     = REFERENCES_URL;
    const MODULE_JS_URL  = REFERENCES_JS_URL;

    /**
     * Access the only instance of this class
     *
     * @return object
     *
     * @static
     * @staticvar   object
     */
    public static function &getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new references_utils();
        }

        return $instance;
    }

    /**
     * Returns a module's option (with cache)
     *
     * @param  string  $option    module option's name
     * @param  boolean $withCache Do we have to use some cache ?
     * @return mixed   option's value
     */
    public static function getModuleOption($option, $withCache = true)
    {
        global $xoopsModuleConfig, $xoopsModule;
        $repmodule = self::MODULE_NAME;
        static $options = array();
        if (is_array($options) && array_key_exists($option, $options) && $withCache) {
            return $options[$option];
        }

        $retval = false;
        if (isset($xoopsModuleConfig) && (is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $repmodule && $xoopsModule->getVar('isactive'))) {
            if (isset($xoopsModuleConfig[$option])) {
                $retval = $xoopsModuleConfig[$option];
            }
        } else {
            $module_handler = xoops_getHandler('module');
            $module         = $module_handler->getByDirname($repmodule);
            $config_handler = xoops_getHandler('config');
            if ($module) {
                $moduleConfig = $config_handler->getConfigsByCat(0, $module->getVar('mid'));
                if (isset($moduleConfig[$option])) {
                    $retval = $moduleConfig[$option];
                }
            }
        }
        $options[$option] = $retval;

        return $retval;
    }

    /**
     * Is Xoops 2.3.x ?
     *
     * @return boolean need to say it ?
     */
    public static function isX23()
    {
        $x23 = false;
        $xv  = str_replace('XOOPS ', '', XOOPS_VERSION);
        if ((int)substr($xv, 2, 1) >= 3) {
            $x23 = true;
        }

        return $x23;
    }

    /**
     * Retreive an editor according to the module's option "form_options"
     *
     * @param  string $caption Caption to give to the editor
     * @param  string $name    Editor's name
     * @param  string $value   Editor's value
     * @param  string $width   Editor's width
     * @param  string $height  Editor's height
     * @param string  $supplemental
     * @return object The editor to use
     */
    public static function getWysiwygForm($caption, $name, $value = '', $width = '100%', $height = '400px', $supplemental = '')
    {
        $editor                   = false;
        $editor_configs           = array();
        $editor_configs['name']   = $name;
        $editor_configs['value']  = $value;
        $editor_configs['rows']   = 35;
        $editor_configs['cols']   = 60;
        $editor_configs['width']  = '100%';
        $editor_configs['height'] = '400px';

        $editor_option = strtolower(self::getModuleOption('form_options'));

        if (self::isX23()) {
            $editor = new XoopsFormEditor($caption, $editor_option, $editor_configs);

            return $editor;
        }

        // Only for Xoops 2.0.x
        switch ($editor_option) {
            case 'fckeditor':
                if (is_readable(XOOPS_ROOT_PATH . '/class/fckeditor/formfckeditor.php')) {
                    require_once(XOOPS_ROOT_PATH . '/class/fckeditor/formfckeditor.php');
                    $editor = new XoopsFormFckeditor($caption, $name, $value);
                }
                break;

            case 'htmlarea':
                if (is_readable(XOOPS_ROOT_PATH . '/class/htmlarea/formhtmlarea.php')) {
                    require_once(XOOPS_ROOT_PATH . '/class/htmlarea/formhtmlarea.php');
                    $editor = new XoopsFormHtmlarea($caption, $name, $value);
                }
                break;

            case 'dhtmltextarea':
                $editor = new XoopsFormDhtmlTextArea($caption, $name, $value, 10, 50, $supplemental);
                break;

            case 'textarea':
                $editor = new XoopsFormTextArea($caption, $name, $value);
                break;

            case 'tinyeditor':
            case 'tinymce':
                if (is_readable(XOOPS_ROOT_PATH . '/class/xoopseditor/tinyeditor/formtinyeditortextarea.php')) {
                    require_once XOOPS_ROOT_PATH . '/class/xoopseditor/tinyeditor/formtinyeditortextarea.php';
                    $editor = new XoopsFormTinyeditorTextArea(array('caption' => $caption, 'name' => $name, 'value' => $value, 'width' => '100%', 'height' => '400px'));
                }
                break;

            case 'koivi':
                if (is_readable(XOOPS_ROOT_PATH . '/class/wysiwyg/formwysiwygtextarea.php')) {
                    require_once(XOOPS_ROOT_PATH . '/class/wysiwyg/formwysiwygtextarea.php');
                    $editor = new XoopsFormWysiwygTextArea($caption, $name, $value, $width, $height, '');
                }
                break;
        }
        if (!is_object($editor)) {
            trigger_error('Error, impossible to get the requested text editor', E_USER_ERROR);
        }

        return $editor;
    }

    /**
     * Create (in a link) a javascript confirmation's box
     *
     * @param  string  $message Message to display
     * @param  boolean $form    Is this a confirmation for a form ?
     * @return string  the javascript code to insert in the link (or in the form)
     */
    public static function javascriptLinkConfirm($message, $form = false)
    {
        if (!$form) {
            return "onclick=\"javascript:return confirm('" . str_replace("'", ' ', $message) . "')\"";
        } else {
            return "onSubmit=\"javascript:return confirm('" . str_replace("'", ' ', $message) . "')\"";
        }
    }

    /**
     * Get current user IP
     *
     * @return string IP address (format Ipv4)
     */
    public static function IP()
    {
        $proxy_ip = '';
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $proxy_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $proxy_ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $proxy_ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            $proxy_ip = $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_VIA'])) {
            $proxy_ip = $_SERVER['HTTP_VIA'];
        } elseif (!empty($_SERVER['HTTP_X_COMING_FROM'])) {
            $proxy_ip = $_SERVER['HTTP_X_COMING_FROM'];
        } elseif (!empty($_SERVER['HTTP_COMING_FROM'])) {
            $proxy_ip = $_SERVER['HTTP_COMING_FROM'];
        }
        $regs = array();
        if (!empty($proxy_ip) && $is_ip = ereg('^([0-9]{1,3}\.){3,3}[0-9]{1,3}', $proxy_ip, $regs) && count($regs) > 0) {
            $the_IP = $regs[0];
        } else {
            $the_IP = $_SERVER['REMOTE_ADDR'];
        }

        return $the_IP;
    }

    /**
     * Set the page's title, meta description and meta keywords
     * Datas are supposed to be sanitized
     *
     * @param  string $pageTitle       Page's Title
     * @param  string $metaDescription Page's meta description
     * @param  string $metaKeywords    Page's meta keywords
     * @return void
     */
    public static function setMetas($pageTitle = '', $metaDescription = '', $metaKeywords = '')
    {
        global $xoTheme, $xoTheme, $xoopsTpl;
        $xoopsTpl->assign('xoops_pagetitle', $pageTitle);
        if (isset($xoTheme) && is_object($xoTheme)) {
            if (!empty($metaKeywords)) {
                $xoTheme->addMeta('meta', 'keywords', $metaKeywords);
            }
            if (!empty($metaDescription)) {
                $xoTheme->addMeta('meta', 'description', $metaDescription);
            }
        } elseif (isset($xoopsTpl) && is_object($xoopsTpl)) {    // Compatibility for old Xoops versions
            if (!empty($metaKeywords)) {
                $xoopsTpl->assign('xoops_meta_keywords', $metaKeywords);
            }
            if (!empty($metaDescription)) {
                $xoopsTpl->assign('xoops_meta_description', $metaDescription);
            }
        }
    }

    /**
     * Send an email from a template to a list of recipients
     *
     * @param         $tplName
     * @param  array  $recipients List of recipients
     * @param  string $subject    Email's subject
     * @param  array  $variables  Varirables to give to the template
     * @return bool Result of the send
     * @internal param string $tpl_name Template's name
     */
    public static function sendEmailFromTpl($tplName, $recipients, $subject, $variables)
    {
        global $xoopsConfig;
        require_once XOOPS_ROOT_PATH . '/class/xoopsmailer.php';
        if (!is_array($recipients)) {
            if (trim($recipients) == '') {
                return false;
            }
        } else {
            if (count($recipients) == 0) {
                return false;
            }
        }
        if (function_exists('xoops_getMailer')) {
            $xoopsMailer =& xoops_getMailer();
        } else {
            $xoopsMailer =& getMailer();
        }

        $xoopsMailer->useMail();
        $xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH . '/modules/' . self::MODULE_NAME . '/language/' . $xoopsConfig['language'] . '/mail_template');
        $xoopsMailer->setTemplate($tplName);
        $xoopsMailer->setToEmails($recipients);
        // TODO: Change !
        // $xoopsMailer->setFromEmail('contact@monsite.com');
        //$xoopsMailer->setFromName('MonSite');
        $xoopsMailer->setSubject($subject);
        foreach ($variables as $key => $value) {
            $xoopsMailer->assign($key, $value);
        }
        $res = $xoopsMailer->send();
        unset($xoopsMailer);
        $filename = XOOPS_UPLOAD_PATH . '/logmail_' . self::MODULE_NAME . '.php';
        if (!file_exists($filename)) {
            $fp = @fopen($filename, 'a');
            if ($fp) {
                fwrite($fp, '<?php exit(); ?>');
                fclose($fp);
            }
        }
        $fp = @fopen($filename, 'a');

        if ($fp) {
            fwrite($fp, str_repeat('-', 120) . "\n");
            fwrite($fp, date('d/m/Y H:i:s') . "\n");
            fwrite($fp, 'Template name : ' . $tplName . "\n");
            fwrite($fp, 'Email subject : ' . $subject . "\n");
            if (is_array($recipients)) {
                fwrite($fp, 'Recipient(s) : ' . implode(',', $recipients) . "\n");
            } else {
                fwrite($fp, 'Recipient(s) : ' . $recipients . "\n");
            }
            fwrite($fp, 'Transmited variables : ' . implode(',', $variables) . "\n");
            fclose($fp);
        }

        return $res;
    }

    /**
     * Remove module's cache
     */
    public static function updateCache()
    {
        global $xoopsModule;
        $folder  = $xoopsModule->getVar('dirname');
        $tpllist = array();
        require_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
        require_once XOOPS_ROOT_PATH . '/class/template.php';
        $tplfile_handler = xoops_getHandler('tplfile');
        $tpllist         = $tplfile_handler->find(null, null, null, $folder);
        xoops_template_clear_module_cache($xoopsModule->getVar('mid'));            // Clear module's blocks cache

        foreach ($tpllist as $onetemplate) {    // Remove cache for each page.
            if ($onetemplate->getVar('tpl_type') === 'module') {
                //  Note, I've been testing all the other methods (like the one of Smarty) and none of them run, that's why I have used this code
                $files_del = array();
                $files_del = glob(XOOPS_CACHE_PATH . '/*' . $onetemplate->getVar('tpl_file') . '*');
                if (count($files_del) > 0 && is_array($files_del)) {
                    foreach ($files_del as $one_file) {
                        if (is_file($one_file)) {
                            unlink($one_file);
                        }
                    }
                }
            }
        }
    }

    /**
     * Redirect user with a message
     *
     * @param string $message message to display
     * @param string $url     The place where to go
     * @param        integer  timeout Time to wait before to redirect
     */
    public static function redirect($message = '', $url = 'index.php', $time = 2)
    {
        redirect_header($url, $time, $message);
        exit();
    }

    /**
     * Internal function used to get the handler of the current module
     *
     * @return object The module
     */
    public static function getModule()
    {
        static $mymodule;
        if (!isset($mymodule)) {
            global $xoopsModule;
            if (isset($xoopsModule) && is_object($xoopsModule) && $xoopsModule->getVar('dirname') == REFERENCES_DIRNAME) {
                $mymodule =& $xoopsModule;
            } else {
                $hModule  = xoops_getHandler('module');
                $mymodule = $hModule->getByDirname(REFERENCES_DIRNAME);
            }
        }

        return $mymodule;
    }

    /**
     * Returns the module's name (as defined by the user in the module manager) with cache
     * @return string Module's name
     */
    public static function getModuleName()
    {
        static $moduleName;
        if (!isset($moduleName)) {
            $mymodule   = self::getModule();
            $moduleName = $mymodule->getVar('name');
        }

        return $moduleName;
    }

    /**
     * Create a title for the href tags inside html links
     *
     * @param  string $title Text to use
     * @return string Formated text
     */
    public static function makeHrefTitle($title)
    {
        $s = "\"'";
        $r = '  ';

        return strtr($title, $s, $r);
    }

    /**
     * Retourne la liste des utilisateurs appartenants � un groupe
     *
     * @param  int $groupId Searched group
     * @return array Array of XoopsUsers
     */
    public static function getUsersFromGroup($groupId)
    {
        $users          = array();
        $member_handler = xoops_getHandler('member');
        $users          = $member_handler->getUsersByGroup($groupId, true);

        return $users;
    }

    /**
     * Retourne les ID des utilisateurs faisant partie de plusieurs groupes
     * @param  array $groups Les ID des groupes dont on recherche les ID utilisateurs
     * @return array Les ID utilisateurs
     */
    public static function getUsersIdsFromGroups($groups)
    {
        $usersIds       = array();
        $member_handler = xoops_getHandler('member');
        foreach ($groups as $groupId) {
            $temporaryGroup = array();
            $temporaryGroup = $member_handler->getUsersByGroup($groupId);
            if (count($temporaryGroup) > 0) {
                $usersIds = array_merge($usersIds, $temporaryGroup);
            }
        }

        return array_unique($usersIds);
    }

    /**
     * Retourne la liste des emails des utilisateurs membres d'un groupe
     *
     * @param $groupId
     * @return array Emails list
     * @internal param int $group_id Group's number
     */
    public static function getEmailsFromGroup($groupId)
    {
        $ret   = array();
        $users = self::getUsersFromGroup($groupId);
        foreach ($users as $user) {
            $ret[] = $user->getVar('email');
        }

        return $ret;
    }

    /**
     * V�rifie que l'utilisateur courant fait partie du groupe des administrateurs
     *
     * @return booleean Admin or not
     */
    public static function isAdmin()
    {
        global $xoopsUser, $xoopsModule;
        if (is_object($xoopsUser)) {
            if (in_array(XOOPS_GROUP_ADMIN, $xoopsUser->getGroups())) {
                return true;
            } elseif (isset($xoopsModule) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the current date in the Mysql format
     *
     * @return string Date in the Mysql format
     */
    public static function getCurrentSQLDate()
    {
        return date('Y-m-d');    // 2007-05-02
    }

    public static function getCurrentSQLDateTime()
    {
        return date('Y-m-d H:i:s');    // 2007-05-02
    }

    /**
     * Convert a Mysql date to the human's format
     *
     * @param  string $date The date to convert
     * @param string  $format
     * @return string The date in a human form
     */
    public static function SQLDateToHuman($date, $format = 'l')
    {
        if ($date != '0000-00-00' && xoops_trim($date) != '') {
            return formatTimestamp(strtotime($date), $format);
        } else {
            return '';
        }
    }

    /**
     * Convert a timestamp to a Mysql date
     *
     * @param  integer $timestamp The timestamp to use
     * @return string  The date in the Mysql format
     */
    public static function timestampToMysqlDate($timestamp)
    {
        return date('Y-m-d', (int)$timestamp);
    }

    /**
     * Conversion d'un dateTime Mysql en date lisible en fran�ais
     * @param $dateTime
     * @return bool|string
     */
    public static function sqlDateTimeToFrench($dateTime)
    {
        return date('d/m/Y H:i:s', strtotime($dateTime));
    }

    /**
     * Convert a timestamp to a Mysql datetime form
     * @param  integer $timestamp The timestamp to use
     * @return string  The date and time in the Mysql format
     */
    public static function timestampToMysqlDateTime($timestamp)
    {
        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * This function indicates if the current Xoops version needs to add asterisks to required fields in forms
     *
     * @return boolean Yes = we need to add them, false = no
     */
    public static function needsAsterisk()
    {
        if (self::isX23()) {
            return false;
        }
        if (strpos(strtolower(XOOPS_VERSION), 'impresscms') !== false) {
            return false;
        }
        if (strpos(strtolower(XOOPS_VERSION), 'legacy') === false) {
            $xv = xoops_trim(str_replace('XOOPS ', '', XOOPS_VERSION));
            if ((int)substr($xv, 4, 2) >= 17) {
                return false;
            }
        }

        return true;
    }

    /**
     * Mark the mandatory fields of a form with a star
     *
     * @param  object $sform The form to modify
     * @return object The modified form
     * @internal param string $caracter The character to use to mark fields
     */
    public static function &formMarkRequiredFields(&$sform)
    {
        if (self::needsAsterisk()) {
            $required = array();
            foreach ($sform->getRequired() as $item) {
                $required[] = $item->_name;
            }
            $elements = array();
            $elements = &$sform->getElements();
            $cnt      = count($elements);
            for ($i = 0; $i < $cnt; ++$i) {
                if (is_object($elements[$i]) && in_array($elements[$i]->_name, $required)) {
                    $elements[$i]->_caption .= ' *';
                }
            }
        }

        return $sform;
    }

    /**
     * Create an html heading (from h1 to h6)
     *
     * @param  string  $title The text to use
     * @param  integer $level Level to return
     * @return string  The heading
     */
    public static function htitle($title = '', $level = 1)
    {
        printf('<h%01d>%s</h%01d>', $level, $title, $level);
    }

    /**
     * Create a unique upload filename
     *
     * @param  string  $folder   The folder where the file will be saved
     * @param  string  $fileName Original filename (coming from the user)
     * @param  boolean $trimName Do we need to create a short unique name ?
     * @return string  The unique filename to use (with its extension)
     */
    public static function createUploadName($folder, $fileName, $trimName = false)
    {
        $workingfolder = $folder;
        if (substr($workingfolder, strlen($workingfolder) - 1, 1) !== '/') {
            $workingfolder .= '/';
        }
        $ext  = basename($fileName);
        $ext  = explode('.', $ext);
        $ext  = '.' . $ext[count($ext) - 1];
        $true = true;
        while ($true) {
            $ipbits = explode('.', $_SERVER['REMOTE_ADDR']);
            list($usec, $sec) = explode(' ', microtime());
            $usec = (integer)($usec * 65536);
            $sec  = ((integer)$sec) & 0xFFFF;

            if ($trimName) {
                $uid = sprintf('%06x%04x%04x', ($ipbits[0] << 24) | ($ipbits[1] << 16) | ($ipbits[2] << 8) | $ipbits[3], $sec, $usec);
            } else {
                $uid = sprintf('%08x-%04x-%04x', ($ipbits[0] << 24) | ($ipbits[1] << 16) | ($ipbits[2] << 8) | $ipbits[3], $sec, $usec);
            }
            if (!file_exists($workingfolder . $uid . $ext)) {
                $true = false;
            }
        }

        return $uid . $ext;
    }

    /**
     * Replace html entities with their ASCII equivalent
     *
     * @param  string $chaine The string undecode
     * @return string The undecoded string
     */
    public static function unhtml($chaine)
    {
        $search = $replace = array();
        $chaine = html_entity_decode($chaine);

        for ($i = 0; $i <= 255; ++$i) {
            $search[]  = '&#' . $i . ';';
            $replace[] = chr($i);
        }
		$replace[]='...'; $search[]='…';
		$replace[]="'";	$search[]='‘';
		$replace[]="'";	$search[]= '’';
		$replace[]='-';	$search[] = '&bull;';	// $replace[] = '•';
		$replace[]='—'; $search[]='&mdash;';
		$replace[]='-'; $search[]='&ndash;';
		$replace[]='-'; $search[]='&shy;';
		$replace[]='"'; $search[]='&quot;';
		$replace[]='&'; $search[]='&amp;';
		$replace[]='ˆ'; $search[]='&circ;';
		$replace[]='¡'; $search[]='&iexcl;';
		$replace[]='¦'; $search[]='&brvbar;';
		$replace[]='¨'; $search[]='&uml;';
		$replace[]='¯'; $search[]='&macr;';
		$replace[]='´'; $search[]='&acute;';
		$replace[]='¸'; $search[]='&cedil;';
		$replace[]='¿'; $search[]='&iquest;';
		$replace[]='˜'; $search[]='&tilde;';
		$replace[]="'"; $search[]='&lsquo;';	// $replace[]='‘';
		$replace[]="'"; $search[]='&rsquo;';	// $replace[]='’';
		$replace[]='‚'; $search[]='&sbquo;';
		$replace[]="'"; $search[]='&ldquo;';	// $replace[]='“';
		$replace[]="'"; $search[]='&rdquo;';	// $replace[]='”';
		$replace[]='„'; $search[]='&bdquo;';
		$replace[]='‹'; $search[]='&lsaquo;';
		$replace[]='›'; $search[]='&rsaquo;';
		$replace[]='<'; $search[]='&lt;';
		$replace[]='>'; $search[]='&gt;';
		$replace[]='±'; $search[]='&plusmn;';
		$replace[]='«'; $search[]='&laquo;';
		$replace[]='»'; $search[]='&raquo;';
		$replace[]='×'; $search[]='&times;';
		$replace[]='÷'; $search[]='&divide;';
		$replace[]='¢'; $search[]='&cent;';
		$replace[]='£'; $search[]='&pound;';
		$replace[]='¤'; $search[]='&curren;';
		$replace[]='¥'; $search[]='&yen;';
		$replace[]='§'; $search[]='&sect;';
		$replace[]='©'; $search[]='&copy;';
		$replace[]='¬'; $search[]='&not;';
		$replace[]='®'; $search[]='&reg;';
		$replace[]='°'; $search[]='&deg;';
		$replace[]='µ'; $search[]='&micro;';
		$replace[]='¶'; $search[]='&para;';
		$replace[]='·'; $search[]='&middot;';
		$replace[]='†'; $search[]='&dagger;';
		$replace[]='‡'; $search[]='&Dagger;';
		$replace[]='‰'; $search[]='&permil;';
		$replace[]='Euro'; $search[]='&euro;';		// $replace[]='€'
		$replace[]='¼'; $search[]='&frac14;';
		$replace[]='½'; $search[]='&frac12;';
		$replace[]='¾'; $search[]='&frac34;';
		$replace[]='¹'; $search[]='&sup1;';
		$replace[]='²'; $search[]='&sup2;';
		$replace[]='³'; $search[]='&sup3;';
		$replace[]='á'; $search[]='&aacute;';
		$replace[]='Á'; $search[]='&Aacute;';
		$replace[]='â'; $search[]='&acirc;';
		$replace[]='Â'; $search[]='&Acirc;';
		$replace[]='à'; $search[]='&agrave;';
		$replace[]='À'; $search[]='&Agrave;';
		$replace[]='å'; $search[]='&aring;';
		$replace[]='Å'; $search[]='&Aring;';
		$replace[]='ã'; $search[]='&atilde;';
		$replace[]='Ã'; $search[]='&Atilde;';
		$replace[]='ä'; $search[]='&auml;';
		$replace[]='Ä'; $search[]='&Auml;';
		$replace[]='ª'; $search[]='&ordf;';
		$replace[]='æ'; $search[]='&aelig;';
		$replace[]='Æ'; $search[]='&AElig;';
		$replace[]='ç'; $search[]='&ccedil;';
		$replace[]='Ç'; $search[]='&Ccedil;';
		$replace[]='ð'; $search[]='&eth;';
		$replace[]='Ð'; $search[]='&ETH;';
		$replace[]='é'; $search[]='&eacute;';
		$replace[]='É'; $search[]='&Eacute;';
		$replace[]='ê'; $search[]='&ecirc;';
		$replace[]='Ê'; $search[]='&Ecirc;';
		$replace[]='è'; $search[]='&egrave;';
		$replace[]='È'; $search[]='&Egrave;';
		$replace[]='ë'; $search[]='&euml;';
		$replace[]='Ë'; $search[]='&Euml;';
		$replace[]='ƒ'; $search[]='&fnof;';
		$replace[]='í'; $search[]='&iacute;';
		$replace[]='Í'; $search[]='&Iacute;';
		$replace[]='î'; $search[]='&icirc;';
		$replace[]='Î'; $search[]='&Icirc;';
		$replace[]='ì'; $search[]='&igrave;';
		$replace[]='Ì'; $search[]='&Igrave;';
		$replace[]='ï'; $search[]='&iuml;';
		$replace[]='Ï'; $search[]='&Iuml;';
		$replace[]='ñ'; $search[]='&ntilde;';
		$replace[]='Ñ'; $search[]='&Ntilde;';
		$replace[]='ó'; $search[]='&oacute;';
		$replace[]='Ó'; $search[]='&Oacute;';
		$replace[]='ô'; $search[]='&ocirc;';
		$replace[]='Ô'; $search[]='&Ocirc;';
		$replace[]='ò'; $search[]='&ograve;';
		$replace[]='Ò'; $search[]='&Ograve;';
		$replace[]='º'; $search[]='&ordm;';
		$replace[]='ø'; $search[]='&oslash;';
		$replace[]='Ø'; $search[]='&Oslash;';
		$replace[]='õ'; $search[]='&otilde;';
		$replace[]='Õ'; $search[]='&Otilde;';
		$replace[]='ö'; $search[]='&ouml;';
		$replace[]='Ö'; $search[]='&Ouml;';
		$replace[]='œ'; $search[]='&oelig;';
		$replace[]='Œ'; $search[]='&OElig;';
		$replace[]='š'; $search[]='&scaron;';
		$replace[]='Š'; $search[]='&Scaron;';
		$replace[]='ß'; $search[]='&szlig;';
		$replace[]='þ'; $search[]='&thorn;';
		$replace[]='Þ'; $search[]='&THORN;';
		$replace[]='ú'; $search[]='&uacute;';
		$replace[]='Ú'; $search[]='&Uacute;';
		$replace[]='û'; $search[]='&ucirc;';
		$replace[]='Û'; $search[]='&Ucirc;';
		$replace[]='ù'; $search[]='&ugrave;';
		$replace[]='Ù'; $search[]='&Ugrave;';
		$replace[]='ü'; $search[]='&uuml;';
		$replace[]='Ü'; $search[]='&Uuml;';
		$replace[]='ý'; $search[]='&yacute;';
		$replace[]='Ý'; $search[]='&Yacute;';
		$replace[]='ÿ'; $search[]='&yuml;';
		$replace[]='Ÿ'; $search[]='&Yuml;';
        $chaine    = str_replace($search, $replace, $chaine);

        return $chaine;
    }

    /**
     * Création d'une titre pour être utilisé par l'url rewriting
     *
     * @param string  $content  Le texte à utiliser pour créer l'url
     * @param integer $urw      La limite basse pour créer les mots
     * @return string Le texte à utiliser pour l'url
     *                          Note, some parts are from Solo's code
     */
    public static function makeSeoUrl($content, $urw = 1)
    {
        $s       = "ÀÁÂÃÄÅÒÓÔÕÖØÈÉÊËÇÌÍÎÏÙÚÛÜŸÑàáâãäåòóôõöøèéêëçìíîïùúûüÿñ '()";
        $r       = 'AAAAAAOOOOOOEEEECIIIIUUUUYNaaaaaaooooooeeeeciiiiuuuuyn----';
        $content = self::unhtml($content);    // First, remove html entities
        $content = strtr($content, $s, $r);
        $content = strip_tags($content);
        $content = strtolower($content);
        $content = htmlentities($content);    // TODO: Vérifier
        $content = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde);/', '$1', $content);
        $content = html_entity_decode($content);
        $content = eregi_replace('quot', ' ', $content);
        $content = eregi_replace("'", ' ', $content);
        $content = eregi_replace('-', ' ', $content);
        $content = eregi_replace('[[:punct:]]', '', $content);
        // Selon option mais attention au fichier .htaccess !
        // $content = eregi_replace('[[:digit:]]','', $content);
        $content = preg_replace('/[^a-z|A-Z|0-9]/', '-', $content);

        $words    = explode(' ', $content);
        $keywords = '';
        foreach ($words as $word) {
            if (strlen($word) >= $urw) {
                $keywords .= '-' . trim($word);
            }
        }
        if (!$keywords) {
            $keywords = '-';
        }
        // Supprime les tirets en double
        $keywords = str_replace('---', '-', $keywords);
        $keywords = str_replace('--', '-', $keywords);
        // Supprime un éventuel tiret à la fin de la chaine
        if (substr($keywords, strlen($keywords) - 1, 1) == '-') {
            $keywords = substr($keywords, 0, strlen($keywords) - 1);
        }

        return $keywords;
    }

    /**
     * Create the meta keywords based on the content
     *
     * @param  string $content Content from which we have to create metakeywords
     * @return string The list of meta keywords
     */
    public static function createMetaKeywords($content)
    {
        $keywordscount = REFERENCES_METAGEN_MAX_KEYWORDS;
        $keywordsorder = REFERENCES_METAGEN_KEYWORDS_ORDER;

        $tmp = array();
        // Search for the "Minimum keyword length"
        // TODO: Remplacer references_keywords_limit par une constante
        if (isset($_SESSION['references_keywords_limit'])) {
            $limit = $_SESSION['references_keywords_limit'];
        } else {
            $config_handler                        = xoops_getHandler('config');
            $xoopsConfigSearch                     =& $config_handler->getConfigsByCat(XOOPS_CONF_SEARCH);
            $limit                                 = $xoopsConfigSearch['keyword_min'];
            $_SESSION['references_keywords_limit'] = $limit;
        }
        $myts            = MyTextSanitizer::getInstance();
        $content         = str_replace('<br>', ' ', $content);
        $content         = $myts->undoHtmlSpecialChars($content);
        $content         = strip_tags($content);
        $content         = strtolower($content);
        $search_pattern  = array('&nbsp;', "\t", "\r\n", "\r", "\n", ',', '.', "'", ';', ':', ')', '(', '"', '?', '!', '{', '}', '[', ']', '<', '>', '/', '+', '-', '_', '\\', '*');
        $replace_pattern = array(' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
        $content         = str_replace($search_pattern, $replace_pattern, $content);
        $keywords        = explode(' ', $content);
        switch ($keywordsorder) {
            case 0:    // Ordre d'apparition dans le texte
                $keywords = array_unique($keywords);
                break;
            case 1:    // Ordre de fréquence des mots
                $keywords = array_count_values($keywords);
                asort($keywords);
                $keywords = array_keys($keywords);
                break;
            case 2:    // Ordre inverse de la fréquence des mots
                $keywords = array_count_values($keywords);
                arsort($keywords);
                $keywords = array_keys($keywords);
                break;
        }
        // Remove black listed words
        if (xoops_trim(self::getModuleOption('metagen_blacklist')) != '') {
            $metagen_blacklist = str_replace("\r", '', self::getModuleOption('metagen_blacklist'));
            $metablack         = explode("\n", $metagen_blacklist);
            array_walk($metablack, 'trim');
            $keywords = array_diff($keywords, $metablack);
        }

        foreach ($keywords as $keyword) {
            if (strlen($keyword) >= $limit && !is_numeric($keyword)) {
                $tmp[] = $keyword;
            }
        }
        $tmp = array_slice($tmp, 0, $keywordscount);
        if (count($tmp) > 0) {
            return implode(',', $tmp);
        } else {
            if (!isset($config_handler) || !is_object($config_handler)) {
                $config_handler = xoops_getHandler('config');
            }
            $xoopsConfigMetaFooter =& $config_handler->getConfigsByCat(XOOPS_CONF_METAFOOTER);
            if (isset($xoopsConfigMetaFooter['meta_keywords'])) {
                return $xoopsConfigMetaFooter['meta_keywords'];
            } else {
                return '';
            }
        }
    }

    /**
     * Fonction chargée de gérer l'upload
     *
     * @param integer $indice L'indice du fichier à télécharger
     * @param string  $dstpath
     * @param null    $mimeTypes
     * @param null    $uploadMaxSize
     * @param null    $maxWidth
     * @param null    $maxHeight
     * @return mixed True si l'upload s'est bien déroulé sinon le message d'erreur correspondant
     */
    public static function uploadFile($indice, $dstpath = XOOPS_UPLOAD_PATH, $mimeTypes = null, $uploadMaxSize = null, $maxWidth = null, $maxHeight = null)
    {
        require_once XOOPS_ROOT_PATH . '/class/uploader.php';
        global $destname;
        if (isset($_POST['xoops_upload_file'])) {
            require_once XOOPS_ROOT_PATH . '/class/uploader.php';
            $fldname = '';
            $fldname = $_FILES[$_POST['xoops_upload_file'][$indice]];
            $fldname = get_magic_quotes_gpc() ? stripslashes($fldname['name']) : $fldname['name'];
            if (xoops_trim($fldname != '')) {
                $destname = self::createUploadName($dstpath, $fldname, true);
                if ($mimeTypes === null) {
                    $permittedtypes = explode("\n", str_replace("\r", '', self::getModuleOption('mimetypes')));
                    array_walk($permittedtypes, 'trim');
                } else {
                    $permittedtypes = $mimeTypes;
                }
                if ($uploadMaxSize === null) {
                    $uploadSize = self::getModuleOption('maxuploadsize');
                } else {
                    $uploadSize = $uploadMaxSize;
                }
                $uploader = new XoopsMediaUploader($dstpath, $permittedtypes, $uploadSize, $maxWidth, $maxHeight);
                //$uploader->allowUnknownTypes = true;
                $uploader->setTargetFileName($destname);
                if ($uploader->fetchMedia($_POST['xoops_upload_file'][$indice])) {
                    if ($uploader->upload()) {
                        return true;
                    } else {
                        return _ERRORS . ' ' . htmlentities($uploader->getErrors());
                    }
                } else {
                    return htmlentities($uploader->getErrors());
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Resize a Picture to some given dimensions (using the wideImage library)
     *
     * @param string  $src_path      Picture's source
     * @param string  $dst_path      Picture's destination
     * @param integer $param_width   Maximum picture's width
     * @param integer $param_height  Maximum picture's height
     * @param boolean $keep_original Do we have to keep the original picture ?
     * @param string  $fit           Resize mode (see the wideImage library for more information)
     * @return bool
     */
    public static function resizePicture($src_path, $dst_path, $param_width, $param_height, $keep_original = false, $fit = 'inside')
    {
        //        require_once self::MODULE_PATH . 'class/wideimage/WideImage.php';
        $img    = WideImage::load($src_path);
        $result = $img->resize($param_width, $param_height, $fit);
        $result->saveToFile($dst_path, null);
        if (!$keep_original) {
            @unlink($src_path);
        }

        return true;
    }

    /**
     * Déclenchement d'une alerte Xoops suite à un évènement
     *
     * @param string       $category La catégorie de l'évènement
     * @param integer      $itemId   L'ID de l'élément (trop général pour être décris précisément)
     * @param unknown_type $event    L'évènement qui est déclencé
     * @param unknown_type $tags     Les variables à passer au template
     */
    public static function notify($category, $itemId, $event, $tags)
    {
        $notification_handler = xoops_getHandler('notification');
        $tags['X_MODULE_URL'] = REFERENCES_URL;
        $notification_handler->triggerEvent($category, $itemId, $event, $tags);
    }

    /**
     * Ajoute des jours à une date et retourne la nouvelle date au format Date de Mysql
     *
     * @param int     $duration
     * @param integer $startingDate Date de départ (timestamp)
     * @return bool|string
     * @internal param int $durations Durée en jours
     */
    public static function addDaysToDate($duration = 1, $startingDate = 0)
    {
        if ($startingDate == 0) {
            $startingDate = time();
        }
        $endingDate = $startingDate + ($duration * 86400);

        return date('Y-m-d', $endingDate);
    }

    /**
     * Retourne un breadcrumb en fonction des paramètres passés et en partant (d'office) de la racine du module
     *
     * @param array  $path  Le chemin complet (excepté la racine) du breadcrumb sous la forme clé=url valeur=titre
     * @param string $raquo Le séparateur par défaut à utiliser
     * @return string le breadcrumb
     */
    public static function breadcrumb($path, $raquo = ' &raquo; ')
    {
        $breadcrumb        = '';
        $workingBreadcrumb = array();
        if (is_array($path)) {
            $moduleName          = self::getModuleName();
            $workingBreadcrumb[] = "<a href='" . REFERENCES_URL . "' title='" . self::makeHrefTitle($moduleName) . "'>" . $moduleName . '</a>';
            foreach ($path as $url => $title) {
                $workingBreadcrumb[] = "<a href='" . $url . "'>" . $title . '</a>';
            }
            $cnt = count($workingBreadcrumb);
            for ($i = 0; $i < $cnt; ++$i) {
                if ($i == $cnt - 1) {
                    $workingBreadcrumb[$i] = strip_tags($workingBreadcrumb[$i]);
                }
            }
            $breadcrumb = implode($raquo, $workingBreadcrumb);
        }

        return $breadcrumb;
    }

    public static function close_tags($string)
    {
        // match opened tags
        if (preg_match_all('/<([a-z\:\-]+)[^\/]>/', $string, $start_tags)) {
            $start_tags = $start_tags[1];

            // match closed tags
            if (preg_match_all('/<\/([a-z]+)>/', $string, $end_tags)) {
                $complete_tags = array();
                $end_tags      = $end_tags[1];

                foreach ($start_tags as $key => $val) {
                    $posb = array_search($val, $end_tags);
                    if (is_int($posb)) {
                        unset($end_tags[$posb]);
                    } else {
                        $complete_tags[] = $val;
                    }
                }
            } else {
                $complete_tags = $start_tags;
            }

            $complete_tags = array_reverse($complete_tags);
            for ($i = 0; $i < count($complete_tags); ++$i) {
                $string .= '</' . $complete_tags[$i] . '>';
            }
        }

        return $string;
    }

    public static function truncate_tagsafe($string, $length = 80, $etc = '...', $break_words = false)
    {
        if ($length == 0) {
            return '';
        }

        if (strlen($string) > $length) {
            $length -= strlen($etc);
            if (!$break_words) {
                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
                $string = preg_replace('/<[^>]*$/', '', $string);
                $string = self::close_tags($string);
            }

            return $string . $etc;
        } else {
            return $string;
        }
    }

    /**
     * Create an infotip
     * @param $text
     * @return string
     */
    public static function makeInfotips($text)
    {
        $ret      = '';
        $infotips = self::getModuleOption('infotips');
        if ($infotips > 0) {
            $myts = MyTextSanitizer::getInstance();
            $ret  = $myts->htmlSpecialChars(substr(strip_tags($text), 0, $infotips));
        }

        return $ret;
    }

    /**
     * Retourne le type Mime d'un fichier en utilisant d'abord finfo puis mime_content
     *
     * @param string $filename Le fichier (avec son chemin d'accès complet) dont on veut connaître le type mime
     * @return string
     */
    public static function getMimeType($filename)
    {
        if (function_exists('finfo_open')) {
            $pathToMagic = REFERENCES_PATH . 'mime/magic';
            $finfo       = new finfo(FILEINFO_MIME, $pathToMagic);
            $mimetype    = $finfo->file($filename);
            finfo_close($finfo);
            unset($finfo);

            return $mimetype;
        } else {
            if (function_exists('mime_content_type')) {
                return mime_content_type($filename);
            } else {
                return '';
            }
        }
    }

    /**
     * Retourne une liste d'objets XoopsUsers à partir d'une liste d'identifiants
     *
     * @param  array $xoopsUsersIDs La liste des ID
     * @return array Les objets XoopsUsers
     */
    public static function getUsersFromIds($xoopsUsersIDs)
    {
        $users = array();
        if (is_array($xoopsUsersIDs) && count($xoopsUsersIDs) > 0) {
            $xoopsUsersIDs = array_unique($xoopsUsersIDs);
            sort($xoopsUsersIDs);
            if (count($xoopsUsersIDs) > 0) {
                $member_handler = xoops_getHandler('user');
                $criteria       = new Criteria('uid', '(' . implode(',', $xoopsUsersIDs) . ')', 'IN');
                $criteria->setSort('uid');
                $users = $member_handler->getObjects($criteria, true);
            }
        }

        return $users;
    }

    /**
     * Retourne l'ID de l'utilisateur courant (s'il est connecté)
     * @return integer L'uid ou 0
     */
    public static function getCurrentUserID()
    {
        global $xoopsUser;
        $uid = is_object($xoopsUser) ? $xoopsUser->getVar('uid') : 0;

        return $uid;
    }

    /**
     * Retourne la liste des groupes de l'utilisateur courant (avec cache)
     * @param int $uid
     * @return array Les ID des groupes auquel l'utilisateur courant appartient
     */
    public static function getMemberGroups($uid = 0)
    {
        static $buffer = array();
        if ($uid == 0) {
            $uid = self::getCurrentUserID();
        }

        if (is_array($buffer) && count($buffer) > 0 && isset($buffer[$uid])) {
            return $buffer[$uid];
        } else {
            if ($uid > 0) {
                $member_handler = xoops_getHandler('member');
                $buffer[$uid]   = $member_handler->getGroupsByUser($uid, false);    // Renvoie un tableau d'ID (de groupes)
            } else {
                $buffer[$uid] = array(XOOPS_GROUP_ANONYMOUS);
            }
        }

        return $buffer[$uid];
    }

    /**
     * Indique si l'utilisateur courant fait partie d'une groupe donné (avec gestion de cache)
     *
     * @param integer $group Groupe recherché
     * @param int     $uid
     * @return bool vrai si l'utilisateur fait partie du groupe, faux sinon
     */
    public static function isMemberOfGroup($group = 0, $uid = 0)
    {
        static $buffer = array();
        $retval = false;
        if ($uid == 0) {
            $uid = self::getCurrentUserID();
        }
        if (is_array($buffer) && array_key_exists($group, $buffer)) {
            $retval = $buffer[$group];
        } else {
            $member_handler = xoops_getHandler('member');
            $groups         = $member_handler->getGroupsByUser($uid, false);    // Renvoie un tableau d'ID (de groupes)
            $retval         = in_array($group, $groups);
            $buffer[$group] = $retval;
        }

        return $retval;
    }

    /**
     * Indique si un utilisateur fait partie de plusieurs groupes
     * La fonction renvoie vrai dès qu'on trouve que le membre fait partir d'un des groupes
     * @param array    $groups La liste des groupes à vérifier
     * @param  integer $uid    L'ID de l'utilisateur
     * @return boolean
     */
    public static function isMemberOfGroups($groups, $uid = 0)
    {
        if (count($groups) == 0) {
            return false;
        }
        if ($uid == 0) {
            $uid = self::getCurrentUserID();
        }
        foreach ($groups as $groupId) {
            if (self::isMemberOfGroup($groupId, $uid)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retourne le nombre total de groupes (y compris les anonymes)
     */
    public static function getGroupsCount()
    {
        static $cache = -1;
        if ($cache == -1) {
            global $xoopsDB;
            $sql    = 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix('groups');
            $result = $xoopsDB->query($sql);
            if (!$result) {
                return false;
            }
            list($cache) = $xoopsDB->fetchRow($result);
        }

        return $cache;
    }

    /**
     * Fonction chargée de vérifier qu'un répertoire existe, qu'on peut écrire dedans et création d'un fichier index.html
     *
     * @param string $folder Le chemin complet du répertoire à vérifier
     * @return void
     */
    public static function prepareFolder($folder)
    {
        if (!is_dir($folder)) {
            mkdir($folder, 0777);
            file_put_contents($folder . '/index.html', '<script>history.go(-1);</script>');
        } else {
            if (!is_writable($folder)) {
                chmod($folder, 0777);
            }
        }
    }

    /**
     * Load a language file
     *
     * @param string $languageFile     The required language file
     * @param string $defaultExtension Default extension to use
     */
    public static function loadLanguageFile($languageFile, $defaultExtension = '.php')
    {
        global $xoopsConfig;
        $root = self::MODULE_PATH;
        if (strstr($languageFile, $defaultExtension) === false) {
            $languageFile .= $defaultExtension;
        }
        if (file_exists($root . 'language' . DIRECTORY_SEPARATOR . $xoopsConfig['language'] . DIRECTORY_SEPARATOR . $languageFile)) {
            require_once $root . 'language' . DIRECTORY_SEPARATOR . $xoopsConfig['language'] . DIRECTORY_SEPARATOR . $languageFile;
        } else {    // Fallback
            require_once $root . 'language' . DIRECTORY_SEPARATOR . 'english' . DIRECTORY_SEPARATOR . $languageFile;
        }
    }

    /**
     * Création d'une vignette en tenant compte des options du module concernant le mode de création des vignettes
     *
     * @param  string  $src_path
     * @param  string  $dst_path
     * @param  integer $param_width
     * @param  integer $param_height
     * @param  boolean $keep_original
     * @param string   $fit Utilisé uniquement pour la méthode 0 (redimensionnement)
     * @return boolean
     */
    public static function createThumb($src_path, $dst_path, $param_width, $param_height, $keep_original = false, $fit = 'inside')
    {
        //        require_once self::MODULE_PATH . 'class/wideimage/WideImage.php';
        $resize = true;
        // On commence par vérifier que l'image originale n'est pas plus petite que les dimensions demandées auquel cas il n'y a rien à faire
        $pictureDimensions = getimagesize($src_path);
        if (is_array($pictureDimensions)) {
            $pictureWidth  = $pictureDimensions[0];
            $pictureHeight = $pictureDimensions[1];
            if ($pictureWidth < $param_width && $pictureHeight < $param_height) {
                $resize = false;
            }
        }
        $img = WideImage::load($src_path);
        if ($resize) { // L'image est suffisament grande pour être réduite
            $thumbMethod = 0;
            if ($thumbMethod == 0) { // Redimensionnement de l'image
                $result = $img->resize($param_width, $param_height, $fit);
            } else { // Récupération d'une partie de l'image depuis le centre
                // Calcul de left et top
                $left = $top = 0;
                if (is_array($pictureDimensions)) {
                    if ($pictureWidth > $param_width) {
                        $left = (int)(($pictureWidth / 2) - ($param_width / 2));
                    }
                    if ($pictureHeight > $param_height) {
                        $top = (int)(($pictureHeight / 2) - ($param_height / 2));
                    }
                }
                $result = $img->crop($left, $top, $param_width, $param_height);
            }
            $result->saveToFile($dst_path, null);
        } else {
            if ($src_path != $dst_path) {
                @copy($src_path, $dst_path);
            }
        }
        if (!$keep_original) {
            @unlink($src_path);
        }

        return true;
    }

    /**
     * Create the <option> of an html select
     *
     * @param  array $array   Array of index and labels
     * @param  mixed $default the default value
     * @param bool   $withNull
     * @return string
     */
    private static function htmlSelectOptions($array, $default = 0, $withNull = true)
    {
        $ret      = array();
        $selected = '';
        if ($withNull) {
            if (!is_array($default)) {
                if ($default === 0) {
                    $selected = " selected = 'selected'";
                }
            } else {
                if (in_array(0, $default)) {
                    $selected = " selected = 'selected'";
                }
            }
            $ret[] = '<option value=0' . $selected . '>---</option>';
        }

        foreach ($array as $index => $label) {
            $selected = '';
            if (!is_array($default)) {
                if ($index == $default) {
                    $selected = " selected = 'selected'";
                }
            } else {
                if (in_array($index, $default)) {
                    $selected = " selected = 'selected'";
                }
            }
            $ret[] = "<option value=\"" . $index . "\"" . $selected . '>' . $label . '</option>';
        }

        return implode("\n", $ret);
    }

    /**
     * Creates an html select an returns its code
     *
     * @param  string  $selectName Select's name
     * @param  array   $array      Key = value of each option, value = label of each option
     * @param  mixed   $default    Default's value
     * @param  boolean $withNull   Do we want a null option
     * @param  string  $style      Is there a style to apply ?
     * @param  boolean $multiple   Can we select multiple elements ?
     * @param  integer $size       Size of the selectbox, only used with multiple selectboxes
     * @param string   $extra
     * @return string
     */
    public static function htmlSelect($selectName, $array, $default, $withNull = true, $style = '', $multiple = false, $size = 1, $extra = '')
    {
        $ret = '';
        $ret .= "<select name='" . $selectName . "' id='" . $selectName . "'";
        if (xoops_trim($style) != '') {
            $ret .= " style='" . $style . "' ";
        }
        if (xoops_trim($multiple) != '') {
            $ret .= " multiple = 'multiple' size='" . $size . "' ";
        }
        $ret .= $extra . ">\n";
        $ret .= self::htmlSelectOptions($array, $default, $withNull);
        $ret .= "</select>\n";

        return $ret;
    }

    /**
     * Verify that a mysql table exists
     * @param $tablename
     * @return bool
     */
    public static function tableExists($tablename)
    {
        global $xoopsDB;
        $result = $xoopsDB->queryF("SHOW TABLES LIKE '$tablename'");

        return ($xoopsDB->getRowsNum($result) > 0);
    }

    /**
     * Verify that a field exists inside a mysql table
     * @param $fieldname
     * @param $table
     * @return bool
     */
    public static function fieldExists($fieldname, $table)
    {
        global $xoopsDB;
        $result = $xoopsDB->queryF("SHOW COLUMNS FROM $table LIKE '$fieldname'");

        return ($xoopsDB->getRowsNum($result) > 0);
    }

    /**
     * Retourne la définition d'un champ
     *
     * @param  string $fieldname
     * @param  string $table
     * @return array
     */
    public static function getFieldDefinition($fieldname, $table)
    {
        global $xoopsDB;
        $result = $xoopsDB->queryF("SHOW COLUMNS FROM $table LIKE '$fieldname'");
        if ($result) {
            return $xoopsDB->fetchArray($result);
        }

        return '';
    }

    /**
     * Add a field to a mysql table
     * @param $field
     * @param $table
     * @return resource
     */
    public static function addField($field, $table)
    {
        global $xoopsDB;
        $result = $xoopsDB->queryF("ALTER TABLE $table ADD $field;");

        return $result;
    }

    /**
     * Maintenance des tables du module et de son cache de requêtes
     *
     * @return void
     */
    public static function maintainTablesCache()
    {
        global $xoopsDB;
        define('REFERENCES_MAINTAIN', true);
        require self::MODULE_PATH . 'xoops_version.php';
        $tables = array();
        foreach ($modversion['tables'] as $table) {
            $tables[] = $xoopsDB->prefix($table);
        }
        if (count($tables) > 0) {
            $list = implode(',', $tables);
            $xoopsDB->queryF('CHECK TABLE ' . $list);
            $xoopsDB->queryF('ANALYZE TABLE ' . $list);
            $xoopsDB->queryF('OPTIMIZE TABLE ' . $list);
        }
        self::updateCache();
        $handlers = references_handler::getInstance();
        $handlers->h_references_articles->forceCacheClean();
    }

    /**
     * Indique si le module Tag existe et est activé
     *
     * @return boolean
     * @credit: smartfactory
     */
    public static function tagModuleExists()
    {
        static $moduleExistsAndIsActive;
        if (!isset($moduleExistsAndIsActive)) {
            $modules_handler = xoops_getHandler('module');
            $tagModule       = $modules_handler->getByDirName('tag');
            if (!$tagModule) {
                $moduleExistsAndIsActive = false;
            } else {
                $moduleExistsAndIsActive = $tagModule->getVar('isactive') == 1;
            }
        }

        return $moduleExistsAndIsActive;
    }
}
