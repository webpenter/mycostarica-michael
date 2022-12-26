function b2a (a) {
  var c, d, e, f, g, h, i, j, o, b = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", k = 0, l = 0, m = "", n = [];
  if (!a) return a;
  do c = a.charCodeAt(k++), d = a.charCodeAt(k++), e = a.charCodeAt(k++), j = c << 16 | d << 8 | e,
  f = 63 & j >> 18, g = 63 & j >> 12, h = 63 & j >> 6, i = 63 & j, n[l++] = b.charAt(f) + b.charAt(g) + b.charAt(h) + b.charAt(i); while (k < a.length);
  return m = n.join(""), o = a.length % 3, (o ? m.slice(0, o - 3) :m) + "===".slice(o || 3);
}

function a2b (a) {
  var b, c, d, e = {}, f = 0, g = 0, h = "", i = String.fromCharCode, j = a.length;
  for (b = 0; 64 > b; b++) e["ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".charAt(b)] = b;
  for (c = 0; j > c; c++) for (b = e[a.charAt(c)], f = (f << 6) + b, g += 6; g >= 8; ) ((d = 255 & f >>> (g -= 8)) || j - 2 > c) && (h += i(d));
  return h;
}

b64e = function (str) {
  return btoa (encodeURIComponent (str).replace (/%([0-9A-F]{2})/g,
    function toSolidBytes (match, p1) {
      return String.fromCharCode ('0x' + p1);
  }));
}

b64d = function (str) {
  return decodeURIComponent (atob (str).split ('').map (function(c) {
    return '%' + ('00' + c.charCodeAt (0).toString (16)).slice (-2);
  }).join (''));
}
;// Semicolon in the case it is missing in the code above
// THIS FILE IS GENERATED - DO NOT EDIT!
/*!mobile-detect v1.4.5 2021-03-13*/
/*global module:false, define:false*/
/*jshint latedef:false*/
/*!@license Copyright 2013, Heinrich Goebl, License: MIT, see https://github.com/hgoebl/mobile-detect.js*/
(function (define, undefined) {
define(function () {
    'use strict';

    var impl = {};

    impl.mobileDetectRules = {
    "phones": {
        "iPhone": "\\biPhone\\b|\\biPod\\b",
        "BlackBerry": "BlackBerry|\\bBB10\\b|rim[0-9]+|\\b(BBA100|BBB100|BBD100|BBE100|BBF100|STH100)\\b-[0-9]+",
        "Pixel": "; \\bPixel\\b",
        "HTC": "HTC|HTC.*(Sensation|Evo|Vision|Explorer|6800|8100|8900|A7272|S510e|C110e|Legend|Desire|T8282)|APX515CKT|Qtek9090|APA9292KT|HD_mini|Sensation.*Z710e|PG86100|Z715e|Desire.*(A8181|HD)|ADR6200|ADR6400L|ADR6425|001HT|Inspire 4G|Android.*\\bEVO\\b|T-Mobile G1|Z520m|Android [0-9.]+; Pixel",
        "Nexus": "Nexus One|Nexus S|Galaxy.*Nexus|Android.*Nexus.*Mobile|Nexus 4|Nexus 5|Nexus 5X|Nexus 6",
        "Dell": "Dell[;]? (Streak|Aero|Venue|Venue Pro|Flash|Smoke|Mini 3iX)|XCD28|XCD35|\\b001DL\\b|\\b101DL\\b|\\bGS01\\b",
        "Motorola": "Motorola|DROIDX|DROID BIONIC|\\bDroid\\b.*Build|Android.*Xoom|HRI39|MOT-|A1260|A1680|A555|A853|A855|A953|A955|A956|Motorola.*ELECTRIFY|Motorola.*i1|i867|i940|MB200|MB300|MB501|MB502|MB508|MB511|MB520|MB525|MB526|MB611|MB612|MB632|MB810|MB855|MB860|MB861|MB865|MB870|ME501|ME502|ME511|ME525|ME600|ME632|ME722|ME811|ME860|ME863|ME865|MT620|MT710|MT716|MT720|MT810|MT870|MT917|Motorola.*TITANIUM|WX435|WX445|XT300|XT301|XT311|XT316|XT317|XT319|XT320|XT390|XT502|XT530|XT531|XT532|XT535|XT603|XT610|XT611|XT615|XT681|XT701|XT702|XT711|XT720|XT800|XT806|XT860|XT862|XT875|XT882|XT883|XT894|XT901|XT907|XT909|XT910|XT912|XT928|XT926|XT915|XT919|XT925|XT1021|\\bMoto E\\b|XT1068|XT1092|XT1052",
        "Samsung": "\\bSamsung\\b|SM-G950F|SM-G955F|SM-G9250|GT-19300|SGH-I337|BGT-S5230|GT-B2100|GT-B2700|GT-B2710|GT-B3210|GT-B3310|GT-B3410|GT-B3730|GT-B3740|GT-B5510|GT-B5512|GT-B5722|GT-B6520|GT-B7300|GT-B7320|GT-B7330|GT-B7350|GT-B7510|GT-B7722|GT-B7800|GT-C3010|GT-C3011|GT-C3060|GT-C3200|GT-C3212|GT-C3212I|GT-C3262|GT-C3222|GT-C3300|GT-C3300K|GT-C3303|GT-C3303K|GT-C3310|GT-C3322|GT-C3330|GT-C3350|GT-C3500|GT-C3510|GT-C3530|GT-C3630|GT-C3780|GT-C5010|GT-C5212|GT-C6620|GT-C6625|GT-C6712|GT-E1050|GT-E1070|GT-E1075|GT-E1080|GT-E1081|GT-E1085|GT-E1087|GT-E1100|GT-E1107|GT-E1110|GT-E1120|GT-E1125|GT-E1130|GT-E1160|GT-E1170|GT-E1175|GT-E1180|GT-E1182|GT-E1200|GT-E1210|GT-E1225|GT-E1230|GT-E1390|GT-E2100|GT-E2120|GT-E2121|GT-E2152|GT-E2220|GT-E2222|GT-E2230|GT-E2232|GT-E2250|GT-E2370|GT-E2550|GT-E2652|GT-E3210|GT-E3213|GT-I5500|GT-I5503|GT-I5700|GT-I5800|GT-I5801|GT-I6410|GT-I6420|GT-I7110|GT-I7410|GT-I7500|GT-I8000|GT-I8150|GT-I8160|GT-I8190|GT-I8320|GT-I8330|GT-I8350|GT-I8530|GT-I8700|GT-I8703|GT-I8910|GT-I9000|GT-I9001|GT-I9003|GT-I9010|GT-I9020|GT-I9023|GT-I9070|GT-I9082|GT-I9100|GT-I9103|GT-I9220|GT-I9250|GT-I9300|GT-I9305|GT-I9500|GT-I9505|GT-M3510|GT-M5650|GT-M7500|GT-M7600|GT-M7603|GT-M8800|GT-M8910|GT-N7000|GT-S3110|GT-S3310|GT-S3350|GT-S3353|GT-S3370|GT-S3650|GT-S3653|GT-S3770|GT-S3850|GT-S5210|GT-S5220|GT-S5229|GT-S5230|GT-S5233|GT-S5250|GT-S5253|GT-S5260|GT-S5263|GT-S5270|GT-S5300|GT-S5330|GT-S5350|GT-S5360|GT-S5363|GT-S5369|GT-S5380|GT-S5380D|GT-S5560|GT-S5570|GT-S5600|GT-S5603|GT-S5610|GT-S5620|GT-S5660|GT-S5670|GT-S5690|GT-S5750|GT-S5780|GT-S5830|GT-S5839|GT-S6102|GT-S6500|GT-S7070|GT-S7200|GT-S7220|GT-S7230|GT-S7233|GT-S7250|GT-S7500|GT-S7530|GT-S7550|GT-S7562|GT-S7710|GT-S8000|GT-S8003|GT-S8500|GT-S8530|GT-S8600|SCH-A310|SCH-A530|SCH-A570|SCH-A610|SCH-A630|SCH-A650|SCH-A790|SCH-A795|SCH-A850|SCH-A870|SCH-A890|SCH-A930|SCH-A950|SCH-A970|SCH-A990|SCH-I100|SCH-I110|SCH-I400|SCH-I405|SCH-I500|SCH-I510|SCH-I515|SCH-I600|SCH-I730|SCH-I760|SCH-I770|SCH-I830|SCH-I910|SCH-I920|SCH-I959|SCH-LC11|SCH-N150|SCH-N300|SCH-R100|SCH-R300|SCH-R351|SCH-R400|SCH-R410|SCH-T300|SCH-U310|SCH-U320|SCH-U350|SCH-U360|SCH-U365|SCH-U370|SCH-U380|SCH-U410|SCH-U430|SCH-U450|SCH-U460|SCH-U470|SCH-U490|SCH-U540|SCH-U550|SCH-U620|SCH-U640|SCH-U650|SCH-U660|SCH-U700|SCH-U740|SCH-U750|SCH-U810|SCH-U820|SCH-U900|SCH-U940|SCH-U960|SCS-26UC|SGH-A107|SGH-A117|SGH-A127|SGH-A137|SGH-A157|SGH-A167|SGH-A177|SGH-A187|SGH-A197|SGH-A227|SGH-A237|SGH-A257|SGH-A437|SGH-A517|SGH-A597|SGH-A637|SGH-A657|SGH-A667|SGH-A687|SGH-A697|SGH-A707|SGH-A717|SGH-A727|SGH-A737|SGH-A747|SGH-A767|SGH-A777|SGH-A797|SGH-A817|SGH-A827|SGH-A837|SGH-A847|SGH-A867|SGH-A877|SGH-A887|SGH-A897|SGH-A927|SGH-B100|SGH-B130|SGH-B200|SGH-B220|SGH-C100|SGH-C110|SGH-C120|SGH-C130|SGH-C140|SGH-C160|SGH-C170|SGH-C180|SGH-C200|SGH-C207|SGH-C210|SGH-C225|SGH-C230|SGH-C417|SGH-C450|SGH-D307|SGH-D347|SGH-D357|SGH-D407|SGH-D415|SGH-D780|SGH-D807|SGH-D980|SGH-E105|SGH-E200|SGH-E315|SGH-E316|SGH-E317|SGH-E335|SGH-E590|SGH-E635|SGH-E715|SGH-E890|SGH-F300|SGH-F480|SGH-I200|SGH-I300|SGH-I320|SGH-I550|SGH-I577|SGH-I600|SGH-I607|SGH-I617|SGH-I627|SGH-I637|SGH-I677|SGH-I700|SGH-I717|SGH-I727|SGH-i747M|SGH-I777|SGH-I780|SGH-I827|SGH-I847|SGH-I857|SGH-I896|SGH-I897|SGH-I900|SGH-I907|SGH-I917|SGH-I927|SGH-I937|SGH-I997|SGH-J150|SGH-J200|SGH-L170|SGH-L700|SGH-M110|SGH-M150|SGH-M200|SGH-N105|SGH-N500|SGH-N600|SGH-N620|SGH-N625|SGH-N700|SGH-N710|SGH-P107|SGH-P207|SGH-P300|SGH-P310|SGH-P520|SGH-P735|SGH-P777|SGH-Q105|SGH-R210|SGH-R220|SGH-R225|SGH-S105|SGH-S307|SGH-T109|SGH-T119|SGH-T139|SGH-T209|SGH-T219|SGH-T229|SGH-T239|SGH-T249|SGH-T259|SGH-T309|SGH-T319|SGH-T329|SGH-T339|SGH-T349|SGH-T359|SGH-T369|SGH-T379|SGH-T409|SGH-T429|SGH-T439|SGH-T459|SGH-T469|SGH-T479|SGH-T499|SGH-T509|SGH-T519|SGH-T539|SGH-T559|SGH-T589|SGH-T609|SGH-T619|SGH-T629|SGH-T639|SGH-T659|SGH-T669|SGH-T679|SGH-T709|SGH-T719|SGH-T729|SGH-T739|SGH-T746|SGH-T749|SGH-T759|SGH-T769|SGH-T809|SGH-T819|SGH-T839|SGH-T919|SGH-T929|SGH-T939|SGH-T959|SGH-T989|SGH-U100|SGH-U200|SGH-U800|SGH-V205|SGH-V206|SGH-X100|SGH-X105|SGH-X120|SGH-X140|SGH-X426|SGH-X427|SGH-X475|SGH-X495|SGH-X497|SGH-X507|SGH-X600|SGH-X610|SGH-X620|SGH-X630|SGH-X700|SGH-X820|SGH-X890|SGH-Z130|SGH-Z150|SGH-Z170|SGH-ZX10|SGH-ZX20|SHW-M110|SPH-A120|SPH-A400|SPH-A420|SPH-A460|SPH-A500|SPH-A560|SPH-A600|SPH-A620|SPH-A660|SPH-A700|SPH-A740|SPH-A760|SPH-A790|SPH-A800|SPH-A820|SPH-A840|SPH-A880|SPH-A900|SPH-A940|SPH-A960|SPH-D600|SPH-D700|SPH-D710|SPH-D720|SPH-I300|SPH-I325|SPH-I330|SPH-I350|SPH-I500|SPH-I600|SPH-I700|SPH-L700|SPH-M100|SPH-M220|SPH-M240|SPH-M300|SPH-M305|SPH-M320|SPH-M330|SPH-M350|SPH-M360|SPH-M370|SPH-M380|SPH-M510|SPH-M540|SPH-M550|SPH-M560|SPH-M570|SPH-M580|SPH-M610|SPH-M620|SPH-M630|SPH-M800|SPH-M810|SPH-M850|SPH-M900|SPH-M910|SPH-M920|SPH-M930|SPH-N100|SPH-N200|SPH-N240|SPH-N300|SPH-N400|SPH-Z400|SWC-E100|SCH-i909|GT-N7100|GT-N7105|SCH-I535|SM-N900A|SGH-I317|SGH-T999L|GT-S5360B|GT-I8262|GT-S6802|GT-S6312|GT-S6310|GT-S5312|GT-S5310|GT-I9105|GT-I8510|GT-S6790N|SM-G7105|SM-N9005|GT-S5301|GT-I9295|GT-I9195|SM-C101|GT-S7392|GT-S7560|GT-B7610|GT-I5510|GT-S7582|GT-S7530E|GT-I8750|SM-G9006V|SM-G9008V|SM-G9009D|SM-G900A|SM-G900D|SM-G900F|SM-G900H|SM-G900I|SM-G900J|SM-G900K|SM-G900L|SM-G900M|SM-G900P|SM-G900R4|SM-G900S|SM-G900T|SM-G900V|SM-G900W8|SHV-E160K|SCH-P709|SCH-P729|SM-T2558|GT-I9205|SM-G9350|SM-J120F|SM-G920F|SM-G920V|SM-G930F|SM-N910C|SM-A310F|GT-I9190|SM-J500FN|SM-G903F|SM-J330F|SM-G610F|SM-G981B|SM-G892A|SM-A530F",
        "LG": "\\bLG\\b;|LG[- ]?(C800|C900|E400|E610|E900|E-900|F160|F180K|F180L|F180S|730|855|L160|LS740|LS840|LS970|LU6200|MS690|MS695|MS770|MS840|MS870|MS910|P500|P700|P705|VM696|AS680|AS695|AX840|C729|E970|GS505|272|C395|E739BK|E960|L55C|L75C|LS696|LS860|P769BK|P350|P500|P509|P870|UN272|US730|VS840|VS950|LN272|LN510|LS670|LS855|LW690|MN270|MN510|P509|P769|P930|UN200|UN270|UN510|UN610|US670|US740|US760|UX265|UX840|VN271|VN530|VS660|VS700|VS740|VS750|VS910|VS920|VS930|VX9200|VX11000|AX840A|LW770|P506|P925|P999|E612|D955|D802|MS323|M257)|LM-G710",
        "Sony": "SonyST|SonyLT|SonyEricsson|SonyEricssonLT15iv|LT18i|E10i|LT28h|LT26w|SonyEricssonMT27i|C5303|C6902|C6903|C6906|C6943|D2533|SOV34|601SO|F8332",
        "Asus": "Asus.*Galaxy|PadFone.*Mobile",
        "Xiaomi": "^(?!.*\\bx11\\b).*xiaomi.*$|POCOPHONE F1|MI 8|Redmi Note 9S|Redmi Note 5A Prime|N2G47H|M2001J2G|M2001J2I|M1805E10A|M2004J11G|M1902F1G|M2002J9G|M2004J19G|M2003J6A1G",
        "NokiaLumia": "Lumia [0-9]{3,4}",
        "Micromax": "Micromax.*\\b(A210|A92|A88|A72|A111|A110Q|A115|A116|A110|A90S|A26|A51|A35|A54|A25|A27|A89|A68|A65|A57|A90)\\b",
        "Palm": "PalmSource|Palm",
        "Vertu": "Vertu|Vertu.*Ltd|Vertu.*Ascent|Vertu.*Ayxta|Vertu.*Constellation(F|Quest)?|Vertu.*Monika|Vertu.*Signature",
        "Pantech": "PANTECH|IM-A850S|IM-A840S|IM-A830L|IM-A830K|IM-A830S|IM-A820L|IM-A810K|IM-A810S|IM-A800S|IM-T100K|IM-A725L|IM-A780L|IM-A775C|IM-A770K|IM-A760S|IM-A750K|IM-A740S|IM-A730S|IM-A720L|IM-A710K|IM-A690L|IM-A690S|IM-A650S|IM-A630K|IM-A600S|VEGA PTL21|PT003|P8010|ADR910L|P6030|P6020|P9070|P4100|P9060|P5000|CDM8992|TXT8045|ADR8995|IS11PT|P2030|P6010|P8000|PT002|IS06|CDM8999|P9050|PT001|TXT8040|P2020|P9020|P2000|P7040|P7000|C790",
        "Fly": "IQ230|IQ444|IQ450|IQ440|IQ442|IQ441|IQ245|IQ256|IQ236|IQ255|IQ235|IQ245|IQ275|IQ240|IQ285|IQ280|IQ270|IQ260|IQ250",
        "Wiko": "KITE 4G|HIGHWAY|GETAWAY|STAIRWAY|DARKSIDE|DARKFULL|DARKNIGHT|DARKMOON|SLIDE|WAX 4G|RAINBOW|BLOOM|SUNSET|GOA(?!nna)|LENNY|BARRY|IGGY|OZZY|CINK FIVE|CINK PEAX|CINK PEAX 2|CINK SLIM|CINK SLIM 2|CINK +|CINK KING|CINK PEAX|CINK SLIM|SUBLIM",
        "iMobile": "i-mobile (IQ|i-STYLE|idea|ZAA|Hitz)",
        "SimValley": "\\b(SP-80|XT-930|SX-340|XT-930|SX-310|SP-360|SP60|SPT-800|SP-120|SPT-800|SP-140|SPX-5|SPX-8|SP-100|SPX-8|SPX-12)\\b",
        "Wolfgang": "AT-B24D|AT-AS50HD|AT-AS40W|AT-AS55HD|AT-AS45q2|AT-B26D|AT-AS50Q",
        "Alcatel": "Alcatel",
        "Nintendo": "Nintendo (3DS|Switch)",
        "Amoi": "Amoi",
        "INQ": "INQ",
        "OnePlus": "ONEPLUS",
        "GenericPhone": "Tapatalk|PDA;|SAGEM|\\bmmp\\b|pocket|\\bpsp\\b|symbian|Smartphone|smartfon|treo|up.browser|up.link|vodafone|\\bwap\\b|nokia|Series40|Series60|S60|SonyEricsson|N900|MAUI.*WAP.*Browser"
    },
    "tablets": {
        "iPad": "iPad|iPad.*Mobile",
        "NexusTablet": "Android.*Nexus[\\s]+(7|9|10)",
        "GoogleTablet": "Android.*Pixel C",
        "SamsungTablet": "SAMSUNG.*Tablet|Galaxy.*Tab|SC-01C|GT-P1000|GT-P1003|GT-P1010|GT-P3105|GT-P6210|GT-P6800|GT-P6810|GT-P7100|GT-P7300|GT-P7310|GT-P7500|GT-P7510|SCH-I800|SCH-I815|SCH-I905|SGH-I957|SGH-I987|SGH-T849|SGH-T859|SGH-T869|SPH-P100|GT-P3100|GT-P3108|GT-P3110|GT-P5100|GT-P5110|GT-P6200|GT-P7320|GT-P7511|GT-N8000|GT-P8510|SGH-I497|SPH-P500|SGH-T779|SCH-I705|SCH-I915|GT-N8013|GT-P3113|GT-P5113|GT-P8110|GT-N8010|GT-N8005|GT-N8020|GT-P1013|GT-P6201|GT-P7501|GT-N5100|GT-N5105|GT-N5110|SHV-E140K|SHV-E140L|SHV-E140S|SHV-E150S|SHV-E230K|SHV-E230L|SHV-E230S|SHW-M180K|SHW-M180L|SHW-M180S|SHW-M180W|SHW-M300W|SHW-M305W|SHW-M380K|SHW-M380S|SHW-M380W|SHW-M430W|SHW-M480K|SHW-M480S|SHW-M480W|SHW-M485W|SHW-M486W|SHW-M500W|GT-I9228|SCH-P739|SCH-I925|GT-I9200|GT-P5200|GT-P5210|GT-P5210X|SM-T311|SM-T310|SM-T310X|SM-T210|SM-T210R|SM-T211|SM-P600|SM-P601|SM-P605|SM-P900|SM-P901|SM-T217|SM-T217A|SM-T217S|SM-P6000|SM-T3100|SGH-I467|XE500|SM-T110|GT-P5220|GT-I9200X|GT-N5110X|GT-N5120|SM-P905|SM-T111|SM-T2105|SM-T315|SM-T320|SM-T320X|SM-T321|SM-T520|SM-T525|SM-T530NU|SM-T230NU|SM-T330NU|SM-T900|XE500T1C|SM-P605V|SM-P905V|SM-T337V|SM-T537V|SM-T707V|SM-T807V|SM-P600X|SM-P900X|SM-T210X|SM-T230|SM-T230X|SM-T325|GT-P7503|SM-T531|SM-T330|SM-T530|SM-T705|SM-T705C|SM-T535|SM-T331|SM-T800|SM-T700|SM-T537|SM-T807|SM-P907A|SM-T337A|SM-T537A|SM-T707A|SM-T807A|SM-T237|SM-T807P|SM-P607T|SM-T217T|SM-T337T|SM-T807T|SM-T116NQ|SM-T116BU|SM-P550|SM-T350|SM-T550|SM-T9000|SM-P9000|SM-T705Y|SM-T805|GT-P3113|SM-T710|SM-T810|SM-T815|SM-T360|SM-T533|SM-T113|SM-T335|SM-T715|SM-T560|SM-T670|SM-T677|SM-T377|SM-T567|SM-T357T|SM-T555|SM-T561|SM-T713|SM-T719|SM-T813|SM-T819|SM-T580|SM-T355Y?|SM-T280|SM-T817A|SM-T820|SM-W700|SM-P580|SM-T587|SM-P350|SM-P555M|SM-P355M|SM-T113NU|SM-T815Y|SM-T585|SM-T285|SM-T825|SM-W708|SM-T835|SM-T830|SM-T837V|SM-T720|SM-T510|SM-T387V|SM-P610|SM-T290|SM-T515|SM-T590|SM-T595|SM-T725|SM-T817P|SM-P585N0|SM-T395|SM-T295|SM-T865|SM-P610N|SM-P615|SM-T970|SM-T380|SM-T5950|SM-T905|SM-T231|SM-T500|SM-T860",
        "Kindle": "Kindle|Silk.*Accelerated|Android.*\\b(KFOT|KFTT|KFJWI|KFJWA|KFOTE|KFSOWI|KFTHWI|KFTHWA|KFAPWI|KFAPWA|WFJWAE|KFSAWA|KFSAWI|KFASWI|KFARWI|KFFOWI|KFGIWI|KFMEWI)\\b|Android.*Silk\/[0-9.]+ like Chrome\/[0-9.]+ (?!Mobile)",
        "SurfaceTablet": "Windows NT [0-9.]+; ARM;.*(Tablet|ARMBJS)",
        "HPTablet": "HP Slate (7|8|10)|HP ElitePad 900|hp-tablet|EliteBook.*Touch|HP 8|Slate 21|HP SlateBook 10",
        "AsusTablet": "^.*PadFone((?!Mobile).)*$|Transformer|TF101|TF101G|TF300T|TF300TG|TF300TL|TF700T|TF700KL|TF701T|TF810C|ME171|ME301T|ME302C|ME371MG|ME370T|ME372MG|ME172V|ME173X|ME400C|Slider SL101|\\bK00F\\b|\\bK00C\\b|\\bK00E\\b|\\bK00L\\b|TX201LA|ME176C|ME102A|\\bM80TA\\b|ME372CL|ME560CG|ME372CG|ME302KL| K010 | K011 | K017 | K01E |ME572C|ME103K|ME170C|ME171C|\\bME70C\\b|ME581C|ME581CL|ME8510C|ME181C|P01Y|PO1MA|P01Z|\\bP027\\b|\\bP024\\b|\\bP00C\\b",
        "BlackBerryTablet": "PlayBook|RIM Tablet",
        "HTCtablet": "HTC_Flyer_P512|HTC Flyer|HTC Jetstream|HTC-P715a|HTC EVO View 4G|PG41200|PG09410",
        "MotorolaTablet": "xoom|sholest|MZ615|MZ605|MZ505|MZ601|MZ602|MZ603|MZ604|MZ606|MZ607|MZ608|MZ609|MZ615|MZ616|MZ617",
        "NookTablet": "Android.*Nook|NookColor|nook browser|BNRV200|BNRV200A|BNTV250|BNTV250A|BNTV400|BNTV600|LogicPD Zoom2",
        "AcerTablet": "Android.*; \\b(A100|A101|A110|A200|A210|A211|A500|A501|A510|A511|A700|A701|W500|W500P|W501|W501P|W510|W511|W700|G100|G100W|B1-A71|B1-710|B1-711|A1-810|A1-811|A1-830)\\b|W3-810|\\bA3-A10\\b|\\bA3-A11\\b|\\bA3-A20\\b|\\bA3-A30|A3-A40",
        "ToshibaTablet": "Android.*(AT100|AT105|AT200|AT205|AT270|AT275|AT300|AT305|AT1S5|AT500|AT570|AT700|AT830)|TOSHIBA.*FOLIO",
        "LGTablet": "\\bL-06C|LG-V909|LG-V900|LG-V700|LG-V510|LG-V500|LG-V410|LG-V400|LG-VK810\\b",
        "FujitsuTablet": "Android.*\\b(F-01D|F-02F|F-05E|F-10D|M532|Q572)\\b",
        "PrestigioTablet": "PMP3170B|PMP3270B|PMP3470B|PMP7170B|PMP3370B|PMP3570C|PMP5870C|PMP3670B|PMP5570C|PMP5770D|PMP3970B|PMP3870C|PMP5580C|PMP5880D|PMP5780D|PMP5588C|PMP7280C|PMP7280C3G|PMP7280|PMP7880D|PMP5597D|PMP5597|PMP7100D|PER3464|PER3274|PER3574|PER3884|PER5274|PER5474|PMP5097CPRO|PMP5097|PMP7380D|PMP5297C|PMP5297C_QUAD|PMP812E|PMP812E3G|PMP812F|PMP810E|PMP880TD|PMT3017|PMT3037|PMT3047|PMT3057|PMT7008|PMT5887|PMT5001|PMT5002",
        "LenovoTablet": "Lenovo TAB|Idea(Tab|Pad)( A1|A10| K1|)|ThinkPad([ ]+)?Tablet|YT3-850M|YT3-X90L|YT3-X90F|YT3-X90X|Lenovo.*(S2109|S2110|S5000|S6000|K3011|A3000|A3500|A1000|A2107|A2109|A1107|A5500|A7600|B6000|B8000|B8080)(-|)(FL|F|HV|H|)|TB-X103F|TB-X304X|TB-X304F|TB-X304L|TB-X505F|TB-X505L|TB-X505X|TB-X605F|TB-X605L|TB-8703F|TB-8703X|TB-8703N|TB-8704N|TB-8704F|TB-8704X|TB-8704V|TB-7304F|TB-7304I|TB-7304X|Tab2A7-10F|Tab2A7-20F|TB2-X30L|YT3-X50L|YT3-X50F|YT3-X50M|YT-X705F|YT-X703F|YT-X703L|YT-X705L|YT-X705X|TB2-X30F|TB2-X30L|TB2-X30M|A2107A-F|A2107A-H|TB3-730F|TB3-730M|TB3-730X|TB-7504F|TB-7504X|TB-X704F|TB-X104F|TB3-X70F|TB-X705F|TB-8504F|TB3-X70L|TB3-710F|TB-X704L",
        "DellTablet": "Venue 11|Venue 8|Venue 7|Dell Streak 10|Dell Streak 7",
        "YarvikTablet": "Android.*\\b(TAB210|TAB211|TAB224|TAB250|TAB260|TAB264|TAB310|TAB360|TAB364|TAB410|TAB411|TAB420|TAB424|TAB450|TAB460|TAB461|TAB464|TAB465|TAB467|TAB468|TAB07-100|TAB07-101|TAB07-150|TAB07-151|TAB07-152|TAB07-200|TAB07-201-3G|TAB07-210|TAB07-211|TAB07-212|TAB07-214|TAB07-220|TAB07-400|TAB07-485|TAB08-150|TAB08-200|TAB08-201-3G|TAB08-201-30|TAB09-100|TAB09-211|TAB09-410|TAB10-150|TAB10-201|TAB10-211|TAB10-400|TAB10-410|TAB13-201|TAB274EUK|TAB275EUK|TAB374EUK|TAB462EUK|TAB474EUK|TAB9-200)\\b",
        "MedionTablet": "Android.*\\bOYO\\b|LIFE.*(P9212|P9514|P9516|S9512)|LIFETAB",
        "ArnovaTablet": "97G4|AN10G2|AN7bG3|AN7fG3|AN8G3|AN8cG3|AN7G3|AN9G3|AN7dG3|AN7dG3ST|AN7dG3ChildPad|AN10bG3|AN10bG3DT|AN9G2",
        "IntensoTablet": "INM8002KP|INM1010FP|INM805ND|Intenso Tab|TAB1004",
        "IRUTablet": "M702pro",
        "MegafonTablet": "MegaFon V9|\\bZTE V9\\b|Android.*\\bMT7A\\b",
        "EbodaTablet": "E-Boda (Supreme|Impresspeed|Izzycomm|Essential)",
        "AllViewTablet": "Allview.*(Viva|Alldro|City|Speed|All TV|Frenzy|Quasar|Shine|TX1|AX1|AX2)",
        "ArchosTablet": "\\b(101G9|80G9|A101IT)\\b|Qilive 97R|Archos5|\\bARCHOS (70|79|80|90|97|101|FAMILYPAD|)(b|c|)(G10| Cobalt| TITANIUM(HD|)| Xenon| Neon|XSK| 2| XS 2| PLATINUM| CARBON|GAMEPAD)\\b",
        "AinolTablet": "NOVO7|NOVO8|NOVO10|Novo7Aurora|Novo7Basic|NOVO7PALADIN|novo9-Spark",
        "NokiaLumiaTablet": "Lumia 2520",
        "SonyTablet": "Sony.*Tablet|Xperia Tablet|Sony Tablet S|SO-03E|SGPT12|SGPT13|SGPT114|SGPT121|SGPT122|SGPT123|SGPT111|SGPT112|SGPT113|SGPT131|SGPT132|SGPT133|SGPT211|SGPT212|SGPT213|SGP311|SGP312|SGP321|EBRD1101|EBRD1102|EBRD1201|SGP351|SGP341|SGP511|SGP512|SGP521|SGP541|SGP551|SGP621|SGP641|SGP612|SOT31|SGP771|SGP611|SGP612|SGP712",
        "PhilipsTablet": "\\b(PI2010|PI3000|PI3100|PI3105|PI3110|PI3205|PI3210|PI3900|PI4010|PI7000|PI7100)\\b",
        "CubeTablet": "Android.*(K8GT|U9GT|U10GT|U16GT|U17GT|U18GT|U19GT|U20GT|U23GT|U30GT)|CUBE U8GT",
        "CobyTablet": "MID1042|MID1045|MID1125|MID1126|MID7012|MID7014|MID7015|MID7034|MID7035|MID7036|MID7042|MID7048|MID7127|MID8042|MID8048|MID8127|MID9042|MID9740|MID9742|MID7022|MID7010",
        "MIDTablet": "M9701|M9000|M9100|M806|M1052|M806|T703|MID701|MID713|MID710|MID727|MID760|MID830|MID728|MID933|MID125|MID810|MID732|MID120|MID930|MID800|MID731|MID900|MID100|MID820|MID735|MID980|MID130|MID833|MID737|MID960|MID135|MID860|MID736|MID140|MID930|MID835|MID733|MID4X10",
        "MSITablet": "MSI \\b(Primo 73K|Primo 73L|Primo 81L|Primo 77|Primo 93|Primo 75|Primo 76|Primo 73|Primo 81|Primo 91|Primo 90|Enjoy 71|Enjoy 7|Enjoy 10)\\b",
        "SMiTTablet": "Android.*(\\bMID\\b|MID-560|MTV-T1200|MTV-PND531|MTV-P1101|MTV-PND530)",
        "RockChipTablet": "Android.*(RK2818|RK2808A|RK2918|RK3066)|RK2738|RK2808A",
        "FlyTablet": "IQ310|Fly Vision",
        "bqTablet": "Android.*(bq)?.*\\b(Elcano|Curie|Edison|Maxwell|Kepler|Pascal|Tesla|Hypatia|Platon|Newton|Livingstone|Cervantes|Avant|Aquaris ([E|M]10|M8))\\b|Maxwell.*Lite|Maxwell.*Plus",
        "HuaweiTablet": "MediaPad|MediaPad 7 Youth|IDEOS S7|S7-201c|S7-202u|S7-101|S7-103|S7-104|S7-105|S7-106|S7-201|S7-Slim|M2-A01L|BAH-L09|BAH-W09|AGS-L09|CMR-AL19",
        "NecTablet": "\\bN-06D|\\bN-08D",
        "PantechTablet": "Pantech.*P4100",
        "BronchoTablet": "Broncho.*(N701|N708|N802|a710)",
        "VersusTablet": "TOUCHPAD.*[78910]|\\bTOUCHTAB\\b",
        "ZyncTablet": "z1000|Z99 2G|z930|z990|z909|Z919|z900",
        "PositivoTablet": "TB07STA|TB10STA|TB07FTA|TB10FTA",
        "NabiTablet": "Android.*\\bNabi",
        "KoboTablet": "Kobo Touch|\\bK080\\b|\\bVox\\b Build|\\bArc\\b Build",
        "DanewTablet": "DSlide.*\\b(700|701R|702|703R|704|802|970|971|972|973|974|1010|1012)\\b",
        "TexetTablet": "NaviPad|TB-772A|TM-7045|TM-7055|TM-9750|TM-7016|TM-7024|TM-7026|TM-7041|TM-7043|TM-7047|TM-8041|TM-9741|TM-9747|TM-9748|TM-9751|TM-7022|TM-7021|TM-7020|TM-7011|TM-7010|TM-7023|TM-7025|TM-7037W|TM-7038W|TM-7027W|TM-9720|TM-9725|TM-9737W|TM-1020|TM-9738W|TM-9740|TM-9743W|TB-807A|TB-771A|TB-727A|TB-725A|TB-719A|TB-823A|TB-805A|TB-723A|TB-715A|TB-707A|TB-705A|TB-709A|TB-711A|TB-890HD|TB-880HD|TB-790HD|TB-780HD|TB-770HD|TB-721HD|TB-710HD|TB-434HD|TB-860HD|TB-840HD|TB-760HD|TB-750HD|TB-740HD|TB-730HD|TB-722HD|TB-720HD|TB-700HD|TB-500HD|TB-470HD|TB-431HD|TB-430HD|TB-506|TB-504|TB-446|TB-436|TB-416|TB-146SE|TB-126SE",
        "PlaystationTablet": "Playstation.*(Portable|Vita)",
        "TrekstorTablet": "ST10416-1|VT10416-1|ST70408-1|ST702xx-1|ST702xx-2|ST80208|ST97216|ST70104-2|VT10416-2|ST10216-2A|SurfTab",
        "PyleAudioTablet": "\\b(PTBL10CEU|PTBL10C|PTBL72BC|PTBL72BCEU|PTBL7CEU|PTBL7C|PTBL92BC|PTBL92BCEU|PTBL9CEU|PTBL9CUK|PTBL9C)\\b",
        "AdvanTablet": "Android.* \\b(E3A|T3X|T5C|T5B|T3E|T3C|T3B|T1J|T1F|T2A|T1H|T1i|E1C|T1-E|T5-A|T4|E1-B|T2Ci|T1-B|T1-D|O1-A|E1-A|T1-A|T3A|T4i)\\b ",
        "DanyTechTablet": "Genius Tab G3|Genius Tab S2|Genius Tab Q3|Genius Tab G4|Genius Tab Q4|Genius Tab G-II|Genius TAB GII|Genius TAB GIII|Genius Tab S1",
        "GalapadTablet": "Android [0-9.]+; [a-z-]+; \\bG1\\b",
        "MicromaxTablet": "Funbook|Micromax.*\\b(P250|P560|P360|P362|P600|P300|P350|P500|P275)\\b",
        "KarbonnTablet": "Android.*\\b(A39|A37|A34|ST8|ST10|ST7|Smart Tab3|Smart Tab2)\\b",
        "AllFineTablet": "Fine7 Genius|Fine7 Shine|Fine7 Air|Fine8 Style|Fine9 More|Fine10 Joy|Fine11 Wide",
        "PROSCANTablet": "\\b(PEM63|PLT1023G|PLT1041|PLT1044|PLT1044G|PLT1091|PLT4311|PLT4311PL|PLT4315|PLT7030|PLT7033|PLT7033D|PLT7035|PLT7035D|PLT7044K|PLT7045K|PLT7045KB|PLT7071KG|PLT7072|PLT7223G|PLT7225G|PLT7777G|PLT7810K|PLT7849G|PLT7851G|PLT7852G|PLT8015|PLT8031|PLT8034|PLT8036|PLT8080K|PLT8082|PLT8088|PLT8223G|PLT8234G|PLT8235G|PLT8816K|PLT9011|PLT9045K|PLT9233G|PLT9735|PLT9760G|PLT9770G)\\b",
        "YONESTablet": "BQ1078|BC1003|BC1077|RK9702|BC9730|BC9001|IT9001|BC7008|BC7010|BC708|BC728|BC7012|BC7030|BC7027|BC7026",
        "ChangJiaTablet": "TPC7102|TPC7103|TPC7105|TPC7106|TPC7107|TPC7201|TPC7203|TPC7205|TPC7210|TPC7708|TPC7709|TPC7712|TPC7110|TPC8101|TPC8103|TPC8105|TPC8106|TPC8203|TPC8205|TPC8503|TPC9106|TPC9701|TPC97101|TPC97103|TPC97105|TPC97106|TPC97111|TPC97113|TPC97203|TPC97603|TPC97809|TPC97205|TPC10101|TPC10103|TPC10106|TPC10111|TPC10203|TPC10205|TPC10503",
        "GUTablet": "TX-A1301|TX-M9002|Q702|kf026",
        "PointOfViewTablet": "TAB-P506|TAB-navi-7-3G-M|TAB-P517|TAB-P-527|TAB-P701|TAB-P703|TAB-P721|TAB-P731N|TAB-P741|TAB-P825|TAB-P905|TAB-P925|TAB-PR945|TAB-PL1015|TAB-P1025|TAB-PI1045|TAB-P1325|TAB-PROTAB[0-9]+|TAB-PROTAB25|TAB-PROTAB26|TAB-PROTAB27|TAB-PROTAB26XL|TAB-PROTAB2-IPS9|TAB-PROTAB30-IPS9|TAB-PROTAB25XXL|TAB-PROTAB26-IPS10|TAB-PROTAB30-IPS10",
        "OvermaxTablet": "OV-(SteelCore|NewBase|Basecore|Baseone|Exellen|Quattor|EduTab|Solution|ACTION|BasicTab|TeddyTab|MagicTab|Stream|TB-08|TB-09)|Qualcore 1027",
        "HCLTablet": "HCL.*Tablet|Connect-3G-2.0|Connect-2G-2.0|ME Tablet U1|ME Tablet U2|ME Tablet G1|ME Tablet X1|ME Tablet Y2|ME Tablet Sync",
        "DPSTablet": "DPS Dream 9|DPS Dual 7",
        "VistureTablet": "V97 HD|i75 3G|Visture V4( HD)?|Visture V5( HD)?|Visture V10",
        "CrestaTablet": "CTP(-)?810|CTP(-)?818|CTP(-)?828|CTP(-)?838|CTP(-)?888|CTP(-)?978|CTP(-)?980|CTP(-)?987|CTP(-)?988|CTP(-)?989",
        "MediatekTablet": "\\bMT8125|MT8389|MT8135|MT8377\\b",
        "ConcordeTablet": "Concorde([ ]+)?Tab|ConCorde ReadMan",
        "GoCleverTablet": "GOCLEVER TAB|A7GOCLEVER|M1042|M7841|M742|R1042BK|R1041|TAB A975|TAB A7842|TAB A741|TAB A741L|TAB M723G|TAB M721|TAB A1021|TAB I921|TAB R721|TAB I720|TAB T76|TAB R70|TAB R76.2|TAB R106|TAB R83.2|TAB M813G|TAB I721|GCTA722|TAB I70|TAB I71|TAB S73|TAB R73|TAB R74|TAB R93|TAB R75|TAB R76.1|TAB A73|TAB A93|TAB A93.2|TAB T72|TAB R83|TAB R974|TAB R973|TAB A101|TAB A103|TAB A104|TAB A104.2|R105BK|M713G|A972BK|TAB A971|TAB R974.2|TAB R104|TAB R83.3|TAB A1042",
        "ModecomTablet": "FreeTAB 9000|FreeTAB 7.4|FreeTAB 7004|FreeTAB 7800|FreeTAB 2096|FreeTAB 7.5|FreeTAB 1014|FreeTAB 1001 |FreeTAB 8001|FreeTAB 9706|FreeTAB 9702|FreeTAB 7003|FreeTAB 7002|FreeTAB 1002|FreeTAB 7801|FreeTAB 1331|FreeTAB 1004|FreeTAB 8002|FreeTAB 8014|FreeTAB 9704|FreeTAB 1003",
        "VoninoTablet": "\\b(Argus[ _]?S|Diamond[ _]?79HD|Emerald[ _]?78E|Luna[ _]?70C|Onyx[ _]?S|Onyx[ _]?Z|Orin[ _]?HD|Orin[ _]?S|Otis[ _]?S|SpeedStar[ _]?S|Magnet[ _]?M9|Primus[ _]?94[ _]?3G|Primus[ _]?94HD|Primus[ _]?QS|Android.*\\bQ8\\b|Sirius[ _]?EVO[ _]?QS|Sirius[ _]?QS|Spirit[ _]?S)\\b",
        "ECSTablet": "V07OT2|TM105A|S10OT1|TR10CS1",
        "StorexTablet": "eZee[_']?(Tab|Go)[0-9]+|TabLC7|Looney Tunes Tab",
        "VodafoneTablet": "SmartTab([ ]+)?[0-9]+|SmartTabII10|SmartTabII7|VF-1497|VFD 1400",
        "EssentielBTablet": "Smart[ ']?TAB[ ]+?[0-9]+|Family[ ']?TAB2",
        "RossMoorTablet": "RM-790|RM-997|RMD-878G|RMD-974R|RMT-705A|RMT-701|RME-601|RMT-501|RMT-711",
        "iMobileTablet": "i-mobile i-note",
        "TolinoTablet": "tolino tab [0-9.]+|tolino shine",
        "AudioSonicTablet": "\\bC-22Q|T7-QC|T-17B|T-17P\\b",
        "AMPETablet": "Android.* A78 ",
        "SkkTablet": "Android.* (SKYPAD|PHOENIX|CYCLOPS)",
        "TecnoTablet": "TECNO P9|TECNO DP8D",
        "JXDTablet": "Android.* \\b(F3000|A3300|JXD5000|JXD3000|JXD2000|JXD300B|JXD300|S5800|S7800|S602b|S5110b|S7300|S5300|S602|S603|S5100|S5110|S601|S7100a|P3000F|P3000s|P101|P200s|P1000m|P200m|P9100|P1000s|S6600b|S908|P1000|P300|S18|S6600|S9100)\\b",
        "iJoyTablet": "Tablet (Spirit 7|Essentia|Galatea|Fusion|Onix 7|Landa|Titan|Scooby|Deox|Stella|Themis|Argon|Unique 7|Sygnus|Hexen|Finity 7|Cream|Cream X2|Jade|Neon 7|Neron 7|Kandy|Scape|Saphyr 7|Rebel|Biox|Rebel|Rebel 8GB|Myst|Draco 7|Myst|Tab7-004|Myst|Tadeo Jones|Tablet Boing|Arrow|Draco Dual Cam|Aurix|Mint|Amity|Revolution|Finity 9|Neon 9|T9w|Amity 4GB Dual Cam|Stone 4GB|Stone 8GB|Andromeda|Silken|X2|Andromeda II|Halley|Flame|Saphyr 9,7|Touch 8|Planet|Triton|Unique 10|Hexen 10|Memphis 4GB|Memphis 8GB|Onix 10)",
        "FX2Tablet": "FX2 PAD7|FX2 PAD10",
        "XoroTablet": "KidsPAD 701|PAD[ ]?712|PAD[ ]?714|PAD[ ]?716|PAD[ ]?717|PAD[ ]?718|PAD[ ]?720|PAD[ ]?721|PAD[ ]?722|PAD[ ]?790|PAD[ ]?792|PAD[ ]?900|PAD[ ]?9715D|PAD[ ]?9716DR|PAD[ ]?9718DR|PAD[ ]?9719QR|PAD[ ]?9720QR|TelePAD1030|Telepad1032|TelePAD730|TelePAD731|TelePAD732|TelePAD735Q|TelePAD830|TelePAD9730|TelePAD795|MegaPAD 1331|MegaPAD 1851|MegaPAD 2151",
        "ViewsonicTablet": "ViewPad 10pi|ViewPad 10e|ViewPad 10s|ViewPad E72|ViewPad7|ViewPad E100|ViewPad 7e|ViewSonic VB733|VB100a",
        "VerizonTablet": "QTAQZ3|QTAIR7|QTAQTZ3|QTASUN1|QTASUN2|QTAXIA1",
        "OdysTablet": "LOOX|XENO10|ODYS[ -](Space|EVO|Xpress|NOON)|\\bXELIO\\b|Xelio10Pro|XELIO7PHONETAB|XELIO10EXTREME|XELIOPT2|NEO_QUAD10",
        "CaptivaTablet": "CAPTIVA PAD",
        "IconbitTablet": "NetTAB|NT-3702|NT-3702S|NT-3702S|NT-3603P|NT-3603P|NT-0704S|NT-0704S|NT-3805C|NT-3805C|NT-0806C|NT-0806C|NT-0909T|NT-0909T|NT-0907S|NT-0907S|NT-0902S|NT-0902S",
        "TeclastTablet": "T98 4G|\\bP80\\b|\\bX90HD\\b|X98 Air|X98 Air 3G|\\bX89\\b|P80 3G|\\bX80h\\b|P98 Air|\\bX89HD\\b|P98 3G|\\bP90HD\\b|P89 3G|X98 3G|\\bP70h\\b|P79HD 3G|G18d 3G|\\bP79HD\\b|\\bP89s\\b|\\bA88\\b|\\bP10HD\\b|\\bP19HD\\b|G18 3G|\\bP78HD\\b|\\bA78\\b|\\bP75\\b|G17s 3G|G17h 3G|\\bP85t\\b|\\bP90\\b|\\bP11\\b|\\bP98t\\b|\\bP98HD\\b|\\bG18d\\b|\\bP85s\\b|\\bP11HD\\b|\\bP88s\\b|\\bA80HD\\b|\\bA80se\\b|\\bA10h\\b|\\bP89\\b|\\bP78s\\b|\\bG18\\b|\\bP85\\b|\\bA70h\\b|\\bA70\\b|\\bG17\\b|\\bP18\\b|\\bA80s\\b|\\bA11s\\b|\\bP88HD\\b|\\bA80h\\b|\\bP76s\\b|\\bP76h\\b|\\bP98\\b|\\bA10HD\\b|\\bP78\\b|\\bP88\\b|\\bA11\\b|\\bA10t\\b|\\bP76a\\b|\\bP76t\\b|\\bP76e\\b|\\bP85HD\\b|\\bP85a\\b|\\bP86\\b|\\bP75HD\\b|\\bP76v\\b|\\bA12\\b|\\bP75a\\b|\\bA15\\b|\\bP76Ti\\b|\\bP81HD\\b|\\bA10\\b|\\bT760VE\\b|\\bT720HD\\b|\\bP76\\b|\\bP73\\b|\\bP71\\b|\\bP72\\b|\\bT720SE\\b|\\bC520Ti\\b|\\bT760\\b|\\bT720VE\\b|T720-3GE|T720-WiFi",
        "OndaTablet": "\\b(V975i|Vi30|VX530|V701|Vi60|V701s|Vi50|V801s|V719|Vx610w|VX610W|V819i|Vi10|VX580W|Vi10|V711s|V813|V811|V820w|V820|Vi20|V711|VI30W|V712|V891w|V972|V819w|V820w|Vi60|V820w|V711|V813s|V801|V819|V975s|V801|V819|V819|V818|V811|V712|V975m|V101w|V961w|V812|V818|V971|V971s|V919|V989|V116w|V102w|V973|Vi40)\\b[\\s]+|V10 \\b4G\\b",
        "JaytechTablet": "TPC-PA762",
        "BlaupunktTablet": "Endeavour 800NG|Endeavour 1010",
        "DigmaTablet": "\\b(iDx10|iDx9|iDx8|iDx7|iDxD7|iDxD8|iDsQ8|iDsQ7|iDsQ8|iDsD10|iDnD7|3TS804H|iDsQ11|iDj7|iDs10)\\b",
        "EvolioTablet": "ARIA_Mini_wifi|Aria[ _]Mini|Evolio X10|Evolio X7|Evolio X8|\\bEvotab\\b|\\bNeura\\b",
        "LavaTablet": "QPAD E704|\\bIvoryS\\b|E-TAB IVORY|\\bE-TAB\\b",
        "AocTablet": "MW0811|MW0812|MW0922|MTK8382|MW1031|MW0831|MW0821|MW0931|MW0712",
        "MpmanTablet": "MP11 OCTA|MP10 OCTA|MPQC1114|MPQC1004|MPQC994|MPQC974|MPQC973|MPQC804|MPQC784|MPQC780|\\bMPG7\\b|MPDCG75|MPDCG71|MPDC1006|MP101DC|MPDC9000|MPDC905|MPDC706HD|MPDC706|MPDC705|MPDC110|MPDC100|MPDC99|MPDC97|MPDC88|MPDC8|MPDC77|MP709|MID701|MID711|MID170|MPDC703|MPQC1010",
        "CelkonTablet": "CT695|CT888|CT[\\s]?910|CT7 Tab|CT9 Tab|CT3 Tab|CT2 Tab|CT1 Tab|C820|C720|\\bCT-1\\b",
        "WolderTablet": "miTab \\b(DIAMOND|SPACE|BROOKLYN|NEO|FLY|MANHATTAN|FUNK|EVOLUTION|SKY|GOCAR|IRON|GENIUS|POP|MINT|EPSILON|BROADWAY|JUMP|HOP|LEGEND|NEW AGE|LINE|ADVANCE|FEEL|FOLLOW|LIKE|LINK|LIVE|THINK|FREEDOM|CHICAGO|CLEVELAND|BALTIMORE-GH|IOWA|BOSTON|SEATTLE|PHOENIX|DALLAS|IN 101|MasterChef)\\b",
        "MediacomTablet": "M-MPI10C3G|M-SP10EG|M-SP10EGP|M-SP10HXAH|M-SP7HXAH|M-SP10HXBH|M-SP8HXAH|M-SP8MXA",
        "MiTablet": "\\bMI PAD\\b|\\bHM NOTE 1W\\b",
        "NibiruTablet": "Nibiru M1|Nibiru Jupiter One",
        "NexoTablet": "NEXO NOVA|NEXO 10|NEXO AVIO|NEXO FREE|NEXO GO|NEXO EVO|NEXO 3G|NEXO SMART|NEXO KIDDO|NEXO MOBI",
        "LeaderTablet": "TBLT10Q|TBLT10I|TBL-10WDKB|TBL-10WDKBO2013|TBL-W230V2|TBL-W450|TBL-W500|SV572|TBLT7I|TBA-AC7-8G|TBLT79|TBL-8W16|TBL-10W32|TBL-10WKB|TBL-W100",
        "UbislateTablet": "UbiSlate[\\s]?7C",
        "PocketBookTablet": "Pocketbook",
        "KocasoTablet": "\\b(TB-1207)\\b",
        "HisenseTablet": "\\b(F5281|E2371)\\b",
        "Hudl": "Hudl HT7S3|Hudl 2",
        "TelstraTablet": "T-Hub2",
        "GenericTablet": "Android.*\\b97D\\b|Tablet(?!.*PC)|BNTV250A|MID-WCDMA|LogicPD Zoom2|\\bA7EB\\b|CatNova8|A1_07|CT704|CT1002|\\bM721\\b|rk30sdk|\\bEVOTAB\\b|M758A|ET904|ALUMIUM10|Smartfren Tab|Endeavour 1010|Tablet-PC-4|Tagi Tab|\\bM6pro\\b|CT1020W|arc 10HD|\\bTP750\\b|\\bQTAQZ3\\b|WVT101|TM1088|KT107"
    },
    "oss": {
        "AndroidOS": "Android",
        "BlackBerryOS": "blackberry|\\bBB10\\b|rim tablet os",
        "PalmOS": "PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino",
        "SymbianOS": "Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\\bS60\\b",
        "WindowsMobileOS": "Windows CE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Windows Mobile|Windows Phone [0-9.]+|WCE;",
        "WindowsPhoneOS": "Windows Phone 10.0|Windows Phone 8.1|Windows Phone 8.0|Windows Phone OS|XBLWP7|ZuneWP7|Windows NT 6.[23]; ARM;",
        "iOS": "\\biPhone.*Mobile|\\biPod|\\biPad|AppleCoreMedia",
        "iPadOS": "CPU OS 13",
        "SailfishOS": "Sailfish",
        "MeeGoOS": "MeeGo",
        "MaemoOS": "Maemo",
        "JavaOS": "J2ME\/|\\bMIDP\\b|\\bCLDC\\b",
        "webOS": "webOS|hpwOS",
        "badaOS": "\\bBada\\b",
        "BREWOS": "BREW"
    },
    "uas": {
        "Chrome": "\\bCrMo\\b|CriOS|Android.*Chrome\/[.0-9]* (Mobile)?",
        "Dolfin": "\\bDolfin\\b",
        "Opera": "Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR\/[0-9.]+$|Coast\/[0-9.]+",
        "Skyfire": "Skyfire",
        "Edge": "\\bEdgiOS\\b|Mobile Safari\/[.0-9]* Edge",
        "IE": "IEMobile|MSIEMobile",
        "Firefox": "fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile|FxiOS",
        "Bolt": "bolt",
        "TeaShark": "teashark",
        "Blazer": "Blazer",
        "Safari": "Version((?!\\bEdgiOS\\b).)*Mobile.*Safari|Safari.*Mobile|MobileSafari",
        "WeChat": "\\bMicroMessenger\\b",
        "UCBrowser": "UC.*Browser|UCWEB",
        "baiduboxapp": "baiduboxapp",
        "baidubrowser": "baidubrowser",
        "DiigoBrowser": "DiigoBrowser",
        "Mercury": "\\bMercury\\b",
        "ObigoBrowser": "Obigo",
        "NetFront": "NF-Browser",
        "GenericBrowser": "NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger",
        "PaleMoon": "Android.*PaleMoon|Mobile.*PaleMoon"
    },
    "props": {
        "Mobile": "Mobile\/[VER]",
        "Build": "Build\/[VER]",
        "Version": "Version\/[VER]",
        "VendorID": "VendorID\/[VER]",
        "iPad": "iPad.*CPU[a-z ]+[VER]",
        "iPhone": "iPhone.*CPU[a-z ]+[VER]",
        "iPod": "iPod.*CPU[a-z ]+[VER]",
        "Kindle": "Kindle\/[VER]",
        "Chrome": [
            "Chrome\/[VER]",
            "CriOS\/[VER]",
            "CrMo\/[VER]"
        ],
        "Coast": [
            "Coast\/[VER]"
        ],
        "Dolfin": "Dolfin\/[VER]",
        "Firefox": [
            "Firefox\/[VER]",
            "FxiOS\/[VER]"
        ],
        "Fennec": "Fennec\/[VER]",
        "Edge": "Edge\/[VER]",
        "IE": [
            "IEMobile\/[VER];",
            "IEMobile [VER]",
            "MSIE [VER];",
            "Trident\/[0-9.]+;.*rv:[VER]"
        ],
        "NetFront": "NetFront\/[VER]",
        "NokiaBrowser": "NokiaBrowser\/[VER]",
        "Opera": [
            " OPR\/[VER]",
            "Opera Mini\/[VER]",
            "Version\/[VER]"
        ],
        "Opera Mini": "Opera Mini\/[VER]",
        "Opera Mobi": "Version\/[VER]",
        "UCBrowser": [
            "UCWEB[VER]",
            "UC.*Browser\/[VER]"
        ],
        "MQQBrowser": "MQQBrowser\/[VER]",
        "MicroMessenger": "MicroMessenger\/[VER]",
        "baiduboxapp": "baiduboxapp\/[VER]",
        "baidubrowser": "baidubrowser\/[VER]",
        "SamsungBrowser": "SamsungBrowser\/[VER]",
        "Iron": "Iron\/[VER]",
        "Safari": [
            "Version\/[VER]",
            "Safari\/[VER]"
        ],
        "Skyfire": "Skyfire\/[VER]",
        "Tizen": "Tizen\/[VER]",
        "Webkit": "webkit[ \/][VER]",
        "PaleMoon": "PaleMoon\/[VER]",
        "SailfishBrowser": "SailfishBrowser\/[VER]",
        "Gecko": "Gecko\/[VER]",
        "Trident": "Trident\/[VER]",
        "Presto": "Presto\/[VER]",
        "Goanna": "Goanna\/[VER]",
        "iOS": " \\bi?OS\\b [VER][ ;]{1}",
        "Android": "Android [VER]",
        "Sailfish": "Sailfish [VER]",
        "BlackBerry": [
            "BlackBerry[\\w]+\/[VER]",
            "BlackBerry.*Version\/[VER]",
            "Version\/[VER]"
        ],
        "BREW": "BREW [VER]",
        "Java": "Java\/[VER]",
        "Windows Phone OS": [
            "Windows Phone OS [VER]",
            "Windows Phone [VER]"
        ],
        "Windows Phone": "Windows Phone [VER]",
        "Windows CE": "Windows CE\/[VER]",
        "Windows NT": "Windows NT [VER]",
        "Symbian": [
            "SymbianOS\/[VER]",
            "Symbian\/[VER]"
        ],
        "webOS": [
            "webOS\/[VER]",
            "hpwOS\/[VER];"
        ]
    },
    "utils": {
        "Bot": "Googlebot|facebookexternalhit|Google-AMPHTML|s~amp-validator|AdsBot-Google|Google Keyword Suggestion|Facebot|YandexBot|YandexMobileBot|bingbot|ia_archiver|AhrefsBot|Ezooms|GSLFbot|WBSearchBot|Twitterbot|TweetmemeBot|Twikle|PaperLiBot|Wotbox|UnwindFetchor|Exabot|MJ12bot|YandexImages|TurnitinBot|Pingdom|contentkingapp|AspiegelBot",
        "MobileBot": "Googlebot-Mobile|AdsBot-Google-Mobile|YahooSeeker\/M1A1-R2D2",
        "DesktopMode": "WPDesktop",
        "TV": "SonyDTV|HbbTV",
        "WebKit": "(webkit)[ \/]([\\w.]+)",
        "Console": "\\b(Nintendo|Nintendo WiiU|Nintendo 3DS|Nintendo Switch|PLAYSTATION|Xbox)\\b",
        "Watch": "SM-V700"
    }
};

    // following patterns come from http://detectmobilebrowsers.com/
    impl.detectMobileBrowsers = {
        fullPattern: /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i,
        shortPattern: /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i,
        tabletPattern: /android|ipad|playbook|silk/i
    };

    var hasOwnProp = Object.prototype.hasOwnProperty,
        isArray;

    impl.FALLBACK_PHONE = 'UnknownPhone';
    impl.FALLBACK_TABLET = 'UnknownTablet';
    impl.FALLBACK_MOBILE = 'UnknownMobile';

    isArray = ('isArray' in Array) ?
        Array.isArray : function (value) { return Object.prototype.toString.call(value) === '[object Array]'; };

    function equalIC(a, b) {
        return a != null && b != null && a.toLowerCase() === b.toLowerCase();
    }

    function containsIC(array, value) {
        var valueLC, i, len = array.length;
        if (!len || !value) {
            return false;
        }
        valueLC = value.toLowerCase();
        for (i = 0; i < len; ++i) {
            if (valueLC === array[i].toLowerCase()) {
                return true;
            }
        }
        return false;
    }

    function convertPropsToRegExp(object) {
        for (var key in object) {
            if (hasOwnProp.call(object, key)) {
                object[key] = new RegExp(object[key], 'i');
            }
        }
    }

    function prepareUserAgent(userAgent) {
        return (userAgent || '').substr(0, 500); // mitigate vulnerable to ReDoS
    }

    (function init() {
        var key, values, value, i, len, verPos, mobileDetectRules = impl.mobileDetectRules;
        for (key in mobileDetectRules.props) {
            if (hasOwnProp.call(mobileDetectRules.props, key)) {
                values = mobileDetectRules.props[key];
                if (!isArray(values)) {
                    values = [values];
                }
                len = values.length;
                for (i = 0; i < len; ++i) {
                    value = values[i];
                    verPos = value.indexOf('[VER]');
                    if (verPos >= 0) {
                        value = value.substring(0, verPos) + '([\\w._\\+]+)' + value.substring(verPos + 5);
                    }
                    values[i] = new RegExp(value, 'i');
                }
                mobileDetectRules.props[key] = values;
            }
        }
        convertPropsToRegExp(mobileDetectRules.oss);
        convertPropsToRegExp(mobileDetectRules.phones);
        convertPropsToRegExp(mobileDetectRules.tablets);
        convertPropsToRegExp(mobileDetectRules.uas);
        convertPropsToRegExp(mobileDetectRules.utils);

        // copy some patterns to oss0 which are tested first (see issue#15)
        mobileDetectRules.oss0 = {
            WindowsPhoneOS: mobileDetectRules.oss.WindowsPhoneOS,
            WindowsMobileOS: mobileDetectRules.oss.WindowsMobileOS
        };
    }());

    /**
     * Test userAgent string against a set of rules and find the first matched key.
     * @param {Object} rules (key is String, value is RegExp)
     * @param {String} userAgent the navigator.userAgent (or HTTP-Header 'User-Agent').
     * @returns {String|null} the matched key if found, otherwise <tt>null</tt>
     * @private
     */
    impl.findMatch = function(rules, userAgent) {
        for (var key in rules) {
            if (hasOwnProp.call(rules, key)) {
                if (rules[key].test(userAgent)) {
                    return key;
                }
            }
        }
        return null;
    };

    /**
     * Test userAgent string against a set of rules and return an array of matched keys.
     * @param {Object} rules (key is String, value is RegExp)
     * @param {String} userAgent the navigator.userAgent (or HTTP-Header 'User-Agent').
     * @returns {Array} an array of matched keys, may be empty when there is no match, but not <tt>null</tt>
     * @private
     */
    impl.findMatches = function(rules, userAgent) {
        var result = [];
        for (var key in rules) {
            if (hasOwnProp.call(rules, key)) {
                if (rules[key].test(userAgent)) {
                    result.push(key);
                }
            }
        }
        return result;
    };

    /**
     * Check the version of the given property in the User-Agent.
     *
     * @param {String} propertyName
     * @param {String} userAgent
     * @return {String} version or <tt>null</tt> if version not found
     * @private
     */
    impl.getVersionStr = function (propertyName, userAgent) {
        var props = impl.mobileDetectRules.props, patterns, i, len, match;
        if (hasOwnProp.call(props, propertyName)) {
            patterns = props[propertyName];
            len = patterns.length;
            for (i = 0; i < len; ++i) {
                match = patterns[i].exec(userAgent);
                if (match !== null) {
                    return match[1];
                }
            }
        }
        return null;
    };

    /**
     * Check the version of the given property in the User-Agent.
     * Will return a float number. (eg. 2_0 will return 2.0, 4.3.1 will return 4.31)
     *
     * @param {String} propertyName
     * @param {String} userAgent
     * @return {Number} version or <tt>NaN</tt> if version not found
     * @private
     */
    impl.getVersion = function (propertyName, userAgent) {
        var version = impl.getVersionStr(propertyName, userAgent);
        return version ? impl.prepareVersionNo(version) : NaN;
    };

    /**
     * Prepare the version number.
     *
     * @param {String} version
     * @return {Number} the version number as a floating number
     * @private
     */
    impl.prepareVersionNo = function (version) {
        var numbers;

        numbers = version.split(/[a-z._ \/\-]/i);
        if (numbers.length === 1) {
            version = numbers[0];
        }
        if (numbers.length > 1) {
            version = numbers[0] + '.';
            numbers.shift();
            version += numbers.join('');
        }
        return Number(version);
    };

    impl.isMobileFallback = function (userAgent) {
        return impl.detectMobileBrowsers.fullPattern.test(userAgent) ||
            impl.detectMobileBrowsers.shortPattern.test(userAgent.substr(0,4));
    };

    impl.isTabletFallback = function (userAgent) {
        return impl.detectMobileBrowsers.tabletPattern.test(userAgent);
    };

    impl.prepareDetectionCache = function (cache, userAgent, maxPhoneWidth) {
        if (cache.mobile !== undefined) {
            return;
        }
        var phone, tablet, phoneSized;

        // first check for stronger tablet rules, then phone (see issue#5)
        tablet = impl.findMatch(impl.mobileDetectRules.tablets, userAgent);
        if (tablet) {
            cache.mobile = cache.tablet = tablet;
            cache.phone = null;
            return; // unambiguously identified as tablet
        }

        phone = impl.findMatch(impl.mobileDetectRules.phones, userAgent);
        if (phone) {
            cache.mobile = cache.phone = phone;
            cache.tablet = null;
            return; // unambiguously identified as phone
        }

        // our rules haven't found a match -> try more general fallback rules
        if (impl.isMobileFallback(userAgent)) {
            phoneSized = MobileDetect.isPhoneSized(maxPhoneWidth);
            if (phoneSized === undefined) {
                cache.mobile = impl.FALLBACK_MOBILE;
                cache.tablet = cache.phone = null;
            } else if (phoneSized) {
                cache.mobile = cache.phone = impl.FALLBACK_PHONE;
                cache.tablet = null;
            } else {
                cache.mobile = cache.tablet = impl.FALLBACK_TABLET;
                cache.phone = null;
            }
        } else if (impl.isTabletFallback(userAgent)) {
            cache.mobile = cache.tablet = impl.FALLBACK_TABLET;
            cache.phone = null;
        } else {
            // not mobile at all!
            cache.mobile = cache.tablet = cache.phone = null;
        }
    };

    // t is a reference to a MobileDetect instance
    impl.mobileGrade = function (t) {
        // impl note:
        // To keep in sync w/ Mobile_Detect.php easily, the following code is tightly aligned to the PHP version.
        // When changes are made in Mobile_Detect.php, copy this method and replace:
        //     $this-> / t.
        //     self::MOBILE_GRADE_(.) / '$1'
        //     , self::VERSION_TYPE_FLOAT / (nothing)
        //     isIOS() / os('iOS')
        //     [reg] / (nothing)   <-- jsdelivr complaining about unescaped unicode character U+00AE
        var $isMobile = t.mobile() !== null;

        if (
            // Apple iOS 3.2-5.1 - Tested on the original iPad (4.3 / 5.0), iPad 2 (4.3), iPad 3 (5.1), original iPhone (3.1), iPhone 3 (3.2), 3GS (4.3), 4 (4.3 / 5.0), and 4S (5.1)
            t.os('iOS') && t.version('iPad')>=4.3 ||
            t.os('iOS') && t.version('iPhone')>=3.1 ||
            t.os('iOS') && t.version('iPod')>=3.1 ||

            // Android 2.1-2.3 - Tested on the HTC Incredible (2.2), original Droid (2.2), HTC Aria (2.1), Google Nexus S (2.3). Functional on 1.5 & 1.6 but performance may be sluggish, tested on Google G1 (1.5)
            // Android 3.1 (Honeycomb)  - Tested on the Samsung Galaxy Tab 10.1 and Motorola XOOM
            // Android 4.0 (ICS)  - Tested on a Galaxy Nexus. Note: transition performance can be poor on upgraded devices
            // Android 4.1 (Jelly Bean)  - Tested on a Galaxy Nexus and Galaxy 7
            ( t.version('Android')>2.1 && t.is('Webkit') ) ||

            // Windows Phone 7-7.5 - Tested on the HTC Surround (7.0) HTC Trophy (7.5), LG-E900 (7.5), Nokia Lumia 800
            t.version('Windows Phone OS')>=7.0 ||

            // Blackberry 7 - Tested on BlackBerry Torch 9810
            // Blackberry 6.0 - Tested on the Torch 9800 and Style 9670
            t.is('BlackBerry') && t.version('BlackBerry')>=6.0 ||
            // Blackberry Playbook (1.0-2.0) - Tested on PlayBook
            t.match('Playbook.*Tablet') ||

            // Palm WebOS (1.4-2.0) - Tested on the Palm Pixi (1.4), Pre (1.4), Pre 2 (2.0)
            ( t.version('webOS')>=1.4 && t.match('Palm|Pre|Pixi') ) ||
            // Palm WebOS 3.0  - Tested on HP TouchPad
            t.match('hp.*TouchPad') ||

            // Firefox Mobile (12 Beta) - Tested on Android 2.3 device
            ( t.is('Firefox') && t.version('Firefox')>=12 ) ||

            // Chrome for Android - Tested on Android 4.0, 4.1 device
            ( t.is('Chrome') && t.is('AndroidOS') && t.version('Android')>=4.0 ) ||

            // Skyfire 4.1 - Tested on Android 2.3 device
            ( t.is('Skyfire') && t.version('Skyfire')>=4.1 && t.is('AndroidOS') && t.version('Android')>=2.3 ) ||

            // Opera Mobile 11.5-12: Tested on Android 2.3
            ( t.is('Opera') && t.version('Opera Mobi')>11 && t.is('AndroidOS') ) ||

            // Meego 1.2 - Tested on Nokia 950 and N9
            t.is('MeeGoOS') ||

            // Tizen (pre-release) - Tested on early hardware
            t.is('Tizen') ||

            // Samsung Bada 2.0 - Tested on a Samsung Wave 3, Dolphin browser
            // @todo: more tests here!
            t.is('Dolfin') && t.version('Bada')>=2.0 ||

            // UC Browser - Tested on Android 2.3 device
            ( (t.is('UC Browser') || t.is('Dolfin')) && t.version('Android')>=2.3 ) ||

            // Kindle 3 and Fire  - Tested on the built-in WebKit browser for each
            ( t.match('Kindle Fire') ||
                t.is('Kindle') && t.version('Kindle')>=3.0 ) ||

            // Nook Color 1.4.1 - Tested on original Nook Color, not Nook Tablet
            t.is('AndroidOS') && t.is('NookTablet') ||

            // Chrome Desktop 11-21 - Tested on OS X 10.7 and Windows 7
            t.version('Chrome')>=11 && !$isMobile ||

            // Safari Desktop 4-5 - Tested on OS X 10.7 and Windows 7
            t.version('Safari')>=5.0 && !$isMobile ||

            // Firefox Desktop 4-13 - Tested on OS X 10.7 and Windows 7
            t.version('Firefox')>=4.0 && !$isMobile ||

            // Internet Explorer 7-9 - Tested on Windows XP, Vista and 7
            t.version('MSIE')>=7.0 && !$isMobile ||

            // Opera Desktop 10-12 - Tested on OS X 10.7 and Windows 7
            // @reference: http://my.opera.com/community/openweb/idopera/
            t.version('Opera')>=10 && !$isMobile

            ){
            return 'A';
        }

        if (
            t.os('iOS') && t.version('iPad')<4.3 ||
            t.os('iOS') && t.version('iPhone')<3.1 ||
            t.os('iOS') && t.version('iPod')<3.1 ||

            // Blackberry 5.0: Tested on the Storm 2 9550, Bold 9770
            t.is('Blackberry') && t.version('BlackBerry')>=5 && t.version('BlackBerry')<6 ||

            //Opera Mini (5.0-6.5) - Tested on iOS 3.2/4.3 and Android 2.3
            ( t.version('Opera Mini')>=5.0 && t.version('Opera Mini')<=6.5 &&
                (t.version('Android')>=2.3 || t.is('iOS')) ) ||

            // Nokia Symbian^3 - Tested on Nokia N8 (Symbian^3), C7 (Symbian^3), also works on N97 (Symbian^1)
            t.match('NokiaN8|NokiaC7|N97.*Series60|Symbian/3') ||

            // @todo: report this (tested on Nokia N71)
            t.version('Opera Mobi')>=11 && t.is('SymbianOS')
            ){
            return 'B';
        }

        if (
        // Blackberry 4.x - Tested on the Curve 8330
            t.version('BlackBerry')<5.0 ||
            // Windows Mobile - Tested on the HTC Leo (WinMo 5.2)
            t.match('MSIEMobile|Windows CE.*Mobile') || t.version('Windows Mobile')<=5.2

            ){
            return 'C';
        }

        //All older smartphone platforms and featurephones - Any device that doesn't support media queries
        //will receive the basic, C grade experience.
        return 'C';
    };

    impl.detectOS = function (ua) {
        return impl.findMatch(impl.mobileDetectRules.oss0, ua) ||
            impl.findMatch(impl.mobileDetectRules.oss, ua);
    };

    impl.getDeviceSmallerSide = function () {
        return window.screen.width < window.screen.height ?
            window.screen.width :
            window.screen.height;
    };

    /**
     * Constructor for MobileDetect object.
     * <br>
     * Such an object will keep a reference to the given user-agent string and cache most of the detect queries.<br>
     * <div style="background-color: #d9edf7; border: 1px solid #bce8f1; color: #3a87ad; padding: 14px; border-radius: 2px; margin-top: 20px">
     *     <strong>Find information how to download and install:</strong>
     *     <a href="https://github.com/hgoebl/mobile-detect.js/">github.com/hgoebl/mobile-detect.js/</a>
     * </div>
     *
     * @example <pre>
     *     var md = new MobileDetect(window.navigator.userAgent);
     *     if (md.mobile()) {
     *         location.href = (md.mobileGrade() === 'A') ? '/mobile/' : '/lynx/';
     *     }
     * </pre>
     *
     * @param {string} userAgent typically taken from window.navigator.userAgent or http_header['User-Agent']
     * @param {number} [maxPhoneWidth=600] <strong>only for browsers</strong> specify a value for the maximum
     *        width of smallest device side (in logical "CSS" pixels) until a device detected as mobile will be handled
     *        as phone.
     *        This is only used in cases where the device cannot be classified as phone or tablet.<br>
     *        See <a href="http://developer.android.com/guide/practices/screens_support.html">Declaring Tablet Layouts
     *        for Android</a>.<br>
     *        If you provide a value < 0, then this "fuzzy" check is disabled.
     * @constructor
     * @global
     */
    function MobileDetect(userAgent, maxPhoneWidth) {
        this.ua = prepareUserAgent(userAgent);
        this._cache = {};
        //600dp is typical 7" tablet minimum width
        this.maxPhoneWidth = maxPhoneWidth || 600;
    }

    MobileDetect.prototype = {
        constructor: MobileDetect,

        /**
         * Returns the detected phone or tablet type or <tt>null</tt> if it is not a mobile device.
         * <br>
         * For a list of possible return values see {@link MobileDetect#phone} and {@link MobileDetect#tablet}.<br>
         * <br>
         * If the device is not detected by the regular expressions from Mobile-Detect, a test is made against
         * the patterns of <a href="http://detectmobilebrowsers.com/">detectmobilebrowsers.com</a>. If this test
         * is positive, a value of <code>UnknownPhone</code>, <code>UnknownTablet</code> or
         * <code>UnknownMobile</code> is returned.<br>
         * When used in browser, the decision whether phone or tablet is made based on <code>screen.width/height</code>.<br>
         * <br>
         * When used server-side (node.js), there is no way to tell the difference between <code>UnknownTablet</code>
         * and <code>UnknownMobile</code>, so you will get <code>UnknownMobile</code> here.<br>
         * Be aware that since v1.0.0 in this special case you will get <code>UnknownMobile</code> only for:
         * {@link MobileDetect#mobile}, not for {@link MobileDetect#phone} and {@link MobileDetect#tablet}.
         * In versions before v1.0.0 all 3 methods returned <code>UnknownMobile</code> which was tedious to use.
         * <br>
         * In most cases you will use the return value just as a boolean.
         *
         * @returns {String} the key for the phone family or tablet family, e.g. "Nexus".
         * @function MobileDetect#mobile
         */
        mobile: function () {
            impl.prepareDetectionCache(this._cache, this.ua, this.maxPhoneWidth);
            return this._cache.mobile;
        },

        /**
         * Returns the detected phone type/family string or <tt>null</tt>.
         * <br>
         * The returned tablet (family or producer) is one of following keys:<br>
         * <br><tt>iPhone, BlackBerry, Pixel, HTC, Nexus, Dell, Motorola, Samsung, LG, Sony, Asus,
         * Xiaomi, NokiaLumia, Micromax, Palm, Vertu, Pantech, Fly, Wiko, iMobile,
         * SimValley, Wolfgang, Alcatel, Nintendo, Amoi, INQ, OnePlus, GenericPhone</tt><br>
         * <br>
         * If the device is not detected by the regular expressions from Mobile-Detect, a test is made against
         * the patterns of <a href="http://detectmobilebrowsers.com/">detectmobilebrowsers.com</a>. If this test
         * is positive, a value of <code>UnknownPhone</code> or <code>UnknownMobile</code> is returned.<br>
         * When used in browser, the decision whether phone or tablet is made based on <code>screen.width/height</code>.<br>
         * <br>
         * When used server-side (node.js), there is no way to tell the difference between <code>UnknownTablet</code>
         * and <code>UnknownMobile</code>, so you will get <code>null</code> here, while {@link MobileDetect#mobile}
         * will return <code>UnknownMobile</code>.<br>
         * Be aware that since v1.0.0 in this special case you will get <code>UnknownMobile</code> only for:
         * {@link MobileDetect#mobile}, not for {@link MobileDetect#phone} and {@link MobileDetect#tablet}.
         * In versions before v1.0.0 all 3 methods returned <code>UnknownMobile</code> which was tedious to use.
         * <br>
         * In most cases you will use the return value just as a boolean.
         *
         * @returns {String} the key of the phone family or producer, e.g. "iPhone"
         * @function MobileDetect#phone
         */
        phone: function () {
            impl.prepareDetectionCache(this._cache, this.ua, this.maxPhoneWidth);
            return this._cache.phone;
        },

        /**
         * Returns the detected tablet type/family string or <tt>null</tt>.
         * <br>
         * The returned tablet (family or producer) is one of following keys:<br>
         * <br><tt>iPad, NexusTablet, GoogleTablet, SamsungTablet, Kindle, SurfaceTablet,
         * HPTablet, AsusTablet, BlackBerryTablet, HTCtablet, MotorolaTablet, NookTablet,
         * AcerTablet, ToshibaTablet, LGTablet, FujitsuTablet, PrestigioTablet,
         * LenovoTablet, DellTablet, YarvikTablet, MedionTablet, ArnovaTablet,
         * IntensoTablet, IRUTablet, MegafonTablet, EbodaTablet, AllViewTablet,
         * ArchosTablet, AinolTablet, NokiaLumiaTablet, SonyTablet, PhilipsTablet,
         * CubeTablet, CobyTablet, MIDTablet, MSITablet, SMiTTablet, RockChipTablet,
         * FlyTablet, bqTablet, HuaweiTablet, NecTablet, PantechTablet, BronchoTablet,
         * VersusTablet, ZyncTablet, PositivoTablet, NabiTablet, KoboTablet, DanewTablet,
         * TexetTablet, PlaystationTablet, TrekstorTablet, PyleAudioTablet, AdvanTablet,
         * DanyTechTablet, GalapadTablet, MicromaxTablet, KarbonnTablet, AllFineTablet,
         * PROSCANTablet, YONESTablet, ChangJiaTablet, GUTablet, PointOfViewTablet,
         * OvermaxTablet, HCLTablet, DPSTablet, VistureTablet, CrestaTablet,
         * MediatekTablet, ConcordeTablet, GoCleverTablet, ModecomTablet, VoninoTablet,
         * ECSTablet, StorexTablet, VodafoneTablet, EssentielBTablet, RossMoorTablet,
         * iMobileTablet, TolinoTablet, AudioSonicTablet, AMPETablet, SkkTablet,
         * TecnoTablet, JXDTablet, iJoyTablet, FX2Tablet, XoroTablet, ViewsonicTablet,
         * VerizonTablet, OdysTablet, CaptivaTablet, IconbitTablet, TeclastTablet,
         * OndaTablet, JaytechTablet, BlaupunktTablet, DigmaTablet, EvolioTablet,
         * LavaTablet, AocTablet, MpmanTablet, CelkonTablet, WolderTablet, MediacomTablet,
         * MiTablet, NibiruTablet, NexoTablet, LeaderTablet, UbislateTablet,
         * PocketBookTablet, KocasoTablet, HisenseTablet, Hudl, TelstraTablet,
         * GenericTablet</tt><br>
         * <br>
         * If the device is not detected by the regular expressions from Mobile-Detect, a test is made against
         * the patterns of <a href="http://detectmobilebrowsers.com/">detectmobilebrowsers.com</a>. If this test
         * is positive, a value of <code>UnknownTablet</code> or <code>UnknownMobile</code> is returned.<br>
         * When used in browser, the decision whether phone or tablet is made based on <code>screen.width/height</code>.<br>
         * <br>
         * When used server-side (node.js), there is no way to tell the difference between <code>UnknownTablet</code>
         * and <code>UnknownMobile</code>, so you will get <code>null</code> here, while {@link MobileDetect#mobile}
         * will return <code>UnknownMobile</code>.<br>
         * Be aware that since v1.0.0 in this special case you will get <code>UnknownMobile</code> only for:
         * {@link MobileDetect#mobile}, not for {@link MobileDetect#phone} and {@link MobileDetect#tablet}.
         * In versions before v1.0.0 all 3 methods returned <code>UnknownMobile</code> which was tedious to use.
         * <br>
         * In most cases you will use the return value just as a boolean.
         *
         * @returns {String} the key of the tablet family or producer, e.g. "SamsungTablet"
         * @function MobileDetect#tablet
         */
        tablet: function () {
            impl.prepareDetectionCache(this._cache, this.ua, this.maxPhoneWidth);
            return this._cache.tablet;
        },

        /**
         * Returns the (first) detected user-agent string or <tt>null</tt>.
         * <br>
         * The returned user-agent is one of following keys:<br>
         * <br><tt>Chrome, Dolfin, Opera, Skyfire, Edge, IE, Firefox, Bolt, TeaShark, Blazer,
         * Safari, WeChat, UCBrowser, baiduboxapp, baidubrowser, DiigoBrowser, Mercury,
         * ObigoBrowser, NetFront, GenericBrowser, PaleMoon</tt><br>
         * <br>
         * In most cases calling {@link MobileDetect#userAgent} will be sufficient. But there are rare
         * cases where a mobile device pretends to be more than one particular browser. You can get the
         * list of all matches with {@link MobileDetect#userAgents} or check for a particular value by
         * providing one of the defined keys as first argument to {@link MobileDetect#is}.
         *
         * @returns {String} the key for the detected user-agent or <tt>null</tt>
         * @function MobileDetect#userAgent
         */
        userAgent: function () {
            if (this._cache.userAgent === undefined) {
                this._cache.userAgent = impl.findMatch(impl.mobileDetectRules.uas, this.ua);
            }
            return this._cache.userAgent;
        },

        /**
         * Returns all detected user-agent strings.
         * <br>
         * The array is empty or contains one or more of following keys:<br>
         * <br><tt>Chrome, Dolfin, Opera, Skyfire, Edge, IE, Firefox, Bolt, TeaShark, Blazer,
         * Safari, WeChat, UCBrowser, baiduboxapp, baidubrowser, DiigoBrowser, Mercury,
         * ObigoBrowser, NetFront, GenericBrowser, PaleMoon</tt><br>
         * <br>
         * In most cases calling {@link MobileDetect#userAgent} will be sufficient. But there are rare
         * cases where a mobile device pretends to be more than one particular browser. You can get the
         * list of all matches with {@link MobileDetect#userAgents} or check for a particular value by
         * providing one of the defined keys as first argument to {@link MobileDetect#is}.
         *
         * @returns {Array} the array of detected user-agent keys or <tt>[]</tt>
         * @function MobileDetect#userAgents
         */
        userAgents: function () {
            if (this._cache.userAgents === undefined) {
                this._cache.userAgents = impl.findMatches(impl.mobileDetectRules.uas, this.ua);
            }
            return this._cache.userAgents;
        },

        /**
         * Returns the detected operating system string or <tt>null</tt>.
         * <br>
         * The operating system is one of following keys:<br>
         * <br><tt>AndroidOS, BlackBerryOS, PalmOS, SymbianOS, WindowsMobileOS, WindowsPhoneOS,
         * iOS, iPadOS, SailfishOS, MeeGoOS, MaemoOS, JavaOS, webOS, badaOS, BREWOS</tt><br>
         *
         * @returns {String} the key for the detected operating system.
         * @function MobileDetect#os
         */
        os: function () {
            if (this._cache.os === undefined) {
                this._cache.os = impl.detectOS(this.ua);
            }
            return this._cache.os;
        },

        /**
         * Get the version (as Number) of the given property in the User-Agent.
         * <br>
         * Will return a float number. (eg. 2_0 will return 2.0, 4.3.1 will return 4.31)
         *
         * @param {String} key a key defining a thing which has a version.<br>
         *        You can use one of following keys:<br>
         * <br><tt>Mobile, Build, Version, VendorID, iPad, iPhone, iPod, Kindle, Chrome, Coast,
         * Dolfin, Firefox, Fennec, Edge, IE, NetFront, NokiaBrowser, Opera, Opera Mini,
         * Opera Mobi, UCBrowser, MQQBrowser, MicroMessenger, baiduboxapp, baidubrowser,
         * SamsungBrowser, Iron, Safari, Skyfire, Tizen, Webkit, PaleMoon,
         * SailfishBrowser, Gecko, Trident, Presto, Goanna, iOS, Android, Sailfish,
         * BlackBerry, BREW, Java, Windows Phone OS, Windows Phone, Windows CE, Windows
         * NT, Symbian, webOS</tt><br>
         *
         * @returns {Number} the version as float or <tt>NaN</tt> if User-Agent doesn't contain this version.
         *          Be careful when comparing this value with '==' operator!
         * @function MobileDetect#version
         */
        version: function (key) {
            return impl.getVersion(key, this.ua);
        },

        /**
         * Get the version (as String) of the given property in the User-Agent.
         * <br>
         *
         * @param {String} key a key defining a thing which has a version.<br>
         *        You can use one of following keys:<br>
         * <br><tt>Mobile, Build, Version, VendorID, iPad, iPhone, iPod, Kindle, Chrome, Coast,
         * Dolfin, Firefox, Fennec, Edge, IE, NetFront, NokiaBrowser, Opera, Opera Mini,
         * Opera Mobi, UCBrowser, MQQBrowser, MicroMessenger, baiduboxapp, baidubrowser,
         * SamsungBrowser, Iron, Safari, Skyfire, Tizen, Webkit, PaleMoon,
         * SailfishBrowser, Gecko, Trident, Presto, Goanna, iOS, Android, Sailfish,
         * BlackBerry, BREW, Java, Windows Phone OS, Windows Phone, Windows CE, Windows
         * NT, Symbian, webOS</tt><br>
         *
         * @returns {String} the "raw" version as String or <tt>null</tt> if User-Agent doesn't contain this version.
         *
         * @function MobileDetect#versionStr
         */
        versionStr: function (key) {
            return impl.getVersionStr(key, this.ua);
        },

        /**
         * Global test key against userAgent, os, phone, tablet and some other properties of userAgent string.
         *
         * @param {String} key the key (case-insensitive) of a userAgent, an operating system, phone or
         *        tablet family.<br>
         *        For a complete list of possible values, see {@link MobileDetect#userAgent},
         *        {@link MobileDetect#os}, {@link MobileDetect#phone}, {@link MobileDetect#tablet}.<br>
         *        Additionally you have following keys:<br>
         * <br><tt>Bot, MobileBot, DesktopMode, TV, WebKit, Console, Watch</tt><br>
         *
         * @returns {boolean} <tt>true</tt> when the given key is one of the defined keys of userAgent, os, phone,
         *                    tablet or one of the listed additional keys, otherwise <tt>false</tt>
         * @function MobileDetect#is
         */
        is: function (key) {
            return containsIC(this.userAgents(), key) ||
                   equalIC(key, this.os()) ||
                   equalIC(key, this.phone()) ||
                   equalIC(key, this.tablet()) ||
                   containsIC(impl.findMatches(impl.mobileDetectRules.utils, this.ua), key);
        },

        /**
         * Do a quick test against navigator::userAgent.
         *
         * @param {String|RegExp} pattern the pattern, either as String or RegExp
         *                        (a string will be converted to a case-insensitive RegExp).
         * @returns {boolean} <tt>true</tt> when the pattern matches, otherwise <tt>false</tt>
         * @function MobileDetect#match
         */
        match: function (pattern) {
            if (!(pattern instanceof RegExp)) {
                pattern = new RegExp(pattern, 'i');
            }
            return pattern.test(this.ua);
        },

        /**
         * Checks whether the mobile device can be considered as phone regarding <code>screen.width</code>.
         * <br>
         * Obviously this method makes sense in browser environments only (not for Node.js)!
         * @param {number} [maxPhoneWidth] the maximum logical pixels (aka. CSS-pixels) to be considered as phone.<br>
         *        The argument is optional and if not present or falsy, the value of the constructor is taken.
         * @returns {boolean|undefined} <code>undefined</code> if screen size wasn't detectable, else <code>true</code>
         *          when screen.width is less or equal to maxPhoneWidth, otherwise <code>false</code>.<br>
         *          Will always return <code>undefined</code> server-side.
         */
        isPhoneSized: function (maxPhoneWidth) {
            return MobileDetect.isPhoneSized(maxPhoneWidth || this.maxPhoneWidth);
        },

        /**
         * Returns the mobile grade ('A', 'B', 'C').
         *
         * @returns {String} one of the mobile grades ('A', 'B', 'C').
         * @function MobileDetect#mobileGrade
         */
        mobileGrade: function () {
            if (this._cache.grade === undefined) {
                this._cache.grade = impl.mobileGrade(this);
            }
            return this._cache.grade;
        }
    };

    // environment-dependent
    if (typeof window !== 'undefined' && window.screen) {
        MobileDetect.isPhoneSized = function (maxPhoneWidth) {
            return maxPhoneWidth < 0 ? undefined : impl.getDeviceSmallerSide() <= maxPhoneWidth;
        };
    } else {
        MobileDetect.isPhoneSized = function () {};
    }

    // should not be replaced by a completely new object - just overwrite existing methods
    MobileDetect._impl = impl;

    MobileDetect.version = '1.4.5 2021-03-13';

    return MobileDetect;
}); // end of call of define()
})((function (undefined) {
    if (typeof module !== 'undefined' && module.exports) {
        return function (factory) { module.exports = factory(); };
    } else if (typeof define === 'function' && define.amd) {
        return define;
    } else if (typeof window !== 'undefined') {
        return function (factory) { window.MobileDetect = factory(); };
    } else {
        // please file a bug if you get this error!
        throw new Error('unknown environment');
    }
})());
if (typeof ai_block_class_def != 'undefined') {

ai_insert = function (insertion, selector, insertion_code) {
    var ai_debug = typeof ai_debugging !== 'undefined'; // 1
//    var ai_debug = false;

//  if (selector.indexOf (':eq') != - 1) {
  // ***
  if (selector.indexOf (':eq(') != - 1) {
    var jq = window.jQuery && window.jQuery.fn;

    if (ai_debug) console.log ('AI INSERT USING jQuery QUERIES:', selector);

    if (!jq) {
      console.error ('AI INSERT USING jQuery QUERIES:', selector, '- jQuery not found');
      return;
    } else var elements = jQuery (selector);
  } else var elements = document.querySelectorAll (selector);

//  Array.prototype.forEach.call (elements, function (element, index) {
  for (var index = 0, len = elements.length; index < len; index++) {
    var element = elements [index];

    if (element.hasAttribute ('id')) {
      selector_string = '#' + element.getAttribute ('id');
    } else
    if (element.hasAttribute ('class')) {
      selector_string = '.' + element.getAttribute ('class').replace (new RegExp (' ', 'g'), '.');
    } else
    selector_string = '';

    if (ai_debug) console.log ('');
    if (ai_debug) console.log ('AI INSERT', insertion, selector, '(' + element.tagName.toLowerCase() + selector_string + ')');

    var template = document.createElement ('div');
    template.innerHTML = insertion_code;

    var ai_selector_counter = template.getElementsByClassName ("ai-selector-counter")[0];
    if (ai_selector_counter != null) {
      ai_selector_counter.innerText = index + 1;
    }

    var ai_debug_name_ai_main = template.getElementsByClassName ("ai-debug-name ai-main")[0];
    if (ai_debug_name_ai_main != null) {
      var insertion_name = insertion.toUpperCase ();

      if (typeof ai_front != 'undefined') {
        if (insertion == 'before') {
          insertion_name = ai_front.insertion_before;
        } else
        if (insertion == 'after') {
          insertion_name = ai_front.insertion_after;
        } else
        if (insertion == 'prepend') {
          insertion_name = ai_front.insertion_prepend;
        } else
        if (insertion == 'append') {
          insertion_name = ai_front.insertion_append;
        } else
        if (insertion == 'replace-content') {
          insertion_name = ai_front.insertion_replace_content;
        } else
        if (insertion == 'replace-element') {
          insertion_name = ai_front.insertion_replace_element;
        }
      }

      if (selector_string.indexOf ('.ai-viewports') == - 1) {
        ai_debug_name_ai_main.innerText = insertion_name + ' ' + selector + ' (' + element.tagName.toLowerCase() + selector_string + ')';
      }
    }

    var range = document.createRange ();

    var fragment_ok = true;
    try {
      var fragment = range.createContextualFragment (template.innerHTML);
    }
    catch (err) {
      var fragment_ok = false;
      if (ai_debug) console.log ('AI INSERT', 'range.createContextualFragment ERROR:', err.message);
    }

    if (insertion == 'before') {
      // ***
//      if (!fragment_ok) {
//        jQuery (template.innerHTML).insertBefore (jQuery (element));
//      } else

      element.parentNode.insertBefore (fragment, element);
    } else
    if (insertion == 'after') {
      // ***
//      if (!fragment_ok) {
//        jQuery (template.innerHTML).insertBefore (jQuery (element.nextSibling));
//      } else

      element.parentNode.insertBefore (fragment, element.nextSibling);
    } else
    if (insertion == 'prepend') {
      // ***
//      if (!fragment_ok) {
//        jQuery (template.innerHTML).insertBefore (jQuery (element.firstChild));
//      } else

      element.insertBefore (fragment, element.firstChild);
    } else
    if (insertion == 'append') {
      // ***
//      if (!fragment_ok) {
//        jQuery (template.innerHTML).appendTo (jQuery (element));
//      } else

      element.insertBefore (fragment, null);
    } else
    if (insertion == 'replace-content') {
      element.innerHTML = '';

      // ***
//      if (!fragment_ok) {
//        jQuery (template.innerHTML).appendTo (jQuery (element));
//      } else

      element.insertBefore (fragment, null);
    } else
    if (insertion == 'replace-element') {
      // ***
//      if (!fragment_ok) {
//        jQuery (template.innerHTML).insertBefore (jQuery (element));
//      } else

      element.parentNode.insertBefore (fragment, element);

      element.parentNode.removeChild (element);
    }
//  });
  };
}

ai_insert_code = function (element) {

  function hasClass (element, cls) {
    if (element == null) return false;

    if (element.classList) return element.classList.contains (cls); else
      return (' ' + element.className + ' ').indexOf (' ' + cls + ' ') > - 1;
  }

  function addClass (element, cls) {
    if (element == null) return;

    if (element.classList) element.classList.add (cls); else
      element.className += ' ' + cls;
  }

  function removeClass (element, cls) {
    if (element == null) return;

    if (element.classList) element.classList.remove (cls); else
      element.className = element.className.replace (new RegExp ('(^|\\b)' + cls.split (' ').join ('|') + '(\\b|$)', 'gi'), ' ');
  }

  if (typeof element == 'undefined') return;

  var insertion = false;

  var ai_debug = typeof ai_debugging !== 'undefined'; // 2
//  var ai_debug = false;

  if (ai_debug) console.log ('AI INSERT ELEMENT class:', element.getAttribute ('class'));

  if (hasClass (element, 'no-visibility-check')) {
    var visible = true;
  } else var visible = !!(element.offsetWidth || element.offsetHeight || element.getClientRects().length);

  if (ai_debug) {
    var block = element.getAttribute ('data-block');
  }

  if (visible) {
    if (ai_debug) console.log ('AI ELEMENT VISIBLE: block', block, 'offsetWidth:', element.offsetWidth, 'offsetHeight:', element.offsetHeight, 'getClientRects().length:', element.getClientRects().length);

    var insertion_code = element.getAttribute ('data-code');
    var insertion_type = element.getAttribute ('data-insertion-position');
    var selector       = element.getAttribute ('data-selector');

    if (insertion_code != null) {
      if (insertion_type != null && selector != null) {
        // ***
        if (selector.indexOf (':eq(') != - 1) {
          var jq = window.jQuery && window.jQuery.fn;
          if (jq) {
            var selector_exists = jQuery (selector).length;
          } else var selector_exists = false;
        } else var selector_exists = document.querySelectorAll (selector).length;

        if (ai_debug) console.log ('AI ELEMENT VISIBLE: block', block, insertion_type, selector, selector_exists ? '' : 'NOT FOUND');

        if (selector_exists) {
          ai_insert (insertion_type, selector, b64d (insertion_code));
          removeClass (element, 'ai-viewports');
        }
      } else {
          if (ai_debug) console.log ('AI ELEMENT VISIBLE: block', block);

          var range = document.createRange ();

          var fragment_ok = true;
          try {
            var fragment = range.createContextualFragment (b64d (insertion_code));
          }
          catch (err) {
            var fragment_ok = false;
            if (ai_debug) console.log ('AI INSERT NEXT', 'range.createContextualFragment ERROR:', err.message);
          }

          // ***
//          if (!fragment_ok) {
//            jQuery (b64d (insertion_code)).insertBefore (jQuery (element.nextSibling));
//          } else

          element.parentNode.insertBefore (fragment, element.nextSibling);

          removeClass (element, 'ai-viewports');
        }
    }

    insertion = true;

    // Should not be removed here as it is needed for tracking - removed there
//    var ai_check_block_data = element.getElementsByClassName ('ai-check-block');
//    if (typeof ai_check_block_data [0] != 'undefined') {
//      // Remove span
//      ai_check_block_data [0].parentNode.removeChild (ai_check_block_data [0]);
//    }
  } else {
      if (ai_debug) console.log ('AI ELEMENT NOT VISIBLE: block', block, 'offsetWidth:', element.offsetWidth, 'offsetHeight:', element.offsetHeight, 'getClientRects().length:', element.getClientRects().length);

      var debug_bar = element.previousElementSibling;

      if (hasClass (debug_bar, 'ai-debug-bar') && hasClass (debug_bar, 'ai-debug-script')) {
        removeClass (debug_bar, 'ai-debug-script');
        addClass (debug_bar, 'ai-debug-viewport-invisible');
      }

      removeClass (element, 'ai-viewports');
    }
  return insertion;
}

ai_insert_list_code = function (id) {
  var ai_block_div = document.getElementsByClassName (id) [0];

  if (typeof ai_block_div != 'undefined') {
    var inserted = ai_insert_code (ai_block_div);
    var wrapping_div = ai_block_div.closest ('div.' + ai_block_class_def);
    if (wrapping_div) {
      if (!inserted) {
        wrapping_div.removeAttribute ('data-ai');
      }

      var debug_block = wrapping_div.querySelectorAll ('.ai-debug-block');
      if (wrapping_div && debug_block.length) {
        wrapping_div.classList.remove ('ai-list-block');
        wrapping_div.classList.remove ('ai-list-block-ip');
        wrapping_div.classList.remove ('ai-list-block-filter');
        wrapping_div.style.visibility = '';
        if (wrapping_div.classList.contains ('ai-remove-position')) {
          wrapping_div.style.position = '';
        }
      }
    }

    ai_block_div.classList.remove (id);

    if (inserted) ai_process_elements ();
  }
}

ai_insert_viewport_code = function (id) {
  var ai_block_div = document.getElementsByClassName (id) [0];

  if (typeof ai_block_div != 'undefined') {
    var inserted = ai_insert_code (ai_block_div);

    ai_block_div.classList.remove (id);

    if (inserted) {
      var wrapping_div = ai_block_div.closest ('div.' + ai_block_class_def);

      if (wrapping_div != null) {
        var viewport_style = ai_block_div.getAttribute ('style');

        if (viewport_style != null) {
          wrapping_div.setAttribute ('style', wrapping_div.getAttribute ('style') + ' ' + viewport_style);
        }
      }
    }

    setTimeout (function () {
      ai_block_div.removeAttribute ('style');
    }, 2);

    ai_process_elements ();
  }
}

ai_insert_adsense_fallback_codes = function (adsense_unfilled_ins) {
  var ai_debug = typeof ai_debugging !== 'undefined'; // 3
//  var ai_debug = false;

  adsense_unfilled_ins.style.display = "none";

  var fallback_wrapper = adsense_unfilled_ins.closest ('.ai-fallback-adsense');
  var fallback_div = fallback_wrapper.nextElementSibling;

  if (!!fallback_div.getAttribute ('data-code')) {
    var inserted = ai_insert_code (fallback_div);

    if (inserted) {
      ai_process_elements ();
    }
  } else {
      fallback_div.style.display = "block";
    }

  if (fallback_wrapper.classList.contains ('ai-empty-code') && adsense_unfilled_ins.closest ('.' + ai_block_class_def) != null) {
    var label_div = adsense_unfilled_ins.closest ('.' + ai_block_class_def).getElementsByClassName ('code-block-label');
    if (label_div.length != 0) {
      label_div [0].style.display = "none";
    }
  }

  if (ai_debug) {
    console.log ('AI FALLBACK ADSENSE UNFILLED:', adsense_unfilled_ins.closest ('.' + ai_block_class_def) != null ? adsense_unfilled_ins.closest ('.' + ai_block_class_def).classList.value : '', !!fallback_div.getAttribute ('data-code') ? 'INSERT' : 'SHOW');
  }
}

//ai_insert_fallback_codes = function () {
//  var ai_debug = typeof ai_debugging !== 'undefined'; // 3
////  var ai_debug = false;

//  var ai_fallback_divs = document.getElementsByClassName ('ai-fallback-adsense');

//  var fallback_check = ai_fallback_divs.length;
//  if (ai_debug && fallback_check) {
//    console.log ('');
//    console.log ('AI FALLBACK CHECK ADSENSE:', ai_fallback_divs.length, 'block' + (ai_fallback_divs.length == 1 ? '' : 's')) ;
//  }

//  for (var adsense = 0; adsense < ai_fallback_divs.length; adsense ++) {
//    var adsense_div = ai_fallback_divs [adsense];
//    var adsense_unfilled_ins = adsense_div.querySelector ('ins.adsbygoogle[data-ad-status="filled"]');

//    if (!!adsense_unfilled_ins) {
//      adsense_unfilled_ins.style.display = "none";

//      var fallback_div = adsense_div.nextElementSibling;
//      var insert = !!fallback_div.getAttribute ('data-code');

//      if (insert) {
//        if (ai_debug) {
//          var block = fallback_div.getAttribute ('data-block');
//          console.log ('AI INSERT FALLBACK CODE FOR BLOCK', block);
//        }

//        var inserted = ai_insert_code (fallback_div);

//        if (inserted) {
//          ai_process_elements ();
//        }
//      } else {
//          if (ai_debug) {
//            var block = fallback_div.getAttribute ('data-block');
//            console.log ('AI SHOW FALLBACK CODE FOR BLOCK', block);
//          }

//          fallback_div.style.display = "block";
//        }
//    }
//  }

//  if (ai_debug && fallback_check) {
//    console.log ('');
//  }
//}

ai_insert_code_by_class = function (id) {
  var ai_block_div = document.getElementsByClassName (id) [0];

  if (typeof ai_block_div != 'undefined') {
    ai_insert_code (ai_block_div);

    ai_block_div.classList.remove (id);
  }
}

ai_insert_client_code = function (id, len) {
  var ai_debug = typeof ai_debugging !== 'undefined'; // 4
//  var ai_debug = false;

  var ai_block_div = document.getElementsByClassName (id) [0];

  if (ai_debug) {
    var block   = ai_block_div.getAttribute ('data-block');
    console.log ('AI INSERT PROTECTED BLOCK', block, '.' + id);
  }

  if (typeof ai_block_div != 'undefined') {
    var insertion_code = ai_block_div.getAttribute ('data-code');

//    if (insertion_code != null && ai_check_block () && ai_check_and_insert_block ()) {
    if (insertion_code != null && ai_check_block () /*&& ai_check_and_insert_block ()*/) {
      ai_block_div.setAttribute ('data-code', insertion_code.substring (Math.floor (len / 19)));
      ai_insert_code_by_class (id);
      ai_block_div.remove();
    }
  }
}

ai_process_elements_active = false;

function ai_process_elements () {
  if (!ai_process_elements_active)
    setTimeout (function() {
      ai_process_elements_active = false;

      if (typeof ai_process_rotations == 'function') {
        ai_process_rotations ();
      }

      if (typeof ai_process_lists == 'function') {
        // ***
        ai_process_lists (jQuery (".ai-list-data"));
      }

      if (typeof ai_process_ip_addresses == 'function') {
        // ***
        ai_process_ip_addresses (jQuery (".ai-ip-data"));
      }

      if (typeof ai_process_filter_hooks == 'function') {
        // ***
//        ai_process_filter_hooks (jQuery (".ai-filter-check"));
        ai_process_filter_hooks ();
      }

      if (typeof ai_adb_process_blocks == 'function') {
        ai_adb_process_blocks ();
      }

      if (typeof ai_process_impressions == 'function' && ai_tracking_finished == true) {
        ai_process_impressions ();
      }
      if (typeof ai_install_click_trackers == 'function' && ai_tracking_finished == true) {
        ai_install_click_trackers ();
      }

      if (typeof ai_install_close_buttons == 'function') {
        ai_install_close_buttons (document);
      }
    }, 5);
  ai_process_elements_active = true;
}

const targetNode = document.querySelector ('body');
const config = {attributes: true, childList: false, subtree: true};
const ai_adsense_callback = function (mutationsList, observer) {
  // Use traditional 'for loops' for IE 11
  for (const mutation of mutationsList) {
    if (mutation.type === 'attributes' && mutation.attributeName == 'data-ad-status' && mutation.target.dataset.adStatus == 'unfilled' && !!mutation.target.closest ('.ai-fallback-adsense')) {
      ai_insert_adsense_fallback_codes (mutation.target);
    }
  }
};

const observer = new MutationObserver (ai_adsense_callback);
observer.observe (targetNode, config);

// Later, we can stop observing
//observer.disconnect();



/*globals jQuery,Window,HTMLElement,HTMLDocument,HTMLCollection,NodeList,MutationObserver */
/*exported Arrive*/
/*jshint latedef:false */

/*
 * arrive.js
 * v2.4.1
 * https://github.com/uzairfarooq/arrive
 * MIT licensed
 *
 * Copyright (c) 2014-2017 Uzair Farooq
 */
var Arrive = (function(window, $, undefined) {

  "use strict";

  if(!window.MutationObserver || typeof HTMLElement === 'undefined'){
    return; //for unsupported browsers
  }

  var arriveUniqueId = 0;

  var utils = (function() {
    var matches = HTMLElement.prototype.matches || HTMLElement.prototype.webkitMatchesSelector || HTMLElement.prototype.mozMatchesSelector
                  || HTMLElement.prototype.msMatchesSelector;

    return {
      matchesSelector: function(elem, selector) {
        return elem instanceof HTMLElement && matches.call(elem, selector);
      },
      // to enable function overloading - By John Resig (MIT Licensed)
      addMethod: function (object, name, fn) {
        var old = object[ name ];
        object[ name ] = function(){
          if ( fn.length == arguments.length ) {
            return fn.apply( this, arguments );
          }
          else if ( typeof old == 'function' ) {
            return old.apply( this, arguments );
          }
        };
      },
      callCallbacks: function(callbacksToBeCalled, registrationData) {
        if (registrationData && registrationData.options.onceOnly && registrationData.firedElems.length == 1) {
          // as onlyOnce param is true, make sure we fire the event for only one item
          callbacksToBeCalled = [callbacksToBeCalled[0]];
        }

        for (var i = 0, cb; (cb = callbacksToBeCalled[i]); i++) {
          if (cb && cb.callback) {
            cb.callback.call(cb.elem, cb.elem);
          }
        }

        if (registrationData && registrationData.options.onceOnly && registrationData.firedElems.length == 1) {
          // unbind event after first callback as onceOnly is true.
          registrationData.me.unbindEventWithSelectorAndCallback.call(
            registrationData.target, registrationData.selector, registrationData.callback);
        }
      },
      // traverse through all descendants of a node to check if event should be fired for any descendant
      checkChildNodesRecursively: function(nodes, registrationData, matchFunc, callbacksToBeCalled) {
        // check each new node if it matches the selector
        for (var i=0, node; (node = nodes[i]); i++) {
          if (matchFunc(node, registrationData, callbacksToBeCalled)) {
            callbacksToBeCalled.push({ callback: registrationData.callback, elem: node });
          }

          if (node.childNodes.length > 0) {
            utils.checkChildNodesRecursively(node.childNodes, registrationData, matchFunc, callbacksToBeCalled);
          }
        }
      },
      mergeArrays: function(firstArr, secondArr){
        // Overwrites default options with user-defined options.
        var options = {},
            attrName;
        for (attrName in firstArr) {
          if (firstArr.hasOwnProperty(attrName)) {
            options[attrName] = firstArr[attrName];
          }
        }
        for (attrName in secondArr) {
          if (secondArr.hasOwnProperty(attrName)) {
            options[attrName] = secondArr[attrName];
          }
        }
        return options;
      },
      toElementsArray: function (elements) {
        // check if object is an array (or array like object)
        // Note: window object has .length property but it's not array of elements so don't consider it an array
        if (typeof elements !== "undefined" && (typeof elements.length !== "number" || elements === window)) {
          elements = [elements];
        }
        return elements;
      }
    };
  })();


  // Class to maintain state of all registered events of a single type
  var EventsBucket = (function() {
    var EventsBucket = function() {
      // holds all the events

      this._eventsBucket    = [];
      // function to be called while adding an event, the function should do the event initialization/registration
      this._beforeAdding    = null;
      // function to be called while removing an event, the function should do the event destruction
      this._beforeRemoving  = null;
    };

    EventsBucket.prototype.addEvent = function(target, selector, options, callback) {
      var newEvent = {
        target:             target,
        selector:           selector,
        options:            options,
        callback:           callback,
        firedElems:         []
      };

      if (this._beforeAdding) {
        this._beforeAdding(newEvent);
      }

      this._eventsBucket.push(newEvent);
      return newEvent;
    };

    EventsBucket.prototype.removeEvent = function(compareFunction) {
      for (var i=this._eventsBucket.length - 1, registeredEvent; (registeredEvent = this._eventsBucket[i]); i--) {
        if (compareFunction(registeredEvent)) {
          if (this._beforeRemoving) {
              this._beforeRemoving(registeredEvent);
          }

          // mark callback as null so that even if an event mutation was already triggered it does not call callback
          var removedEvents = this._eventsBucket.splice(i, 1);
          if (removedEvents && removedEvents.length) {
            removedEvents[0].callback = null;
          }
        }
      }
    };

    EventsBucket.prototype.beforeAdding = function(beforeAdding) {
      this._beforeAdding = beforeAdding;
    };

    EventsBucket.prototype.beforeRemoving = function(beforeRemoving) {
      this._beforeRemoving = beforeRemoving;
    };

    return EventsBucket;
  })();


  /**
   * @constructor
   * General class for binding/unbinding arrive and leave events
   */
  var MutationEvents = function(getObserverConfig, onMutation) {
    var eventsBucket    = new EventsBucket(),
        me              = this;

    var defaultOptions = {
      fireOnAttributesModification: false
    };

    // actual event registration before adding it to bucket
    eventsBucket.beforeAdding(function(registrationData) {
      var
        target    = registrationData.target,
        observer;

      // mutation observer does not work on window or document
      if (target === window.document || target === window) {
        target = document.getElementsByTagName("html")[0];
      }

      // Create an observer instance
      observer = new MutationObserver(function(e) {
        onMutation.call(this, e, registrationData);
      });

      var config = getObserverConfig(registrationData.options);

      observer.observe(target, config);

      registrationData.observer = observer;
      registrationData.me = me;
    });

    // cleanup/unregister before removing an event
    eventsBucket.beforeRemoving(function (eventData) {
      eventData.observer.disconnect();
    });

    this.bindEvent = function(selector, options, callback) {
      options = utils.mergeArrays(defaultOptions, options);

      var elements = utils.toElementsArray(this);

      for (var i = 0; i < elements.length; i++) {
        eventsBucket.addEvent(elements[i], selector, options, callback);
      }
    };

    this.unbindEvent = function() {
      var elements = utils.toElementsArray(this);
      eventsBucket.removeEvent(function(eventObj) {
        for (var i = 0; i < elements.length; i++) {
          if (this === undefined || eventObj.target === elements[i]) {
            return true;
          }
        }
        return false;
      });
    };

    this.unbindEventWithSelectorOrCallback = function(selector) {
      var elements = utils.toElementsArray(this),
          callback = selector,
          compareFunction;

      if (typeof selector === "function") {
        compareFunction = function(eventObj) {
          for (var i = 0; i < elements.length; i++) {
            if ((this === undefined || eventObj.target === elements[i]) && eventObj.callback === callback) {
              return true;
            }
          }
          return false;
        };
      }
      else {
        compareFunction = function(eventObj) {
          for (var i = 0; i < elements.length; i++) {
            if ((this === undefined || eventObj.target === elements[i]) && eventObj.selector === selector) {
              return true;
            }
          }
          return false;
        };
      }
      eventsBucket.removeEvent(compareFunction);
    };

    this.unbindEventWithSelectorAndCallback = function(selector, callback) {
      var elements = utils.toElementsArray(this);
      eventsBucket.removeEvent(function(eventObj) {
          for (var i = 0; i < elements.length; i++) {
            if ((this === undefined || eventObj.target === elements[i]) && eventObj.selector === selector && eventObj.callback === callback) {
              return true;
            }
          }
          return false;
      });
    };

    return this;
  };


  /**
   * @constructor
   * Processes 'arrive' events
   */
  var ArriveEvents = function() {
    // Default options for 'arrive' event
    var arriveDefaultOptions = {
      fireOnAttributesModification: false,
      onceOnly: false,
      existing: false
    };

    function getArriveObserverConfig(options) {
      var config = {
        attributes: false,
        childList: true,
        subtree: true
      };

      if (options.fireOnAttributesModification) {
        config.attributes = true;
      }

      return config;
    }

    function onArriveMutation(mutations, registrationData) {
      mutations.forEach(function( mutation ) {
        var newNodes    = mutation.addedNodes,
            targetNode = mutation.target,
            callbacksToBeCalled = [],
            node;

        // If new nodes are added
        if( newNodes !== null && newNodes.length > 0 ) {
          utils.checkChildNodesRecursively(newNodes, registrationData, nodeMatchFunc, callbacksToBeCalled);
        }
        else if (mutation.type === "attributes") {
          if (nodeMatchFunc(targetNode, registrationData, callbacksToBeCalled)) {
            callbacksToBeCalled.push({ callback: registrationData.callback, elem: targetNode });
          }
        }

        utils.callCallbacks(callbacksToBeCalled, registrationData);
      });
    }

    function nodeMatchFunc(node, registrationData, callbacksToBeCalled) {
      // check a single node to see if it matches the selector
      if (utils.matchesSelector(node, registrationData.selector)) {
        if(node._id === undefined) {
          node._id = arriveUniqueId++;
        }
        // make sure the arrive event is not already fired for the element
        if (registrationData.firedElems.indexOf(node._id) == -1) {
          registrationData.firedElems.push(node._id);

          return true;
        }
      }

      return false;
    }

    arriveEvents = new MutationEvents(getArriveObserverConfig, onArriveMutation);

    var mutationBindEvent = arriveEvents.bindEvent;

    // override bindEvent function
    arriveEvents.bindEvent = function(selector, options, callback) {

      if (typeof callback === "undefined") {
        callback = options;
        options = arriveDefaultOptions;
      } else {
        options = utils.mergeArrays(arriveDefaultOptions, options);
      }

      var elements = utils.toElementsArray(this);

      if (options.existing) {
        var existing = [];

        for (var i = 0; i < elements.length; i++) {
          var nodes = elements[i].querySelectorAll(selector);
          for (var j = 0; j < nodes.length; j++) {
            existing.push({ callback: callback, elem: nodes[j] });
          }
        }

        // no need to bind event if the callback has to be fired only once and we have already found the element
        if (options.onceOnly && existing.length) {
          return callback.call(existing[0].elem, existing[0].elem);
        }

        setTimeout(utils.callCallbacks, 1, existing);
      }

      mutationBindEvent.call(this, selector, options, callback);
    };

    return arriveEvents;
  };


  /**
   * @constructor
   * Processes 'leave' events
   */
  var LeaveEvents = function() {
    // Default options for 'leave' event
    var leaveDefaultOptions = {};

    function getLeaveObserverConfig() {
      var config = {
        childList: true,
        subtree: true
      };

      return config;
    }

    function onLeaveMutation(mutations, registrationData) {
      mutations.forEach(function( mutation ) {
        var removedNodes  = mutation.removedNodes,
            callbacksToBeCalled = [];

        if( removedNodes !== null && removedNodes.length > 0 ) {
          utils.checkChildNodesRecursively(removedNodes, registrationData, nodeMatchFunc, callbacksToBeCalled);
        }

        utils.callCallbacks(callbacksToBeCalled, registrationData);
      });
    }

    function nodeMatchFunc(node, registrationData) {
      return utils.matchesSelector(node, registrationData.selector);
    }

    leaveEvents = new MutationEvents(getLeaveObserverConfig, onLeaveMutation);

    var mutationBindEvent = leaveEvents.bindEvent;

    // override bindEvent function
    leaveEvents.bindEvent = function(selector, options, callback) {

      if (typeof callback === "undefined") {
        callback = options;
        options = leaveDefaultOptions;
      } else {
        options = utils.mergeArrays(leaveDefaultOptions, options);
      }

      mutationBindEvent.call(this, selector, options, callback);
    };

    return leaveEvents;
  };


  var arriveEvents = new ArriveEvents(),
      leaveEvents  = new LeaveEvents();

  function exposeUnbindApi(eventObj, exposeTo, funcName) {
    // expose unbind function with function overriding
    utils.addMethod(exposeTo, funcName, eventObj.unbindEvent);
    utils.addMethod(exposeTo, funcName, eventObj.unbindEventWithSelectorOrCallback);
    utils.addMethod(exposeTo, funcName, eventObj.unbindEventWithSelectorAndCallback);
  }

  /*** expose APIs ***/
  function exposeApi(exposeTo) {
    exposeTo.arrive = arriveEvents.bindEvent;
    exposeUnbindApi(arriveEvents, exposeTo, "unbindArrive");

    exposeTo.leave = leaveEvents.bindEvent;
    exposeUnbindApi(leaveEvents, exposeTo, "unbindLeave");
  }

  if ($) {
    exposeApi($.fn);
  }
  exposeApi(HTMLElement.prototype);
  exposeApi(NodeList.prototype);
  exposeApi(HTMLCollection.prototype);
  exposeApi(HTMLDocument.prototype);
  exposeApi(Window.prototype);

  var Arrive = {};
  // expose functions to unbind all arrive/leave events
  exposeUnbindApi(arriveEvents, Arrive, "unbindAllArrive");
  exposeUnbindApi(leaveEvents, Arrive, "unbindAllLeave");

  return Arrive;

})(window, typeof jQuery === 'undefined' ? null : jQuery, undefined);

}

if (typeof ai_block_class_def !== 'undefined') {

/*!
 * JavaScript Cookie v2.2.0
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 */
;(function (factory) {
  var registeredInModuleLoader;
  if (typeof define === 'function' && define.amd) {
    define(factory);
    registeredInModuleLoader = true;
  }
  if (typeof exports === 'object') {
    module.exports = factory();
    registeredInModuleLoader = true;
  }
  if (!registeredInModuleLoader) {
    var OldCookies = window.Cookies;
    var api = window.Cookies = factory();
    api.noConflict = function () {
      window.Cookies = OldCookies;
      return api;
    };
  }
}(function () {
  function extend () {
    var i = 0;
    var result = {};
    for (; i < arguments.length; i++) {
      var attributes = arguments[ i ];
      for (var key in attributes) {
        result[key] = attributes[key];
      }
    }
    return result;
  }

  function decode (s) {
    return s.replace(/(%[0-9A-Z]{2})+/g, decodeURIComponent);
  }

  function init (converter) {
    function api() {}

    function set (key, value, attributes) {
      if (typeof document === 'undefined') {
        return;
      }

      attributes = extend({
        path: '/',
        sameSite: 'Lax'
      }, api.defaults, attributes);

      if (typeof attributes.expires === 'number') {
        attributes.expires = new Date(new Date() * 1 + attributes.expires * 864e+5);
      }

      // We're using "expires" because "max-age" is not supported by IE
      attributes.expires = attributes.expires ? attributes.expires.toUTCString() : '';

      try {
        var result = JSON.stringify(value);
        if (/^[\{\[]/.test(result)) {
          value = result;
        }
      } catch (e) {}

      value = converter.write ?
        converter.write(value, key) :
        encodeURIComponent(String(value))
          .replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g, decodeURIComponent);

      key = encodeURIComponent(String(key))
        .replace(/%(23|24|26|2B|5E|60|7C)/g, decodeURIComponent)
        .replace(/[\(\)]/g, escape);

      var stringifiedAttributes = '';
      for (var attributeName in attributes) {
        if (!attributes[attributeName]) {
          continue;
        }
        stringifiedAttributes += '; ' + attributeName;
        if (attributes[attributeName] === true) {
          continue;
        }

        // Considers RFC 6265 section 5.2:
        // ...
        // 3.  If the remaining unparsed-attributes contains a %x3B (";")
        //     character:
        // Consume the characters of the unparsed-attributes up to,
        // not including, the first %x3B (";") character.
        // ...
        stringifiedAttributes += '=' + attributes[attributeName].split(';')[0];
      }

      return (document.cookie = key + '=' + value + stringifiedAttributes);
    }

    function get (key, json) {
      if (typeof document === 'undefined') {
        return;
      }

      var jar = {};
      // To prevent the for loop in the first place assign an empty array
      // in case there are no cookies at all.
      var cookies = document.cookie ? document.cookie.split('; ') : [];
      var i = 0;

      for (; i < cookies.length; i++) {
        var parts = cookies[i].split('=');
        var cookie = parts.slice(1).join('=');

        if (!json && cookie.charAt(0) === '"') {
          cookie = cookie.slice(1, -1);
        }

        try {
          var name = decode(parts[0]);
          cookie = (converter.read || converter)(cookie, name) ||
            decode(cookie);

          if (json) {
            try {
              cookie = JSON.parse(cookie);
            } catch (e) {}
          }

          jar[name] = cookie;

          if (key === name) {
            break;
          }
        } catch (e) {}
      }

      return key ? jar[key] : jar;
    }

    api.set = set;
    api.get = function (key) {
      return get(key, false /* read as raw */);
    };
    api.getJSON = function (key) {
      return get(key, true /* read as json */);
    };
    api.remove = function (key, attributes) {
      set(key, '', extend(attributes, {
        expires: -1
      }));
    };

    api.defaults = {};

    api.withConverter = init;

    return api;
  }

  return init(function () {});
}));


AiCookies = Cookies.noConflict();


ai_check_block = function (block) {
  var ai_debug = typeof ai_debugging !== 'undefined'; // 1
//  var ai_debug = false;

  if (block == null) {
    return true;
  }

  var ai_cookie_name = 'aiBLOCKS';
  var ai_cookie = AiCookies.getJSON (ai_cookie_name);
  ai_debug_cookie_status = '';

  if (ai_cookie == null) {
    ai_cookie = {};
  }

  if (typeof ai_delay_showing_pageviews !== 'undefined') {
    if (!ai_cookie.hasOwnProperty (block)) {
      ai_cookie [block] = {};
    }

    if (!ai_cookie [block].hasOwnProperty ('d')) {
      ai_cookie [block]['d'] = ai_delay_showing_pageviews;

      if (ai_debug) console.log ('AI CHECK block', block, 'NO COOKIE DATA d, delayed for', ai_delay_showing_pageviews, 'pageviews');
    }
  }

  if (ai_cookie.hasOwnProperty (block)) {
    for (var cookie_block_property in ai_cookie [block]) {

      if (cookie_block_property == 'x') {

        var code_hash = '';
        var block_object = document.querySelectorAll ('span[data-ai-block="'+block+'"]') [0]
        if ("aiHash" in block_object.dataset) {
          code_hash = block_object.dataset.aiHash;
        }

        var cookie_code_hash = '';
        if (ai_cookie [block].hasOwnProperty ('h')) {
          cookie_code_hash = ai_cookie [block]['h'];
        }
        if (ai_debug) console.log ('AI CHECK block', block, 'x cookie hash', cookie_code_hash, 'code hash', code_hash);

        var date = new Date();
        var closed_for = ai_cookie [block][cookie_block_property] - Math.round (date.getTime() / 1000);
        if (closed_for > 0 && cookie_code_hash == code_hash) {
          var message = 'closed for ' + closed_for + ' s = ' + (Math.round (10000 * closed_for / 3600 / 24) / 10000) + ' days';
          ai_debug_cookie_status = message;
          if (ai_debug) console.log ('AI CHECK block', block, message);
          if (ai_debug) console.log ('');

          return false;
        } else {
            if (ai_debug) console.log ('AI CHECK block', block, 'removing x');

            ai_set_cookie (block, 'x', '');
            if (!ai_cookie [block].hasOwnProperty ('i') && !ai_cookie [block].hasOwnProperty ('c')) {
              ai_set_cookie (block, 'h', '');
            }
          }
      } else
      if (cookie_block_property == 'd') {
        if (ai_cookie [block][cookie_block_property] != 0) {
          var message = 'delayed for ' + ai_cookie [block][cookie_block_property] + ' pageviews';
          ai_debug_cookie_status = message;
          if (ai_debug) console.log ('AI CHECK block', block, message);
          if (ai_debug) console.log ('');

          return false;
        }
      } else
      if (cookie_block_property == 'i') {

        var code_hash = '';
        var block_object = document.querySelectorAll ('span[data-ai-block="'+block+'"]') [0]
        if ("aiHash" in block_object.dataset) {
          code_hash = block_object.dataset.aiHash;
        }

        var cookie_code_hash = '';
        if (ai_cookie [block].hasOwnProperty ('h')) {
          cookie_code_hash = ai_cookie [block]['h'];
        }
        if (ai_debug) console.log ('AI CHECK block', block, 'i cookie hash', cookie_code_hash, 'code hash', code_hash);

        if (ai_cookie [block][cookie_block_property] == 0 && cookie_code_hash == code_hash) {
          var message = 'max impressions reached';
          ai_debug_cookie_status = message;
          if (ai_debug) console.log ('AI CHECK block', block, message);
          if (ai_debug) console.log ('');

          return false;
        } else

        if (ai_cookie [block][cookie_block_property] < 0 && cookie_code_hash == code_hash) {
          var date = new Date();
          var closed_for = - ai_cookie [block][cookie_block_property] - Math.round (date.getTime() / 1000);
          if (closed_for > 0) {
            var message = 'max imp. reached (' + Math. round (10000 * closed_for / 24 / 3600) / 10000 + ' days = ' + closed_for + ' s)';
            ai_debug_cookie_status = message;
            if (ai_debug) console.log ('AI CHECK block', block, message);
            if (ai_debug) console.log ('');

            return false;
          } else {
              if (ai_debug) console.log ('AI CHECK block', block, 'removing i');

              ai_set_cookie (block, 'i', '');
              if (!ai_cookie [block].hasOwnProperty ('c') && !ai_cookie [block].hasOwnProperty ('x')) {
                if (ai_debug) console.log ('AI CHECK block', block, 'cookie h removed');

                ai_set_cookie (block, 'h', '');
              }
            }
        }
      }
      if (cookie_block_property == 'ipt') {
        if (ai_cookie [block][cookie_block_property] == 0) {

          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);
          var closed_for = ai_cookie [block]['it'] - timestamp;

          if (closed_for > 0) {
            var message = 'max imp. per time reached (' + Math. round (10000 * closed_for / 24 / 3600) / 10000 + ' days = ' + closed_for + ' s)';
            ai_debug_cookie_status = message;
            if (ai_debug) console.log ('AI CHECK block', block, message);
            if (ai_debug) console.log ('');

            return false;
          }
        }
      }
      if (cookie_block_property == 'c') {

        var code_hash = '';
        var block_object = document.querySelectorAll ('span[data-ai-block="'+block+'"]') [0]
        if ("aiHash" in block_object.dataset) {
          code_hash = block_object.dataset.aiHash;
        }

        var cookie_code_hash = '';
        if (ai_cookie [block].hasOwnProperty ('h')) {
          cookie_code_hash = ai_cookie [block]['h'];
        }
        if (ai_debug) console.log ('AI CHECK block', block, 'c cookie hash', cookie_code_hash, 'code hash', code_hash);

        if (ai_cookie [block][cookie_block_property] == 0 && cookie_code_hash == code_hash) {
          var message = 'max clicks reached';
          ai_debug_cookie_status = message;
          if (ai_debug) console.log ('AI CHECK block', block, message);
          if (ai_debug) console.log ('');

          return false;
        } else

        if (ai_cookie [block][cookie_block_property] < 0 && cookie_code_hash == code_hash) {
          var date = new Date();
          var closed_for = - ai_cookie [block][cookie_block_property] - Math.round (date.getTime() / 1000);
          if (closed_for > 0) {
            var message = 'max clicks reached (' + Math. round (10000 * closed_for / 24 / 3600) / 10000 + ' days = ' + closed_for + ' s)';
            ai_debug_cookie_status = message;
            if (ai_debug) console.log ('AI CHECK block', block, message);
            if (ai_debug) console.log ('');

            return false;
          } else {
              if (ai_debug) console.log ('AI CHECK block', block, 'removing c');

              ai_set_cookie (block, 'c', '');
              if (!ai_cookie [block].hasOwnProperty ('i') && !ai_cookie [block].hasOwnProperty ('x')) {
                if (ai_debug) console.log ('AI CHECK block', block, 'cookie h removed');

                ai_set_cookie (block, 'h', '');
              }
            }
        }
      }
      if (cookie_block_property == 'cpt') {
        if (ai_cookie [block][cookie_block_property] == 0) {

          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);
          var closed_for = ai_cookie [block]['ct'] - timestamp;

          if (closed_for > 0) {
            var message = 'max clicks per time reached (' + Math. round (10000 * closed_for / 24 / 3600) / 10000 + ' days = ' + closed_for + ' s)';
            ai_debug_cookie_status = message;
            if (ai_debug) console.log ('AI CHECK block', block, message);
            if (ai_debug) console.log ('');

            return false;
          }
        }
      }
    }

    if (ai_cookie.hasOwnProperty ('G') && ai_cookie ['G'].hasOwnProperty ('cpt')) {
      if (ai_cookie ['G']['cpt'] == 0) {

        var date = new Date();
        var timestamp = Math.round (date.getTime() / 1000);
        var closed_for = ai_cookie ['G']['ct'] - timestamp;

        if (closed_for > 0) {
          var message = 'max global clicks per time reached (' + Math. round (10000 * closed_for / 24 / 3600) / 10000 + ' days = ' + closed_for + ' s)';
          ai_debug_cookie_status = message;
          if (ai_debug) console.log ('AI CHECK GLOBAL', message);
          if (ai_debug) console.log ('');

          return false;
        }
      }
    }
  }

  ai_debug_cookie_status = 'OK';
  if (ai_debug) console.log ('AI CHECK block', block, 'OK');
  if (ai_debug) console.log ('');

  return true;
}

ai_check_and_insert_block = function (block, id) {

  var ai_debug = typeof ai_debugging !== 'undefined'; // 2
//  var ai_debug = false;

  if (block == null) {
    return true;
  }

  var ai_block_divs = document.getElementsByClassName (id);
  if (ai_block_divs.length) {

    var ai_block_div = ai_block_divs [0];
    var wrapping_div = ai_block_div.closest ('.' + ai_block_class_def);

    var insert_block = ai_check_block (block);

    if (!insert_block) {
//      if (ai_debug) console.log ('AI CHECK FAILED, !insert_block', block);
      // Check for a fallback block
      if (parseInt (ai_block_div.getAttribute ('limits-fallback')) != 0 && ai_block_div.hasAttribute ('data-fallback-code')) {

        if (ai_debug) console.log ('AI CHECK FAILED, INSERTING FALLBACK BLOCK', ai_block_div.getAttribute ('limits-fallback'));

        ai_block_div.setAttribute ('data-code', ai_block_div.getAttribute ('data-fallback-code'));

        if (wrapping_div != null && wrapping_div.hasAttribute ('data-ai')) {
          if (ai_block_div.hasAttribute ('fallback-tracking') && ai_block_div.hasAttribute ('fallback_level')) {
            wrapping_div.setAttribute ('data-ai-' + ai_block_div.getAttribute ('fallback_level'), ai_block_div.getAttribute ('fallback-tracking'));
          }
        }

        insert_block = true;
      }
    }

    // Remove selector to prevent further insertions at this element
    ai_block_div.removeAttribute ('data-selector');

    if (insert_block) {
      ai_insert_code (ai_block_div);

      if (wrapping_div) {

        var debug_block = wrapping_div.querySelectorAll ('.ai-debug-block');
        if (wrapping_div && debug_block.length) {
          wrapping_div.classList.remove ('ai-list-block');
          wrapping_div.classList.remove ('ai-list-block-ip');
          wrapping_div.classList.remove ('ai-list-block-filter');
          wrapping_div.style.visibility = '';
          if (wrapping_div.classList.contains ('ai-remove-position')) {
            wrapping_div.style.position = '';
          }
        }

      }
    } else {
        var ai_block_div_data = ai_block_div.closest ('div[data-ai]');
        if (ai_block_div_data != null && typeof ai_block_div_data.getAttribute ("data-ai") != "undefined") {
          var data = JSON.parse (b64d (ai_block_div_data.getAttribute ("data-ai")));
          if (typeof data !== "undefined" && data.constructor === Array) {
            data [1] = "";
            ai_block_div_data.setAttribute ("data-ai", b64e (JSON.stringify (data)));
          }
        }
        var debug_block = wrapping_div.querySelectorAll ('.ai-debug-block');
        if (wrapping_div && debug_block.length) {
          wrapping_div.classList.remove ('ai-list-block');
          wrapping_div.classList.remove ('ai-list-block-ip');
          wrapping_div.classList.remove ('ai-list-block-filter');
          wrapping_div.style.visibility = '';
          if (wrapping_div.classList.contains ('ai-remove-position')) {
            wrapping_div.style.position = '';
          }
        }
      }

    // Remove class
    ai_block_div.classList.remove (id);
  }

  var ai_debug_bars = document.querySelectorAll ('.' + id + '-dbg');

//  for (let ai_debug_bar of ai_debug_bars) {
  for (var index = 0, len = ai_debug_bars.length; index < len; index++) {
    var ai_debug_bar = ai_debug_bars [index];
    ai_debug_bar.querySelector ('.ai-status').textContent = ai_debug_cookie_status;
    ai_debug_bar.querySelector ('.ai-cookie-data').textContent = ai_get_cookie_text (block);
    ai_debug_bar.classList.remove (id + '-dbg');
  }
}

function ai_load_cookie () {

  var ai_debug = typeof ai_debugging !== 'undefined'; // 3
//  var ai_debug = false;

  var ai_cookie_name = 'aiBLOCKS';
  var ai_cookie = AiCookies.getJSON (ai_cookie_name);

  if (ai_cookie == null) {
    ai_cookie = {};

    if (ai_debug) console.log ('AI COOKIE NOT PRESENT');
  }

  if (ai_debug) console.log ('AI COOKIE LOAD', ai_cookie);

  return ai_cookie;
}

function ai_get_cookie (block, property) {

  var ai_debug = typeof ai_debugging !== 'undefined'; // 4
//  var ai_debug = false;

  var value = '';
  var ai_cookie = ai_load_cookie ();

  if (ai_cookie.hasOwnProperty (block)) {
    if (ai_cookie [block].hasOwnProperty (property)) {
      value = ai_cookie [block][property];
    }
  }

  if (ai_debug) console.log ('AI COOKIE GET block:', block, 'property:', property, 'value:', value);

  return value;
}

function ai_set_cookie (block, property, value) {

  function isEmpty (obj) {
    for (var key in obj) {
        if (obj.hasOwnProperty (key))
          return false;
    }
    return true;
  }

  var ai_cookie_name = 'aiBLOCKS';
  var ai_debug = typeof ai_debugging !== 'undefined'; // 5
//  var ai_debug = false;

  if (ai_debug) console.log ('AI COOKIE SET block:', block, 'property:', property, 'value:', value);

  var ai_cookie = ai_load_cookie ();

  if (value === '') {
    if (ai_cookie.hasOwnProperty (block)) {
      delete ai_cookie [block][property];
      if (isEmpty (ai_cookie [block])) {
        delete ai_cookie [block];
      }
    }
  } else {
      if (!ai_cookie.hasOwnProperty (block)) {
        ai_cookie [block] = {};
      }
      ai_cookie [block][property] = value;
    }

  if (Object.keys (ai_cookie).length === 0 && ai_cookie.constructor === Object) {
    AiCookies.remove (ai_cookie_name);

    if (ai_debug) console.log ('AI COOKIE REMOVED');
  } else {
      AiCookies.set (ai_cookie_name, ai_cookie, {expires: 365, path: '/'});
    }

  if (ai_debug) {
    var ai_cookie_test = AiCookies.getJSON (ai_cookie_name);
    if (typeof (ai_cookie_test) != 'undefined') {
      console.log ('AI COOKIE NEW', ai_cookie_test);

      console.log ('AI COOKIE DATA:');
      for (var cookie_block in ai_cookie_test) {
        for (var cookie_block_property in ai_cookie_test [cookie_block]) {
          if (cookie_block_property == 'x') {
            var date = new Date();
            var closed_for = ai_cookie_test [cookie_block][cookie_block_property] - Math.round (date.getTime() / 1000);
            console.log ('  BLOCK', cookie_block, 'closed for', closed_for, 's = ', Math.round (10000 * closed_for / 3600 / 24) / 10000, 'days');
          } else
          if (cookie_block_property == 'd') {
            console.log ('  BLOCK', cookie_block, 'delayed for', ai_cookie_test [cookie_block][cookie_block_property], 'pageviews');
          } else
          if (cookie_block_property == 'e') {
            console.log ('  BLOCK', cookie_block, 'show every', ai_cookie_test [cookie_block][cookie_block_property], 'pageviews');
          } else
          if (cookie_block_property == 'i') {
            var i = ai_cookie_test [cookie_block][cookie_block_property];
            if (i >= 0) {
              console.log ('  BLOCK', cookie_block, ai_cookie_test [cookie_block][cookie_block_property], 'impressions until limit');
            } else {
                var date = new Date();
                var closed_for = - i - Math.round (date.getTime() / 1000);
                console.log ('  BLOCK', cookie_block, 'max impressions, closed for', closed_for, 's =', Math.round (10000 * closed_for / 3600 / 24) / 10000, 'days');
              }
          } else
          if (cookie_block_property == 'ipt') {
            console.log ('  BLOCK', cookie_block, ai_cookie_test [cookie_block][cookie_block_property], 'impressions until limit per time period');
          } else
          if (cookie_block_property == 'it') {
            var date = new Date();
            var closed_for = ai_cookie_test [cookie_block][cookie_block_property] - Math.round (date.getTime() / 1000);
            console.log ('  BLOCK', cookie_block, 'impressions limit expiration in', closed_for, 's =', Math.round (10000 * closed_for / 3600 / 24) / 10000, 'days');
          } else
          if (cookie_block_property == 'c') {
            var c = ai_cookie_test [cookie_block][cookie_block_property]
            if (c >= 0) {
              console.log ('  BLOCK', cookie_block, c, 'clicks until limit');
            } else {
                var date = new Date();
                var closed_for = - c - Math.round (date.getTime() / 1000);
                console.log ('  BLOCK', cookie_block, 'max clicks, closed for', closed_for, 's =', Math.round (10000 * closed_for / 3600 / 24) / 10000, 'days');
              }
          } else
          if (cookie_block_property == 'cpt') {
            console.log ('  BLOCK', cookie_block, ai_cookie_test [cookie_block][cookie_block_property], 'clicks until limit per time period');
          } else
          if (cookie_block_property == 'ct') {
            var date = new Date();
            var closed_for = ai_cookie_test [cookie_block][cookie_block_property] - Math.round (date.getTime() / 1000);
            console.log ('  BLOCK', cookie_block, 'clicks limit expiration in ', closed_for, 's =', Math.round (10000 * closed_for / 3600 / 24) / 10000, 'days');
          } else
          if (cookie_block_property == 'h') {
            console.log ('  BLOCK', cookie_block, 'hash', ai_cookie_test [cookie_block][cookie_block_property]);
          } else
          console.log ('      ?:', cookie_block, ':', cookie_block_property, ai_cookie_test [cookie_block][cookie_block_property]);
        }
        console.log ('');
      }
    } else console.log ('AI COOKIE NOT PRESENT');
  }

  return ai_cookie;
}

ai_get_cookie_text = function (block) {
  var ai_cookie_name = 'aiBLOCKS';
  var ai_cookie = AiCookies.getJSON (ai_cookie_name);

  if (ai_cookie == null) {
    ai_cookie = {};
  }

  var global_data = '';
  if (ai_cookie.hasOwnProperty ('G')) {
    global_data = 'G[' + JSON.stringify (ai_cookie ['G']).replace (/\"/g, '').replace ('{', '').replace('}', '') + '] ';
  }

  var block_data = '';
  if (ai_cookie.hasOwnProperty (block)) {
    block_data = JSON.stringify (ai_cookie [block]).replace (/\"/g, '').replace ('{', '').replace('}', '');
  }

  return global_data + block_data;
}

}
if (typeof ai_internal_tracking !== 'undefined') {

function matchRuleShort (str, rule) {
  var escapeRegex = (str) => str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
  return new RegExp("^" + rule.split("*").map(escapeRegex).join(".*") + "$").test(str);
}

function ai_addEventListener (el, eventName, eventHandler, selector) {
  if (selector) {
    const wrappedHandler = (e) => {
      if (e.target && e.target.matches(selector)) {
        eventHandler(e);
      }
    };
    el.addEventListener (eventName, wrappedHandler);
    return wrappedHandler;
  } else {
    el.addEventListener (eventName, eventHandler);
    return eventHandler;
  }
}

// ***
//(function($) {
  // Tracking handler manager
  // ***
//  $.fn.iframeTracker = function(handler) {
  installIframeTracker = function (handler, target) {
    // Building handler object from handler function
    if (typeof handler == "function") {
      handler = {
        blurCallback: handler
      };
    }

    // ***
//    var target = this.get();
    if (handler === null || handler === false) {
//      $.iframeTracker.untrack(target);
      ai_iframeTracker.untrack (target);
    } else if (typeof handler == "object") {
//      $.iframeTracker.track(target, handler);
      ai_iframeTracker.track (target, handler);
    } else {
      throw new Error ("Wrong handler type (must be an object, or null|false to untrack)");
    }
    return this;
  };

  // ***
  var ai_mouseoverHander = function (handler, event){
    event.data = {'handler': handler};
    ai_iframeTracker.mouseoverListener (event);
  }
  var ai_mouseoutHander = function (handler, event){
    event.data = {'handler': handler};
    ai_iframeTracker.mouseoutListener (event);
  }

  // Iframe tracker common object
  // ***
//  $.iframeTracker = {
  ai_iframeTracker = {
    // State
    focusRetriever: null,  // Element used for restoring focus on window (element)
    focusRetrieved: false, // Says if the focus was retrieved on the current page (bool)
    handlersList: [],      // Store a list of every trakers (created by calling $(selector).iframeTracker...)
    isIE8AndOlder: false,  // true for Internet Explorer 8 and older

    // Init (called once on document ready)
    init: function () {
    // ***
      // Determine browser version (IE8-)
        try {
          // ### AI
          // To prevent replacement of regexp pattern with CDN url (CDN code bug)
          var msie_regexp = new RegExp ('(msie) ([\\w.]+)', 'i');

//          var matches = navigator.userAgent.match(/(msie) ([\w.]+)/i);
          var matches = navigator.userAgent.match (msie_regexp);
          // ### /AI

          if (matches [2] < 9) {
            this.isIE8AndOlder = true;
          }
        } catch (ex2) {}

      // Listening window blur
      // ***
//      $(window).focus();
      window.focus ();

      // ***
//      $(window).blur(function(e) {
      window.addEventListener ('blur', (event) => {
        // ***
//        $.iframeTracker.windowLoseFocus (e);
        ai_iframeTracker.windowLoseFocus (event);
      });

      // Focus retriever (get the focus back to the page, on mouse move)
      // ### AI
      // ### added label for tools like https://web.dev/measure/
      // ***
//      $("body").append('<div style="position:fixed; top:0; left:0; overflow:hidden;"><input style="position:absolute; left:-300px;" type="text" value="" id="focus_retriever" readonly="true" /><label for="focus_retriever">&nbsp;</label></div>');
//      document.querySelector ('body').innerHTML += '<div style="position:fixed; top:0; left:0; overflow:hidden;"><input style="position:absolute; left:-300px;" type="text" value="" id=" focus_retriever" readonly="true" /><label for="focus_retriever">&nbsp;</label></div>';

      var focus_retriever_holder = document.createElement ('div');
      focus_retriever_holder.style = 'position:fixed; top:0; left:0; overflow:hidden;';
      focus_retriever_holder.innerHTML = '<input style="position:absolute; left:-300px;" type="text" value="" id="focus_retriever" readonly="true" /><label for="focus_retriever">&nbsp;</label>';
      document.querySelector ('body').append (focus_retriever_holder);

      // ### /AI
      // ***
//      this.focusRetriever = $("#focus_retriever");
      this.focusRetriever = document.querySelector ("#focus_retriever");
      this.focusRetrieved = false;

      // Special processing to make it work with my old friend IE8 (and older) ;)
      if (this.isIE8AndOlder) {
        // Blur doesn't works correctly on IE8-, so we need to trigger it manually

        this.focusRetriever.blur (function (e) {
          e.stopPropagation ();
          e.preventDefault ();
          // ***
//          $.iframeTracker.windowLoseFocus(e);
          ai_iframeTracker.windowLoseFocus (e);

        });

        // Keep focus on window (fix bug IE8-, focusable elements)
        // ***
//        $("body").click(function(e) {
        document.querySelector ('body').addEventListener ('click', (e) => {
          // ***
//          $(window).focus();
          window.focus ();
        });
        // ***
//        $("form").click(function(e) {
        document.querySelector ('form').addEventListener ('click', (e) => {
          e.stopPropagation ();
        });

        // Same thing for "post-DOMready" created forms (issue #6)
        try {
          // ***
//          $("body").on("click", "form", function(e) {
          ai_addEventListener (document.querySelector ('body'), 'click', (e) => {e.stopPropagation();}, 'form');
        } catch (ex) {
          // ***
//          console.log("[iframeTracker] Please update jQuery to 1.7 or newer. (exception: " + ex.message + ")");
          console.log ("[iframeTracker] error (exception: " + ex.message + ")");
        }
      }
    },

    // Add tracker to target using handler (bind boundary listener + register handler)
    // target: Array of target elements (native DOM elements)
    // handler: User handler object
    track: function (target, handler) {
      // Adding target elements references into handler
      handler.target = target;

      // Storing the new handler into handler list
      // ***
//      $.iframeTracker.handlersList.push(handler);
      ai_iframeTracker.handlersList.push (handler);

      // Binding boundary listener
      // ***
//      $(target)
//        .bind("mouseover", { handler: handler }, $.iframeTracker.mouseoverListener)
//        .bind("mouseout",  { handler: handler }, $.iframeTracker.mouseoutListener);

      target.addEventListener ('mouseover', ai_mouseoverHander.bind (event, handler), false);
      target.addEventListener ('mouseout', ai_mouseoutHander.bind (event, handler), false);
    },

    // Remove tracking on target elements
    // target: target element
    untrack: function (target) {
      if (typeof Array.prototype.filter != "function") {
        console.log ("Your browser doesn't support Array filter, untrack disabled");
        return;
      }

      // Unbinding boundary listener
      // ***
//      $(target).each(function(index) {
      target.forEach ((el, i) => {
//        $(this)
//          .unbind("mouseover", $.iframeTracker.mouseoverListener)
//          .unbind("mouseout", $.iframeTracker.mouseoutListener);

        el.removeEventListener ('mouseover', ai_mouseoverHander, false);
        el.removeEventListener ('mouseout',  ai_mouseoutHander,  false);
      });

      // Handler garbage collector
      var nullFilter = function(value) {
        return value === null ? false : true;
      };
      for (var i in this.handlersList) {
        // Prune target
        for (var j in this.handlersList [i].target) {
          if ($.inArray (this.handlersList [i].target [j], target) !== -1) {
            this.handlersList [i].target [j] = null;
          }
        }
        this.handlersList [i].target = this.handlersList[i].target.filter (nullFilter);

        // Delete handler if unused
        if (this.handlersList [i].target.length === 0) {
          this.handlersList [i] = null;
        }
      }
      this.handlersList = this.handlersList.filter (nullFilter);
    },

    // Target mouseover event listener
    mouseoverListener: function(e) {
      e.data.handler.over = true;
      // ***
//      $.iframeTracker.retrieveFocus();
      ai_iframeTracker.retrieveFocus ();
      try {
        // ***
//        e.data.handler.overCallback(this, e);
        e.data.handler.overCallback (e.data.handler.target, e);
      } catch (ex) {}
    },

    // Target mouseout event listener
    mouseoutListener: function(e) {
      e.data.handler.over = false;
      // ***
//      $.iframeTracker.retrieveFocus();
      ai_iframeTracker.retrieveFocus ();
      try {
        // ***
//        e.data.handler.outCallback(this, e);
        e.data.handler.outCallback (e.data.handler.target, e);
      } catch (ex) {}
    },

    // Give back focus from an iframe to parent page
    retrieveFocus: function() {
      if (document.activeElement && document.activeElement.tagName === "IFRAME") {
        var process_iframe = true;

        // Do not process listed iframes
        if (document.activeElement.hasAttribute ('id') && typeof ai_ignore_iframe_ids !== "undefined" && ai_ignore_iframe_ids.constructor === Array) {
          var iframe_id = document.activeElement.id;
          ai_ignore_iframe_ids.forEach (function (ignored_id) {if (matchRuleShort (iframe_id, ignored_id)) process_iframe = false});
        }

        if (process_iframe && document.activeElement.hasAttribute ('class') && typeof ai_ignore_iframe_classes !== "undefined" && ai_ignore_iframe_classes.constructor === Array) {
          var iframe_class = document.activeElement.className;
          ai_ignore_iframe_classes.forEach (function (ignored_class) {if (matchRuleShort (iframe_class, ignored_class)) process_iframe = false});
        }

        if (process_iframe) {
          // ***
//          $.iframeTracker.focusRetriever.focus();
          ai_iframeTracker.focusRetriever.focus ();
          // ***
//          $.iframeTracker.focusRetrieved = true;
          ai_iframeTracker.focusRetrieved = true;
        }
      }
    },

    // Calls blurCallback for every handler with over=true on window blur
    windowLoseFocus: function (e) {
      for (var i in this.handlersList) {
        if (this.handlersList [i].over === true) {
          try {
            this.handlersList [i].blurCallback (e);
          } catch (ex) {}
        }
      }
    }
  };

function ai_ready (fn) {
  if (document.readyState === 'complete' || (document.readyState !== 'loading' && !document.documentElement.doScroll)) {
    fn ();
  } else {
     document.addEventListener ('DOMContentLoaded', fn);
  }
}

  // Init the iframeTracker on document ready
    // ***
//  $(document).ready(function() {
    // ***
//    $.iframeTracker.init();
function ai_init_IframeTracker () {
  ai_iframeTracker.init ();
}

ai_ready (ai_init_IframeTracker);

// ***
//})(jQuery);

// ***
//}));


ai_tracking_finished = false;

// ***
//jQuery(document).ready(function($) {
function ai_tracking () {

//  var ai_internal_tracking = AI_INTERNAL_TRACKING;
//  var ai_external_tracking = AI_EXTERNAL_TRACKING;

//  var ai_external_tracking_category  = "AI_EXT_CATEGORY";
//  var ai_external_tracking_action    = "AI_EXT_ACTION";
//  var ai_external_tracking_label     = "AI_EXT_LABEL";
//  var ai_external_tracking_username  = "WP_USERNAME";

//  var ai_track_pageviews = AI_TRACK_PAGEVIEWS;
//  var ai_advanced_click_detection = AI_ADVANCED_CLICK_DETECTION;
//  var ai_viewport_widths = AI_VIEWPORT_WIDTHS;
//  var ai_viewport_indexes = AI_VIEWPORT_INDEXES;
//  var ai_viewport_names = JSON.parse (b64d ("AI_VIEWPORT_NAMES"));
//  var ai_data_id = "AI_NONCE";
//  var ai_ajax_url = "AI_SITE_URL/wp-admin/admin-ajax.php";
//  var ai_debug_tracking = AI_DEBUG_TRACKING;

  if (ai_debug_tracking) {
    ai_ajax_url = ai_ajax_url + '?ai-debug-tracking=1';
  }

  Number.isInteger = Number.isInteger || function (value) {
    return typeof value === "number" &&
           isFinite (value) &&
           Math.floor (value) === value;
  };

  function replace_tags (text, event, block, block_name, block_counter, version, version_name) {
    text = text.replace ('[EVENT]',                 event);
    text = text.replace ('[BLOCK_NUMBER]',          block);
    text = text.replace ('[BLOCK_NAME]',            block_name);
    text = text.replace ('[BLOCK_COUNTER]',         block_counter);
    text = text.replace ('[VERSION_NUMBER]',        version);
    text = text.replace ('[VERSION_NAME]',          version_name);
    text = text.replace ('[BLOCK_VERSION_NUMBER]',  block + (version == 0 ? '' : ' - ' + version));
    text = text.replace ('[BLOCK_VERSION_NAME]',    block_name + (version_name == '' ? '' : ' - ' + version_name));
    text = text.replace ('[WP_USERNAME]',           ai_external_tracking_username);

    return (text);
  }

  function external_tracking (event, block, block_name, block_counter, version, version_name, non_interaction) {

    var category = replace_tags (ai_external_tracking_category, event, block, block_name, block_counter, version, version_name);
    var action   = replace_tags (ai_external_tracking_action,   event, block, block_name, block_counter, version, version_name);
    var label    = replace_tags (ai_external_tracking_label,    event, block, block_name, block_counter, version, version_name);

    var ai_debug = typeof ai_debugging !== 'undefined'; // 1
//    var ai_debug = false;

    if (ai_debug) console.log ("AI TRACKING EXTERNAL", event, block, '["' + category + '", "' + action + '", "' + label + '"]');

    if (typeof ai_external_tracking_event == 'function') {
      if (ai_debug) console.log ('AI TRACKING ai_external_tracking_event (' + block + ', ' + event + ', ' + category + ', ' + action + ', ' + label + ', ' + non_interaction + ')');

      var event_data = {'event': event, 'block': block, 'block_name': block_name, 'block_counter': block_counter, 'version': version, 'version_name': version_name};

      var result = ai_external_tracking_event (event_data, category, action, label, non_interaction);

      if (ai_debug) console.log ('AI TRACKING ai_external_tracking_event ():', result);

      if (result == 0) return;
    }

//        Google Analytics
    if (typeof window.ga == 'function') {
      var ga_command = 'send';

      if (typeof ai_ga_tracker_name == 'string') {
        ga_command = ai_ga_tracker_name + '.' + ga_command;

        if (ai_debug) console.log ("AI TRACKING ai_ga_tracker_name:", ai_ga_tracker_name);
      } else {
          var trackers = ga.getAll();

          if (trackers.length != 0) {
            var tracker_name = trackers [0].get ('name');
            if (tracker_name != 't0') {
              ga_command = tracker_name + '.' + ga_command;

              if (ai_debug) console.log ("AI TRACKING ga tracker name:", tracker_name);
            }
          } else {
              if (ai_debug) console.log ("AI TRACKING no ga tracker");
            }
        }

      ga (ga_command, 'event', {
        eventCategory: category,
        eventAction: action,
        eventLabel: label,
        nonInteraction: non_interaction
      });

      if (ai_debug) console.log ("AI TRACKING Google Universal Analytics:", non_interaction);
    } else

    if (typeof window.gtag == 'function') {
      gtag ('event', 'impression', {
        'event_category': category,
        'event_action': action,
        'event_label': label,
        'non_interaction': non_interaction
      });

      if (ai_debug) console.log ("AI TRACKING Global Site Tag:", non_interaction);
    } else

    if (typeof window.__gaTracker == 'function') {
      __gaTracker ('send', 'event', {
        eventCategory: category,
        eventAction: action,
        eventLabel: label,
        nonInteraction: non_interaction
      });

      if (ai_debug) console.log ("AI TRACKING Google Universal Analytics by MonsterInsights:", non_interaction);
    } else

    if (typeof _gaq == 'object') {
//      _gaq.push (['_trackEvent', category, action, label]);
      _gaq.push (['_trackEvent', category, action, label, undefined, non_interaction]);

      if (ai_debug) console.log ("AI TRACKING Google Legacy Analytics:", non_interaction);
    }

//        Matomo (Piwik)
    if (typeof _paq == 'object') {
      _paq.push (['trackEvent', category, action, label]);

      if (ai_debug) console.log ("AI TRACKING Matomo");
    }
  }

  function ai_click (data, click_type) {

    var ai_debug = typeof ai_debugging !== 'undefined'; //2
//    var ai_debug = false;

    var block         = data [0];
    var code_version  = data [1];

    if (Number.isInteger (code_version)) {

      if (typeof ai_check_data == 'undefined' && typeof ai_check_data_timeout == 'undefined') {
        if (ai_debug) console.log ('AI CHECK CLICK - DATA NOT SET YET');

        ai_check_data_timeout = true;
        setTimeout (function() {if (ai_debug) console.log (''); if (ai_debug) console.log ('AI CHECK CLICK TIMEOUT'); ai_click (data, click_type);}, 2500);
        return;
      }

      if (ai_debug) console.log ('AI CHECK CLICK block', block);
      if (ai_debug) console.log ('AI CHECK CLICK data', ai_check_data);

      ai_cookie = ai_load_cookie ();

      for (var cookie_block in ai_cookie) {

        if (parseInt (block) != parseInt (cookie_block)) continue;

        for (var cookie_block_property in ai_cookie [cookie_block]) {
          if (cookie_block_property == 'c') {
            if (ai_debug) console.log ('AI CHECK CLICKS block:', cookie_block);

            var clicks = ai_cookie [cookie_block][cookie_block_property];
            if (clicks > 0) {
              if (ai_debug) console.log ('AI CLICK, block', cookie_block, 'remaining', clicks - 1, 'clicks');

              ai_set_cookie (cookie_block, 'c', clicks - 1);

              if (clicks == 1) {
                if (ai_debug) console.log ('AI CLICKS #1, closing block', block, '- no more clicks');

                // ***
//                var cfp_time = $('span[data-ai-block=' + block + ']').data ('ai-cfp-time');
                var cfp_time = document.querySelector ('span[data-ai-block="' + block + '"]').dataset.aiCfpTime;
                var date = new Date();
                var timestamp = Math.round (date.getTime() / 1000);

                var closed_until = timestamp + 7 * 24 * 3600;
                ai_set_cookie (cookie_block, 'c', - closed_until);

                // ***
//                setTimeout (function() {$('span[data-ai-block=' + block + ']').closest ("div[data-ai]").remove ();}, 50);
                setTimeout (function() {
                  document.querySelectorAll ('span[data-ai-block="' + block + '"]').forEach ((el, index) => {
                    var closest = el.closest ("div[data-ai]");
                    if (closest) {
                      closest.remove ();
                    }
                  });
                }, 50);
              } else ai_set_cookie (cookie_block, 'c', clicks - 1);
            }
          } else

          if (cookie_block_property == 'cpt') {
            if (ai_debug) console.log ('AI CHECK CLICKS PER TIME PERIOD block:', cookie_block);

            var clicks = ai_cookie [cookie_block][cookie_block_property];
            if (clicks > 0) {
              if (ai_debug) console.log ('AI CLICKS, block', cookie_block, 'remaining', clicks - 1, 'clicks per time period');

              ai_set_cookie (cookie_block, 'cpt', clicks - 1);

              if (clicks == 1) {
                if (ai_debug) console.log ('AI CLICKS, closing block', block, '- no more clicks per time period');

                // ***
//                var cfp_time = $('span[data-ai-block=' + block + ']').data ('ai-cfp-time');
                var cfp_time = document.querySelector ('span[data-ai-block="' + block + '"]').dataset.aiCfpTime;

                var date = new Date();
                var timestamp = Math.round (date.getTime() / 1000);

                var closed_until = ai_cookie [cookie_block]['ct'];
                ai_set_cookie (cookie_block, 'x', closed_until);

                if (ai_debug) console.log ('AI CLICKS, closing block', block, 'for', closed_until - timestamp, 's');

                // ***
//                var block_to_close = $('span[data-ai-block=' + block + ']').closest ("div[data-ai]");
//                setTimeout (function() {
//                  block_to_close.closest ("div[data-ai]").remove ();
//                }, 75); // Remove after CFP check
                setTimeout (function() {
                  document.querySelectorAll ('span[data-ai-block="' + block + '"]').forEach ((el, index) => {
                    var closest = el.closest ("div[data-ai]");
                    if (closest) {
                      closest.remove ();
                    }
                  });
                }, 75); // After CFP is processed

                if (typeof cfp_time != 'undefined') {
                  if (ai_debug) console.log ('AI CLICKS CFP, closing block', block, 'for', cfp_time, 'days');

                  var closed_until = timestamp + cfp_time * 24 * 3600;

//                  if (ai_debug) console.log ('AI COOKIE x 3 block', block, 'closed_until', closed_until);
                  ai_set_cookie (block, 'x', closed_until);

                  // ***
//                  $('span.ai-cfp').each (function (index) {
                  document.querySelectorAll ('span.ai-cfp').forEach ((el, index) => {
                    // ***
//                    var cfp_block = $(this).data ('ai-block');
                    var cfp_block = el.dataset.aiBlock;

                    if (ai_debug) console.log ('AI CLICKS CFP, closing block', cfp_block, 'for', cfp_time, 'days');

                    // ***
//                    var block_to_close = $(this);
                    var block_to_close = el;

                    setTimeout (function() {
//                      block_to_close.closest ("div[data-ai]").remove ();
                      var closest = block_to_close.closest ("div[data-ai]");
                      if (closest) {
                        closest.remove ();
                      }
                    }, 50);

//                  if (ai_debug) console.log ('AI COOKIE x 4 block', cfp_block, 'closed_until', closed_until);
                    ai_set_cookie (cfp_block, 'x', closed_until);
                  });
                }
              }
            } else {
                if (ai_check_data.hasOwnProperty (cookie_block) && ai_check_data [cookie_block].hasOwnProperty ('cpt') && ai_check_data [cookie_block].hasOwnProperty ('ct')) {
                  if (ai_cookie.hasOwnProperty (cookie_block) && ai_cookie [cookie_block].hasOwnProperty ('ct')) {
                    var date = new Date();
                    var closed_for = ai_cookie [cookie_block]['ct'] - Math.round (date.getTime() / 1000);
                    if (closed_for <= 0) {
                      if (ai_debug) console.log ('AI CLICKS, block', cookie_block, 'set max clicks period (', ai_check_data [cookie_block]['ct'], 'days =', ai_check_data [cookie_block]['ct'] * 24 * 3600, 's)');

                      var timestamp = Math.round (date.getTime() / 1000);

                      ai_set_cookie (cookie_block, 'cpt', ai_check_data [cookie_block]['cpt'] - 1);
                      ai_set_cookie (cookie_block, 'ct', Math.round (timestamp + ai_check_data [cookie_block]['ct'] * 24 * 3600));
                    }
                  }
                } else {
                    if (ai_cookie.hasOwnProperty (cookie_block) && ai_cookie [cookie_block].hasOwnProperty ('cpt')) {
                      if (ai_debug) console.log ('AI CLICKS, block', cookie_block, 'removing cpt');

                      ai_set_cookie (cookie_block, 'cpt', '');
                    }
                    if (ai_cookie.hasOwnProperty (cookie_block) && ai_cookie [cookie_block].hasOwnProperty ('ct')) {
                      if (ai_debug) console.log ('AI CLICKS, block', cookie_block, 'removing ct');

                      ai_set_cookie (cookie_block, 'ct', '');
                    }
                  }
              }
          }
        }
      }

      if (ai_cookie.hasOwnProperty ('G') && ai_cookie ['G'].hasOwnProperty ('cpt')) {
        if (ai_debug) console.log ('AI CHECK GLOBAL CLICKS PER TIME PERIOD');

        var clicks = ai_cookie ['G']['cpt'];
        if (clicks > 0) {
          if (ai_debug) console.log ('AI CLICKS, GLOBAL remaining', clicks - 1, 'clicks per time period');

          ai_set_cookie ('G', 'cpt', clicks - 1);

          if (clicks == 1) {
            if (ai_debug) console.log ('AI CLICKS, closing block', block, '- no more global clicks per time period');

            // ***
//            var cfp_time = $('span[data-ai-block=' + block + ']').data ('ai-cfp-time');
            var cfp_time = document.querySelector ('span[data-ai-block="' + block + '"]').dataset.aiCfpTime;
            var date = new Date();
            var timestamp = Math.round (date.getTime() / 1000);

            var closed_until = ai_cookie ['G']['ct'];
            ai_set_cookie (block, 'x', closed_until);

            if (ai_debug) console.log ('AI CLICKS, closing block', block, 'for', closed_until - timestamp, 's');

            // ***
//            var block_to_close = $('span[data-ai-block=' + block + ']').closest ("div[data-ai]");
            setTimeout (function() {
              document.querySelectorAll ('span[data-ai-block="' + block + '"]').forEach ((el, index) => {
                var closest = el.closest ("div[data-ai]");
                if (closest) {
                  closest.remove ();
                }
              });
            }, 75); // After CFP is processed

            if (ai_debug) console.log ('AI CLICKS GLOBAL block', block, 'cfp_time', cfp_time);

            if (typeof cfp_time != 'undefined') {
              if (ai_debug) console.log ('AI CLICKS GLOBAL CFP, closing block', block, 'for', cfp_time, 'days');

              var closed_until = timestamp + cfp_time * 24 * 3600;

//                if (ai_debug) console.log ('AI COOKIE x 3 block', block, 'closed_until', closed_until);
              ai_set_cookie (block, 'x', closed_until);

              // ***
//              $('span.ai-cfp').each (function (index) {
              document.querySelectorAll ('span.ai-cfp').forEach ((el, index) => {
                // ***
//                var cfp_block = $(this).data ('ai-block');
                var cfp_block = el.dataset.aiBlock;
                if (ai_debug) console.log ('AI CLICKS GLOBAL CFP, closing block', cfp_block, 'for', cfp_time, 'days');

                // ***
//                var block_to_close = $(this);
                var block_to_close = el;
                setTimeout (function() {
                  block_to_close.closest ("div[data-ai]").remove ();
                }, 50);

//                if (ai_debug) console.log ('AI COOKIE x 4 block', cfp_block, 'closed_until', closed_until);
                ai_set_cookie (cfp_block, 'x', closed_until);
              });
            }
          }
        } else {
            if (ai_check_data.hasOwnProperty ('G') && ai_check_data ['G'].hasOwnProperty ('cpt') && ai_check_data ['G'].hasOwnProperty ('ct')) {
              if (ai_cookie.hasOwnProperty ('G') && ai_cookie ['G'].hasOwnProperty ('ct')) {
                var date = new Date();
                var closed_for = ai_cookie ['G']['ct'] - Math.round (date.getTime() / 1000);
                if (closed_for <= 0) {
                  if (ai_debug) console.log ('AI CLICKS GLOBAL set max clicks period (', ai_check_data ['G']['ct'], 'days =', ai_check_data ['G']['ct'] * 24 * 3600, 's)');

                  var timestamp = Math.round (date.getTime() / 1000);

                  ai_set_cookie ('G', 'cpt', ai_check_data ['G']['cpt'] - 1);
                  ai_set_cookie ('G', 'ct', Math.round (timestamp + ai_check_data ['G']['ct'] * 24 * 3600));
                }
              }
            } else {
                if (ai_cookie.hasOwnProperty ('G') && ai_cookie ['G'].hasOwnProperty ('cpt')) {
                  if (ai_debug) console.log ('AI CLICKS GLOBAL removing cpt');

                  ai_set_cookie ('G', 'cpt', '');
                }
                if (ai_cookie.hasOwnProperty ('G') && ai_cookie ['G'].hasOwnProperty ('ct')) {
                  if (ai_debug) console.log ('AI CLICKS GLOBAL removing ct');

                  ai_set_cookie ('G', 'ct', '');
                }
              }
          }
      }


      if (ai_debug) console.log ("AI CLICK: ", data, click_type);

      if (ai_internal_tracking) {
        if (typeof ai_internal_tracking_no_clicks === 'undefined') {
              // ***
//          $.ajax ({
//              url: ai_ajax_url,
//              type: "post",
//              data: {
//                action: "ai_ajax",
//                ai_check: ai_data_id,
//                click: block,
//                version: code_version,
//                type: click_type,
//              },
//              async: true
//          }).done (function (data) {

          var url_data = {
            action: "ai_ajax",
            ai_check: ai_data_id,
            click: block,
            version: code_version,
            type: click_type,
          };

          var formBody = [];
          for (var property in url_data) {
            var encodedKey = encodeURIComponent (property);
            var encodedValue = encodeURIComponent (url_data [property]);
            formBody.push (encodedKey + "=" + encodedValue);
          }
          formBody = formBody.join ("&");

          async function ai_post_clicks () {
            const response = await fetch (ai_ajax_url, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
              },
              body: formBody
            });

            const text = await response.text ();

            return text;
          }

          ai_post_clicks ().then (data => {

              data = data.trim ();
              if (data != "") {
                var db_records = JSON.parse (data);

                if (ai_debug) {
                  console.log ("AI DB RECORDS: ", db_records);
                }

                if (typeof db_records ['#'] != 'undefined' && db_records ['#'] == block) {
                  // Reload cookie data
                  ai_cookie = ai_load_cookie ();

                  var date = new Date();
                  var closed_until = Math.round (date.getTime() / 1000) + 12 * 3600;

                  if (ai_debug) console.log ("AI SERVERSIDE LIMITED BLOCK:", block);

                  if (!ai_cookie.hasOwnProperty (block) || !ai_cookie [block].hasOwnProperty ('x')) {
                    if (ai_debug) console.log ("AI SERVERSIDE LIMITED BLOCK:", block, ' not closed - closing for 12 hours');

                    ai_set_cookie (block, 'x', closed_until);
                  }

//                  setTimeout (function() {$('span[data-ai-block=' + block + ']').closest ("div[data-ai]").remove ();}, 50);
                  // ***
                  setTimeout (function () {
                    document.querySelectorAll ('span[data-ai-block="' + block + '"]').forEach ((el, index) => {
                      var closest = el.closest ("div[data-ai]");
                      if (closest) {
                        closest.remove ();
                      }
                    });
                  }, 50);
                }

                if (ai_debug) {
                  var db_record = db_records ['='];

                  if (typeof db_record != "undefined") {
                    if (typeof db_record == "string")
                      console.log ("AI CLICK " + block, code_version == 0 ? "" : "[" + code_version + "]", "(" + db_record + ")"); else
                        console.log ("AI CLICK " + block, code_version == 0 ? "" : "[" + code_version + "]", "(Views: " + db_record [4] + ", Clicks: " + db_record [5] + (click_type == "" ? "" : ", " + click_type) + ")");
                  }
                }
              } else if (ai_debug) console.log ("AI CLICK " + block, code_version == 0 ? "" : "[" + code_version + "]", "(NO DATA" + (click_type == "" ? "" : ", " + click_type) + ")");

              if (ai_debug) console.log ('');
          });
        } else {
            if (ai_debug) console.log ("AI CLICK INTERNAL TRACKING DISABLED");
          }
      }

      if (ai_external_tracking) {
        if (typeof ai_external_tracking_no_clicks === 'undefined') {
          var block_name         = data [2];
          var code_version_name  = data [3];
          var block_counter      = data [4];

          external_tracking ("click", block, block_name, block_counter, code_version, code_version_name, false);
        } else {
            if (ai_debug) console.log ("AI CLICK EXTERNAL TRACKING DISABLED");
          }
      }

      if (typeof ai_click_action == 'function') {
        if (ai_debug) console.log ('AI CLICK ai_click_action (' + block + ') CALLED');

        ai_click_action (block, block_name, code_version, code_version_name);
      }
    }
  }

  ai_install_standard_click_trackers = function (block_wrapper) {
    var ai_debug = typeof ai_debugging !== 'undefined'; // 3
//    var ai_debug = false;

    if (typeof block_wrapper == 'undefined') {
      // ***
//      block_wrapper = $('body');
      block_wrapper = document.querySelector ('body');
    }

//    var elements = $("div.ai-track[data-ai]:visible a", block_wrapper);
    // ***
//    var elements = $("div.ai-track[data-ai]:visible", block_wrapper);
    var elements = block_wrapper.querySelectorAll ("div.ai-track[data-ai]");

    // ***
//    var filtered_elements = $();
    var filtered_elements = [];
    // ***
//    elements.each (function () {
    elements.forEach ((element, i) => {
      if (!!(element.offsetWidth || element.offsetHeight || element.getClientRects ().length)) {
      // ### Excludes element also when class is found in rotation option
//      var ai_lazy_loading = $(this).find ('div.ai-lazy');
//      var ai_manual_loading = $(this).find ('div.ai-manual');
//      var ai_manual_loading_list = $(this).find ('div.ai-list-manual');
//      var ai_manual_loading_auto = $(this).find ('div.ai-manual-auto');
//      if (ai_lazy_loading.length == 0 && ai_manual_loading.length == 0 && ai_manual_loading_list.length == 0 && ai_manual_loading_auto.length == 0) filtered_elements = filtered_elements.add ($(this));

      // ***
//      if ($(this).find ('div.ai-lazy, div.ai-manual, div.ai-list-manual, div.ai-manual-auto, div.ai-delayed').length == 0) filtered_elements = filtered_elements.add ($(this));
      if (!element.querySelectorAll ('div.ai-lazy, div.ai-manual, div.ai-list-manual, div.ai-manual-auto, div.ai-delayed').length) filtered_elements.push (element);
      // ***
      }
    });

    elements = filtered_elements;


    // Mark as tracked
    // ***
//    elements.removeClass ('ai-track');
    // ***
//    elements = elements.find ('a');
    var processed_elements = [];
    elements.forEach ((element, i) => {
      element.classList.remove ('ai-track');
      processed_elements.push.apply (processed_elements, element.querySelectorAll ('a'));
    });

    // ***
    elements = processed_elements;

    if (elements.length != 0) {
      if (ai_advanced_click_detection) {
        // ***
//        elements.click (function () {
        elements.forEach ((element, i) => {
          element.addEventListener ('click', () => {
            // ***
  //          var wrapper = $(this).closest ("div[data-ai]");
            var wrapper = element.closest ("div[data-ai]");
            // ***
  //          while (typeof wrapper.attr ("data-ai") != "undefined") {
            while (wrapper !== null && wrapper.hasAttribute ("data-ai")) {
              // ***
  //            var data = JSON.parse (b64d (wrapper.attr ("data-ai")));
              var data = JSON.parse (b64d (wrapper.getAttribute ("data-ai")));
              if (typeof data !== "undefined" && data.constructor === Array) {
                if (Number.isInteger (data [1])) {
                  // ***
  //                if (!wrapper.hasClass ("clicked")) {
                  if (!wrapper.classList.contains ("clicked")) {
                    // ***
  //                  wrapper.addClass ("clicked");
                    wrapper.classList.add ("clicked");
                    ai_click (data, "a.click");
                  }
                }
              }
              // ***
  //            wrapper = wrapper.parent ().closest ("div[data-ai]");
              wrapper = wrapper.parentElement.closest ("div[data-ai]");
            }
          });
        // ***
        });

        if (ai_debug) {
          // ***
//          elements.each (function (){
          elements.forEach ((element, i) => {
            // ***
//            var wrapper = $(this).closest ("div[data-ai]");
            var wrapper = element.closest ("div[data-ai]");
            // ***
//            if (typeof wrapper.attr ("data-ai") != "undefined") {
            if (wrapper !== null && wrapper.hasAttribute ("data-ai")) {
              // ***
//              var data = JSON.parse (b64d (wrapper.data ("ai")));
              var data = JSON.parse (b64d (wrapper.dataset.ai));
              if (typeof data !== "undefined" && data.constructor === Array) {
                if (Number.isInteger (data [1])) {
                  // ***
//                  if (!wrapper.hasClass ("clicked")) {
                  if (!wrapper.classList.contains ("clicked")) {
                    console.log ("AI STANDARD CLICK TRACKER for link installed on block", data [0]);
                  } else console.log ("AI STANDARD CLICK TRACKER for link NOT installed on block", data [0], "- has class clicked");
                } else console.log ("AI STANDARD CLICK TRACKER for link NOT installed on block", data [0], "- version not set");

              }
            }
          });
        }
      } else {
          // ***
//          elements.click (function () {
          elements.forEach ((element, i) => {
            element.addEventListener ('click', () => {
              // ***
  //            var wrapper = $(this).closest ("div[data-ai]");
              var wrapper = element.closest ("div[data-ai]");
              // ***
  //            while (typeof wrapper.attr ("data-ai") != "undefined") {
              while (wrapper !== null && wrapper.hasAttribute ("data-ai")) {
                // ***
  //              var data = JSON.parse (b64d (wrapper.attr ("data-ai")));
                var data = JSON.parse (b64d (wrapper.getAttribute ("data-ai")));
                if (typeof data !== "undefined" && data.constructor === Array) {
                  if (Number.isInteger (data [1])) {
                    ai_click (data, "a.click");
                    clicked = true;
                  }
                }
                // ***
  //              wrapper = wrapper.parent ().closest ("div[data-ai]");
                wrapper = wrapper.parentElement.closest ("div[data-ai]");
              }
            });
            // ***
          });

          if (ai_debug) {
            // ***
//            elements.each (function (){
            elements.forEach ((element, i) => {
              // ***
//              var wrapper = $(this).closest ("div[data-ai]");
              var wrapper = element.closest ("div[data-ai]");
              // ***
//              if (typeof wrapper.attr ("data-ai") != "undefined") {
              if (wrapper !== null && wrapper.hasAttribute ("data-ai")) {
                // ***
//                var data = JSON.parse (b64d (wrapper.attr ("data-ai")));
                var data = JSON.parse (b64d (wrapper.getAttribute ("data-ai")));

                if (typeof data !== "undefined" && data.constructor === Array) {
                  if (Number.isInteger (data [1])) {
                    console.log ("AI STANDARD CLICK TRACKER installed on block", data [0]);
                  } else console.log ("AI STANDARD CLICK TRACKER NOT installed on block", data [0], "- version not set");

                }
              }
            });
          }
        }
    }
  }

  ai_install_click_trackers = function (block_wrapper) {

    var ai_debug = typeof ai_debugging !== 'undefined'; // 4
//    var ai_debug = false;

    if (typeof block_wrapper == 'undefined') {
      // ***
//      block_wrapper = $('body');
      block_wrapper = document.querySelector ('body');
      if (ai_debug) console.log ("AI INSTALL CLICK TRACKERS");
    // ***
//    }  else if (ai_debug) console.log ("AI INSTALL CLICK TRACKERS:", block_wrapper.prop ("tagName"), block_wrapper.attr ('class'));
    }  else if (ai_debug) console.log ("AI INSTALL CLICK TRACKERS:", block_wrapper.tagName, block_wrapper.getAttribute ('class'));


    if (ai_advanced_click_detection) {
                                                       // timed rotation options that may contain blocks for tracking (block shortcodes) - only currently active option is visible
      // ***
//      var elements = $("div.ai-track[data-ai]:visible, div.ai-rotate[data-info]:visible div.ai-track[data-ai]", block_wrapper);
      var elements = block_wrapper.querySelectorAll ("div.ai-track[data-ai], div.ai-rotate[data-info] div.ai-track[data-ai]");

      var all_elements = [];
      elements.forEach ((element, i) => {
        // Install iframe click tracker only for visible blocks
        if (!!(element.offsetWidth || element.offsetHeight || element.getClientRects ().length)) {
          all_elements.push (element);
        }
      });

      // ***
//      if (typeof block_wrapper.attr ("data-ai") != "undefined" && $(block_wrapper).hasClass ('ai-track') && $(block_wrapper).is (':visible')) {
      if (block_wrapper.hasAttribute ("data-ai") && block_wrapper.classList.contains ('ai-track') && !!(block_wrapper.offsetWidth || block_wrapper.offsetHeight || block_wrapper.getClientRects ().length)) {
        // ***
//        elements = elements.add (block_wrapper);
        all_elements.push (block_wrapper);
      }

      // ***
//      var filtered_elements = $();
      var filtered_elements = [];
//      elements.each (function () {
      all_elements.forEach ((element, i) => {

        // ### Excludes element also when class is found in rotation option
//        var ai_lazy_loading = $(this).find ('div.ai-lazy');
//        var ai_manual_loading = $(this).find ('div.ai-manual');
//        var ai_manual_loading_auto = $(this).find ('div.ai-manual-auto');
//        if (ai_lazy_loading.length == 0 && ai_manual_loading.length == 0 && ai_manual_loading_auto.length == 0) filtered_elements = filtered_elements.add ($(this));

        // ***
//        if ($(this).find ('div.ai-lazy, div.ai-manual, div.ai-list-manual, div.ai-manual-auto, div.ai-delayed').length == 0) filtered_elements = filtered_elements.add ($(this));
        if (!element.querySelectorAll ('div.ai-lazy, div.ai-manual, div.ai-list-manual, div.ai-manual-auto, div.ai-delayed').length) filtered_elements.push (element);
      });

      elements = filtered_elements;

    // Mark as tracked - prevents ai_install_standard_click_trackers
      // ***
//      elements.removeClass ('ai-track');

//      var processed_elements = [];
//      elements.forEach ((element, i) => {
//        element.classList.remove ('ai-track');
//        processed_elements.push (processed_elements);
//      });
//      elements = processed_elements;

    // Will be marked in ai_install_standard_click_trackers

      if (elements.length != 0) {
        // ***
//        elements.iframeTracker ({
        elements.forEach ((element, i) => {
          installIframeTracker ({

          blurCallback: function(){
            if (this.ai_data != null && wrapper != null) {
              if (ai_debug) console.log ("AI blurCallback for block: " + this.ai_data [0]);
              // ***
//              if (!wrapper.hasClass ("clicked")) {
              if (!wrapper.classList.contains ("clicked")) {
                // ***
//                wrapper.addClass ("clicked");
                wrapper.classList.add ("clicked");
                ai_click (this.ai_data, "blurCallback");

                // ***
//                var inner_wrapper = wrapper.find ("div[data-ai]:visible");
                var inner_wrapper = wrapper.querySelector ("div[data-ai]");
                // ***
//                while (typeof inner_wrapper.attr ("data-ai") != "undefined") {
                while (inner_wrapper != null && !!(inner_wrapper.offsetWidth || inner_wrapper.offsetHeight || inner_wrapper.getClientRects ().length) && inner_wrapper.hasAttribute ("data-ai")) {
                  // ***
//                  var data = JSON.parse (b64d (inner_wrapper.attr ("data-ai")));
                  var data = JSON.parse (b64d (inner_wrapper.getAttribute ("data-ai")));
                  if (typeof data !== "undefined" && data.constructor === Array && Number.isInteger (data [1])) {
                    ai_click (data, "blurCallback INNER");
                  }
                  // ***
//                  inner_wrapper = inner_wrapper.find ("div[data-ai]:visible");
                  inner_wrapper = inner_wrapper.querySelector ("div[data-ai]");
                }
              }
            }
          },
          overCallback: function(element){
            // ***
//            var closest = $(element).closest ("div[data-ai]");
            var closest = element.closest ("div[data-ai]");
            // ***
//            if (typeof closest.attr ("data-ai") != "undefined") {
            if (closest.hasAttribute ("data-ai")) {
              // ***
//              var data = JSON.parse (b64d (closest.attr ("data-ai")));
              var data = JSON.parse (b64d (closest.getAttribute ("data-ai")));
              if (typeof data !== "undefined" && data.constructor === Array && Number.isInteger (data [1])) {
                wrapper = closest;
                this.ai_data = data;
                if (ai_debug) console.log ("AI overCallback for block: " + this.ai_data [0]);
              } else {
                  // ***
//                  if (wrapper != null) wrapper.removeClass ("clicked");
                  if (wrapper != null) wrapper.classList.remove ("clicked");
                  wrapper        = null;
                  this.ai_data  = null;
                }
            }
          },
          outCallback: function (element){
            if (ai_debug && this.ai_data != null) console.log ("AI outCallback for block: " + this.ai_data [0]);
            // ***
//            if (wrapper != null) wrapper.removeClass ("clicked");
            if (wrapper != null) wrapper.classList.remove ("clicked");
            wrapper = null;
            this.ai_data = null;
          },
          focusCallback: function(element){
            if (this.ai_data != null && wrapper != null) {
              if (ai_debug) console.log ("AI focusCallback for block: " + this.ai_data [0]);
              // ***
//              if (!wrapper.hasClass ("clicked")) {
              if (!wrapper.classList.contains ("clicked")) {
                // ***
//                wrapper.addClass ("clicked");
                wrapper.classList.add ("clicked");
                ai_click (this.ai_data, "focusCallback");

//                var inner_wrapper = wrapper.find ("div[data-ai]:visible");
                var inner_wrapper = wrapper.querySelector ("div[data-ai]");

                // ***
//                while (typeof inner_wrapper.attr ("data-ai") != "undefined") {
                while (inner_wrapper != null && !!(inner_wrapper.offsetWidth || inner_wrapper.offsetHeight || inner_wrapper.getClientRects ().length) && inner_wrapper.hasAttribute ("data-ai")) {
                  // ***
//                  var data = JSON.parse (b64d (inner_wrapper.attr ("data-ai")));
                  var data = JSON.parse (b64d (inner_wrapper.getAttribute ("data-ai")));
                  if (typeof data !== "undefined" && data.constructor === Array && Number.isInteger (data [1])) {
                    ai_click (data, "focusCallback INNER");
                  }
                  // ***
//                  inner_wrapper = inner_wrapper.find ("div[data-ai]:visible");
                  inner_wrapper = inner_wrapper.querySelector ("div[data-ai]");
                }
              }
            }
          },
          wrapper:  null,
          ai_data: null,
          block:   null,
          version: null
        // ***
//        });
        }
        , element
        );
        // ***
        });

        if (ai_debug) {
          // ***
//          elements.each (function (){
          elements.forEach ((element, i) => {
            // ***
//            var closest = $(this).closest ("div[data-ai]");
            var closest = element.closest ("div[data-ai]");
            // ***
//            if (typeof closest.attr ("data-ai") != "undefined") {
            if (closest.hasAttribute ("data-ai")) {
            // ***
//              var data = JSON.parse (b64d (closest.attr ("data-ai")));
              var data = JSON.parse (b64d (closest.getAttribute ("data-ai")));
              if (typeof data !== "undefined" && data.constructor === Array) {
                console.log ("AI ADVANCED CLICK TRACKER installed on block", data [0]);
              }
            }
          });
        }
      }
    }


    ai_install_standard_click_trackers (block_wrapper);
  }

  var pageview_data = [];

  ai_process_impressions = function (block_wrapper) {

    var ai_debug = typeof ai_debugging !== 'undefined'; // 5
//    var ai_debug = false;

    if (typeof block_wrapper == 'undefined') {
      // ***
//      block_wrapper = $('body');
      block_wrapper = document.querySelector ('body');
      if (ai_debug) console.log ("AI PROCESS IMPRESSIONS");
    // ***
//    }  else if (ai_debug) console.log ("AI PROCESS IMPRESSIONS:", block_wrapper.prop ("tagName"), block_wrapper.attr ('class'));
    }  else if (ai_debug) console.log ("AI PROCESS IMPRESSIONS:", block_wrapper.tagName, block_wrapper.getAttribute ('class'));

    var blocks = [];
    var versions = [];
    var block_names = [];
    var version_names = [];
    var block_counters = [];

    if (pageview_data.length != 0) {
      if (ai_debug) console.log ('AI PROCESS IMPRESSIONS - SENDING ALSO PAGEVIEW DATA', pageview_data);

      blocks.push          (pageview_data [0]);
      versions.push        (pageview_data [1]);
      block_names.push     ('Pageviews');
      block_counters.push  (0);
      version_names.push   ('');
    }

                                                                // timed rotation options that may contain blocks for tracking (block shortcodes) - only currently active option is visible
    // ***
//    var blocks_for_tracking = $("div.ai-track[data-ai]:visible, div.ai-rotate[data-info]:visible div.ai-track[data-ai]", block_wrapper);
    var blocks_for_tracking = block_wrapper.querySelectorAll ("div.ai-track[data-ai], div.ai-rotate[data-info] div.ai-track[data-ai]");
    var visible_elements = [];
    blocks_for_tracking.forEach ((element, i) => {
      if (!!(element.offsetWidth || element.offsetHeight || element.getClientRects ().length)) {
        visible_elements.push (element);
      }
    });

    // ***
//    if (typeof $(block_wrapper).attr ("data-ai") != "undefined" && $(block_wrapper).hasClass ('ai-track') && $(block_wrapper).is (':visible')) {
    if (block_wrapper !== null && block_wrapper.hasAttribute ("data-ai") && block_wrapper.classList.contains ('ai-track') && !!(block_wrapper.offsetWidth || block_wrapper.offsetHeight || block_wrapper.getClientRects ().length)) {
      visible_elements.push (block_wrapper);
    }
    blocks_for_tracking = visible_elements;;

    // ***
//    if (ai_debug) console.log ("AI BLOCKS FOR TRACKING:", blocks_for_tracking.each (function () {return $(this).attr ('class')}).get ());
    if (ai_debug) {
      console.log ("AI BLOCKS FOR TRACKING:");
      blocks_for_tracking.forEach ((element, i) => {console.log ('  ', element.getAttribute ('class'))});
    }

    if (blocks_for_tracking.length != 0) {
      if (ai_debug) console.log ("");

      // ***
//      $(blocks_for_tracking).each (function (){
      blocks_for_tracking.forEach ((element, i) => {

        // ***
//        if (typeof $(this).attr ("data-ai") != "undefined") {
        if (element.hasAttribute ("data-ai")) {
          // ***
//          var data = JSON.parse (b64d ($(this).attr ("data-ai")));
          var data = JSON.parse (b64d (element.getAttribute ("data-ai")));

          if (typeof data !== "undefined" && data.constructor === Array) {
            if (ai_debug) console.log ("AI TRACKING DATA:", data);

            var timed_rotation_count = 0;
            // ***
//            var ai_rotation_info = $(this).find ('div.ai-rotate[data-info]');
            var ai_rotation_info = element.querySelectorAll ('div.ai-rotate[data-info]');
            if (ai_rotation_info.length == 1) {
              // ***
//              var block_rotation_info = JSON.parse (b64d (ai_rotation_info.data ('info')));
              var block_rotation_info = JSON.parse (b64d (ai_rotation_info [0].dataset.info));

              if (ai_debug) console.log ("AI TIMED ROTATION DATA:", block_rotation_info);

              timed_rotation_count = block_rotation_info [1];
            }

            if (Number.isInteger (data [0]) && data [0] != 0) {
              if (Number.isInteger (data [1])) {

                var adb_flag = 0;
                // Deprecated
                // ***
//                var no_tracking = $(this).hasClass ('ai-no-tracking');
                var no_tracking = element.classList.contains ('ai-no-tracking');

                // ***
//                var ai_masking_data = jQuery(b64d ("Ym9keQ==")).attr (AI_ADB_ATTR_NAME);
                var ai_masking_data = document.querySelector (b64d ("Ym9keQ==")).getAttribute (ai_adb_attribute);
                if (typeof ai_masking_data === "string") {
                  var ai_masking = ai_masking_data == b64d ("bWFzaw==");
                }

                if (typeof ai_masking_data === "string" && typeof ai_masking === "boolean") {
                  // ***
//                  var outer_height = $(this).outerHeight ();
                  var outer_height = element.offsetHeight;

                  // ***
//                  var ai_attributes = $(this).find ('.ai-attributes');
                  var ai_attributes = element.querySelectorAll ('.ai-attributes');
                  if (ai_attributes.length) {
//                    ai_attributes.each (function (){
                    // ***
                    ai_attributes.forEach ((el, i) => {
                      // ***
//                      if (outer_height >= $(this).outerHeight ()) {
                      if (outer_height >= element.offsetHeight) {
                        // ***
//                        outer_height -= $(this).outerHeight ();
                        outer_height -= element.offsetHeight;
                      }
                    });
                  }

                  // ***
//                  var ai_code = $(this).find ('.ai-code');
                  var ai_code = element.querySelectorAll ('.ai-code');
                  outer_height = 0;
                  if (ai_code.length) {
                    // ***
//                    ai_code.each (function (){
                    ai_code.forEach ((element, i) => {
                      // ***
//                      outer_height += $(this).outerHeight ();
                      outer_height += element.offsetHeight;
                    });
                  }

  //                no_tracking = $(this).hasClass ('ai-no-tracking');
                  // ***
//                  if (ai_debug) console.log ('AI ad blocking:', ai_masking, " outerHeight:", outer_height, 'no tracking:', no_tracking);
                  if (ai_debug) console.log ('AI ad blocking:', ai_masking, " offsetHeight:", outer_height, 'no tracking:', no_tracking);
                  if (ai_masking && outer_height === 0) {
                    adb_flag = 0x80;
                  }
                }

//                var ai_lazy_loading = $(this).find ('div.ai-lazy');
//                var ai_manual_loading = $(this).find ('div.ai-manual');
//                var ai_manual_loading_list = $(this).find ('div.ai-list-manual');
//                var ai_manual_loading_auto = $(this).find ('div.ai-manual-auto');

//                if (ai_lazy_loading.length != 0 || ai_manual_loading.length != 0 || ai_manual_loading_list.length != 0 || ai_manual_loading_auto.length != 0) {

                // ***
//                if ($(this).find ('div.ai-lazy, div.ai-manual, div.ai-list-manual, div.ai-manual-auto, div.ai-delayed').length != 0) {
                if (element.querySelectorAll ('div.ai-lazy, div.ai-manual, div.ai-list-manual, div.ai-manual-auto, div.ai-delayed').length != 0) {
                  no_tracking = true;

                  if (ai_debug) {
                    // ***
//                    if ($(this).find ('div.ai-lazy').length   != 0) console.log ("AI TRACKING block", data [0], "is set for lazy loading");
//                    if ($(this).find ('div.ai-manual').length != 0) console.log ("AI TRACKING block", data [0], "is set for manual loading");
//                    if ($(this).find ('div.ai-list-manual').length != 0) console.log ("AI TRACKING block", data [0], "is set for manual loading AUTO list");
//                    if ($(this).find ('div.ai-manual-auto').length != 0) console.log ("AI TRACKING block", data [0], "is set for manual loading AUTO");
//                    if ($(this).find ('div.ai-delayed').length != 0) console.log ("AI TRACKING block", data [0], "is set for delayed loading");

                    if (element.querySelectorAll ('div.ai-lazy').length   != 0) console.log ("AI TRACKING block", data [0], "is set for lazy loading");
                    if (element.querySelectorAll ('div.ai-manual').length != 0) console.log ("AI TRACKING block", data [0], "is set for manual loading");
                    if (element.querySelectorAll ('div.ai-list-manual').length != 0) console.log ("AI TRACKING block", data [0], "is set for manual loading AUTO list");
                    if (element.querySelectorAll ('div.ai-manual-auto').length != 0) console.log ("AI TRACKING block", data [0], "is set for manual loading AUTO");
                    if (element.querySelectorAll ('div.ai-delayed').length != 0) console.log ("AI TRACKING block", data [0], "is set for delayed loading");
                  }
                }

                if (!no_tracking) {
                  if (timed_rotation_count == 0) {
                    blocks.push (data [0]);
                    versions.push (data [1] | adb_flag);
                    block_names.push (data [2]);
                    version_names.push (data [3]);
                    block_counters.push (data [4]);
                  } else {
                      // Timed rotation
                      for (var option = 1; option <= timed_rotation_count; option ++) {
                        blocks.push (data [0]);
                        versions.push (option | adb_flag);
                        block_names.push (data [2]);
                        version_names.push (data [3]);
                        block_counters.push (data [4]);
                      }
                    }

                } else if (ai_debug) console.log ("AI TRACKING block", data [0], "DISABLED");

              // ***
//              } else if (ai_debug) console.log ("AI TRACKING block", data [0], "- version not set", $(this).find ('div.ai-lazy').length != 0 ? 'LAZY LOADING' : '', ($(this).find ('div.ai-manual').length + $(this).find ('div.ai-list-manual').length + $(this).find ('div.ai-manual-auto').length) != 0 ? 'MANUAL LOADING' : '');
              } else if (ai_debug) console.log ("AI TRACKING block", data [0], "- version not set", element.querySelectorAll ('div.ai-lazy').length != 0 ? 'LAZY LOADING' : '', (element.querySelectorAll ('div.ai-manual').length + element.querySelectorAll ('div.ai-list-manual').length + element.querySelectorAll ('div.ai-manual-auto').length) != 0 ? 'MANUAL LOADING' : '');
            } else if (ai_debug) console.log ("AI TRACKING DISABLED");
          }
        }
      });
    }

    if (ai_debug) console.log ('AI CHECK IMPRESSIONS blocks', blocks);
    if (ai_debug) console.log ('AI CHECK IMPRESSIONS data', ai_check_data);

    ai_cookie = ai_load_cookie ();

    for (var cookie_block in ai_cookie) {

      if (!blocks.includes (parseInt (cookie_block))) continue;

      for (var cookie_block_property in ai_cookie [cookie_block]) {
        if (cookie_block_property == 'i') {
          if (ai_debug) console.log ('AI CHECK IMPRESSIONS block:', cookie_block);

          var impressions = ai_cookie [cookie_block][cookie_block_property];
          if (impressions > 0) {
            if (ai_debug) console.log ('AI IMPRESSION, block', cookie_block, 'remaining', impressions - 1, 'impressions');

            if (impressions == 1) {
              var date = new Date();
                var closed_until = Math.round (date.getTime() / 1000) + 7 * 24 * 3600;
//              // TEST
//              var closed_until = Math.round (date.getTime() / 1000) + 36;
              ai_set_cookie (cookie_block, 'i', - closed_until);
            } else ai_set_cookie (cookie_block, 'i', impressions - 1);
          }
        } else

        if (cookie_block_property == 'ipt') {
          if (ai_debug) console.log ('AI CHECK IMPRESSIONS PER TIME PERIOD block:', cookie_block);

          var impressions = ai_cookie [cookie_block][cookie_block_property];
          if (impressions > 0) {
            if (ai_debug) console.log ('AI IMPRESSIONS, block', cookie_block, 'remaining', impressions - 1, 'impressions per time period');

            ai_set_cookie (cookie_block, 'ipt', impressions - 1);
          } else {
              if (ai_check_data.hasOwnProperty (cookie_block) && ai_check_data [cookie_block].hasOwnProperty ('ipt') && ai_check_data [cookie_block].hasOwnProperty ('it')) {
                if (ai_cookie.hasOwnProperty (cookie_block) && ai_cookie [cookie_block].hasOwnProperty ('it')) {
                  var date = new Date();
                  var closed_for = ai_cookie [cookie_block]['it'] - Math.round (date.getTime() / 1000);
                  if (closed_for <= 0) {
                    if (ai_debug) console.log ('AI IMPRESSIONS, block', cookie_block, 'set max impressions period (' + ai_check_data [cookie_block]['it'], 'days =', ai_check_data [cookie_block]['it'] * 24 * 3600, 's)');

                    var timestamp = Math.round (date.getTime() / 1000);

                    ai_set_cookie (cookie_block, 'ipt', ai_check_data [cookie_block]['ipt']);
                    ai_set_cookie (cookie_block, 'it', Math.round (timestamp + ai_check_data [cookie_block]['it'] * 24 * 3600));
                  }
                }
              } else {
                  if (ai_cookie.hasOwnProperty (cookie_block) && ai_cookie [cookie_block].hasOwnProperty ('ipt')) {
                    if (ai_debug) console.log ('AI IMPRESSIONS, block', cookie_block, 'removing ipt');

                    ai_set_cookie (cookie_block, 'ipt', '');
                  }
                  if (ai_cookie.hasOwnProperty (cookie_block) && ai_cookie [cookie_block].hasOwnProperty ('it')) {
                    if (ai_debug) console.log ('AI IMPRESSIONS, block', cookie_block, 'removing it');

                    ai_set_cookie (cookie_block, 'it', '');
                  }
                }
            }
        }
      }
    }

    if (blocks.length) {
      if (ai_debug) {
        console.log ("AI IMPRESSION blocks:", blocks);
        console.log ("            versions:", versions);
      }

      if (ai_internal_tracking) {
        if (typeof ai_internal_tracking_no_impressions === 'undefined') {

          // Mark as sent
          pageview_data = [];

          // ***
//          $.ajax ({
//              url: ai_ajax_url,
//              type: "post",
//              data: {
//                action: "ai_ajax",
//                ai_check: ai_data_id,
//                views: blocks,
//                versions: versions,
//              },
//              async: true
//          }).done (function (data) {

          var url_data = {
            action: "ai_ajax",
            ai_check: ai_data_id,
          };

          var formBody = [];
          for (var property in url_data) {
            var encodedKey = encodeURIComponent (property);
            var encodedValue = encodeURIComponent (url_data [property]);
            formBody.push (encodedKey + "=" + encodedValue);
          }

          for (var index in blocks) {
            var encodedKey = encodeURIComponent ('views[]');
            var encodedValue = encodeURIComponent (blocks [index]);
            formBody.push (encodedKey + "=" + encodedValue);
          }

          for (var index in versions) {
            var encodedKey = encodeURIComponent ('versions[]');
            var encodedValue = encodeURIComponent (versions [index]);
            formBody.push (encodedKey + "=" + encodedValue);
          }

          formBody = formBody.join ("&");

          async function ai_post_views () {
            const response = await fetch (ai_ajax_url, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
              },
              body: formBody
            });

            const text = await response.text ();
            return text;
          }

          ai_post_views ().then (data => {

              data = data.trim ();
              if (data != "") {
                var db_records = JSON.parse (data);

                if (ai_debug) console.log ("AI DB RECORDS: ", db_records);

                if (typeof db_records ['#'] != 'undefined') {
                  // Reload cookie data
                  ai_cookie = ai_load_cookie ();

                  var date = new Date();
                  var closed_until = Math.round (date.getTime() / 1000) + 12 * 3600;

                  var blocks_to_remove = new Array();
                  for (var limited_block_index in db_records ['#']) {
                    if (ai_debug) console.log ("AI SERVERSIDE LIMITED BLOCK:", db_records ['#'][limited_block_index]);

                    // Not needed as they will remain closed from the next page load
//                    blocks_to_remove.push (db_records ['#'][limited_block_index]);

                    if (!ai_cookie.hasOwnProperty (db_records ['#'][limited_block_index]) || !ai_cookie [db_records ['#'][limited_block_index]].hasOwnProperty ('x')) {
                      if (ai_debug) console.log ("AI SERVERSIDE LIMITED BLOCK:", db_records ['#'][limited_block_index], ' not closed - closing for 12 hours');

                      ai_set_cookie (db_records ['#'][limited_block_index], 'x', closed_until);
                    }
                  }

                  setTimeout (function () {
                    for (index = 0; index < blocks_to_remove.length; ++index) {
                      // ***
//                      $('span[data-ai-block=' + blocks_to_remove [index] + ']').closest ("div[data-ai]").remove ();
                      document.querySelectorAll ('span[data-ai-block="' + blocks_to_remove [index] + '"]').forEach ((el, index) => {
                        var closest = el.closest ("div[data-ai]");
                        if (closest) {
                          closest.remove ();
                        }
                      });
                    }
                  }, 50);
                }

                if (ai_debug) console.log ('');
              }

          });
        } else {
            if (ai_debug) console.log ("AI PROCESS IMPRESSIONS INTERNAL TRACKING DISABLED");
          }
      }

      if (ai_external_tracking) {
        if (typeof ai_external_tracking_no_impressions === 'undefined') {
          for (var i = 0; i < blocks.length; i++) {
            // Skip pageview data
            if (blocks [i] != 0) {
              external_tracking ("impression", blocks [i],  block_names [i], block_counters [i], versions [i], version_names [i], true);
            }
          }
        } else {
            if (ai_debug) console.log ("AI PROCESS IMPRESSIONS EXTERNAL TRACKING DISABLED");
          }
      }
    }
  }

  function ai_process_pageview_checks () {

    var ai_debug = typeof ai_debugging !== 'undefined'; // 6
//    var ai_debug = false;

    ai_check_data = {};

    if (typeof ai_iframe != 'undefined') return;

    if (ai_debug) console.log ('AI PROCESS PAGEVIEW CHECKS');

    ai_cookie = ai_load_cookie ();

    // ***
//    $('.ai-check-block').each (function () {
    document.querySelectorAll ('.ai-check-block').forEach ((element, i) => {

      // ***
//      var block = $(this).data ('ai-block');
//      var delay_pv = $(this).data ('ai-delay-pv');
//      var every_pv = $(this).data ('ai-every-pv');

//      var code_hash             = $(this).data ('ai-hash');
//      var max_imp               = $(this).data ('ai-max-imp');
//      var limit_imp_per_time    = $(this).data ('ai-limit-imp-per-time');
//      var limit_imp_time        = $(this).data ('ai-limit-imp-time');
//      var max_clicks            = $(this).data ('ai-max-clicks');
//      var limit_clicks_per_time = $(this).data ('ai-limit-clicks-per-time');
//      var limit_clicks_time     = $(this).data ('ai-limit-clicks-time');

//      var global_limit_clicks_per_time = $(this).data ('ai-global-limit-clicks-per-time');
//      var global_limit_clicks_time     = $(this).data ('ai-global-limit-clicks-time');


      var block = element.dataset.aiBlock;
      var delay_pv = element.dataset.aiDelayPv;
      var every_pv = element.dataset.aiEveryPv;

      var code_hash             = element.dataset.aiHash;
      var max_imp               = element.dataset.aiMaxImp;
      var limit_imp_per_time    = element.dataset.aiLimitImpPerTime;
      var limit_imp_time        = element.dataset.aiLimitImpTime;
      var max_clicks            = element.dataset.aiMaxClicks;
      var limit_clicks_per_time = element.dataset.aiLimitClicksPerTime;
      var limit_clicks_time     = element.dataset.aiLimitClicksTime;


      var global_limit_clicks_per_time = element.dataset.aiGlobalLimitClicksPerTime;
      var global_limit_clicks_time     = element.dataset.aiGlobalLimitClicksTime;

      if (ai_debug) console.log ('AI CHECK INITIAL DATA, block:', block);

      if (typeof delay_pv != 'undefined' && delay_pv > 0) {
        if (!ai_check_data.hasOwnProperty (block)) {
          ai_check_data [block] = {};
        }
        ai_check_data [block]['d'] = delay_pv;

        var cookie_delay_pv = '';
        if (ai_cookie.hasOwnProperty (block)) {
          if (ai_cookie [block].hasOwnProperty ('d')) {
            cookie_delay_pv = ai_cookie [block]['d'];
          }
        }

        if (cookie_delay_pv === '') {
          if (ai_debug) console.log ('AI CHECK PAGEVIEWS, block:', block, 'delay:', delay_pv);

          ai_set_cookie (block, 'd', delay_pv - 1);
        }
      }

      if (typeof every_pv != 'undefined' && every_pv >= 2) {
        if (!ai_check_data.hasOwnProperty (block)) {
          ai_check_data [block] = {};
        }

        if (typeof ai_delay_showing_pageviews === 'undefined' && (!ai_cookie.hasOwnProperty (block) || !ai_cookie [block].hasOwnProperty ('d'))) {
          // Set d to process e
          if (!ai_cookie.hasOwnProperty (block)) {
            ai_cookie [block] = {};
          }
          ai_cookie [block]['d'] = 0;
        }

        ai_check_data [block]['e'] = every_pv;
      }

      if (typeof max_imp != 'undefined' && max_imp > 0) {
        if (!ai_check_data.hasOwnProperty (block)) {
          ai_check_data [block] = {};
        }
        ai_check_data [block]['i'] = max_imp;
        ai_check_data [block]['h'] = code_hash;

        var cookie_code_hash = '';
        var cookie_max_imp = '';
        if (ai_cookie.hasOwnProperty (block)) {
          if (ai_cookie [block].hasOwnProperty ('i')) {
            cookie_max_imp = ai_cookie [block]['i'];
          }
          if (ai_cookie [block].hasOwnProperty ('h')) {
            cookie_code_hash = ai_cookie [block]['h'];
          }
        }

        if (cookie_max_imp === '' || cookie_code_hash != code_hash) {
          if (ai_debug) console.log ('AI CHECK IMPRESSIONS, block:', block, 'max', max_imp, 'impressions', 'hash', code_hash);

          ai_set_cookie (block, 'i', max_imp);
          ai_set_cookie (block, 'h', code_hash);
        }
      } else {
          if (ai_cookie.hasOwnProperty (block) && ai_cookie [block].hasOwnProperty ('i')) {
            if (ai_debug) console.log ('AI IMPRESSIONS, block', block, 'removing i');

            ai_set_cookie (block, 'i', '');
            if (!ai_cookie [block].hasOwnProperty ('c') && !ai_cookie [block].hasOwnProperty ('x')) {
              ai_set_cookie (block, 'h', '');
            }
          }
        }

      if (typeof limit_imp_per_time != 'undefined' && limit_imp_per_time > 0 && typeof limit_imp_time != 'undefined' && limit_imp_time > 0) {
        if (!ai_check_data.hasOwnProperty (block)) {
          ai_check_data [block] = {};
        }
        ai_check_data [block]['ipt'] = limit_imp_per_time;
        ai_check_data [block]['it']  = limit_imp_time;

        var cookie_limit_imp_per_time = '';
        var cookie_limit_imp_time = '';
        if (ai_cookie.hasOwnProperty (block)) {
          if (ai_cookie [block].hasOwnProperty ('ipt')) {
            cookie_limit_imp_per_time = ai_cookie [block]['ipt'];
          }
          if (ai_cookie [block].hasOwnProperty ('it')) {
            cookie_limit_imp_time = ai_cookie [block]['it'];
          }
        }

        if (cookie_limit_imp_per_time === '' || cookie_limit_imp_time === '') {
          if (ai_debug) console.log ('AI CHECK IMPRESSIONS, block:', block, 'max', limit_imp_per_time, 'impresssions per', limit_imp_time, 'days (' + (limit_imp_time * 24 * 3600), 's)');

          ai_set_cookie (block, 'ipt', limit_imp_per_time);

          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);

          ai_set_cookie (block, 'it', Math.round (timestamp + limit_imp_time * 24 * 3600));
        }
        if (cookie_limit_imp_time > 0) {
          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);

          if (cookie_limit_imp_time <= timestamp) {
            if (ai_debug) console.log ('AI CHECK IMPRESSIONS, block:', block, 'reset max', limit_imp_per_time, 'impresssions per', limit_imp_time, 'days (' + (limit_imp_time * 24 * 3600), 's)');

            ai_set_cookie (block, 'ipt', limit_imp_per_time);
            ai_set_cookie (block, 'it', Math.round (timestamp + limit_imp_time * 24 * 3600));
          }
        }
      } else {
          if (ai_cookie.hasOwnProperty (block)) {
            if (ai_cookie [block].hasOwnProperty ('ipt')) ai_set_cookie (block, 'ipt', '');
            if (ai_cookie [block].hasOwnProperty ('it'))  ai_set_cookie (block, 'it',  '');
          }
        }

      if (typeof max_clicks != 'undefined' && max_clicks > 0) {
        if (!ai_check_data.hasOwnProperty (block)) {
          ai_check_data [block] = {};
        }
        ai_check_data [block]['c'] = max_clicks;
        ai_check_data [block]['h'] = code_hash;

        var cookie_code_hash = '';
        var cookie_max_clicks = '';
        if (ai_cookie.hasOwnProperty (block)) {
          if (ai_cookie [block].hasOwnProperty ('c')) {
            cookie_max_clicks = ai_cookie [block]['c'];
          }
          if (ai_cookie [block].hasOwnProperty ('h')) {
            cookie_code_hash = ai_cookie [block]['h'];
          }
        }

        if (cookie_max_clicks === '' || cookie_code_hash != code_hash) {
          if (ai_debug) console.log ('AI CHECK CLICKS, block:', block, 'max', max_clicks, 'clicks', 'hash', code_hash);

          ai_set_cookie (block, 'c', max_clicks);
          ai_set_cookie (block, 'h', code_hash);
        }
      } else {
          if (ai_cookie.hasOwnProperty (block) && ai_cookie [block].hasOwnProperty ('c')) {
            if (ai_debug) console.log ('AI CLICKS, block', block, 'removing c');

            ai_set_cookie (block, 'c', '');
            if (!ai_cookie [block].hasOwnProperty ('i') && !ai_cookie [block].hasOwnProperty ('x')) {
              ai_set_cookie (block, 'h', '');
            }
          }
        }

      if (typeof limit_clicks_per_time != 'undefined' && limit_clicks_per_time > 0 && typeof limit_clicks_time != 'undefined' && limit_clicks_time > 0) {
        if (!ai_check_data.hasOwnProperty (block)) {
          ai_check_data [block] = {};
        }
        ai_check_data [block]['cpt'] = limit_clicks_per_time;
        ai_check_data [block]['ct']  = limit_clicks_time;

        var cookie_limit_clicks_per_time = '';
        var cookie_limit_clicks_time = '';

        if (ai_cookie.hasOwnProperty (block)) {
          if (ai_cookie [block].hasOwnProperty ('cpt')) {
            cookie_limit_clicks_per_time = ai_cookie [block]['cpt'];
          }
          if (ai_cookie [block].hasOwnProperty ('ct')) {
            cookie_limit_clicks_time = ai_cookie [block]['ct'];
          }
        }

        if (cookie_limit_clicks_per_time === '' || cookie_limit_clicks_time === '') {
          if (ai_debug) console.log ('AI CHECK CLICKS, block:', block, 'max', limit_clicks_per_time, 'clicks per', limit_clicks_time, 'days (' + (limit_clicks_time * 24 * 3600), 's)');

          ai_set_cookie (block, 'cpt', limit_clicks_per_time);

          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);

          ai_set_cookie (block, 'ct', Math.round (timestamp + limit_clicks_time * 24 * 3600));
        }

        if (cookie_limit_clicks_time > 0) {
          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);

          if (cookie_limit_clicks_time <= timestamp) {
            if (ai_debug) console.log ('AI CHECK CLICKS, block:', block, 'reset max', limit_clicks_per_time, 'clicks per', limit_clicks_time, 'days (' + (limit_clicks_time * 24 * 3600), 's)');

            ai_set_cookie (block, 'cpt', limit_clicks_per_time);
            ai_set_cookie (block, 'ct', Math.round (timestamp + limit_clicks_time * 24 * 3600));
          }
        }
      } else {
          if (ai_cookie.hasOwnProperty (block)) {
            if (ai_cookie [block].hasOwnProperty ('cpt')) ai_set_cookie (block, 'cpt', '');
            if (ai_cookie [block].hasOwnProperty ('ct'))  ai_set_cookie (block, 'ct', '');
          }
        }

      if (typeof global_limit_clicks_per_time != 'undefined' && global_limit_clicks_per_time > 0 && typeof global_limit_clicks_time != 'undefined' && global_limit_clicks_time > 0) {
        if (!ai_check_data.hasOwnProperty ('G')) {
          ai_check_data ['G'] = {};
        }
        ai_check_data ['G']['cpt'] = global_limit_clicks_per_time;
        ai_check_data ['G']['ct']  = global_limit_clicks_time;

        var global_cookie_limit_clicks_per_time = '';
        var global_cookie_limit_clicks_time = '';

        if (ai_cookie.hasOwnProperty ('G')) {
          if (ai_cookie ['G'].hasOwnProperty ('cpt')) {
            global_cookie_limit_clicks_per_time = ai_cookie ['G']['cpt'];
          }
          if (ai_cookie ['G'].hasOwnProperty ('ct')) {
            global_cookie_limit_clicks_time = ai_cookie ['G']['ct'];
          }
        }

        if (global_cookie_limit_clicks_per_time === '' || global_cookie_limit_clicks_time === '') {
          if (ai_debug) console.log ('AI CHECK CLICKS GLOBAL: max', global_limit_clicks_per_time, 'clicks per', global_limit_clicks_time, 'days (' + (global_limit_clicks_time * 24 * 3600), 's)');

          ai_set_cookie ('G', 'cpt', global_limit_clicks_per_time);

          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);

          ai_set_cookie ('G', 'ct', Math.round (timestamp + global_limit_clicks_time * 24 * 3600));
        }

        if (global_cookie_limit_clicks_time > 0) {
          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);

          if (global_cookie_limit_clicks_time <= timestamp) {
            if (ai_debug) console.log ('AI CHECK CLICKS GLOBAL: reset max', global_limit_clicks_per_time, 'clicks per', global_limit_clicks_time, 'days (' + (global_limit_clicks_time * 24 * 3600), 's)');

            ai_set_cookie ('G', 'cpt', global_limit_clicks_per_time);
            ai_set_cookie ('G', 'ct', Math.round (timestamp + global_limit_clicks_time * 24 * 3600));
          }
        }
      } else {
          if (ai_cookie.hasOwnProperty ('G')) {
            if (ai_cookie ['G'].hasOwnProperty ('cpt')) ai_set_cookie ('G', 'cpt', '');
            if (ai_cookie ['G'].hasOwnProperty ('ct'))  ai_set_cookie ('G', 'ct', '');
          }
        }
    });

    // Remove check class so it's not processed again when tracking is called
    // ***
//    $('.ai-check-block'). removeClass ('ai-check-block');
    document.querySelectorAll ('.ai-check-block').forEach ((element, i) => {
      element.classList.remove ('ai-check-block');
    });


    if (ai_debug) console.log ('');
    if (ai_debug) console.log ('AI PROCESS CHECKS', ai_check_data);


    if (ai_debug) console.log ('AI CHECK PAGEVIEWS');

    for (var cookie_block in ai_cookie) {
      for (var cookie_block_property in ai_cookie [cookie_block]) {
        if (cookie_block_property == 'd') {
          if (ai_debug) console.log ('AI CHECK PAGEVIEWS block:', cookie_block);

          var delay = ai_cookie [cookie_block][cookie_block_property];
          if (delay > 0) {
            if (ai_debug) console.log ('AI PAGEVIEW, block', cookie_block, 'delayed for', delay - 1, 'pageviews');

            ai_set_cookie (cookie_block, 'd', delay - 1);
          } else {
              if (ai_check_data.hasOwnProperty (cookie_block) && ai_check_data [cookie_block].hasOwnProperty ('e')) {
                if (ai_debug) console.log ('AI PAGEVIEW, block', cookie_block, 'show every', ai_check_data [cookie_block]['e'], 'pageviews, delayed for', ai_check_data [cookie_block]['e'] - 1, 'pageviews');

                ai_set_cookie (cookie_block, 'd', ai_check_data [cookie_block]['e'] - 1);
              } else {
                  if (!ai_check_data.hasOwnProperty (cookie_block) || !ai_check_data [cookie_block].hasOwnProperty ('d')) {
                    if (ai_debug) console.log ('AI PAGEVIEW, block', cookie_block, 'removing d');

                    ai_set_cookie (cookie_block, 'd', '');
                  }
                }
            }
        }
      }
    }
  }

  function ai_log_impressions () {

    var ai_debug = typeof ai_debugging !== 'undefined'; // 7
//    var ai_debug = false;

    if (ai_debug) console.log ('');
    if (ai_debug) console.log ('AI TRACKING');

    Array.prototype.forEach.call (document.querySelectorAll ('[data-ai]'), function (block_wrapping_div) {
      var new_tracking_data = '';

      if (ai_debug && block_wrapping_div.hasAttribute ('data-ai-1')) console.log ('AI TRACKING CHECKING BLOCK', block_wrapping_div.getAttribute ('class'));

      for (var fallback_level = 1; fallback_level <= 9; fallback_level ++) {
        if (block_wrapping_div.hasAttribute ('data-ai-' + fallback_level)) {
          new_tracking_data = block_wrapping_div.getAttribute ('data-ai-' + fallback_level);

          if (ai_debug) console.log ('  FALLBACK LEVEL', fallback_level);
        } else break;
      }

      if (new_tracking_data != '') {
        block_wrapping_div.setAttribute ('data-ai', new_tracking_data);
      }

      if (ai_debug) console.log ('  TRACKING DATA UPDATED TO', b64d (block_wrapping_div.getAttribute ('data-ai')));
    });

    if (ai_track_pageviews) {
      var client_width = document.documentElement.clientWidth, inner_width =  window.innerWidth;
      var viewport_width = client_width < inner_width ? inner_width : client_width;

      var version = 0;
      var name = '?';
      // ***
//      $.each (ai_viewport_widths, function (index, width) {
      ai_viewport_widths.forEach ((width, index) => {
        if (viewport_width >= width) {
          version = ai_viewport_indexes [index];
          name = ai_viewport_names [index];
          return (false);
        }
      });

      if (ai_debug) console.log ('AI TRACKING PAGEVIEW, viewport width:', viewport_width, '=>', name);

      // ***
//      var ai_masking_data = jQuery(b64d ("Ym9keQ==")).attr (AI_ADB_ATTR_NAME);
      var ai_masking_data = document.querySelector (b64d ("Ym9keQ==")).getAttribute (ai_adb_attribute);
      if (typeof ai_masking_data === "string") {
        var ai_masking = ai_masking_data == b64d ("bWFzaw==");
      }

      if (typeof ai_masking_data === "string" && typeof ai_masking === "boolean" && ai_masking) {
        if (ai_external_tracking) {
          external_tracking ("ad blocking", 0, ai_viewport_names [version - 1], 0, 0, '', true);
        }
        version |= 0x80;
      }

      pageview_data = [0, version];
    }

    ai_process_pageview_checks ();

    ai_process_impressions ();

    // Pageview data was not sent with block impressions
    if (pageview_data.length != 0) {
      if (ai_debug) console.log ('AI PROCESS IMPRESSIONS - SENDING PAGEVIEW DATA', pageview_data);

      if (ai_internal_tracking) {
        // ***
//        $.ajax ({
//            url: ai_ajax_url,
//            type: "post",
//            data: {
//              action: "ai_ajax",
//              ai_check: ai_data_id,
//              views: [0],
//              versions: [version],
//            },
//            async: true
//        }).done (function (data) {



        var url_data = {
          action: "ai_ajax",
          ai_check: ai_data_id,
        };

        var formBody = [];
        for (var property in url_data) {
          var encodedKey = encodeURIComponent (property);
          var encodedValue = encodeURIComponent (url_data [property]);
          formBody.push (encodedKey + "=" + encodedValue);
        }

        var encodedKey = encodeURIComponent ('views[]');
        var encodedValue = encodeURIComponent (0);
        formBody.push (encodedKey + "=" + encodedValue);

        var encodedKey = encodeURIComponent ('versions[]');
        var encodedValue = encodeURIComponent (version);
        formBody.push (encodedKey + "=" + encodedValue);

        formBody = formBody.join ("&");

        async function ai_post_pageview () {
          const response = await fetch (ai_ajax_url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: formBody
          });

          const text = await response.text ();
          return text;
        }

        ai_post_pageview ().then (data => {

            if (ai_debug) {
              data = data.trim ();
              if (data != "") {
                var db_records = JSON.parse (data);
                console.log ("AI DB RECORDS: ", db_records);
              }
            }
        });
      }
    }

    ai_tracking_finished = true;
  }

  // ***
//  jQuery (window).on ('load', function () {
  window.addEventListener ('load', (event) => {
    if (typeof ai_delay_tracking == 'undefined') {
      ai_delay_tracking = 0;
    }

    setTimeout (ai_log_impressions, ai_delay_tracking + 1400);
    setTimeout (ai_install_click_trackers, ai_delay_tracking + 1500);
  });
// ***
//});
}

ai_ready (ai_tracking);

}
if (typeof ai_adsense_ad_names !== 'undefined') {

//var ai_adsense_ad_names = [];
//var ai_preview_window = typeof ai_preview !== 'undefined';

function ai_process_adsense_ad (element) {
  var ai_debug = typeof ai_debugging !== 'undefined'; // 1
//  var ai_debug = false;

//  var adsense_container = jQuery(element);
  var adsense_container = element;

//  var adsense_width = adsense_container.attr ('width');
  var adsense_width = adsense_container.getAttribute ('width');
//  var adsense_height = adsense_container.attr ('height');
  var adsense_height = adsense_container.getAttribute ('height');

//  var adsense_iframe2 = adsense_container.contents().find ('iframe[allowtransparency]');
//  var url_parameters = getAllUrlParams (adsense_iframe2.attr ('src'))
//  var url_parameters = getAllUrlParams (adsense_container.attr ('src'))
  var url_parameters = getAllUrlParams (adsense_container.getAttribute ('src'))

  if (typeof url_parameters ['client'] !== 'undefined') {
    var adsense_ad_client = url_parameters ['client'];
    var adsense_publisher_id = adsense_ad_client.replace ('ca-', '');
    var adsense_ad_slot = url_parameters ['slotname'];
    var adsense_index = url_parameters ['ifi'];

    if (ai_debug) console.log ('AI ADSENSE', adsense_index, adsense_ad_client, adsense_ad_slot, url_parameters ['format'], url_parameters ['w'], url_parameters ['h']);

//    var adsense_overlay = jQuery('<div class="ai-debug-ad-overlay"></div>');
    var adsense_overlay_class = 'ai-debug-ad-overlay';

    var adsense_ad_info = '';
    if (typeof adsense_ad_slot !== 'undefined') {
      var adsense_ad_name = '';
      if (typeof ai_adsense_ad_names ['publisher_id'] !== 'undefined' &&
          ai_adsense_ad_names ['publisher_id'] == adsense_publisher_id &&
          typeof ai_adsense_ad_names [adsense_ad_slot] !== 'undefined') {
        adsense_ad_name = '<div class="ai-info ai-info-2">' + ai_adsense_ad_names [adsense_ad_slot] + '</div>';
      }
      adsense_ad_info = '<div class="ai-info ai-info-1">' + adsense_ad_slot + '</div>' + adsense_ad_name;
    } else {
        var adsense_auto_ads = adsense_container.closest ('div.google-auto-placed').length != 0;
        if (adsense_auto_ads) {
//          adsense_overlay.addClass ('ai-auto-ads');
          adsense_overlay_class += ' ai-auto-ads';

          adsense_ad_info = '<div class="ai-info ai-info-1">Auto ads</div>';
//        } else adsense_overlay.addClass ('ai-no-slot');
        } else adsense_overlay_class += ' ai-no-slot';
      }

    var adsense_overlay = '<div class="' + adsense_overlay_class + '"></div>';

//    var adsense_info = jQuery('<div class="ai-debug-ad-info"><div class="ai-info ai-info-1">AdSense #' + adsense_index + '</div><div class="ai-info ai-info-2">' + adsense_width + 'x' + adsense_height + '</div>' + adsense_ad_info + '</div>');
    var adsense_info = '<div class="ai-debug-ad-info"><div class="ai-info ai-info-1">AdSense #' + adsense_index + '</div><div class="ai-info ai-info-2">' + adsense_width + 'x' + adsense_height + '</div>' + adsense_ad_info + '</div>';

//    adsense_container.after (adsense_info);
    adsense_container.insertAdjacentHTML ('afterend', adsense_info);

    if (!ai_preview_window) {
//      adsense_container.after (adsense_overlay);
      adsense_container.insertAdjacentHTML ('afterend', adsense_overlay);
    }
  }
}

//function ai_process_adsense_ads () {
////  jQuery('ins > ins > iframe[src*="google"]:visible').each (function () {
//  document.querySelectorAll ('ins iframe[src*="google"]').forEach ((el, index) => {
//    if (!!(el.offsetWidth || el.offsetHeight || el.getClientRects ().length)) {
////      ai_process_adsense_ad (this);
//      ai_process_adsense_ad (el);
//    }
//  });
//}


//jQuery(document).ready(function($) {
function ai_load_adsense_ad_units () {

  var ai_debug = typeof ai_debugging !== 'undefined'; // 2
//  var ai_debug = false;

//  var ai_ajax_url = 'AI_AJAXURL';
//  var ai_nonce = 'AI_NONCE';
//  var adsense_data = {'ai': 1}; // dummy

//  $.post (ai_ajax_url, {'action': 'ai_ajax', 'ai_check': ai_nonce, 'adsense-ad-units': adsense_data}
//  ).done (function (data) {

  var data = {
    'action': "ai_ajax",
    'ai_check': ai_nonce,
    'adsense-ad-units[ai]': 1
  };

  var formBody = [];
  for (var property in data) {
    var encodedKey = encodeURIComponent (property);
    var encodedValue = encodeURIComponent (data [property]);
    formBody.push (encodedKey + "=" + encodedValue);
  }
  formBody = formBody.join ("&");

  async function ai_load_adsense () {
    const response = await fetch (ai_ajax_url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
      },
      body: formBody
    });

    const text = await response.text ();

    return text;
  }

  ai_load_adsense ().then (data => {
    if (data != '') {
      try {
        ai_adsense_ad_names = JSON.parse (data);

        if (ai_debug) console.log ('');
        if (ai_debug) console.log ("AI ADSENSE DATA:", Object.keys (ai_adsense_ad_names).length - 1, 'ad units');

      } catch (error) {
        if (ai_debug) console.log ("AI ADSENSE DATA ERROR:", data);
      }
    }
    if (ai_debug) console.log ('AI ADSENSE DATA', 'END');
//  }).fail (function (xhr, status, error) {
  }).catch ((error) => {
    if (ai_debug) console.log ("AI ADSENSE DATA ERROR:", error.status, error.statusText);
//  }).always (function (data) {
  });

//  $(window).on ('load', function () {
//    if (!ai_preview_window) setTimeout (function() {ai_process_adsense_ads (jQuery);}, 500);
//  });
//});
}

function ai_ready (fn) {
  if (document.readyState   === 'complete' || (document.readyState !== 'loading' && !document.documentElement.doScroll)) {
    fn ();
  } else {
     document.addEventListener ('DOMContentLoaded', fn);
  }
}

ai_ready (ai_load_adsense_ad_units);

if (!ai_preview_window) {
  const targetNode = document.querySelector ('body');
  const config = {attributes: false, childList: true, subtree: true};
  const ai_process_adsense_callback = function (mutationsList, observer) {
    // Use traditional 'for loops' for IE 11
    for (const mutation of mutationsList) {
      if (mutation.type === 'childList' &&
          mutation.addedNodes.length &&
          mutation.addedNodes [0].tagName == 'IFRAME' &&
          mutation.addedNodes [0].getAttribute ('width') != null &&
          mutation.addedNodes [0].getAttribute ('height') != null &&
          !!mutation.addedNodes [0].closest ('.adsbygoogle')) {
        ai_process_adsense_ad (mutation.addedNodes [0]);
      }
    }
  };

  const observer = new MutationObserver (ai_process_adsense_callback);
  observer.observe (targetNode, config);
}


function getAllUrlParams (url) {

  // get query string from url (optional) or window
  var queryString = url ? url.split('?')[1] : window.location.search.slice(1);

  // we'll store the parameters here
  var obj = {};

  // if query string exists
  if (queryString) {

    // stuff after # is not part of query string, so get rid of it
    queryString = queryString.split('#')[0];

    // split our query string into its component parts
    var arr = queryString.split('&');

    for (var i=0; i<arr.length; i++) {
      // separate the keys and the values
      var a = arr[i].split('=');

      // in case params look like: list[]=thing1&list[]=thing2
      var paramNum = undefined;
      var paramName = a[0].replace(/\[\d*\]/, function(v) {
        paramNum = v.slice(1,-1);
        return '';
      });

      // set parameter value (use 'true' if empty)
//      var paramValue = typeof(a[1])==='undefined' ? true : a[1];
      var paramValue = typeof(a[1])==='undefined' ? '' : a[1];

      // (optional) keep case consistent
      paramName = paramName.toLowerCase();
      paramValue = paramValue.toLowerCase();

      // if parameter name already exists
      if (obj[paramName]) {
        // convert value to array (if still string)
        if (typeof obj[paramName] === 'string') {
          obj[paramName] = [obj[paramName]];
        }
        // if no array index number specified...
        if (typeof paramNum === 'undefined') {
          // put the value on the end of the array
          obj[paramName].push(paramValue);
        }
        // if array index number specified...
        else {
          // put the value at that index number
          obj[paramName][paramNum] = paramValue;
        }
      }
      // if param name doesn't exist yet, set it
      else {
        obj[paramName] = paramValue;
      }
    }
  }

  return obj;
}

}
if (typeof ai_adsense_ad_names !== 'undefined') {

//jQuery(window).on ('load', function () {
window.addEventListener ('load', (event) => {
  setTimeout (function() {
//    var google_auto_placed = jQuery ('.google-auto-placed > ins');
//    google_auto_placed.before ('<section class=\"ai-debug-bar ai-debug-adsense ai-adsense-auto-ads\">' + ai_front.automatically_placed + '</section>');
    document.querySelectorAll ('.google-auto-placed > ins').forEach ((el, index) => {
      el.insertAdjacentHTML ('afterbegin',  '<section class=\"ai-debug-bar ai-debug-adsense ai-adsense-auto-ads\">' + ai_front.automatically_placed + '</section>');
    });

  }, 150);
});

}
//jQuery(document).ready(function($) {
// ***
function ai_check_close_buttons () {
  var ai_debug = typeof ai_debugging !== 'undefined';
//  var ai_debug = false;

  function ai_process_close_button (element) {
//    var ai_close_button = $(element).find ('.ai-close-button.ai-close-unprocessed');
    // ***
    var ai_close_button = element.querySelector ('.ai-close-button.ai-close-unprocessed');

    if (ai_close_button != null) {


      ai_close_button.addEventListener ('click', (event) => {
        ai_close_block (ai_close_button);
      });


//      if ($(element).outerHeight () !== 0) {
      // ***
      if (element.offsetHeight !== 0) {
//        if (!$(element).find ('.ai-parallax').length) {
        // ***
        if (element.querySelector ('.ai-parallax') == null) {
//          $(element).css ('width', '').addClass ('ai-close-fit');
          // ***
          element.style.width = '';
          element.classList.add ('ai-close-fit');
        }
//        $(element).find ('.ai-close-button').fadeIn (50);
        // ***
        ai_fade_in (element.querySelector ('.ai-close-button'), 50);

//        if (ai_debug) console.log ('AI CLOSE BUTTON', $(element).attr ('class'));
        // ***
        if (ai_debug) console.log ('AI CLOSE BUTTON', element.hasAttribute ("class") ? element.getAttribute ('class') : '');
      } else {
//          if (ai_debug) console.log ('AI CLOSE BUTTON outerHeight 0', $(element).attr ('class'));
          // ***
          if (ai_debug) console.log ('AI CLOSE BUTTON outerHeight 0', element.hasAttribute ("class") ? element.getAttribute ('class') : '');

//          var ai_close_button = $(element);
          // ***
          var ai_close_button = element;
          setTimeout (function() {
            if (ai_debug) console.log ('');

//            if (ai_close_button.outerHeight () !== 0) {
            // ***
            if (ai_close_button.offsetHeight !== 0) {
//              if (!ai_close_button.find ('.ai-parallax').length) {
              // ***
//              if (!ai_close_button.find ('.ai-parallax').length) {
              // ***
              if (ai_close_button.querySelector ('.ai-parallax') == null) {
              // ***
//                ai_close_button.css ('width', '').addClass ('ai-close-fit');
                ai_close_button.style.width = '';
                ai_close_button.classList.add ('ai-close-fit');
              }
//              ai_close_button.find ('.ai-close-button').fadeIn (50);
              // ***
              ai_fade_in (ai_close_button.querySelector ('.ai-close-button'), 50);

//              if (ai_debug) console.log ('AI DELAYED CLOSE BUTTON ', ai_close_button.attr ('class'));
              // ***
              if (ai_debug) console.log ('AI DELAYED CLOSE BUTTON ', ai_close_button.hasAttribute ("class") ? ai_close_button.getAttribute ('class') : '');
//            } else if (ai_debug) console.log ('AI DELAYED CLOSE BUTTON outerHeight 0', ai_close_button.attr ('class'));
            // ***
            } else if (ai_debug) console.log ('AI DELAYED CLOSE BUTTON outerHeight 0', ai_close_button.hasAttribute ("class") ? ai_close_button.getAttribute ('class') : '');
          }, 4000);
        }



        if (typeof ai_preview === 'undefined') {
    //      setTimeout (function() {

    //          var button = $(this);
              // ***
              var button = ai_close_button;
    //          var timeout = button.data ('ai-close-timeout');
              // ***
              var timeout = button.dataset.aiCloseTimeout;

              if (typeof timeout != 'undefined' && timeout > 0) {
    //            if (ai_debug) console.log ('AI CLOSE TIME', timeout, 's,', typeof button.closest ('.ai-close').attr ('class') != 'undefined' ? button.closest ('.ai-close').attr ('class') : '');
                // ***
                if (ai_debug) console.log ('AI CLOSE TIME', timeout, 's,', button.closest ('.ai-close').hasAttribute ('class') ? button.closest ('.ai-close').getAttribute ('class') : '');

                // Compensate for delayed timeout
                if (timeout > 2) timeout = timeout - 2; else timeout = 0;

                setTimeout (function() {
                  if (ai_debug) console.log ('');
    //              if (ai_debug) console.log ('AI CLOSE TIMEOUT', typeof button.closest ('.ai-close').attr ('class') != 'undefined' ? button.closest ('.ai-close').attr ('class') : '');
                  // ***
                  if (ai_debug) console.log ('AI CLOSE TIMEOUT', button.closest ('.ai-close').hasAttribute ('class') ? button.closest ('.ai-close').getAttribute ('class') : '');

                  ai_close_block (button);
                }, timeout * 1000 + 1);
              }
    //      }, 2000);
        }


//      $(ai_close_button).removeClass ('ai-close-unprocessed');
      // ***
      ai_close_button.classList.remove ('ai-close-unprocessed');
    }
  }

  ai_close_block = function (button) {
//    var block_wrapper = $(button).closest ('.ai-close');
    // ***
    var block_wrapper = button.closest ('.ai-close');
//    var block = $(button).data ('ai-block');
    // ***
    var block = button.dataset.aiBlock;
//    if (typeof block_wrapper != 'undefined') {
    // ***
    if (block_wrapper != null) {
//      var hash = block_wrapper.find ('.ai-attributes [data-ai-hash]').data ('ai-hash');
      // ***
      if (block_wrapper.querySelector ('.ai-attributes [data-ai-hash]') != null && 'aiHash' in block_wrapper.querySelector ('.ai-attributes [data-ai-hash]').dataset) {
        var hash = block_wrapper.querySelector ('.ai-attributes [data-ai-hash]').dataset.aiHash;
//        var closed = $(button).data ('ai-closed-time');
//        if (typeof closed != 'undefined') {
        // ***
        if ('aiClosedTime'in button.dataset) {
          var closed = button.dataset.aiClosedTime;
          if (ai_debug) console.log ('AI CLOSED block', block, 'for', closed, 'days');

          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);

          // TODO: stay closed for session
          ai_set_cookie (block, 'x', Math.round (timestamp + closed * 24 * 3600));
          ai_set_cookie (block, 'h', hash);
        }
      } else {
          var ai_cookie = ai_set_cookie (block, 'x', '');
          if (ai_cookie.hasOwnProperty (block) && !ai_cookie [block].hasOwnProperty ('i') && !ai_cookie [block].hasOwnProperty ('c')) {
            ai_set_cookie (block, 'h', '');
          }
        }

      block_wrapper.remove ();
    } else {
        ai_set_cookie (block, 'x', '');
        if (ai_cookie.hasOwnProperty (block) && !ai_cookie [block].hasOwnProperty ('i') && !ai_cookie [block].hasOwnProperty ('c')) {
          ai_set_cookie (block, 'h', '');
        }
      }
  }

  ai_install_close_buttons = function (element) {
//    if (ai_debug) console.log ('AI CLOSE BUTTONS INSTALL');

//    setTimeout (function () {
////      $('.ai-close-button.ai-close-unprocessed', element).click (function () {
//      // ***
//      element.querySelectorAll ('.ai-close-button.ai-close-unprocessed').forEach ((el, index) => {

//        if (!el.classList.contains ('ai-close-event')) {
//          el.addEventListener ('click', (event) => {
//            ai_close_block (el);
//          });
//        }
//        el.classList.add ('ai-close-event');
//      });
//    }, 1800);

//    if (typeof ai_preview === 'undefined') {
//      setTimeout (function() {
////        $('.ai-close-button.ai-close-unprocessed', element).each (function () {
//        // ***
//        element.querySelectorAll ('.ai-close-button.ai-close-unprocessed').forEach ((el, index) => {

////          var button = $(this);
//          // ***
//          var button = el;
////          var timeout = button.data ('ai-close-timeout');
//          // ***
//          var timeout = button.dataset.aiCloseTimeout;

//          if (typeof timeout != 'undefined' && timeout > 0) {
////            if (ai_debug) console.log ('AI CLOSE TIME', timeout, 's,', typeof button.closest ('.ai-close').attr ('class') != 'undefined' ? button.closest ('.ai-close').attr ('class') : '');
//            // ***
//            if (ai_debug) console.log ('AI CLOSE TIME', timeout, 's,', button.closest ('.ai-close').hasAttribute ('class') ? button.closest ('.ai-close').getAttribute ('class') : '');

//            // Compensate for delayed timeout
//            if (timeout > 2) timeout = timeout - 2; else timeout = 0;

//            setTimeout (function() {
//              if (ai_debug) console.log ('');
////              if (ai_debug) console.log ('AI CLOSE TIMEOUT', typeof button.closest ('.ai-close').attr ('class') != 'undefined' ? button.closest ('.ai-close').attr ('class') : '');
//              // ***
//              if (ai_debug) console.log ('AI CLOSE TIMEOUT', button.closest ('.ai-close').hasAttribute ('class') ? button.closest ('.ai-close').getAttribute ('class') : '');

//              ai_close_block (button);
//            }, timeout * 1000 + 1);
//          }
//        });
//      }, 2000);
//    }

    setTimeout (function() {
      if (ai_debug) console.log ('');
//      if (ai_debug) console.log ('AI CLOSE BUTTON INSTALL', typeof $(element).attr ('class') != 'undefined' ? $(element).attr ('class') : '');
      // ***

      if (ai_debug) console.log ('AI CLOSE BUTTON INSTALL', element instanceof Element && element.hasAttribute ('class') ? element.getAttribute ('class') : '');

//      if ($(element).hasClass ('ai-close')) ai_process_close_button (element); else
      // ***
      if (element instanceof Element && element.classList.contains ('ai-close')) ai_process_close_button (element); else
//        $('.ai-close', element).each (function() {
        // ***
        element.querySelectorAll ('.ai-close').forEach ((el, index) => {
//          ai_process_close_button (this);
          // ***
          ai_process_close_button (el);
        });
     }, ai_close_button_delay);
  }

  if (typeof ai_close_button_delay == 'undefined') {
    ai_close_button_delay = 2200;
  }

  ai_install_close_buttons (document);
//});
// ***
}


function ai_fade_in (el, time) {
  el.style.display = 'block';
  el.style.opacity = 0;

  var last = +new Date();
  var tick = function () {
    el.style.opacity = +el.style.opacity + (new Date() - last) / time;
    last = +new Date();

    if (+el.style.opacity < 1) {
      (window.requestAnimationFrame && requestAnimationFrame (tick)) || setTimeout (tick, 16);
    }
  };

  tick ();
}

function ai_ready (fn) {
  if (document.readyState === 'complete' || (document.readyState !== 'loading' && !document.documentElement.doScroll)) {
    fn ();
  } else {
     document.addEventListener ('DOMContentLoaded', fn);
  }
}

ai_ready (ai_check_close_buttons);
if (typeof ai_filter != 'undefined') {

  function prev (el, selector) {
    if (selector) {
      let previous = el.previousElementSibling;
      while (previous && !previous.matches (selector)) {
        previous = previous.previousElementSibling;
      }
      return previous;
    } else {
      return el.previousElementSibling;
    }
  }

//jQuery (function ($) {
// ***
//  function ai_random_parameter () {
//    var current_time = new Date ().getTime ();
//    return '&ver=' + current_time + '-' + Math.round (Math.random () * 100000);
//  }
  function ai_random_parameter () {
    var current_time = new Date ().getTime ();
    return current_time + '-' + Math.round (Math.random () * 100000);
  }

  function process_filter_hook_data (ai_filter_hook_blocks) {

    var ai_debug = typeof ai_debugging !== 'undefined'; // 1
//    var ai_debug = false;

//    ai_filter_hook_blocks.removeClass ('ai-filter-check');
    // ***
    ai_filter_hook_blocks.forEach ((el, i) => {
      el.classList.remove ('ai-filter-check');
    });

    var enable_block = false;

    if (ai_debug) console.log ('');
    if (ai_debug) console.log ("AI FILTER HOOK DATA: " + ai_filter_hook_data);

    if (ai_filter_hook_data == '') {
      if (ai_debug) console.log ('AI FILTER HOOK DATA EMPTY');
      return;
    }
    try {
      var filter_hook_data_array = JSON.parse (ai_filter_hook_data);

    } catch (error) {
        if (ai_debug) console.log ('AI FILTER HOOK DATA JSON ERROR');
        return;
    }

//    if (filter_hook_data_array != null) ai_filter_hook_blocks.each (function () {
    // ***
    if (filter_hook_data_array != null) ai_filter_hook_blocks.forEach ((el, index) => {

//      var block_wrapping_div = $(this).closest ('div.AI_FUNCT_GET_BLOCK_CLASS_NAME');
      // ***
      var block_wrapping_div = el.closest ('div.' + ai_block_class_def);
//      var block = parseInt ($(this).data ('block'));
      // ***
      var block = parseInt (el.dataset.block);

//      if (ai_debug) console.log ('AI FILTER HOOK BLOCK', block_wrapping_div.attr ('class'));
      // ***
      if (ai_debug) console.log ('AI FILTER HOOK BLOCK', block_wrapping_div.hasAttribute ('class') ? block_wrapping_div.getAttribute ('class') : '');

      enable_block = false;

      if (typeof filter_hook_data_array !== 'undefined') {
        if (filter_hook_data_array.includes ('*')) {
          enable_block = true;
          if (filter_hook_data_array.includes (- block)) {
            enable_block = false;
          }
        }
        else if (filter_hook_data_array.includes (block)) enable_block = true;
      }

      if (ai_debug) console.log ('AI FILTER HOOK BLOCK', block, enable_block ? 'ENABLED' : 'DISABLED');

//      $(this).css ({"visibility": "", "position": "", "width": "", "height": "", "z-index": ""});
      // ***
      el.style.visibility = '';
      el.style.position = 'none';
      el.style.width = '';
      el.style.height = '';
      el.style.zIndex = '';

      var comments = '';
      var comments_decoded = JSON.parse (ai_filter_hook_comments);
      if (typeof comments_decoded == 'string') {
        comments = comments_decoded;
      }
      else if (typeof comments_decoded == 'object') {
        comments = '';
        for (const [key, value] of Object.entries (comments_decoded)) {
          comments = comments + `${key}: ${value}\n`;
        }
      }
      else comments = ai_filter_hook_comments;

      if (typeof ai_front != 'undefined') {
  //      var debug_bar = $(this).prev ('.ai-debug-bar');
        // ***
        var debug_bar = prev (el, '.ai-debug-bar');
        if (debug_bar != null) {
    //      debug_bar.find ('.ai-status').text (enable_block ? ai_front.visible : ai_front.hidden);
          // ***
          debug_bar.querySelectorAll ('.ai-status').forEach ((element, index) => {
            element.textContent = enable_block ? ai_front.visible : ai_front.hidden;
          });

    //      debug_bar.find ('.ai-filter-data').attr ('title', comments);
          // ***
          debug_bar.querySelectorAll ('.ai-filter-data').forEach ((element, index) => {
            element.setAttribute ('title', comments);
          });
        }
      }

      if (!enable_block) {
//        $(this).hide (); // .ai-filter-check
        // ***
        el.style.display = 'none'; // .ai-filter-check

//        if (!block_wrapping_div.find ('.ai-debug-block').length) {
        // ***
        if (!block_wrapping_div.querySelector ('.ai-debug-block') != null) {
//          block_wrapping_div.hide ();
          // ***
          block_wrapping_div.style.display = 'none'; // .ai-filter-check
        }

//        block_wrapping_div.removeAttr ('data-ai');
        // ***
        block_wrapping_div.removeAttribute ('data-ai');

//        if (block_wrapping_div.find ('.ai-debug-block')) {
        // ***
        if (block_wrapping_div.querySelector('.ai-debug-block') != null) {
//          block_wrapping_div.css ({"visibility": ""}).removeClass ('ai-close');
          // ***
          block_wrapping_div.style.visibility = '';
          block_wrapping_div.classList.remove ('ai-close');

//          if (block_wrapping_div.hasClass ('ai-remove-position')) {
          // ***
          if (block_wrapping_div.classList.contains ('ai-remove-position')) {
//            block_wrapping_div.css ({"position": ""});
            block_wrapping_div.style.position = '';
          }

          // In case client-side insert is used and lists will not be processed
//          if (typeof $(this).data ('code') != 'undefined') {
          // ***
          if ('code' in el.dataset) {
            // Remove ai-list-block to show debug info
//            block_wrapping_div.removeClass ('ai-list-block');
//            block_wrapping_div.removeClass ('ai-list-block-ip');
            // ***
            block_wrapping_div.classList.remove ('ai-list-block');
            block_wrapping_div.classList.remove ('ai-list-block-ip');

            // Remove also 'NOT LOADED' bar if it is there
//            if (block_wrapping_div.prev ().hasClass ('ai-debug-info')) {
            // ***
            if (prev (block_wrapping_div) != null && prev (block_wrapping_div).classList.contains ('ai-debug-info')) {
//              block_wrapping_div.prev ().remove ();
              // ***
              prev (block_wrapping_div).remove ();
            }
          }

//        } else block_wrapping_div.hide ();
        // ***
        } else block_wrapping_div.style.display = 'none';;
      } else {
//          block_wrapping_div.css ({"visibility": ""});
          // ***
          block_wrapping_div.style.visibility = '';

//          if (block_wrapping_div.hasClass ('ai-remove-position')) {
          // ***
          if (block_wrapping_div.classList.contains ('ai-remove-position')) {
//            block_wrapping_div.css ({"position": ""});
            // ***
            block_wrapping_div.style.position = '';
          }

//          if (typeof $(this).data ('code') != 'undefined') {
          // ***
          if ('code' in el.dataset) {
//            var block_code = b64d ($(this).data ('code'));
            var block_code = b64d (el.dataset.code);

            var template = document.createElement ('div');
            template.innerHTML = block_code;

            var range = document.createRange ();

            var fragment_ok = true;
            try {
              var fragment = range.createContextualFragment (template.innerHTML);
            }
            catch (err) {
              var fragment_ok = false;
              if (ai_debug) console.log ('AI INSERT', 'range.createContextualFragment ERROR:', err.message);
            }

//            if ($(this).closest ('head').length != 0) {
            // ***
            if (el.closest ('head') != null) {
//              $(this).after (block_code);
              // ***
              el.insertBefore (fragment, null);

//              if (!ai_debug) $(this).remove ();
              // ***
              if (!ai_debug) el.remove ();
//            } else $(this).append (block_code);
            // ***
            } else el.parentNode.insertBefore (fragment, el.nextSibling);

//                if (!ai_debug)
//            $(this).attr ('data-code', '');
            // ***
            el.setAttribute ('data-code', '');

//            if (ai_debug) console.log ('AI INSERT CODE', $(block_wrapping_div).attr ('class'));
            // ***
            if (ai_debug) console.log ('AI INSERT CODE', block_wrapping_div.hasAttribute ('class') ? block_wrapping_div.getAttribute ('class') : '');
            if (ai_debug) console.log ('');

//            ai_process_element (this);
            // ***
            ai_process_element (el);
          }
        }

//      block_wrapping_div.removeClass ('ai-list-block-filter');
      block_wrapping_div.classList.remove ('ai-list-block-filter');
    });
  }

//  ai_process_filter_hooks = function (ai_filter_hook_blocks) {
  // ***
  ai_process_filter_hooks = function (element) {

    var ai_debug = typeof ai_debugging !== 'undefined'; // 2
//    var ai_debug = false;

    if (element == null) {
//      ai_filter_hook_blocks = $("div.ai-filter-check, meta.ai-filter-check");
      // ***
      ai_filter_hook_blocks = document.querySelectorAll ("div.ai-filter-check, meta.ai-filter-check");
    } else {
//        ai_filter_hook_blocks = ai_filter_hook_blocks.filter ('.ai-filter-check');

        // Temp fix for jQuery elements
        // ***
        if (window.jQuery && window.jQuery.fn && element instanceof jQuery && element.hasOwnProperty ('0')) {
          ai_filter_hook_blocks = element [0];
        } else

        // ***
        ai_filter_hook_blocks = element.querySelectorAll ('.ai-filter-check');
      }

    if (!ai_filter_hook_blocks.length) return;

    if (ai_debug) console.log ("AI PROCESSING FILTER HOOK:", ai_filter_hook_blocks.length, "blocks");

    if (typeof ai_filter_hook_data != 'undefined') {
      if (ai_debug) console.log ("SAVED FILTER HOOK DATA:", ai_filter_hook_data);
      process_filter_hook_data (ai_filter_hook_blocks);
      return;
    }

    if (typeof ai_filter_hook_data_requested != 'undefined') {
      if (ai_debug) console.log ("FILTER HOOK DATA ALREADY REQUESTED, STILL WAITING...");
      return;
    }

    var user_agent = window.navigator.userAgent;
    var language = navigator.language;

    if (ai_debug) console.log ("REQUESTING FILTER HOOK DATA");
    if (ai_debug) console.log ("USER AGENT:", user_agent);
    if (ai_debug) console.log ("LANGUAGE:", language);

    ai_filter_hook_data_requested = true;

//    var page = site_url+"/wp-admin/admin-ajax.php?action=ai_ajax&filter-hook-data=all&ai_check=" + ai_data_id + '&http_user_agent=' + encodeURIComponent (user_agent) + '&http_accept_language=' + encodeURIComponent (language) + ai_random_parameter ();
//    $.get (page, function (filter_hook_data) {
    // ***
    var url_data = {
      action: "ai_ajax",
      'filter-hook-data': 'all',
      check: ai_data_id,
      http_user_agent: encodeURIComponent (user_agent),
      http_accept_language: encodeURIComponent (language),
      ver: ai_random_parameter ()
    };

    var formBody = [];
    for (var property in url_data) {
      var encodedKey = encodeURIComponent (property);
      var encodedValue = encodeURIComponent (url_data [property]);
      formBody.push (encodedKey + "=" + encodedValue);
    }
    formBody = formBody.join ("&");

    async function ai_filter_check () {
      const response = await fetch (ai_ajax_url + '?' + formBody, {
        method: 'GET',
      });

//      if (!response.ok) {
////        throw new Error(`HTTP error! status: ${response.status}`);
//        if (ai_debug) console.log ("Ajax call failed, Status: " + response.status + ", Error: " + response.statusText);
//      }

      const text = await response.text ();

      return text;
    }

    ai_filter_check ().then (filter_hook_data => {

      if (filter_hook_data == '') {
        var error_message = 'AI FILTER HOOK Ajax request returned empty data, filter hook checks not processed';
        console.error (error_message);

        if (typeof ai_js_errors != 'undefined') {
          ai_js_errors.push ([error_message, page, 0]);
        }
      } else {
          try {
            var filter_hook_data_test = JSON.parse (filter_hook_data);
          } catch (error) {
            var error_message = 'AI FILTER HOOK Ajax call returned invalid data, filter hook checks not processed';
            console.error (error_message);

            if (typeof ai_js_errors != 'undefined') {
              ai_js_errors.push ([error_message, page, 0]);
            }
          }
        }

      ai_filter_hook_data = JSON.stringify (filter_hook_data_test ['blocks']);
      ai_filter_hook_comments = JSON.stringify (filter_hook_data_test ['comments']);

      if (ai_debug) console.log ('');
      if (ai_debug) console.log ("AI FILTER HOOK RETURNED DATA:", ai_filter_hook_data);
      if (ai_debug) console.log ("AI FILTER HOOK RETURNED COMMENTS:", filter_hook_data_test ['comments']);

      // Check blocks again - some blocks might get inserted after the filte hook data was requested
//      ai_filter_hook_blocks = $("div.ai-filter-check, meta.ai-filter-check");
      ai_filter_hook_blocks = document.querySelectorAll ("div.ai-filter-check, meta.ai-filter-check");

      if (ai_debug) console.log ("AI FILTER HOOK BLOCKS:", ai_filter_hook_blocks.length);

      process_filter_hook_data (ai_filter_hook_blocks);
//    }).fail (function(jqXHR, status, err) {
    // ***
    }).catch ((error) => {
//      if (ai_debug) console.log ("Ajax call failed, Status: " + status + ", Error: " + err);
      // ***
      if (ai_debug) console.error ("AI FILTER ERROR:", error);
//      $("div.ai-filter-check").each (function () {
      document.querySelectorAll ('div.ai-filter-check').forEach ((el, index) => {
//        $(this).css ({"display": "none", "visibility": "", "position": "", "width": "", "height": "", "z-index": ""}).removeClass ('ai-filter-check').hide ();
        el.style.display = 'none';
        el.style.visibility = '';
        el.style.position = '';
        el.style.width = '';
        el.style.height = '';
        el.style.zIndex = '';

        el.classList.remove ('ai-filter-check');
        el.style.display = 'none';
      });
    });
  }


//  $(document).ready (function($) {
//    setTimeout (function () {ai_process_filter_hooks ()}, 3);
//  });
// ***
function ai_ready (fn) {
  if (document.readyState === 'complete' || (document.readyState !== 'loading' && !document.documentElement.doScroll)) {
    fn ();
  } else {
     document.addEventListener ('DOMContentLoaded', fn);
  }
}

function ai_check_filter_hooks () {
  setTimeout (function () {ai_process_filter_hooks ()}, 3);
}

ai_ready (ai_check_filter_hooks);

//});
// ***

function ai_process_element (element) {
  setTimeout (function() {
    if (typeof ai_process_rotations_in_element == 'function') {
      ai_process_rotations_in_element (element);
    }

    if (typeof ai_process_lists == 'function') {
      // ***
      ai_process_lists (jQuery (".ai-list-data", element));
    }

    if (typeof ai_process_ip_addresses == 'function') {
      // ***
      ai_process_ip_addresses (jQuery (".ai-ip-data", element));
    }

    if (typeof ai_process_filter_hooks == 'function') {
//      ai_process_filter_hooks (jQuery (".ai-filter-check", element));
      // ***
      ai_process_filter_hooks (element);
    }

    if (typeof ai_adb_process_blocks == 'function') {
      ai_adb_process_blocks (element);
    }

    if (typeof ai_process_impressions == 'function' && ai_tracking_finished == true) {
      ai_process_impressions ();
    }
    if (typeof ai_install_click_trackers == 'function' && ai_tracking_finished == true) {
      ai_install_click_trackers ();
    }

    if (typeof ai_install_close_buttons == 'function') {
      ai_install_close_buttons (document);
    }
  }, 5);
}

}

/**
 * Copyright Marc J. Schmidt. See the LICENSE file at the top-level
 * directory of this distribution and at
 * https://github.com/marcj/css-element-queries/blob/master/LICENSE.
 */
;
(function() {

    /**
     * Class for dimension change detection.
     *
     * @param {Element|Element[]|Elements|jQuery} element
     * @param {Function} callback
     *
     * @constructor
     */
    var ResizeSensor = function(element, callback) {
        /**
         *
         * @constructor
         */
        function EventQueue() {
            this.q = [];
            this.add = function(ev) {
                this.q.push(ev);
            };

            var i, j;
            this.call = function() {
                for (i = 0, j = this.q.length; i < j; i++) {
                    this.q[i].call();
                }
            };
        }

        /**
         * @param {HTMLElement} element
         * @param {String}      prop
         * @returns {String|Number}
         */
        function getComputedStyle(element, prop) {
            if (element.currentStyle) {
                return element.currentStyle[prop];
            } else if (window.getComputedStyle) {
                return window.getComputedStyle(element, null).getPropertyValue(prop);
            } else {
                return element.style[prop];
            }
        }

        /**
         *
         * @param {HTMLElement} element
         * @param {Function}    resized
         */
        function attachResizeEvent(element, resized) {
            if (!element.resizedAttached) {
                element.resizedAttached = new EventQueue();
                element.resizedAttached.add(resized);
            } else if (element.resizedAttached) {
                element.resizedAttached.add(resized);
                return;
            }

            element.resizeSensor = document.createElement('div');
            element.resizeSensor.className = 'resize-sensor';
            var style = 'position: absolute; left: 0; top: 0; right: 0; bottom: 0; overflow: hidden; z-index: -1; visibility: hidden;';
            var styleChild = 'position: absolute; left: 0; top: 0; transition: 0s;';

            element.resizeSensor.style.cssText = style;
            element.resizeSensor.innerHTML =
                '<div class="resize-sensor-expand" style="' + style + '">' +
                    '<div style="' + styleChild + '"></div>' +
                '</div>' +
                '<div class="resize-sensor-shrink" style="' + style + '">' +
                    '<div style="' + styleChild + ' width: 200%; height: 200%"></div>' +
                '</div>';
            element.appendChild(element.resizeSensor);

            if (!{fixed: 1, absolute: 1}[getComputedStyle(element, 'position')]) {
                element.style.position = 'relative';
            }

            var expand = element.resizeSensor.childNodes[0];
            var expandChild = expand.childNodes[0];
            var shrink = element.resizeSensor.childNodes[1];
            var shrinkChild = shrink.childNodes[0];

            var lastWidth, lastHeight;

            var reset = function() {
                expandChild.style.width = expand.offsetWidth + 10 + 'px';
                expandChild.style.height = expand.offsetHeight + 10 + 'px';
                expand.scrollLeft = expand.scrollWidth;
                expand.scrollTop = expand.scrollHeight;
                shrink.scrollLeft = shrink.scrollWidth;
                shrink.scrollTop = shrink.scrollHeight;
                lastWidth = element.offsetWidth;
                lastHeight = element.offsetHeight;
            };

            reset();

            var changed = function() {
                if (element.resizedAttached) {
                    element.resizedAttached.call();
                }
            };

            var addEvent = function(el, name, cb) {
                if (el.attachEvent) {
                    el.attachEvent('on' + name, cb);
                } else {
                    el.addEventListener(name, cb);
                }
            };

            var onScroll = function() {
              if (element.offsetWidth != lastWidth || element.offsetHeight != lastHeight) {
                  changed();
              }
              reset();
            };

            addEvent(expand, 'scroll', onScroll);
            addEvent(shrink, 'scroll', onScroll);
        }

        var elementType = Object.prototype.toString.call(element);
        var isCollectionTyped = ('[object Array]' === elementType
            || ('[object NodeList]' === elementType)
            || ('[object HTMLCollection]' === elementType)
            || ('undefined' !== typeof jQuery && element instanceof jQuery) //jquery
            || ('undefined' !== typeof Elements && element instanceof Elements) //mootools
        );

        if (isCollectionTyped) {
            var i = 0, j = element.length;
            for (; i < j; i++) {
                attachResizeEvent(element[i], callback);
            }
        } else {
            attachResizeEvent(element, callback);
        }

        this.detach = function() {
            if (isCollectionTyped) {
                var i = 0, j = element.length;
                for (; i < j; i++) {
                    ResizeSensor.detach(element[i]);
                }
            } else {
                ResizeSensor.detach(element);
            }
        };
    };

    ResizeSensor.detach = function(element) {
        if (element.resizeSensor) {
            element.removeChild(element.resizeSensor);
            delete element.resizeSensor;
            delete element.resizedAttached;
        }
    };

    // make available to common module loader
    if (typeof module !== 'undefined' && typeof module.exports !== 'undefined') {
        module.exports = ResizeSensor;
    }
    else {
        window.ResizeSensor = ResizeSensor;
    }

})();


/*!
 * Theia Sticky Sidebar v1.7.0
 * https://github.com/WeCodePixels/theia-sticky-sidebar
 *
 * Glues your website's sidebars, making them permanently visible while scrolling.
 *
 * Copyright 2013-2016 WeCodePixels and other contributors
 * Released under the MIT license
 */
;
(function ($) {
    $.fn.theiaStickySidebar = function (options) {
        var defaults = {
            'containerSelector': '',
            'additionalMarginTop': 0,
            'additionalMarginBottom': 0,
            'updateSidebarHeight': true,
            'minWidth': 0,
            'disableOnResponsiveLayouts': true,
            'sidebarBehavior': 'modern',
            'defaultPosition': 'relative',
            'namespace': 'TSS'
        };
        options = $.extend(defaults, options);

        // Validate options
        options.additionalMarginTop = parseInt(options.additionalMarginTop) || 0;
        options.additionalMarginBottom = parseInt(options.additionalMarginBottom) || 0;

        tryInitOrHookIntoEvents(options, this);

        // Try doing init, otherwise hook into window.resize and document.scroll and try again then.
        function tryInitOrHookIntoEvents(options, $that) {
            var success = tryInit(options, $that);

            if (!success) {
                console.log('TSS: Body width smaller than options.minWidth. Init is delayed.');

                $(document).on('scroll.' + options.namespace, function (options, $that) {
                    return function (evt) {
                        var success = tryInit(options, $that);

                        if (success) {
                            $(this).unbind(evt);
                        }
                    };
                }(options, $that));
                $(window).on('resize.' + options.namespace, function (options, $that) {
                    return function (evt) {
                        var success = tryInit(options, $that);

                        if (success) {
                            $(this).unbind(evt);
                        }
                    };
                }(options, $that))
            }
        }

        // Try doing init if proper conditions are met.
        function tryInit(options, $that) {
            if (options.initialized === true) {
                return true;
            }

            if ($('body').width() < options.minWidth) {
                return false;
            }

            init(options, $that);

            return true;
        }

        // Init the sticky sidebar(s).
        function init(options, $that) {
            options.initialized = true;

            // Add CSS
            var existingStylesheet = $('#theia-sticky-sidebar-stylesheet-' + options.namespace);
            if (existingStylesheet.length === 0) {
                $('head').append($('<style id="theia-sticky-sidebar-stylesheet-' + options.namespace + '">.theiaStickySidebar:after {content: ""; display: table; clear: both;}</style>'));
            }

            $that.each(function () {
                var o = {};

                o.sidebar = $(this);

                // Save options
                o.options = options || {};

                // Get container
                o.container = $(o.options.containerSelector);
                if (o.container.length == 0) {
                    o.container = o.sidebar.parent();
                }

                // Create sticky sidebar
                o.sidebar.parents().css('-webkit-transform', 'none'); // Fix for WebKit bug - https://code.google.com/p/chromium/issues/detail?id=20574
                o.sidebar.css({
                    'position': o.options.defaultPosition,
                    'overflow': 'visible',
                    // The "box-sizing" must be set to "content-box" because we set a fixed height to this element when the sticky sidebar has a fixed position.
                    '-webkit-box-sizing': 'border-box',
                    '-moz-box-sizing': 'border-box',
                    'box-sizing': 'border-box'
                });

                // Get the sticky sidebar element. If none has been found, then create one.
                o.stickySidebar = o.sidebar.find('.theiaStickySidebar');
                if (o.stickySidebar.length == 0) {
                    // Remove <script> tags, otherwise they will be run again when added to the stickySidebar.
                    var javaScriptMIMETypes = /(?:text|application)\/(?:x-)?(?:javascript|ecmascript)/i;
                    o.sidebar.find('script').filter(function (index, script) {
                        return script.type.length === 0 || script.type.match(javaScriptMIMETypes);
                    }).remove();

                    o.stickySidebar = $('<div>').addClass('theiaStickySidebar').append(o.sidebar.children());
                    o.sidebar.append(o.stickySidebar);
                }

                // Get existing top and bottom margins and paddings
                o.marginBottom = parseInt(o.sidebar.css('margin-bottom'));
                o.paddingTop = parseInt(o.sidebar.css('padding-top'));
                o.paddingBottom = parseInt(o.sidebar.css('padding-bottom'));

                // Add a temporary padding rule to check for collapsable margins.
                var collapsedTopHeight = o.stickySidebar.offset().top;
                var collapsedBottomHeight = o.stickySidebar.outerHeight();
                o.stickySidebar.css('padding-top', 1);
                o.stickySidebar.css('padding-bottom', 1);
                collapsedTopHeight -= o.stickySidebar.offset().top;
                collapsedBottomHeight = o.stickySidebar.outerHeight() - collapsedBottomHeight - collapsedTopHeight;
                if (collapsedTopHeight == 0) {
                    o.stickySidebar.css('padding-top', 0);
                    o.stickySidebarPaddingTop = 0;
                }
                else {
                    o.stickySidebarPaddingTop = 1;
                }

                if (collapsedBottomHeight == 0) {
                    o.stickySidebar.css('padding-bottom', 0);
                    o.stickySidebarPaddingBottom = 0;
                }
                else {
                    o.stickySidebarPaddingBottom = 1;
                }

                // We use this to know whether the user is scrolling up or down.
                o.previousScrollTop = null;

                // Scroll top (value) when the sidebar has fixed position.
                o.fixedScrollTop = 0;

                // Set sidebar to default values.
                resetSidebar();

                o.onScroll = function (o) {
                    // Stop if the sidebar isn't visible.
                    if (!o.stickySidebar.is(":visible")) {
                        return;
                    }

                    // Stop if the window is too small.
                    if ($('body').width() < o.options.minWidth) {
                        resetSidebar();
                        return;
                    }

                    // Stop if the sidebar width is larger than the container width (e.g. the theme is responsive and the sidebar is now below the content)
                    if (o.options.disableOnResponsiveLayouts) {
                        var sidebarWidth = o.sidebar.outerWidth(o.sidebar.css('float') == 'none');

                        if (sidebarWidth + 50 > o.container.width()) {
                            resetSidebar();
                            return;
                        }
                    }

                    var scrollTop = $(document).scrollTop();
                    var position = 'static';

                    // If the user has scrolled down enough for the sidebar to be clipped at the top, then we can consider changing its position.
                    if (scrollTop >= o.sidebar.offset().top + (o.paddingTop - o.options.additionalMarginTop)) {
                        // The top and bottom offsets, used in various calculations.
                        var offsetTop = o.paddingTop + options.additionalMarginTop;
                        var offsetBottom = o.paddingBottom + o.marginBottom + options.additionalMarginBottom;

                        // All top and bottom positions are relative to the window, not to the parent elemnts.
                        var containerTop = o.sidebar.offset().top;
                        var containerBottom = o.sidebar.offset().top + getClearedHeight(o.container);

                        // The top and bottom offsets relative to the window screen top (zero) and bottom (window height).
                        var windowOffsetTop = 0 + options.additionalMarginTop;
                        var windowOffsetBottom;

                        var sidebarSmallerThanWindow = (o.stickySidebar.outerHeight() + offsetTop + offsetBottom) < $(window).height();
                        if (sidebarSmallerThanWindow) {
                            windowOffsetBottom = windowOffsetTop + o.stickySidebar.outerHeight();
                        }
                        else {
                            windowOffsetBottom = $(window).height() - o.marginBottom - o.paddingBottom - options.additionalMarginBottom;
                        }

                        var staticLimitTop = containerTop - scrollTop + o.paddingTop;
                        var staticLimitBottom = containerBottom - scrollTop - o.paddingBottom - o.marginBottom;

                        var top = o.stickySidebar.offset().top - scrollTop;
                        var scrollTopDiff = o.previousScrollTop - scrollTop;

                        // If the sidebar position is fixed, then it won't move up or down by itself. So, we manually adjust the top coordinate.
                        if (o.stickySidebar.css('position') == 'fixed') {
                            if (o.options.sidebarBehavior == 'modern') {
                                top += scrollTopDiff;
                            }
                        }

                        if (o.options.sidebarBehavior == 'stick-to-top') {
                            top = options.additionalMarginTop;
                        }

                        if (o.options.sidebarBehavior == 'stick-to-bottom') {
                            top = windowOffsetBottom - o.stickySidebar.outerHeight();
                        }

                        if (scrollTopDiff > 0) { // If the user is scrolling up.
                            top = Math.min(top, windowOffsetTop);
                        }
                        else { // If the user is scrolling down.
                            top = Math.max(top, windowOffsetBottom - o.stickySidebar.outerHeight());
                        }

                        top = Math.max(top, staticLimitTop);

                        top = Math.min(top, staticLimitBottom - o.stickySidebar.outerHeight());

                        // If the sidebar is the same height as the container, we won't use fixed positioning.
                        var sidebarSameHeightAsContainer = o.container.height() == o.stickySidebar.outerHeight();

                        if (!sidebarSameHeightAsContainer && top == windowOffsetTop) {
                            position = 'fixed';
                        }
                        else if (!sidebarSameHeightAsContainer && top == windowOffsetBottom - o.stickySidebar.outerHeight()) {
                            position = 'fixed';
                        }
                        else if (scrollTop + top - o.sidebar.offset().top - o.paddingTop <= options.additionalMarginTop) {
                            // Stuck to the top of the page. No special behavior.
                            position = 'static';
                        }
                        else {
                            // Stuck to the bottom of the page.
                            position = 'absolute';
                        }
                    }

                    /*
                     * Performance notice: It's OK to set these CSS values at each resize/scroll, even if they don't change.
                     * It's way slower to first check if the values have changed.
                     */
                    if (position == 'fixed') {
                        var scrollLeft = $(document).scrollLeft();

                        o.stickySidebar.css({
                            'position': 'fixed',
                            'width': getWidthForObject(o.stickySidebar) + 'px',
                            'transform': 'translateY(' + top + 'px)',
                            'left': (o.sidebar.offset().left + parseInt(o.sidebar.css('padding-left')) - scrollLeft) + 'px',
                            'top': '0px'
                        });
                    }
                    else if (position == 'absolute') {
                        var css = {};

                        if (o.stickySidebar.css('position') != 'absolute') {
                            css.position = 'absolute';
                            css.transform = 'translateY(' + (scrollTop + top - o.sidebar.offset().top - o.stickySidebarPaddingTop - o.stickySidebarPaddingBottom) + 'px)';
                            css.top = '0px';
                        }

                        css.width = getWidthForObject(o.stickySidebar) + 'px';
                        css.left = '';

                        o.stickySidebar.css(css);
                    }
                    else if (position == 'static') {
                        resetSidebar();
                    }

                    if (position != 'static') {
                        if (o.options.updateSidebarHeight == true) {
                            o.sidebar.css({
                                'min-height': o.stickySidebar.outerHeight() + o.stickySidebar.offset().top - o.sidebar.offset().top + o.paddingBottom
                            });
                        }
                    }

                    o.previousScrollTop = scrollTop;
                };

                // Initialize the sidebar's position.
                o.onScroll(o);

                // Recalculate the sidebar's position on every scroll and resize.
                $(document).on('scroll.' + o.options.namespace, function (o) {
                    return function () {
                        o.onScroll(o);
                    };
                }(o));
                $(window).on('resize.' + o.options.namespace, function (o) {
                    return function () {
                        o.stickySidebar.css({'position': 'static'});
                        o.onScroll(o);
                    };
                }(o));

                // Recalculate the sidebar's position every time the sidebar changes its size.
                if (typeof ResizeSensor !== 'undefined') {
                    new ResizeSensor(o.stickySidebar[0], function (o) {
                        return function () {
                            o.onScroll(o);
                        };
                    }(o));
                }

                // Reset the sidebar to its default state
                function resetSidebar() {
                    o.fixedScrollTop = 0;
                    o.sidebar.css({
                        'min-height': '1px'
                    });
                    o.stickySidebar.css({
                        'position': 'static',
                        'width': '',
                        'transform': 'none'
                    });
                }

                // Get the height of a div as if its floated children were cleared. Note that this function fails if the floats are more than one level deep.
                function getClearedHeight(e) {
                    var height = e.height();

                    e.children().each(function () {
                        height = Math.max(height, $(this).height());
                    });

                    return height;
                }
            });
        }

        function getWidthForObject(object) {
            var width;

            try {
                width = object[0].getBoundingClientRect().width;
            }
            catch (err) {
            }

            if (typeof width === "undefined") {
                width = object.width();
            }

            return width;
        }

        return this;
    }
})(jQuery);

var ai_functions = true; if (typeof ai_debugging !== 'undefined') console.log ('AI FUNCTIONS LOADED');
