-- ISD-FASTAPPS Platform
-- Core Tables

CREATE TABLE User (
	id INT(11) NOT NULL AUTO_INCREMENT,
	username VARCHAR(64) NOT NULL UNIQUE,
	firstName VARCHAR(30) NOT NULL,
	lastName VARCHAR(30) NOT NULL,
	email TEXT NOT NULL,
	password CHAR(128) DEFAULT NULL,
	disabled TINYINT(1) NOT NULL DEFAULT '0',
	authType ENUM('loca', 'ldap') NOT NULL DEFAULT 'loca',
	PRIMARY KEY (id)
);

CREATE TABLE Role (
	id INT(11) NOT NULL AUTO_INCREMENT,
	name VARCHAR(64) NOT NULL UNIQUE,
	PRIMARY KEY (id)
);

CREATE TABLE User_Role (
	user INT(11) NOT NULL,
	role INT(11) NOT NULL,
	PRIMARY KEY (user, role),
	FOREIGN KEY (user) REFERENCES User(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (role) REFERENCES Role(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE Token (
	token CHAR(128) NOT NULL,
	user INT(11) NOT NULL,
	issueTime DATETIME NOT NULL,
	expireTime DATETIME NOT NULL,
	expired TINYINT(1) NOT NULL DEFAULT '0',
	ipAddress VARCHAR(39) NOT NULL,
	PRIMARY KEY (token),
	FOREIGN KEY (user) REFERENCES User(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE Permission (
	code VARCHAR(30) NOT NULL UNIQUE,
	PRIMARY KEY (code)
);

CREATE TABLE Role_Permission (
	role INT(11) NOT NULL,
	permission VARCHAR(30) NOT NULL,
	PRIMARY KEY (role, permission),
	FOREIGN KEY (role) REFERENCES Role(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (permission) REFERENCES Permission(code) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE Page (
	id INT(11) NOT NULL AUTO_INCREMENT,
	extension VARCHAR(64) NOT NULL,
	title VARCHAR(30) NOT NULL,
	url VARCHAR(64) NOT NULL UNIQUE,
	permission VARCHAR(30) NOT NULL,
	type ENUM('sect', 'page', 'link') NOT NULL,
	parent INT(11) DEFAULT NULL,
	weight INT(11) DEFAULT 0,
	icon TEXT DEFAULT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (permission) REFERENCES Permission(code) ON UPDATE CASCADE,
	FOREIGN KEY (parent) REFERENCES Page(id) ON UPDATE CASCADE
);

CREATE TABLE Attribute (
	id INT(11) NOT NULL AUTO_INCREMENT,
	extension CHAR(4) NOT NULL DEFAULT 'core',
	type CHAR(4) NOT NULL,
	code CHAR(4) NOT NULL,
	name VARCHAR(30) NOT NULL,
	PRIMARY KEY (id),
	UNIQUE KEY (extension, type, code)
);

CREATE TABLE Notification (
	id INT(11) NOT NULL AUTO_INCREMENT,
	user INT(11) NOT NULL,
	title VARCHAR(64) NOT NULL,
	data TEXT NOT NULL,
	`read` TINYINT(1) NOT NULL DEFAULT 0,
	deleted TINYINT(1) NOT NULL DEFAULT 0,
	important TINYINT(1) NOT NULL DEFAULT 0,
	time DATETIME NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (user) REFERENCES User(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE PageLastVisit (
	user INT(11) NOT NULL,
	page INT(11) NOT NULL,
	getVars TEXT NOT NULL,
	PRIMARY KEY(user, page),
	FOREIGN KEY (user) REFERENCES User(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (page) REFERENCES Page(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE Bulletin (
	id INT(11) NOT NULL AUTO_INCREMENT,
	user INT(11) DEFAULT NULL,
	startDate DATE NOT NULL,
	endDate DATE NOT NULL,
	title TEXT NOT NULL,
	message TEXT NOT NULL,
	inactive TINYINT(1) NOT NULL DEFAULT 0,
	`type` ENUM('i', 'a') NOT NULL DEFAULT 'i',
	PRIMARY KEY (id),
	FOREIGN KEY (user) REFERENCES User(id) ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE Role_Bulletin (
	role INT(11) NOT NULL,
	bulletin INT(11) NOT NULL,
	PRIMARY KEY (role, bulletin),
	FOREIGN KEY (role) REFERENCES Role(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (bulletin) REFERENCES Bulletin(id) ON UPDATE CASCADE ON DELETE CASCADE
);

-- Default data

INSERT INTO Permission VALUES ('home-use'), ('settings-use');

INSERT INTO Role VALUES (1, 'System Operator'), (2, 'User');

INSERT INTO Role_Permission VALUES (1, 'home-use'), (1, 'settings-use'), (2, 'home-use');
	
INSERT INTO Page (extension, title, url, permission, type) VALUES 
	('core', 'Home', 'home', 'home-use', 'sect'),
	('core', 'Homepage', 'home/homepage', 'home-use', 'page'),
	('core', 'My Account', 'home/myaccount', 'home-use', 'page'),
	('core', 'Change Account Details', 'home/myaccount/editdetails', 'home-use', 'page'),
	('core', 'Change Password', 'home/myaccount/changepassword', 'home-use', 'page'),
	('core', 'Inbox', 'home/inbox', 'home-use', 'page'),
	('core', 'View Notification', 'home/inbox/view', 'home-use', 'page'),
	('core', 'Delete Notification', 'home/inbox/delete', 'home-use', 'page'),
	('core', 'Old Notifications', 'home/inbox/old', 'home-use', 'page'),
	('core', 'About', 'home/about', 'home-use', 'page'),
	('core', 'Settings', 'settings', 'settings-use', 'sect'),
	('core', 'Users', 'settings/users', 'settings-use', 'page'),
	('core', 'User Logs', 'settings/userlogs', 'settings-use', 'page'),
	('core', 'Add User', 'settings/users/add', 'settings-use', 'page'),
	('core', 'Edit User', 'settings/users/edit', 'settings-use', 'page'),
	('core', 'Roles', 'settings/roles', 'settings-use', 'page'),
	('core', 'Add Role', 'settings/roles/add', 'settings-use', 'page'),
	('core', 'Edit Role', 'settings/roles/edit', 'settings-use', 'page'), 
	('core', 'Delete Role', 'settings/roles/delete', 'settings-use', 'page'),
	('core', 'Notifications', 'settings/notifications', 'settings-use', 'page'),
	('core', 'Send Notification', 'settings/notifications/send', 'settings-use', 'page'),
	('core', 'Bulletins', 'settings/bulletins', 'settings-use', 'page'),
	('core', 'New Bulletin', 'settings/bulletins/new', 'settings-use', 'page'),
	('core', 'Edit Bulletin', 'settings/bulletins/edit', 'settings-use', 'page');
	
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'home') AS x) WHERE url = 'home/homepage';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'home') AS x) WHERE url = 'home/about';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'home') AS x) WHERE url = 'home/myaccount';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'home/myaccount') AS x) WHERE url = 'home/myaccount/editdetails';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'home/myaccount') AS x) WHERE url = 'home/myaccount/changepassword';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'home') AS x) WHERE url = 'home/inbox';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'home/inbox') AS x) WHERE url = 'home/inbox/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'home/inbox') AS x) WHERE url = 'home/inbox/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'home/inbox') AS x) WHERE url = 'home/inbox/delete';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'home/inbox') AS x) WHERE url = 'home/inbox/old';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'settings') AS x) WHERE url = 'settings/users';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'settings/users') AS x) WHERE url = 'settings/users/add';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'settings/users') AS x) WHERE url = 'settings/users/edit';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'settings') AS x) WHERE url = 'settings/userlogs';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'settings') AS x) WHERE url = 'settings/roles';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'settings/roles') AS x) WHERE url = 'settings/roles/add';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'settings/roles') AS x) WHERE url = 'settings/roles/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'settings/roles') AS x) WHERE url = 'settings/roles/delete';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'settings') AS x) WHERE url = 'settings/notifications';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'settings/notifications') AS x) WHERE url = 'settings/notifications/send';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'settings') AS x) WHERE url = 'settings/bulletins';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'settings/bulletins') AS x) WHERE url = 'settings/bulletins/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'settings/bulletins') AS x) WHERE url = 'settings/bulletins/edit';

INSERT INTO User (username, firstName, lastName, password) VALUES ('isdadmin', 'Built-In', 'Administrator', '5df1e24de41f7d214600d6291c7815746df817fceb129d5d0097fb779771a166f85c5d068b145a9cfa04958afbd9640228d7c6db1459958f51fa4904993dd008');
INSERT INTO User_Role VALUES ((SELECT id FROM User WHERE username = 'isdadmin'), 1);