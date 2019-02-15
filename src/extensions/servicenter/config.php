<?php
	/////
	// ISD-FASTAPPS
	// SERVICENTER EXTENSION
	// CONFIGURATION FILE
	/////
	
	// Custom queries for ServiCenter Widgets
	// Array in format ['title', [options]]
	$SERVICENTER_WIDGETS = [['My Assignments', ['assi' => 'self', 'nsta' => ['clos']]],
							['Open Severity 1 & 2 Tickets', ['seve' => ['sev1', 'sev2'], 'nsta' => ['clos']]],
							['Open Planning Tickets', ['seve' => ['plan'], 'nsta' => ['clos']]], 
							['Open Incidents', ['type' => ['inci'], 'nsta' => ['clos']]],
							['Open Emergency Patches', ['type' => ['empa'], 'nsta' => ['clos']]],
							['Approved Tickets', ['tsta' => ['appr']]],
							['Tickets Pending Review', ['tsta' => ['pere']]]];
	
	// Number of results to show per widget
	$SERVICENTER_WIDGET_LIMIT = 10;
