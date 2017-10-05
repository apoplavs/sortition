<?php
/**
*  created by Anrii Poplavskyi
*/
class Sector
{
	private $members = [];

	public $name;

	private $max_members;


	function __construct($name, $max_members) {
		$this->name = $name;
		$this->max_members = $max_members;
	}

	public function AddMember($new_member) {
		$this->members[] = $new_member;
	}

	public function isFree() {
		if (count($this->members) >= $this->max_members) {
		 	return false;
		}
		 return true;
	}
	
	public function GetMembers() {
		return $this->members;
	}
		public function GetName() {
		return $this->name;
	}

	public function CleanMembers() {
		$this->members  = array();
	}
}
?>