<?php
/* Copyright (C) 2023       Frédéric France <frederic.france@netlogic.fr>
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
 * \file    easytooltip/class/actions_easytooltip.class.php
 * \ingroup easytooltip
 * \brief   EasyTooltip hook overload.
 *
 * Put detailed description here.
 */

/**
 * Class ActionsEasyTooltip
 */
class ActionsEasyTooltip
{
	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	/**
	 * @var string Error code (or message)
	 */
	public $error = '';

	/**
	 * @var array Errors
	 */
	public $errors = [];


	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = [];

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var int		Priority of hook (50 is used if value is not defined)
	 */
	public $priority = 99;


	/**
	 * Constructor
	 *
	 *  @param		DoliDB $db Database handler.
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}


	/**
	 * Overloading the getTooltipContent function : replacing the parent's function with the one below
	 *
	 * @param   array        $parameters  Hook metadatas (context, etc...)
	 * @param   CommonObject $object      The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string       $action      Current action (if set). Generally create or edit or null
	 * @param   HookManager  $hookmanager Hook manager propagated to allow calling another hook
	 * @return  integer                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function getTooltipContent($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		// var_dump($parameters);
		$langs->load('easytooltip@easytooltip');
		$contexts = explode(':', $parameters['context']);
		$found = false;
		if (in_array('commandedao', $contexts)) {
			/** @var Commande $object */
			$found = true;
		} elseif (in_array('propaldao', $contexts)) {
			/** @var Propal $object */
			$found = true;
		} elseif (in_array('facturedao', $contexts)) {
			/** @var Facture $object */
			$found = true;
		} elseif (in_array('productdao', $contexts)) {
			/** @var Product $object */
			$found = true;
			// ADDING DURATION IF NOT PRESENT
			if ($object->type == Product::TYPE_SERVICE && empty($parameters['tooltipcontentarray']['duration']) && !empty($object->duration_value)) {
				// Duration
				$tooltip = '<br><b>' . $langs->trans("Duration") . ':</b> ' . $object->duration_value;
				if ($object->duration_value > 1) {
					$dur = array("i" => $langs->trans("Minutes"), "h" => $langs->trans("Hours"), "d" => $langs->trans("Days"), "w" => $langs->trans("Weeks"), "m" => $langs->trans("Months"), "y" => $langs->trans("Years"));
				} elseif ($object->duration_value > 0) {
					$dur = array("i" => $langs->trans("Minute"), "h" => $langs->trans("Hour"), "d" => $langs->trans("Day"), "w" => $langs->trans("Week"), "m" => $langs->trans("Month"), "y" => $langs->trans("Year"));
				}
				$tooltip .= (!empty($object->duration_unit) && isset($dur[$object->duration_unit]) ? "&nbsp;" . $langs->trans($dur[$object->duration_unit]) : '');
				self::array_splice_assoc($parameters['tooltipcontentarray'], 4, 0, ['duration' => $tooltip]);
			}
			// ADDING LAST CUSTOMER ORDER
			if ($object->type == Product::TYPE_PRODUCT || ($object->type == Product::TYPE_SERVICE && getDolGlobalString('STOCK_SUPPORTS_SERVICES'))) {
				$sql = 'SELECT c.rowid as id, c.fk_soc, cd.qty FROM ' . MAIN_DB_PREFIX . 'commandedet as cd';
				$sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'commande as c ON c.rowid=cd.fk_commande WHERE cd.fk_product=' . $object->id . ' ORDER BY cd.rowid DESC LIMIT 2';
				$resql = $this->db->query($sql);
				if ($this->db->num_rows($resql) > 0) {
					require_once DOL_DOCUMENT_ROOT . '/commande/class/commande.class.php';
					$langs->load('orders');
					$static_order = new Commande($this->db);
					$static_customer = new Societe($this->db);
					$tooltip = '';
					// $tooltip = '<br>';
					$tooltip .= '<table class="noborder centpercent">';
					$tooltip .= '<tr class="liste_titre">';
					$tooltip .= '<th>' . $langs->trans("LastCustomerOrders") . '</th>';
					$tooltip .= '<th class="left">' . $langs->trans("EasyTooltipCustomers") . '</th>';
					$tooltip .= '<th class="right">' . $langs->trans("OrderDate") . '</th>';
					$tooltip .= '<th class="right">' . $langs->trans("Qty") . '</th>';
					$tooltip .= '</tr>';
					$total = 0;
					$lines = 0;
					while ($obj = $this->db->fetch_object($resql)) {
						$static_order->fetch($obj->id);
						$static_customer->fetch($obj->fk_soc);
						$tooltip .= '<tr class="oddeven">';
						$tooltip .= '<td>' . $static_order->getNomUrl(1, '', 0, 0, 1) . '</td>';
						$tooltip .= '<td class="left">' . $static_customer->getNomUrl(1, '', 0, 1) . '</td>';
						$tooltip .= '<td class="right">' . dol_print_date($static_order->date, 'day') . '</td>';
						$tooltip .= '<td class="right">' . $obj->qty . '</td>';
						$total += $obj->qty;
						$lines++;
						$tooltip .= '</tr>';
					}
					if ($lines > 1) {
						$tooltip .= '<tr class="liste_total">';
						$tooltip .= '<td colspan="3" class="liste_total">' . $langs->trans("Total") . ':</td>';
						$tooltip .= '<td class="liste_total right">' . $total . '</td>';
						$tooltip .= '</tr>';
					}
					$tooltip .= '</table>';
					$parameters['tooltipcontentarray']['lastcustomerorder'] = $tooltip;
				}
			}
			// ADDING LAST SUPPLIER ORDER
			if ($object->type == Product::TYPE_PRODUCT || ($object->type == Product::TYPE_SERVICE && getDolGlobalString('STOCK_SUPPORTS_SERVICES'))) {
				$sql = 'SELECT c.rowid as id, c.fk_soc, cd.qty FROM ' . MAIN_DB_PREFIX . 'commande_fournisseurdet as cd';
				$sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'commande_fournisseur as c ON c.rowid=cd.fk_commande WHERE cd.fk_product=' . $object->id . ' ORDER BY cd.rowid DESC LIMIT 2';
				$resql = $this->db->query($sql);
				if ($this->db->num_rows($resql) > 0) {
					require_once DOL_DOCUMENT_ROOT . '/fourn/class/fournisseur.commande.class.php';
					$langs->load('orders');
					$static_order = new CommandeFournisseur($this->db);
					$static_supplier = new Societe($this->db);
					$tooltip = '';
					// $tooltip = '<br>';
					$tooltip .= '<table class="noborder centpercent">';
					$tooltip .= '<tr class="liste_titre">';
					$tooltip .= '<th>' . $langs->trans("LastSupplierOrders") . '</th>';
					$tooltip .= '<th class="left">' . $langs->trans("Suppliers") . '</th>';
					$tooltip .= '<th class="right">' . $langs->trans("OrderDate") . '</th>';
					$tooltip .= '<th class="right">' . $langs->trans("Qty") . '</th>';
					$tooltip .= '</tr>';
					$total = 0;
					$lines = 0;
					while ($obj = $this->db->fetch_object($resql)) {
						$static_order->fetch($obj->id);
						$static_supplier->fetch($obj->fk_soc);
						$tooltip .= '<tr class="oddeven">';
						$tooltip .= '<td>' . $static_order->getNomUrl(1, '', 0, 0, 1) . '</td>';
						$tooltip .= '<td class="left">' . $static_supplier->getNomUrl(1, '', 0, 1) . '</td>';
						$tooltip .= '<td class="right">' . dol_print_date($static_order->date, 'day') . '</td>';
						$tooltip .= '<td class="right">' . $obj->qty . '</td>';
						$total += $obj->qty;
						$lines++;
						$tooltip .= '</tr>';
					}
					if ($lines > 1) {
						$tooltip .= '<tr class="liste_total">';
						$tooltip .= '<td colspan="3" class="liste_total">' . $langs->trans("Total") . ':</td>';
						$tooltip .= '<td class="liste_total right">' . $total . '</td>';
						$tooltip .= '</tr>';
					}
					$tooltip .= '</table>';
					$parameters['tooltipcontentarray']['lastsupplierorder'] = $tooltip;
				}
			}
			// ADDING WAREHOUSE
			if ($object->type == Product::TYPE_PRODUCT || ($object->type == Product::TYPE_SERVICE && getDolGlobalString('STOCK_SUPPORTS_SERVICES'))) {
				$langs->load('stocks');
				require_once DOL_DOCUMENT_ROOT . '/product/stock/class/entrepot.class.php';
				$warehouse = new Entrepot($this->db);

				$sql = "SELECT e.rowid, e.ref,";
				$sql .= " ps.reel, ps.rowid as product_stock_id";
				$sql .= " FROM " . MAIN_DB_PREFIX . "entrepot as e";
				$sql .= " , " . MAIN_DB_PREFIX . "product_stock as ps";
				// $sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "product as p ON p.rowid = ps.fk_product";
				$sql .= " WHERE ps.reel != 0 AND ps.fk_entrepot = e.rowid";
				$sql .= " AND ps.fk_product = " . (int) $object->id . " ORDER BY e.ref";
				$sql .= " AND e.entity IN (" . getEntity('stock', 1) . ")";

				$resql = $this->db->query($sql);
				if ($this->db->num_rows($resql) > 0) {
					$total = 0;
					$lines = 0;
					$tooltip = '';
					// $tooltip = '<br>';
					$tooltip .= '<table class="noborder centpercent">';
					$tooltip .= '<thead>';
					$tooltip .= '<tr class="liste_titre">';
					$tooltip .= '<th>' . $langs->trans("Warehouse") . '</th>';
					$tooltip .= '<th class="right">' . $langs->trans("NumberOfUnit") . '</th>';
					$tooltip .= '</tr>';
					$tooltip .= '</thead>';
					while ($obj = $this->db->fetch_object($resql)) {
						$warehouse->fetch($obj->rowid);
						$real_stock = round($obj->reel, 8);
						$tooltip .= '<tr class="oddeven">';
						$tooltip .= '<td>' . $warehouse->getNomUrl(1, 'nolink', 0, 1) . '</td>';
						$tooltip .= '<td class="right">' . $real_stock . ($real_stock < 0 ? ' ' . img_warning() : '') . '</td>';
						$tooltip .= '</tr>';
						$total += $obj->reel;
						$lines++;
					}
					if ($lines > 1) {
						$tooltip .= '<tr class="liste_total">';
						$tooltip .= '<td class="liste_total">' . $langs->trans("Total") . ':</td>';
						$tooltip .= '<td class="liste_total right">' . $total . '</td>';
						$tooltip .= '</tr>';
					}
					$tooltip .= '</table>';
					$parameters['tooltipcontentarray']['stocks'] = $tooltip;
				}
			}
		} elseif (in_array('societedao', $contexts)) {
			/** @var Societe $object */
			$found = true;
		} elseif (in_array('userdao', $contexts)) {
			/** @var User $object */
			$found = true;
		} elseif (in_array('bank_accountdao', $contexts)) {
			/** @var Account $object */
			$found = true;
		} elseif (in_array('memberdao', $contexts)) {
			/** @var Adherent $object */
			$found = true;
		} elseif (in_array('adherent_typedao', $contexts)) {
			/** @var AdherentType $object */
			$found = true;
		} elseif (in_array('ticketdao', $contexts)) {
			/** @var Ticket $object */
			$found = true;
		} elseif (in_array('projectdao', $contexts)) {
			/** @var Project $object */
			$found = true;
		}
		if ($found) {
			require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
			foreach ($parameters['tooltipcontentarray'] as $key => $value) {
				if ((getDolGlobalString('EASYTOOLTIP_' . mb_strtoupper($object->element) . '_' . $conf->entity . '_' .  mb_strtoupper($key))) == '') {
					// set default values
					dolibarr_set_const($this->db, 'EASYTOOLTIP_' . mb_strtoupper($object->element) . '_' . $conf->entity . '_' . mb_strtoupper($key), '1', 'chaine', 0, '', $conf->entity);
				}
			}
			foreach ($parameters['tooltipcontentarray'] as $key => $value) {
				if (getDolGlobalString('EASYTOOLTIP_' . mb_strtoupper($object->element) . '_' . $conf->entity . '_' .  mb_strtoupper($key)) == '0') {
					unset($parameters['tooltipcontentarray'][$key]);
				}
			}
		}

		return 0;
	}

	/**
	 * array_splice_assoc
	 *
	 * @param  mixed $input
	 * @param  mixed $offset
	 * @param  mixed $length
	 * @param  mixed $replacement
	 * @return void
	 */
	private function array_splice_assoc(&$input, $offset, $length, $replacement)
	{
		$replacement = (array) $replacement;
		$key_indices = array_flip(array_keys($input));
		if (isset($input[$offset]) && is_string($offset)) {
			$offset = $key_indices[$offset];
		}
		if (isset($input[$length]) && is_string($length)) {
			$length = $key_indices[$length] - $offset;
		}

		$input = array_slice($input, 0, $offset, true) + $replacement + array_slice($input, $offset + $length, null, true);
	}
}
