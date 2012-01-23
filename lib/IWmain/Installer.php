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
        $this->setVar('url', 'http://phobos.xtec.net/intraweb')
                ->setVar('email', 'intraweb@xtec.cat')
                ->setVar('documentRoot', 'data')
                ->setVar('extensions', 'odt|ods|odp|zip|pdf|doc|jpg|gif|txt')
                ->setVar('maxsize', '1000000')
                ->setVar('usersvarslife', '60')
                ->setVar('cronHeaderText', $this->__('Header text of the cron automatic emails with the new things to see'))
                ->setVar('cronFooterText', $this->__('Footer text of the email'))
                ->setVar('showHideFiles', '0')
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
        DBUtil::dropTable('IWmain');

        //Delete module vars
        $this->delVar('url')
                ->delVar('email')
                ->delVar('documentRoot')
                ->delVar('extensions')
                ->delVar('maxsize')
                ->delVar('usersvarslife')
                ->delVar('cronHeaderText')
                ->delVar('cronFooterText')
                ->delVar('showHideFiles')
                ->delVar('URLBase');

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

        // create new needed tables and index
        if (!DBUtil::createTable('IWmain_logs'))
            return false;

        $table = DBUtil::getTables();
        $c = $table['IWmain_logs_column'];
        if (!DBUtil::createIndex($c['moduleName'], 'IWmain', 'moduleName'))
            return false;
        if (!DBUtil::createIndex($c['visible'], 'IWmain', 'visible'))
            return false;

        $prefix = $GLOBALS['ZConfig']['System']['prefix'];

        //Rename table
        if (!DBUtil::renameTable('IWmain', 'IWmain'))
            return false;

        //Rename iw_module column values
        $c = "SELECT DISTINCT `iw_module` FROM `{$prefix}_IWmain` WHERE 1";
        $res = DBUtil::executeSQL($c);

        $oldNames = DBUtil::marshallFieldArray($res);

        foreach ($oldNames as $oldName) {
            $newName = substr($oldName, 3);
            $c = "UPDATE {$prefix}_IWmain SET iw_module = 'IW{$newName}' WHERE iw_module = '{$oldName}'";
            if (!DBUtil::executeSQL($c)) {
                return false;
            }
        }

        //Update module_vars table
        //Update the name (keeps old var value)
        $c = "UPDATE {$prefix}_module_vars SET z_modname = 'IWmain' WHERE z_bkey = 'IWmain'";
        if (!DBUtil::executeSQL($c)) {
            return false;
        }

        //Array de noms
        $oldVarsNames = DBUtil::selectFieldArray("module_vars", 'name', "`z_modname` = 'IWmain'", '', false, '');

        $newVarsNames = Array('url', 'email', 'documentRoot', 'extensions', 'maxsize', 'usersvarslife',
            'cronHeaderText', 'cronFooterText', 'showHideFiles', 'URLBase');

        $newVars = Array('url' => 'http://phobos.xtec.net/intraweb',
            'email' => 'intraweb@xtec.cat',
            'documentRoot' => 'data',
            'extensions' => 'odt|ods|odp|zip|pdf|doc|jpg|gif|txt',
            'maxsize', '1000000',
            'usersvarslife' => '60',
            'cronHeaderText' => $this->__('Header text of the cron automatic emails with the new things to see'),
            'cronFooterText' => $this->__('Footer text of the email'),
            'showHideFiles' => '0',
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

        return true;
    }

}