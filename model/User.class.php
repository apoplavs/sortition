<?php 
/**
* created by Anrii Poplavskyi
*/
require_once 'Sector.class.php';

class User
{
	private $user_number;

	private $was_with_users = [];

	private $was_sectors = [];

	private $sit;


	function __construct($user_num) {
		$this->user_number = $user_num;
		$this->sit = false;
		$this->was_sectors['tour 0'] =  array('sector' => -1);
	}


	public function addNeighbors(array $neighbors) {
		foreach ($neighbors as $neighbor) {
			if ($neighbor != $this->user_number && !in_array($neighbor, $this->was_with_users)) {
				$this->was_with_users[] = $neighbor;
			}
		}
	}


	public function IsSit() {
		return $this->sit;
	}

	public function setSit($value) {
		$this->sit = $value;
	}

	
	public function IsWasSector($sector) {
		if (empty($this->was_sectors)) {
			return false;
		}
		 foreach ($this->was_sectors as $key => $current_sector) {
		 	if ($current_sector['sector'] == $sector) {
		 		return true;
		 	}
		 }
		return false;
	}

	public function IsWasNeighbor(array $neighbors) {

		if (empty($this->was_with_users) || empty($neighbors)) {
			return false;
		}
		foreach ($neighbors as $num => $neighbor) {
			if (in_array($neighbor, $this->was_with_users)) {
				return (true);
			}
		}
		return (false);
	}

	public function addPosition($tour, $sector) {
		$sector_name = $sector->GetName();
		$position = count($sector->GetMembers());

		$this->was_sectors['tour '.$tour] =  array('sector' => $sector_name, 'position' => ($position + 1));
		$this->setSit(true);
	}

	public function IsCanSit($sector, $sectors_in_zone, $num_sectors) {
		$sector_name = $sector->GetName();
		$last_sector = $this->getLastSector();

		if ($sector_name == ($last_sector + 1) || $sector_name == ($last_sector - 1)) {
			return false;
		}
			if ($sector->isFree() === false
			|| $this->IsWasNeighbor($sector->GetMembers())
			|| $this->IsWasSector($sector_name)) {
			 	return false;
			 }
		return true;
	}

	public function removeLastSector() {
		$last_sector = array_pop($this->was_sectors);
		return $last_sector['sector'];
	}
	public function removeLastNeighbors($count_neighbors) {
		while ($count_neighbors > 0) {
			array_pop($this->was_with_users);
			$count_neighbors--;
		}
	}
	public function removeFirstPosition() {
		array_shift($this->was_sectors);
	}


	public function getPositions() {
		return $this->was_sectors;
	}
	public function getLastSector() {
		$last = end($this->was_sectors);
		return $last['sector'];
	}
	public function getPositionInTour($tour) {
		return $this->was_sectors['tour '.$tour];
	}
	public function getNumber() {
		return $this->user_number;
	}

}

?>