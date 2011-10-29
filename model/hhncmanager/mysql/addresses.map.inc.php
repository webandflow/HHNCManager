<?php
$xpdo_meta_map['Addresses']= array (
  'package' => 'hhncmanager',
  'version' => '1.1',
  'table' => 'addresses',
  'fields' => 
  array (
    'name' => NULL,
    'st1' => NULL,
    'st2' => NULL,
    'city' => NULL,
    'st' => NULL,
    'zip' => NULL,
    'useraddress' => NULL,
    'addedby' => NULL,
    'addedon' => NULL,
    'active' => NULL,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '128',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
    ),
    'st1' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '128',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
    ),
    'st2' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '128',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
    ),
    'city' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '128',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
    ),
    'st' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '2',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
    ),
    'zip' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
    ),
    'useraddress' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'addedby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'addedon' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
  ),
  'indexes' => 
  array (
    'dropoff' => 
    array (
      'alias' => 'dropoff',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'useraddress' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'addedby' => 
    array (
      'alias' => 'addedby',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'addedby' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'addedon' => 
    array (
      'alias' => 'addedon',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'addedon' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'active' => 
    array (
      'alias' => 'active',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'active' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'name' => 
    array (
      'alias' => 'name',
      'primary' => false,
      'unique' => false,
      'type' => 'FULLTEXT',
      'columns' => 
      array (
        'name' => 
        array (
          'length' => '',
          'collation' => '',
          'null' => false,
        ),
      ),
    ),
    'st1' => 
    array (
      'alias' => 'st1',
      'primary' => false,
      'unique' => false,
      'type' => 'FULLTEXT',
      'columns' => 
      array (
        'st1' => 
        array (
          'length' => '',
          'collation' => '',
          'null' => false,
        ),
      ),
    ),
    'st2' => 
    array (
      'alias' => 'st2',
      'primary' => false,
      'unique' => false,
      'type' => 'FULLTEXT',
      'columns' => 
      array (
        'st2' => 
        array (
          'length' => '',
          'collation' => '',
          'null' => false,
        ),
      ),
    ),
    'city' => 
    array (
      'alias' => 'city',
      'primary' => false,
      'unique' => false,
      'type' => 'FULLTEXT',
      'columns' => 
      array (
        'city' => 
        array (
          'length' => '',
          'collation' => '',
          'null' => false,
        ),
      ),
    ),
    'st' => 
    array (
      'alias' => 'st',
      'primary' => false,
      'unique' => false,
      'type' => 'FULLTEXT',
      'columns' => 
      array (
        'st' => 
        array (
          'length' => '',
          'collation' => '',
          'null' => false,
        ),
      ),
    ),
    'zip' => 
    array (
      'alias' => 'zip',
      'primary' => false,
      'unique' => false,
      'type' => 'FULLTEXT',
      'columns' => 
      array (
        'zip' => 
        array (
          'length' => '',
          'collation' => '',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'DefaultAddress' => 
    array (
      'class' => 'DefaultAddresses',
      'foreign' => 'addressid',
      'local' => 'id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Order' => 
    array (
      'class' => 'Orders',
      'local' => 'id',
      'foreign' => 'addressid',
      'cardinality' => 'many',
      'owner' => 'foreign',
    ),
  ),
);
