<?php
class IWmain_Controller_Admin extends Zikula_Controller
{
    /**
     * Give access to the main Intraweb configuration
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	The form for general configuration values of the Intraweb modules
     */
    public function main()
    {
        // Security check
        if (!SecurityUtil::checkPermission('IWmain::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }
        // Create output object
        $view = Zikula_View::getInstance('IWmain', false);
        //Check if the cron file exists
        if (!file_exists('iwcron.php')) {
            $view->assign('noCron', true);
            return $view->fetch('IWmain_admin_main.htm');
        }
        //Check if module Mailer is active
        $modid = ModUtil::getIdFromName('Mailer');
        $modinfo = ModUtil::getInfo($modid);
        //if it is not active
        if ($modinfo['state'] != 3) {
            $view->assign('noMailer', true);
            return $view->fetch('IWmain_admin_main.htm');
        }
        //-100 really is not a user but represents the system user
        $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
        $cronResponse = ModUtil::func('IWmain', 'user', 'userGetVar',
                                   array('uid' => -100,
                                         'name' => 'cronResponse',
                                         'module' => 'IWmain_cron',
                                         'sv' => $sv));
        $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
        $lastCron = ModUtil::func('IWmain', 'user', 'userGetVar',
                               array('uid' => -100,
                                     'name' => 'lastCron',
                                     'module' => 'IWmain_cron',
                                     'sv' => $sv));
        $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
        $lastCronSuccessfull = ModUtil::func('IWmain', 'user', 'userGetVar',
                                          array('uid' => -100,
                                                'name' => 'lastCronSuccessfull',
                                                'module' => 'IWmain_cron',
                                                'sv' => $sv));
        $elapsedTime = 24 * 60 * 60;
        if ($lastCron < time() - $elapsedTime) {
            $view->assign('executeCron', '1');
        }
        if ($lastCronSuccessfull > time() - $elapsedTime) {
            $view->assign('noCronTime', true);
        }
        $view->assign('cronResponse', $cronResponse);
        return $view->fetch('IWmain_admin_main.htm');
    }

    /**
     * Give access to the Intraweb configuration
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	The form for general configuration values of the Intraweb modules
     */
    public function conf()
    {
        // Security check
        if (!SecurityUtil::checkPermission('IWmain::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }
        $noWriteabledocumentRoot = false;
        $noFolder = false;
        //Check if the directory of document root files exists
        if (!file_exists(ModUtil::getVar('IWmain', 'documentRoot')) || ModUtil::getVar('IWmain', 'documentRoot') == '') {
            $noFolder = true;
        } else {
            if (!is_writeable(ModUtil::getVar('IWmain', 'documentRoot'))) {
                $noWriteabledocumentRoot = true;
            }
        }

        $noWriteablePictureFolder = false;
        $noPictureFolder = false;
        //Check if the users picture folder exists
        if (!file_exists(ModUtil::getVar('IWmain', 'documentRoot') . '/' . ModUtil::getVar('IWmain', 'usersPictureFolder')) || ModUtil::getVar('IWmain', 'usersPictureFolder') == '') {
            $noPictureFolder = true;
        } else {
            if (!is_writeable(ModUtil::getVar('IWmain', 'documentRoot') . '/' . ModUtil::getVar('IWmain', 'usersPictureFolder'))) {
                $noWriteablePictureFolder = true;
            }
        }

        $multizk = (isset($GLOBALS['PNConfig']['Multisites']['multi']) && $GLOBALS['PNConfig']['Multisites']['multi'] == 1) ? 1 : 0;
        if (extension_loaded('gd')) $gdAvailable = true;

        // Create output object
        $view = Zikula_View::getInstance('IWmain', false);
        $view->assign('gdAvailable', $gdAvailable);
        $view->assign('noWriteabledocumentRoot', $noWriteabledocumentRoot);
        $view->assign('noFolder', $noFolder);
        $view->assign('noPictureFolder', $noPictureFolder);
        $view->assign('noWriteablePictureFolder', $noWriteablePictureFolder);
        $view->assign('multizk', $multizk);
        $view->assign('extensions', ModUtil::getVar('IWmain', 'extensions'));
        $view->assign('extensions', ModUtil::getVar('IWmain', 'extensions'));
        $view->assign('maxsize', ModUtil::getVar('IWmain', 'maxsize'));
        $view->assign('usersvarslife', ModUtil::getVar('IWmain', 'usersvarslife'));
        $view->assign('documentRoot', ModUtil::getVar('IWmain', 'documentRoot'));
        $view->assign('usersPictureFolder', ModUtil::getVar('IWmain', 'usersPictureFolder'));
        $view->assign('cronHeaderText', ModUtil::getVar('IWmain', 'cronHeaderText'));
        $view->assign('cronFooterText', ModUtil::getVar('IWmain', 'cronFooterText'));
        $view->assign('allowUserChangeAvatar', ModUtil::getVar('IWmain', 'allowUserChangeAvatar'));
        $view->assign('avatarChangeValidationNeeded', ModUtil::getVar('IWmain', 'avatarChangeValidationNeeded'));
        return $view->fetch('IWmain_admin_conf.htm');
    }

    /**
     * Show the module information
     * @author	Albert Pérez Monfort (aperezm@xtec.cat)
     * @return	The module information
     */
    public function executeCron()
    {
        // Security check
        if (!SecurityUtil::checkPermission('IWmain::', "::", ACCESS_ADMIN)) {
            return LogUtil::registerError($this->__('Sorry! No authorization to access this module.'), 403);
        }
        LogUtil::registerStatus($this->__('The cron has been executed'));
        return System::redirect('iwcron.php?full=1&return=1');
    }

    /**
     * Update the module configuration
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	True if success or false in other case
     */
    public function updateconfig()
    {
        // Get parameters from whatever input we need.
        $documentRoot = FormUtil::getPassedValue('documentRoot', isset($args['documentRoot']) ? $args['documentRoot'] : null, 'POST');
        $extensions = FormUtil::getPassedValue('extensions', isset($args['extensions']) ? $args['extensions'] : null, 'POST');
        $maxsize = FormUtil::getPassedValue('maxsize', isset($args['maxsize']) ? $args['maxsize'] : null, 'POST');
        $usersvarslife = FormUtil::getPassedValue('usersvarslife', isset($args['usersvarslife']) ? $args['usersvarslife'] : null, 'POST');
        $usersPictureFolder = FormUtil::getPassedValue('usersPictureFolder', isset($args['usersPictureFolder']) ? $args['usersPictureFolder'] : null, 'POST');
        $cronHeaderText = FormUtil::getPassedValue('cronHeaderText', isset($args['cronHeaderText']) ? $args['cronHeaderText'] : null, 'POST');
        $cronFooterText = FormUtil::getPassedValue('cronFooterText', isset($args['cronFooterText']) ? $args['cronFooterText'] : null, 'POST');
        $allowUserChangeAvatar = FormUtil::getPassedValue('allowUserChangeAvatar', isset($args['allowUserChangeAvatar']) ? $args['allowUserChangeAvatar'] : 0, 'POST');
        $avatarChangeValidationNeeded = FormUtil::getPassedValue('avatarChangeValidationNeeded', isset($args['avatarChangeValidationNeeded']) ? $args['avatarChangeValidationNeeded'] : 0, 'POST');
        // Security check
        if (!SecurityUtil::checkPermission('IWmain::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }
        // Confirm authorisation code
        if (!SecurityUtil::confirmAuthKey()) {
            return LogUtil::registerAuthidError(ModUtil::url('IWmain', 'admin', 'conf'));
        }
        //Check if the uservarlife value is correct
        if (!isset($usersvarslife) || !is_numeric($usersvarslife) || $usersvarslife <= 0) {
            LogUtil::registerError($this->__('The value of the life time for users variables is incorrect') . ': ' . $usersvarslife);
            $usersvarslife = 0;
        }
        // Update module variables
        if (!isset($GLOBALS['PNConfig']['Multisites']['multi']) || $GLOBALS['PNConfig']['Multisites']['multi'] == 0) {
            $multizk = $Intraweb['multizk'];
            ModUtil::setVar('IWmain', 'documentRoot', $documentRoot);
        }
        ModUtil::setVar('IWmain', 'extensions', $extensions);
        ModUtil::setVar('IWmain', 'maxsize', $maxsize);
        ModUtil::setVar('IWmain', 'usersvarslife', $usersvarslife);
        ModUtil::setVar('IWmain', 'usersPictureFolder', $usersPictureFolder);
        ModUtil::setVar('IWmain', 'cronHeaderText', $cronHeaderText);
        ModUtil::setVar('IWmain', 'cronFooterText', $cronFooterText);
        ModUtil::setVar('IWmain', 'allowUserChangeAvatar', $allowUserChangeAvatar);
        ModUtil::setVar('IWmain', 'avatarChangeValidationNeeded', $avatarChangeValidationNeeded);
        ModUtil::setVar('IWmain', 'URLBase', System::getBaseUrl());
        LogUtil::registerStatus($this->__('The configuration have been updated'));
        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        return System::redirect(ModUtil::url('IWmain', 'admin', 'conf'));
    }

    /**
     * Get the files in users pictures folder for avatar replacement
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:		An array with the files from a change avatar request
     */
    public function getChangeAvatarRequest()
    {
        // Security check
        if (!SecurityUtil::checkPermission('IWmain::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }
        $folder = ModUtil::getVar('IWmain', 'documentRoot') . '/' . ModUtil::getVar('IWmain', 'usersPictureFolder');
        //Get information files
        $fileList = ModUtil::func('IWmain', 'admin', 'dir_list',
                                   array('folder' => $folder));
        $filesArray = array();
        if ($fileList) {
            foreach ($fileList['file'] as $file) {
                if (substr($file['name'], 0, 1) == '_' && substr($file['name'], -6, -4) != '_s') {
                    $filesArray[] = array($file['name']);
                }
            }
        }
        return $filesArray;
    }

    /**
     * List the users who have asked for a avatar replacement
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:		The list of users
     */
    public function changeAvatarView()
    {
        // Security check
        if (!SecurityUtil::checkPermission('IWmain::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }
        $files = ModUtil::func('IWmain', 'admin', 'getChangeAvatarRequest');
        // Create output object
        $view = Zikula_View::getInstance('IWmain', false);
        $usersList = '$$';
        $filesArray = array();
        foreach ($files as $file) {
            $userName = substr($file[0], 1, -4);
            $userId = UserUtil::getIdFromName($userName);
            $usersList .= $userId . '$$';
            $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
            $photo = ModUtil::func('IWmain', 'user', 'getUserPicture',
                                    array('uname' => $userName,
                                          'sv' => $sv));
            $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
            $photo_new = ModUtil::func('IWmain', 'user', 'getUserPicture',
                                        array('uname' => '_' . $userName,
                                              'sv' => $sv));
            if ($userId != '') {
                $filesArray[] = array('uid' => $userId,
                                      'photo' => $photo,
                                      'photo_new' => $photo_new,
                                      'fileName' => $file[0]);
            }
        }
        $users = '';
        if (count($files) > 0) {
            //get all users information
            $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
            $users = ModUtil::func('IWmain', 'user', 'getAllUsersInfo',
                                    array('sv' => $sv,
                                          'info' => 'ncc',
                                          'list' => $usersList));
        }
        $view->assign('users', $users);
        $view->assign('filesArray', $filesArray);
        return $view->fetch('IWmain_admin_changeAvatarView.htm');
    }

    /**
     * Check if it is installed the correct version of IWmain when somebody try to install a new module that needs IWmain
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	args   Array with the version of the module IWmain needed
     * @return:	True if the version is correct and false in other case
     */
    public function checkVersion($args)
    {
        // Checks if module IWmain is installed. If not returns error
        $modid = ModUtil::getIdFromName('IWmain');
        $modinfo = ModUtil::getInfo($modid);
        if ($modinfo['version'] < $args['version']) {
            return LogUtil::registerError($this->__('The current version of IWmain module is incorrect. You must upgrade it before install, upgrade or use this module.'));
        }
        // The current version is correct
        return true;
    }

    /**
     * redirect administrator to IWfiles modules. The management files has been removed from the IWmain module
     * @author:	Robert Barrera (rbarrer5@xtec.cat)
     * @param:	args   Array with the folder name where list the files and subfolders
     * @return:	The list of files and folders
     */
    public function filesList($args)
    {
        return System::redirect(ModUtil::url('Files', 'user', 'main'));
    }

    /**
     * List the information files in folder
     * @author:	Robert Barrera (rbarrer5@xtec.cat)
     * @param:	dir Folder Path
     * @return:	Objects array of the files
     */
    public function dir_list($args)
    {
        $folder = FormUtil::getPassedValue('folder', isset($args['folder']) ? $args['folder'] : null, 'POST');

        // Security check
        if (!SecurityUtil::checkPermission('IWfiles::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }
        $initFolderPath = ModUtil::getVar('IWmain', 'documentRoot');

        //Check is the last character is a /
        if (substr($folder, strlen($folder) - 1 ,1) != '/') $folder .= '/';
        //Check is a directory
        if (!is_dir($folder)) return array();
        $dir_handle = opendir($folder);
        $dir_objects = array();
        while ($object = readdir($dir_handle)) {
            if (!in_array($object, array('.', '..'))) {
                $filename = $folder . $object;
                // Get file extension
                $fileExtension = strtolower(substr(strrchr($filename, "."), 1));
                // get file icon
                $ctypeArray = ModUtil::func('IWfiles', 'user', 'getMimetype',
                                             array('extension' => $fileExtension));
                $fileIcon = $ctypeArray['icon'];
                if (substr($filename, strrpos($filename, '/') + 1, 1) != '.' || ModUtil::getVar('IWfiles', 'showHideFiles') == 1) {
                    $file_object = array('name' => $object,
                                         'size' => filesize($filename),
                                         'type' => filetype($filename),
                                         'time' => date("j F Y, H:i", filemtime($filename)),
                                         'fileIcon' => $fileIcon
                    );
                    if (is_dir($filename)) {
                        $dir_objects['dir'][] = $file_object;
                    } else {
                        $dir_objects['file'][] = $file_object;
                    }
                }
            }
        }
        closedir($dir_handle);
        return $dir_objects;
    }
}