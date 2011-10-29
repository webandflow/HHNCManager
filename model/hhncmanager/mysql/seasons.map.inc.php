<?php
$xpdo_meta_map['Seasons']= array (
  'package' => 'hhncmanager',
  'version' => '1.1',
  'table' => 'seasons',
  'fields' => 
  array (
    'start' => NULL,
    'end' => NULL,
    'name' => NULL,
  ),
  'fieldMeta' => 
  array (
    'start' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'end' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '128',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'id' => 
    array (
      'alias' => 'id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'id' => 
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
    'Members' => 
    array (
      'class' => 'Memberships',
      'foreign' => 'seasonid',
      'local' => 'id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'DefItems' => 
    array (
      'class' => 'WeeklyDefaults',
      'foreign' => 'seasonid',
      'local' => 'id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'SeasCatalog' => 
    array (
      'class' => 'Catalog',
      'foreign' => 'seasonid',
      'local' => 'id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Order' => 
    array (
      'class' => 'Orders',
      'local' => 'id',
      'foreign' => 'seasonid',
      'cardinality' => 'many',
      'owner' => 'foreign',
    ),
  ),
);
