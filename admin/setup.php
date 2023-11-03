<?php
/* Copyright (C) 2004-2017  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2019-2023  Frédéric France         <frederic.france@netlogic.fr>
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
 * \file    htdocs/modulebuilder/template/admin/setup.php
 * \ingroup easytooltip
 * \brief   Easytooltip setup page.
 */

// Load Dolibarr environment
include '../config.php';

global $langs, $user;

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once '../lib/easytooltip.lib.php';

// Translations
$langs->loadLangs(["admin", "easytooltip@easytooltip"]);

// Access control
if (!$user->admin) {
	accessforbidden();
}

// Parameters
$action = GETPOST('action', 'aZ09');
$module = GETPOST('module', 'alphanohtml');
$backtopage = GETPOST('backtopage', 'alpha');

$arrayofparameters = [
	// 'EASYTOOLTIP_FILENAME' => [
	// 	'css' => 'minwidth500',
	// 	'default' => 'tableExport',
	// 	'type' => 'text',
	// 	'enabled' => 1,
	// ],
];
if ((int) DOL_VERSION > 17 && getDolGlobalString('MAIN_ENABLE_AJAX_TOOLTIP')) {
	// tweak dolibarr
	$arrayofparameters = array_merge(
		$arrayofparameters,
		[
			'MAX_EXTRAFIELDS_TO_SHOW_IN_TOOLTIP' => [
				'css' => 'minwidth500',
				'default' => '6',
				'type' => 'text',
				'enabled' => 1,
			],
		]
	);
}
$selected = !empty($module) ? $module : '%';
$sql = 'SELECT rowid, name, value FROM ' . MAIN_DB_PREFIX . 'const WHERE name LIKE "EASYTOOLTIP_' . $selected . '_' . $conf->entity . '_%" ORDER by name';

$resql = $db->query($sql);
$modules = [];
while ($resql && $obj = $db->fetch_object($resql)) {
	$modules[$obj->name] = $obj->name;
}
// Paramètres ON/OFF
$modules = array_merge(
	$modules,
	[
		// 'EASYTOOLTIP_ENABLE_CSV' => 'EasytooltipEnableCsvExport',
		'EASYTOOLTIP_ENABLE_DEVELOPPER_MODE' => 'EasytooltipEnableDevelopperMode',
		// tweak dolibarr
		'CHECKLASTVERSION_EXTERNALMODULE' => 'CHECKLASTVERSION_EXTERNALMODULE',
	],
);
if ((int) DOL_VERSION > 17) {
	// tweak dolibarr
	$modules = array_merge(
		$modules,
		[
			'MAIN_ENABLE_AJAX_TOOLTIP' => 'MAIN_ENABLE_AJAX_TOOLTIP',
		]
	);
}



/*
 * Actions
 */
foreach ($modules as $constant => $desc) {
	if ($action == 'enable_' . strtolower($constant)) {
		dolibarr_set_const($db, $constant, "1", 'chaine', 0, '', $conf->entity);
	}
	if ($action == 'disable_' . strtolower($constant)) {
		dolibarr_set_const($db, $constant, "0", 'chaine', 0, '', $conf->entity);
		//header("Location: ".$_SERVER["PHP_SELF"]);
		//exit;
	}
	if ($action == 'enable_MAIN_ENABLE_AJAX_TOOLTIP' || $action == 'disable_MAIN_ENABLE_AJAX_TOOLTIP') {
		dolibarr_set_const($db, "MAIN_IHM_PARAMS_REV", getDolGlobalInt('MAIN_IHM_PARAMS_REV') + 1, 'chaine', 0, '', $conf->entity);
	}
}
if ($action == 'update') {
	$error = 0;
	$db->begin();
	foreach ($arrayofparameters as $key => $val) {
		$result = dolibarr_set_const($db, $key, GETPOST($key, 'alpha'), 'chaine', 0, '', $conf->entity);
		if ($result < 0) {
			$error++;
			break;
		}
	}
	if (!$error) {
		$db->commit();
		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	} else {
		$db->rollback();
		setEventMessages($langs->trans("SetupNotSaved"), null, 'errors');
	}
}

/*
 * View
 */

llxHeader();

$form = new Form($db);

$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php?restore_lastsearch_values=1">';
$linkback .= $langs->trans("BackToModuleList") . '</a>';
print load_fiche_titre($langs->trans('EasytooltipSettings'), $linkback, 'technic');

$head = easytooltipAdminPrepareHead();

$tab = !empty($module) ? 'settings_' . $module : 'settings';
print dol_get_fiche_head($head, $tab, $langs->trans('EasytooltipSettings'), -1, 'technic');

if ($action == 'edit') {
	print '<form action="' . $_SERVER["PHP_SELF"] . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="update">';

	print '<table class="noborder centpercent">';
	print '<tr class="liste_titre"><td class="titlefield">' . $langs->trans("Parameter") . '</td><td>' . $langs->trans("Value") . '</td></tr>';

	foreach ($arrayofparameters as $key => $val) {
		print '<tr class="oddeven">';
		print '<td>';
		$tooltiphelp = (($langs->trans($key . 'Tooltip') != $key . 'Tooltip') ? $langs->trans($key . 'Tooltip') : '');
		print $form->textwithpicto($langs->trans($key), $tooltiphelp);
		$type = empty($val['type']) ? 'text' : $val['type'];
		$value = !empty($conf->global->$key) ? $conf->global->$key : (isset($val['default']) ? $val['default'] : '');
		print '</td>';
		print '<td><input name="' . $key . '" type="' . $type . '" class="flat ' . (empty($val['css']) ? 'minwidth200' : $val['css']) . '" value="' . $value . '"></td>';
		print '</tr>';
	}
	print '</table>';

	print '<br><div class="center">';
	print '<input class="button" type="submit" value="' . $langs->trans("Save") . '">';
	print '</div>';

	print '</form>';
	print '<br>';
} else {
	print '<table class="noborder centpercent">';

	print '<tr class="liste_titre">';
	print '<td class="titlefield">' . $langs->trans("Parameter") . '</td>';
	print '<td>' . $langs->trans("Value") . '</td></tr>';

	foreach ($arrayofparameters as $key => $val) {
		print '<tr class="oddeven"><td>';
		$tooltiphelp = (($langs->trans($key . 'Tooltip') != $key . 'Tooltip') ? $langs->trans($key . 'Tooltip') : '');
		print $form->textwithpicto($langs->trans($key), $tooltiphelp);
		print '</td><td>';
		$value = $conf->global->$key;
		if (isset($val['type']) && $val['type'] == 'password') {
			$value = preg_replace('/./i', '*', $value);
		}
		print $value;
		print '</td></tr>';
	}

	print '</table>';

	print '<div class="tabsAction">';
	print '<a class="butAction" href="' . $_SERVER["PHP_SELF"] . '?action=edit">' . $langs->trans("Modify") . '</a>';
	print '</div>';

	// Modules
	print '<table class="noborder centpercent">';
	print '<tr class="liste_titre">';
	print '<td>' . $langs->trans("Paramètres Divers") . '</td>';
	print '<td align="center" width="100">' . $langs->trans("Action") . '</td>';
	print "</tr>\n";
	foreach ($modules as $constant => $desc) {
		print '<tr class="oddeven">';
		print '<td>' . $langs->trans($desc) . '</td>';
		print '<td align="center" width="100">';
		$value = (isset($conf->global->$constant) ? $conf->global->$constant : 0);
		if ($value == 0) {
			print '<a href="' . $_SERVER['PHP_SELF'] . '?action=enable_' . strtolower($constant) . '&token=' . $_SESSION['newtoken'] . '&module=' . $module . '">';
			print img_picto($langs->trans("Disabled"), 'switch_off');
			print '</a>';
		} elseif ($value == 1) {
			print '<a href="' . $_SERVER['PHP_SELF'] . '?action=disable_' . strtolower($constant) . '&token=' . $_SESSION['newtoken'] . '&module=' . $module . '">';
			print img_picto($langs->trans("Enabled"), 'switch_on');
			print '</a>';
		}
		print "</td>";
		print '</tr>';
	}
	print '</table>' . PHP_EOL;
	print '<br>' . PHP_EOL;
}

print dol_get_fiche_end();

// End of page
llxFooter();
$db->close();
