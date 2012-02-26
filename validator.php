<?php
/**
 * @author Han Lin Yap < http://zencodez.net/ >
 * @copyright 2012 zencodez.net
 * @license http://creativecommons.org/licenses/by-sa/3.0/
 * @package Validator
 * @version 3.0.0 - 2012-02-26
 */
class Validator extends Laravel\Validator {

	/**
	 * Determine if a given rule implies that the attribute is required.
	 *
	 * @param  string  $rule
	 * @return bool
	 * @since 3.0.0
	 */
	protected function implicit($rule)
	{
		return $rule == 'captcha' or parent::implicit($rule);
	}

	/**
	 * Validate that an attribute has a matching confirmation attribute.
	 *
	 * This works by JavaScript is prepending an input field with attribute 
	 * name 'js' and attribute value '1' on every form-tag and that has an 
	 * input field with attribute name '*_value' which are and should match
	 * a hidden input field with attribute name without '_value' and its value
	 * is md5.
	 *
	 * <code>
	 * 		// check if javascript is enabled
	 * 		jQuery('form').append('<input type="hidden" name="js" value="1" />');
	 * </code>
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 * @since 3.0.0
	 */
	protected function validate_captcha($attribute, $value)
	{
		$confirmed = $attribute.'_value';

		return $this->validate_required($attribute, $value) 
			and isset($this->attributes['js'])
			and $this->attributes['js'] == 1
			and isset($this->attributes[$confirmed])
			and $value == md5($this->attributes[$confirmed]);
	}

	/**
	 * Validate that an attribute is money value.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 * @since 2.0.6
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
	 * @since 2.0.6
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
	 * @since 2.0.6
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
	 * @since 2.0.6
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
	 * @since 2.0.6
	 */
	public function validate_url_spotify($attribute, $value) {
		$test = parse_url($value);
		$allow_schemes = array('spotify', 'http', 'https');
		return isset($test['scheme']) && in_array($test['scheme'], $allow_schemes);
	}
}
