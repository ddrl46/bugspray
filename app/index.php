<?php
/**
 * bugspray issue tracking software
 * Copyright (c) 2009-2010 a2h - http://a2h.uni.cc/
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * Under section 7b of the GNU General Public License you are
 * required to preserve this notice. Additional attribution may be
 * found in the NOTICES.txt file provided with the Program.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

include("functions.php");
$page->setType('dashboard');
$page->setTitle('Dashboard');

// installer completion screen
if (isset($_GET['installerdone']) && is_dir('install'))
{
	$page->addCSS('install/installer.css');
	include("install/index.php");
}

// current status
$curstatus = $_GET['status'];

// restrict the status
$whereclause = '';
if ($curstatus != 'all')
{
	if (!isset($_GET['status']))
		$curstatus = 'open';
	
	$wherests = array();
	foreach (getstatuses() as $status)
	{
		if ($status['type'] == $curstatus)
		{
			$wherests[] = $status['id'];
		}
	}
	if (count($wherests) > 0)
	{
		$whereclause = 'WHERE (';
		$i = 0;
		foreach ($wherests as $st)
		{
			if ($i > 0)
				$whereclause .= ' OR';
			$whereclause .= ' issues.status = '.$st;
			$i++;
		}
		$whereclause .= ')';
	}
}

// status tabs
$status_tabs = array(
	array(
		'name' => 'Open',
		'url' => 'index.php',
		'sel' => $curstatus == 'open' ? true : false
	),
	array(
		'name' => 'Assigned',
		'url' => 'index.php?status=assigned',
		'sel' => $curstatus == 'assigned' ? true : false
	),
	array(
		'name' => 'Resolved',
		'url' => 'index.php?status=resolved',
		'sel' => $curstatus == 'resolved' ? true : false
	),
	array(
		'name' => 'Declined',
		'url' => 'index.php?status=declined',
		'sel' => $curstatus == 'declined' ? true : false
	),
	array(
		'name' => 'All',
		'url' => 'index.php?status=all',
		'sel' => $curstatus == 'all' ? true : false
	)
);

$page->setPage(
	'dashboard.php',
	array(
		'status_tabs' => $status_tabs
	)
);
?>