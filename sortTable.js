function sortTable(n) {
	var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0, isNum = false;
	table = document.getElementById("tableID");
	switching = true;
	//Set the sorting direction to descending:
	dir = "desc"; 
	/*Make a loop that will continue until
	no switching has been done:*/
	while (switching) {
    //start by saying: no switching is done:
    switching = false;
    rows = table.getElementsByTagName("TR");
    if(rows[1].getElementsByTagName("TD")[n].innerHTML.charAt(0)!='<'){//這種if是拿來判斷這字串是否為'<'開頭(因為tableBar把td的內容改為div形式  下面還有用到這種if)
	    if(!isNaN(parseInt(rows[1].getElementsByTagName("TD")[n].innerHTML))){
	    	isNum = true;//isNum是個boolean來判斷表格內容是否為數字,用內建函式isNaN來幫助判斷
	    }
	}
	else{//如果為div時
		if(!isNaN(parseInt(rows[1].getElementsByTagName("TD")[n].childNodes[0].innerHTML))){//childNodes[0]可以準確抓到div
	    	isNum = true;//isNum是個boolean來判斷表格內容是否為數字,用內建函式isNaN來幫助判斷
	    }
	}
    /*Loop through all table rows (except the
    first, which contains table headers):*/
    for (i = 1; i < (rows.length - 1); i++) {
      //start by saying there should be no switching:
      shouldSwitch = false;
      /*Get the two elements you want to compare,
      one from current row and one from the next:*/
      if(rows[i].getElementsByTagName("TD")[n]!=null){
	      if(rows[i].getElementsByTagName("TD")[n].innerHTML.charAt(0)!='<'){
	      	x = rows[i].getElementsByTagName("TD")[n];//這邊沒有這個if會出錯
	      }
	      else{
	      	x = rows[i].getElementsByTagName("TD")[n].childNodes[0];
	      }
      }
      if(rows[i + 1].getElementsByTagName("TD")[n]!=null){
      	if(rows[i+1].getElementsByTagName("TD")[n].innerHTML.charAt(0)!='<'){
      		y = rows[i + 1].getElementsByTagName("TD")[n];
      	}
      	else{
      		y = rows[i + 1].getElementsByTagName("TD")[n].childNodes[0];
      	}
      }
      /*check if the two rows should switch place,
      based on the direction, asc or desc:*/
      if (dir == "asc") {
      	if(isNum){
	        if (parseInt(x.innerHTML) > parseInt(y.innerHTML)) {//如果為數字就將內容轉為數字再做運算
	          //if so, mark as a switch and break the loop:
	          shouldSwitch= true;
	          break;
	        }
	    }
	    else{
	    	if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {//不是數字就轉為小寫
	          //if so, mark as a switch and break the loop:
	          shouldSwitch= true;
	          break;
	        }
	    }
      } else if (dir == "desc") {
        if(isNum){
	        if (parseInt(x.innerHTML) < parseInt(y.innerHTML)) {
	          //if so, mark as a switch and break the loop:
	          shouldSwitch= true;
	          break;
	        }
	    }
	    else{
	    	if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
	          //if so, mark as a switch and break the loop:
	          shouldSwitch= true;
	          break;
	        }
	    }
	  }
    }
    if (shouldSwitch) {
      /*If a switch has been marked, make the switch
      and mark that a switch has been done:*/
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      //Each time a switch is done, increase this count by 1:
      switchcount ++; 
    } else {
      /*If no switching has been done AND the direction is "asc",
      set the direction to "desc" and run the while loop again.*/
      if (switchcount == 0 && dir == "desc") {
        dir = "asc";
        switching = true;
      }
    }
  }
}
