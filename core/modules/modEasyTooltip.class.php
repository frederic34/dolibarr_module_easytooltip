<?php
/* Copyright (C) 2004-2018  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2019  Nicolas ZABOURI         <info@inovea-conseil.com>
 * Copyright (C) 2019-2020  Frédéric France         <frederic.france@free.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup   easytooltip     Module EasyTooltip
 *  \brief      EasyTooltip module descriptor.
 *
 *  \file       htdocs/easytooltip/core/modules/modEasyTooltip.class.php
 *  \ingroup    easytooltip
 *  \brief      Description and activation file for module EasyTooltip
 */
include_once DOL_DOCUMENT_ROOT . '/core/modules/DolibarrModules.class.php';

// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
/**
 *  Description and activation class for module EasyTooltip
 */
class modEasyTooltip extends DolibarrModules
{
	// phpcs:enable
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;

		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 491360; // TODO Go on page https://wiki.dolibarr.org/index.php/List_of_modules_id to reserve an id number for your module

		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'easytooltip';

		// Family can be 'base' (core modules),'crm','financial','hr','projects','products','ecm','technic' (transverse modules),'interface' (link with external tools),'other','...'
		// It is used to group modules by family in module setup page
		$this->family = "other";

		// Module position in the family on 2 digits ('01', '10', '20', ...)
		$this->module_position = '90';

		// Gives the possibility for the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
		//$this->familyinfo = array('myownfamily' => array('position' => '01', 'label' => $langs->trans("MyOwnFamily")));
		// Module label (no space allowed), used if translation string 'ModuleEasyTooltipName' not found (EasyTooltip is name of module).
		$this->name = preg_replace('/^mod/i', '', get_class($this));

		// Module description, used if translation string 'ModuleEasyTooltipDesc' not found (EasyTooltip is name of module).
		$this->description = "EasyTooltipDescription";
		// Used only if file README.md and README-LL.md not found.
		$this->descriptionlong = "EasyTooltipDescription";

		// Author
		$this->editor_name = 'frederic34';
		$this->editor_url = '';

		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated', 'experimental_deprecated' or a version string like 'x.y.z'
		$this->version = '1.0.0';
		// Url to the file with your last numberversion of this module

		// Key used in llx_const table to save module status enabled/disabled (where EASYTOOLTIP is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);

		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		// To use a supported fa-xxx css style of font awesome, use this->picto='xxx'
		$this->picto = 'technic';

		// Define some features supported by module (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts = [
			// Set this to 1 if module has its own trigger directory (core/triggers)
			'triggers' => 0,
			// Set this to 1 if module has its own login method file (core/login)
			'login' => 0,
			// Set this to 1 if module has its own substitution function file (core/substitutions)
			'substitutions' => 0,
			// Set this to 1 if module has its own menus handler directory (core/menus)
			'menus' => 0,
			// Set this to 1 if module overwrite template dir (core/tpl)
			'tpl' => 0,
			// Set this to 1 if module has its own barcode directory (core/modules/barcode)
			'barcode' => 0,
			// Set this to 1 if module has its own models directory (core/modules/xxx)
			'models' => 0,
			// Set this to 1 if module has its own printing directory (core/modules/printing)
			'printing' => 0,
			// Set this to 1 if module has its own theme directory (theme)
			'theme' => 0,
			// Set this to relative path of css file if module has its own css file
			'css' => [
				//    '/easytooltip/css/easytooltip.css.php',
			],
			// Set this to relative path of js file if module must load a js on all pages
			'js' => [
				//   '/easytooltip/js/easytooltip.js.php',
			],
			// Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context to 'all'
			'hooks' => [
				'data' => [
					'globalcard',
					'main',
					'commandedao',
					'contratdao',
					'facturedao',
					'productdao',
					'propaldao',
				],
				'entity' => $conf->entity,
			],
			// Set this to 1 if features of module are opened to external users
			'moduleforexternal' => 0,
		];

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/easytooltip/temp","/easytooltip/subdir");
		$this->dirs = ["/easytooltip/temp"];

		// Config pages. Put here list of php page, stored into easytooltip/admin directory, to use to setup module.
		$this->config_page_url = ["setup.php@easytooltip"];

		// Dependencies
		// A condition to hide module
		$this->hidden = false;
		// List of module class names that must be enabled if this module is enabled. Example: array('always'=>array('modModuleToEnable1','modModuleToEnable2'), 'FR'=>array('modModuleToEnableFR')...)
		$this->depends = [];
		// List of module class names to disable if this one is disabled. Example: array('modModuleToDisable1', ...)
		$this->requiredby = [];
		// List of module class names this module is in conflict with. Example: array('modModuleToDisable1', ...)
		$this->conflictwith = [];

		// The language file dedicated to your module
		$this->langfiles = ["easytooltip@easytooltip"];

		// Prerequisites
		$this->phpmin = [7, 1]; // Minimum version of PHP required by module
		$this->need_dolibarr_version = [18, -3]; // Minimum version of Dolibarr required by module

		// Messages at activation
		$this->warnings_activation = []; // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		$this->warnings_activation_ext = []; // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		//$this->automatic_activation = array('FR'=>'EasyTooltipWasAutomaticallyActivatedBecauseOfYourCountryChoice');
		//$this->always_enabled = true;								// If true, can't be disabled

		// Constants
		$this->const = [];

		if (!isset($conf->easytooltip) || !isset($conf->easytooltip->enabled)) {
			$conf->easytooltip = new stdClass();
			$conf->easytooltip->enabled = 0;
		}

		// Array to add new pages in new tabs
		$this->tabs = [];

		// Dictionaries
		$this->dictionaries = [];

		// Boxes/Widgets
		// Add here list of php file(s) stored in easytooltip/core/boxes that contains a class to show a widget.
		$this->boxes = [];

		// Cronjobs (List of cron jobs entries to add when module is enabled)
		// unit_frequency must be 60 for minute, 3600 for hour, 86400 for day, 604800 for week
		$this->cronjobs = [];

		// Permissions provided by this module
		$this->rights = [];

		// Main menu entries to add
		$this->menu = [];
	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 *  @param      string $options Options when enabling module ('', 'noboxes')
	 *  @return     integer                 1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		global $conf, $langs;

		//$result = $this->_load_tables('/install/mysql/', 'easytooltip');
		$result = $this->_load_tables('/easytooltip/sql/');
		if ($result < 0) {
			return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')
		}

		// Permissions
		$this->remove($options);

		$sql = [];

		return $this->_init($sql, $options);
	}

	/**
	 *  Function called when module is disabled.
	 *  Remove from database constants, boxes and permissions from Dolibarr database.
	 *  Data directories are not deleted
	 *
	 *  @param      string $options Options when enabling module ('', 'noboxes')
	 *  @return     integer                 1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = [];
		return $this->_remove($sql, $options);
	}
}
