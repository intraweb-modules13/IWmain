<?php

class IWmain_Installer extends Zikula_AbstractInstaller {

    /**
     * Initialise the IWmain module creating module tables and module vars
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @return bool true if successful, false otherwise
     */
    public function install() {
        // Create module table
        if (!DBUtil::createTable('IWmain'))
            return false;
        if (!DBUtil::createTable('IWmain_logs'))
            return false;

        //Create indexes
        $table = DBUtil::getTables();
        $c = $table['IWmain_column'];
        if (!DBUtil::createIndex($c['module'], 'IWmain', 'module'))
            return false;
        if (!DBUtil::createIndex($c['name'], 'IWmain', 'name'))
            return false;
        if (!DBUtil::createIndex($c['uid'], 'IWmain', 'uid'))
            return false;
        $c = $table['IWmain_logs_column'];
        if (!DBUtil::createIndex($c['moduleName'], 'IWmain_logs', 'moduleName'))
            return false;
        if (!DBUtil::createIndex($c['visible'], 'IWmain_logs', 'visible'))
            return false;

        //Create module vars
        $this->setVar('url', 'https://github.com/intraweb-modules13/IWmain')
                ->setVar('email', 'intraweb@xtec.cat')
                ->setVar('documentRoot', 'data')
                ->setVar('extensions', 'odt|ods|odp|zip|pdf|doc|jpg|gif|txt')
                ->setVar('maxsize', '1000000')
                ->setVar('usersvarslife', '60')
                ->setVar('cronHeaderText', '')
                ->setVar('cronFooterText', '')
                ->setVar('cronSubjectText', $this->__('User Reports'))
                ->setVar('captchaPrivateCode', '')
                ->setVar('captchaPublicCode', '')
                ->setVar('URLBase', System::getBaseUrl())
		->setVar('cronPasswordActive', false)
		->setVar('cronPasswrodString','')
                ->setVar('crAc_UserReports', false)
                ->setVar('crAc_UR_IWforums', false)
                ->setVar('crAc_UR_IWmessages', false)
                ->setVar('crAc_UR_IWforms', false)
                ->setVar('crAc_UR_IWnoteboard', false)
                ->setVar('crAc_UR_IWforums_hd', '')
                ->setVar('crAc_UR_IWmessages_hd', '')
                ->setVar('crAc_UR_IWforms_hd', '')
                ->setVar('crAc_UR_IWnoteboard_hd', '')
                ->setVar('everybodySubscribed', true)
                ->setVar('cronURfreq', '23.5');

        return true;
    }

    /**
     * Delete the IWmain module
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @return bool true if successful, false otherwise
     */
    public function uninstall() {
        // Delete module table
        DBUtil::dropTable('IWmain');
        //Delete module vars
        $this->delVars;
        //Deletion successfull
        return true;
    }

    /**
     * Update the IWmain module
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @author Jaume Fernàndez Valiente (jfern343@xtec.cat)
     * @return bool true if successful, false otherwise
     */
    public function upgrade($oldversion) {
	switch ($oldversion) {
	    case ($oldversion < '3.0.0'):
        	// create new needed tables and index
        	if (!DBUtil::createTable('IWmain_logs'))
            	    return false;

        	$table = DBUtil::getTables();
        	$c = $table['IWmain_logs_column'];
        	if (!DBUtil::createIndex($c['moduleName'], 'IWmain_logs', 'moduleName'))
            	    return false;
        	if (!DBUtil::createIndex($c['visible'], 'IWmain_logs', 'visible'))
            	    return false;

            	//Array de noms
        	$oldVarsNames = DBUtil::selectFieldArray("module_vars", 'name', "`modname` = 'IWmain'", '', false, '');

        	$newVarsNames = Array('url', 'email', 'documentRoot', 'extensions', 'maxsize', 'usersvarslife',
            	    'cronHeaderText', 'cronFooterText', 'showHideFiles', 'URLBase');

        	$newVars = Array('url' => 'https://github.com/intraweb-modules13/IWmain',
            	    'email' => 'intraweb@xtec.cat',
            	    'documentRoot' => 'data',
            	    'extensions' => 'odt|ods|odp|zip|pdf|doc|jpg|gif|txt',
            	    'maxsize', '1000000',
            	    'usersvarslife' => '60',
            	    'cronHeaderText' => $this->__('Header text of the cron automatic emails with the new things to see'),
            	    'cronFooterText' => $this->__('Footer text of the email'),
            	    'showHideFiles' => '0',
            	    'captchaPrivateCode' => '',
            	    'captchaPublicCode' => '',
            	    'URLBase' => System::getBaseUrl());

        	// Delete unneeded vars
        	$del = array_diff($oldVarsNames, $newVarsNames);
        	foreach ($del as $i) {
            	    $this->delVar($i);
        	}

        	// Add new vars
        	$add = array_diff($newVarsNames, $oldVarsNames);
        	foreach ($add as $i) {
            	    $this->setVar($i, $newVars[$i]);
        	}
	    case '3.0.0':
		// Clean upgrade. Only fix iwcron problems and table definitions to run with IWusers 3.1.0
	    case '3.0.1':
			// Add new vars
			$this->setVar('cronPasswordActive', false)
                            ->setVar('cronPasswrodString','')
                            ->delVar('showHideFiles')
                            ->setVar('cronSubjectText', $this->__('User Reports'))
                            ->setVar('crAc_UserReports', false)
                            ->setVar('crAc_UR_IWforums', false)
                            ->setVar('crAc_UR_IWmessages', false)
                            ->setVar('crAc_UR_IWforms', false)
                            ->setVar('crAc_UR_IWnoteboard', false)
                            ->setVar('crAc_UR_IWforums_hd', '')
                            ->setVar('crAc_UR_IWmessages_hd', '')
                            ->setVar('crAc_UR_IWforms_hd', '')
                            ->setVar('crAc_UR_IWnoteboard_hd', '')
                            ->setVar('everybodySubscribed', true)
                            ->setVar('cronURfreq', '0');
	}
        return true;
    }

}
