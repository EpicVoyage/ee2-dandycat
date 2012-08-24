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
	var $version = '1.0';

	function Dandy_cat_upd($switch = TRUE) {
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	}

	/**
	 * Installer for the Mx_google_map module
	 */
	function install() {
		$data = array(
			'module_name' 	 => 'Dandy_cat',
			'module_version' => $this->version,
			'has_cp_backend' => 'n'
		);

		$this->EE->db->insert('modules', $data);

		return TRUE;
	}

	/**
	 * Uninstall the Mx_google_map module
	 */
	function uninstall() {
		$this->EE->db->select('module_id');
		$query = $this->EE->db->get_where('modules', array('module_name' => $this->module_name));

		$this->EE->db->where('module_id', $query->row('module_id'));
		$this->EE->db->delete('module_member_groups');

		$this->EE->db->where('module_name', $this->module_name);
		$this->EE->db->delete('modules');

		$this->EE->db->where('class', $this->module_name);
		$this->EE->db->delete('actions');

		$this->EE->db->where('class', $this->module_name.'_mcp');
		$this->EE->db->delete('actions');

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
