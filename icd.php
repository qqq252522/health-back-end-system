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

$sql = "

SELECT t.ICD as id, t.cht as name, COUNT(t.ICD) as item FROM
(
SELECT DISTINCT hung_collected.rx_id, hung_collected.ICD1 as ICD, ICD_2017.cht_des as cht
FROM hung_collected, ICD_2017
WHERE hung_collected.ICD1 = ICD_2017.ICD
AND
LEFT(hung_collected.Rx_id, 5) >= 10601
AND
LEFT(hung_collected.Rx_id, 5) <= 10612
) as t
GROUP BY t.ICD, t.cht ORDER BY COUNT(t.ICD) DESC LIMIT 10

";
		
$query = mysqli_query($conn, $sql);

if (!$query) {
	die ('SQL Error: ' . mysqli_error($conn));
}
?>
<html>
<head>
	<title>Displaying MySQL Data in HTML Table</title>

</head>
<body>
<script type="text/javascript" src="jquery.min.js">
	var icd='';//給tablinks button 的 tableText函式用的變數
	var time_1;
	var time_2;//tableText的時間變數
	var selection;
</script>a
<script type="text/javascript" src="sortTable.js"></script>
<div class="navbar">
  <a href="icd.php">ICD 排行</a>
  <a href="atc.php">ATC 排行</a>
  <a href="index.php">處方來源排行(院所)</a>
</div>
	<h1 id = "header">ICD 排行</h1>
	<div class="tab" style="z-index: -1; position: absolute;"></div>
	<div class="tab" id="tab">
		<button class="tablinks" onclick="window.location.href='icd.php'">上一層</button>
		<button class="tablinks" onclick="selection=1;tableText(icd,time_1,time_2,selection)">來源排行</button><!--第二層的三個button-->
		<button class="tablinks" onclick="selection=2;tableText(icd,time_1,time_2,selection)">ATC排行</button>
		<button class="tablinks" onclick="selection=3;tableText(icd,time_1,time_2,3)">產品排行</button>
		<button class="tablinks" onclick="tableText(icd,time_1,time_2,4)">廠商排行</button>
	</div>
	<div class="dd-group">
		<div class="dropdown">
			<button class="dropbtn">年份</button> 
			<div class="dropdown-content" id="year">
			<a href="#" onclick="time_1=10401;time_2=10412;tableText(icd,time_1,time_2,selection)">2015</a>
			<a href="#" onclick="time_1=10501;time_2=10512;tableText(icd,time_1,time_2,selection)">2016</a>
			<a href="#" onclick="time_1=10601;time_2=10612;tableText(icd,time_1,time_2,selection)">2017</a>
			</div>
		</div>
		<div class="dropdown">
			<button class="dropbtn">季度</button>
			<div class="dropdown-content" id="quarter">
				<a href="#" onclick="time_1=combine(year(time_1),'01');time_2=combine(year(time_1),'03');tableText(icd,time_1,time_2,selection)">Q1</a>
				<a href="#" onclick="time_1=combine(year(time_1),'04');time_2=combine(year(time_1),'06');tableText(icd,time_1,time_2,selection)">Q2</a>
				<a href="#" onclick="time_1=combine(year(time_1),'07');time_2=combine(year(time_1),'09');tableText(icd,time_1,time_2,selection)">Q3</a>
				<a href="#" onclick="time_1=combine(year(time_1),'10');time_2=combine(year(time_1),'12');tableText(icd,time_1,time_2,selection)">Q4</a> 
			</div>
		</div>
		<div class="dropdown">
			<button class="dropbtn">月份</button>
			<div class="dropdown-content" id="quarter">
				<a href="#" onclick="time_1=combine(year(time_1),'01');tableText(icd,time_1,time_1,selection)">一月</a>
				<a href="#" onclick="time_1=combine(year(time_1),'02');tableText(icd,time_1,time_1,selection)">二月</a>
				<a href="#" onclick="time_1=combine(year(time_1),'03');tableText(icd,time_1,time_1,selection)">三月</a>
				<a href="#" onclick="time_1=combine(year(time_1),'04');tableText(icd,time_1,time_1,selection)">四月</a>
				<a href="#" onclick="time_1=combine(year(time_1),'05');tableText(icd,time_1,time_1,selection)">五月</a>
				<a href="#" onclick="time_1=combine(year(time_1),'06');tableText(icd,time_1,time_1,selection)">六月</a>
				<a href="#" onclick="time_1=combine(year(time_1),'07');tableText(icd,time_1,time_1,selection)">七月</a>
				<a href="#" onclick="time_1=combine(year(time_1),'08');tableText(icd,time_1,time_1,selection)">八月</a>
				<a href="#" onclick="time_1=combine(year(time_1),'09');tableText(icd,time_1,time_1,selection)">九月</a>
				<a href="#" onclick="time_1=combine(year(time_1),'10');tableText(icd,time_1,time_1,selection)">十月</a>
				<a href="#" onclick="time_1=combine(year(time_1),'11');tableText(icd,time_1,time_1,selection)">十一月</a>
				<a href="#" onclick="time_1=combine(year(time_1),'12');tableText(icd,time_1,time_1,selection)">十二月</a>
			</div>
		</div> 
	</div>
	<table class="data-table" id = "tableID">
		<thead>
			<tr>
				<th onclick="sortTable(0)">ICD code ⇅</th>
				<th onclick="sortTable(1)">中文說明 ⇅</th>
				<th onclick="sortTable(2)">處方數 ⇅</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$no 	= 1;
		$total 	= 0;
		while ($row = mysqli_fetch_array($query))
		{
			if($total==0)$max=$row['item'];
			$percent=$row['item']/$max*100*0.8;
			$item  = $row['item'] == 0 ? '' : number_format($row['item']);
			echo '<tr>
					<td id="link">'.$row['id'].'</td>
					<td>'.$row['name'].'</td>
					<td>'.$row['item'].'</td>
				  </tr>';
			$total += $row['item'];
			$no++;
		}?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="2">排行前<?=number_format($no-1)?> TOTAL</th>
				<th><?=number_format($total)?></th>
			</tr>
		</tfoot>
	</table>
	<script type="text/javascript">
			$("#tab").hide();//一開始先讓上一層和第二層的三個button hide
			var old="";
			icd='';
			selection=0;
			time_1=10601;
			time_2=10612;
			var table = document.getElementById("tableID");
			tableBar(2);
			if (table != null) {
				for (var i = 1; i < table.rows.length; i++) {
		           for (var j = 0; j < table.rows[i].cells.length; j++)
		           table.rows[i].cells[0].onclick = function () {//這可以偵測點哪格 並把那格的數值傳給tableText函式
		               selection=1;
					   tableText(this.innerHTML,time_1,time_2,selection);
		               var icd_ch = this.parentNode.cells[1].innerHTML;//把ICD的中文標題儲存給變數 讓下一個頁面可以用到
		               icd=this.innerHTML;
		               document.getElementById("header").innerHTML="ICD:"+icd+"  "+icd_ch+"排行";
		               //顯示類似於 ICD:I10 本態性(原發性)高血壓排行
		               $("#tab").show();
		           };
		       }
		   }
		   function $_xmlHttpRequest()
			{   
			    if(window.ActiveXObject)
			    {
			        xmlHTTP=new ActiveXObject("Microsoft.XMLHTTP");
			    }
			    else if(window.XMLHttpRequest)
			    {
			        xmlHTTP=new XMLHttpRequest();
			    }
			}
		   
		   function tableText(value,time1,time2,select) {//value為icd,select為選項
		    $_xmlHttpRequest();
		    xmlHTTP.open("GET","icd_2.php?icd="+value+"&time1="+time1+"&time2="+time2+"&select_op="+select,true);
	    
	        xmlHTTP.onreadystatechange=function check_user()
	        {
	            if(xmlHTTP.readyState == 4)
	            {
	                if(xmlHTTP.status == 200)
	                {
	                	old=document.getElementById("tableID");
	                    var str=xmlHTTP.responseText;
	                    document.getElementById("tableID").innerHTML=str;
	                    if(select==2){//ATC的時候畫2 3欄
	                    	tableBar(2);
	                    	tableBar(3);
	                    }
	                    else if(select==0){
	                    	var table = document.getElementById("tableID");
							tableBar(2);
							if (table != null) {
								for (var i = 1; i < table.rows.length; i++) {
						           for (var j = 0; j < table.rows[i].cells.length; j++)
						           table.rows[i].cells[0].onclick = function () {//這可以偵測點哪格 並把那格的數值傳給tableText函式
						               selection=1;
									   tableText(this.innerHTML,time_1,time_2,selection);
						               var icd_ch = this.parentNode.cells[1].innerHTML;//把ICD的中文標題儲存給變數 讓下一個頁面可以用到
						               icd=this.innerHTML;
						               document.getElementById("header").innerHTML="ICD:"+icd+"  "+icd_ch+"排行";
						               //顯示類似於 ICD:I10 本態性(原發性)高血壓排行
						               $("#tab").show();
						           };
						       }
						   }
	                    }
	                    else{//其他畫1 2欄
		                    tableBar(1);
		                    tableBar(2);
		                }
	                }
	            }
	        }
	        xmlHTTP.send(null);
		   }
		   
			function tableBar(col){
				var max = 0;
				for(var i = 1;i<table.rows.length-1; i++){
					if(parseInt(max) < parseInt(table.rows[i].cells[col].innerHTML)){
						max = parseInt(table.rows[i].cells[col].innerHTML);//找那欄最大的數字  以此作為百分比基準
					}
				}
				var percent = 0;
				for(var i = 1; i<table.rows.length-1; i++){
					now = table.rows[i].cells[col].innerHTML;//為了等等能丟到div裡面
					percent = now/max*100*0.8;//百分比乘以0.8以防Bar太長
					$("tr:eq("+i+")>td:eq("+col+")").css({width:"25%"});//為td新增css class 以防長條圖太長
					$("tr:eq("+i+")>td:eq("+col+")").empty().prepend("<div>"+now+"</div>");//用empty先清空 然後新增div(要分成<td><div></div></td>才能畫長條圖)
					$("tr:eq("+i+")>td:eq("+col+")>div").css({
						"text-align": "right",//向右對齊
						color: "black",//文字顏色
						width:percent+"%",//長條圖長度
						"background-color": "#8CC8FF"//長條圖底色
					});
				}
			}
			function year(time){
			    var str = time.toString();
			    return str.substring(0,3);
			}
			function combine(year,season){
			    return parseInt(year+season);
			}
		</script>
	<link rel=stylesheet type="text/css" href="external-stylesheet.css">

</body>
</html>
