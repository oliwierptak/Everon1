<?php return array (
  0 => 
  array (
    'TABLE_NAME' => 'user_group_rel',
    'COLUMN_NAME' => 'user_id',
    'CONSTRAINT_NAME' => 'user_id_fk',
    'REFERENCED_TABLE_NAME' => 'user',
    'REFERENCED_COLUMN_NAME' => 'id',
  ),
  1 => 
  array (
    'TABLE_NAME' => 'user_group_rel',
    'COLUMN_NAME' => 'group_id',
    'CONSTRAINT_NAME' => 'group_id_fk',
    'REFERENCED_TABLE_NAME' => 'user_group',
    'REFERENCED_COLUMN_NAME' => 'id',
  ),
); 