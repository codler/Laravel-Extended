<?php
/**
 * @author Han Lin Yap < http://zencodez.net/ >
 * @copyright 2012 zencodez.net
 * @license http://creativecommons.org/licenses/by-sa/3.0/
 * @package Paginator
 * @version 2.0.6 - 3.0 - 2011-12-22
 */
class Paginator extends Laravel\Paginator {

	/**
	 * The "dots" element used in the pagination slider.
	 *
	 * @var string
	 * @since 2.0.6
	 */
	protected $dots = '...';

	/**
	 * Create a new Paginator instance.
	 *
	 * @param  array  $results
	 * @param  int    $last
	 * @param  int    $page
	 * @param  int    $total
	 * @param  int    $per_page
	 * @return void
	 * @since 2.0.6
	 */
	public function __construct($results, $page, $total, $per_page, $last)
	{
		$this->page = $page;
		$this->last = $last;
		$this->total = $total;
		$this->results = $results;
		$this->per_page = $per_page;
	}

	/**
	 * Create the raw pagination links.
	 *
	 * Example: 1 2 ... 23 24 25 [26] 27 28 29 ... 51 52
	 *
	 * @param  int     $adjacent
	 * @return array
	 * @since 2.0.6
	 */
	public function raw_links($adjacent = 3) {
		if ($this->last <= 1) return array(1);

		if ($this->last < 7 + ($adjacent * 2)) {
			return explode(' ', $this->range(1, $this->last));
		}

		return explode(' ', $this->slider($adjacent));
	}

	/**
	 * Build a range of numeric pagination links.
	 *
	 * @param  int     $start
	 * @param  int     $end
	 * @return string
	 * @since 2.0.6
	 */
	protected function range($start, $end) {
		return implode(' ', range($start, $end));
	}
}
