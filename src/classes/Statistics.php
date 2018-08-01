<?php



class Statistics
{
	public function __construct()
	{
		$this->pdo = DB::pdo();
	}

	public function regional()
	{
		$st = $this->pdo->prepare(
			"SELECT `regional`,COUNT(`regional`) AS `total` FROM `news` GROUP BY `regional` ORDER BY `total` DESC;"
		);
	}
}