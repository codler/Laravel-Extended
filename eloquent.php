<?php
/**
 * @author Han Lin Yap
 * @version 2.0.6 (2011-12-22)
 */
class Eloquent extends Laravel\Database\Eloquent\Model {

	/**
	 * List of never allowed strings
	 *
	 * Code from https://github.com/EllisLab/CodeIgniter/blob/develop/system/core/Security.php
	 *
	 * @var array
	 * @access protected
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
	 */
	protected $_never_allowed_regex = array(
		"javascript\s*:"			=> '[removed]',
		"expression\s*(\(|&\#40;)"	=> '[removed]', // CSS and IE
		"vbscript\s*:"				=> '[removed]', // IE, surprise!
		"Redirect\s+302"			=> '[removed]'
	);

	/**
	 * Save the model to the database.
	 *
	 * Do a XSS-clean before saving.
	 *
	 * @param bool $safe
	 * @return bool
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
}
