<?php
/**
 * @author Han Lin Yap
 * @version 2.0.6 (2011-12-22)
 */
class Validator extends Laravel\Validator {

	/**
	 * Validate that an attribute is money value.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	public function validate_money($attribute, $value) {
		return preg_match('/^([0-9,.])+$/i', $value);
	}

	/**
	 * Validate that an attribute is a date (YYYY-MM-DD).
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	public function validate_date($attribute, $value) {
		return preg_match('/^([0-9]){4}-([0-9]){1,2}-([0-9]){1,2}$/i', $value);
	}

	/**
	 * Validate that an attribute is an time (HH:DD).
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	public function validate_time($attribute, $value) {
		return preg_match('/^([0-9]){1,2}:([0-9]){1,2}$/i', $value);
	}

	/**
	 * Validate that an attribute is a comma separated list of integers.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	public function validate_integer_list($attribute, $value) {
		$integers = explode(',', $value);
		foreach($integers AS $integer) {
			if ( strlen($value) > 0 && !$this->validate_integer($attribute, $integer) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Validate that an attribute is a valid Spotify URL.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	public function validate_url_spotify($attribute, $value) {
		$test = parse_url($value);
		$allow_schemes = array('spotify', 'http', 'https');
		return isset($test['scheme']) && in_array($test['scheme'], $schemes);
	}
}
