<?php
$xpdo_meta_map['PointLevels']= array (
  'package' => 'hhncmanager',
  'version' => '1.1',
  'table' => 'point_levels',
  'fields' => 
  array (
    'title_modifier' => NULL,
    'points' => NULL,
  ),
  'fieldMeta' => 
  array (
    'title_modifier' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '128',
      'phptype' => 'string',
      'null' => true,
    ),
    'points' => 
    array (
      'dbtype' => 'int',
      'precision' => '6',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
  ),
  'indexes' => 
  array (
    'points' => 
    array (
      'alias' => 'points',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'points' => 
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
    'MembershipLevel' => 
    array (
      'class' => 'MembershipLevels',
      'foreign' => 'level_points',
      'local' => 'id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
