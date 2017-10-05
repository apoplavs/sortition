<?php
$full_table = "
<style type='text/css'>
.common-result {
	 border-collapse: collapse;
}
.common-result td, th {
	 padding: 3px 10px;
	 border: 1px solid black;
	 text-align: center;
}
.common-result  tr:nth-child(2n) {
	background: #e6fff7;
}
.common-result th {
	background: #b0e0e6;
}
</style>
</br>
</br>
	<table align='center' class='common-result'>
		<tr>
			<th>номер</th><th>Участник</th>";
$full_table .= $table;
$full_table .= "
	</table>
	</br>
	</br>";
echo $full_table;