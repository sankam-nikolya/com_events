DROP TABLE IF EXISTS `#__events_categories`;
DROP TABLE IF EXISTS `#__events_items`;
DELETE FROM `#__content_types` WHERE (type_alias LIKE 'com_events.%');
