function toggleField(hideObj,showObj) {
  hideObj.disabled=true;
  hideObj.style.display='none';
  showObj.disabled=false;
  showObj.style.display='inline';
  showObj.focus();
}
function checkPass(){                   //used in confirm matching password entries
  var pass1 = document.getElementById('pass1');
  var pass2 = document.getElementById('pass2');
  var goodColor = "#66cc66";
  var badColor = "#ff6666";
  if((pass1.value != '') && (pass1.value == pass2.value)){
    pass2.style.backgroundColor = goodColor;
    document.getElementById('submitpwd').removeAttribute("disabled");
  }else{
    pass2.style.backgroundColor = badColor;
    document.getElementById('submitpwd').setAttribute("disabled","disabled");
  }
}
function disableOnEmpty() {
    if (arguments.length >= 2)
    {
    var ti = document.getElementById(arguments[0]);
    if(ti.value.replace(/ /g,'') != '') {
        for (i = 1; i < arguments.length; i++) {
        document.getElementById(arguments[i]).removeAttribute("disabled");
        }
    }
    else {
        for (i = 1; i < arguments.length; i++) {
        document.getElementById(arguments[i]).setAttribute("disabled", "disabled");
        }
    }
    }
}
function checkPsk() {
	if(psk1.value.length > 0 && psk1.value.length < 8) {
		psk1.style.background='#ff6666';
	} else {
		psk1.style.background='#66cc66';
	}
}
function checkPskMatch(){                   //used in confirm matching psk entries
  var psk1 = document.getElementById('psk1');
  var psk2 = document.getElementById('psk2');
  var goodColor = "#66cc66";
  var badColor = "#ff6666";
  if((psk1.value != '') && (psk1.value == psk2.value)){
    psk2.style.backgroundColor = goodColor;
    document.getElementById('submitpsk').removeAttribute("disabled");
  }else{
    psk2.style.backgroundColor = badColor;
    document.getElementById('submitpsk').setAttribute("disabled","disabled");
  }
}
function checkFrequency(){
  // Set the colours
  var goodColor = "#66cc66";
  var badColor = "#ff6666";
  // Get the objects from the config page
  var freqTRX = document.getElementById('confFREQ');
  var freqRX = document.getElementById('confFREQrx');
  var freqTX = document.getElementById('confFREQtx');
  var freqPOCSAG = document.getElementById('pocsagFrequency');
  if(freqTRX){
    confFREQ.style.backgroundColor = badColor;		// Set to bad colour first, then check
    var intFreqTRX = parseFloat(freqTRX.value);		// Swap to float
    // TRX Good
    if (144 <= intFreqTRX && intFreqTRX <= 148)   { confFREQ.style.backgroundColor = goodColor; }
    if (220 <= intFreqTRX && intFreqTRX <= 225)   { confFREQ.style.backgroundColor = goodColor; }
    if (420 <= intFreqTRX && intFreqTRX <= 450)   { confFREQ.style.backgroundColor = goodColor; }
    if (842 <= intFreqTRX && intFreqTRX <= 950)   { confFREQ.style.backgroundColor = goodColor; }
    // TRX Bad
    if (145.8 <= intFreqTRX && intFreqTRX <= 146) { confFREQ.style.backgroundColor = badColor; }
    if (435 <= intFreqTRX && intFreqTRX <= 438)   { confFREQ.style.backgroundColor = badColor; }
  }
  if(freqRX){
    confFREQrx.style.backgroundColor = badColor;	// Set to bad colour first, then check
    var intFreqRX = parseFloat(freqRX.value);		// Swap to float
    // RX Good
    if (144 <= intFreqRX && intFreqRX <= 148)   { confFREQrx.style.backgroundColor = goodColor; }
    if (220 <= intFreqRX && intFreqRX <= 225)   { confFREQrx.style.backgroundColor = goodColor; }
    if (420 <= intFreqRX && intFreqRX <= 450)   { confFREQrx.style.backgroundColor = goodColor; }
    if (842 <= intFreqRX && intFreqRX <= 950)   { confFREQrx.style.backgroundColor = goodColor; }
    // RX Bad
    if (145.8 <= intFreqRX && intFreqRX <= 146) { confFREQrx.style.backgroundColor = badColor; }
    if (435 <= intFreqRX && intFreqRX <= 438)   { confFREQrx.style.backgroundColor = badColor; }
  }
  if(freqTX){
    confFREQtx.style.backgroundColor = badColor;	// Set to bad colour first, then check
    var intFreqTX = parseFloat(freqTX.value);		// Swap to float
    // TX Good
    if (144 <= intFreqTX && intFreqTX <= 148)   { confFREQtx.style.backgroundColor = goodColor; }
    if (220 <= intFreqTX && intFreqTX <= 225)   { confFREQtx.style.backgroundColor = goodColor; }
    if (420 <= intFreqTX && intFreqTX <= 450)   { confFREQtx.style.backgroundColor = goodColor; }
    if (842 <= intFreqTX && intFreqTX <= 950)   { confFREQtx.style.backgroundColor = goodColor; }
    // TX Bad
    if (145.8 <= intFreqTX && intFreqTX <= 146) { confFREQtx.style.backgroundColor = badColor; }
    if (435 <= intFreqTX && intFreqTX <= 438)   { confFREQtx.style.backgroundColor = badColor; }
  }
  if(freqPOCSAG){
    pocsagFrequency.style.backgroundColor = badColor;		// Set to bad colour first, then check
    var intFreqPOCSAG = parseFloat(freqPOCSAG.value);		// Swap to float
    // TX Good
    if (144 <= intFreqPOCSAG && intFreqPOCSAG <= 148)   { pocsagFrequency.style.backgroundColor = goodColor; }
    if (220 <= intFreqPOCSAG && intFreqPOCSAG <= 225)   { pocsagFrequency.style.backgroundColor = goodColor; }
    if (420 <= intFreqPOCSAG && intFreqPOCSAG <= 450)   { pocsagFrequency.style.backgroundColor = goodColor; }
    if (842 <= intFreqPOCSAG && intFreqPOCSAG <= 950)   { pocsagFrequency.style.backgroundColor = goodColor; }
    // TX Bad
    if (145.8 <= intFreqPOCSAG && intFreqPOCSAG <= 146) { pocsagFrequency.style.backgroundColor = badColor; }
    if (435 <= intFreqPOCSAG && intFreqPOCSAG <= 438)   { pocsagFrequency.style.backgroundColor = badColor; }
  }
}
function setMmdvmPort(modem) {
    port = document.getElementById('confPort');
    switch(modem) {
        case 'dvmpis':
        case 'dvmpid':
        case 'zumspotgpio':
        case 'zumspotdualgpio':
        case 'zumspotduplexgpio':
        case 'zumradiopigpio':
        case 'stm32dvm':
        case 'stm32dvmv3+':
        case 'stm32dvmmtr2kopi':
        case 'f4mgpio':
        case 'mmdvmhshat':
        case 'lshshatgpio':
        case 'mmdvmhsdualbandgpio':
        case 'sbhsdualbandgpio':
        case 'mmdvmhsdualhatgpio':
        case 'lshsdualhatgpio':
        case 'mmdvmrpthat':
        case 'mmdvmmdohat':
        case 'mmdvmvyehat':
        case 'mmdvmvyehatdual':
        case 'nanodv':
        case 'dvmpicast':
            port.value = "/dev/ttyAMA0";
            break;

        case 'dvmuadu':
        case 'dvmbss':
        case 'dvmbsd':
        case 'dvmuagmsku':
        case 'stm32usb':
        case 'stm32usbv3+':
        case 'f4mf7m':
            port.value = "/dev/ttyUSB0";
            break;

        case 'dvmuada':
        case 'dvmuagmska':
        case 'dvrptr1':
        case 'dvrptr2':
        case 'dvrptr3':
        case 'zumspotlibre':
        case 'zumspotusb':
        case 'lsusb':
        case 'zumradiopiusb':
        case 'zum':
        case 'mmdvmhsdualhatusb':
        case 'nanodvusb':
        case 'opengd77':
            port.value = "/dev/ttyACM0";
            break;

        case 'mmdvmhshatambe':
            port.value = "/dev/ttySC0";
            break;

        case 'dvmpicasths':
        case 'dvmpicasthd':
            port.value = "/dev/ttyS2";
            break;

        default:
            port.value = "/dev/ttyAMA0";
            break;
    }
}
function toggleDMRCheckbox(event) {
  switch(document.getElementById('aria-toggle-dmr').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-dmr').setAttribute('aria-checked', "false");
      //document.getElementById('toggle-dmr').click();
      break;
    case "false":
      document.getElementById('aria-toggle-dmr').setAttribute('aria-checked', "true");
      //document.getElementById('toggle-dmr').click();
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-dmr').click(); }
}
function toggleDSTARCheckbox(event) {
  switch(document.getElementById('aria-toggle-dstar').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-dstar').setAttribute('aria-checked', "false");
      //document.getElementById('toggle-dstar').click();
      break;
    case "false":
      document.getElementById('aria-toggle-dstar').setAttribute('aria-checked', "true");
      //document.getElementById('toggle-dstar').click();
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-dstar').click(); }
}
function toggleYSFCheckbox(event) {
  switch(document.getElementById('aria-toggle-ysf').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-ysf').setAttribute('aria-checked', "false");
      //document.getElementById('toggle-ysf').click();
      break;
    case "false":
      document.getElementById('aria-toggle-ysf').setAttribute('aria-checked', "true");
      //document.getElementById('toggle-ysf').click();
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-ysf').click(); }
}
function toggleP25Checkbox(event) {
  switch(document.getElementById('aria-toggle-p25').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-p25').setAttribute('aria-checked', "false");
      //document.getElementById('toggle-p25').click();
      break;
    case "false":
      document.getElementById('aria-toggle-p25').setAttribute('aria-checked', "true");
      //document.getElementById('toggle-p25').click();
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-p25').click(); }
}
function toggleNXDNCheckbox(event) {
  switch(document.getElementById('aria-toggle-nxdn').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-nxdn').setAttribute('aria-checked', "false");
      //document.getElementById('toggle-nxdn').click();
      break;
    case "false":
      document.getElementById('aria-toggle-nxdn').setAttribute('aria-checked', "true");
      //document.getElementById('toggle-nxdn').click();
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-nxdn').click(); }
}
function toggleYSF2DMRCheckbox(event) {
  switch(document.getElementById('aria-toggle-ysf2dmr').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-ysf2dmr').setAttribute('aria-checked', "false");
      //document.getElementById('toggle-ysf2dmr').click();
      break;
    case "false":
      document.getElementById('aria-toggle-ysf2dmr').setAttribute('aria-checked', "true");
      //document.getElementById('toggle-ysf2dmr').click();
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-ysf2dmr').click(); }
}
function toggleYSF2NXDNCheckbox(event) {
  switch(document.getElementById('aria-toggle-ysf2nxdn').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-ysf2nxdn').setAttribute('aria-checked', "false");
      //document.getElementById('toggle-ysf2nxdn').click();
      break;
    case "false":
      document.getElementById('aria-toggle-ysf2nxdn').setAttribute('aria-checked', "true");
      //document.getElementById('toggle-ysf2nxdn').click();
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-ysf2nxdn').click(); }
}
function toggleYSF2P25Checkbox(event) {
  switch(document.getElementById('aria-toggle-ysf2p25').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-ysf2p25').setAttribute('aria-checked', "false");
      //document.getElementById('toggle-ysf2p25').click();
      break;
    case "false":
      document.getElementById('aria-toggle-ysf2p25').setAttribute('aria-checked', "true");
      //document.getElementById('toggle-ysf2p25').click();
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-ysf2p25').click(); }
}
function toggleDMR2YSFCheckbox(event) {
  switch(document.getElementById('aria-toggle-dmr2ysf').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-dmr2ysf').setAttribute('aria-checked', "false");
      //document.getElementById('toggle-dmr2ysf').click();
      break;
    case "false":
      document.getElementById('aria-toggle-dmr2ysf').setAttribute('aria-checked', "true");
      //document.getElementById('toggle-dmr2ysf').click();
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-dmr2ysf').click(); }
}
function toggleDMR2NXDNCheckbox(event) {
  switch(document.getElementById('aria-toggle-dmr2nxdn').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-dmr2nxdn').setAttribute('aria-checked', "false");
      //document.getElementById('toggle-dmr2nxdn').click();
      break;
    case "false":
      document.getElementById('aria-toggle-dmr2nxdn').setAttribute('aria-checked', "true");
      //document.getElementById('toggle-dmr2nxdn').click();
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-dmr2nxdn').click(); }
}
function togglePOCSAGCheckbox(event) {
  switch(document.getElementById('aria-toggle-pocsag').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-pocsag').setAttribute('aria-checked', "false");
      //document.getElementById('toggle-pocsag').click();
      break;
    case "false":
      document.getElementById('aria-toggle-pocsag').setAttribute('aria-checked', "true");
      //document.getElementById('toggle-pocsag').click();
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-pocsag').click(); }
}
function toggleDmrGatewayNet1EnCheckbox(event) {
  switch(document.getElementById('aria-toggle-dmrGatewayNet1En').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-dmrGatewayNet1En').setAttribute('aria-checked', "false");
      break;
    case "false":
      document.getElementById('aria-toggle-dmrGatewayNet1En').setAttribute('aria-checked', "true");
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-dmrGatewayNet1En').click(); }
}
function toggleDmrGatewayNet2EnCheckbox(event) {
  switch(document.getElementById('aria-toggle-dmrGatewayNet2En').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-dmrGatewayNet2En').setAttribute('aria-checked', "false");
      break;
    case "false":
      document.getElementById('aria-toggle-dmrGatewayNet2En').setAttribute('aria-checked', "true");
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-dmrGatewayNet2En').click(); }
}
function toggleDmrGatewayXlxEnCheckbox(event) {
  switch(document.getElementById('aria-toggle-dmrGatewayXlxEn').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-dmrGatewayXlxEn').setAttribute('aria-checked', "false");
      break;
    case "false":
      document.getElementById('aria-toggle-dmrGatewayXlxEn').setAttribute('aria-checked', "true");
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-dmrGatewayXlxEn').click(); }
}
function toggleDmrEmbeddedLCOnly(event) {
  switch(document.getElementById('aria-toggle-dmrEmbeddedLCOnly').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-dmrEmbeddedLCOnly').setAttribute('aria-checked', "false");
      break;
    case "false":
      document.getElementById('aria-toggle-dmrEmbeddedLCOnly').setAttribute('aria-checked', "true");
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-dmrEmbeddedLCOnly').click(); }
}
function toggleDmrDumpTAData(event) {
  switch(document.getElementById('aria-toggle-dmrDumpTAData').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-dmrDumpTAData').setAttribute('aria-checked', "false");
      break;
    case "false":
      document.getElementById('aria-toggle-dmrDumpTAData').setAttribute('aria-checked', "true");
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-dmrDumpTAData').click(); }
}
function toggleHostFilesYSFUpper(event) {
  switch(document.getElementById('aria-toggle-confHostFilesYSFUpper').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-confHostFilesYSFUpper').setAttribute('aria-checked', "false");
      break;
    case "false":
      document.getElementById('aria-toggle-confHostFilesYSFUpper').setAttribute('aria-checked', "true");
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-confHostFilesYSFUpper').click(); }
}
function toggleWiresXCommandPassthrough(event) {
  switch(document.getElementById('aria-toggle-confWiresXCommandPassthrough').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-confWiresXCommandPassthrough').setAttribute('aria-checked', "false");
      break;
    case "false":
      document.getElementById('aria-toggle-confWiresXCommandPassthrough').setAttribute('aria-checked', "true");
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-confWiresXCommandPassthrough').click(); }
}
function toggleDstarTimeAnnounce(event) {
  switch(document.getElementById('aria-toggle-timeAnnounce').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-timeAnnounce').setAttribute('aria-checked', "false");
      break;
    case "false":
      document.getElementById('aria-toggle-timeAnnounce').setAttribute('aria-checked', "true");
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-timeAnnounce').click(); }
}
function toggleDstarDplusHostfiles(event) {
  switch(document.getElementById('aria-toggle-dplusHostFiles').getAttribute('aria-checked')) {
    case "true":
      document.getElementById('aria-toggle-dplusHostFiles').setAttribute('aria-checked', "false");
      break;
    case "false":
      document.getElementById('aria-toggle-dplusHostFiles').setAttribute('aria-checked', "true");
      break;
  }
  if(event.keyCode == '32') { document.getElementById('aria-toggle-dplusHostFiles').click(); }
}
function toggleGpsdCheckbox(event) {
  var gpsdCheckbox = document.getElementById('aria-toggle-GPSD');

  switch (gpsdCheckbox.getAttribute('aria-checked')) {
    case "true":
      gpsdCheckbox.setAttribute('aria-checked', "false");
      break;
    case "false":
      gpsdCheckbox.setAttribute('aria-checked', "true");
      break;
  }

  if (event.keyCode == '32') {
    gpsdCheckbox.click();
  }
}
function toggleOLEDScreenSaver(event) {
	switch (document.getElementById('aria-toggle-oledScreenSaver').getAttribute('aria-checked')) {
		case 'true':
			document.getElementById('aria-toggle-oledScreenSaver').setAttribute('aria-checked', 'false');
			//document.getElementById('toggle-oledScreenSaver').click();
			break;
		case 'false':
			document.getElementById('aria-toggle-oledScreenSaver').setAttribute('aria-checked', 'true');
			//document.getElementById('toggle-oledScreenSaver').click();
			break;
	}
	if (event.keyCode == '32') {
		document.getElementById('aria-toggle-oledScreenSaver').click()
	}
}
function toggleOLEDScroll(event) {
	switch (document.getElementById('aria-toggle-oledScroll').getAttribute('aria-checked')) {
		case 'true':
			document.getElementById('aria-toggle-oledScroll').setAttribute('aria-checked', 'false');
			//document.getElementById('toggle-oledScroll').click();
			break;
		case 'false':
			document.getElementById('aria-toggle-oledScroll').setAttribute('aria-checked', 'true');
			//document.getElementById('toggle-oledScroll').click();
			break;
	}
	if (event.keyCode == '32') {
		document.getElementById('aria-toggle-oledScroll').click()
	}
}
function toggleOLEDRotate(event) {
	switch (document.getElementById('aria-toggle-oledRotate').getAttribute('aria-checked')) {
		case 'true':
			document.getElementById('aria-toggle-oledRotate').setAttribute('aria-checked', 'false');
			//document.getElementById('toggle-oledRotate').click();
			break;
		case 'false':
			document.getElementById('aria-toggle-oledRotate').setAttribute('aria-checked', 'true');
			//document.getElementById('toggle-oledRotate').click();
			break;
	}
	if (event.keyCode == '32') {
		document.getElementById('aria-toggle-oledRotate').click()
	}
}
function toggleOLEDInvert(event) {
	switch (document.getElementById('aria-toggle-oledInvert').getAttribute('aria-checked')) {
		case 'true':
			document.getElementById('aria-toggle-oledInvert').setAttribute('aria-checked', 'false');
			//document.getElementById('toggle-oledInvert').click();
			break;
		case 'false':
			document.getElementById('aria-toggle-oledInvert').setAttribute('aria-checked', 'true');
			//document.getElementById('toggle-oledInvert').click();
			break;
	}
	if (event.keyCode == '32') {
		document.getElementById('aria-toggle-oledInvert').click()
	}
}

function _getDatetime( format = '24' ) {
  d1  = new Date();
  fmt = new DateFormatter();

  if ( '24' === format ) {
    format = 'H:i:s, M j';
  } else {
    format = 'h:i:s A, M j';
  }
  return fmt.formatDate(d1, format);
}

/*!
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2020
 * @version 1.3.6
 *
 * Date formatter utility library that allows formatting date/time variables or Date objects using PHP DateTime format.
 * This library is a standalone javascript library and does not depend on other libraries or plugins like jQuery. The
 * library also adds support for Universal Module Definition (UMD).
 *
 * @see http://php.net/manual/en/function.date.php
 *
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
 !function(t,e){"function"==typeof define&&define.amd?define([],e):"object"==typeof module&&module.exports?module.exports=e():t.DateFormatter=e()}("undefined"!=typeof self?self:this,function(){var t,e;return e={DAY:864e5,HOUR:3600,defaults:{dateSettings:{days:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],daysShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],months:["January","February","March","April","May","June","July","August","September","October","November","December"],monthsShort:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],meridiem:["AM","PM"],ordinal:function(t){var e=t%10,n={1:"st",2:"nd",3:"rd"};return 1!==Math.floor(t%100/10)&&n[e]?n[e]:"th"}},separators:/[ \-+\/.:@]/g,validParts:/[dDjlNSwzWFmMntLoYyaABgGhHisueTIOPZcrU]/g,intParts:/[djwNzmnyYhHgGis]/g,tzParts:/\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,tzClip:/[^-+\dA-Z]/g},getInt:function(t,e){return parseInt(t,e?e:10)},compare:function(t,e){return"string"==typeof t&&"string"==typeof e&&t.toLowerCase()===e.toLowerCase()},lpad:function(t,n,r){var a=t.toString();return r=r||"0",a.length<n?e.lpad(r+a,n):a},merge:function(t){var n,r;for(t=t||{},n=1;n<arguments.length;n++)if(r=arguments[n])for(var a in r)r.hasOwnProperty(a)&&("object"==typeof r[a]?e.merge(t[a],r[a]):t[a]=r[a]);return t},getIndex:function(t,e){for(var n=0;n<e.length;n++)if(e[n].toLowerCase()===t.toLowerCase())return n;return-1}},t=function(t){var n=this,r=e.merge(e.defaults,t);n.dateSettings=r.dateSettings,n.separators=r.separators,n.validParts=r.validParts,n.intParts=r.intParts,n.tzParts=r.tzParts,n.tzClip=r.tzClip},t.prototype={constructor:t,getMonth:function(t){var n,r=this;return n=e.getIndex(t,r.dateSettings.monthsShort)+1,0===n&&(n=e.getIndex(t,r.dateSettings.months)+1),n},parseDate:function(t,n){var r,a,u,i,o,s,c,f,l,d,g=this,h=!1,m=!1,p=g.dateSettings,y={date:null,year:null,month:null,day:null,hour:0,min:0,sec:0};if(!t)return null;if(t instanceof Date)return t;if("U"===n)return u=e.getInt(t),u?new Date(1e3*u):t;switch(typeof t){case"number":return new Date(t);case"string":break;default:return null}if(r=n.match(g.validParts),!r||0===r.length)throw new Error("Invalid date format definition.");for(u=r.length-1;u>=0;u--)"S"===r[u]&&r.splice(u,1);for(a=t.replace(g.separators,"\x00").split("\x00"),u=0;u<a.length;u++)switch(i=a[u],o=e.getInt(i),r[u]){case"y":case"Y":if(!o)return null;l=i.length,y.year=2===l?e.getInt((70>o?"20":"19")+i):o,h=!0;break;case"m":case"n":case"M":case"F":if(isNaN(o)){if(s=g.getMonth(i),!(s>0))return null;y.month=s}else{if(!(o>=1&&12>=o))return null;y.month=o}h=!0;break;case"d":case"j":if(!(o>=1&&31>=o))return null;y.day=o,h=!0;break;case"g":case"h":if(c=r.indexOf("a")>-1?r.indexOf("a"):r.indexOf("A")>-1?r.indexOf("A"):-1,d=a[c],-1!==c)f=e.compare(d,p.meridiem[0])?0:e.compare(d,p.meridiem[1])?12:-1,o>=1&&12>=o&&-1!==f?y.hour=o%12===0?f:o+f:o>=0&&23>=o&&(y.hour=o);else{if(!(o>=0&&23>=o))return null;y.hour=o}m=!0;break;case"G":case"H":if(!(o>=0&&23>=o))return null;y.hour=o,m=!0;break;case"i":if(!(o>=0&&59>=o))return null;y.min=o,m=!0;break;case"s":if(!(o>=0&&59>=o))return null;y.sec=o,m=!0}if(h===!0){var D=y.year||0,v=y.month?y.month-1:0,S=y.day||1;y.date=new Date(D,v,S,y.hour,y.min,y.sec,0)}else{if(m!==!0)return null;y.date=new Date(0,0,0,y.hour,y.min,y.sec,0)}return y.date},guessDate:function(t,n){if("string"!=typeof t)return t;var r,a,u,i,o,s,c=this,f=t.replace(c.separators,"\x00").split("\x00"),l=/^[djmn]/g,d=n.match(c.validParts),g=new Date,h=0;if(!l.test(d[0]))return t;for(u=0;u<f.length;u++){if(h=2,o=f[u],s=e.getInt(o.substr(0,2)),isNaN(s))return null;switch(u){case 0:"m"===d[0]||"n"===d[0]?g.setMonth(s-1):g.setDate(s);break;case 1:"m"===d[0]||"n"===d[0]?g.setDate(s):g.setMonth(s-1);break;case 2:if(a=g.getFullYear(),r=o.length,h=4>r?r:4,a=e.getInt(4>r?a.toString().substr(0,4-r)+o:o.substr(0,4)),!a)return null;g.setFullYear(a);break;case 3:g.setHours(s);break;case 4:g.setMinutes(s);break;case 5:g.setSeconds(s)}i=o.substr(h),i.length>0&&f.splice(u+1,0,i)}return g},parseFormat:function(t,n){var r,a=this,u=a.dateSettings,i=/\\?(.?)/gi,o=function(t,e){return r[t]?r[t]():e};return r={d:function(){return e.lpad(r.j(),2)},D:function(){return u.daysShort[r.w()]},j:function(){return n.getDate()},l:function(){return u.days[r.w()]},N:function(){return r.w()||7},w:function(){return n.getDay()},z:function(){var t=new Date(r.Y(),r.n()-1,r.j()),n=new Date(r.Y(),0,1);return Math.round((t-n)/e.DAY)},W:function(){var t=new Date(r.Y(),r.n()-1,r.j()-r.N()+3),n=new Date(t.getFullYear(),0,4);return e.lpad(1+Math.round((t-n)/e.DAY/7),2)},F:function(){return u.months[n.getMonth()]},m:function(){return e.lpad(r.n(),2)},M:function(){return u.monthsShort[n.getMonth()]},n:function(){return n.getMonth()+1},t:function(){return new Date(r.Y(),r.n(),0).getDate()},L:function(){var t=r.Y();return t%4===0&&t%100!==0||t%400===0?1:0},o:function(){var t=r.n(),e=r.W(),n=r.Y();return n+(12===t&&9>e?1:1===t&&e>9?-1:0)},Y:function(){return n.getFullYear()},y:function(){return r.Y().toString().slice(-2)},a:function(){return r.A().toLowerCase()},A:function(){var t=r.G()<12?0:1;return u.meridiem[t]},B:function(){var t=n.getUTCHours()*e.HOUR,r=60*n.getUTCMinutes(),a=n.getUTCSeconds();return e.lpad(Math.floor((t+r+a+e.HOUR)/86.4)%1e3,3)},g:function(){return r.G()%12||12},G:function(){return n.getHours()},h:function(){return e.lpad(r.g(),2)},H:function(){return e.lpad(r.G(),2)},i:function(){return e.lpad(n.getMinutes(),2)},s:function(){return e.lpad(n.getSeconds(),2)},u:function(){return e.lpad(1e3*n.getMilliseconds(),6)},e:function(){var t=/\((.*)\)/.exec(String(n))[1];return t||"Coordinated Universal Time"},I:function(){var t=new Date(r.Y(),0),e=Date.UTC(r.Y(),0),n=new Date(r.Y(),6),a=Date.UTC(r.Y(),6);return t-e!==n-a?1:0},O:function(){var t=n.getTimezoneOffset(),r=Math.abs(t);return(t>0?"-":"+")+e.lpad(100*Math.floor(r/60)+r%60,4)},P:function(){var t=r.O();return t.substr(0,3)+":"+t.substr(3,2)},T:function(){var t=(String(n).match(a.tzParts)||[""]).pop().replace(a.tzClip,"");return t||"UTC"},Z:function(){return 60*-n.getTimezoneOffset()},c:function(){return"Y-m-d\\TH:i:sP".replace(i,o)},r:function(){return"D, d M Y H:i:s O".replace(i,o)},U:function(){return n.getTime()/1e3||0}},o(t,t)},formatDate:function(t,n){var r,a,u,i,o,s=this,c="",f="\\";if("string"==typeof t&&(t=s.parseDate(t,n),!t))return null;if(t instanceof Date){for(u=n.length,r=0;u>r;r++)o=n.charAt(r),"S"!==o&&o!==f&&(r>0&&n.charAt(r-1)===f?c+=o:(i=s.parseFormat(o,t),r!==u-1&&s.intParts.test(o)&&"S"===n.charAt(r+1)&&(a=e.getInt(i)||0,i+=s.dateSettings.ordinal(a)),c+=i));return c}return""}},t});
