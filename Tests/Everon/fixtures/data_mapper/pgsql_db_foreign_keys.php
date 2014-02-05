<?php return array (
  0 => 
  array (
    'constraint_catalog' => 'everon_test',
    'constraint_schema' => 'public',
    'constraint_name' => 'Ref_user_has_user_group_to_user',
    'table_catalog' => 'everon_test',
    'table_schema' => 'public',
    'table_name' => 'user_group_rel',
    'constraint_type' => 'FOREIGN KEY',
    'is_deferrable' => 'NO',
    'initially_deferred' => 'NO',
    'column_name' => 'user_id',
    'ordinal_position' => 1,
    'position_in_unique_constraint' => 1,
    'TABLE_NAME' => 'user_group_rel',
  ),
  1 => 
  array (
    'constraint_catalog' => 'everon_test',
    'constraint_schema' => 'public',
    'constraint_name' => 'Ref_user_has_user_group_to_user_group',
    'table_catalog' => 'everon_test',
    'table_schema' => 'public',
    'table_name' => 'user_group_rel',
    'constraint_type' => 'FOREIGN KEY',
    'is_deferrable' => 'NO',
    'initially_deferred' => 'NO',
    'column_name' => 'group_id',
    'ordinal_position' => 1,
    'position_in_unique_constraint' => 1,
    'TABLE_NAME' => 'user_group_rel',
  ),
); 