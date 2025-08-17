
function currency_format(oObj)
{
	num = oObj.aData[oObj.iDataColumn];
	num = isNaN(num) || num === '' || num === null ? 0.00 : num;
	return "$" + parseFloat(num).toFixed(2);
}

// sort by oracle's date format: 15-JUN-11 -----------------------------------
jQuery.fn.dataTableExt.oSort['oracle_date-asc']  = function(a,b) {
	if ((a == '') && (b == '')) return(0);
	if (a == '') return(-1);
	if (b == '') return(1);

	var parts_a = a.split('-');
	var parts_b = b.split('-');
     
	x = new Date(year_digits(parts_a[2]), month_digits(parts_a[1]), parts_a[0]);
	y = new Date(year_digits(parts_b[2]), month_digits(parts_b[1]), parts_b[0]);
	return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['oracle_date-desc'] = function(a,b) {
	if ((a == '') && (b == '')) return(0);
	if (a == '') return(1);
	if (b == '') return(-1);

	var parts_a = a.split('-');
	var parts_b = b.split('-');

	x = new Date(year_digits(parts_a[2]), month_digits(parts_a[1]), parts_a[0]);
	y = new Date(year_digits(parts_b[2]), month_digits(parts_b[1]), parts_b[0]);
	return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
};

function year_digits(str) {  // convert to 4 digit year
	if (str.length <= 2) {
		if (str > 50)  // 1950 is the cutoff year
			return("19" + str);
		else
			return("20" + str);
	}
	else
		return(str);
}

function month_digits(str) { // convert to digits for month
	var months = new Array("JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC");
	for (var i = 0; i < months.length; i++) {
		if (months[i] == str)
			return(i);
	}
	return(-1);
}
