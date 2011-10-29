<?php
$xpdo_meta_map['Memberships']= array (
  'package' => 'hhncmanager',
  'version' => '1.1',
  'table' => 'memberships',
  'fields' => 
  array (
    'modx_user_id' => NULL,
    'seasonid' => NULL,
    'membership_status' => NULL,
    'membership_verified' => NULL,
    'can_alacarte' => NULL,
    'can_customorder' => NULL,
    'can_homedeliver' => NULL,
    'manual_override' => NULL,
  ),
  'fieldMeta' => 
  array (
    'modx_user_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'seasonid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'membership_status' => 
    array (
      'dbtype' => 'int',
      'precision' => '2',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'membership_verified' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'can_alacarte' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'can_customorder' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'can_homedeliver' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'manual_override' => 
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
      ),
    ),
    'membership_status' => 
    array (
      'alias' => 'membership_status',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'membership_status' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
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
    'can_alacarte' => 
    array (
      'alias' => 'can_alacarte',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'can_alacarte' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'can_customorder' => 
    array (
      'alias' => 'can_customorder',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'can_customorder' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'can_homedeliver' => 
    array (
      'alias' => 'can_homedeliver',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'can_homedeliver' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'manual_override' => 
    array (
      'alias' => 'manual_override',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'manual_override' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'membership_verified' => 
    array (
      'alias' => 'membership_verified',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'membership_verified' => 
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
    'Level' => 
    array (
      'class' => 'Membershiplevels',
      'foreign' => 'level_number',
      'local' => 'membership_status',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Season' => 
    array (
      'class' => 'Seasons',
      'foreign' => 'id',
      'local' => 'seasonid',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
