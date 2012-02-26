<?php
/**
 * @author Han Lin Yap < http://zencodez.net/ >
 * @copyright 2012 zencodez.net
 * @license http://creativecommons.org/licenses/by-sa/3.0/
 * @package Asset
 * @version 3.0.0 - 2012-02-26
 */
class Asset extends Laravel\Asset {

	/**
	 * Same as in Laravel\Asset but makes sure Asset_Container is not calling 
	 * Laravel\Asset_Container
	 * 
	 * @since 2.0.9
	 */
	public static function container($container = 'default')
	{
		if ( ! isset(static::$containers[$container]))
		{
			static::$containers[$container] = new Asset_Container($container);
		}

		return static::$containers[$container];
	}
}

class Asset_Container extends Laravel\Asset_Container {

	/**
	 * Get all added assets path location by group
	 *
	 * @since 2.0.9
	 */
	public function raw($group) {

		if ( ! isset($this->assets[$group]) or count($this->assets[$group]) == 0) return '';

		$assets = array();

		foreach ($this->arrange($this->assets[$group]) as $name => $data)
		{
			$assets[] = $this->assets[$group][$name]['source'];
		}
		return $assets;
	}
}