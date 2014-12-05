
DELETE FROM `topology` WHERE `topology_page` = '51301';
DELETE FROM `topology` WHERE `topology_page` = '513';
DELETE FROM `topology` WHERE `topology_page` = '403';

DELETE FROM `options` WHERE `key` = 'centreon_nagvis_uri';
DELETE FROM `options` WHERE `key` = 'centreon_nagvis_path';
