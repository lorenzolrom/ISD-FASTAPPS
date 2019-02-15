-- ISD-FASTAPPS
-- N.I.S. URL Alias Extension
-- Database Initialization

CREATE TABLE `NIS_URLAlias` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`alias` varchar(64) NOT NULL,
	`destination` text NOT NULL,
	`disabled` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE KEY `alias` (`alias`)
);

-- Permissions

INSERT INTO Permission VALUES
	('nisurlaliases');

-- Pages

INSERT INTO Page (extension, title, url, permission, type) VALUES
	('nisurlalias', 'URL Aliases', 'webmanager/urlaliases', 'nisurlaliases', 'page'),
	('nisurlalias', 'New URL Alias', 'webmanager/urlaliases/new', 'nisurlaliases', 'page'),
	('nisurlalias', 'Edit URL Alias', 'webmanager/urlaliases/edit', 'nisurlaliases', 'page'),
	('nisurlalias', 'Delete URL Alias', 'webmanager/urlaliases/delete', 'nisurlaliases', 'page');
	
-- Page Parents
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager') AS x) WHERE url = 'webmanager/urlaliases';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager/urlaliases') AS x) WHERE url = 'webmanager/urlaliases/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager/urlaliases') AS x) WHERE url = 'webmanager/urlaliases/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager/urlaliases') AS x) WHERE url = 'webmanager/urlaliases/delete';