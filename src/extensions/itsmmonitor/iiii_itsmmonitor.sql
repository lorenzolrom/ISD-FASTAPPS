-- ISD-FASTAPPS
-- ITSM Monitor Extension
-- Database Initialization

CREATE TABLE ITSM_HostCategory(
	id INT(11) NOT NULL AUTO_INCREMENT,
	name VARCHAR(64) NOT NULL UNIQUE,
	displayed TINYINT(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (id)
);

CREATE TABLE ITSM_Host_HostCategory(
	`host` INT(11) NOT NULL,
	category INT(11) NOT NULL,
	PRIMARY KEY (`host`, category),
	FOREIGN KEY (`host`) REFERENCES ITSM_Host(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (category) REFERENCES ITSM_HostCategory(id) ON UPDATE CASCADE ON DELETE CASCADE
);

--Permissions

INSERT INTO Permission VALUES
	('itsmmonitor'),
	('itsmmonitor-hosts'),
	('itsmmonitor-hosts-write');

-- Pages

INSERT INTO Page (extension, title, url, permission, type) VALUES
	('itsmmonitor', 'Monitor', 'monitor', 'itsmmonitor', 'sect'),
	('itsmmonitor', 'Host Monitor', 'monitor/hosts', 'itsmmonitor-hosts', 'page'),
	('itsmmonitor', 'Host Monitor A.P.I.', 'monitor/hosts/api', 'itsmmonitor-hosts', 'page'),
	('itsmmonitor', 'Host Categories', 'monitor/hosts/categories', 'itsmmonitor-hosts-write', 'page'),
	('itsmmonitor', 'New Category', 'monitor/hosts/categories/new', 'itsmmonitor-hosts-write', 'page'),
	('itsmmonitor', 'Edit Category', 'monitor/hosts/categories/edit', 'itsmmonitor-hosts-write', 'page'),
	('itsmmonitor', 'Delete Category', 'monitor/hosts/categories/delete', 'itsmmonitor-hosts-write', 'page');

-- Page Parents

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'monitor') AS x) WHERE url = 'monitor/hosts';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'monitor/hosts') AS x) WHERE url = 'monitor/hosts/api';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'monitor/hosts') AS x) WHERE url = 'monitor/hosts/categories';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'monitor/hosts/categories') AS x) WHERE url = 'monitor/hosts/categories/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'monitor/hosts/categories') AS x) WHERE url = 'monitor/hosts/categories/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'monitor/hosts/categories') AS x) WHERE url = 'monitor/hosts/categories/delete';