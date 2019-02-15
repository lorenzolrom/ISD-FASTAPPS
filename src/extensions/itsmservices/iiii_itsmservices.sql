-- ISD-FASTAPPS
-- ITSM Application Inventory & Tracking Extension
-- Database Initialization

-- Tables

CREATE TABLE ITSM_Application (
	id INT(11) NOT NULL AUTO_INCREMENT,
	number INT(11) NOT NULL UNIQUE,
	name VARCHAR(64) NOT NULL,
	description TEXT NOT NULL,
	owner INT(11) NOT NULL,
	type INT(11) NOT NULL,
	status INT(11) NOT NULL,
	publicFacing TINYINT(1) NOT NULL DEFAULT 0,
	lifeExpectancy INT(11) NOT NULL,
	dataVolume INT(11) NOT NULL,
	authType INT(11) NOT NULL,
	port VARCHAR(5) NOT NULL,
	createUser INT(11) NOT NULL,
	createDate DATE NOT NULL,
	lastModifyUser INT(11) NOT NULL,
	lastModifyDate DATE NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY (type) REFERENCES Attribute(id) ON UPDATE CASCADE,
	FOREIGN KEY (status) REFERENCES Attribute(id) ON UPDATE CASCADE,
	FOREIGN KEY (owner) REFERENCES User(id) ON UPDATE CASCADE,
	FOREIGN KEY (lifeExpectancy) REFERENCES Attribute(id) ON UPDATE CASCADE,
	FOREIGN KEY (dataVolume) REFERENCES Attribute(id) ON UPDATE CASCADE,
	FOREIGN KEY (authType) REFERENCES Attribute(id) ON UPDATE CASCADE,
	FOREIGN KEY (createUser) REFERENCES User(id) ON UPDATE CASCADE,
	FOREIGN KEY (lastModifyUser) REFERENCES User(id) ON UPDATE CASCADE
);

CREATE TABLE ITSM_Application_Host(
	application INT(11) NOT NULL,
	host INT(11) NOT NULL,
	relationship CHAR(4) NOT NULL,
	PRIMARY KEY (application, host, relationship),
	FOREIGN KEY (application) REFERENCES ITSM_Application(id) ON UPDATE CASCADE,
	FOREIGN KEY (host) REFERENCES ITSM_Host(id) ON UPDATE CASCADE
);

CREATE TABLE ITSM_Application_VHost(
	application INT(11) NOT NULL,
	vhost INT(11) NOT NULL,
	PRIMARY KEY (application, vhost),
	FOREIGN KEY (application) REFERENCES ITSM_Application(id) ON UPDATE CASCADE,
	FOREIGN KEY (vhost) REFERENCES ITSM_VHost(id) ON UPDATE CASCADE
);

CREATE TABLE ITSM_ApplicationUpdate(
	id INT(11) NOT NULL AUTO_INCREMENT,
	application INT(11) NOT NULL,
	status INT(11) NOT NULL,
	time DATETIME NOT NULL,
	user INT(11) NOT NULL,
	description TEXT NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY (application) REFERENCES ITSM_Application(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (user) REFERENCES User(id) ON UPDATE CASCADE
);

-- Attributes

INSERT INTO Attribute (extension, extension, `type`, code, name) VALUES
	('itsm', 'aitt', 'infr', 'Infrastructure Service'),
	('itsm', 'aitt', 'weba', 'Web Application'),
	('itsm', 'aitt', 'osap', 'O.S. Application'),
	('itsm', 'aitd', 'lt1g', '<1 GB'),
	('itsm', 'aitd', '110g', '1-10 GB'),
	('itsm', 'aitd', '1150', '11-50 GB'),
	('itsm', 'aitd', '5110', '51-100 GB'),
	('itsm', 'aitd', '1015', '101-500 GB'),
	('itsm', 'aitd', '501t', '501 GB - 1 TB'),
	('itsm', 'aitd', 'gt1t', '>1 TB'),
	('itsm', 'aitl', '1t5y', '1-5 Years'),
	('itsm', 'aitl', '5t10', '5-10 Years'),
	('itsm', 'aitl', 'gt10', '>10 Years'),
	('itsm', 'aita', 'loca', 'Local D.B.'),
	('itsm', 'aita', 'extn', 'External D.B.'),
	('itsm', 'aita', 'ldap', 'LDAP'),
	('itsm', 'aita', 'sson', 'Single Sign-On'),
	('itsm', 'aita', 'none', 'None'),
	('itsm', 'aits', 'addd', 'Added'),
	('itsm', 'aits', 'inde', 'In Development'),
	('itsm', 'aits', 'pror', 'Project Rejected'),
	('itsm', 'aits', 'inpr', 'In Production'),
	('itsm', 'aits', 'prjr', 'Projected Retire'),
	('itsm', 'aits', 'read', 'Retain App & Data'),
	('itsm', 'aits', 'rdao', 'Retain Data Only'),
	('itsm', 'aits', 'reti', 'Retired'),
	('itsm', 'aits', 'deco', 'Decomissioned');

-- Permissions

INSERT INTO Permission VALUES 
	('itsm_srv-use'),
	('itsm_srv-apps'),
	('itsm_srv-apps-write');
	
-- Pages

INSERT INTO Page (extension, title, url, permission, type) VALUES
	('itsmservices', 'Services', 'services', 'itsm_srv-use', 'sect'),
	('itsmservices', 'Applications', 'services/applications', 'itsm_srv-apps', 'page'),
	('itsmservices', 'View Application', 'services/applications/view', 'itsm_srv-apps', 'page'),
	('itsmservices', 'New Application', 'services/applications/new', 'itsm_srv-apps-write', 'page'),
	('itsmservices', 'Edit Application', 'services/applications/edit', 'itsm_srv-apps-write', 'page'),
	('itsmservices', 'Delete Application', 'services/applications/delete', 'itsm_srv-apps-write', 'page'),
	('itsmservices', 'Update Application', 'services/applications/update', 'itsm_srv-apps-write', 'page');
	
-- Page Parents

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'services') AS x) WHERE url = 'services/applications';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'services/applications') AS x) WHERE url = 'services/applications/update';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'services/applications') AS x) WHERE url = 'services/applications/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'services/applications') AS x) WHERE url = 'services/applications/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'services/applications') AS x) WHERE url = 'services/applications/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'services/applications') AS x) WHERE url = 'services/applications/delete';