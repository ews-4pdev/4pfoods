/**
 *	Compiler Functions: These functions must remain exactly as they are in order for the compiler
 *	to function properly produce executable code!
 */

function isiPhone(){
  return (
    //Detect iPhone
    (navigator.platform.indexOf("iPhone") != -1) ||
    //Detect iPod
    (navigator.platform.indexOf("iPod") != -1)
  );
} 
 
function toMoney(amount) {
  return "$"+amount.toFixed(2);
}

//For use in the compiling process only. Should be used in source code to wrap the
//name for an Ajax task name so the compiler targets it in task decoding
function task(task) {
	return task;
}

//For use in the compiling process. In Source Mode, it does nothing,
//but the compiler will replace calls to this function with the task code
function ttask(input) {
	return input;
}

//For use with compiled build files. Wraps the string at the passed index in the $() function
function v(num) {
	return $('#'+s_[num]);
}

function $defined(checkMe) {
  return (typeof checkMe != 'undefined');
}

//For use with compiled build files. Wraps the string at the passed index in $defined($(**))
function d(num) {
	return ($defined(v(num)));
}

//For use with compiled build files. Outputs the string at the passed index with a "." before it.
//Normal context is to translate $$('.someclass') into p(#)
function p(num) {
	return "."+s_[num];
}

//A shortcut for the most common use of the p() function
function q(num) {
	return $(p(num));
}

function getAllTds($id){
	var data = [];
	var attribute_value = null;

	$('#hidden_'+$id).find('input').each (function( index, value) {
		var name = $(this).attr('name');
		if (typeof name !== typeof undefined && name !== false) {

			attribute_value = $(this).attr('value');
			if(attribute_value.indexOf("$") > -1){
				attribute_value = attribute_value.replace("$", "");
			}
			data[name] = attribute_value;
		}
	});
	return data;
}

function fillForm(formId, data){
	    var id = null;
		for(var key in data){
		if (data.hasOwnProperty(key)) {
			id = $('#'+key);

			switch (id.prop('nodeName')) {
				case "SELECT":
					 $('#'+key+' option').filter(function() {
						if( $(this).text() === data[key] ){
							$(this).attr('selected', 'selected');
						}
					});
					break;
				case "INPUT":
					if(id.is(':checkbox')){
						id.prop('checked', (data[key] == 1) ? true : false);
					}else{
						id.val(data[key]);
					}
					break;
				case "TEXTAREA":
					id.val(data[key]);
					break;
			}
		}
	}
}

function hide_products(id) {
	$('#'+id).toggle(50);
}

function isInt(value) {
	var x;
	if (isNaN(value)) {
		return false;
	}
	x = parseFloat(value);
	return (x | 0) === x;
}

function sortArray(arr){

	var zip = arr.Zip;
	var distance = arr.distanceArray;

	for(var i in zip){
		if( zip.hasOwnProperty(i) ){
		loop1:
			for(var k in distance){
				if( distance.hasOwnProperty(k) ){
					if( distance[k].zip_code == zip[i].Zip ){
						zip[i].distance = distance[k].distance;
					}
				}
			}
		}
	}

	return zip;
}

function parseDate(input, format) {
	format = format || 'yyyy-mm-dd'; // default format
	var parts = input.match(/(\d+)/g),
			i = 0, fmt = {};
	// extract date-part indexes from the format
	format.replace(/(yyyy|dd|mm)/g, function(part) { fmt[part] = i++; });

	return new Date(parts[fmt['yyyy']], parts[fmt['mm']]-1, parts[fmt['dd']]);
}


function sliceObject(obj){
	for(var i in obj){
		if( obj.hasOwnProperty(i) ){
			if(typeof obj[i] == 'object'){
				sliceObject(obj[i]);
			}else{
				obj[i] =  createDate(obj[i]);
			}
		}
	}
	return obj;
}

function createDate(date){
	var nd = new Date( date );
	var ndd = nd.getDate();
	var ndm = nd.getMonth()+1;
	var ndy = nd.getFullYear();
	var ndg = [parseInt(ndy), parseInt(ndm), parseInt(ndd)];
	return new Date(ndg);
}

var weekdays = new Array(7);
weekdays[0] = "Sunday";
weekdays[1] = "Monday";
weekdays[2] = "Tuesday";
weekdays[3] = "Wednesday";
weekdays[4] = "Thursday";
weekdays[5] = "Friday";
weekdays[6] = "Saturday";

/**
//These functions, together with the extensions of the Element class, allow common processes to be
//minified and obfuscated. Additionally, their use in the compiler reduces the size of the output
//build file by maximize the reuse of numerical characters
function c1_(elNum, classNum) {
	return (v(elNum).c1(classNum));
}
function c2_(elNum, classNum) {
	v(elNum).c2(classNum);
}
function c3_(elNum, classNum) {
	v(elNum).c3(classNum);
}
Element.implement({
	c1: function(num){ return this.hasClass(c_[num]); },
	c2: function(num){ return this.addClass(c_[num]); },
	c3: function(num){ return this.removeClass(c_[num]); }
});
**/

/** End Compiler Functions **/
