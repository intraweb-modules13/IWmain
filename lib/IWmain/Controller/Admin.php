<?php

class IWmain_Controller_Admin extends Zikula_AbstractController {

    protected function postInitialize() {
        // Set caching to false by default.
        $this->view->setCaching(false);
    }

    /**
     * Give access to the main Intraweb configuration
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	The form for general configuration values of the Intraweb modules
     */
    public function main() {
        $this->redirect(ModUtil::url($this->name, 'admin', 'conf'));
    }
    public function main2() {
        // Security check
        if (!SecurityUtil::checkPermission('IWmain::', '::', ACCESS_ADMIN)) {
            throw new Zikula_Exception_Forbidden();
        }

        //Check if the cron file exists
        if (!file_exists('iwcron.php')) {
            return $this->view->assign('noCron', true)
                            ->fetch('IWmain_admin_main.tpl');
        }
        //Check if module Mailer is active
        $modid = ModUtil::getIdFromName('Mailer');
        $modinfo = ModUtil::getInfo($modid);
        //if it is not active
        if ($modinfo['state'] != 3) {
            $this->view->assign('noMailer', true)
                    ->assign('noCron', false)
                    ->fetch('IWmain_admin_main.tpl');
        }
        //-100 really is not a user but represents the system user
        $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
        $cronResponse = ModUtil::func('IWmain', 'user', 'userGetVar', array('uid' => -100,
                    'name' => 'cronResponse',
                    'module' => 'IWmain_cron',
                    'sv' => $sv));
        $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
        $lastCron = ModUtil::func('IWmain', 'user', 'userGetVar', array('uid' => -100,
                    'name' => 'lastCron',
                    'module' => 'IWmain_cron',
                    'sv' => $sv));
        $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
        $lastCronSuccessfull = ModUtil::func('IWmain', 'user', 'userGetVar', array('uid' => -100,
                    'name' => 'lastCronSuccessfull',
                    'module' => 'IWmain_cron',
                    'sv' => $sv));
        $elapsedTime = 24 * 60 * 60;
        $executeCron = ($lastCron < time() - $elapsedTime) ? 1 : 0;
        //$noCronTime = ($lastCronSuccessfull > time() - $elapsedTime) ? true : false;
        return $this->view->assign('executeCron', $executeCron)
                        //->assign('noCronTime', $noCronTime)
                        ->assign('cronResponse', $cronResponse)
                        ->assign('noCron', false)
                        ->assign('noMailer', false)
			->assign('cronPasswordActive', $this->getVar('cronPasswordActive'))
			->assign('cronPasswordString', $this->getVar('cronPasswordString'))
                        ->assign('cronHeaderText', $this->getVar('cronHeaderText'))
                        ->assign('cronFooterText', $this->getVar('cronFooterText'))
                        ->fetch('IWmain_admin_main.tpl');
    }

    /**
     * Give access to the Intraweb configuration
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	The form for general configuration values of the Intraweb modules
     */
    public function conf() {
        // Security check
        if (!SecurityUtil::checkPermission('IWmain::', '::', ACCESS_ADMIN)) {
            throw new Zikula_Exception_Forbidden();
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

        $multizk = (isset($GLOBALS['ZConfig']['Multisites']['multi']) && $GLOBALS['ZConfig']['Multisites']['multi'] == 1) ? 1 : 0;

        // Create output object
        return $this->view->assign('noWriteabledocumentRoot', $noWriteabledocumentRoot)
                        ->assign('noFolder', $noFolder)
                        ->assign('multizk', $multizk)
                        ->assign('extensions', $this->getVar('extensions'))
                        ->assign('maxsize', $this->getVar('maxsize'))
                        ->assign('usersvarslife', $this->getVar('usersvarslife'))
                        ->assign('documentRoot', $this->getVar('documentRoot'))
                        ->assign('captchaPrivateCode', $this->getVar('captchaPrivateCode'))
                        ->assign('captchaPublicCode', $this->getVar('captchaPublicCode'))
			->assign('cronPasswordActive', $this->getVar('cronPasswordActive'))
			->assign('cronPasswrodString', $this->getVar('cronPasswrodString'))
                        ->fetch('IWmain_admin_conf.tpl');
    }
    public function updateCronConfig(){
        // Security check
        if (!SecurityUtil::checkPermission('IWmain::', '::', ACCESS_ADMIN)) {
            throw new Zikula_Exception_Forbidden();
        }
        $cronPasswordActive = FormUtil::getPassedValue('cronPasswordActive', false, 'POST')? true : false;
        $cronPasswordString = FormUtil::getPassedValue('cronPasswordString', isset($args['cronPasswordString']) ? $args['cronPasswordString'] : '', 'POST');
        $cronHeaderText = FormUtil::getPassedValue('cronHeaderText', isset($args['cronHeaderText']) ? $args['cronHeaderText'] : null, 'POST');
        $cronFooterText = FormUtil::getPassedValue('cronFooterText', isset($args['cronFooterText']) ? $args['cronFooterText'] : null, 'POST');
        $this->checkCsrfToken();
        $this->setVar('cronPasswordActive', $cronPasswordActive)
             ->setVar('cronPasswordString', $cronPasswordString)
             ->setVar('cronHeaderText', $cronHeaderText)
             ->setVar('cronFooterText', $cronFooterText);
        LogUtil::registerStatus($this->__('The configuration have been updated'));
        return System::redirect(ModUtil::url('IWmain', 'admin', 'main2'));


    }
    /**
     * Show the module information
     * @author	Albert Pérez Monfort (aperezm@xtec.cat)
     * @return	The module information
     */
    public function executeCron() {
        // Security check
        if (!SecurityUtil::checkPermission('IWmain::', "::", ACCESS_ADMIN)) {
            throw new Zikula_Exception_Forbidden();
        }
        $cronURL = 'iwcron.php?full=1&return=1';
        if ($this->getVar('cronPasswordActive')) {
            $cronURL .= '&password='.$this->getVar('cronPasswordString');
        }
        LogUtil::registerStatus($this->__('The cron has been executed'));
        return System::redirect($cronURL);
    }

    /**
     * Update the module configuration
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	True if success or false in other case
     */
    public function updateconfig() {
        // Get parameters from whatever input we need.
        $documentRoot = FormUtil::getPassedValue('documentRoot', isset($args['documentRoot']) ? $args['documentRoot'] : null, 'POST');
        $extensions = FormUtil::getPassedValue('extensions', isset($args['extensions']) ? $args['extensions'] : null, 'POST');
        $maxsize = FormUtil::getPassedValue('maxsize', isset($args['maxsize']) ? $args['maxsize'] : null, 'POST');
        $usersvarslife = FormUtil::getPassedValue('usersvarslife', isset($args['usersvarslife']) ? $args['usersvarslife'] : null, 'POST');
        $captchaPrivateCode = FormUtil::getPassedValue('captchaPrivateCode', isset($args['captchaPrivateCode']) ? $args['captchaPrivateCode'] : null, 'POST');
        $captchaPublicCode = FormUtil::getPassedValue('captchaPublicCode', isset($args['captchaPublicCode']) ? $args['captchaPublicCode'] : null, 'POST');

        // Security check
        if (!SecurityUtil::checkPermission('IWmain::', '::', ACCESS_ADMIN)) {
            throw new Zikula_Exception_Forbidden();
        }

        $this->checkCsrfToken();

        //Check if the uservarlife value is correct
        if (!isset($usersvarslife) || !is_numeric($usersvarslife) || $usersvarslife <= 0) {
            LogUtil::registerError($this->__('The value of the life time for users variables is incorrect') . ': ' . $usersvarslife);
            $usersvarslife = 0;
        }

        $this->setVar('extensions', $extensions)
                ->setVar('documentRoot', $documentRoot)
                ->setVar('maxsize', $maxsize)
                ->setVar('usersvarslife', $usersvarslife)
                ->setVar('usersPictureFolder', $usersPictureFolder)
                ->setVar('captchaPrivateCode', $captchaPrivateCode)
                ->setVar('captchaPublicCode', $captchaPublicCode)
                ->setVar('URLBase', System::getBaseUrl());

        LogUtil::registerStatus($this->__('The configuration have been updated'));
        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        return System::redirect(ModUtil::url('IWmain', 'admin', 'conf'));
    }

    /**
     * Check if it is installed the correct version of IWmain when somebody try to install a new module that needs IWmain
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	args   Array with the version of the module IWmain needed
     * @return:	True if the version is correct and false in other case
     */
    public function checkVersion($args) {
        // Checks if module IWmain is installed. If not returns error
        $modid = ModUtil::getIdFromName('IWmain');
        $modinfo = ModUtil::getInfo($modid);
        if ($modinfo['version'] < $args['version']) {
            throw new Zikula_Exception_Forbidden($this->__('The current version of IWmain module is incorrect. You must upgrade it before install, upgrade or use this module.'));
        }
        // The current version is correct
        return true;
    }

}
