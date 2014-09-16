var scr = document.createElement("script");
scr.src = "http://code.jquery.com/jquery-1.9.1.min.js";
document.body.appendChild(scr);

if (document.forms[0].txt.value.length >= 1)
{
	document.forms[0].submit();
}

var x = [];
var i = 0;
//for (var i=0;i<26;i++){
timer = setInterval(function(){
	if (i>=26*26)
		clearInterval(timer);
	var agadi = i/26;
	var pichadi = i%26;
	var str = String.fromCharCode(agadi + 97);
	str += String.fromCharCode(pichadi + 97);
	document.forms[0].txt.value= str;
	document.forms[0].submit();
	var arr = document.getElementsByTagName('iframe')[0].contentDocument.getElementsByTagName('a');
	/*x[i] = new Array(arr.length);
	for (var j=0;j<arr.length;j++){
		x[i][j]=arr[j].href;
	}*/
	x.push(arr);
	i++;
},2000);

for (var k=0;k<1;k++){
	for (var l=0;l<x[k].length;l++){
		console.log(x[k][l]);
	}
}

//FlipKart
for(var i=0;i<10;i++){
	$(".next-div .icon").click();
	$("#page1 .page fkt").each(function(){console.log($(this).text());});
}