<?php
/**
 * @author Han Lin Yap < http://zencodez.net/ >
 * @copyright 2012 zencodez.net
 * @license http://creativecommons.org/licenses/by-sa/3.0/
 * @package Eloquent
 * @version 3.0.0 - 2012-02-26
 */
class Eloquent extends Eloquent\Model {

	/**
	 * List of never allowed strings
	 *
	 * Code from https://github.com/EllisLab/CodeIgniter/blob/develop/system/core/Security.php
	 *
	 * @var array
	 * @access protected
	 * @since 2.0.6
	 */
	protected $_never_allowed_str = array(
		'document.cookie'	=> '[removed]',
		'document.write'	=> '[removed]',
		'.parentNode'		=> '[removed]',
		'.innerHTML'		=> '[removed]',
		'window.location'	=> '[removed]',
		'-moz-binding'		=> '[removed]',
		'<!--'				=> '&lt;!--',
		'-->'				=> '--&gt;',
		'<![CDATA['			=> '&lt;![CDATA[',
		'<comment>'			=> '&lt;comment&gt;'
	);

	/**
	 * List of never allowed regex replacement
	 *
	 * Code from https://github.com/EllisLab/CodeIgniter/blob/develop/system/core/Security.php
	 *
	 * @var array
	 * @access protected
	 * @since 2.0.6
	 */
	protected $_never_allowed_regex = array(
		"javascript\s*:"			=> '[removed]',
		"expression\s*(\(|&\#40;)"	=> '[removed]', // CSS and IE
		"vbscript\s*:"				=> '[removed]', // IE, surprise!
		"Redirect\s+302"			=> '[removed]'
	);

	/**
	 * Table prefix
	 *
	 * @return string
	 * @since 3.0.0
	 */
	public function prefix() {
		return array_get($this->query->connection->config, 'prefix', '');
	}

	/**
	 * Save the model to the database.
	 *
	 * Do a XSS-clean before saving.
	 *
	 * @param bool $safe
	 * @return bool
	 * @since 2.0.6
	 */
	public function save($safe = true) {
		# like a "before filter"
		if ($safe) {
			foreach(array_filter($this->attributes) AS $k => $value) {
				if (!$value instanceof Expression && is_string($value)) {
					$this->attributes[$k] = $this->xss_clean($value);
				}
			}
		}
		
		return parent::save();		
	}

	/**
	 * XSS filter, use with caution! NOT 100% bulletproof!
	 *
	 * Code from https://github.com/EllisLab/CodeIgniter/blob/develop/system/core/Security.php
	 *
	 * @param string $value
	 * @return string
	 * @since 2.0.6
	 */
	public function xss_clean($value) {
		$value = preg_replace('#(alert|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', "\\1\\2&#40;\\3&#41;", $value);

		foreach ($this->_never_allowed_str as $key => $val) {
			$value = str_replace($key, $val, $value);
		}

		foreach ($this->_never_allowed_regex as $key => $val) {
			$value = preg_replace("#".$key."#i", $val, $value);
		}

		return $value;
	}

	/**
	 * Get dynamic all values in a column.
	 *
	 * Dynamic queries are caught by the __call magic method and are parsed here.
	 * They provide a convenient, expressive API for building simple conditions.
	 *
	 * @param  string  $method
	 * @return array
	 * @since 2.0.9
	 */
	private function dynamic_get($method)
	{
		// Strip the "get_" off of the method.
		$finder = substr($method, 4);

		$items = array();
		foreach($this->get() AS $item) {
			$items[] = $item->$finder;
		}
		return $items;
	}

	/**
	 * Magic Method for handling dynamic functions.
	 *
	 * This method handles all calls to aggregate functions as well
	 * as the construction of dynamic where clauses.
	 *
	 * @since 2.0.9
	 */
	public function __call($method, $parameters) {
		if (strpos($method, 'get_') === 0) {
			return $this->dynamic_get($method);
		}

		return parent::__call($method, $parameters);
	}
}
