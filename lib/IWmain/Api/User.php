<?php
class IWmain_Api_User extends Zikula_Api
{
    /**
     * Get all the users
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	And array with the users
    */
    public function getAllUsers($args)
    {
        $fromArray = FormUtil::getPassedValue('fromArray', isset($args['fromArray']) ? $args['fromArray'] : null, 'POST');
        $list = FormUtil::getPassedValue('list', isset($args['list']) ? $args['list'] : null, 'POST');
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))){
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        $pntable = DBUtil::getTables();
        $where = "";
        $c = $pntable['users_column'];
        if ($fromArray != null && count($fromArray) > 0) {
            foreach ($fromArray as $f) {
                $where .= " $c[uid] = $f[uid] OR";
            }
            $where = substr($where,0,-3);
        }
        if ($list != null && strlen($list) > 0) {
            $modArray = explode('$$',$list);
            $modArray = array_unique($modArray);
            foreach ($modArray as $mod) {
                $mod = str_replace('$','',$mod);
                if ($mod != '' && is_numeric($mod)) {
                    $where .= " $c[uid] = " . $mod . " OR";
                }
            }
            $where = substr($where,0,-3);
        }
        // get the objects from the db
        $items = DBUtil::selectObjectArray('users', $where);
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) return LogUtil::registerError ($this->__('Error! Could not load items.'));
        // Return the items
        return $items;
    }

    /**
     * Get information from IWusers of all users
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	And array with the users
    */
    public function getUsersExtraInfo($args)
    {
        $fromArray = FormUtil::getPassedValue('fromArray', isset($args['fromArray']) ? $args['fromArray'] : null, 'POST');
        $list = FormUtil::getPassedValue('list', isset($args['list']) ? $args['list'] : null, 'POST');
        $items = array();
        $pntable = DBUtil::getTables();
        $where = "";
        $c = $pntable['IWusers_column'];
        //die('tt');
        if ($fromArray != null && count($fromArray) > 0) {
            foreach ($fromArray as $f) {
                $where .= " $c[uid] = $f[uid] OR";
            }
            $where = substr($where,0,-3);
        }
        if ($list != null && strlen($list) > 0) {
            $modArray = explode('$$',$list);
            $modArray = array_unique($modArray);
            foreach ($modArray as $mod) {
                $mod = str_replace('$','',$mod);
                if ($mod != '' && is_numeric($mod)) $where .= " $c[uid] = " . $mod . " OR";
            }
            $where = substr($where,0,-3);
        }
        // get the objects from the db
        $items = DBUtil::selectObjectArray('IWusers', $where);
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) {
            return LogUtil::registerError ($this->__('Error! Could not load items.'));
        }
        // Return the items
        return $items;
    }

    /**
     * Get an user
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	args   id of the user
     * @return:	And array with the user information
    */
    public function getUser($args)
    {
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        $uid = FormUtil::getPassedValue('uid', isset($args['uid']) ? $args['uid'] : null, 'POST');
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))){
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        $items = array();
        $pntable = DBUtil::getTables();
        $c = $pntable['users_column'];
        $where = "$c[uid]=$uid";
        // get the objects from the db
        $items = DBUtil::selectObjectArray('users', $where);
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) return LogUtil::registerError ($this->__('Error! Could not load items.'));

        // Return the items
        return $items;
    }

    /**
     * Get information from IWusers of an users
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	args   id of the user
     * @return:	And array with the user information
    */
    public function getUserExtraInfo($args)
    {
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        $uid = FormUtil::getPassedValue('uid', isset($args['uid']) ? $args['uid'] : null, 'POST');
        $items = array();
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))) {
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        $pntable = DBUtil::getTables();
        $c = $pntable['IWusers_column'];
        $where = "$c[uid]=$uid";
        // get the objects from the db
        $items = DBUtil::selectObjectArray('IWusers', $where);
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) {
            return LogUtil::registerError ($this->__('Error! Could not load items.'));
        }
        // Return the items
        return $items;
    }

    /**
     * Get all the groups
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	And array with the users
    */
    public function getAllGroups($args)
    {
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))) {
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        $pntable = DBUtil::getTables();
        $c = $pntable['groups_column'];
        $orderby = "$c[name]";
        $items = array();
        // get the objects from the db
        $items = DBUtil::selectObjectArray('groups', '', $orderby);
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) {
            return LogUtil::registerError ($this->__('Error! Could not load items.'));
        }
        //print_r($items);
        // Return the items
        return $items;
    }

    /**
     * Get the members of a group
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	And array with the users
    */
    public function getMembersGroup($args)
    {
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        $gid = FormUtil::getPassedValue('gid', isset($args['gid']) ? $args['gid'] : null, 'POST');
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))) {
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        $myJoin = array();
        $myJoin[] = array('join_table' => 'users',
                          'join_field' => array('uid'),
                          'object_field_name' => array('uid'),
                          'compare_field_table' => 'uid',
                          'compare_field_join' => 'uid');
        $myJoin[] = array('join_table' => 'group_membership',
                          'join_field' => array(),
                          'object_field_name' => array(),
                          'compare_field_table' => 'uid',
                          'compare_field_join' => 'uid');
        $pntables = DBUtil::getTables();
        $ccolumn = $pntables['users_column'];
        $ocolumn = $pntables['group_membership_column'];
        $where = "b.$ocolumn[gid] = " . $gid;
        $orderBy = "ORDER BY tbl.$ccolumn[uname]";
        $items = DBUtil::selectExpandedObjectArray('users', $myJoin, $where, $orderBy);
        return $items;
    }

    /**
     * Get all the groups
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	And array with the users
    */
    public function getAllGroupsInfo($args)
    {
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))) {
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        $items = array();
        // get the objects from the db
        $items = DBUtil::selectObjectArray('groups');
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) return LogUtil::registerError ($this->__('Error! Could not load items.'));

        // Return the items
        return $items;
    }

    /**
     * Check if a user is member of a group
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	True if the user is member and false otherwise
    */
    public function isMember($args)
    {
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        $gid = FormUtil::getPassedValue('gid', isset($args['gid']) ? $args['gid'] : null, 'POST');
        $uid = FormUtil::getPassedValue('uid', isset($args['uid']) ? $args['uid'] : null, 'POST');
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))){
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        if ($uid == null || !is_numeric($uid)) {
            return LogUtil::registerError ($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        //Check if the user is member of the group
        if ($gid != 0){
            $items = array();
            $pntable = DBUtil::getTables();
            $c = $pntable['group_membership_column'];
            $where = "$c[uid]=" . $uid . " AND $c[gid]=" . $gid;
            // get the objects from the db
            $items = DBUtil::selectObjectArray('group_membership', $where);
            // Check for an error with the database code, and if so set an appropriate
            // error message and return
            if ($items === false) return LogUtil::registerError ($this->__('Error! Could not load items.'));
            $isMember = (count($items) > 0) ? true : false;
        }else{
            $isMember = true;
        }
        return $isMember;
    }

    /**
     * Get all the groups of a user
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	And array with the users
    */
    public function getAllUserGroups($args)
    {
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        $uid = FormUtil::getPassedValue('uid', isset($args['uid']) ? $args['uid'] : null, 'POST');
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))){
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        // argument needed
        if ($uid == null || !is_numeric($uid)) return false;
        $items = array();
        $pntable = DBUtil::getTables();
        $c = $pntable['group_membership_column'];
        $where = "$c[uid]=" . $uid;
        // get the objects from the db
        $items = DBUtil::selectObjectArray('group_membership', $where);
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) return LogUtil::registerError ($this->__('Error! Could not load items.'));
        // Return the items
        return $items;
    }

    //***************************************************************************************
    //
    // API function used to work with the database
    //
    // All this functions are call from the users managment funcions
    //
    //***************************************************************************************

    /**
     * Get an user variable associate with a module
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	args   Array with the elements:
                    - module: module where the varible is used
                    - name: name of the variable
                    - uid: user id
                    - sv: security value
     * @return:	The value of the variable if it is find
    */
    public function userGetVar($args)
    {
        $uid = FormUtil::getPassedValue('uid', isset($args['uid']) ? $args['uid'] : null, 'POST');
        $module = FormUtil::getPassedValue('module', isset($args['module']) ? $args['module'] : null, 'POST');
        $name = FormUtil::getPassedValue('name', isset($args['name']) ? $args['name'] : null, 'POST');
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))) {
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        // Argument check
        if ($uid == null ||	$module == null || $name == null) {
            return LogUtil::registerError ($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        $pntable = DBUtil::getTables();
        $c = $pntable['IWmain_column'];
        $where = "$c[uid]=" . $uid . " AND $c[module]='" . $module . "' AND $c[name]='" . $name . "'";
        // get the objects from the db
        $items = DBUtil::selectObjectArray('IWmain', $where);
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) return LogUtil::registerError ($this->__('Error! Could not load items.'));

        // Return the items
        return $items;
    }

    /**
     * Check if an user variable exists
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	args   Array with the elements:
                    - module: module where the varible is used
                    - name: name of the variable
                    - uid: user id
                    - sv: security value
     * @return:	Thue if exists and false if not
    */
    public function userVarExists($args)
    {
        $uid = FormUtil::getPassedValue('uid', isset($args['uid']) ? $args['uid'] : null, 'POST');
        $module = FormUtil::getPassedValue('module', isset($args['module']) ? $args['module'] : null, 'POST');
        $name = FormUtil::getPassedValue('name', isset($args['name']) ? $args['name'] : null, 'POST');
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))){
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        // Argument check
        if ($uid == null || $module == null || $name == null){
            return LogUtil::registerError ($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        $pntable = DBUtil::getTables();
        $c = $pntable['IWmain_column'];
        $where = "$c[uid]=" . $uid . " AND $c[module]='" . $module . "' AND $c[name]='" . $name."'";
        // get the objects from the db
        $items = DBUtil::selectObjectArray('IWmain', $where);
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) return LogUtil::registerError ($this->__('Error! Could not load items.'));

        // Return true if the item exists or false if not
        $exists = (count($items) > 0) ? true : false;
        return $exists;
    }

    /**
     * Create an user variable associated with a module
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	args   Array with the elements:
                    - module: module where the varible is used
                    - name: name of the variable
                    - lifetime: date of caducity of the variable
                    - uid: user id
                    - value: value for the variable
                    - sv: security value
     * @return:	The id of the value created
    */
    public function createUserVar($args)
    {
        $uid = FormUtil::getPassedValue('uid', isset($args['uid']) ? $args['uid'] : null, 'POST');
        $module = FormUtil::getPassedValue('module', isset($args['module']) ? $args['module'] : null, 'POST');
        $value = FormUtil::getPassedValue('value', isset($args['value']) ? $args['value'] : '', 'POST');
        $lifetime = FormUtil::getPassedValue('lifetime', isset($args['lifetime']) ? $args['lifetime'] : null, 'POST');
        $name = FormUtil::getPassedValue('name', isset($args['name']) ? $args['name'] : null, 'POST');
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))){
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        // Argument check
        if ($uid == null || $module == null || $name == null || $lifetime == null) {
            return LogUtil::registerError ($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        $item = array('uid' => $uid,
                      'module' => $module,
                      'name' => $name,
                      'value' => $value,
                      'lifetime' => $lifetime);
        if (!DBUtil::insertObject($item, 'IWmain')) {
            return LogUtil::registerError ($this->__('Error! Creation attempt failed.'));
        }
        // Return the id of the newly created item to the calling process
        return true;
    }

    /**
    * Update the field lifetime in users variables
    * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
    * @param:	args   Array with the elements:
                - module: module where the varible have to be deleted
                - name: name of the variable that have to be deleted (if name is .* all varibles of the user in the module are deleted)
                - uid: user id
                - sv: security value
    * @return:	True if success
    */
    public function userUpdateGetVarTime($args){
        $uid = FormUtil::getPassedValue('uid', isset($args['uid']) ? $args['uid'] : null, 'POST');
        $module = FormUtil::getPassedValue('module', isset($args['module']) ? $args['module'] : null, 'POST');
        $name = FormUtil::getPassedValue('name', isset($args['name']) ? $args['name'] : null, 'POST');
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))){
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        // Argument check
        if ($uid == null ||	$module == null || $name == null) {
            return LogUtil::registerError ($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        $item = array('lifetime' => time() + 24*60*60*ModUtil::getVar('IWmain', 'usersvarslife'),
                      'nult' => 0);
        $pntable = DBUtil::getTables();
        $c = $pntable['IWmain_column'];
        $where = "$c[uid]=" . $uid . " AND $c[module]='" . $module . "' AND $c[name]='" . $name . "'";
        if (!DBUtil::updateObject($item, 'IWmain', $where, 'mid')) return LogUtil::registerError ($this->__('Error! Update attempt failed.'));

        // Let the calling process know that we have finished successfully
        return true;
    }

    /**
     * Update the field lifetime in users variables
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	args   Array with the elements:
                    - module: module where the varible have to be deleted
                    - name: name of the variable that have to be deleted (if name is .* all varibles of the user in the module are deleted)
                    - uid: user id
                    - sv: security value
     * @return:	True if success
    */
    public function userUpdateNultVar($args){
        $uid = FormUtil::getPassedValue('uid', isset($args['uid']) ? $args['uid'] : null, 'POST');
        $module = FormUtil::getPassedValue('module', isset($args['module']) ? $args['module'] : null, 'POST');
        $name = FormUtil::getPassedValue('name', isset($args['name']) ? $args['name'] : null, 'POST');
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))){
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        // Argument check
        if ($uid == null ||	$module == null || $name == null){
            return LogUtil::registerError ($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        $item = array('nult' => 1);
        $pntable = DBUtil::getTables();
        $c = $pntable['IWmain_column'];
        $where = "$c[uid]=" . $uid . " AND $c[module]='" . $module . "' AND $c[name]='" . $name . "'";
        if (!DBUtil::updateObject($item, 'IWmain', $where, 'mid')) {
            return LogUtil::registerError ($this->__('Error! Update attempt failed.'));
        }
        // Let the calling process know that we have finished successfully
        return true;
    }

    /**
     * Update an user variable associate with a module
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	args   Array with the elements:
                    - module: module where the varible is used
                    - name: name of the variable
                    - lifetime: date of caducity of the variable
                    - uid: user id
                    - value: value for the variable
                    - sv: security value
     * @return:	Thue if success
    */
    public function updateUserVar($args){
        $uid = FormUtil::getPassedValue('uid', isset($args['uid']) ? $args['uid'] : null, 'POST');
        $module = FormUtil::getPassedValue('module', isset($args['module']) ? $args['module'] : null, 'POST');
        $value = FormUtil::getPassedValue('value', isset($args['value']) ? $args['value'] : null, 'POST');
        $lifetime = FormUtil::getPassedValue('lifetime', isset($args['lifetime']) ? $args['lifetime'] : null, 'POST');
        $name = FormUtil::getPassedValue('name', isset($args['name']) ? $args['name'] : null, 'POST');
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))) {
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        // Argument check
        if ($uid == null || $module == null || $name == null || $lifetime == null) {
            return LogUtil::registerError ($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        $item = array('value' => $value,
                      'lifetime' => $lifetime);
        $pntable = DBUtil::getTables();
        $c = $pntable['IWmain_column'];
        $where = "$c[uid]=" . $uid . " AND $c[module]='" . $module . "' AND $c[name]='" . $name . "'";
        if (!DBUtil::updateObject($item, 'IWmain', $where, 'mid')) {
            return LogUtil::registerError ($this->__('Error! Update attempt failed.'));
        }
        // Let the calling process know that we have finished successfully
        return true;
    }

    /**
     * Delete the user variables that have been raised the lifetime value
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	args   Array with the elements:
                    - sv: security value
     * @return:	Thue if success
    */
    public function userDeleteOldVars($args){
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))) {
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        $now = time();
        $pntables = DBUtil::getTables();
        $c = $pntables['IWmain_column'];
        $where    = "WHERE $c[lifetime] < '$now'";
        if (!DBUtil::deleteWhere('IWmain', $where)) {
            return LogUtil::registerError ($this->__('Error! Sorry! Deletion attempt failed.'));
        }
        // Let the calling process know that we have finished successfully
        return true;
    }

    /**
     * Delete all users variables of a module
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	args   Array with the elements:
                    - module: module where the varible is used
                    - name: name of the variable to delete (the value .* means all the variables)
                    - sv: security value
     * @return:	Thue if success
    */
    public function usersVarsDelModule($args){
        $module = FormUtil::getPassedValue('module', isset($args['module']) ? $args['module'] : null, 'POST');
        $name = FormUtil::getPassedValue('name', isset($args['name']) ? $args['name'] : null, 'POST');
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))) {
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        // Argument check
        if ($module == null || $name == null) {
            return LogUtil::registerError ($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        $pntables = DBUtil::getTables();
        $c = $pntables['IWmain_column'];
        $where = ($name == '.*') ? "WHERE $c[module] = '" . $module . "'" : "WHERE $c[module] = '" . $module . "' AND $c[name]='" . $name . "'";
        if (!DBUtil::deleteWhere('IWmain', $where)) return LogUtil::registerError ($this->__('Error! Sorry! Deletion attempt failed.'));

        // Let the calling process know that we have finished successfully
        return true;
    }

    /**
     * Delete the users variables of a module for an user
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	args   Array with the elements:
                    - uid: user id
                    - module: module where the varible is used
                    - name: name of the variable to delete (the value .* means all the variables)
                    - sv: security value
     * @return:	Thue if success
    */
    public function userDelVar($args){
        $uid = FormUtil::getPassedValue('uid', isset($args['uid']) ? $args['uid'] : null, 'POST');
        $module = FormUtil::getPassedValue('module', isset($args['module']) ? $args['module'] : null, 'POST');
        $name = FormUtil::getPassedValue('name', isset($args['name']) ? $args['name'] : null, 'POST');
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))) {
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        // Argument check
        if ($module == null || $uid == null || $name == null) {
            return LogUtil::registerError ($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        $pntables = DBUtil::getTables();
        $c = $pntables['IWmain_column'];
        $where = ($name == '.*') ? "WHERE $c[module] = '" . $module . "' AND $c[uid] = " . $uid : "WHERE $c[module] = '" . $module . "' AND $c[name] = '" . $name . "' AND $c[uid] = " . $uid;
        if (!DBUtil::deleteWhere('IWmain', $where)) {
            return LogUtil::registerError ($this->__('Error! Sorry! Deletion attempt failed.'));
        }
        // Let the calling process know that we have finished successfully
        return true;
    }

    /**
     * Delete all the variables for a user that are temporally. The variables that have got the parameter nult in the value 1
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	args   Array with the elements:
                    - uid: user id
                    - sv: security value
     * @return:	True if success and false if not
    */
    public function regenDinamicVars ($args){
        $uid = FormUtil::getPassedValue('uid', isset($args['uid']) ? $args['uid'] : null, 'POST');
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))) {
            return LogUtil::registerError ($this->__('You are not allowed to access to some information.'));
        }
        // Argument check
        if ($uid == null) {
            return LogUtil::registerError ($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        $pntables = DBUtil::getTables();
        $c = $pntables['IWmain_column'];
        $where = "WHERE $c[nult] = 1 AND $c[uid] = " . $uid;
        if (!DBUtil::deleteWhere('IWmain', $where)) return LogUtil::registerError ($this->__('Error! Sorry! Deletion attempt failed.'));

        // Let the calling process know that we have finished successfully
        return true;
    }
    //***************************************************************************************
    //
    // END - API function used to work with the database
    //
    //***************************************************************************************
}