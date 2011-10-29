<?php
$xpdo_meta_map['FoxyCartResponses']= array (
  'package' => 'hhncmanager',
  'version' => '1.1',
  'table' => 'foxy_cart_responses',
  'fields' => 
  array (
    'time' => NULL,
    'data' => NULL,
  ),
  'fieldMeta' => 
  array (
    'time' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'data' => 
    array (
      'dbtype' => 'longtext',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'time' => 
    array (
      'alias' => 'time',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'time' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
