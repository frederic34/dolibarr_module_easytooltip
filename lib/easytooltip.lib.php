<?php
/* Copyright (C) 2023 Frédéric France
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    easytooltip/lib/easytooltip.lib.php
 * \ingroup easytooltip
 * \brief   Library files with common functions for EasyTooltip
 */

/**
 * Prepare admin pages header
 *
 * @return array
 */
function easytooltipAdminPrepareHead()
{
	global $conf, $db, $langs;

	$langs->load("easytooltip@easytooltip");

	$h = 0;
	$head = [];

	$head[$h][0] = dol_buildpath("/easytooltip/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;
	$sql = 'SELECT rowid, name, value FROM ' . MAIN_DB_PREFIX . 'const WHERE name LIKE "EASYTOOLTIP_%_' . $conf->entity . '_%" ORDER by name';

	$resql = $db->query($sql);
	$modules = [];
	while ($resql && $obj = $db->fetch_object($resql)) {
		$names = explode('_'.$conf->entity.'_', $obj->name);
		$name = str_replace('EASYTOOLTIP_', '', $names[0]);
		if (!isset($modules[$name])) {
			$modules[$name] = 0;
		}
		$modules[$name]++;
	}
	foreach ($modules as $key => $value) {
		$head[$h][0] = dol_buildpath("/easytooltip/admin/setup.php?module=" . $key, 1);
		$head[$h][1] = $langs->trans($key);
		if ($value > 0) {
			$head[$h][1] .= ' <span class="badge">' . $value . '</span>';
		}
		$head[$h][2] = 'settings_' . $key;
		$h++;
	}
	/*
	$head[$h][0] = dol_buildpath("/easytooltip/admin/myobject_extrafields.php", 1);
	$head[$h][1] = $langs->trans("ExtraFields");
	$nbExtrafields = is_countable($extrafields->attributes['myobject']['label']) ? count($extrafields->attributes['myobject']['label']) : 0;
	if ($nbExtrafields > 0) {
		$head[$h][1] .= ' <span class="badge">' . $nbExtrafields . '</span>';
	}
	$head[$h][2] = 'myobject_extrafields';
	$h++;
	*/

	$head[$h][0] = dol_buildpath("/easytooltip/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@easytooltip:/easytooltip/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@easytooltip:/easytooltip/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'easytooltip@easytooltip');

	complete_head_from_modules($conf, $langs, null, $head, $h, 'easytooltip@easytooltip', 'remove');

	return $head;
}
