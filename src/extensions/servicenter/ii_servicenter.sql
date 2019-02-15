-- ISD-FASTAPPS
-- ServiCenter Extension
-- Database Initialization

-- Tables

CREATE TABLE ServiCenter_Workspace(
	id INT(11) NOT NULL AUTO_INCREMENT,
	name VARCHAR(64) NOT NULL UNIQUE,
	`default` TINYINT(1) NOT NULL DEFAULT 0,
	priorityLevels INT(11) NOT NULL DEFAULT 0,
	scaleLevels INT(11) NOT NULL DEFAULT 0,
	widgetCount INT(11) NOT NULL DEFAULT 3,
	PRIMARY KEY (id)
);

CREATE TABLE ServiCenter_TicketAttribute(
	id INT(11) NOT NULL AUTO_INCREMENT,
	workspace INT(11) NOT NULL,
	type CHAR(4) NOT NULL,
	code CHAR(4) NOT NULL,
	name VARCHAR(30) NOT NULL,
	`default` TINYINT(1) NOT NULL DEFAULT 0,
	PRIMARY KEY(id),
	UNIQUE KEY extension (workspace, type, code),
	FOREIGN KEY (workspace) REFERENCES ServiCenter_Workspace(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE ServiCenter_Ticket(
	id INT(11) NOT NULL AUTO_INCREMENT,
	workspace INT(11) NOT NULL,
	`number` INT(11) NOT NULL,
	title VARCHAR(64) NOT NULL,
	contact INT(11) DEFAULT NULL,
	severity INT(11) NOT NULL,
	priority INT(11) NOT NULL DEFAULT 0,
	scale INT(11) NOT NULL DEFAULT 0,
	type INT(11) NOT NULL,
	category INT(11) NOT NULL,
	source INT(11) NOT NULL,
	status CHAR(4) NOT NULL,
	closureCode CHAR(4) DEFAULT NULL,
	desiredDueDate DATE DEFAULT NULL,
	nextReviewDate DATE DEFAULT NULL,
	workScheduleDate DATE DEFAULT NULL,
	targetDate DATE DEFAULT NULL,
	vendorInfo TEXT NOT NULL,
	location TEXT NOT NULL,
	createUser INT(11) NOT NULL,
	createDate DATETIME NOT NULL,
	PRIMARY KEY (id),
	UNIQUE KEY `number` (workspace, `number`),
	FOREIGN KEY (workspace) REFERENCES ServiCenter_Workspace(id) ON UPDATE CASCADE,
	FOREIGN KEY (contact) REFERENCES User(id) ON UPDATE CASCADE,
	FOREIGN KEY (severity) REFERENCES ServiCenter_TicketAttribute(id) ON UPDATE CASCADE,
	FOREIGN KEY (type) REFERENCES ServiCenter_TicketAttribute(id) ON UPDATE CASCADE,
	FOREIGN KEY (category) REFERENCES ServiCenter_TicketAttribute(id) ON UPDATE CASCADE,
	FOREIGN KEY (source) REFERENCES Attribute(id) ON UPDATE CASCADE,
	FOREIGN KEY (createUser) REFERENCES User(id) ON UPDATE CASCADE
);

CREATE TABLE ServiCenter_TicketDetail (
	id INT(11) NOT NULL AUTO_INCREMENT,
	ticket INT(11) NOT NULL,
	type ENUM('u', 'l', 'i') NOT NULL,
	user INT(11) NOT NULL,
	`date` DATETIME NOT NULL,
	`data` TEXT NOT NULL,
	seconds INT(11) DEFAULT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (ticket) REFERENCES ServiCenter_Ticket(id) ON UPDATE CASCADE,
	FOREIGN KEY (user) REFERENCES User(id) ON UPDATE CASCADE
);

CREATE TABLE ServiCenter_TicketLink(
	ticket1 INT(11) NOT NULL,
	ticket2 INT(11) NOT NULL,
	linkType ENUM('s', 'd') NOT NULL,
	PRIMARY KEY (ticket1, ticket2),
	FOREIGN KEY (ticket1) REFERENCES ServiCenter_Ticket(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (ticket2) REFERENCES ServiCenter_Ticket(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE ServiCenter_Team(
	id INT(11) NOT NULL AUTO_INCREMENT,
	name VARCHAR(64) NOT NULL UNIQUE,
	PRIMARY KEY (id)
);

CREATE TABLE ServiCenter_Team_User(
	team INT(11) NOT NULL,
	user INT(11) NOT NULL,
	PRIMARY KEY (team, user),
	FOREIGN KEY (team) REFERENCES ServiCenter_Team(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (user) REFERENCES User(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE ServiCenter_Team_Workspace(
	team INT(11) NOT NULL,
	workspace INT(11) NOT NULL,
	PRIMARY KEY (team, workspace),
	FOREIGN KEY (team) REFERENCES ServiCenter_Team(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (workspace) REFERENCES ServiCenter_Workspace(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE ServiCenter_Ticket_Assignee(
	ticket INT(11) NOT NULL,
	team INT(11) DEFAULT NULL,
	user INT(11) DEFAULT NULL,
	FOREIGN KEY (ticket) REFERENCES ServiCenter_Ticket(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (team) REFERENCES ServiCenter_Team(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (user) REFERENCES User(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE ServiCenter_Workspace_Widget (
	workspace INT(11) NOT NULL,
	position INT(11) NOT NULL,
	widget INT(11) NOT NULL,
	PRIMARY KEY (workspace, position),
	FOREIGN KEY (workspace) REFERENCES ServiCenter_Workspace(id) ON UPDATE CASCADE ON DELETE CASCADE);

-- Permissions

INSERT INTO Permission VALUES
	('servicenter'),
	('servicenter-requests'),
	('servicenter-desk'),
	('servicenter-desk-write'),
	('servicenter-admin');

-- Attributes (Global)

INSERT INTO Attribute (extension, `type`, code, name) VALUES
	('srvc', 'tsta', 'new', 'New'),
	('srvc', 'tsta', 'wip', 'Work In Progress'),
	('srvc', 'tsta', 'wsch', 'Work Scheduled'),
	('srvc', 'tsta', 'pcus', 'Pending Customer'),
	('srvc', 'tsta', 'cusr', 'Customer Responded'),
	('srvc', 'tsta', 'clos', 'Closed'),
	('srvc', 'tsta', 'reop', 'Re-Opened'),
	('srvc', 'tclc', 'cmpl', 'Completed Successfully'),
	('srvc', 'tclc', 'cusc', 'Customer Cancelled'),
	('srvc', 'tclc', 'cuhr', 'Customer Hasn\'t Responded'),
	('srvc', 'tclc', 'dupl', 'Duplicate Ticket'),
	('srvc', 'tclc', 'info', 'Information Provided'),
	('srvc', 'tclc', 'notr', 'Not A Request'),
	('srvc', 'tclc', 'nffd', 'No Fault Found'),
	('srvc', 'tsrc', 'emai', 'Email'),
	('srvc', 'tsrc', 'faxm', 'Fax'),
	('srvc', 'tsrc', 'inpe', 'In Person'),
	('srvc', 'tsrc', 'phon', 'Phone'),
	('srvc', 'tsrc', 'self', 'Self'),
	('srvc', 'tsrc', 'unsp', 'Unspecified'),
	('srvc', 'tsrc', 'inte', 'Internet'),
	('srvc', 'tsrc', 'othe', 'Other');

-- Pages

INSERT INTO Page (extension, title, url, permission, type) VALUES
	('servicenter', 'Tickets', 'servicenter', 'servicenter', 'sect'),
	('servicenter', 'Requests', 'servicenter/requests', 'servicenter-requests', 'page'),
	('servicenter', 'New Request', 'servicenter/requests/new', 'servicenter-requests', 'page'),
	('servicenter', 'View Request', 'servicenter/requests/view', 'servicenter-requests', 'page'),
	('servicenter', 'Update Request', 'servicenter/requests/update', 'servicenter-requests', 'page'),
	('servicenter', 'Service Desk', 'servicenter/desk', 'servicenter-desk', 'page'),
	('servicenter', 'Search Tickets', 'servicenter/desk/search', 'servicenter-desk', 'page'),
	('servicenter', 'New Ticket', 'servicenter/desk/new', 'servicenter-desk-write', 'page'),
	('servicenter', 'View Ticket', 'servicenter/desk/view', 'servicenter-desk', 'page'),
	('servicenter', 'Edit Ticket', 'servicenter/desk/edit', 'servicenter-desk-write', 'page'),
	('servicenter', 'Link Ticket', 'servicenter/desk/link', 'servicenter-desk-write', 'page'),
	('servicenter', 'Assign Contact', 'servicenter/desk/contact', 'servicenter-desk-write', 'page'),
	('servicenter', 'Workspaces', 'servicenter/workspaces', 'servicenter-admin', 'page'),
	('servicenter', 'New Workspace', 'servicenter/workspaces/new', 'servicenter-admin', 'page'),
	('servicenter', 'View Workspace', 'servicenter/workspaces/view', 'servicenter-admin', 'page'),
	('servicenter', 'Edit Workspace', 'servicenter/workspaces/edit', 'servicenter-admin', 'page'),
	('servicenter', 'Workspace Teams', 'servicenter/workspaces/teams', 'servicenter-admin', 'page'),
	('servicenter', 'Workspace Settings', 'servicenter/workspaces/settings', 'servicenter-admin', 'page'),
	('servicenter', 'Attributes', 'servicenter/workspaces/settings/attributes', 'servicenter-admin', 'page'),
	('servicenter', 'Scales', 'servicenter/workspaces/settings/scales', 'servicenter-admin', 'page'),
	('servicenter', 'Widgets', 'servicenter/workspaces/settings/widgets', 'servicenter-admin', 'page'),
	('servicenter', 'Teams', 'servicenter/teams', 'servicenter-admin', 'page'),
	('servicenter', 'New Team', 'servicenter/teams/new', 'servicenter-admin', 'page'),
	('servicenter', 'Edit Team', 'servicenter/teams/edit', 'servicenter-admin', 'page'),
	('servicenter', 'View Team', 'servicenter/teams/view', 'servicenter-admin', 'page'),
	('servicenter', 'Delete Team', 'servicenter/teams/delete', 'servicenter-admin', 'page'),
	('servicenter', 'Team Members', 'servicenter/teams/members', 'servicenter-admin', 'page');

-- Permissions
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter') AS x) WHERE url = 'servicenter/requests';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/requests') AS x) WHERE url = 'servicenter/requests/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/requests') AS x) WHERE url = 'servicenter/requests/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/requests') AS x) WHERE url = 'servicenter/requests/update';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter') AS x) WHERE url = 'servicenter/desk';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/desk') AS x) WHERE url = 'servicenter/desk/search';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/desk') AS x) WHERE url = 'servicenter/desk/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/desk') AS x) WHERE url = 'servicenter/desk/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/desk') AS x) WHERE url = 'servicenter/desk/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/desk') AS x) WHERE url = 'servicenter/desk/link';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/desk') AS x) WHERE url = 'servicenter/desk/contact';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter') AS x) WHERE url = 'servicenter/workspaces';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/workspaces') AS x) WHERE url = 'servicenter/workspaces/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/workspaces') AS x) WHERE url = 'servicenter/workspaces/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/workspaces') AS x) WHERE url = 'servicenter/workspaces/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/workspaces') AS x) WHERE url = 'servicenter/workspaces/teams';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/workspaces') AS x) WHERE url = 'servicenter/workspaces/settings';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/workspaces/settings') AS x) WHERE url = 'servicenter/workspaces/settings/attributes';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/workspaces/settings') AS x) WHERE url = 'servicenter/workspaces/settings/scales';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/workspaces/settings') AS x) WHERE url = 'servicenter/workspaces/settings/widgets';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter') AS x) WHERE url = 'servicenter/teams';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/teams') AS x) WHERE url = 'servicenter/teams/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/teams') AS x) WHERE url = 'servicenter/teams/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/teams') AS x) WHERE url = 'servicenter/teams/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/teams') AS x) WHERE url = 'servicenter/teams/delete';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'servicenter/teams') AS x) WHERE url = 'servicenter/teams/members';