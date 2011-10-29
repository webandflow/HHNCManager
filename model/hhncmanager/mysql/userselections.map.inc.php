<?php
$xpdo_meta_map['UserSelections']= array (
  'package' => 'hhncmanager',
  'version' => '1.1',
  'table' => 'user_selections',
  'fields' => 
  array (
    'seasonid' => NULL,
    'week' => NULL,
    'userid' => NULL,
    'data' => NULL,
    'updated' => NULL,
  ),
  'fieldMeta' => 
  array (
    'seasonid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'week' => 
    array (
      'dbtype' => 'int',
      'precision' => '2',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'userid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'data' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
    ),
    'updated' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
  ),
  'indexes' => 
  array (
    'seasonid' => 
    array (
      'alias' => 'seasonid',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'seasonid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'week' => 
    array (
      'alias' => 'week',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'week' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'userid' => 
    array (
      'alias' => 'userid',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'userid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'updated' => 
    array (
      'alias' => 'updated',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'updated' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'data' => 
    array (
      'alias' => 'data',
      'primary' => false,
      'unique' => false,
      'type' => 'FULLTEXT',
      'columns' => 
      array (
        'data' => 
        array (
          'length' => '',
          'collation' => '',
          'null' => false,
        ),
      ),
    ),
  ),
);
