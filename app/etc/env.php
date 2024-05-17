<?php
return array (
  'cache' => 
  array (
    'frontend' => 
    array (
      'default' => 
      array (
        'backend' => 'Cm_Cache_Backend_Redis',
        'backend_options' => 
        array (
          'server' => 'localhost',
          'port' => '6370',
          'load_from_slave' => 
          array (
            'server' => 'localhost',
            'port' => '26370',
          ),
          'read_timeout' => 1,
          'retry_reads_on_master' => 1,
          'database' => 1,
        ),
        'frontend_options' => 
        array (
          'write_control' => false,
        ),
      ),
      'page_cache' => 
      array (
        'backend' => 'Cm_Cache_Backend_Redis',
        'backend_options' => 
        array (
          'server' => 'localhost',
          'port' => '6370',
          'load_from_slave' => 
          array (
            'server' => 'localhost',
            'port' => '26370',
          ),
          'read_timeout' => 1,
          'retry_reads_on_master' => 1,
          'database' => 2,
        ),
        'frontend_options' => 
        array (
          'write_control' => false,
        ),
      ),
    ),
    'graphql' => 
    array (
      'id_salt' => 'FSXI3QMazq993boW9mmj5kMShzzM6xhD',
    ),
  ),
  'MAGE_MODE' => 'production',
  'cache_types' => 
  array (
    'compiled_config' => 1,
    'config' => 1,
    'layout' => 1,
    'block_html' => 1,
    'collections' => 1,
    'reflection' => 1,
    'db_ddl' => 1,
    'eav' => 1,
    'customer_notification' => 1,
    'config_integration' => 1,
    'config_integration_api' => 1,
    'full_page' => 1,
    'target_rule' => 1,
    'config_webservice' => 1,
    'translate' => 1,
    'vertex' => 1,
  ),
  'cron' => 
  array (
  ),
  'backend' => 
  array (
    'frontName' => 'admin_5258h3',
  ),
  'remote_storage' => 
  array (
    'driver' => 'file',
  ),
  'queue' => 
  array (
    'amqp' => 
    array (
      'host' => 'localhost',
      'port' => '5672',
      'user' => 'myzz375vyfhda',
      'password' => 'ELBrWD0AHm3x8Ynq',
      'virtualhost' => 'myzz375vyfhda',
    ),
    'consumers_wait_for_messages' => 0,
  ),
  'db' => 
  array (
    'connection' => 
    array (
      'default' => 
      array (
        'host' => '127.0.0.1',
        'username' => 'myzz375vyfhda',
        'dbname' => 'myzz375vyfhda',
        'password' => 'FZk6O4mNWllOXC4',
      ),
      'indexer' => 
      array (
        'host' => '127.0.0.1',
        'username' => 'myzz375vyfhda',
        'dbname' => 'myzz375vyfhda',
        'password' => 'FZk6O4mNWllOXC4',
      ),
    ),
    'slave_connection' => 
    array (
      'default' => 
      array (
        'host' => '127.0.0.1:3304',
        'username' => 'myzz375vyfhda',
        'dbname' => 'myzz375vyfhda',
        'password' => 'FZk6O4mNWllOXC4',
        'model' => 'mysql4',
        'engine' => 'innodb',
        'initStatements' => 'SET NAMES utf8;',
        'active' => '1',
        'synchronous_replication' => true,
      ),
    ),
  ),
  'crypt' => 
  array (
    'key' => '7afe39808e3bf83a2dbbd993333b1f9f',
  ),
  'resource' => 
  array (
    'default_setup' => 
    array (
      'connection' => 'default',
    ),
  ),
  'x-frame-options' => 'SAMEORIGIN',
  'session' => 
  array (
    'save' => 'redis',
    'redis' => 
    array (
      'host' => 'localhost',
      'port' => '6370',
      'database' => 0,
      'disable_locking' => 1,
    ),
  ),
  'lock' => 
  array (
    'provider' => 'file',
    'config' => 
    array (
      'path' => '/run/myzz375vyfhda/locks',
    ),
  ),
  'directories' => 
  array (
    'document_root_is_pub' => true,
  ),
  'downloadable_domains' => 
  array (
    0 => 'mcprod.perfumeriasunidas.com.pe',
  ),
  'install' => 
  array (
    'date' => 'Wed, 05 Jan 2022 21:28:02 +0000',
  ),
  'static_content_on_demand_in_production' => 0,
  'force_html_minification' => 1,
  'cron_consumers_runner' => 
  array (
    'cron_run' => true,
    'max_messages' => 1000,
    'consumers' => 
    array (
    ),
    'multiple_processes' => 
    array (
    ),
  ),
  'system' => 
  array (
    'default' => 
    array (
      'catalog' => 
      array (
        'search' => 
        array (
          'engine' => 'amasty_elastic',
        ),
      ),
    ),
  ),
);