-- ISD-FASTAPPS
-- ITSM Monitor Extension (Services/Applications)
-- Database Initialization

CREATE TABLE ITSM_ApplicationCategory(
	id INT(11) NOT NULL AUTO_INCREMENT,
	name VARCHAR(64) NOT NULL UNIQUE,
	displayed TINYINT(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (id)
);

CREATE TABLE ITSM_Application_ApplicationCategory(
	`application` INT(11) NOT NULL,
	category INT(11) NOT NULL,
	PRIMARY KEY (`application`, category),
	FOREIGN KEY (`application`) REFERENCES ITSM_Application(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (category) REFERENCES ITSM_ApplicationCategory(id) ON UPDATE CASCADE ON DELETE CASCADE
);

--Permissions

INSERT INTO Permission VALUES
	('itsmmonitor-services'),
	('itsmmonitor-services-write');

-- Pages

INSERT INTO Page (extension, title, url, permission, type) VALUES
	('servicenter', 'Service Monitor', 'monitor/services', 'itsmmonitor-services', 'page'),
	('servicenter', 'Service Monitor A.P.I.', 'monitor/services/api', 'itsmmonitor-services', 'page'),
	('servicenter', 'Service Categories', 'monitor/services/categories', 'itsmmonitor-services-write', 'page'),
	('servicenter', 'New Category', 'monitor/services/categories/new', 'itsmmonitor-services-write', 'page'),
	('servicenter', 'Edit Category', 'monitor/services/categories/edit', 'itsmmonitor-services-write', 'page'),
	('servicenter', 'Delete Category', 'monitor/services/categories/delete', 'itsmmonitor-services-write', 'page');

-- Page Parents

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'monitor') AS x) WHERE url = 'monitor/services';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'monitor/services') AS x) WHERE url = 'monitor/services/api';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'monitor/services') AS x) WHERE url = 'monitor/services/categories';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'monitor/services/categories') AS x) WHERE url = 'monitor/services/categories/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'monitor/services/categories') AS x) WHERE url = 'monitor/services/categories/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'monitor/services/categories') AS x) WHERE url = 'monitor/services/categories/delete';