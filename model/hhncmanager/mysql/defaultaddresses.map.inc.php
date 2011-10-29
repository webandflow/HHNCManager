<?php
$xpdo_meta_map['DefaultAddresses']= array (
  'package' => 'hhncmanager',
  'version' => '1.1',
  'table' => 'default_addresses',
  'fields' => 
  array (
    'userid' => NULL,
    'addressid' => NULL,
    'type' => NULL,
    'timestamp' => NULL,
  ),
  'fieldMeta' => 
  array (
    'userid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'addressid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '2',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
    ),
    'timestamp' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
  ),
  'indexes' => 
  array (
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
    'addressid' => 
    array (
      'alias' => 'addressid',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'addressid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'type' => 
    array (
      'alias' => 'type',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'type' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'timestamp' => 
    array (
      'alias' => 'timestamp',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'timestamp' => 
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
    'UserAddresses' => 
    array (
      'class' => 'Addresses',
      'foreign' => 'id',
      'local' => 'addressid',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
