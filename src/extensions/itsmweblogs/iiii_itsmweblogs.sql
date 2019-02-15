-- ISD-FASTAPPS
-- ITSM WebLog Extension
-- Database Initialization

-- Permissions

INSERT INTO Permission VALUES
	('itsm_weblogs');

-- Pages

INSERT INTO Page (extension, title, url, permission, type) VALUES
	('itsmweblogs', 'Web Logs', 'webmanager/weblogs', 'itsm_weblogs', 'page'),
	('itsmweblogs', 'View Site', 'webmanager/weblogs/site', 'itsm_weblogs', 'page'),
	('itsmweblogs', 'View Log', 'webmanager/weblogs/log', 'itsm_weblogs', 'page');
	
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager') AS x) WHERE url = 'webmanager/weblogs';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager/weblogs') AS x) WHERE url = 'webmanager/weblogs/site';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager/weblogs') AS x) WHERE url = 'webmanager/weblogs/log';