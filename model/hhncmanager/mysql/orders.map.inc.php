<?php
$xpdo_meta_map['Orders']= array (
  'package' => 'hhncmanager',
  'version' => '1.1',
  'table' => 'orders',
  'fields' => 
  array (
    'modx_user_id' => NULL,
    'seasonid' => NULL,
    'week' => NULL,
    'data' => NULL,
    'is_alacarte' => NULL,
    'is_homedeliver' => NULL,
    'addressid' => NULL,
    'time' => NULL,
  ),
  'fieldMeta' => 
  array (
    'modx_user_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'seasonid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'week' => 
    array (
      'dbtype' => 'int',
      'precision' => '4',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'data' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
    ),
    'is_alacarte' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
    ),
    'is_homedeliver' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
    ),
    'addressid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'time' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'modx_user_id' => 
    array (
      'alias' => 'modx_user_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'modx_user_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'seasonid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'week' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'is_alacarte' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'is_homedeliver' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'addressid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'time' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Season' => 
    array (
      'class' => 'Seasons',
      'foreign' => 'id',
      'local' => 'seasonid',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Address' => 
    array (
      'class' => 'Addresses',
      'foreign' => 'id',
      'local' => 'addressid',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
