<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Dandy Cat
 *
 * @package	Dandy_cat
 * @subpackage	ThirdParty
 * @category	Modules
 * @author	EpicVoyage
 * @link	https://www.epicvoyage.org/ee/dandy_cat
 */
class Dandy_cat {
	function entries() {
		# Hijack the Functions class.
		$functions = ee()->functions;
		ee()->evalStringInFacadeScope('<?php $this->loaded["functions_dandy_cat_backup"] = $this->loaded["functions"]; $this->loaded["functions"] = new Dandy_cat_EE_Functions; ?>', array());
		// EE5 allowed us to do this: ee()->functions = new Dandy_cat_EE_Functions;

		# Create a new instance of the EE Channel class so that it can do all of our heavy lifting.
		$channel = new Channel;

		# EE's Channel class completely refactors any category string with an '&' in it. Let's use commas to sneak past that bit of code.
		if (isset(ee()->TMPL->tagparams['category'])) {
			ee()->TMPL->tagparams['category'] = str_replace('&', ',', ee()->TMPL->tagparams['category']);
		}

		# We use the the Channel Entries workhorse for most of this.
		$entries = $channel->entries();

		# Restore the Functions class and return.
		ee()->evalStringInFacadeScope('<?php $this->loaded["functions"] = $this->loaded["functions_dandy_cat_backup"]; ?>', array());
		// EE5 allowed us to do this: ee()->functions = $functions;
		return $entries;
	}

	function form() {
		return '';
	}

	# Input Example:
	#   $str = (1|2|3)&((!4|5|6)|(7&8))
	#   $field = exp_categories.cat_id
	#
	# Output:
	#   SQL: (exp_categories.cat_id IN (1, 2, 3) AND ...)
	public static function parse($str, $field) {
		static $c1 = 0, $c2 = 0;

		# Handle EE's "not 1|2|3" but we prefer "!1|2|3".
		$str = preg_replace('/not/', '!', $str);

		# Remove any/all spaces.
		$str = preg_replace('/\s*/', '', $str);

		# And we do not support | or & at the ends of the string (try to guarantee valid SQL output).
		$str = trim($str, "|&");

		if (strpos($str, '(') !== false) {
			$start = 0;
			$paren = 0;
			$ret = '(';
			for ($x = 0, $len = strlen($str); $x < $len; $x++) {
				switch ($str[$x]) {
				case '(':
					if ($paren == 0) {
						$start = $x + 1;
					}
					$paren++;
					break;
				case ')':
					$paren--;
					if ($paren == 0) {
						$ret .= Dandy_cat::parse(substr($str, $start, $x - $start), $field);
					}
					break;
				case '|':
					if ($paren == 0) {
						$ret .= ' OR ';
					}
					break;
				case '&':
					if ($paren == 0) {
						$ret .= ' AND ';
					}
					break;
				}
			}
			$ret .= ')';
		} else {
			if ($invert = (strncmp($str, '!', 1) == 0)) {
				$str = str_replace('!', '', $str);
			}
			if ($all = (strpos($str, '&') !== false)) {
				$str = str_replace('&', '|', $str);
			}

			# Protect the database from each value.
			$categories = explode('|', $str);
			foreach ($categories as $k => $v) {
				$categories[$k] = ee()->db->escape($v);
			}

			# Don't do anything for empty parenthesis.
			if (empty($str) || empty($categories)) {
				# Return a valid SQL statement.
				return '1';
			}

			# Put together our query string. First, if & was used...
			if ($invert || $all) {
				$mode = $invert && !$all ? ' OR ' : ' AND ';
				$f = explode('.', $field);
				$table = $f[0];
				$where = '';

				# Build a sub-query to verify the matched categories. This is not as efficient as
				# EE's code, but we require this method due to our desired power level.
				#
				# TODO: It is conceivable that in some edge cases t.entry_id is not the right choice.
				$ret = 't.entry_id'.($invert ? ' NOT' : '').' IN (SELECT ctdc'.$c1.'.entry_id
					FROM exp_channel_titles ctdc'.$c1;
				foreach ($categories as $v) {
					$c2++;
					$ret .= '
						LEFT JOIN exp_category_posts cp'.$c2.' ON (cp'.$c2.'.entry_id = ctdc'.$c1.'.entry_id AND cp'.$c2.'.cat_id = '.$v.')';

					$where .= (empty($where) ? '' : $mode).'cp'.$c2.'.entry_id IS NOT NULL';
				}
				$ret .= '
					WHERE '.$where.')';
				$c1++;

			# Otherwise, if pipes were used (or only one category)...
			} else {
				$ret = '('.$field.($invert ? ' NOT' : '').' IN ('.implode(', ', $categories).')'.($invert ? ' OR '.$field.' IS NULL' : '').')';
			}
		}

		return $ret;
	}
}

if (!class_exists('Channel')) {
	require PATH_MOD.'channel/mod.channel.php';
}
if (!class_exists('EE_Functions')) {
	require APPPATH.'libraries/Functions.php';
}

# Sub-class the Functions class...
class Dandy_cat_EE_Functions extends EE_Functions {
	function sql_andor_string($str, $field, $prefix = '', $null = FALSE) {
		# Hijack comparisons on the exp_categories table.
		if (strpos($field, 'categories.') !== false) {
			$ret = 'AND '.Dandy_cat::parse(str_replace(',', '&', $str), $field);

		# And fall back to the original function for everything else (even though our function
		# should produce compatible output).
		} else {
			$ret = parent::sql_andor_string($str, $field, $prefix, $null);
		}

		return $ret;
	}
}
/* End of file mod.dandy_cat.php */
/* Location: ./system/expressionengine/third_party/dandy_cat/mod.dandy_cat.php */
