<?php
/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendartableview\value;

use marttiphpbb\calendartableview\value\dayspan;

class blockspan extends dayspan
{
	protected $rowspan;

	public function __construct(
		int $start_jd,
		int $end_jd,
		rowspan $rowspan
	)
	{
		parent::__construct($start_jd, $end_jd);
		$this->rowspan = $rowspan;
	}

	public function get_rowspan():rowspan
	{
		return $this->rowspan;
	}
}
