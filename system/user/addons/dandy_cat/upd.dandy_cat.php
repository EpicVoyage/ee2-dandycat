<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Dandy Cat
 *
 * @package		Dandy Cat
 * @subpackage		ThirdParty
 * @category		Modules
 * @author		EpicVoyage
 * @link		https://www.epicvoyage.org/ee/dandy_cat
 */
class Dandy_cat_upd {
	var $version = '0.2';

	/**
	 * Installer for the Mx_google_map module
	 */
	function install() {
		$data = array(
			'module_name' 	 => 'Dandy_cat',
			'module_version' => $this->version,
			'has_cp_backend' => 'n'
		);

		ee()->db->insert('modules', $data);

		return TRUE;
	}

	/**
	 * Uninstall the Mx_google_map module
	 */
	function uninstall() {
		ee()->db->select('module_id');
		$query = ee()->db->get_where('modules', array('module_name' => $this->module_name));

		ee()->db->where('module_id', $query->row('module_id'));
		ee()->db->delete('module_member_groups');

		ee()->db->where('module_name', $this->module_name);
		ee()->db->delete('modules');

		ee()->db->where('class', $this->module_name);
		ee()->db->delete('actions');

		ee()->db->where('class', $this->module_name.'_mcp');
		ee()->db->delete('actions');

		return TRUE;
	}

	/**
	 * Update the Mx_google_map module
	 *
	 * @param $current current version number
	 * @return boolean indicating whether or not the module was updated
	 */
	function update($current = '') {
		return FALSE;
	}
}

/* End of file upd.dandy_cat.php */
/* Location: ./system/expressionengine/third_party/dandy_cat/upd.dandy_cat.php */
