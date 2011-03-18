<?php
/**
 * Define module tables
 * @author Albert P�rez Monfort (aperezm@xtec.cat)
 * @return module tables information
 */
function IWmain_tables()
{
    // Initialise table array
    $table = array();

    // IWmain table definition
    $table['IWmain'] = DBUtil::getLimitedTablename('IWmain');
    $table['IWmain_column'] = array('id' => 'iw_id',
                                    'module' => 'iw_module',
                                    'name' => 'iw_name',
                                    'value' => 'iw_value',
                                    'uid' => 'iw_uid',
                                    'lifetime' => 'iw_lifetime',
                                    'nult' => 'iw_nult');

    $table['IWmain_column_def'] = array('id' => "I NOTNULL AUTO PRIMARY",
                                        'module' => "C(50) NOTNULL DEFAULT ''",
                                        'name' => "C(50) NOTNULL DEFAULT ''",
                                        'value' => "X NOTNULL",
                                        'uid' => "I NOTNULL DEFAULT '0'",
                                        'lifetime' => "C(20) NOTNULL DEFAULT '0'",
                                        'nult' => "I(1) NOTNULL DEFAULT '0'");

    ObjectUtil::addStandardFieldsToTableDefinition($table['IWmain_column'], 'pn_');
    ObjectUtil::addStandardFieldsToTableDataDefinition($table['IWmain_column_def'], 'iw_');

    // IWusers table definition
    $table['IWusers'] = DBUtil::getLimitedTablename('IWusers');
    $table['IWusers_column'] = array('suid' => 'iw_suid',
                                            'uid' => 'iw_uid',
                                            'id' => 'iw_id',
                                            'nom' => 'iw_nom',
                                            'cognom1' => 'iw_cognom1',
                                            'cognom2' => 'iw_cognom2',
                                            'naixement' => 'iw_naixement',
                                            'accio' => 'iw_accio');
    // Return the table information
    return $table;
}