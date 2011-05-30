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

        //Create indexes
        $pntable = DBUtil::getTables();
        $c = $pntable['IWmain_column'];
        if (!DBUtil::createIndex($c['module'], 'IWmain', 'module'))
            return false;
        if (!DBUtil::createIndex($c['name'], 'IWmain', 'name'))
            return false;
        if (!DBUtil::createIndex($c['uid'], 'IWmain', 'uid'))
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

        $prefix = $GLOBALS['ZConfig']['System']['prefix'];

        //Rename table
        if (!DBUtil::renameTable('iw_main', 'IWmain'))
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
        
        // Array de d'arrays de parells [name] [value]
        $oldVars = DBUtil::selectObjectArray("module_vars", "`z_modname` = 'iw_main'", '', -1, -1, '', null, null, array('name', 'value'));

        //Array de noms
        $oldVarsNames = DBUtil::selectFieldArray("module_vars", 'name', "`z_modname` = 'iw_main'", '', false, '');

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


        //Delete unneeded vars and update the rest
        foreach ($oldVarsNames as $old) {
            // echo ($old . '<br>');
            ModUtil::delVar('iw_main', $old);
            if ($newVars[$old]) {
                //     echo ($old . ' ' . $newVars[$old]);
                $this->addVar($old, $oldVars[$old]);
            }
        }

        //Add new vars
        $add = array_diff($newVarsNames, $oldVarsNames);
        foreach ($add as $i) {
            //  echo($i . ': ' . 'afegida '/*$newVars[$i]*/);
            $this->setVar($i, $newVars[$i]);
        }

        return true;
    }

}