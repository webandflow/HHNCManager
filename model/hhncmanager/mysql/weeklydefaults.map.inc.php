<?php
$xpdo_meta_map['WeeklyDefaults']= array (
  'package' => 'hhncmanager',
  'version' => '1.1',
  'table' => 'weekly_defaults',
  'fields' => 
  array (
    'seasonid' => NULL,
    'week' => NULL,
    'data' => NULL,
    'submittedby' => NULL,
    'submittedon' => NULL,
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
    'data' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
    ),
    'submittedby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'submittedon' => 
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
    'submittedby' => 
    array (
      'alias' => 'submittedby',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'submittedby' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'submittedon' => 
    array (
      'alias' => 'submittedon',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'submittedon' => 
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
  'aggregates' => 
  array (
    'DefSeason' => 
    array (
      'class' => 'Seasons',
      'foreign' => 'id',
      'local' => 'seasonid',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
