<?php
if ($_POST['partisipants'] || $_POST['sectors'] || $_POST['zones'] || $_POST['partisipantsInSector'] || $_POST['tours']) {
	$partisipants = $_POST['partisipants'];
	$sectors = $_POST['sectors'];
	$zones = $_POST['zones'];
	$partisipantsInSector = $_POST['partisipantsInSector'];
	$tours = $_POST['tours'];
} else {
	$partisipants = '';
	$sectors = '';
	$zones = '';
	$partisipantsInSector = '';
	$tours = '';
}
$circularPass = isset($_POST['circularPass']) ? 'checked' : '';
?>

	<style type="text/css">
		.input-form td {
		padding: 5px 10px;
		}
		.input-form td:first-child {
			width: 300px;
		}
		.input-form td:nth-child(2n) input{
			width: 70px;
			height: 25px;
			border-radius: 5px;
		}
	</style>
	<form action="" onsubmit="return validate();" method="post" class="input-form">
		<table align="center">
			<tr>
				<td>
					Количество участников:
				</td>
				<td>
					<input type="number" name="partisipants" min="8" max="500" value="<?= $partisipants ?>">
				</td>
			</tr>
			<tr>
				<td>
					Количество секторов:
				</td>
				<td>
					<input type="number" name="sectors" min="4" max="200" value="<?= $sectors ?>">
				</td>
			</tr>
			<tr>
				<td>
					Количество зон:
				</td>
				<td>
					<input type="number" name="zones" min="2" max="20" value="<?= $zones ?>">
				</td>
			</tr>
			<tr>
				<td>
					Количество участников в секторе:
				</td>
				<td>
					<input type="number" name="partisipantsInSector" min="1" max="5" value="<?= $partisipantsInSector ?>">
				</td>
			</tr>
			<tr>
				<td>
					Количество туров:
				</td>
				<td>
					<input type="number" name="tours" min="1" max="10" value="<?= $tours ?>">
				</td>
			</tr>
			<tr>
				<td>
					Есть проход по кругу:
				</td>
				<td>
					<input type="checkbox" name="circularPass" <?= $circularPass ?>>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td>
				<button type="submit" name="submit-b" >выполнить</button>
				</td>
			</tr>
		</table>
	</form>
	</br>
	<script>
		function validate() {
			var partisipants = document.getElementsByName("partisipants")[0];
			var sectors = document.getElementsByName("sectors")[0];
			var zones = document.getElementsByName("zones")[0];
			var partisipantsInSector= document.getElementsByName("partisipantsInSector")[0];
			var tours = document.getElementsByName("tours")[0];

			if (!partisipants.value) {
				partisipants.style.borderColor = "red";
				return false;
			} else {
				partisipants.style.borderColor = "lime";
			}
			if (!sectors.value) {
				sectors.style.borderColor = "red";
				return false;
			} else {
				sectors.style.borderColor = "lime";
			}
			if (!zones.value) {
				zones.style.borderColor = "red";
				return false;
			} else {
				zones.style.borderColor = "lime";
			}
			if (!partisipantsInSector.value) {
				partisipantsInSector.style.borderColor = "red";
				return false;
			} else {
				partisipantsInSector.style.borderColor = "lime";
			}
			if (!tours.value) {
				tours.style.borderColor = "red";
				return false;
			} else {
				tours.style.borderColor = "lime";
			}
			
			if ((sectors.value * partisipantsInSector.value) < partisipants.value) {
				partisipants.style.borderColor = "orange";
				sectors.style.borderColor = "orange";
				partisipantsInSector.style.borderColor = "orange";
				alert("Для всех участников не хватает места");
				return false;
			}
			if (tours.value > (sectors.value / 2)) {
				tours.style.borderColor = "silver";
				sectors.style.borderColor = "silver";
				alert("слишком много туров для такого количества участников");
				return false;
			}
			return true;
		}
	</script>