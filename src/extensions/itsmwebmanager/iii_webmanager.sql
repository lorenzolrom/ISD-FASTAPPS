-- ISD-FASTAPPS
-- ITSM WebManager Extension
-- Database Initialization

-- Tables

CREATE TABLE ITSM_Registrar (
	id INT(11) NOT NULL AUTO_INCREMENT,
	code VARCHAR(32) UNIQUE,
	name TEXT NOT NULL,
	url TEXT NOT NULL,
	phone VARCHAR(20) NOT NULL,
	createDate DATE NOT NULL,
	createUser INT(11) NOT NULL,
	lastModifyDate DATE NOT NULL,
	lastModifyUser INT(11) NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (createUser) REFERENCES User(id) ON UPDATE CASCADE,
	FOREIGN KEY (lastModifyUser) REFERENCES User(id) ON UPDATE CASCADE
);

CREATE TABLE ITSM_VHost (
	id INT(11) NOT NULL AUTO_INCREMENT,
	`domain` TEXT NOT NULL,
	subdomain TEXT NOT NULL,
	name VARCHAR(64) NOT NULL,
	host INT(11) NOT NULL,
	registrar INT(11) NOT NULL,
	status INT(11) NOT NULL,
	renewCost FLOAT(11,2) NOT NULL,
	notes TEXT DEFAULT NULL,
	registerDate DATE NOT NULL,
	expireDate DATE DEFAULT NULL,
	createDate DATE NOT NULL,
	createUser INT(11) NOT NULL,
	modifyDate DATE NOT NULL,
	modifyUser INT(11) NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (host) REFERENCES ITSM_Host(id) ON UPDATE CASCADE,
	FOREIGN KEY (registrar) REFERENCES ITSM_Registrar(id) ON UPDATE CASCADE,
	FOREIGN KEY (status) REFERENCES Attribute(id) ON UPDATE CASCADE,
	FOREIGN KEY (createUser) REFERENCES User(id) ON UPDATE CASCADE,
	FOREIGN KEY (modifyUser) REFERENCES User(id) ON UPDATE CASCADE
);

-- Attributes

INSERT INTO Attribute (extension, `type`, code, name) VALUES
	('itsm', 'wdns', 'acti', 'Active'),
	('itsm', 'wdns', 'redi', 'Redirected'),
	('itsm', 'wdns', 'dorm', 'Dormant'),
	('itsm', 'wdns', 'expi', 'Expired');

-- Permissions

INSERT INTO Permission VALUES 
	('itsm_webman-use'),
	('itsm_webman-vhosts'),
	('itsm_webman-vhosts-write'),
	('itsm_webman-registrars'),
	('itsm_webman-registrars-write');
	
-- Pages

INSERT INTO Page (extension, title, url, permission, type) VALUES
	('itsmwebmanager', 'Web', 'webmanager', 'itsm_webman-use', 'sect'),
	('itsmwebmanager', 'VHosts', 'webmanager/vhosts', 'itsm_webman-vhosts', 'page'),
	('itsmwebmanager', 'VHost A.P.I.', 'webmanager/vhosts/api', 'itsm_webman-vhosts-write', 'page'),
	('itsmwebmanager', 'View VHost', 'webmanager/vhosts/view', 'itsm_webman-vhosts', 'page'),
	('itsmwebmanager', 'Edit VHost', 'webmanager/vhosts/edit', 'itsm_webman-vhosts-write', 'page'),
	('itsmwebmanager', 'New VHost', 'webmanager/vhosts/new', 'itsm_webman-vhosts-write', 'page'),
	('itsmwebmanager', 'Delete VHost', 'webmanager/vhosts/delete', 'itsm_webman-vhosts-write', 'page'),
	('itsmwebmanager', 'Registrars', 'webmanager/registrars', 'itsm_webman-registrars', 'page'),
	('itsmwebmanager', 'View Registrar', 'webmanager/registrars/view', 'itsm_webman-registrars', 'page'),
	('itsmwebmanager', 'Edit Registrar', 'webmanager/registrars/edit', 'itsm_webman-registrars-write', 'page'),
	('itsmwebmanager', 'New Registrar', 'webmanager/registrars/new', 'itsm_webman-registrars-write', 'page'),
	('itsmwebmanager', 'Delete Registrar', 'webmanager/registrars/delete', 'itsm_webman-registrars-write', 'page');

-- Page Parents

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager') AS x) WHERE url = 'webmanager/vhosts';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager/vhosts') AS x) WHERE url = 'webmanager/vhosts/api';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager/vhosts') AS x) WHERE url = 'webmanager/vhosts/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager/vhosts') AS x) WHERE url = 'webmanager/vhosts/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager/vhosts') AS x) WHERE url = 'webmanager/vhosts/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager/vhosts') AS x) WHERE url = 'webmanager/vhosts/delete';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager') AS x) WHERE url = 'webmanager/registrars';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager/registrars') AS x) WHERE url = 'webmanager/registrars/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager/registrars') AS x) WHERE url = 'webmanager/registrars/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager/registrars') AS x) WHERE url = 'webmanager/registrars/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'webmanager/registrars') AS x) WHERE url = 'webmanager/registrars/delete';