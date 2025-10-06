<?php
/* Copyright (C) 2004-2017  Laurent Destailleur	    <eldy@users.sourceforge.net>
 * Copyright (C) 2023       Frédéric France         <frederic.france@free.fr>
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
 * \file    easytooltip/admin/about.php
 * \ingroup easytooltip
 * \brief   About page of module EasyTooltip.
 */

// Load Dolibarr environment
include '../config.php';

// Libraries
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/functions2.lib.php';
require_once '../lib/easytooltip.lib.php';

// Translations
$langs->loadLangs(["errors", "admin", "easytooltip@easytooltip"]);

// Access control
if (!$user->admin) {
	accessforbidden();
}

// Parameters
$action = GETPOST('action', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');


/*
 * Actions
 */

// None


/*
 * View
 */

$form = new Form($db);

$help_url = '';
$page_name = "EasyTooltipAbout";

llxHeader('', $langs->trans($page_name), $help_url);

// Subheader
$linkback = '<a href="' . ($backtopage ? $backtopage : DOL_URL_ROOT . '/admin/modules.php?restore_lastsearch_values=1') . '">' . $langs->trans("BackToModuleList") . '</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'title_setup');

// Configuration header
$head = easytooltipAdminPrepareHead();
print dol_get_fiche_head($head, 'about', $langs->trans($page_name), 0, 'easytooltip@easytooltip');

dol_include_once('/easytooltip/core/modules/modEasyTooltip.class.php');
$tmpmodule = new modEasyTooltip($db);
print $tmpmodule->getDescLong();

// Page end
print dol_get_fiche_end();
llxFooter();
$db->close();
