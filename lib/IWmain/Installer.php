<?php

/**
 * PostNuke Application Framework
 *
 * @copyright (c) 2002, PostNuke Development Team
 * @link http://www.postnuke.com
 * @version $Id: pninit.php 22139 2007-06-01 10:57:16Z markwest $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package PostNuke_Value_Addons
 * @subpackage Webbox
 */
class IWmain_Installer extends Zikula_Installer
{
    /**
     * Initialise the IWmain module creating module tables and module vars
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @return bool true if successful, false otherwise
     */
    public function install() {
        // Create module table
        if (!DBUtil::createTable('iw_main')) return false;

        //Create indexes
        $pntable = DBUtil::getTables();
        $c = $pntable['iw_main_column'];
        if (!DBUtil::createIndex($c['module'], 'iw_main', 'module')) return false;
        if (!DBUtil::createIndex($c['name'], 'iw_main', 'name')) return false;
        if (!DBUtil::createIndex($c['uid'], 'iw_main', 'uid')) return false;

        //Create module vars
        $this->setVar('url', 'http://phobos.xtec.net/intraweb')
             ->setVar('email', 'intraweb@xtec.cat')
             ->setVar('documentRoot', 'documents')
             ->setVar('extensions', 'odt|ods|odp|zip|pdf|doc|jpg|gif|txt')
             ->setVar('maxsize', '1000000')
             ->setVar('usersvarslife', '60')
             ->setVar('usersPictureFolder', 'photos')
             ->setVar('tempFolder', 'temp')
             ->setVar('cronHeaderText', $this->__('Header text of the cron automatic emails with the new things to see'))
             ->setVar('cronFooterText', $this->__('Footer text of the email'))
             ->setVar('publicFolder', 'public')
             ->setVar('showHideFiles', '0')
             ->setVar('allowUserChangeAvatar', '1')
             ->setVar('avatarChangeValidationNeeded', '1')
             ->setVar('URLBase', System::getBaseUrl());

        return true;
    }

    /**
     * Delete the IWmain module
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @return bool true if successful, false otherwise
     */
    public function uninstall() {
        // Delete module table
        DBUtil::dropTable('iw_main');

        //Delete module vars
        $this->delVar('url')
             ->delVar('email')
             ->delVar('documentRoot')
             ->delVar('extensions')
             ->delVar('maxsize')
             ->delVar('usersvarslife')
             ->delVar('usersPictureFolder')
             ->delVar('tempFolder')
             ->delVar('cronHeaderText')
             ->delVar('cronFooterText')
             ->delVar('publicFolder')
             ->delVar('showHideFiles')
             ->delVar('allowUserChangeAvatar')
             ->delVar('avatarChangeValidationNeeded')
             ->delVar('URLBase');

        //Deletion successfull
        return true;
    }

    /**
     * Update the IWmain module
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @return bool true if successful, false otherwise
     */
    public function upgrade($oldversion) {
        return true;
    }
}