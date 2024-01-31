<SCRIPT language=javascript>
// 기본설정
var value_ctl;
var color;
var bg_ctl;
var mode_arr = new Array();
mode_arr[3] = 0;
mode_arr[4] = 0;
mode_arr[5] = 255;

// 사용자 설정
var marginLeft = 8;
var marginHeight = 10;
var init_mode = 3;

function init() {
	var	 bPageLoaded = false;
	drawBody();
	color = new colorSet( 51, 5, 20 );
	drawPallete( init_mode, mode_arr[init_mode] );
	document.all['COLORTABLE'].onmousemove = palleteStatus;
	document.all['COLORTABLE'].onmousedown = palleteSelect;
	document.all['ADJUST'].onmousemove = adjustStatus;
	document.all['ADJUST'].onmousedown = adjustSelect;
}

function ColorPicker( ctl1, ctl2, ctl3 ) {
	var	 leftpos = 0;
	var	 toppos = 0;
	var cur_hcolor = '';

	if( bPageLoaded ) {
		value_ctl = ctl1;
		bg_ctl = ctl3;
		ctl2.value = ( ctl2.value ) ? ctl2.value : '#000000';
		cur_hcolor = ctl2.value.substring(0,1) == '#' ? ctl2.value.substring( 1, ctl2.value.length ) : ctl2.value;
		document.status.current.value = cur_hcolor;
		document.all['SAMPLE'].style.backgroundColor = cur_hcolor;
	}
	return;
}

function colorSet( cells, cellSize, adjustSize ) {
	this.rgb = new Array();
	this.current = new Array();
	this.cells = cells;
	this.cellSize = cellSize;
	this.adjustSize = adjustSize;
	this.curAdjust = 0;
	this.hueMode = 0;
	this.table = "<table border=0 cellspacing=0 cellpadding=0 height=" + (cells+1)*cellSize + ">";
	this.col = "<col width=" + adjustSize + ">";
	this.tr = "<tr height=" + cellSize + ">";
	this.target = 'bTextColor';
}

function drawPallete( mode, value ) {
	color.mode = mode;
	color.value = value;
	colorInit();
	drawTable();
	drawAdjust();
}

function colorInit() {
	if( color.mode <= 2 ) {
		initRgbBase(color.value);
		setCurrentColor(color.value,0,0);
		color.degree = getHueDegree(color.current);
	}
	else if( color.mode == 3 ) {
		color.degree = color.value;
		initHueBase(color.degree);
		setCurrentColor(255,color.hueValue,0);
	}
	else {
		color.degree = changeScale(color.value,255,360);
		initHueBase(0);
		setCurrentColor(255,color.hueValue,0);
	}
}


function drawBody() {
	document.write( "" +
		"<body topmargin=" + marginHeight + " leftmargin=" + marginLeft + ">" +
		"<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=275><tr><td>" +
		"<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">" +
		"	<tr>" +
		"		<td id=\"COLORTABLE\" width=255></td>" +
		"		<td id=\"ADJUST\"><div id=div_adjust></div></td>" +
		"	</tr>" +
		"</table>" +
		"<table width=\"100%\" border=\"0\" cellspacing=\"5\" cellpadding=\"0\">" +
		"	<tr>" +
		"		<td id=\"SAMPLE\" height=\"30\" width=30%></td>" +
		"		<td id=\"SELECT\" width=30%></td>" +
		"		<td rowspan=2 width=40%>" +
		"		<BUTTON onclick=\"drawPallete(5,255);\" style=\"width: 100%; cursor: hand; color: black; background-color: #efefef; border-right: #cccccc 1px solid; border-top: #cccccc 1px solid; border-left: #cccccc 1px solid; border-bottom: #cccccc 1px solid; font-size: 8pt; font-family: tahoma;\" onfocus=\"blur();\"><b>B</b>rightness</BUTTON><br>" +
		"		<BUTTON onclick=\"drawPallete(4,0);\" style=\"width: 100%; cursor: hand; color: black; background-color: #efefef; border-right: #cccccc 1px solid; border-top: #cccccc 1px solid; border-left: #cccccc 1px solid; border-bottom: #cccccc 1px solid; font-size: 8pt; font-family: tahoma;\" onfocus=\"blur();\"><b>S</b>aturation</BUTTON><br>" +
		"		<BUTTON onclick=\"drawPallete(3,0);\" style=\"width: 100%; cursor: hand; color: black; background-color: #efefef; border-right: #cccccc 1px solid; border-top: #cccccc 1px solid; border-left: #cccccc 1px solid; border-bottom: #cccccc 1px solid; font-size: 8pt; font-family: tahoma;\" onfocus=\"blur();\"><b>H</b>ue</BUTTON>" +
		"		</td>" +
		"	</tr>" +
		"<FORM name=status>" +
		"	<tr>" +
		"		<td><INPUT size=5 value='000000' name=\"current\" style=\"border-right: #cccccc 0px solid; border-top: #cccccc 0px solid; border-left: #cccccc 0px solid; border-bottom: #cccccc 1px solid; font-size: 8pt; font-family: tahoma;\"></td>" +
		"		<td><INPUT size=5 value='000000' name=\"text\" style=\"border-right: #cccccc 0px solid; border-top: #cccccc 0px solid; border-left: #cccccc 0px solid; border-bottom: #cccccc 1px solid; font-size: 8pt; font-family: tahoma;\"></td>" +
		"	</tr>" +
		"</FORM>" +
		"</table>" +
		"<TABLE cellSpacing=0 cellPadding=0 border=0 width=100%>" +
		"	<TR align=middle> " +
		"		<TD>" +
		"		<BUTTON onclick=\"applyChannelColor();\" style=\"width: 100px; cursor: hand; color: black; background-color: #efefef; border-right: #cccccc 1px solid; border-top: #cccccc 1px solid; border-left: #cccccc 1px solid; border-bottom: #cccccc 1px solid; font-size: 8pt; font-family: tahoma;\" onfocus=\"blur();\">선택</BUTTON>" +
		"&nbsp;" +
		"		<BUTTON onclick=\"self.close();\" style=\"width: 100px; cursor: hand; color: black; background-color: #efefef; border-right: #cccccc 1px solid; border-top: #cccccc 1px solid; border-left: #cccccc 1px solid; border-bottom: #cccccc 1px solid; font-size: 8pt; font-family: tahoma;\" onfocus=\"blur();\">닫기</BUTTON>" +
		"		</TD>" +
		"	</TR>" +
		"</TABLE>" +
		"</td></tr></table>" +
		"</body>"
	);
	bPageLoaded = true;
}

function drawTable() {
	html = color.table;
	range = getRange( 0, 255, color.cells );
	for ( i = 0; i <= color.cells; i++ )
		html += "<col width=" + color.cellSize + ">";
	if( color.mode <= 2 ) {
		for( i = color.cells; i >= 0; i-- ) {
			html += color.tr;
			for( j = 0; j <= color.cells; j++ ) {
				setColor( color.value,range[i],range[j] );
				html += getPalleteCells();
			}
			html += "</tr>";
		}
	}
	else if( color.mode == 3 ) {
		end = getRange( 0, color.hueValue, color.cells );
		for( i = color.cells; i >= 0; i-- ) {
			html += color.tr;
			for( j = color.cells; j >= 0; j-- ) {
				setColor( range[i], getCoord(end[i],range[i],j,color.cells), getCoord( 0, range[i], j,color.cells ) );
				html += getPalleteCells();
			}
			html += "</tr>";
		}
	}
	else if( color.mode == 4 ) {
		for( i = color.cells; i >= 0; i-- ) {
			html += color.tr;
			for( j = 0; j <= color.cells; j++ ) {
				degree = changeScale( j, ( color.cells + 1 ), 360 );
				initHueBase( degree )
				max=getCoord( color.hueValue, 255, color.value, 255 );
				setColor( range[i], getCoord( 0, max, i, color.cells ), getCoord( 0, color.value, i, color.cells ) );
				html += getPalleteCells();
			}
			html += "</tr>";
		}
	}
	else if( color.mode == 5 ) {
		for( i = 0; i <= color.cells; i++ ) {
			html += color.tr;
			for( j = 0; j <= color.cells; j++ ) {
				degree = changeScale( j, ( color.cells + 1 ), 360 );
				initHueBase( degree )
				min = getCoord( 0, color.hueValue, color.value, 255 );
				setColor( color.value, getCoord( min, color.value, i, color.cells ), getCoord( 0, color.value, i, color.cells ) );
				html += getPalleteCells();
			}
			html += "</tr>";
		}
	}
	html += "</table>";
	paintPallete( "COLORTABLE" );
}

function palleteSelect(){
	applySelection();
	color.current=new Array(color.rgb[0],color.rgb[1],color.rgb[2]);
	if (color.mode !=3)	drawAdjust();
}

function drawAdjust(){
	html  = color.table+color.col;
	if (color.mode <=2){
		for (i=color.cells;i>=0;i--){
			setColor(getCoord(0,255,i,color.cells),color.current[1],color.current[2]);
			html +=getAdjustCells();
		}
	}
	else if (color.mode ==3){
		for (i=color.cells;i>=0;i--){
			degree=changeScale(i,(color.cells+1),360);
			initHueBase(degree);
			setColor(255,color.hueValue,0);
			html += getAdjustCells();
		}
	}
	else if (color.mode ==4){
		for (i=0;i<=color.cells;i++){
			setColor(color.current[color.base[0]],getCoord(color.current[color.base[1]],color.current[color.base[0]],i,color.cells),getCoord(0,color.current[color.base[0]],i,color.cells));
			html +=getAdjustCells();
		}
	}
	else if (color.mode ==5){
		range = getRange(0,255,color.cells);
		for (i=color.cells;i>=0;i--){
			setColor(range[i],getCoord(0,color.current[color.base[1]],i,color.cells),getCoord(0,color.current[color.base[2]],i,color.cells));
			html +=getAdjustCells();
		}
	}
	html +="</table>";
	paintPallete("ADJUST");
}

function palleteStatus() {
	obj = document.all['COLORTABLE'];
	crdX = changeScale( event.clientX - marginLeft, obj.clientWidth, 255 );
	crdY = changeScale( event.clientY - marginHeight, obj.clientHeight, 255 );
	if (color.mode <=2){
		setColor(color.value,255-crdY,crdX);
		color.degree=getHueDegree(color.rgb);
	}
	else if (color.mode ==3){
		crdY=255-crdY;
		crdX=255-crdX;
		initHueBase(color.value);
		start = getCoord(0,color.hueValue,crdY,255);
		setColor(crdY,getCoord(start,crdY,crdX,255),getCoord(0,crdY,crdX,255));
	}
	else if (color.mode ==4){
		crdY=255-crdY;
		color.degree=changeScale(crdX,255,360);
		initHueBase(color.degree);
		max=getCoord(color.hueValue,255,color.value,255);
		setColor(crdY,getCoord(0,max,crdY,255),getCoord(0,color.value,crdY,255));
	}
	else if (color.mode ==5){
		color.degree=changeScale(crdX,255,360);
		initHueBase(color.degree);
		min=getCoord(0,color.hueValue,color.value,255);
		setColor(color.value,getCoord(min,color.value,crdY,255),getCoord(0,color.value,crdY,255));
	}
	applyColor();
}

function adjustStatus(){
	obj = document.all['ADJUST'];
	crdY = changeScale( event.y - marginHeight, obj.clientHeight, 255 );
	if (color.mode <=2){
		color.adjust = 255-crdY;
		setColor(color.adjust,color.current[color.base[1]],color.current[color.base[2]]);
		color.degree=getHueDegree(color.rgb);
	}
	else if (color.mode ==3){
		color.degree=color.adjust = changeScale(255-crdY,255,360);
		initHueBase(color.adjust);
		setColor(255,color.hueValue,0);
	}
	else if (color.mode==4){
		color.adjust=crdY;
		setColor(color.current[color.base[0]],getCoord(color.current[color.base[1]],color.current[color.base[0]],crdY,255),getCoord(color.current[color.base[2]],color.current[color.base[0]],crdY,255));
	}
	else if (color.mode==5){
		color.adjust=255-crdY;
		setColor(getCoord(0,color.current[color.base[0]],color.adjust,255),getCoord(0,color.current[color.base[1]],color.adjust,255),getCoord(0,color.current[color.base[2]],color.adjust,255));
	}
	applyColor();
}

function applyColor(){
	color.hexValue=getHexValue();
	document.status.current.value=color.hexValue;
	document.all['SAMPLE'].style.backgroundColor=color.hexValue;
}

function applySelection(){
	obj=document.all['SELECT'];
	if (color.target=='bBgColor'){
		obj.style.backgroundColor=color.hexValue;
		document.status.background.value=color.hexValue;
	}
	else{
		obj.style.backgroundColor=color.hexValue;
		document.status.text.value=color.hexValue;
	}
}

function adjustSelect(){
	applySelection();
	color.value = color.adjust;
	drawTable();
}

function setCurrentColor(base0,base1,base2){
	color.current[color.base[0]]=base0;
	color.current[color.base[1]]=base1;
	color.current[color.base[2]]=base2;
}

function setColor(base0,base1,base2){
	color.rgb[color.base[0]]=base0;
	color.rgb[color.base[1]]=base1;
	color.rgb[color.base[2]]=base2;
}

function paintPallete(id){
	colorTable=document.all[id];
	colorTable.innerHTML='';
	colorTable.innerHTML=html;
}

function getPalleteCells(){					return "<td bgcolor="+ getHexValue() + "></td>";				}
function getHexValue(){						return hex(color.rgb[0])+hex(color.rgb[1])+hex(color.rgb[2]);	}
function getAdjustCells(){					return color.tr + getPalleteCells()+"</tr>";					}
function getCoord(start,end,pos,cellNum){	return  start + parseInt( (end-start)*pos/cellNum);				}
function getSatPercent(){					return changeScale((255 - color.rgb[color.base[2]]),255,100);	}

function initRgbBase(value){
	if (color.mode==0)
		color.base=new Array(0,1,2);
	else if (color.mode==1)
		color.base=new Array(1,0,2);
	else
		color.base=new Array(2,1,0);
}

function getHueDegree(rgb){
	r=rgb[0];
	g=rgb[1];
	b=rgb[2];
	if (r==b && r==g && b==g) return 0;
	else if (r==b) return 300;
	else if (r==g) return 60;
	else if (b==g) return 180;
	else{
		if (r>g && r>b){
			if (g>b)	base=new Array(0,1,2);
			else		base=new Array(0,2,1);
		}
		else if (g>r && g>b){
			if (b>r)	base=new Array(1,2,0);
			else		base=new Array(1,0,2);
		}
		else if (b>g && b>r){
			if (r>g)	base=new Array(2,0,1);
			else		base=new Array(2,1,0);
		}
		f=rgb[base[0]];
		v=rgb[base[1]];
		n=rgb[base[2]];
		baseDegree=base[0]*120;
		if (n>v)	degree = baseDegree - parseInt(60*(n-v)/(f-v));
		else		degree = baseDegree + parseInt(60*(v-n)/(f-n));
		if (degree <0)	degree +=360;
		return degree;
	}
}

function initHueBase(degree){
	degree=degree % 360;
	if (degree < 60)	color.base=new Array(0,1,2);
	else if (degree < 120)	color.base=new Array(1,0,2);
	else if (degree < 180)	color.base=new Array(1,2,0);
	else if (degree < 240)	color.base=new Array(2,1,0);
	else if (degree < 300)	color.base=new Array(2,0,1);
	else if (degree < 360)	color.base=new Array(0,2,1);
	huePos=degree%60;
	if (degree %120 >= 60)	huePos=60 - huePos;
	color.hueValue = changeScale(huePos,60,255);
}

function changeScale(value,oldScale,newScale){
	newValue= parseInt(value*newScale/oldScale);
	if (newValue > newScale)	newValue=newScale;
	return newValue;
}

function getRange(start,end,cellNum){
	section=new Array();
	for (k=0;k<=cellNum;k++)	section[k]=getCoord(start,end,k,cellNum);
	return section;
}

function hex (dec) {
	var Hexstring = "0123456789ABCDEF";
	var a = dec % 16;
	var b = (dec - a)/16;
	value = "" + Hexstring.charAt(b) + Hexstring.charAt(a);
	return value;
}

function on_event_load(){	return false;	}
document.onselectstart = on_event_load;
document.ondragstart = on_event_load;
document.oncontextmenu = on_event_load;

function applyChannelColor(){
	/*
	if(typeof(opener)!='undefined'){
		if(typeof(opener.SetColor)=='function')
			opener.SetColor(document.status.text.value);
		if(typeof(opener.ColorCode)=='string')
			opener.ColorCode.value='#'+document.status.text.value;
	}
	*/
	if( typeof( value_ctl ) == 'object' ) {
		value_ctl.value = '#' + document.status.text.value;
	}
	/*
	if(typeof(bg_ctl)=='object'){
		if(typeof(bg_ctl.style)=='object')
			if(typeof(bg_ctl.style.backgroundColor)!='undefined')
				bg_ctl.style.backgroundColor='#'+document.status.text.value;
	}
	*/
	var bg_ctl_str = bg_ctl + " = '" + document.status.text.value + "';";
	eval( bg_ctl_str );

	value_ctl.focus();

	self.close();
}
/*
function OpenWindow(){
	var selColor = window.open("", "selColor","width=290,height=406,resizable=0,scrollbars=0");
	selColor.focus();
}
function SetColor(hexColorCode){
	
}
*/
</SCRIPT>
<SCRIPT LANGUAGE="JavaScript">
<!--
init();
ColorPicker( <?=stripslashes($ctl1)?>, <?=stripslashes($ctl2)?>, '<?=$ctl3?>' );
//-->
</SCRIPT>