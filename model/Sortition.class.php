<?php
require_once 'User.class.php';
//require_once 'Sector.class.php';
//require_once 'dompdf/autoload.inc.php';

/**
* 
*/
class Sortition
{
	private $zones = [];
	private $sectors = [];
	private $count_sectors;
	private $count_members;
	private $members_in_sector;
	private $sectors_in_zone;
	private $tours;
	private $circularPass;
	private $members = [];
	
	function __construct(int $num_of_members, int $sectors, int $zones, int $members_in_sector, int $tours, bool $circularPass) {
		$this->members_in_sector = $members_in_sector;
		$this->tours = $tours;
		$this->circularPass = $circularPass;

		for ($i = 1; $i <= $num_of_members; $i++) { 
			$this->members[$i] = new User($i);
		}
		for ($i = 1; $i <= $sectors; $i++) {
			$this->sectors[$i] = new Sector($i, $members_in_sector);
		}
		$sectors_in_zone = ($sectors % $zones) == 0 ? $sectors / $zones : $sectors / $zones + 1;

		$this->sectors_in_zone = intval($sectors_in_zone);

		$this->zones = array_chunk($this->sectors, $this->sectors_in_zone, true);

		$this->count_sectors = count($this->sectors);
		$this->count_members = count($this->members);

		
	}


	/**
	* check is there any participants that cannot be placed in the current round
	* return bool;
	*/
	private function areStanding()
	{
		$current_member = 1;
		while ($current_member <= $this->count_members) {
			if ($this->members[$current_member]->IsSit() === false) {
				return true;
			}
			$current_member++;
		}
		return false;
	}

	/**
	* if anybody cannot be placed in the current round then all participants stand up
	*/
	private function standUp()
	{
		$current_member = 1;
		while ($current_member <= $this->count_members) {
			if ($this->members[$current_member]->IsSit() === true) {
				$last_sector = $this->members[$current_member]->removeLastSector();
				$this->members[$current_member]->removeLastNeighbors(count($this->sectors[$last_sector]->GetMembers()) - 1);
				$this->members[$current_member]->SetSit(false);
			}
			$current_member++;
		}
		foreach ($this->sectors as $k =>$mem) {
				$this->sectors[$k]->CleanMembers();
			}
	}

	/**
	* try to place the current participant in the current round
	* return bool;
	*/
	private function recursiveSit($current_tour, $current_member, $current_sector, $step) {
		$next_sector = $current_sector;
		$current_sector += $step;

		if (($next_sector + $step) > $this->count_sectors && ($next_sector + (-$step)) < 1) {
			return false;
		}
		 if ($current_sector > $this->count_sectors) {
		 	$step++;	
		 	$this->recursiveSit($current_tour, $current_member, $next_sector, -$step);
		 	return true;
		 }
		 if ($current_sector < 1) {
		 	if ($step == 0) {
		 		$step++;
		 	}
			$this->recursiveSit($current_tour, $current_member, $next_sector, -$step);
			return true;
		 }
		 	// check is current member can sit in current position
		if ($this->members[$current_member]->IsCanSit($this->sectors[$current_sector], $this->sectors_in_zone, $this->count_sectors)) {
			// add position in object member
			$this->members[$current_member]->addPosition($current_tour, $this->sectors[$current_sector]);
			// add memeber to current sector
			$this->sectors[$current_sector]->AddMember($this->members[$current_member]->getNumber());
			// add neighbors to each member in sector
			foreach ($this->sectors[$current_sector]->GetMembers() as $in_sector) {
				$this->members[$in_sector]->addNeighbors($this->sectors[$current_sector]->GetMembers());
			}
			return true;
		}
		$step *= -1;
		if ($step <= 0) {
				$step--;	
			}
		$this->recursiveSit($current_tour, $current_member, $next_sector, $step);
	}

	/**
	* try to place all participants in the current round
	* return bool;
	*/
	private function placementInTour($current_tour, $start_member) {
		$current_try = 1;

		while ($current_try <= $this->count_members) {
			$start_member++;
			if ($start_member > $this->count_members) {
					$start_member = 1;
			}

			$current_member = $start_member;
			$count_iteration = 0;

			while ($count_iteration < $this->count_members) {
				// get last sector where was current member
				$last_sector = $this->members[$current_member]->getLastSector();

				// if is circular pass then to count on another
				if ($this->circularPass && ($last_sector + $this->sectors_in_zone) >= $this->count_sectors) {
						$current_sector = ($this->sectors_in_zone - ($this->count_sectors - $last_sector)) + 1;
				} else {
					if (($last_sector + $this->sectors_in_zone) <= $this->count_sectors){
						$current_sector = $last_sector + $this->sectors_in_zone;
					} else {
						$current_sector = $last_sector - $this->sectors_in_zone;
					}
				}
				// try to place the current participant
				$this->recursiveSit($current_tour, $current_member, $current_sector, 0);
					// transition to next partcipant
				$current_member++;
					// if number of current participant is very big then begin from first
				if ($current_member > $this->count_members) {
					$current_member = 1;
				}
				$count_iteration++;
			}
			$current_try++;
			// if all participant is placed then do not try to placement more, and transition to next tour
			if ($this->areStanding() === false || $current_try > $this->count_members) {
				break;
			}
			// if no, then try placement again 
			$this->standUp();
		}
	}

	/**
	* the main function for the placement of participants
	*/
	public function TossUp() {

		$current_tour = 1;
		// iterates through each tour and placement of partcipants
		while ($current_tour <= $this->tours) {

			// generates random number of participant from whose start placement
			$start_member = rand(0, ($this->count_members - 3));
				//echo "in tour ".$current_tour." rand_num = ".print_r($rand_num, true)."<br>";
			$this->placementInTour($current_tour, $start_member);

			if ($this->areStanding()) {
				echo '<h3 align="center">в '.print_r($current_tour, true).' туре невозможно разместить всех участников попробуйте изменить начальные параметры<h3/>';
				die();
			}

			// transition to next tour
			$current_tour++;
			foreach ($this->members as $key =>$member) {
				$this->members[$key]->SetSit(false);
			}
			foreach ($this->sectors as $k =>$mem) {
				$this->sectors[$k]->CleanMembers();
			}

		}
		$this->printResult();
	
// for feature extension to pdf

	// $current_tour = 1;
	// 	while ($current_tour <= $this->tours)
	// 	{
	// 		echo "<hr><b>TOUR # ". print_r($current_tour, true).'</b><br>';
	// 		foreach ($this->members as $member) {
	// 			//print_r($member);
	// 			$sector = $member->getPositionInTour($current_tour);
	// 			echo " member ".print_r($member->getNumber(), true).' => '.print_r($sector, true).'<br>';
	// 		}
	// 		$current_tour++;
	// 	}
// echo "<hr>members<br>";
// print_r($this->members);
//  echo "<hr>sectors<br>";
//  print_r($this->sectors);


	}

	public function printResult() {
		foreach ($this->members as $key =>$member) {
				$this->members[$key]->removeFirstPosition();
			}
			$this->printCommonResult();
	}

	private function printCommonResult() {
		$table = "";
		for ($tour=1; $tour <= $this->tours ; $tour++) { 
			$table .= "<th>тур ".$tour."</th>";
		}
		$table .= "
		</tr>";
		for ($member=1; $member <= $this->count_members; $member++) {
			$table .= "
		<tr>
			";
			$table .= "<td>".$member."</td><td>ФИО ".$member."</td>";

			for ($tour=1; $tour <= $this->tours ; $tour++) {
				$position = $this->members[$member]->getPositionInTour($tour);
				$table .= "<td>".$position['sector']."<sub>".$position['position']."</sub></td>";
			}

			$table .= "
		</tr>";
		}
	require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'common_result.php';
	}

	private function printBadges() {
		
	}



}

?>