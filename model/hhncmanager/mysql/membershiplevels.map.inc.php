<?php
$xpdo_meta_map['Membershiplevels']= array (
  'package' => 'hhncmanager',
  'version' => '1.1',
  'table' => 'membershiplevels',
  'fields' => 
  array (
    'level_number' => NULL,
    'level_name' => NULL,
    'level_points' => NULL,
    'level_price' => NULL,
    'custom_order' => NULL,
    'alacarte' => NULL,
    'homedelivery' => NULL,
  ),
  'fieldMeta' => 
  array (
    'level_number' => 
    array (
      'dbtype' => 'int',
      'precision' => '5',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'level_name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '128',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
    ),
    'level_points' => 
    array (
      'dbtype' => 'int',
      'precision' => '5',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'level_price' => 
    array (
      'dbtype' => 'double',
      'phptype' => 'float',
      'null' => false,
      'index' => 'index',
    ),
    'custom_order' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'alacarte' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'homedelivery' => 
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
    'level_number' => 
    array (
      'alias' => 'level_number',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'level_number' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'level_points' => 
    array (
      'alias' => 'level_points',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'level_points' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'custom_order' => 
    array (
      'alias' => 'custom_order',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'custom_order' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'alacart' => 
    array (
      'alias' => 'alacart',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'alacarte' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'homedelivery' => 
    array (
      'alias' => 'homedelivery',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'homedelivery' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'level_price' => 
    array (
      'alias' => 'level_price',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'level_price' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'level_name' => 
    array (
      'alias' => 'level_name',
      'primary' => false,
      'unique' => false,
      'type' => 'FULLTEXT',
      'columns' => 
      array (
        'level_name' => 
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
    'Mem' => 
    array (
      'class' => 'Memberships',
      'local' => 'level_number',
      'foreign' => 'membership_status',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'PointLevel' => 
    array (
      'class' => 'PointLevels',
      'foreign' => 'id',
      'local' => 'level_points',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
