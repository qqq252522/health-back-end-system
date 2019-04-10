<?php
$db_host = 'localhost'; // Server Name
$db_user = 'root'; // Username
$db_pass = 'root'; // Password
$db_name = 'NHI hung'; // Database Name

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
	die ('Failed to connect to MySQL: ' . mysqli_connect_error());	
}
mysqli_query($conn,"SET NAMES 'UTF8'");
if(isset($_GET['icd'])){
	$icd=$_GET['icd'];
}
if(isset($_GET['select_op'])){
	$select_op=$_GET['select_op'];
}
if(isset($_GET['time1'])){
	$time1=$_GET['time1'];
}
if(isset($_GET['time2'])){
	$time2=$_GET['time2'];
}
if($select_op==1){
	$sql = "

		SELECT hung_collected.name as id, COUNT(distinct hung_collected.Rx_id) as name, ROUND(SUM(hung_collected.unit_price * hung_collected.total_quan), 2) as item FROM `hung_collected`
		WHERE hung_collected.ICD1 = '{$icd}'
		AND
		LEFT(hung_collected.Rx_id, 5) >= {$time1}
		AND
		LEFT(hung_collected.Rx_id, 5) <= {$time2}
		GROUP BY hung_collected.name 
		ORDER BY `item`  DESC
		LIMIT 10
	";
}
elseif ($select_op==2) {
	$sql="
		SELECT t.ATC as id, ATC_WHO.name_cht as id2, SUM(t.num) as name, SUM(t.total) as item FROM (SELECT SUBSTRING(ATC,1,4) as ATC, COUNT(ATC) as num, SUM(ROUND(hung_collected.unit_price * hung_collected.total_quan, 2)) as total
		FROM `hung_collected`
		WHERE ICD1 = '{$icd}'
		AND
		LEFT(hung_collected.Rx_id, 5) >= {$time1}
		AND
		LEFT(hung_collected.Rx_id, 5) <= {$time2}
		GROUP BY ATC
		) as t, ATC_WHO
		WHERE t.ATC = ATC_WHO.ATC_code GROUP BY t.ATC, ATC_WHO.name_cht ORDER BY SUM(t.total) DESC
		LIMIT 10
	";
	}	
elseif ($select_op==3) {
	$sql="
		SELECT t.name as id, t.num as name, t.total as item FROM
		(
		SELECT hung_collected.drug_name as name, COUNT(hung_collected.drug_name) as num, SUM(ROUND(hung_collected.unit_price * hung_collected.total_quan)) as total
		FROM `hung_collected`
		WHERE ICD1 = '{$icd}'
		AND
		LEFT(hung_collected.Rx_id, 5) >= {$time1}
		AND
		LEFT(hung_collected.Rx_id, 5) <= {$time2}
		GROUP BY hung_collected.drug_name ) as t
		ORDER BY t.total DESC
		LIMIT 10
	";
}
elseif ($select_op==4) {
	$sql="
		SELECT manu as id, COUNT(manu) as name, SUM(ROUND(hung_collected.unit_price * hung_collected.total_quan)) as item
		FROM `hung_collected` WHERE ICD1 = '{$icd}'
		AND
		LEFT(hung_collected.Rx_id, 5) >= {$time1}
		AND
		LEFT(hung_collected.Rx_id, 5) <= {$time2}
		GROUP BY id
		ORDER BY item DESC 
		LIMIT 10
	";
}
elseif ($select_op==0) {
	$sql="
		SELECT t.ICD as id, t.cht as name, COUNT(t.ICD) as item FROM
		(
		SELECT DISTINCT hung_collected.rx_id, hung_collected.ICD1 as ICD, ICD_2017.cht_des as cht
		FROM hung_collected, ICD_2017
		WHERE hung_collected.ICD1 = ICD_2017.ICD
		AND
		LEFT(hung_collected.Rx_id, 5) >= {$time1}
		AND
		LEFT(hung_collected.Rx_id, 5) <= {$time2}
		) as t
		GROUP BY t.ICD, t.cht ORDER BY COUNT(t.ICD) DESC LIMIT 10
	";
}
$query = mysqli_query($conn, $sql);

if (!$query) {
	die ('SQL Error: ' . mysqli_error($conn));
}
?>



	
		<caption class="title"></caption>
		<thead>
			<tr>
			<?php
			if($select_op==1){
				echo '<th onclick="sortTable(0)">醫院名稱 ⇅</th>
						<th onclick="sortTable(1)">處方數 ⇅</th>
						<th onclick="sortTable(2)">總金額 ⇅</th>';
			}
			elseif ($select_op==2) {
				echo '<th onclick="sortTable(0)">ATC ⇅</th>
				<th onclick="sortTable(1)">ATC中文名稱 ⇅</th>
				<th onclick="sortTable(2)">藥品開立/次 ⇅</th>
				<th onclick="sortTable(3)">總金額 ⇅</th>';
			}
			elseif ($select_op==3) {
				echo '<th onclick="sortTable(0)">產品名稱 ⇅</th>
				<th onclick="sortTable(1)">藥品開立/次 ⇅</th>
				<th onclick="sortTable(2)">總金額 ⇅</th>';
			}
			elseif ($select_op==4) {
				echo '<th onclick="sortTable(0)">廠商名稱 ⇅</th>
				<th onclick="sortTable(1)">藥品開立/次 ⇅</th>
				<th onclick="sortTable(2)">總金額 ⇅</th>';
			}
			elseif ($select_op==0) {
				echo '<th onclick="sortTable(0)">ICD code ⇅</th>
				<th onclick="sortTable(1)">中文說明 ⇅</th>
				<th onclick="sortTable(2)">處方數 ⇅</th>';
			}
			?>
			</tr>
		</thead>
		<tbody>
		<?php
		$no 	= 1;
		$total 	= 0;
		$id2=null;
		while ($row = mysqli_fetch_array($query))
		{
			if($no==1&&isset($row['id2']))$id2=True;//判斷是否有第二個id
			$item  = $row['item'] == 0 ? '' : number_format($row['item']);
			echo '<tr>';
			if($select_op==0){
				echo '<td id="link">'.$row['id'].'</td>';
			}
			else{
				echo '<td>'.$row['id'].'</td>';
				}
			if($id2){
				echo '<td>'.$row['id2'].'</td>';
			}
			echo   '<td>'.$row['name'].'</td>
					<td>'.$row['item'].'</td>
				  </tr>';
			$total += $row['item'];
			$no++;
		}?>
		</tbody>
		<tfoot>
			<tr>
			<?php
				if($id2){
					echo '<th colspan="3">排行前'.number_format($no-1).' TOTAL</th>';
				}
				else{
					echo '<th colspan="2">排行前'.number_format($no-1).' TOTAL</th>';
				}
			?>
				<th><?=number_format($total)?></th>
			</tr>
		</tfoot>
	
