

if(typeof SAPO==='undefined'){window.SAPO={};}else{window.SAPO=window.SAPO;}
SAPO.namespace=function(ns){if(!ns||!ns.length){return null;}
var levels=ns.split(".");var nsobj=SAPO;for(var i=(levels[0]==="SAPO")?1:0;i<levels.length;++i){nsobj[levels[i]]=nsobj[levels[i]]||{};nsobj=nsobj[levels[i]];}
return nsobj;};SAPO.verify=function(ns,minVersion){if(!ns){return;}
var levels=ns.split(".");var nsobj=SAPO;for(var k=levels[0]==='SAPO'?1:0,m=levels.length;k<m;k++){nsobj=nsobj[levels[k]];if(!nsobj){throw new Error('SAPO.verify: '+ns+' not found');}}
if(!minVersion){return;}
if(typeof nsobj==='function'){nsobj=nsobj.prototype;}
var lhs=String(nsobj.version).match(/\d+/g)||[0];var rhs=String(minVersion).match(/\d+/g)||[0];for(k=0,m=Math.min(lhs.length,rhs.length);k<m;k++){if(lhs[k]<rhs[k]){throw new Error('SAPO.verify: '+ns+' has low version ('+nsobj.version+' < '+minVersion+')');}}
if(lhs.length<rhs.length){throw new Error('SAPO.verify: '+ns+' has low version ('+nsobj.version+' < '+minVersion+')');}};SAPO.Class=function(name,baseClass,properties){var derivedFunction=function(){if(this.__dont_init){return;}
if(this===window||!this){throw new Error('Call "new '+name+'(...);"');}
if(derivedFunction['abstract']){throw new Error("Abstract class: don't instantiate");}
if(baseClass){var abstractBackup=baseClass['abstract'];if(abstractBackup){baseClass['abstract']=false;}
baseClass.apply(this,arguments);if(abstractBackup){baseClass['abstract']=abstractBackup;}}
if(properties&&typeof properties.init==='function'){properties.init.apply(this,arguments);}};derivedFunction.name=derivedFunction.displayName=name;derivedFunction['abstract']=properties['abstract'];if(baseClass){baseClass.prototype.__dont_init=1;derivedFunction.prototype=new baseClass();delete baseClass.prototype.__dont_init;}
derivedFunction.prototype.toString=function(){return'[object '+name+']';};if(properties){SAPO.extendObj(derivedFunction.prototype,properties);}
return derivedFunction;};SAPO.safeCall=function(object,listener){function rethrow(exception){setTimeout(function(){if(exception.message){exception.message+='\n'+(exception.stacktrace||exception.stack||'');}
throw exception;},1);}
if(object===null){object=window;}
if(typeof listener==='string'&&typeof object[listener]==='function'){try{return object[listener].apply(object,[].slice.call(arguments,2));}catch(ex){rethrow(ex);}}else if(typeof listener==='function'){try{return listener.apply(object,[].slice.call(arguments,2));}catch(ex){rethrow(ex);}}else if(typeof object==='function'){try{return object.apply(window,[].slice.call(arguments,1));}catch(ex){rethrow(ex);}}};window.s$=function(element){if(arguments.length>1){for(var i=0,elements=[],length=arguments.length;i<length;i++){elements.push(s$(arguments[i]));}
return elements;}
if(typeof element==='string'){element=document.getElementById(element);}
return element;};Function.prototype.bindObj=function(){if(arguments.length<2&&arguments[0]===undefined){return this;}
var __method=this;var args=[];for(var i=0,total=arguments.length;i<total;i++){args.push(arguments[i]);}
var object=args.shift();var fn=function(){return __method.apply(object,args.concat(function(tmpArgs){var args2=[];for(var j=0,total=tmpArgs.length;j<total;j++){args2.push(tmpArgs[j]);}
return args2;}(arguments)));};fn.toString=function(){return String(__method);};fn.name=fn.displayName=__method.name;return fn;};Function.prototype.bindObjEvent=function(){var __method=this;var args=[];for(var i=0;i<arguments.length;i++){args.push(arguments[i]);}
var object=args.shift();return function(event){return __method.apply(object,[event||window.event].concat(args));};};Object.extend=function(destination,source){for(var property in source){destination[property]=source[property];}
return destination;};SAPO.extendObj=function(destination,source){if(source){for(var property in source){if(source.hasOwnProperty(property)){destination[property]=source[property];}}}
return destination;};if(typeof SAPO.Browser==='undefined'){SAPO.Browser={IE:false,GECKO:false,OPERA:false,SAFARI:false,KONQUEROR:false,CHROME:false,model:false,version:false,userAgent:false,init:function()
{this.detectBrowser();this.setDimensions();this.setReferrer();},setDimensions:function()
{var myWidth=0,myHeight=0;if(typeof window.innerWidth==='number'){myWidth=window.innerWidth;myHeight=window.innerHeight;}else if(document.documentElement&&(document.documentElement.clientWidth||document.documentElement.clientHeight)){myWidth=document.documentElement.clientWidth;myHeight=document.documentElement.clientHeight;}else if(document.body&&(document.body.clientWidth||document.body.clientHeight)){myWidth=document.body.clientWidth;myHeight=document.body.clientHeight;}
this.windowWidth=myWidth;this.windowHeight=myHeight;},setReferrer:function()
{this.referrer=document.referrer!==undefined?document.referrer.length>0?window.escape(document.referrer):false:false;},detectBrowser:function()
{var sAgent=navigator.userAgent;this.userAgent=sAgent;sAgent=sAgent.toLowerCase();if((new RegExp("applewebkit\/")).test(sAgent)){if((new RegExp("chrome\/")).test(sAgent)){this.CHROME=true;this.model='chrome';this.version=sAgent.replace(new RegExp("(.*)chrome\/([^\\s]+)(.*)"),"$2");this.cssPrefix='-webkit-';this.domPrefix='Webkit';}else{this.SAFARI=true;this.model='safari';this.version=sAgent.replace(new RegExp("(.*)applewebkit\/([^\\s]+)(.*)"),"$2");this.cssPrefix='-webkit-';this.domPrefix='Webkit';}}else if((new RegExp("opera")).test(sAgent)){this.OPERA=true;this.model='opera';this.version=sAgent.replace(new RegExp("(.*)opera.([^\\s$]+)(.*)"),"$2");this.cssPrefix='-o-';this.domPrefix='O';}else if((new RegExp("konqueror")).test(sAgent)){this.KONQUEROR=true;this.model='konqueror';this.version=sAgent.replace(new RegExp("(.*)konqueror\/([^;]+);(.*)"),"$2");this.cssPrefix='-khtml-';this.domPrefix='Khtml';}else if((new RegExp("msie\\ ")).test(sAgent)){this.IE=true;this.model='ie';this.version=sAgent.replace(new RegExp("(.*)\\smsie\\s([^;]+);(.*)"),"$2");this.cssPrefix='-ms-';this.domPrefix='ms';}else if((new RegExp("gecko")).test(sAgent)){this.GECKO=true;var re=new RegExp("(camino|chimera|epiphany|minefield|firefox|firebird|phoenix|galeon|iceweasel|k\\-meleon|seamonkey|netscape|songbird|sylera)");if(re.test(sAgent)){this.model=sAgent.match(re)[1];this.version=sAgent.replace(new RegExp("(.*)"+this.model+"\/([^;\\s$]+)(.*)"),"$2");this.cssPrefix='-moz-';this.domPrefix='Moz';}else{this.model='mozilla';var reVersion=new RegExp("(.*)rv:([^)]+)(.*)");if(reVersion.test(sAgent)){this.version=sAgent.replace(reVersion,"$2");}
this.cssPrefix='-moz-';this.domPrefix='Moz';}}},debug:function()
{var str="known browsers: (ie, gecko, opera, safari, konqueror) \n";str+=[this.IE,this.GECKO,this.OPERA,this.SAFARI,this.KONQUEROR]+"\n";str+="model -> "+this.model+"\n";str+="version -> "+this.version+"\n";str+="\n";str+="original UA -> "+this.userAgent;alert(str);}};SAPO.Browser.init();}
SAPO.logReferer=function(classURL){var thisOptions=SAPO.extendObj({s:'js.sapo.pt',swakt:'59a97a5f-0924-3720-a62e-0c44d9ea4f16',pg:false,swasection:false,swasubsection:'',dc:'',ref:false,etype:'libsapojs-view',swav:'1',swauv:'1',bcs:'1',bsr:'1',bul:'1',bje:'1',bfl:'1',debug:false},arguments[1]||{});if(typeof classURL!=='undefined'&&classURL!==null){if(!thisOptions.pg){thisOptions.pg=classURL;}
if(!thisOptions.swasection){thisOptions.swasection=classURL;}
if(!thisOptions.ref){thisOptions.ref=location.href;}
var waURI='http://wa.sl.pt/wa.gif?';var waURISSL='https://ssl.sapo.pt/wa.sl.pt/wa.gif?';var aQuery=['pg='+encodeURIComponent(thisOptions.pg),'swasection='+encodeURIComponent(thisOptions.swasection),'swasubsection='+encodeURIComponent(thisOptions.swasubsection),'dc='+encodeURIComponent(thisOptions.dc),'s='+thisOptions.s,'ref='+encodeURIComponent(thisOptions.ref),'swakt='+thisOptions.swakt,'etype='+encodeURIComponent(thisOptions.etype),'swav='+encodeURIComponent(thisOptions.swav),'swauv='+encodeURIComponent(thisOptions.swauv),'bcs='+encodeURIComponent(thisOptions.bcs),'bsr='+encodeURIComponent(thisOptions.bsr),'bul='+encodeURIComponent(thisOptions.bul),'bje='+encodeURIComponent(thisOptions.bje),'bfl='+encodeURIComponent(thisOptions.bfl),''];var waLogURI=((location.protocol==='https:')?waURISSL:waURI);var img=new Image();img.src=waLogURI+aQuery.join('&');}};SAPO._require=function(uri,callBack)
{if(typeof uri!=='string'){return;}
var script=document.createElement('script');script.type='text/javascript';var aHead=document.getElementsByTagName('HEAD');if(aHead.length>0){aHead[0].appendChild(script);}
if(document.addEventListener){script.onload=function(e){if(typeof callBack!=='undefined'){callBack();}};}else{script.onreadystatechange=function(e){if(this.readyState==='loaded'){if(typeof callBack!=='undefined'){callBack();}}};}
script.src=uri;};SAPO.require=function(reqArray,callBack)
{var objectsToCheck=[];var uriToAdd=[];var _isSAPOObject=function(param){if(typeof param==='string'){if(/^SAPO\./.test(param)){return true;}}
return false;};var _isObjectUri=function(param){if(typeof param==='object'&&param.constructor===Object){if(typeof param.uri==='string'){return true;}}
return false;};var _isObjectArray=function(param){if(typeof param==='object'&&param.constructor===Array){return true;}
return false;};var _parseSAPOObject=function(param){var aSAPO=param.split('.');var sapoURI=aSAPO.join('/');return'http://js.sapo.pt/'+sapoURI+'/';};var _parseObjectUri=function(param){return param.uri;};var _objectExists=function(objStr,ver){if(typeof objStr!=='undefined'){var aStrObj=objStr.split('.');var objParent=window;for(var k=0,aStrObjLength=aStrObj.length;k<aStrObjLength;k++){if(typeof objParent[aStrObj[k]]!=='undefined'){objParent=objParent[aStrObj[k]];}else{return false;}}
if(typeof ver!=='undefined'&&ver!==null){if(typeof objParent.version!=='undefined'){if(objParent.version===ver){return true;}else{return false;}}else{return true;}}
return true;}};var requestRecursive=function()
{if(uriToAdd.length>1){SAPO._require(uriToAdd[0],requestRecursive);uriToAdd.splice(0,1);}else if(uriToAdd.length===1){if(typeof callBack!=='undefined'){SAPO._require(uriToAdd[0],callBack);}else{SAPO._require(uriToAdd[0]);}
uriToAdd.splice(0,1);}else if(uriToAdd.length===0){if(typeof callBack!=='undefined'){callBack();}}};if(typeof reqArray!=='undefined'){var cur=false;var curURI=false;if(typeof reqArray==='string'){if(_isSAPOObject(reqArray)){if(!_objectExists(reqArray)){uriToAdd.push(_parseSAPOObject(reqArray));}}else{uriToAdd.push(reqArray);}}else{for(var i=0,reqArrayLength=reqArray.length;i<reqArrayLength;i++){cur=reqArray[i];if(_isSAPOObject(cur)){if(!_objectExists(cur)){objectsToCheck.push(cur);uriToAdd.push(_parseSAPOObject(cur));}}else if(_isObjectArray(cur)){if(cur.length>0){if(_isSAPOObject(cur[0])){if(!_objectExists(cur[0])){if(cur.length===2){uriToAdd.push(_parseSAPOObject(cur[0])+cur[1]+'/');}else{uriToAdd.push(_parseSAPOObject(cur[0]));}}}}}else{if(typeof cur==='string'){uriToAdd.push(cur);}else{if(_isObjectUri(cur)){if(typeof cur.check==='string'){if(typeof cur.version==='string'){if(!_objectExists(cur.check,cur.version)){uriToAdd.push(_parseObjectUri(cur));}}else{if(!_objectExists(cur.check)){uriToAdd.push(_parseObjectUri(cur));}}}else{uriToAdd.push(_parseObjectUri(cur));}}}}}}
if(arguments.length===3){if(typeof arguments[2]==='boolean'){if(arguments[2]===true){for(var l=0,uriToAddLength=uriToAdd.length;l<uriToAddLength;l++){SAPO._require(uriToAdd[l]);}
if(typeof callBack!=='undefined'){callBack();}
return;}}
requestRecursive();}else{requestRecursive();}}};
(function(window,undefined){'use strict';SAPO.namespace('Dom');if(SAPO.Dom.Css){return;}
var ua=navigator.userAgent.toLowerCase();var isNativeAndroidBrowser=ua.indexOf('android')!==-1&&ua.indexOf('chrome')===-1&&ua.indexOf('safari')!==-1;var isNode=(typeof Node==='object')?function(o){return o instanceof Node;}:function(o){return o&&typeof o==='object'&&typeof o.nodeType==='number'&&typeof o.nodeName==='string';};SAPO.Dom.Css={addRemoveClassName:function(elm,className,addRemState){if(addRemState){return SAPO.Dom.Css.addClassName(elm,className);}
SAPO.Dom.Css.removeClassName(elm,className);},addClassName:function(elm,className){elm=s$(elm);if(elm&&className){if(typeof elm.classList!=="undefined"){elm.classList.add(className);}
else if(!this.hasClassName(elm,className)){elm.className+=(elm.className?' ':'')+className;}}},removeClassName:function(elm,className){elm=s$(elm);if(elm&&className){if(typeof elm.classList!=="undefined"){elm.classList.remove(className);}else{if(typeof elm.className==="undefined"){return false;}
var elmClassName=elm.className,re=new RegExp("(^|\\s+)"+className+"(\\s+|$)");elmClassName=elmClassName.replace(re,' ');elmClassName=elmClassName.replace(/^\s+/,'').replace(/\s+$/,'');elm.className=elmClassName;}}},setClassName:function(elm,className,add){if(add){SAPO.Dom.Css.addClassName(elm,className);}
else{SAPO.Dom.Css.removeClassName(elm,className);}},hasClassName:function(elm,className){elm=s$(elm);if(elm&&className){if(typeof elm.classList!=="undefined"){return elm.classList.contains(className);}
else{if(typeof elm.className==="undefined"){return false;}
var elmClassName=elm.className;if(typeof elmClassName.length==="undefined"){return false;}
if(elmClassName.length>0){if(elmClassName===className){return true;}
else{var re=new RegExp("(^|\\s)"+className+"(\\s|$)");if(re.test(elmClassName)){return true;}}}}}
return false;},blinkClass:function(element,className,timeout,negate){element=s$(element);SAPO.Dom.Css.setClassName(element,className,!negate);setTimeout(function(){SAPO.Dom.Css.setClassName(element,className,negate);},Number(timeout)|100);},toggleClassName:function(elm,className,forceAdd){if(elm&&className){if(typeof elm.classList!=="undefined"){elm=s$(elm);if(elm!==null){elm.classList.toggle(className);}
return true;}}
if(typeof forceAdd!=='undefined'){if(forceAdd===true){this.addClassName(elm,className);}
else if(forceAdd===false){this.removeClassName(elm,className);}}else{if(this.hasClassName(elm,className)){this.removeClassName(elm,className);}
else{this.addClassName(elm,className);}}},setOpacity:function(elm,value){elm=s$(elm);if(elm!==null){var val=1;if(!isNaN(Number(value))){if(value<=0){val=0;}
else if(value<=1){val=value;}
else if(value<=100){val=value/100;}
else{val=1;}}
if(typeof elm.style.opacity!=='undefined'){elm.style.opacity=val;}
else{elm.style.filter="alpha(opacity:"+(val*100|0)+")";}}},_camelCase:function(str){return str?str.replace(/-(\w)/g,function(_,$1){return $1.toUpperCase();}):str;},getStyle:function(elm,style){elm=s$(elm);if(elm!==null){style=style==='float'?'cssFloat':SAPO.Dom.Css._camelCase(style);var value=elm.style[style];if(window.getComputedStyle&&(!value||value==='auto')){var css=getComputedStyle(elm,null);value=css?css[style]:null;}
else if(!value&&elm.currentStyle){value=elm.currentStyle[style];if(value==='auto'&&(style==='width'||style==='height')){value=elm["offset"+style.charAt(0).toUpperCase()+style.slice(1)]+"px";}}
if(style==='opacity'){return value?parseFloat(value,10):1.0;}
else if(style==='borderTopWidth'||style==='borderBottomWidth'||style==='borderRightWidth'||style==='borderLeftWidth'){if(value==='thin'){return'1px';}
else if(value==='medium'){return'3px';}
else if(value==='thick'){return'5px';}}
return value==='auto'?null:value;}},setStyle:function(elm,style){elm=s$(elm);if(elm!==null){if(typeof style==='string'){elm.style.cssText+='; '+style;if(style.indexOf('opacity')!==-1){this.setOpacity(elm,style.match(/opacity:\s*(\d?\.?\d*)/)[1]);}}
else{for(var prop in style){if(style.hasOwnProperty(prop)){if(prop==='opacity'){this.setOpacity(elm,style[prop]);}
else{if(prop==='float'||prop==='cssFloat'){if(typeof elm.style.styleFloat==='undefined'){elm.style.cssFloat=style[prop];}
else{elm.style.styleFloat=style[prop];}}else{elm.style[prop]=style[prop];}}}}}}},show:function(elm,forceDisplayProperty){elm=s$(elm);if(elm!==null){elm.style.display=(forceDisplayProperty)?forceDisplayProperty:'';}},hide:function(elm){elm=s$(elm);if(elm!==null){elm.style.display='none';}},showHide:function(elm,show){elm=s$(elm);if(elm){elm.style.display=show?'':'none';}},toggle:function(elm,forceShow){elm=s$(elm);if(elm!==null){if(typeof forceShow!=='undefined'){if(forceShow===true){this.show(elm);}else{this.hide(elm);}}
else{if(elm.style.display==='none'){this.show(elm);}
else{this.hide(elm);}}}},_getRefTag:function(head){if(head.firstElementChild){return head.firstElementChild;}
for(var child=head.firstChild;child;child=child.nextSibling){if(child.nodeType===1){return child;}}
return null;},appendStyleTag:function(selector,style,options){options=SAPO.extendObj({type:'text/css',force:false},options||{});var styles=document.getElementsByTagName("style"),oldStyle=false,setStyle=true,i,l;for(i=0,l=styles.length;i<l;i++){oldStyle=styles[i].innerHTML;if(oldStyle.indexOf(selector)>=0){setStyle=false;}}
if(setStyle){var defStyle=document.createElement("style"),head=document.getElementsByTagName("head")[0],refTag=false,styleStr='';defStyle.type=options.type;styleStr+=selector+" {";styleStr+=style;styleStr+="} ";if(typeof defStyle.styleSheet!=="undefined"){defStyle.styleSheet.cssText=styleStr;}else{defStyle.appendChild(document.createTextNode(styleStr));}
if(options.force){head.appendChild(defStyle);}else{refTag=this._getRefTag(head);if(refTag){head.insertBefore(defStyle,refTag);}}}},appendStylesheet:function(path,options){options=SAPO.extendObj({media:'screen',type:'text/css',force:false},options||{});var refTag,style=document.createElement("link"),head=document.getElementsByTagName("head")[0];style.media=options.media;style.type=options.type;style.href=path;style.rel="Stylesheet";if(options.force){head.appendChild(style);}
else{refTag=this._getRefTag(head);if(refTag){head.insertBefore(style,refTag);}}},_loadingCSSFiles:{},_loadedCSSFiles:{},appendStylesheetCb:function(url,callback){if(!url){return callback(url);}
if(SAPO.Dom.Css._loadedCSSFiles[url]){return callback(url);}
var cbs=SAPO.Dom.Css._loadingCSSFiles[url];if(cbs){return cbs.push(callback);}
SAPO.Dom.Css._loadingCSSFiles[url]=[callback];var linkEl=document.createElement('link');linkEl.type='text/css';linkEl.rel='stylesheet';linkEl.href=url;var headEl=document.getElementsByTagName('head')[0];headEl.appendChild(linkEl);var innerCb=function(frameEl){var url=this;SAPO.Dom.Css._loadedCSSFiles[url]=true;var callbacks=SAPO.Dom.Css._loadingCSSFiles[url];for(var i=0,f=callbacks.length;i<f;++i){callbacks[i](url);}
delete SAPO.Dom.Css._loadingCSSFiles[url];if(frameEl&&isNode(frameEl)){frameEl.parentNode.removeChild(frameEl);}};if(!isNativeAndroidBrowser){var imgEl=document.createElement('img');imgEl.onerror=innerCb.bindObj(url);imgEl.src=url;}
else{var frameEl=document.createElement('iframe');frameEl.style.display='none';frameEl.onerror=innerCb;frameEl.onload=innerCb.bindObj(url,frameEl);document.body.appendChild(frameEl);}},decToHex:function(dec){var normalizeTo2=function(val){if(val.length===1){val='0'+val;}
val=val.toUpperCase();return val;};if(typeof dec==='object'){var rDec=normalizeTo2(parseInt(dec.r,10).toString(16));var gDec=normalizeTo2(parseInt(dec.g,10).toString(16));var bDec=normalizeTo2(parseInt(dec.b,10).toString(16));return rDec+gDec+bDec;}
else{dec+='';var rgb=dec.match(/\((\d+),\s?(\d+),\s?(\d+)\)/);if(rgb!==null){return normalizeTo2(parseInt(rgb[1],10).toString(16))+
normalizeTo2(parseInt(rgb[2],10).toString(16))+
normalizeTo2(parseInt(rgb[3],10).toString(16));}
else{return normalizeTo2(parseInt(dec,10).toString(16));}}},hexToDec:function(hex){if(hex.indexOf('#')===0){hex=hex.substr(1);}
if(hex.length===6){return{r:parseInt(hex.substr(0,2),16),g:parseInt(hex.substr(2,2),16),b:parseInt(hex.substr(4,2),16)};}
else if(hex.length===3){return{r:parseInt(hex.charAt(0)+hex.charAt(0),16),g:parseInt(hex.charAt(1)+hex.charAt(1),16),b:parseInt(hex.charAt(2)+hex.charAt(2),16)};}
else if(hex.length<=2){return parseInt(hex,16);}},getPropertyFromStylesheet:function(selector,property){var rule=SAPO.Dom.Css.getRuleFromStylesheet(selector);if(rule){return rule.style[property];}
return null;},getPropertyFromStylesheet2:function(selector,property){var rules=SAPO.Dom.Css.getRulesFromStylesheet(selector);rules.forEach(function(rule){var x=rule.style[property];if(x!==null&&x!==undefined){return x;}});return null;},getRuleFromStylesheet:function(selector){var sheet,rules,ri,rf,rule;var s=document.styleSheets;if(!s){return null;}
for(var si=0,sf=document.styleSheets.length;si<sf;++si){sheet=document.styleSheets[si];rules=sheet.rules?sheet.rules:sheet.cssRules;if(!rules){return null;}
for(ri=0,rf=rules.length;ri<rf;++ri){rule=rules[ri];if(!rule.selectorText){continue;}
if(rule.selectorText===selector){return rule;}}}
return null;},getRulesFromStylesheet:function(selector){var res=[];var sheet,rules,ri,rf,rule;var s=document.styleSheets;if(!s){return res;}
for(var si=0,sf=document.styleSheets.length;si<sf;++si){sheet=document.styleSheets[si];rules=sheet.rules?sheet.rules:sheet.cssRules;if(!rules){return null;}
for(ri=0,rf=rules.length;ri<rf;++ri){rule=rules[ri];if(!rule.selectorText){continue;}
if(rule.selectorText===selector){res.push(rule);}}}
return res;},getPropertiesFromRule:function(selector){var rule=this.getRuleFromStylesheet(selector);var props={};var prop,i,f;rule=rule.style.cssText;var parts=rule.split(';');var steps,val,pre,pos;for(i=0,f=parts.length;i<f;++i){if(parts[i].charAt(0)===' '){parts[i]=parts[i].substring(1);}
steps=parts[i].split(':');prop=this._camelCase(steps[0].toLowerCase());val=steps[1];if(val){val=val.substring(1);if(prop==='padding'||prop==='margin'||prop==='borderWidth'){if(prop==='borderWidth'){pre='border';pos='Width';}
else{pre=prop;pos='';}
if(val.indexOf(' ')!==-1){val=val.split(' ');props[pre+'Top'+pos]=val[0];props[pre+'Bottom'+pos]=val[0];props[pre+'Left'+pos]=val[1];props[pre+'Right'+pos]=val[1];}
else{props[pre+'Top'+pos]=val;props[pre+'Bottom'+pos]=val;props[pre+'Left'+pos]=val;props[pre+'Right'+pos]=val;}}
else if(prop==='borderRadius'){if(val.indexOf(' ')!==-1){val=val.split(' ');props.borderTopLeftRadius=val[0];props.borderBottomRightRadius=val[0];props.borderTopRightRadius=val[1];props.borderBottomLeftRadius=val[1];}
else{props.borderTopLeftRadius=val;props.borderTopRightRadius=val;props.borderBottomLeftRadius=val;props.borderBottomRightRadius=val;}}
else{props[prop]=val;}}}
return props;},changeFontSize:function(selector,delta,op,minVal,maxVal){var e;if(typeof selector!=='string'){e='1st argument must be a CSS selector rule.';}
else if(typeof delta!=='number'){e='2nd argument must be a number.';}
else if(op!==undefined&&op!=='+'&&op!=='*'){e='3rd argument must be one of "+", "*".';}
else if(minVal!==undefined&&(typeof minVal!=='number'||minVal<=0)){e='4th argument must be a positive number.';}
else if(maxVal!==undefined&&(typeof maxVal!=='number'||maxVal<maxVal)){e='5th argument must be a positive number greater than minValue.';}
if(e){throw new TypeError(e);}
var val,el,els=SAPO.Dom.Selector.select(selector);if(minVal===undefined){minVal=1;}
op=(op==='*')?function(a,b){return a*b;}:function(a,b){return a+b;};for(var i=0,f=els.length;i<f;++i){el=els[i];val=parseFloat(SAPO.Dom.Css.getStyle(el,'fontSize'));val=op(val,delta);if(val<minVal){continue;}
if(typeof maxVal==='number'&&val>maxVal){continue;}
el.style.fontSize=val+'px';}}};})(window);
SAPO.namespace('Dom');SAPO.Dom.Element={get:function(elm){if(typeof elm!=='undefined'){if(typeof elm==='string'){return document.getElementById(elm);}
return elm;}
return null;},create:function(tag,properties){var el=document.createElement(tag);SAPO.extendObj(el,properties);return el;},remove:function(el){var parEl;if(el&&(parEl=el.parentNode)){parEl.removeChild(el);}},scrollTo:function(elm){elm=this.get(elm);if(elm){if(elm.scrollIntoView){return elm.scrollIntoView();}
var elmOffset={},elmTop=0,elmLeft=0;do{elmTop+=elm.offsetTop||0;elmLeft+=elm.offsetLeft||0;elm=elm.offsetParent;}while(elm);elmOffset={x:elmLeft,y:elmTop};window.scrollTo(elmOffset.x,elmOffset.y);}},offsetTop:function(elm){elm=this.get(elm);var offset=elm.offsetTop;while(elm.offsetParent){if(elm.offsetParent.tagName.toLowerCase()!=="body"){elm=elm.offsetParent;offset+=elm.offsetTop;}else{break;}}
return offset;},offsetLeft:function(elm){elm=this.get(elm);var offset=elm.offsetLeft;while(elm.offsetParent){if(elm.offsetParent.tagName.toLowerCase()!=="body"){elm=elm.offsetParent;offset+=elm.offsetLeft;}else{break;}}
return offset;},positionedOffset:function(element){var valueTop=0,valueLeft=0;element=this.get(element);do{valueTop+=element.offsetTop||0;valueLeft+=element.offsetLeft||0;element=element.offsetParent;if(element){if(element.tagName.toLowerCase()==='body'){break;}
var value=element.style.position;if(!value&&element.currentStyle){value=element.currentStyle.position;}
if((!value||value==='auto')&&typeof getComputedStyle!=='undefined'){var css=getComputedStyle(element,null);value=css?css.position:null;}
if(value==='relative'||value==='absolute'){break;}}}while(element);return[valueLeft,valueTop];},offset:function(elm){return[this.offsetLeft(elm),this.offsetTop(elm)];},scroll:function(elm){elm=elm?s$(elm):document.body;return[((!window.pageXOffset)?elm.scrollLeft:window.pageXOffset),((!window.pageYOffset)?elm.scrollTop:window.pageYOffset)];},_getPropPx:function(cs,prop){var n,c;var val=cs.getPropertyValue?cs.getPropertyValue(prop):cs[prop];if(!val){n=0;}
else{c=val.indexOf('px');if(c===-1){n=0;}
else{n=parseInt(val,10);}}
return n;},offset2:function(el){el=s$(el);var bProp=['border-left-width','border-top-width'];var res=[0,0];var dRes,bRes,parent,cs;var getPropPx=SAPO.Dom.Element._getPropPx;do{cs=window.getComputedStyle?window.getComputedStyle(el,null):el.currentStyle;dRes=[el.offsetLeft|0,el.offsetTop|0];bRes=[getPropPx(cs,bProp[0]),getPropPx(cs,bProp[1])];if(SAPO.Browser.OPERA){res[0]+=dRes[0];res[1]+=dRes[1];}
else{res[0]+=dRes[0]+bRes[0];res[1]+=dRes[1]+bRes[1];}
parent=el.offsetParent;}while(el=parent);bRes=[getPropPx(cs,bProp[0]),getPropPx(cs,bProp[1])];if(SAPO.Browser.OPERA){}
else if(SAPO.Browser.GECKO){res[0]+=bRes[0];res[1]+=bRes[1];}
else{res[0]-=bRes[0];res[1]-=bRes[1];}
return res;},hasAttribute:function(elm,attr){return elm.hasAttribute?elm.hasAttribute(attr):!!elm.getAttribute(attr);},insertAfter:function(newElm,targetElm){if(targetElm=this.get(targetElm)){targetElm.parentNode.insertBefore(newElm,targetElm.nextSibling);}},insertTop:function(newElm,targetElm){if(targetElm=this.get(targetElm)){targetElm.insertBefore(newElm,targetElm.firstChild);}},textContent:function(node){node=s$(node);var text;switch(node&&node.nodeType){case 9:return this.textContent(node.documentElement||node.body&&node.body.parentNode||node.body);case 1:text=node.innerText;if(typeof text!=='undefined'){return text;}
case 11:text=node.textContent;if(typeof text!=='undefined'){return text;}
if(node.firstChild===node.lastChild){return this.textContent(node.firstChild);}
text=[];for(var k=0,child,cs=node.childNodes,m=cs.length;k<m,child=cs[k];k++){text.push(this.textContent(child));}
return text.join('');case 3:case 4:return node.nodeValue;}
return'';},setTextContent:function(node,text){node=s$(node);switch(node&&node.nodeType)
{case 1:if('innerText'in node){node.innerText=text;break;}
case 11:if('textContent'in node){node.textContent=text;break;}
case 9:while(node.firstChild){node.removeChild(node.firstChild);}
if(text!==''){var doc=node.ownerDocument||node;node.appendChild(doc.createTextNode(text));}
break;case 3:case 4:node.nodeValue=text;break;}},isLink:function(element){var b=element&&element.nodeType===1&&((/^a|area$/i).test(element.tagName)||element.hasAttributeNS&&element.hasAttributeNS('http://www.w3.org/1999/xlink','href'));return!!b;},isAncestorOf:function(ancestor,node){if(!node||!ancestor){return false;}
if(node.compareDocumentPosition){return(ancestor.compareDocumentPosition(node)&0x10)!==0;}
while(node=node.parentNode){if(node===ancestor){return true;}}
return false;},descendantOf:function(node,descendant){return node!==descendant&&this.isAncestorOf(node,descendant);},firstElementChild:function(elm){if(!elm){return null;}
if('firstElementChild'in elm){return elm.firstElementChild;}
var child=elm.firstChild;while(child&&child.nodeType!==1){child=child.nextSibling;}
return child;},lastElementChild:function(elm){if(!elm){return null;}
if('lastElementChild'in elm){return elm.lastElementChild;}
var child=elm.lastChild;while(child&&child.nodeType!==1){child=child.previousSibling;}
return child;},nextElementSibling:function(node){var sibling=null;if(!node){return sibling;}
if("nextElementSibling"in node){return node.nextElementSibling;}else{sibling=node.nextSibling;while(sibling&&sibling.nodeType!==1){sibling=sibling.nextSibling;}
return sibling;}},previousElementSibling:function(node){var sibling=null;if(!node){return sibling;}
if("previousElementSibling"in node){return node.previousElementSibling;}else{sibling=node.previousSibling;while(sibling&&sibling.nodeType!==1){sibling=sibling.previousSibling;}
return sibling;}},elementWidth:function(element){if(typeof element==="string"){element=document.getElementById(element);}
return element.offsetWidth;},elementHeight:function(element){if(typeof element==="string"){element=document.getElementById(element);}
return element.offsetHeight;},elementLeft:function(element){if(typeof element==="string"){element=document.getElementById(element);}
return element.offsetLeft;},elementTop:function(element){if(typeof element==="string"){element=document.getElementById(element);}
return element.offsetTop;},elementDimensions:function(element){if(typeof element==="string"){element=document.getElementById(element);}
return Array(element.offsetWidth,element.offsetHeight);},clonePosition:function(cloneTo,cloneFrom){cloneTo.style.top=this.offsetTop(cloneFrom)+'px';cloneTo.style.left=this.offsetLeft(cloneFrom)+'px';return cloneTo;},ellipsizeText:function(element,ellipsis){if(element=s$(element)){while(element&&element.scrollHeight>(element.offsetHeight+8)){element.textContent=element.textContent.replace(/(\s+\S+)\s*$/,replace||'\u2026');}}},findUpwardsByClass:function(element,className){var re=new RegExp("(^|\\s)"+className+"(\\s|$)");while(true){if(typeof(element.className)!=='undefined'&&re.test(element.className)){return element;}
else{element=element.parentNode;if(!element||element.nodeType!==1){return false;}}}},findUpwardsByTag:function(element,tag){while(true){if(element&&element.nodeName.toUpperCase()===tag.toUpperCase()){return element;}else{element=element.parentNode;if(!element||element.nodeType!==1){return false;}}}},findUpwardsById:function(element,id){while(true){if(typeof(element.id)!=='undefined'&&element.id===id){return element;}else{element=element.parentNode;if(!element||element.nodeType!==1){return false;}}}},getChildrenText:function(el,removeIt){var node,j,part,nodes=el.childNodes,jLen=nodes.length,text='';if(!el){return text;}
for(j=0;j<jLen;++j){node=nodes[j];if(!node){continue;}
if(node.nodeType===3){part=this._trimString(String(node.data));if(part.length>0){text+=part;if(removeIt){el.removeChild(node);}}
else{el.removeChild(node);}}}
return text;},_trimString:function(text){return(String.prototype.trim)?text.trim():text.replace(/^\s*/,'').replace(/\s*$/,'');},getSelectValues:function(select){var selectEl=s$(select);var values=[];for(var i=0;i<selectEl.options.length;++i){values.push(selectEl.options[i].value);}
return values;},_normalizeData:function(data){var d,data2=[];for(var i=0,f=data.length;i<f;++i){d=data[i];if(!(d instanceof Array)){d=[d,d];}
else if(d.length===1){d.push(d[0]);}
data2.push(d);}
return data2;},fillSelect:function(container,data,skipEmpty,defaultValue){var containerEl=s$(container);if(!containerEl){return;}
containerEl.innerHTML='';var d,optionEl;if(!skipEmpty){optionEl=document.createElement('option');optionEl.setAttribute('value','');containerEl.appendChild(optionEl);}
data=SAPO.Dom.Element._normalizeData(data);for(var i=0,f=data.length;i<f;++i){d=data[i];optionEl=document.createElement('option');optionEl.setAttribute('value',d[0]);if(d.length>2){optionEl.setAttribute('extra',d[2]);}
optionEl.appendChild(document.createTextNode(d[1]));if(d[0]===defaultValue){optionEl.setAttribute('selected','selected');}
containerEl.appendChild(optionEl);}},fillSelect2:function(ctn,opts){ctn=s$(ctn);ctn.innerHTML='';var defs={skipEmpty:false,skipCreate:false,emptyLabel:'none',createLabel:'create',optionsGroupLabel:'groups',emptyOptionsGroupLabel:'none exist',defaultValue:''};if(!opts){throw'param opts is a requirement!';}
if(!opts.data){throw'opts.data is a requirement!';}
opts=SAPO.extendObj(defs,opts);var optionEl,optGroupEl,d;var optGroupValuesEl=document.createElement('optgroup');optGroupValuesEl.setAttribute('label',opts.optionsGroupLabel);opts.data=SAPO.Dom.Element._normalizeData(opts.data);if(!opts.skipCreate){opts.data.unshift(['$create$',opts.createLabel]);}
if(!opts.skipEmpty){opts.data.unshift(['',opts.emptyLabel]);}
for(var i=0,f=opts.data.length;i<f;++i){d=opts.data[i];optionEl=document.createElement('option');optionEl.setAttribute('value',d[0]);optionEl.appendChild(document.createTextNode(d[1]));if(d[0]===opts.defaultValue){optionEl.setAttribute('selected','selected');}
if(d[0]===''||d[0]==='$create$'){ctn.appendChild(optionEl);}
else{optGroupValuesEl.appendChild(optionEl);}}
var lastValIsNotOption=function(data){var lastVal=data[data.length-1][0];return(lastVal===''||lastVal==='$create$');};if(lastValIsNotOption(opts.data)){optionEl=document.createElement('option');optionEl.setAttribute('value','$dummy$');optionEl.setAttribute('disabled','disabled');optionEl.appendChild(document.createTextNode(opts.emptyOptionsGroupLabel));optGroupValuesEl.appendChild(optionEl);}
ctn.appendChild(optGroupValuesEl);var addOption=function(v,l){var optionEl=ctn.options[ctn.options.length-1];if(optionEl.getAttribute('disabled')){optionEl.parentNode.removeChild(optionEl);}
optionEl=document.createElement('option');optionEl.setAttribute('value',v);optionEl.appendChild(document.createTextNode(l));optGroupValuesEl.appendChild(optionEl);ctn.options[ctn.options.length-1].setAttribute('selected',true);};if(!opts.skipCreate){ctn.onchange=function(){if((ctn.value==='$create$')&&(typeof opts.onCreate==='function')){opts.onCreate(ctn,addOption);}};}},fillRadios:function(insertAfterEl,name,data,skipEmpty,defaultValue,splitEl){var afterEl=s$(insertAfterEl);afterEl=afterEl.nextSibling;while(afterEl&&afterEl.nodeType!==1){afterEl=afterEl.nextSibling;}
var containerEl=document.createElement('span');if(afterEl){afterEl.parentNode.insertBefore(containerEl,afterEl);}
else{s$(insertAfterEl).appendChild(containerEl);}
data=SAPO.Dom.Element._normalizeData(data);if(name.substring(name.length-1)!==']'){name+='[]';}
var d,inputEl;if(!skipEmpty){inputEl=document.createElement('input');inputEl.setAttribute('type','radio');inputEl.setAttribute('name',name);inputEl.setAttribute('value','');containerEl.appendChild(inputEl);if(splitEl){containerEl.appendChild(document.createElement(splitEl));}}
for(var i=0;i<data.length;++i){d=data[i];inputEl=document.createElement('input');inputEl.setAttribute('type','radio');inputEl.setAttribute('name',name);inputEl.setAttribute('value',d[0]);containerEl.appendChild(inputEl);containerEl.appendChild(document.createTextNode(d[1]));if(splitEl){containerEl.appendChild(document.createElement(splitEl));}
if(d[0]===defaultValue){inputEl.checked=true;}}
return containerEl;},fillChecks:function(insertAfterEl,name,data,defaultValue,splitEl){var afterEl=s$(insertAfterEl);afterEl=afterEl.nextSibling;while(afterEl&&afterEl.nodeType!==1){afterEl=afterEl.nextSibling;}
var containerEl=document.createElement('span');if(afterEl){afterEl.parentNode.insertBefore(containerEl,afterEl);}
else{s$(insertAfterEl).appendChild(containerEl);}
data=SAPO.Dom.Element._normalizeData(data);if(name.substring(name.length-1)!==']'){name+='[]';}
var d,inputEl;for(var i=0;i<data.length;++i){d=data[i];inputEl=document.createElement('input');inputEl.setAttribute('type','checkbox');inputEl.setAttribute('name',name);inputEl.setAttribute('value',d[0]);containerEl.appendChild(inputEl);containerEl.appendChild(document.createTextNode(d[1]));if(splitEl){containerEl.appendChild(document.createElement(splitEl));}
if(d[0]===defaultValue){inputEl.checked=true;}}
return containerEl;},parentIndexOf:function(parentEl,childEl){var node,idx=0;for(var i=0,f=parentEl.childNodes.length;i<f;++i){node=parentEl.childNodes[i];if(node.nodeType===1){if(node===childEl){return idx;}
++idx;}}
return-1;},nextSiblings:function(elm){if(typeof(elm)==="string"){elm=document.getElementById(elm);}
if(typeof(elm)==='object'&&elm!==null&&elm.nodeType&&elm.nodeType===1){var elements=[],siblings=elm.parentNode.children,index=SAPO.Dom.Element.parentIndexOf(elm.parentNode,elm);for(var i=++index,len=siblings.length;i<len;i++){elements.push(siblings[i]);}
return elements;}
return[];},previousSiblings:function(elm){if(typeof(elm)==="string"){elm=document.getElementById(elm);}
if(typeof(elm)==='object'&&elm!==null&&elm.nodeType&&elm.nodeType===1){var elements=[],siblings=elm.parentNode.children,index=SAPO.Dom.Element.parentIndexOf(elm.parentNode,elm);for(var i=0,len=index;i<len;i++){elements.push(siblings[i]);}
return elements;}
return[];},siblings:function(elm){if(typeof(elm)==="string"){elm=document.getElementById(elm);}
if(typeof(elm)==='object'&&elm!==null&&elm.nodeType&&elm.nodeType===1){var elements=[],siblings=elm.parentNode.children;for(var i=0,len=siblings.length;i<len;i++){if(elm!==siblings[i]){elements.push(siblings[i]);}}
return elements;}
return[];},childElementCount:function(elm){elm=s$(elm);if('childElementCount'in elm){return elm.childElementCount;}
if(!elm){return 0;}
return this.siblings(elm).length+1;},appendHTML:function(elm,html){var temp=document.createElement('div');temp.innerHTML=html;var tempChildren=temp.children;for(var i=0;i<tempChildren.length;i++){elm.appendChild(tempChildren[i]);}},prependHTML:function(elm,html){var temp=document.createElement('div');temp.innerHTML=html;var first=elm.firstChild;var tempChildren=temp.children;for(var i=tempChildren.length-1;i>=0;i--){elm.insertBefore(tempChildren[i],first);first=elm.firstChild;}},htmlToFragment:function(html){if(typeof document.createRange==='function'&&typeof Range.prototype.createContextualFragment==='function'){this.htmlToFragment=function(html){var range;if(typeof html!=='string'){return document.createDocumentFragment();}
range=document.createRange();range.selectNode(document.body);return range.createContextualFragment(html);};}else{this.htmlToFragment=function(html){var fragment=document.createDocumentFragment(),tempElement,current;if(typeof html!=='string'){return fragment;}
tempElement=document.createElement('div');tempElement.innerHTML=html;while(current=tempElement.firstChild){fragment.appendChild(current);}
return fragment;};}
return this.htmlToFragment.call(this,html);},data:function(selector){if(typeof selector!=='object'&&typeof selector!=='string'){throw'[SAPO.Dom.Element.data] :: Invalid selector defined';}
if(typeof selector==='object'){this._element=selector;}else{this._element=SAPO.Dom.Selector.select(selector);if(this._element.length<=0){throw"[SAPO.Dom.Element.data] :: Can't find any element with the specified selector";}
this._element=this._element[0];}
var dataset={};var
attributesElements=this._element.dataset||this._element.attributes||{},prop;var propName,i;for(prop in attributesElements){if((("hasOwnProperty"in attributesElements)&&!attributesElements.hasOwnProperty(prop))||typeof attributesElements[prop]==='undefined'){continue;}else if(typeof attributesElements[prop]==='object'){prop=attributesElements[prop].name||prop;if(((attributesElements[prop].name||attributesElements[prop].nodeValue)&&(prop.indexOf('data-')!==0))||!(attributesElements[prop].nodeValue||attributesElements[prop].value||attributesElements[prop])){continue;}}
propName=prop.replace('data-','');if(propName.indexOf('-')!==-1){propName=propName.split("-");for(i=1;i<propName.length;i+=1){propName[i]=propName[i].substr(0,1).toUpperCase()+propName[i].substr(1);}
propName=propName.join('');}
dataset[propName]=attributesElements[prop].nodeValue||attributesElements[prop].value||attributesElements[prop];if(dataset[propName]==="true"||dataset[propName]==="false"){dataset[propName]=(dataset[propName]==='true');}}
return dataset;},moveCursorTo:function(el,t){if(el.setSelectionRange){el.setSelectionRange(t,t);}
else{var range=el.createTextRange();range.collapse(true);range.moveEnd('character',t);range.moveStart('character',t);range.select();}}};
SAPO.namespace('Dom');SAPO.Dom.Event={KEY_BACKSPACE:8,KEY_TAB:9,KEY_RETURN:13,KEY_ESC:27,KEY_LEFT:37,KEY_UP:38,KEY_RIGHT:39,KEY_DOWN:40,KEY_DELETE:46,KEY_HOME:36,KEY_END:35,KEY_PAGEUP:33,KEY_PAGEDOWN:34,KEY_INSERT:45,element:function(ev)
{var node=ev.target||(ev.type=='mouseout'&&ev.fromElement)||(ev.type=='mouseleave'&&ev.fromElement)||(ev.type=='mouseover'&&ev.toElement)||(ev.type=='mouseenter'&&ev.toElement)||ev.srcElement||null;return node&&(node.nodeType==3||node.nodeType==4)?node.parentNode:node;},relatedTarget:function(ev){var node=ev.relatedTarget||(ev.type=='mouseout'&&ev.toElement)||(ev.type=='mouseleave'&&ev.toElement)||(ev.type=='mouseover'&&ev.fromElement)||(ev.type=='mouseenter'&&ev.fromElement)||null;return node&&(node.nodeType==3||node.nodeType==4)?node.parentNode:node;},findElement:function(ev,elmTagName,force)
{var node=this.element(ev);while(true){if(node.nodeName.toLowerCase()===elmTagName.toLowerCase()){return node;}else{node=node.parentNode;if(!node){if(force){return false;}
return document;}
if(!node.parentNode){if(force){return false;}
return document;}}}},fire:function(element,eventName,memo)
{element=s$(element);var ev,nativeEvents;if(document.createEvent){nativeEvents={"DOMActivate":true,"DOMFocusIn":true,"DOMFocusOut":true,"focus":true,"focusin":true,"focusout":true,"blur":true,"load":true,"unload":true,"abort":true,"error":true,"select":true,"change":true,"submit":true,"reset":true,"resize":true,"scroll":true,"click":true,"dblclick":true,"mousedown":true,"mouseenter":true,"mouseleave":true,"mousemove":true,"mouseover":true,"mouseout":true,"mouseup":true,"mousewheel":true,"wheel":true,"textInput":true,"keydown":true,"keypress":true,"keyup":true,"compositionstart":true,"compositionupdate":true,"compositionend":true,"DOMSubtreeModified":true,"DOMNodeInserted":true,"DOMNodeRemoved":true,"DOMNodeInsertedIntoDocument":true,"DOMNodeRemovedFromDocument":true,"DOMAttrModified":true,"DOMCharacterDataModified":true,"DOMAttributeNameChanged":true,"DOMElementNameChanged":true,"hashchange":true};}else{nativeEvents={"onabort":true,"onactivate":true,"onafterprint":true,"onafterupdate":true,"onbeforeactivate":true,"onbeforecopy":true,"onbeforecut":true,"onbeforedeactivate":true,"onbeforeeditfocus":true,"onbeforepaste":true,"onbeforeprint":true,"onbeforeunload":true,"onbeforeupdate":true,"onblur":true,"onbounce":true,"oncellchange":true,"onchange":true,"onclick":true,"oncontextmenu":true,"oncontrolselect":true,"oncopy":true,"oncut":true,"ondataavailable":true,"ondatasetchanged":true,"ondatasetcomplete":true,"ondblclick":true,"ondeactivate":true,"ondrag":true,"ondragend":true,"ondragenter":true,"ondragleave":true,"ondragover":true,"ondragstart":true,"ondrop":true,"onerror":true,"onerrorupdate":true,"onfilterchange":true,"onfinish":true,"onfocus":true,"onfocusin":true,"onfocusout":true,"onhashchange":true,"onhelp":true,"onkeydown":true,"onkeypress":true,"onkeyup":true,"onlayoutcomplete":true,"onload":true,"onlosecapture":true,"onmessage":true,"onmousedown":true,"onmouseenter":true,"onmouseleave":true,"onmousemove":true,"onmouseout":true,"onmouseover":true,"onmouseup":true,"onmousewheel":true,"onmove":true,"onmoveend":true,"onmovestart":true,"onoffline":true,"ononline":true,"onpage":true,"onpaste":true,"onprogress":true,"onpropertychange":true,"onreadystatechange":true,"onreset":true,"onresize":true,"onresizeend":true,"onresizestart":true,"onrowenter":true,"onrowexit":true,"onrowsdelete":true,"onrowsinserted":true,"onscroll":true,"onselect":true,"onselectionchange":true,"onselectstart":true,"onstart":true,"onstop":true,"onstorage":true,"onstoragecommit":true,"onsubmit":true,"ontimeout":true,"onunload":true};}
if(element!==null){if(element==document&&document.createEvent&&!element.dispatchEvent){element=document.documentElement;}
if(document.createEvent){ev=document.createEvent("HTMLEvents");if(typeof nativeEvents[eventName]==="undefined"){ev.initEvent("dataavailable",true,true);}else{ev.initEvent(eventName,true,true);}}else{ev=document.createEventObject();if(typeof nativeEvents["on"+eventName]==="undefined"){ev.eventType="ondataavailable";}else{ev.eventType="on"+eventName;}}
ev.eventName=eventName;ev.memo=memo||{};try{if(document.createEvent){element.dispatchEvent(ev);}else if(element.fireEvent){element.fireEvent(ev.eventType,ev);}else{return;}}catch(ex){}
return ev;}},observe:function(element,eventName,callBack)
{element=s$(element);if(element!==null){if(eventName.indexOf(':')!=-1||(eventName=="hashchange"&&element.attachEvent&&!window.onhashchange)){var argCallback=callBack;callBack=function(ev,eventName,cb){if(ev.eventName===eventName||(SAPO.Browser.IE&&eventName=='dom:loaded')){if(window.addEventListener){window.event=ev;}
cb();}}.bindObjEvent(this,eventName,argCallback);eventName='dataavailable';}
if(element.addEventListener){element.addEventListener(eventName,callBack,false);}else{element.attachEvent('on'+eventName,callBack);}}},stopObserving:function(element,eventName,callBack)
{element=s$(element);if(element!==null){if(element.removeEventListener){element.removeEventListener(eventName,callBack,false);}else{element.detachEvent('on'+eventName,callBack);}}},stop:function(event)
{if(event.cancelBubble!==null){event.cancelBubble=true;}
if(event.stopPropagation){event.stopPropagation();}
if(event.preventDefault){event.preventDefault();}
if(window.attachEvent){event.returnValue=false;}
if(event.cancel!==null){event.cancel=true;}},stopDefault:function(event)
{if(event.preventDefault){event.preventDefault();}
if(window.attachEvent){event.returnValue=false;}
if(event.cancel!==null){event.cancel=true;}},pointer:function(ev)
{return{x:ev.pageX||(ev.clientX+(document.documentElement.scrollLeft||document.body.scrollLeft)),y:ev.pageY||(ev.clientY+(document.documentElement.scrollTop||document.body.scrollTop))};},pointerX:function(ev)
{return this.pointer(ev).x;},pointerY:function(ev)
{return this.pointer(ev).y;},isLeftClick:function(ev){if(window.addEventListener){if(ev.button===0){return true;}
else if(ev.type.substring(0,5)=='touch'&&ev.button==null){return true;}}
else{if(ev.button===1){return true;}}
return false;},isRightClick:function(ev){if(ev.button===2){return true;}
return false;},isMiddleClick:function(ev){if(window.addEventListener){if(ev.button===1){return true;}}
else{if(ev.button===4){return true;}}
return false;},getCharFromKeyboardEvent:function(event,changeCasing){var k=event.keyCode;var c=String.fromCharCode(k);var shiftOn=event.shiftKey;if(k>=65&&k<=90){if(typeof changeCasing==='boolean'){shiftOn=changeCasing;}
return(shiftOn)?c:c.toLowerCase();}
else if(k>=96&&k<=105){return String.fromCharCode(48+(k-96));}
switch(k){case 109:case 189:return'-';case 107:case 187:return'+';}
return c;},debug:function(){}};
SAPO.namespace('Dom');SAPO.Dom.Loaded={version:'1.1',_cbQueue:[],run:function(win,fn){if(!fn){fn=win;win=window;}
this._win=win;this._doc=win.document;this._root=this._doc.documentElement;this._done=false;this._top=true;this._handlers={checkState:this._checkState.bindObjEvent(this),poll:this._poll.bindObj(this)};var ael=this._doc.addEventListener;this._add=ael?'addEventListener':'attachEvent';this._rem=ael?'removeEventListener':'detachEvent';this._pre=ael?'':'on';this._det=ael?'DOMContentLoaded':'onreadystatechange';this._wet=this._pre+'load';var csf=this._handlers.checkState;if(this._doc.readyState==='complete'){fn.call(this._win,'lazy');}
else{this._cbQueue.push(fn);this._doc[this._add](this._det,csf);this._win[this._add](this._wet,csf);if(!ael&&this._root.doScroll){try{this._top=!this._win.frameElement;}catch(e){}
if(this._top){this._poll();}}}},_checkState:function(event){if(!event||(event.type==='readystatechange'&&this._doc.readyState!=='complete')){return;}
var where=(event.type==='load')?this._win:this._doc;where[this._rem](this._pre+event.type,this._handlers.checkState,false);this._ready();},_poll:function(){try{this._root.doScroll('left');}catch(e){return setTimeout(this._handlers.poll,50);}
this._ready();},_ready:function(){if(!this._done){this._done=true;for(var i=0;i<this._cbQueue.length;++i){this._cbQueue[i].call(this._win);}}}};
SAPO.namespace('Dom');(function(window,undefined){var cachedruns,assertGetIdNotName,Expr,getText,isXML,contains,compile,sortOrder,hasDuplicate,outermostContext,baseHasDuplicate=true,strundefined="undefined",expando=("sizcache"+Math.random()).replace(".",""),Token=String,document=window.document,docElem=document.documentElement,dirruns=0,done=0,pop=[].pop,push=[].push,slice=[].slice,indexOf=[].indexOf||function(elem){var i=0,len=this.length;for(;i<len;i++){if(this[i]===elem){return i;}}
return-1;},markFunction=function(fn,value){fn[expando]=value==null||value;return fn;},createCache=function(){var cache={},keys=[];return markFunction(function(key,value){if(keys.push(key)>Expr.cacheLength){delete cache[keys.shift()];}
return(cache[key]=value);},cache);},classCache=createCache(),tokenCache=createCache(),compilerCache=createCache(),whitespace="[\\x20\\t\\r\\n\\f]",characterEncoding="(?:\\\\.|[-\\w]|[^\\x00-\\xa0])+",identifier=characterEncoding.replace("w","w#"),operators="([*^$|!~]?=)",attributes="\\["+whitespace+"*("+characterEncoding+")"+whitespace+"*(?:"+operators+whitespace+"*(?:(['\"])((?:\\\\.|[^\\\\])*?)\\3|("+identifier+")|)|)"+whitespace+"*\\]",pseudos=":("+characterEncoding+")(?:\\((?:(['\"])((?:\\\\.|[^\\\\])*?)\\2|([^()[\\]]*|(?:(?:"+attributes+")|[^:]|\\\\.)*|.*))\\)|)",pos=":(even|odd|eq|gt|lt|nth|first|last)(?:\\("+whitespace+"*((?:-\\d)?\\d*)"+whitespace+"*\\)|)(?=[^-]|$)",rtrim=new RegExp("^"+whitespace+"+|((?:^|[^\\\\])(?:\\\\.)*)"+whitespace+"+$","g"),rcomma=new RegExp("^"+whitespace+"*,"+whitespace+"*"),rcombinators=new RegExp("^"+whitespace+"*([\\x20\\t\\r\\n\\f>+~])"+whitespace+"*"),rpseudo=new RegExp(pseudos),rquickExpr=/^(?:#([\w\-]+)|(\w+)|\.([\w\-]+))$/,rnot=/^:not/,rsibling=/[\x20\t\r\n\f]*[+~]/,rendsWithNot=/:not\($/,rheader=/h\d/i,rinputs=/input|select|textarea|button/i,rbackslash=/\\(?!\\)/g,matchExpr={"ID":new RegExp("^#("+characterEncoding+")"),"CLASS":new RegExp("^\\.("+characterEncoding+")"),"NAME":new RegExp("^\\[name=['\"]?("+characterEncoding+")['\"]?\\]"),"TAG":new RegExp("^("+characterEncoding.replace("w","w*")+")"),"ATTR":new RegExp("^"+attributes),"PSEUDO":new RegExp("^"+pseudos),"POS":new RegExp(pos,"i"),"CHILD":new RegExp("^:(only|nth|first|last)-child(?:\\("+whitespace+"*(even|odd|(([+-]|)(\\d*)n|)"+whitespace+"*(?:([+-]|)"+whitespace+"*(\\d+)|))"+whitespace+"*\\)|)","i"),"needsContext":new RegExp("^"+whitespace+"*[>+~]|"+pos,"i")},assert=function(fn){var div=document.createElement("div");try{return fn(div);}catch(e){return false;}finally{div=null;}},assertTagNameNoComments=assert(function(div){div.appendChild(document.createComment(""));return!div.getElementsByTagName("*").length;}),assertHrefNotNormalized=assert(function(div){div.innerHTML="<a href='#'></a>";return div.firstChild&&typeof div.firstChild.getAttribute!==strundefined&&div.firstChild.getAttribute("href")==="#";}),assertAttributes=assert(function(div){div.innerHTML="<select></select>";var type=typeof div.lastChild.getAttribute("multiple");return type!=="boolean"&&type!=="string";}),assertUsableClassName=assert(function(div){div.innerHTML="<div class='hidden e'></div><div class='hidden'></div>";if(!div.getElementsByClassName||!div.getElementsByClassName("e").length){return false;}
div.lastChild.className="e";return div.getElementsByClassName("e").length===2;}),assertUsableName=assert(function(div){div.id=expando+0;div.innerHTML="<a name='"+expando+"'></a><div name='"+expando+"'></div>";docElem.insertBefore(div,docElem.firstChild);var pass=document.getElementsByName&&document.getElementsByName(expando).length===2+
document.getElementsByName(expando+0).length;assertGetIdNotName=!document.getElementById(expando);docElem.removeChild(div);return pass;});try{slice.call(docElem.childNodes,0)[0].nodeType;}catch(e){slice=function(i){var elem,results=[];for(;(elem=this[i]);i++){results.push(elem);}
return results;};}
function Sizzle(selector,context,results,seed){results=results||[];context=context||document;var match,elem,xml,m,nodeType=context.nodeType;if(!selector||typeof selector!=="string"){return results;}
if(nodeType!==1&&nodeType!==9){return[];}
xml=isXML(context);if(!xml&&!seed){if((match=rquickExpr.exec(selector))){if((m=match[1])){if(nodeType===9){elem=context.getElementById(m);if(elem&&elem.parentNode){if(elem.id===m){results.push(elem);return results;}}else{return results;}}else{if(context.ownerDocument&&(elem=context.ownerDocument.getElementById(m))&&contains(context,elem)&&elem.id===m){results.push(elem);return results;}}}else if(match[2]){push.apply(results,slice.call(context.getElementsByTagName(selector),0));return results;}else if((m=match[3])&&assertUsableClassName&&context.getElementsByClassName){push.apply(results,slice.call(context.getElementsByClassName(m),0));return results;}}}
return select(selector.replace(rtrim,"$1"),context,results,seed,xml);}
Sizzle.matches=function(expr,elements){return Sizzle(expr,null,null,elements);};Sizzle.matchesSelector=function(elem,expr){return Sizzle(expr,null,null,[elem]).length>0;};function createInputPseudo(type){return function(elem){var name=elem.nodeName.toLowerCase();return name==="input"&&elem.type===type;};}
function createButtonPseudo(type){return function(elem){var name=elem.nodeName.toLowerCase();return(name==="input"||name==="button")&&elem.type===type;};}
function createPositionalPseudo(fn){return markFunction(function(argument){argument=+argument;return markFunction(function(seed,matches){var j,matchIndexes=fn([],seed.length,argument),i=matchIndexes.length;while(i--){if(seed[(j=matchIndexes[i])]){seed[j]=!(matches[j]=seed[j]);}}});});}
getText=Sizzle.getText=function(elem){var node,ret="",i=0,nodeType=elem.nodeType;if(nodeType){if(nodeType===1||nodeType===9||nodeType===11){if(typeof elem.textContent==="string"){return elem.textContent;}else{for(elem=elem.firstChild;elem;elem=elem.nextSibling){ret+=getText(elem);}}}else if(nodeType===3||nodeType===4){return elem.nodeValue;}}else{for(;(node=elem[i]);i++){ret+=getText(node);}}
return ret;};isXML=Sizzle.isXML=function(elem){var documentElement=elem&&(elem.ownerDocument||elem).documentElement;return documentElement?documentElement.nodeName!=="HTML":false;};contains=Sizzle.contains=docElem.contains?function(a,b){var adown=a.nodeType===9?a.documentElement:a,bup=b&&b.parentNode;return a===bup||!!(bup&&bup.nodeType===1&&adown.contains&&adown.contains(bup));}:docElem.compareDocumentPosition?function(a,b){return b&&!!(a.compareDocumentPosition(b)&16);}:function(a,b){while((b=b.parentNode)){if(b===a){return true;}}
return false;};Sizzle.attr=function(elem,name){var val,xml=isXML(elem);if(!xml){name=name.toLowerCase();}
if((val=Expr.attrHandle[name])){return val(elem);}
if(xml||assertAttributes){return elem.getAttribute(name);}
val=elem.getAttributeNode(name);return val?typeof elem[name]==="boolean"?elem[name]?name:null:val.specified?val.value:null:null;};Expr=Sizzle.selectors={cacheLength:50,createPseudo:markFunction,match:matchExpr,attrHandle:assertHrefNotNormalized?{}:{"href":function(elem){return elem.getAttribute("href",2);},"type":function(elem){return elem.getAttribute("type");}},find:{"ID":assertGetIdNotName?function(id,context,xml){if(typeof context.getElementById!==strundefined&&!xml){var m=context.getElementById(id);return m&&m.parentNode?[m]:[];}}:function(id,context,xml){if(typeof context.getElementById!==strundefined&&!xml){var m=context.getElementById(id);return m?m.id===id||typeof m.getAttributeNode!==strundefined&&m.getAttributeNode("id").value===id?[m]:undefined:[];}},"TAG":assertTagNameNoComments?function(tag,context){if(typeof context.getElementsByTagName!==strundefined){return context.getElementsByTagName(tag);}}:function(tag,context){var results=context.getElementsByTagName(tag);if(tag==="*"){var elem,tmp=[],i=0;for(;(elem=results[i]);i++){if(elem.nodeType===1){tmp.push(elem);}}
return tmp;}
return results;},"NAME":assertUsableName&&function(tag,context){if(typeof context.getElementsByName!==strundefined){return context.getElementsByName(name);}},"CLASS":assertUsableClassName&&function(className,context,xml){if(typeof context.getElementsByClassName!==strundefined&&!xml){return context.getElementsByClassName(className);}}},relative:{">":{dir:"parentNode",first:true}," ":{dir:"parentNode"},"+":{dir:"previousSibling",first:true},"~":{dir:"previousSibling"}},preFilter:{"ATTR":function(match){match[1]=match[1].replace(rbackslash,"");match[3]=(match[4]||match[5]||"").replace(rbackslash,"");if(match[2]==="~="){match[3]=" "+match[3]+" ";}
return match.slice(0,4);},"CHILD":function(match){match[1]=match[1].toLowerCase();if(match[1]==="nth"){if(!match[2]){Sizzle.error(match[0]);}
match[3]=+(match[3]?match[4]+(match[5]||1):2*(match[2]==="even"||match[2]==="odd"));match[4]=+((match[6]+match[7])||match[2]==="odd");}else if(match[2]){Sizzle.error(match[0]);}
return match;},"PSEUDO":function(match){var unquoted,excess;if(matchExpr["CHILD"].test(match[0])){return null;}
if(match[3]){match[2]=match[3];}else if((unquoted=match[4])){if(rpseudo.test(unquoted)&&(excess=tokenize(unquoted,true))&&(excess=unquoted.indexOf(")",unquoted.length-excess)-unquoted.length)){unquoted=unquoted.slice(0,excess);match[0]=match[0].slice(0,excess);}
match[2]=unquoted;}
return match.slice(0,3);}},filter:{"ID":assertGetIdNotName?function(id){id=id.replace(rbackslash,"");return function(elem){return elem.getAttribute("id")===id;};}:function(id){id=id.replace(rbackslash,"");return function(elem){var node=typeof elem.getAttributeNode!==strundefined&&elem.getAttributeNode("id");return node&&node.value===id;};},"TAG":function(nodeName){if(nodeName==="*"){return function(){return true;};}
nodeName=nodeName.replace(rbackslash,"").toLowerCase();return function(elem){return elem.nodeName&&elem.nodeName.toLowerCase()===nodeName;};},"CLASS":function(className){var pattern=classCache[expando][className];if(!pattern){pattern=classCache(className,new RegExp("(^|"+whitespace+")"+className+"("+whitespace+"|$)"));}
return function(elem){return pattern.test(elem.className||(typeof elem.getAttribute!==strundefined&&elem.getAttribute("class"))||"");};},"ATTR":function(name,operator,check){return function(elem,context){var result=Sizzle.attr(elem,name);if(result==null){return operator==="!=";}
if(!operator){return true;}
result+="";return operator==="="?result===check:operator==="!="?result!==check:operator==="^="?check&&result.indexOf(check)===0:operator==="*="?check&&result.indexOf(check)>-1:operator==="$="?check&&result.substr(result.length-check.length)===check:operator==="~="?(" "+result+" ").indexOf(check)>-1:operator==="|="?result===check||result.substr(0,check.length+1)===check+"-":false;};},"CHILD":function(type,argument,first,last){if(type==="nth"){return function(elem){var node,diff,parent=elem.parentNode;if(first===1&&last===0){return true;}
if(parent){diff=0;for(node=parent.firstChild;node;node=node.nextSibling){if(node.nodeType===1){diff++;if(elem===node){break;}}}}
diff-=last;return diff===first||(diff%first===0&&diff/first>=0);};}
return function(elem){var node=elem;switch(type){case"only":case"first":while((node=node.previousSibling)){if(node.nodeType===1){return false;}}
if(type==="first"){return true;}
node=elem;case"last":while((node=node.nextSibling)){if(node.nodeType===1){return false;}}
return true;}};},"PSEUDO":function(pseudo,argument){var args,fn=Expr.pseudos[pseudo]||Expr.setFilters[pseudo.toLowerCase()]||Sizzle.error("unsupported pseudo: "+pseudo);if(fn[expando]){return fn(argument);}
if(fn.length>1){args=[pseudo,pseudo,"",argument];return Expr.setFilters.hasOwnProperty(pseudo.toLowerCase())?markFunction(function(seed,matches){var idx,matched=fn(seed,argument),i=matched.length;while(i--){idx=indexOf.call(seed,matched[i]);seed[idx]=!(matches[idx]=matched[i]);}}):function(elem){return fn(elem,0,args);};}
return fn;}},pseudos:{"not":markFunction(function(selector){var input=[],results=[],matcher=compile(selector.replace(rtrim,"$1"));return matcher[expando]?markFunction(function(seed,matches,context,xml){var elem,unmatched=matcher(seed,null,xml,[]),i=seed.length;while(i--){if((elem=unmatched[i])){seed[i]=!(matches[i]=elem);}}}):function(elem,context,xml){input[0]=elem;matcher(input,null,xml,results);return!results.pop();};}),"has":markFunction(function(selector){return function(elem){return Sizzle(selector,elem).length>0;};}),"contains":markFunction(function(text){return function(elem){return(elem.textContent||elem.innerText||getText(elem)).indexOf(text)>-1;};}),"enabled":function(elem){return elem.disabled===false;},"disabled":function(elem){return elem.disabled===true;},"checked":function(elem){var nodeName=elem.nodeName.toLowerCase();return(nodeName==="input"&&!!elem.checked)||(nodeName==="option"&&!!elem.selected);},"selected":function(elem){if(elem.parentNode){elem.parentNode.selectedIndex;}
return elem.selected===true;},"parent":function(elem){return!Expr.pseudos["empty"](elem);},"empty":function(elem){var nodeType;elem=elem.firstChild;while(elem){if(elem.nodeName>"@"||(nodeType=elem.nodeType)===3||nodeType===4){return false;}
elem=elem.nextSibling;}
return true;},"header":function(elem){return rheader.test(elem.nodeName);},"text":function(elem){var type,attr;return elem.nodeName.toLowerCase()==="input"&&(type=elem.type)==="text"&&((attr=elem.getAttribute("type"))==null||attr.toLowerCase()===type);},"radio":createInputPseudo("radio"),"checkbox":createInputPseudo("checkbox"),"file":createInputPseudo("file"),"password":createInputPseudo("password"),"image":createInputPseudo("image"),"submit":createButtonPseudo("submit"),"reset":createButtonPseudo("reset"),"button":function(elem){var name=elem.nodeName.toLowerCase();return name==="input"&&elem.type==="button"||name==="button";},"input":function(elem){return rinputs.test(elem.nodeName);},"focus":function(elem){var doc=elem.ownerDocument;return elem===doc.activeElement&&(!doc.hasFocus||doc.hasFocus())&&!!(elem.type||elem.href);},"active":function(elem){return elem===elem.ownerDocument.activeElement;},"first":createPositionalPseudo(function(matchIndexes,length,argument){return[0];}),"last":createPositionalPseudo(function(matchIndexes,length,argument){return[length-1];}),"eq":createPositionalPseudo(function(matchIndexes,length,argument){return[argument<0?argument+length:argument];}),"even":createPositionalPseudo(function(matchIndexes,length,argument){for(var i=0;i<length;i+=2){matchIndexes.push(i);}
return matchIndexes;}),"odd":createPositionalPseudo(function(matchIndexes,length,argument){for(var i=1;i<length;i+=2){matchIndexes.push(i);}
return matchIndexes;}),"lt":createPositionalPseudo(function(matchIndexes,length,argument){for(var i=argument<0?argument+length:argument;--i>=0;){matchIndexes.push(i);}
return matchIndexes;}),"gt":createPositionalPseudo(function(matchIndexes,length,argument){for(var i=argument<0?argument+length:argument;++i<length;){matchIndexes.push(i);}
return matchIndexes;})}};function siblingCheck(a,b,ret){if(a===b){return ret;}
var cur=a.nextSibling;while(cur){if(cur===b){return-1;}
cur=cur.nextSibling;}
return 1;}
sortOrder=docElem.compareDocumentPosition?function(a,b){if(a===b){hasDuplicate=true;return 0;}
return(!a.compareDocumentPosition||!b.compareDocumentPosition?a.compareDocumentPosition:a.compareDocumentPosition(b)&4)?-1:1;}:function(a,b){if(a===b){hasDuplicate=true;return 0;}else if(a.sourceIndex&&b.sourceIndex){return a.sourceIndex-b.sourceIndex;}
var al,bl,ap=[],bp=[],aup=a.parentNode,bup=b.parentNode,cur=aup;if(aup===bup){return siblingCheck(a,b);}else if(!aup){return-1;}else if(!bup){return 1;}
while(cur){ap.unshift(cur);cur=cur.parentNode;}
cur=bup;while(cur){bp.unshift(cur);cur=cur.parentNode;}
al=ap.length;bl=bp.length;for(var i=0;i<al&&i<bl;i++){if(ap[i]!==bp[i]){return siblingCheck(ap[i],bp[i]);}}
return i===al?siblingCheck(a,bp[i],-1):siblingCheck(ap[i],b,1);};[0,0].sort(sortOrder);baseHasDuplicate=!hasDuplicate;Sizzle.uniqueSort=function(results){var elem,i=1;hasDuplicate=baseHasDuplicate;results.sort(sortOrder);if(hasDuplicate){for(;(elem=results[i]);i++){if(elem===results[i-1]){results.splice(i--,1);}}}
return results;};Sizzle.error=function(msg){throw new Error("Syntax error, unrecognized expression: "+msg);};function tokenize(selector,parseOnly){var matched,match,tokens,type,soFar,groups,preFilters,cached=tokenCache[expando][selector];if(cached){return parseOnly?0:cached.slice(0);}
soFar=selector;groups=[];preFilters=Expr.preFilter;while(soFar){if(!matched||(match=rcomma.exec(soFar))){if(match){soFar=soFar.slice(match[0].length);}
groups.push(tokens=[]);}
matched=false;if((match=rcombinators.exec(soFar))){tokens.push(matched=new Token(match.shift()));soFar=soFar.slice(matched.length);matched.type=match[0].replace(rtrim," ");}
for(type in Expr.filter){if((match=matchExpr[type].exec(soFar))&&(!preFilters[type]||(match=preFilters[type](match,document,true)))){tokens.push(matched=new Token(match.shift()));soFar=soFar.slice(matched.length);matched.type=type;matched.matches=match;}}
if(!matched){break;}}
return parseOnly?soFar.length:soFar?Sizzle.error(selector):tokenCache(selector,groups).slice(0);}
function addCombinator(matcher,combinator,base){var dir=combinator.dir,checkNonElements=base&&combinator.dir==="parentNode",doneName=done++;return combinator.first?function(elem,context,xml){while((elem=elem[dir])){if(checkNonElements||elem.nodeType===1){return matcher(elem,context,xml);}}}:function(elem,context,xml){if(!xml){var cache,dirkey=dirruns+" "+doneName+" ",cachedkey=dirkey+cachedruns;while((elem=elem[dir])){if(checkNonElements||elem.nodeType===1){if((cache=elem[expando])===cachedkey){return elem.sizset;}else if(typeof cache==="string"&&cache.indexOf(dirkey)===0){if(elem.sizset){return elem;}}else{elem[expando]=cachedkey;if(matcher(elem,context,xml)){elem.sizset=true;return elem;}
elem.sizset=false;}}}}else{while((elem=elem[dir])){if(checkNonElements||elem.nodeType===1){if(matcher(elem,context,xml)){return elem;}}}}};}
function elementMatcher(matchers){return matchers.length>1?function(elem,context,xml){var i=matchers.length;while(i--){if(!matchers[i](elem,context,xml)){return false;}}
return true;}:matchers[0];}
function condense(unmatched,map,filter,context,xml){var elem,newUnmatched=[],i=0,len=unmatched.length,mapped=map!=null;for(;i<len;i++){if((elem=unmatched[i])){if(!filter||filter(elem,context,xml)){newUnmatched.push(elem);if(mapped){map.push(i);}}}}
return newUnmatched;}
function setMatcher(preFilter,selector,matcher,postFilter,postFinder,postSelector){if(postFilter&&!postFilter[expando]){postFilter=setMatcher(postFilter);}
if(postFinder&&!postFinder[expando]){postFinder=setMatcher(postFinder,postSelector);}
return markFunction(function(seed,results,context,xml){if(seed&&postFinder){return;}
var i,elem,postFilterIn,preMap=[],postMap=[],preexisting=results.length,elems=seed||multipleContexts(selector||"*",context.nodeType?[context]:context,[],seed),matcherIn=preFilter&&(seed||!selector)?condense(elems,preMap,preFilter,context,xml):elems,matcherOut=matcher?postFinder||(seed?preFilter:preexisting||postFilter)?[]:results:matcherIn;if(matcher){matcher(matcherIn,matcherOut,context,xml);}
if(postFilter){postFilterIn=condense(matcherOut,postMap);postFilter(postFilterIn,[],context,xml);i=postFilterIn.length;while(i--){if((elem=postFilterIn[i])){matcherOut[postMap[i]]=!(matcherIn[postMap[i]]=elem);}}}
if(seed){i=preFilter&&matcherOut.length;while(i--){if((elem=matcherOut[i])){seed[preMap[i]]=!(results[preMap[i]]=elem);}}}else{matcherOut=condense(matcherOut===results?matcherOut.splice(preexisting,matcherOut.length):matcherOut);if(postFinder){postFinder(null,results,matcherOut,xml);}else{push.apply(results,matcherOut);}}});}
function matcherFromTokens(tokens){var checkContext,matcher,j,len=tokens.length,leadingRelative=Expr.relative[tokens[0].type],implicitRelative=leadingRelative||Expr.relative[" "],i=leadingRelative?1:0,matchContext=addCombinator(function(elem){return elem===checkContext;},implicitRelative,true),matchAnyContext=addCombinator(function(elem){return indexOf.call(checkContext,elem)>-1;},implicitRelative,true),matchers=[function(elem,context,xml){return(!leadingRelative&&(xml||context!==outermostContext))||((checkContext=context).nodeType?matchContext(elem,context,xml):matchAnyContext(elem,context,xml));}];for(;i<len;i++){if((matcher=Expr.relative[tokens[i].type])){matchers=[addCombinator(elementMatcher(matchers),matcher)];}else{matcher=Expr.filter[tokens[i].type].apply(null,tokens[i].matches);if(matcher[expando]){j=++i;for(;j<len;j++){if(Expr.relative[tokens[j].type]){break;}}
return setMatcher(i>1&&elementMatcher(matchers),i>1&&tokens.slice(0,i-1).join("").replace(rtrim,"$1"),matcher,i<j&&matcherFromTokens(tokens.slice(i,j)),j<len&&matcherFromTokens((tokens=tokens.slice(j))),j<len&&tokens.join(""));}
matchers.push(matcher);}}
return elementMatcher(matchers);}
function matcherFromGroupMatchers(elementMatchers,setMatchers){var bySet=setMatchers.length>0,byElement=elementMatchers.length>0,superMatcher=function(seed,context,xml,results,expandContext){var elem,j,matcher,setMatched=[],matchedCount=0,i="0",unmatched=seed&&[],outermost=expandContext!=null,contextBackup=outermostContext,elems=seed||byElement&&Expr.find["TAG"]("*",expandContext&&context.parentNode||context),dirrunsUnique=(dirruns+=contextBackup==null?1:Math.E);if(outermost){outermostContext=context!==document&&context;cachedruns=superMatcher.el;}
for(;(elem=elems[i])!=null;i++){if(byElement&&elem){for(j=0;(matcher=elementMatchers[j]);j++){if(matcher(elem,context,xml)){results.push(elem);break;}}
if(outermost){dirruns=dirrunsUnique;cachedruns=++superMatcher.el;}}
if(bySet){if((elem=!matcher&&elem)){matchedCount--;}
if(seed){unmatched.push(elem);}}}
matchedCount+=i;if(bySet&&i!==matchedCount){for(j=0;(matcher=setMatchers[j]);j++){matcher(unmatched,setMatched,context,xml);}
if(seed){if(matchedCount>0){while(i--){if(!(unmatched[i]||setMatched[i])){setMatched[i]=pop.call(results);}}}
setMatched=condense(setMatched);}
push.apply(results,setMatched);if(outermost&&!seed&&setMatched.length>0&&(matchedCount+setMatchers.length)>1){Sizzle.uniqueSort(results);}}
if(outermost){dirruns=dirrunsUnique;outermostContext=contextBackup;}
return unmatched;};superMatcher.el=0;return bySet?markFunction(superMatcher):superMatcher;}
compile=Sizzle.compile=function(selector,group){var i,setMatchers=[],elementMatchers=[],cached=compilerCache[expando][selector];if(!cached){if(!group){group=tokenize(selector);}
i=group.length;while(i--){cached=matcherFromTokens(group[i]);if(cached[expando]){setMatchers.push(cached);}else{elementMatchers.push(cached);}}
cached=compilerCache(selector,matcherFromGroupMatchers(elementMatchers,setMatchers));}
return cached;};function multipleContexts(selector,contexts,results,seed){var i=0,len=contexts.length;for(;i<len;i++){Sizzle(selector,contexts[i],results,seed);}
return results;}
function select(selector,context,results,seed,xml){var i,tokens,token,type,find,match=tokenize(selector),j=match.length;if(!seed){if(match.length===1){tokens=match[0]=match[0].slice(0);if(tokens.length>2&&(token=tokens[0]).type==="ID"&&context.nodeType===9&&!xml&&Expr.relative[tokens[1].type]){context=Expr.find["ID"](token.matches[0].replace(rbackslash,""),context,xml)[0];if(!context){return results;}
selector=selector.slice(tokens.shift().length);}
for(i=matchExpr["POS"].test(selector)?-1:tokens.length-1;i>=0;i--){token=tokens[i];if(Expr.relative[(type=token.type)]){break;}
if((find=Expr.find[type])){if((seed=find(token.matches[0].replace(rbackslash,""),rsibling.test(tokens[0].type)&&context.parentNode||context,xml))){tokens.splice(i,1);selector=seed.length&&tokens.join("");if(!selector){push.apply(results,slice.call(seed,0));return results;}
break;}}}}}
compile(selector,match)(seed,context,xml,results,rsibling.test(selector));return results;}
if(document.querySelectorAll){(function(){var disconnectedMatch,oldSelect=select,rescape=/'|\\/g,rattributeQuotes=/\=[\x20\t\r\n\f]*([^'"\]]*)[\x20\t\r\n\f]*\]/g,rbuggyQSA=[":focus"],rbuggyMatches=[":active",":focus"],matches=docElem.matchesSelector||docElem.mozMatchesSelector||docElem.webkitMatchesSelector||docElem.oMatchesSelector||docElem.msMatchesSelector;assert(function(div){div.innerHTML="<select><option selected=''></option></select>";if(!div.querySelectorAll("[selected]").length){rbuggyQSA.push("\\["+whitespace+"*(?:checked|disabled|ismap|multiple|readonly|selected|value)");}
if(!div.querySelectorAll(":checked").length){rbuggyQSA.push(":checked");}});assert(function(div){div.innerHTML="<p test=''></p>";if(div.querySelectorAll("[test^='']").length){rbuggyQSA.push("[*^$]="+whitespace+"*(?:\"\"|'')");}
div.innerHTML="<input type='hidden'/>";if(!div.querySelectorAll(":enabled").length){rbuggyQSA.push(":enabled",":disabled");}});rbuggyQSA=new RegExp(rbuggyQSA.join("|"));select=function(selector,context,results,seed,xml){if(!seed&&!xml&&(!rbuggyQSA||!rbuggyQSA.test(selector))){var groups,i,old=true,nid=expando,newContext=context,newSelector=context.nodeType===9&&selector;if(context.nodeType===1&&context.nodeName.toLowerCase()!=="object"){groups=tokenize(selector);if((old=context.getAttribute("id"))){nid=old.replace(rescape,"\\$&");}else{context.setAttribute("id",nid);}
nid="[id='"+nid+"'] ";i=groups.length;while(i--){groups[i]=nid+groups[i].join("");}
newContext=rsibling.test(selector)&&context.parentNode||context;newSelector=groups.join(",");}
if(newSelector){try{push.apply(results,slice.call(newContext.querySelectorAll(newSelector),0));return results;}catch(qsaError){}finally{if(!old){context.removeAttribute("id");}}}}
return oldSelect(selector,context,results,seed,xml);};if(matches){assert(function(div){disconnectedMatch=matches.call(div,"div");try{matches.call(div,"[test!='']:sizzle");rbuggyMatches.push("!=",pseudos);}catch(e){}});rbuggyMatches=new RegExp(rbuggyMatches.join("|"));Sizzle.matchesSelector=function(elem,expr){expr=expr.replace(rattributeQuotes,"='$1']");if(!isXML(elem)&&!rbuggyMatches.test(expr)&&(!rbuggyQSA||!rbuggyQSA.test(expr))){try{var ret=matches.call(elem,expr);if(ret||disconnectedMatch||elem.document&&elem.document.nodeType!==11){return ret;}}catch(e){}}
return Sizzle(expr,null,null,[elem]).length>0;};}})();}
Expr.pseudos["nth"]=Expr.pseudos["eq"];function setFilters(){}
Expr.filters=setFilters.prototype=Expr.pseudos;Expr.setFilters=new setFilters();SAPO.Dom.Selector={};SAPO.Dom.Selector.select=Sizzle;SAPO.Dom.Selector.matches=Sizzle.matches;SAPO.Dom.Selector.find=function(){throw("SAPO.Dom.Selector.find() no longer exists. Use SAPO.Dom.Selector.select() instead or use version 0.1");};})(window);
SAPO.namespace('Utility');if(!Array.prototype.forEach){Array.prototype.forEach=function forEach(cb,thisArg){var O,len,T,k,kValue;if(this===null||this===undefined){throw new TypeError('this is null or not defined');}
O=Object(this);len=O.length>>>0;if({}.toString.call(cb)!=='[object Function]'){throw new TypeError(cb+' is not a function');}
if(thisArg){T=thisArg;}
k=0;while(k<len){if(Object.prototype.hasOwnProperty.call(O,k)){kValue=O[k];cb.call(T,kValue,k,O);}
++k;}};}
if(!Array.prototype.map){Array.prototype.map=function(callback,thisArg){var T,A,k;if(this===null||this===undefined){new TypeError(" this is null or not defined");}
var O=Object(this);var len=O.length>>>0;if({}.toString.call(callback)!=="[object Function]"){throw new TypeError(callback+" is not a function");}
if(thisArg){T=thisArg;}
A=new Array(len);k=0;while(k<len){var kValue,mappedValue;if(k in O){kValue=O[k];mappedValue=callback.call(T,kValue,k,O);A[k]=mappedValue;}
++k;}
return A;};}
SAPO.Utility.Array={inArray:function(value,arr){if(typeof arr==='object'){for(var i=0,f=arr.length;i<f;++i){if(arr[i]===value){return true;}}}
return false;},sortMulti:function(arr,key){if(typeof arr==='undefined'||arr.constructor!==Array){return false;}
if(typeof key!=='string'){return arr.sort();}
if(arr.length>0){if(typeof(arr[0][key])==='undefined'){return false;}
arr.sort(function(a,b){var x=a[key];var y=b[key];return((x<y)?-1:((x>y)?1:0));});}
return arr;},keyValue:function(value,arr,first){if(typeof value!=='undefined'&&typeof arr==='object'&&this.inArray(value,arr)){var aKeys=[];for(var i=0,f=arr.length;i<f;++i){if(arr[i]===value){if(typeof first!=='undefined'&&first===true){return i;}else{aKeys.push(i);}}}
return aKeys;}
return false;},shuffle:function(arr){if(typeof(arr)!=='undefined'&&arr.constructor!==Array){return false;}
var total=arr.length,tmp1=false,rnd=false;while(total--){rnd=Math.floor(Math.random()*(total+1));tmp1=arr[total];arr[total]=arr[rnd];arr[rnd]=tmp1;}
return arr;},each:function(arr,cb){var arrCopy=arr.slice(0),total=arrCopy.length,iterations=Math.floor(total/8),leftover=total%8,i=0;if(leftover>0){do{cb(arrCopy[i++],i-1,arr);}while(--leftover>0);}
if(iterations===0){return arr;}
do{cb(arrCopy[i++],i-1,arr);cb(arrCopy[i++],i-1,arr);cb(arrCopy[i++],i-1,arr);cb(arrCopy[i++],i-1,arr);cb(arrCopy[i++],i-1,arr);cb(arrCopy[i++],i-1,arr);cb(arrCopy[i++],i-1,arr);cb(arrCopy[i++],i-1,arr);}while(--iterations>0);return arr;},intersect:function(arr1,arr2){if(!arr1||!arr2||arr1 instanceof Array===false||arr2 instanceof Array===false){return[];}
var shared=[];for(var i=0,I=arr1.length;i<I;++i){for(var j=0,J=arr2.length;j<J;++j){if(arr1[i]===arr2[j]){shared.push(arr1[i]);}}}
return shared;},convert:function(arr){return Array.prototype.slice.call(arr||[],0);},insert:function(arr,idx,value){arr.splice(idx,0,value);}};
SAPO.namespace('Utility');SAPO.Utility.Date={_months:function(index){var _m=['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];return _m[index];},_iMonth:function(month)
{if(Number(month)){return+month-1;}
return{'janeiro':0,'jan':0,'fevereiro':1,'fev':1,'março':2,'mar':2,'abril':3,'abr':3,'maio':4,'mai':4,'junho':5,'jun':5,'julho':6,'jul':6,'agosto':7,'ago':7,'setembro':8,'set':8,'outubro':9,'out':9,'novembro':10,'nov':10,'dezembro':11,'dez':11}[month.toLowerCase()];},_wDays:function(index){var _d=['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'];return _d[index];},_iWeek:function(week)
{if(Number(week)){return+week||7;}
return{'segunda':1,'seg':1,'terça':2,'ter':2,'quarta':3,'qua':3,'quinta':4,'qui':4,'sexta':5,'sex':5,'sábado':6,'sáb':6,'domingo':7,'dom':7}[week.toLowerCase()];},_daysInMonth:function(_m,_y){var nDays;if(_m===1||_m===3||_m===5||_m===7||_m===8||_m===10||_m===12)
{nDays=31;}
else if(_m===4||_m===6||_m===9||_m===11)
{nDays=30;}
else
{if((_y%400===0)||(_y%4===0&&_y%100!==0))
{nDays=29;}
else
{nDays=28;}}
return nDays;},get:function(format,_date){if(typeof(format)==='undefined'||format===''){format="Y-m-d";}
var iFormat=format.split("");var result=new Array(iFormat.length);var escapeChar="\\";var jsDate;if(typeof(_date)==='undefined'){jsDate=new Date();}else if(typeof(_date)==='number'){jsDate=new Date(_date*1000);}else{jsDate=new Date(_date);}
var jsFirstDay,jsThisDay,jsHour;for(var i=0;i<iFormat.length;i++){switch(iFormat[i]){case escapeChar:result[i]=iFormat[i+1];i++;break;case"d":var jsDay=jsDate.getDate();result[i]=(String(jsDay).length>1)?jsDay:"0"+jsDay;break;case"D":result[i]=this._wDays(jsDate.getDay()).substring(0,3);break;case"j":result[i]=jsDate.getDate();break;case"l":result[i]=this._wDays(jsDate.getDay());break;case"N":result[i]=jsDate.getDay()||7;break;case"S":var temp=jsDate.getDate();var suffixes=["st","nd","rd"];var suffix="";if(temp>=11&&temp<=13){result[i]="th";}else{result[i]=(suffix=suffixes[String(temp).substr(-1)-1])?(suffix):("th");}
break;case"w":result[i]=jsDate.getDay();break;case"z":jsFirstDay=Date.UTC(jsDate.getFullYear(),0,0);jsThisDay=Date.UTC(jsDate.getFullYear(),jsDate.getMonth(),jsDate.getDate());result[i]=Math.floor((jsThisDay-jsFirstDay)/(1000*60*60*24));break;case"W":var jsYearStart=new Date(jsDate.getFullYear(),0,1);jsFirstDay=jsYearStart.getDay()||7;var days=Math.floor((jsDate-jsYearStart)/(24*60*60*1000)+1);result[i]=Math.ceil((days-(8-jsFirstDay))/7)+1;break;case"F":result[i]=this._months(jsDate.getMonth());break;case"m":var jsMonth=String(jsDate.getMonth()+1);result[i]=(jsMonth.length>1)?jsMonth:"0"+jsMonth;break;case"M":result[i]=this._months(jsDate.getMonth()).substring(0,3);break;case"n":result[i]=jsDate.getMonth()+1;break;case"t":result[i]=this._daysInMonth(jsDate.getMonth()+1,jsDate.getYear());break;case"L":var jsYear=jsDate.getFullYear();result[i]=(jsYear%4)?false:((jsYear%100)?true:((jsYear%400)?false:true));break;case"o":throw'"o" not implemented!';case"Y":result[i]=jsDate.getFullYear();break;case"y":result[i]=String(jsDate.getFullYear()).substring(2);break;case"a":result[i]=(jsDate.getHours()<12)?"am":"pm";break;case"A":result[i]=(jsDate.getHours<12)?"AM":"PM";break;case"B":throw'"B" not implemented!';case"g":jsHour=jsDate.getHours();result[i]=(jsHour<=12)?jsHour:(jsHour-12);break;case"G":result[i]=String(jsDate.getHours());break;case"h":jsHour=String(jsDate.getHours());jsHour=(jsHour<=12)?jsHour:(jsHour-12);result[i]=(jsHour.length>1)?jsHour:"0"+jsHour;break;case"H":jsHour=String(jsDate.getHours());result[i]=(jsHour.length>1)?jsHour:"0"+jsHour;break;case"i":var jsMinute=String(jsDate.getMinutes());result[i]=(jsMinute.length>1)?jsMinute:"0"+jsMinute;break;case"s":var jsSecond=String(jsDate.getSeconds());result[i]=(jsSecond.length>1)?jsSecond:"0"+jsSecond;break;case"u":throw'"u" not implemented!';case"e":throw'"e" not implemented!';case"I":jsFirstDay=new Date(jsDate.getFullYear(),0,1);result[i]=(jsDate.getTimezoneOffset()!==jsFirstDay.getTimezoneOffset())?(1):(0);break;case"O":var jsMinZone=jsDate.getTimezoneOffset();var jsMinutes=jsMinZone%60;jsHour=String(((jsMinZone-jsMinutes)/60)*-1);if(jsHour.charAt(0)!=="-"){jsHour="+"+jsHour;}
jsHour=(jsHour.length===3)?(jsHour):(jsHour.replace(/([+\-])(\d)/,"$1"+0+"$2"));result[i]=jsHour+jsMinutes+"0";break;case"P":throw'"P" not implemented!';case"T":throw'"T" not implemented!';case"Z":result[i]=jsDate.getTimezoneOffset()*60;break;case"c":throw'"c" not implemented!';case"r":var jsDayName=this._wDays(jsDate.getDay()).substr(0,3);var jsMonthName=this._months(jsDate.getMonth()).substr(0,3);result[i]=jsDayName+", "+jsDate.getDate()+" "+jsMonthName+this.get(" Y H:i:s O",jsDate);break;case"U":result[i]=Math.floor(jsDate.getTime()/1000);break;default:result[i]=iFormat[i];}}
return result.join('');},set:function(format,str_date){if(typeof str_date==='undefined'){return;}
if(typeof format==='undefined'||format===''){format="Y-m-d";}
var iFormat=format.split("");var result=new Array(iFormat.length);var escapeChar="\\";var objIndex={year:undefined,month:undefined,day:undefined,dayY:undefined,dayW:undefined,week:undefined,hour:undefined,hourD:undefined,min:undefined,sec:undefined,msec:undefined,ampm:undefined,diffM:undefined,diffH:undefined,date:undefined};var matches=0;for(var i=0;i<iFormat.length;i++){switch(iFormat[i]){case escapeChar:result[i]=iFormat[i+1];i++;break;case"d":result[i]='(\\d{2})';objIndex.day={original:i,match:matches++};break;case"j":result[i]='(\\d{1,2})';objIndex.day={original:i,match:matches++};break;case"D":result[i]='([\\wá]{3})';objIndex.dayW={original:i,match:matches++};break;case"l":result[i]='([\\wá]{5,7})';objIndex.dayW={original:i,match:matches++};break;case"N":result[i]='(\\d)';objIndex.dayW={original:i,match:matches++};break;case"w":result[i]='(\\d)';objIndex.dayW={original:i,match:matches++};break;case"S":result[i]='\\w{2}';break;case"z":result[i]='(\\d{1,3})';objIndex.dayY={original:i,match:matches++};break;case"W":result[i]='(\\d{1,2})';objIndex.week={original:i,match:matches++};break;case"F":result[i]='([\\wç]{4,9})';objIndex.month={original:i,match:matches++};break;case"M":result[i]='(\\w{3})';objIndex.month={original:i,match:matches++};break;case"m":result[i]='(\\d{2})';objIndex.month={original:i,match:matches++};break;case"n":result[i]='(\\d{1,2})';objIndex.month={original:i,match:matches++};break;case"t":result[i]='\\d{2}';break;case"L":result[i]='\\w{4,5}';break;case"o":throw'"o" not implemented!';case"Y":result[i]='(\\d{4})';objIndex.year={original:i,match:matches++};break;case"y":result[i]='(\\d{2})';if(typeof objIndex.year==='undefined'||iFormat[objIndex.year.original]!=='Y'){objIndex.year={original:i,match:matches++};}
break;case"a":result[i]='(am|pm)';objIndex.ampm={original:i,match:matches++};break;case"A":result[i]='(AM|PM)';objIndex.ampm={original:i,match:matches++};break;case"B":throw'"B" not implemented!';case"g":result[i]='(\\d{1,2})';objIndex.hourD={original:i,match:matches++};break;case"G":result[i]='(\\d{1,2})';objIndex.hour={original:i,match:matches++};break;case"h":result[i]='(\\d{2})';objIndex.hourD={original:i,match:matches++};break;case"H":result[i]='(\\d{2})';objIndex.hour={original:i,match:matches++};break;case"i":result[i]='(\\d{2})';objIndex.min={original:i,match:matches++};break;case"s":result[i]='(\\d{2})';objIndex.sec={original:i,match:matches++};break;case"u":throw'"u" not implemented!';case"e":throw'"e" not implemented!';case"I":result[i]='\\d';break;case"O":result[i]='([-+]\\d{4})';objIndex.diffH={original:i,match:matches++};break;case"P":throw'"P" not implemented!';case"T":throw'"T" not implemented!';case"Z":result[i]='(\\-?\\d{1,5})';objIndex.diffM={original:i,match:matches++};break;case"c":throw'"c" not implemented!';case"r":result[i]='([\\wá]{3}, \\d{1,2} \\w{3} \\d{4} \\d{2}:\\d{2}:\\d{2} [+\\-]\\d{4})';objIndex.date={original:i,match:matches++};break;case"U":result[i]='(\\d{1,13})';objIndex.date={original:i,match:matches++};break;default:result[i]=iFormat[i];}}
var pattr=new RegExp(result.join(''));try{var mList=str_date.match(pattr);if(!mList){return;}}
catch(e){return;}
var _haveDatetime=typeof objIndex.date!=='undefined';var _haveYear=typeof objIndex.year!=='undefined';var _haveYDay=typeof objIndex.dayY!=='undefined';var _haveDay=typeof objIndex.day!=='undefined';var _haveMonth=typeof objIndex.month!=='undefined';var _haveMonthDay=_haveMonth&&_haveDay;var _haveOnlyDay=!_haveMonth&&_haveDay;var _haveWDay=typeof objIndex.dayW!=='undefined';var _haveWeek=typeof objIndex.week!=='undefined';var _haveWeekWDay=_haveWeek&&_haveWDay;var _haveOnlyWDay=!_haveWeek&&_haveWDay;var _validDate=_haveYDay||_haveMonthDay||!_haveYear&&_haveOnlyDay||_haveWeekWDay||!_haveYear&&_haveOnlyWDay;var _noDate=!_haveYear&&!_haveYDay&&!_haveDay&&!_haveMonth&&!_haveWDay&&!_haveWeek;var _haveHour12=typeof objIndex.hourD!=='undefined'&&typeof objIndex.ampm!='undefined';var _haveHour24=typeof objIndex.hour!=='undefined';var _haveHour=_haveHour12||_haveHour24;var _haveMin=typeof objIndex.min!=='undefined';var _haveSec=typeof objIndex.sec!=='undefined';var _haveMSec=typeof objIndex.msec!=='undefined';var _haveMoreM=!_noDate||_haveHour;var _haveMoreS=_haveMoreM||_haveMin;var _haveDiffM=typeof objIndex.diffM!=='undefined';var _haveDiffH=typeof objIndex.diffH!=='undefined';var _haveGMT=_haveDiffM||_haveDiffH;if(_haveDatetime){if(iFormat[objIndex.date.original]==='U'){return new Date(+mList[objIndex.date.match+1]*1000);}
var dList=mList[objIndex.date.match+1].match(/\w{3}, (\d{1,2}) (\w{3}) (\d{4}) (\d{2}):(\d{2}):(\d{2}) ([+\-]\d{4})/);var hour=+dList[4]+(+dList[7].slice(0,3));var min=+dList[5]+(dList[7].slice(0,1)+dList[7].slice(3))/100*60;return new Date(dList[3],this._iMonth(dList[2]),dList[1],hour,min,dList[6]);}
var _d=new Date();var year;var month;var day;var date;var hour;var min;var sec;var msec;var gmt;if(!_validDate&&!_noDate){return;}
if(_validDate){if(_haveYear){var _y=_d.getFullYear()-50+'';year=mList[objIndex.year.match+1];if(iFormat[objIndex.year.original]=='y'){year=+_y.slice(0,2)+(year>=(_y).slice(2)?0:1)+year;}}
else{year=_d.getFullYear();}
if(_haveYDay){month=0;day=mList[objIndex.dayY.match+1]}
else if(_haveDay){if(_haveMonth){month=this._iMonth(mList[objIndex.month.match+1]);}
else{month=_d.getMonth();}
day=mList[objIndex.day.match+1]}
else{month=0;var week;if(_haveWeek){week=mList[objIndex.week.match+1];}
else{week=this.get('W',_d)}
day=(week-2)*7+(8-((new Date(year,0,1)).getDay()||7))+this._iWeek(mList[objIndex.week.match+1]);}
if(month==0&&day>31){var aux=new Date(year,month,day);month=aux.getMonth();day=aux.getDate();}}
else{year=_d.getFullYear();month=_d.getMonth();day=_d.getDate();}
date=year+'-'+(month+1)+'-'+day+' ';if(_haveHour12){hour=+mList[objIndex.hourD.match+1]+(mList[objIndex.ampm.match+1]=='pm'?12:0);}
else if(_haveHour24){hour=mList[objIndex.hour.match+1];}
else if(_noDate){hour=_d.getHours();}
else{hour='00';}
if(_haveMin){min=mList[objIndex.min.match+1];}
else if(!_haveMoreM){min=_d.getMinutes();}
else{min='00';}
if(_haveSec){sec=mList[objIndex.sec.match+1];}
else if(!_haveMoreS){sec=_d.getSeconds();}
else{sec='00';}
if(_haveMSec){msec=mList[objIndex.msec.match+1];}
else{msec='000';}
if(_haveDiffH){gmt=mList[objIndex.diffH.match+1];}
else if(_haveDiffM){gmt=String(-1*mList[objIndex.diffM.match+1]/60*100).replace(/^(\d)/,'+$1').replace(/(^[-+])(\d{3}$)/,'$10$2');}
else{gmt='+0000';}
return new Date(date+hour+':'+min+':'+sec+'.'+msec+gmt);}};
(function(window,undefined){'use strict';SAPO.namespace('Utility');var Event=SAPO.Dom.Event;var Swipe=function(el,options){this._options=SAPO.extendObj({minDist:undefined,maxDist:undefined,minDuration:undefined,maxDuration:undefined,forceAxis:undefined,storeGesture:false,stopEvents:true},options||{});this._handlers={down:this._onDown.bindObjEvent(this),move:this._onMove.bindObjEvent(this),up:this._onUp.bindObjEvent(this)};this._element=s$(el);this._init();};Swipe._supported=('ontouchstart'in document.documentElement);Swipe.prototype={version:'0.1',_init:function(){var db=document.body;Event.observe(db,'touchstart',this._handlers.down);if(this._options.storeGesture){Event.observe(db,'touchmove',this._handlers.move);}
Event.observe(db,'touchend',this._handlers.up);this._isOn=false;},_isMeOrParent:function(el,parentEl){if(!el){return;}
do{if(el===parentEl){return true;}
el=el.parentNode;}while(el);return false;},_onDown:function(ev){if(event.changedTouches.length!==1){return;}
if(!this._isMeOrParent(ev.target,this._element)){return;}
if(this._options.stopEvents===true){Event.stop(ev);}
ev=ev.changedTouches[0];this._isOn=true;this._target=ev.target;this._t0=new Date().valueOf();this._p0=[ev.pageX,ev.pageY];if(this._options.storeGesture){this._gesture=[this._p0];this._time=[0];}},_onMove:function(ev){if(!this._isOn||event.changedTouches.length!==1){return;}
if(this._options.stopEvents===true){Event.stop(ev);}
ev=ev.changedTouches[0];var t1=new Date().valueOf();var dt=(t1-this._t0)*0.001;this._gesture.push([ev.pageX,ev.pageY]);this._time.push(dt);},_onUp:function(ev){if(!this._isOn||event.changedTouches.length!==1){return;}
if(this._options.stopEvents===true){Event.stop(ev);}
ev=ev.changedTouches[0];this._isOn=false;var t1=new Date().valueOf();var p1=[ev.pageX,ev.pageY];var dt=(t1-this._t0)*0.001;var dr=[p1[0]-this._p0[0],p1[1]-this._p0[1]];var dist=Math.sqrt(dr[0]*dr[0]+dr[1]*dr[1]);var axis=Math.abs(dr[0])>Math.abs(dr[1])?'x':'y';var o=this._options;if(o.minDist&&dist<o.minDist){return;}
if(o.maxDist&&dist>o.maxDist){return;}
if(o.minDuration&&dt<o.minDuration){return;}
if(o.maxDuration&&dt>o.maxDuration){return;}
if(o.forceAxis&&axis!==o.forceAxis){return;}
var O={elementId:this._element.id,duration:dt,dr:dr,dist:dist,axis:axis,target:this._target};if(this._options.storeGesture){O.gesture=this._gesture;O.time=this._time;}
this._options.callback(this,O);}};SAPO.Utility.Swipe=Swipe;})(window);
SAPO.namespace('Utility');SAPO.Utility.Url={_keyStr:'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=',getUrl:function()
{var url=false;url=location.href;return url;},genQueryString:function(uri,params){var hasQuestionMark=uri.indexOf('?')!==-1;var sep,pKey,pValue,parts=[uri];for(pKey in params){if(params.hasOwnProperty(pKey)){if(!hasQuestionMark){sep='?';hasQuestionMark=true;}
else{sep='&';}
pValue=params[pKey];if(typeof pValue!=='number'&&!pValue){pValue='';}
parts=parts.concat([sep,encodeURIComponent(pKey),'=',encodeURIComponent(pValue)]);}}
return parts.join('');},getQueryString:function(string)
{var url;if(string&&typeof(string)!=='undefined'){url=string;}else{url=this.getUrl();}
var aParams={};if(url.match(/\?(.+)/i)){var queryStr=url.replace(/^(.*)\?([^\#]+)(\#(.*))?/g,"$2");if(queryStr.length>0){var aQueryStr=queryStr.split(/[;&]/);for(var i=0;i<aQueryStr.length;i++){var pairVar=aQueryStr[i].split('=');aParams[decodeURIComponent(pairVar[0])]=(typeof(pairVar[1])!=='undefined'&&pairVar[1])?decodeURIComponent(pairVar[1]):false;}}}
return aParams;},getAnchor:function(string)
{var url;if(string&&typeof(string)!=='undefined'){url=string;}else{url=this.getUrl();}
var anchor=false;if(url.match(/#(.+)/)){anchor=url.replace(/([^#]+)#(.*)/,"$2");}
return anchor;},getAnchorString:function(string)
{var url;if(string&&typeof(string)!=='undefined'){url=string;}else{url=this.getUrl();}
var aParams={};if(url.match(/#(.+)/i)){var anchorStr=url.replace(/^([^#]+)#(.*)?/g,"$2");if(anchorStr.length>0){var aAnchorStr=anchorStr.split(/[;&]/);for(var i=0;i<aAnchorStr.length;i++){var pairVar=aAnchorStr[i].split('=');aParams[decodeURIComponent(pairVar[0])]=(typeof(pairVar[1])!=='undefined'&&pairVar[1])?decodeURIComponent(pairVar[1]):false;}}}
return aParams;},parseUrl:function(url)
{var aURL={};if(url&&typeof(url)!=='undefined'&&typeof(url)==='string'){if(url.match(/^([^:]+):\/\//i)){var re=/^([^:]+):\/\/([^\/]*)\/?([^\?#]*)\??([^#]*)#?(.*)/i;if(url.match(re)){aURL.scheme=url.replace(re,"$1");aURL.host=url.replace(re,"$2");aURL.path='/'+url.replace(re,"$3");aURL.query=url.replace(re,"$4")||false;aURL.fragment=url.replace(re,"$5")||false;}}else{var re1=new RegExp("^([^\\?]+)\\?([^#]+)#(.*)","i");var re2=new RegExp("^([^\\?]+)\\?([^#]+)#?","i");var re3=new RegExp("^([^\\?]+)\\??","i");if(url.match(re1)){aURL.scheme=false;aURL.host=false;aURL.path=url.replace(re1,"$1");aURL.query=url.replace(re1,"$2");aURL.fragment=url.replace(re1,"$3");}else if(url.match(re2)){aURL.scheme=false;aURL.host=false;aURL.path=url.replace(re2,"$1");aURL.query=url.replace(re2,"$2");aURL.fragment=false;}else if(url.match(re3)){aURL.scheme=false;aURL.host=false;aURL.path=url.replace(re3,"$1");aURL.query=false;aURL.fragment=false;}}
if(aURL.host){var regPort=new RegExp("^(.*)\\:(\\d+)$","i");if(aURL.host.match(regPort)){var tmpHost1=aURL.host;aURL.host=tmpHost1.replace(regPort,"$1");aURL.port=tmpHost1.replace(regPort,"$2");}else{aURL.port=false;}
if(aURL.host.match(/@/i)){var tmpHost2=aURL.host;aURL.host=tmpHost2.split('@')[1];var tmpUserPass=tmpHost2.split('@')[0];if(tmpUserPass.match(/\:/)){aURL.user=tmpUserPass.split(':')[0];aURL.pass=tmpUserPass.split(':')[1];}else{aURL.user=tmpUserPass;aURL.pass=false;}}}}
return aURL;},currentScriptElement:function(match)
{var aScripts=document.getElementsByTagName('script');if(typeof(match)==='undefined'){if(aScripts.length>0){return aScripts[(aScripts.length-1)];}else{return false;}}else{var curScript=false;var re=new RegExp(""+match+"","i");for(var i=0,total=aScripts.length;i<total;i++){curScript=aScripts[i];if(re.test(curScript.src)){return curScript;}}
return false;}},base64Encode:function(string)
{if(!SAPO.Utility.String||typeof(SAPO.Utility.String)==='undefined'){throw"SAPO.Utility.Url.base64Encode depends of SAPO.Utility.String, which has not been referred.";}
var output="";var chr1,chr2,chr3,enc1,enc2,enc3,enc4;var i=0;var input=SAPO.Utility.String.utf8Encode(string);while(i<input.length){chr1=input.charCodeAt(i++);chr2=input.charCodeAt(i++);chr3=input.charCodeAt(i++);enc1=chr1>>2;enc2=((chr1&3)<<4)|(chr2>>4);enc3=((chr2&15)<<2)|(chr3>>6);enc4=chr3&63;if(isNaN(chr2)){enc3=enc4=64;}else if(isNaN(chr3)){enc4=64;}
output=output+
this._keyStr.charAt(enc1)+this._keyStr.charAt(enc2)+
this._keyStr.charAt(enc3)+this._keyStr.charAt(enc4);}
return output;},base64Decode:function(string)
{if(!SAPO.Utility.String||typeof(SAPO.Utility.String)==='undefined'){throw"SAPO.Utility.Url.base64Decode depends of SAPO.Utility.String, which has not been referred.";}
var output="";var chr1,chr2,chr3;var enc1,enc2,enc3,enc4;var i=0;var input=string.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(i<input.length){enc1=this._keyStr.indexOf(input.charAt(i++));enc2=this._keyStr.indexOf(input.charAt(i++));enc3=this._keyStr.indexOf(input.charAt(i++));enc4=this._keyStr.indexOf(input.charAt(i++));chr1=(enc1<<2)|(enc2>>4);chr2=((enc2&15)<<4)|(enc3>>2);chr3=((enc3&3)<<6)|enc4;output=output+String.fromCharCode(chr1);if(enc3!==64){output=output+String.fromCharCode(chr2);}
if(enc4!==64){output=output+String.fromCharCode(chr3);}}
output=SAPO.Utility.String.utf8Decode(output);return output;}};
SAPO.namespace('Utility');SAPO.Utility.Validator={_countryCodes:['AO','CV','MZ','PT'],_internacionalPT:351,_indicativosPT:{21:'lisboa',22:'porto',231:'mealhada',232:'viseu',233:'figueira da foz',234:'aveiro',235:'arganil',236:'pombal',238:'seia',239:'coimbra',241:'abrantes',242:'ponte de sôr',243:'santarém',244:'leiria',245:'portalegre',249:'torres novas',251:'valença',252:'vila nova de famalicão',253:'braga',254:'peso da régua',255:'penafiel',256:'são joão da madeira',258:'viana do castelo',259:'vila real',261:'torres vedras',262:'caldas da raínha',263:'vila franca de xira',265:'setúbal',266:'évora',268:'estremoz',269:'santiago do cacém',271:'guarda',272:'castelo branco',273:'bragança',274:'proença-a-nova',275:'covilhã',276:'chaves',277:'idanha-a-nova',278:'mirandela',279:'moncorvo',281:'tavira',282:'portimão',283:'odemira',284:'beja',285:'moura',286:'castro verde',289:'faro',291:'funchal, porto santo',292:'corvo, faial, flores, horta, pico',295:'angra do heroísmo, graciosa, são jorge, terceira',296:'ponta delgada, são miguel, santa maria',91:'rede móvel 91 (Vodafone / Yorn)',93:'rede móvel 93 (Optimus)',96:'rede móvel 96 (TMN)',925:'rede móvel 925 (TMN 925)',926:'rede móvel 926 (TMN 926)',927:'rede móvel 927 (TMN 927)',922:'rede móvel 922 (Phone-ix)',707:'número único',760:'número único',800:'número grátis',808:'chamada local'},_internacionalCV:238,_indicativosCV:{2:'fixo',91:'móvel 91',95:'móvel 95',97:'móvel 97',98:'móvel 98',99:'móvel 99'},_internacionalAO:244,_indicativosAO:{2:'fixo',91:'móvel 91',92:'móvel 92'},_internacionalMZ:258,_indicativosMZ:{2:'fixo',82:'móvel 82',84:'móvel 84'},_internacionalTL:670,_indicativosTL:{3:'fixo',7:'móvel 7'},_isLeapYear:function(year){var yearRegExp=/^\d{4}$/;if(yearRegExp.test(year)){return((year%4)?false:((year%100)?true:((year%400)?false:true)));}
return false;},_dateParsers:{'yyyy-mm-dd':{day:5,month:3,year:1,sep:'-',parser:/^(\d{4})(\-)(\d{1,2})(\-)(\d{1,2})$/},'yyyy/mm/dd':{day:5,month:3,year:1,sep:'/',parser:/^(\d{4})(\/)(\d{1,2})(\/)(\d{1,2})$/},'yy-mm-dd':{day:5,month:3,year:1,sep:'-',parser:/^(\d{2})(\-)(\d{1,2})(\-)(\d{1,2})$/},'yy/mm/dd':{day:5,month:3,year:1,sep:'/',parser:/^(\d{2})(\/)(\d{1,2})(\/)(\d{1,2})$/},'dd-mm-yyyy':{day:1,month:3,year:5,sep:'-',parser:/^(\d{1,2})(\-)(\d{1,2})(\-)(\d{4})$/},'dd/mm/yyyy':{day:1,month:3,year:5,sep:'/',parser:/^(\d{1,2})(\/)(\d{1,2})(\/)(\d{4})$/},'dd-mm-yy':{day:1,month:3,year:5,sep:'-',parser:/^(\d{1,2})(\-)(\d{1,2})(\-)(\d{2})$/},'dd/mm/yy':{day:1,month:3,year:5,sep:'/',parser:/^(\d{1,2})(\/)(\d{1,2})(\/)(\d{2})$/}},_daysInMonth:function(_m,_y){var nDays=0;if(_m===1||_m===3||_m===5||_m===7||_m===8||_m===10||_m===12)
{nDays=31;}
else if(_m===4||_m===6||_m===9||_m===11)
{nDays=30;}
else
{if((_y%400===0)||(_y%4===0&&_y%100!==0))
{nDays=29;}
else
{nDays=28;}}
return nDays;},_isValidDate:function(year,month,day){var yearRegExp=/^\d{4}$/;var validOneOrTwo=/^\d{1,2}$/;if(yearRegExp.test(year)&&validOneOrTwo.test(month)&&validOneOrTwo.test(day)){if(month>=1&&month<=12&&day>=1&&this._daysInMonth(month,year)>=day){return true;}}
return false;},mail:function(email)
{var emailValido=new RegExp("^[_a-z0-9-]+((\\.|\\+)[_a-z0-9-]+)*@([\\w]*-?[\\w]*\\.)+[a-z]{2,4}$","i");if(!emailValido.test(email)){return false;}else{return true;}},url:function(url,full)
{if(typeof full==="undefined"||full===false){var reHTTP=new RegExp("(^(http\\:\\/\\/|https\\:\\/\\/)(.+))","i");if(reHTTP.test(url)===false){url='http://'+url;}}
var reUrl=new RegExp("^(http:\\/\\/|https:\\/\\/)([\\w]*(-?[\\w]*)*\\.)+[a-z]{2,4}","i");if(reUrl.test(url)===false){return false;}else{return true;}},isPTPhone:function(phone)
{phone=phone.toString();var aInd=[];for(var i in this._indicativosPT){if(typeof(this._indicativosPT[i])==='string'){aInd.push(i);}}
var strInd=aInd.join('|');var re351=/^(00351|\+351)/;if(re351.test(phone)){phone=phone.replace(re351,"");}
var reSpecialChars=/(\s|\-|\.)+/g;phone=phone.replace(reSpecialChars,'');var reInt=/[\d]{9}/i;if(phone.length===9&&reInt.test(phone)){var reValid=new RegExp("^("+strInd+")");if(reValid.test(phone)){return true;}}
return false;},isPortuguesePhone:function(phone)
{return this.isPTPhone(phone);},isCVPhone:function(phone)
{phone=phone.toString();var aInd=[];for(var i in this._indicativosCV){if(typeof(this._indicativosCV[i])==='string'){aInd.push(i);}}
var strInd=aInd.join('|');var re238=/^(00238|\+238)/;if(re238.test(phone)){phone=phone.replace(re238,"");}
var reSpecialChars=/(\s|\-|\.)+/g;phone=phone.replace(reSpecialChars,'');var reInt=/[\d]{7}/i;if(phone.length===7&&reInt.test(phone)){var reValid=new RegExp("^("+strInd+")");if(reValid.test(phone)){return true;}}
return false;},isAOPhone:function(phone)
{phone=phone.toString();var aInd=[];for(var i in this._indicativosAO){if(typeof(this._indicativosAO[i])==='string'){aInd.push(i);}}
var strInd=aInd.join('|');var re244=/^(00244|\+244)/;if(re244.test(phone)){phone=phone.replace(re244,"");}
var reSpecialChars=/(\s|\-|\.)+/g;phone=phone.replace(reSpecialChars,'');var reInt=/[\d]{9}/i;if(phone.length===9&&reInt.test(phone)){var reValid=new RegExp("^("+strInd+")");if(reValid.test(phone)){return true;}}
return false;},isMZPhone:function(phone)
{phone=phone.toString();var aInd=[];for(var i in this._indicativosMZ){if(typeof(this._indicativosMZ[i])==='string'){aInd.push(i);}}
var strInd=aInd.join('|');var re258=/^(00258|\+258)/;if(re258.test(phone)){phone=phone.replace(re258,"");}
var reSpecialChars=/(\s|\-|\.)+/g;phone=phone.replace(reSpecialChars,'');var reInt=/[\d]{8,9}/i;if((phone.length===9||phone.length===8)&&reInt.test(phone)){var reValid=new RegExp("^("+strInd+")");if(reValid.test(phone)){if(phone.indexOf('2')===0&&phone.length===8){return true;}else if(phone.indexOf('8')===0&&phone.length===9){return true;}}}
return false;},isTLPhone:function(phone)
{phone=phone.toString();var aInd=[];for(var i in this._indicativosTL){if(typeof(this._indicativosTL[i])==='string'){aInd.push(i);}}
var strInd=aInd.join('|');var re670=/^(00670|\+670)/;if(re670.test(phone)){phone=phone.replace(re670,"");}
var reSpecialChars=/(\s|\-|\.)+/g;phone=phone.replace(reSpecialChars,'');var reInt=/[\d]{7}/i;if(phone.length===7&&reInt.test(phone)){var reValid=new RegExp("^("+strInd+")");if(reValid.test(phone)){return true;}}
return false;},isPhone:function(){var index;if(arguments.length===0){return false;}
var phone=arguments[0];if(arguments.length>1){if(arguments[1].constructor===Array){var func;for(index=0;index<arguments[1].length;index++){if(typeof(func=this['is'+arguments[1][index].toUpperCase()+'Phone'])==='function'){if(func(phone)){return true;}}else{throw"Invalid Country Code!";}}}else if(typeof(this['is'+arguments[1].toUpperCase()+'Phone'])==='function'){return this['is'+arguments[1].toUpperCase()+'Phone'](phone);}else{throw"Invalid Country Code!";}}else{for(index=0;index<this._countryCodes.length;index++){if(this['is'+this._countryCodes[index]+'Phone'](phone)){return true;}}}
return false;},codPostal:function(cp1,cp2,returnBothResults){var cPostalSep=/^(\s*\-\s*|\s+)$/;var trim=/^\s+|\s+$/g;var cPostal4=/^[1-9]\d{3}$/;var cPostal3=/^\d{3}$/;var parserCPostal=/^(.{4})(.*)(.{3})$/;returnBothResults=!!returnBothResults;cp1=cp1.replace(trim,'');if(typeof(cp2)!=='undefined'){cp2=cp2.replace(trim,'');if(cPostal4.test(cp1)&&cPostal3.test(cp2)){if(returnBothResults===true){return[true,true];}else{return true;}}}else{if(cPostal4.test(cp1)){if(returnBothResults===true){return[true,false];}else{return true;}}
var cPostal=cp1.match(parserCPostal);if(cPostal!==null&&cPostal4.test(cPostal[1])&&cPostalSep.test(cPostal[2])&&cPostal3.test(cPostal[3])){if(returnBothResults===true){return[true,false];}else{return true;}}}
if(returnBothResults===true){return[false,false];}else{return false;}},isDate:function(format,dateStr){if(typeof(this._dateParsers[format])==='undefined'){return false;}
var yearIndex=this._dateParsers[format].year;var monthIndex=this._dateParsers[format].month;var dayIndex=this._dateParsers[format].day;var dateParser=this._dateParsers[format].parser;var separator=this._dateParsers[format].sep;var trim=/^\w+|\w+$/g;var data=dateStr.match(dateParser);if(data!==null){if(data[2]===data[4]&&data[2]===separator){var _y=((data[yearIndex].length===2)?"20"+data[yearIndex].toString():data[yearIndex]);if(this._isValidDate(_y,data[monthIndex].toString(),data[dayIndex].toString())){return true;}}}
return false;},isColor:function(str){var match,valid=false,keyword=/^[a-zA-Z]+$/,hexa=/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/,rgb=/^rgb\(\s*([0-9]{1,3})(%)?\s*,\s*([0-9]{1,3})(%)?\s*,\s*([0-9]{1,3})(%)?\s*\)$/,rgba=/^rgba\(\s*([0-9]{1,3})(%)?\s*,\s*([0-9]{1,3})(%)?\s*,\s*([0-9]{1,3})(%)?\s*,\s*(1(\.0)?|0(\.[0-9])?)\s*\)$/,hsl=/^hsl\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})(%)?\s*,\s*([0-9]{1,3})(%)?\s*\)$/,hsla=/^hsla\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})(%)?\s*,\s*([0-9]{1,3})(%)?\s*,\s*(1(\.0)?|0(\.[0-9])?)\s*\)$/;if(keyword.test(str)||hexa.test(str)){return true;}
var i;if((match=rgb.exec(str))!==null||(match=rgba.exec(str))!==null){i=match.length;while(i--){if((i===2||i===4||i===6)&&typeof match[i]!=="undefined"&&match[i]!==""){if(typeof match[i-1]!=="undefined"&&match[i-1]>=0&&match[i-1]<=100){valid=true;}else{return false;}}
if(i===1||i===3||i===5&&(typeof match[i+1]==="undefined"||match[i+1]==="")){if(typeof match[i]!=="undefined"&&match[i]>=0&&match[i]<=255){valid=true;}else{return false;}}}}
if((match=hsl.exec(str))!==null||(match=hsla.exec(str))!==null){i=match.length;while(i--){if(i===3||i===5){if(typeof match[i-1]!=="undefined"&&typeof match[i]!=="undefined"&&match[i]!==""&&match[i-1]>=0&&match[i-1]<=100){valid=true;}else{return false;}}
if(i===1){if(typeof match[i]!=="undefined"&&match[i]>=0&&match[i]<=360){valid=true;}else{return false;}}}}
return valid;}};
SAPO.namespace('Communication');SAPO.Communication.Ajax=function(url,options){this.init(url,options);};SAPO.Communication.Ajax.version=2.1;SAPO.Communication.Ajax.globalOptions={parameters:{},requestHeaders:{}};var xMLHttpRequestWithCredentials='XMLHttpRequest'in window&&'withCredentials'in(new XMLHttpRequest());SAPO.Communication.Ajax.prototype={version:SAPO.Communication.Ajax.version,init:function(url,userOptions){if(!url){throw new Error("WRONG_ARGUMENTS_ERR");}
var options=SAPO.extendObj({asynchronous:true,method:'POST',parameters:null,timeout:0,delay:0,postBody:'',contentType:'application/x-www-form-urlencoded',requestHeaders:null,onComplete:null,onSuccess:null,onFailure:null,onException:null,onHeaders:null,onCreate:null,onInit:null,onTimeout:null,sanitizeJSON:false,evalJS:true,xhrProxy:'',cors:false,debug:false,useCredentials:false,signRequest:false},SAPO.Communication.Ajax.globalOptions);if(userOptions&&typeof userOptions==='object'){options=SAPO.extendObj(options,userOptions);if(typeof userOptions.parameters==='object'){options.parameters=SAPO.extendObj(SAPO.extendObj({},SAPO.Communication.Ajax.globalOptions.parameters),userOptions.parameters);}
else if(userOptions.parameters!=null){var globalParameters=this.paramsObjToStr(SAPO.Communication.Ajax.globalOptions.parameters);if(globalParameters){options.parameters=userOptions.parameters+'&'+globalParameters;}}
options.requestHeaders=SAPO.extendObj({},SAPO.Communication.Ajax.globalOptions.requestHeaders);options.requestHeaders=SAPO.extendObj(options.requestHeaders,userOptions.requestHeaders);}
this.options=options;this.safeCall('onInit');var urlLocation=document.createElementNS?document.createElementNS('http://www.w3.org/1999/xhtml','a'):document.createElement('a');urlLocation.href=url;this.url=url;this.isHTTP=urlLocation.protocol.match(/^https?:$/i)&&true;this.requestHasBody=options.method.search(/^get|head$/i)<0;if(!this.isHTTP||location.protocol=='widget:'||typeof window.widget==='object'){this.isCrossDomain=false;}else{this.isCrossDomain=location.protocol!=urlLocation.protocol||location.host!=urlLocation.host.split(':')[0];}
if(this.options.cors){this.isCrossDomain=false;}
this.transport=this.getTransport();this.request();},getTransport:function()
{if(!xMLHttpRequestWithCredentials&&this.options.cors&&'XDomainRequest'in window){this.usingXDomainReq=true;return new XDomainRequest();}
else if(typeof XMLHttpRequest!=='undefined'){return new XMLHttpRequest();}
else if(typeof ActiveXObject!=='undefined'){try{return new ActiveXObject('Msxml2.XMLHTTP');}catch(e){return new ActiveXObject('Microsoft.XMLHTTP');}}else{return null;}},setHeaders:function()
{if(this.transport){try{var headers={"Accept":"text/javascript,text/xml,application/xml,application/xhtml+xml,text/html,application/json;q=0.9,text/plain;q=0.8,video/x-mng,image/png,image/jpeg,image/gif;q=0.2,*/*;q=0.1","Accept-Language":navigator.language,"X-Requested-With":"XMLHttpRequest","X-SAPO-Version":"0.1"};if(this.options.cors){if(!this.options.signRequest){delete headers['X-Requested-With'];}
delete headers['X-SAPO-Version'];}
if(this.options.requestHeaders&&typeof this.options.requestHeaders==='object'){for(var headerReqName in this.options.requestHeaders){headers[headerReqName]=this.options.requestHeaders[headerReqName];}}
if(this.transport.overrideMimeType&&(navigator.userAgent.match(/Gecko\/(\d{4})/)||[0,2005])[1]<2005){headers['Connection']='close';}
for(var headerName in headers){if(headers.hasOwnProperty(headerName)){this.transport.setRequestHeader(headerName,headers[headerName]);}}}catch(e){}}},paramsObjToStr:function(optParams){var k,m,p,a,params=[];if(typeof optParams==='object'){for(p in optParams){if(optParams.hasOwnProperty(p)){a=optParams[p];if(Object.prototype.toString.call(a)==='[object Array]'&&!isNaN(a.length)){for(k=0,m=a.length;k<m;k++){params=params.concat([encodeURIComponent(p),'=',encodeURIComponent(a[k]),'&']);}}
else{params=params.concat([encodeURIComponent(p),'=',encodeURIComponent(a),'&']);}}}
if(params.length>0){params.pop();}}
else
{return optParams;}
return params.join('');},setParams:function()
{var params=null,optParams=this.options.parameters;if(typeof optParams==="object"){params=this.paramsObjToStr(optParams);}else{params=''+optParams;}
if(params){if(this.url.indexOf('?')>-1){this.url=this.url.split('#')[0]+'&'+params;}else{this.url=this.url.split('#')[0]+'?'+params;}}},getHeader:function(name)
{if(this.usingXDomainReq&&name==='Content-Type'){return this.transport.contentType;}
try{return this.transport.getResponseHeader(name);}catch(e){return null;}},getAllHeaders:function()
{try{return this.transport.getAllResponseHeaders();}catch(e){return null;}},getResponse:function(){var t=this.transport,r={headerJSON:null,responseJSON:null,getHeader:this.getHeader,getAllHeaders:this.getAllHeaders,request:this,transport:t,timeTaken:new Date()-this.startTime,requestedUrl:this.url};r.readyState=t.readyState;try{r.responseText=t.responseText;}catch(e){}
try{r.responseXML=t.responseXML;}catch(e){}
try{r.status=t.status;}catch(e){r.status=0;}
try{r.statusText=t.statusText;}catch(e){r.statusText='';}
return r;},abort:function(){if(this.transport){clearTimeout(this.delayTimeout);clearTimeout(this.stoTimeout);try{this.transport.abort();}catch(ex){}
this.finish();}},runStateChange:function()
{var rs=this.transport.readyState;if(rs===3){if(this.isHTTP){this.safeCall('onHeaders');}}else if(rs===4||this.usingXDomainReq){if(this.options.asynchronous&&this.options.delay&&(this.startTime+this.options.delay>new Date().getTime())){this.delayTimeout=setTimeout(this.runStateChange.bindObj(this),this.options.delay+this.startTime-new Date().getTime());return;}
var responseJSON,responseContent=this.transport.responseText,response=this.getResponse(),curStatus=this.transport.status;if(this.isHTTP&&!this.options.asynchronous){this.safeCall('onHeaders');}
clearTimeout(this.stoTimeout);if(curStatus===0){if(this.isHTTP){this.safeCall('onException',this.makeError(18,'NETWORK_ERR'));}else{curStatus=responseContent?200:404;}}
else if(curStatus===304){curStatus=200;}
var isSuccess=this.usingXDomainReq||200<=curStatus&&curStatus<300;var headerContentType=this.getHeader('Content-Type')||'';if(this.options.evalJS&&(headerContentType.indexOf("application/json")>=0||this.options.evalJS==='force')){try{responseJSON=this.evalJSON(responseContent,this.sanitizeJSON);if(responseJSON){responseContent=response.responseJSON=responseJSON;}}catch(e){if(isSuccess){this.safeCall('onException',e);}}}
if(this.usingXDomainReq&&headerContentType.indexOf('xml')!==-1&&'DOMParser'in window){var mimeType;switch(headerContentType){case'application/xml':case'application/xhtml+xml':case'image/svg+xml':mimeType=headerContentType;break;default:mimeType='text/xml';}
var xmlDoc=(new DOMParser()).parseFromString(this.transport.responseText,mimeType);this.transport.responseXML=xmlDoc;response.responseXML=xmlDoc;}
if(this.transport.responseXML!=null&&response.responseJSON==null&&this.transport.responseXML.xml!==""){responseContent=this.transport.responseXML;}
if(curStatus||this.usingXDomainReq){if(isSuccess){this.safeCall('onSuccess',response,responseContent);}else{this.safeCall('onFailure',response,responseContent);}
this.safeCall('on'+curStatus,response,responseContent);}
this.finish(response,responseContent);}},finish:function(response,responseContent){if(response){this.safeCall('onComplete',response,responseContent);}
clearTimeout(this.stoTimeout);if(this.transport){try{this.transport.onreadystatechange=null;}catch(e){}
if(typeof this.transport.destroy==='function'){this.transport.destroy();}
this.transport=null;}},safeCall:function(listener,first,second){function rethrow(exception){setTimeout(function(){if(exception.message){exception.message+='\n'+(exception.stacktrace||exception.stack||'');}
throw exception;},1);}
if(typeof this.options[listener]==='function'){SAPO.safeCall(this,this.options[listener],first,second);}else if(first&&window.Error&&(first instanceof Error)){rethrow(first);}},setRequestHeader:function(name,value){if(!this.options.requestHeaders){this.options.requestHeaders={};}
this.options.requestHeaders[name]=value;},request:function()
{if(this.transport){var params=null;if(this.requestHasBody){if(this.options.postBody!=null&&this.options.postBody!==''){params=this.options.postBody;this.setParams();}else if(this.options.parameters!=null&&this.options.parameters!==''){params=this.options.parameters;}
if(typeof params==="object"&&!params.nodeType){params=this.paramsObjToStr(params);}else if(typeof params!=="object"&&params!=null){params=''+params;}
if(this.options.contentType){this.setRequestHeader('Content-Type',this.options.contentType);}}else{this.setParams();}
var url=this.url;var method=this.options.method;var crossDomain=this.isCrossDomain;if(crossDomain&&this.options.xhrProxy){this.setRequestHeader('X-Url',url);url=this.options.xhrProxy+encodeURIComponent(url);crossDomain=false;}
try{this.transport.open(method,url,this.options.asynchronous);}catch(e){this.safeCall('onException',e);return this.finish(this.getResponse(),null);}
this.setHeaders();this.safeCall('onCreate');if(this.options.timeout&&!isNaN(this.options.timeout)){this.stoTimeout=setTimeout(function(){if(this.options.onTimeout){this.safeCall('onTimeout');this.abort();}}.bindObj(this),(this.options.timeout*1000));}
if(this.options.useCredentials&&!this.usingXDomainReq){this.transport.withCredentials=true;}
if(this.options.asynchronous&&!this.usingXDomainReq){this.transport.onreadystatechange=this.runStateChange.bindObj(this);}
else if(this.usingXDomainReq){this.transport.onload=this.runStateChange.bindObj(this);}
try{if(crossDomain){throw this.makeError(18,'NETWORK_ERR');}else{this.startTime=new Date().getTime();this.transport.send(params);}}catch(e){this.safeCall('onException',e);return this.finish(this.getResponse(),null);}
if(!this.options.asynchronous){this.runStateChange();}}},makeError:function(code,message){if(typeof Error!=='function'){return{code:code,message:message};}
var e=new Error(message);e.code=code;return e;},isJSON:function(str)
{if(typeof str!=="string"||!str){return false;}
str=str.replace(/\\./g,'@').replace(/"[^"\\\n\r]*"/g,'');return(/^[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]*$/).test(str);},evalJSON:function(strJSON,sanitize)
{if(strJSON&&(!sanitize||this.isJSON(strJSON))){try{if(typeof JSON!=="undefined"&&typeof JSON.parse!=='undefined'){return JSON.parse(strJSON);}
return eval('('+strJSON+')');}catch(e){throw new Error('ERROR: Bad JSON string...');}}
return null;}};SAPO.Communication.Ajax.load=function(url,callback){return new SAPO.Communication.Ajax(url,{method:'GET',onSuccess:function(response){callback(response.responseText,response);}});};SAPO.Communication.Ajax.ping=function(url,callback){return new SAPO.Communication.Ajax(url,{method:'HEAD',onSuccess:function(response){if(typeof callback==='function'){callback(response);}}});};
(function(undefined){'use strict';SAPO.namespace('Ink');var instances={};var lastIdNum=0;var Ajax=SAPO.Communication.Ajax,Css=SAPO.Dom.Css,Selector=SAPO.Dom.Selector,Url=SAPO.Utility.Url;SAPO.Ink.Aux={Layouts:{SMALL:'s',MEDIUM:'m',LARGE:'l'},isDOMElement:function(o){return(typeof o==='object'&&'nodeType'in o&&o.nodeType===1);},isInteger:function(n){return(typeof n==='number'&&n%1===0);},elOrSelector:function(elOrSelector,fieldName){if(!this.isDOMElement(elOrSelector)){var t=Selector.select(elOrSelector);if(t.length===0){throw new TypeError(fieldName+' must either be a DOM Element or a selector expression!\nThe script element must also be after the DOM Element itself.');}
return t[0];}
return elOrSelector;},clone:function(o){try{if(typeof o!=='object'){throw new Error('Given argument is not an object!');}
return JSON.parse(JSON.stringify(o));}catch(ex){throw new Error('Given object cannot have loops!');}},childIndex:function(childEl){var els=Selector.select('> *',childEl.parentNode);for(var i=0,f=els.length;i<f;++i){if(els[i]===childEl){return i;}}
throw'not found!';},ajaxJSON:function(endpoint,params,cb){new Ajax(endpoint,{evalJS:'force',method:'POST',parameters:params,onSuccess:function(r){try{r=r.responseJSON;if(r.status!=='ok'){throw'server error: '+r.message;}
cb(null,r);}catch(ex){cb(ex);}},onFailure:function(){cb('communication failure');}});},currentLayout:function(){var i,f,k,v,el,detectorEl=Selector.select('#ink-layout-detector')[0];if(!detectorEl){detectorEl=document.createElement('div');detectorEl.id='ink-layout-detector';for(k in this.Layouts){if(this.Layouts.hasOwnProperty(k)){v=this.Layouts[k];el=document.createElement('div');el.className='ink-for-'+v;el.setAttribute('data-ink-layout',v);detectorEl.appendChild(el);}}
document.body.appendChild(detectorEl);}
for(i=0,f=detectorEl.childNodes.length;i<f;++i){el=detectorEl.childNodes[i];if(Css.getStyle(el,'visibility')!=='hidden'){return el.getAttribute('data-ink-layout');}}},hashSet:function(o){if(typeof o!=='object'){throw new TypeError('o should be an object!');}
var hashParams=Url.getAnchorString();hashParams=SAPO.extendObj(hashParams,o);window.location.hash=Url.genQueryString('',hashParams).substring(1);},cleanChildren:function(parentEl){var prevEl,el=parentEl.lastChild;while(el){prevEl=el.previousSibling;parentEl.removeChild(el);el=prevEl;}},storeIdAndClasses:function(fromEl,inObj){var id=fromEl.id;if(id){inObj._id=id;}
var classes=fromEl.className;if(classes){inObj._classes=classes;}},restoreIdAndClasses:function(toEl,inObj){if(inObj._id&&toEl.id!==inObj._id){toEl.id=inObj._id;}
if(inObj._classes&&toEl.className.indexOf(inObj._classes)===-1){if(toEl.className){toEl.className+=' '+inObj._classes;}
else{toEl.className=inObj._classes;}}
if(inObj._instanceId&&!toEl.getAttribute('data-instance')){toEl.setAttribute('data-instance',inObj._instanceId);}},registerInstance:function(inst,el,optionalPrefix){if(inst._instanceId){return;}
if(typeof inst!=='object'){throw new TypeError('1st argument must be a JavaScript object!');}
if(inst._options&&inst._options.skipRegister){return;}
if(!this.isDOMElement(el)){throw new TypeError('2nd argument must be a DOM element!');}
if(optionalPrefix!==undefined&&typeof optionalPrefix!=='string'){throw new TypeError('3rd argument must be a string!');}
var id=(optionalPrefix||'instance')+(++lastIdNum);instances[id]=inst;inst._instanceId=id;var dataInst=el.getAttribute('data-instance');dataInst=(dataInst!==null)?[dataInst,id].join(' '):id;el.setAttribute('data-instance',dataInst);},unregisterInstance:function(id){delete instances[id];},getInstanceFromSelector:function(selector){var el=Selector.select(selector)[0];if(!el){throw new Error('Element not found!');}
return this.getInstance(el);},getInstance:function(instanceIdOrElement){var ids;if(this.isDOMElement(instanceIdOrElement)){ids=instanceIdOrElement.getAttribute('data-instance');if(ids===null){throw new Error('argument is not a DOM instance element!');}}
else{ids=instanceIdOrElement;}
ids=ids.split(' ');var inst,id,i,l=ids.length;var res=[];for(i=0;i<l;++i){id=ids[i];if(!id){throw new Error('Element is not a JS instance!');}
inst=instances[id];if(!inst){throw new Error('Instance "'+id+'" not found!');}
res.push(inst);}
return(l===1)?res[0]:res;},getInstanceIds:function(){var res=[];for(var id in instances){if(instances.hasOwnProperty(id)){res.push(id);}}
return res;},getInstances:function(){var res=[];for(var id in instances){if(instances.hasOwnProperty(id)){res.push(instances[id]);}}
return res;},destroyComponent:function(){SAPO.Ink.Aux.unregisterInstance(this._instanceId);this._element.parentNode.removeChild(this._element);}};SAPO.Dom.Loaded.run(function(){SAPO.Ink.Close();var collapsibles=Selector.select('.ink-collapsible');collapsibles.forEach(function(collapsible){new SAPO.Ink.Collapsible(collapsible);});var dockables=Selector.select('.ink-dockable');dockables.forEach(function(dockable){new SAPO.Ink.Dockable(dockable);});});})();
(function(undefined){SAPO.namespace('Ink');var Aux=SAPO.Ink.Aux,Element=SAPO.Dom.Element,Event=SAPO.Dom.Event;SAPO.Ink.DatePicker=function(selector,options){this._options=SAPO.extendObj({instance:'scdp_'+Math.round(99999*Math.random()),format:'yyyy-mm-dd',cssClass:'sapo_component_datepicker',position:'right',onFocus:true,validDayFn:undefined,startDate:false,onSetDate:false,displayInSelect:false,showClose:true,showClean:true,yearRange:false,dateRange:false,startWeekDay:1,closeText:'Fechar',cleanText:'Limpar',prevLinkText:'«',nextLinkText:'»',ofText:'&nbsp;de&nbsp;',month:{1:'Janeiro',2:'Fevereiro',3:'Mar&ccedil;o',4:'Abril',5:'Maio',6:'Junho',7:'Julho',8:'Agosto',9:'Setembro',10:'Outubro',11:'Novembro',12:'Dezembro'},wDay:{0:'Domingo',1:'Segunda',2:'Ter&ccedil;a',3:'Quarta',4:'Quinta',5:'Sexta',6:'S&aacute;bado'}},options||{});this._options.format=this._dateParsers[this._options.format]||this._options.format;this._hoverPicker=false;if(selector){this._dataField=Aux.elOrSelector(selector,'1st argument');}
this._picker=null;if(this._options.pickerField){this._picker=Aux.elOrSelector(this._options.pickerField,'pickerField');}
this._today=new Date();this._day=this._today.getDate();this._month=this._today.getMonth();this._year=this._today.getFullYear();this._setMinMax(this._options.dateRange||this._options.yearRange);if(this._options.startDate&&typeof this._options.startDate==='string'&&/\d\d\d\d\-\d\d\-\d\d/.test(this._options.startDate)){var parsed=this._options.startDate.split("-");this._year=parsed[0];this._month=parsed[1]-1;this._day=parsed[2];}
this._data=new Date(Date.UTC.apply(this,this._checkDateRange(this._year,this._month,this._day)));this._init();this._render();if(this._options.startDate)this.setDate();Aux.registerInstance(this,this._containerObject,'datePicker');};SAPO.Ink.DatePicker.prototype={version:'0.1',_init:function(){},_render:function(){this._containerObject=document.createElement('div');this._containerObject.id=this._options.instance;this._containerObject.className='sapo_component_datepicker';var dom=document.getElementsByTagName('body')[0];if(this._options.showClose||this._options.showClean){this._superTopBar=document.createElement("div");this._superTopBar.className='sapo_cal_top_options';if(this._options.showClean){var clean=document.createElement('a');clean.className='clean';clean.innerHTML=this._options.cleanText;this._superTopBar.appendChild(clean);}
if(this._options.showClose){var close=document.createElement('a');close.className='close';close.innerHTML=this._options.closeText;this._superTopBar.appendChild(close);}
this._containerObject.appendChild(this._superTopBar);}
var calendarTop=document.createElement("div");calendarTop.className='sapo_cal_top';this._monthDescContainer=document.createElement("div");this._monthDescContainer.className='sapo_cal_month_desc';this._monthPrev=document.createElement('div');this._monthPrev.className='sapo_cal_prev';this._monthPrev.innerHTML='<a href="#prev" class="change_month_prev">'+this._options.prevLinkText+'</a>';this._monthNext=document.createElement('div');this._monthNext.className='sapo_cal_next';this._monthNext.innerHTML='<a href="#next" class="change_month_next">'+this._options.nextLinkText+'</a>';calendarTop.appendChild(this._monthPrev);calendarTop.appendChild(this._monthDescContainer);calendarTop.appendChild(this._monthNext);this._monthContainer=document.createElement("div");this._monthContainer.className='sapo_cal_month';this._containerObject.appendChild(calendarTop);this._containerObject.appendChild(this._monthContainer);this._monthSelector=document.createElement('ul');this._monthSelector.className='sapo_cal_month_selector';var ulSelector;var liMonth;for(var i=1;i<=12;i++){if((i-1)%4===0){ulSelector=document.createElement('ul');}
liMonth=document.createElement('li');liMonth.innerHTML='<a href="#" class="sapo_calmonth_'+((String(i).length===2)?i:"0"+i)+'">'+this._options.month[i].substring(0,3)+'</a>';ulSelector.appendChild(liMonth);if(i%4===0){this._monthSelector.appendChild(ulSelector);}}
this._containerObject.appendChild(this._monthSelector);this._yearSelector=document.createElement('ul');this._yearSelector.className='sapo_cal_year_selector';this._containerObject.appendChild(this._yearSelector);if(!this._options.onFocus||this._options.displayInSelect){if(!this._options.pickerField){this._picker=document.createElement('a');this._picker.href='#open_cal';this._picker.innerHTML='open';this._picker.style.position='absolute';this._picker.style.top=Element.elementTop(this._dataField);this._picker.style.left=Element.elementLeft(this._dataField)+(Element.elementWidth(this._dataField)||0)+5+'px';this._dataField.parentNode.appendChild(this._picker);this._picker.className='sapo_cal_date_picker';}else{this._picker=Aux.elOrSelector(this._options.pickerField,'pickerField');}}
if(this._options.displayInSelect){if(this._options.dayField&&this._options.monthField&&this._options.yearField||this._options.pickerField){this._options.dayField=Aux.elOrSelector(this._options.dayField,'dayField');this._options.monthField=Aux.elOrSelector(this._options.monthField,'monthField');this._options.yearField=Aux.elOrSelector(this._options.yearField,'yearField');}
else{throw"To use display in select you *MUST* to set dayField, monthField, yearField and pickerField!";}}
dom.insertBefore(this._containerObject,dom.childNodes[0]);if(!this._picker){Event.observe(this._dataField,'focus',function(){this._containerObject=Element.clonePosition(this._containerObject,this._dataField);if(this._options.position=='bottom')
{this._containerObject.style.top=Element.elementHeight(this._dataField)+Element.offsetTop(this._dataField)+'px';}
else
{this._containerObject.style.left=Element.elementWidth(this._dataField)+Element.offsetLeft(this._dataField)+'px';}
dom.appendChild(this._containerObject);this._updateDate();this._showMonth();this._containerObject.style.display='block';}.bindObjEvent(this));}
else{Event.observe(this._picker,'click',function(e){Event.stop(e);this._containerObject=Element.clonePosition(this._containerObject,this._picker);this._updateDate();this._showMonth();this._containerObject.style.display='block';}.bindObjEvent(this));}
if(!this._options.displayInSelect){Event.observe(this._dataField,'change',function(){this._updateDate();this._showDefaultView();this.setDate();if(!this._hoverPicker)
{this._containerObject.style.display='none';}}.bindObjEvent(this));Event.observe(this._dataField,'blur',function(){if(!this._hoverPicker)
{this._containerObject.style.display='none';}}.bindObjEvent(this));}
else{Event.observe(this._options.dayField,'change',function(){var yearSelected=this._options.yearField[this._options.yearField.selectedIndex].value;if(yearSelected!==''&&yearSelected!==0){this._updateDate();this._showDefaultView();}}.bindObjEvent(this));Event.observe(this._options.monthField,'change',function(){var yearSelected=this._options.yearField[this._options.yearField.selectedIndex].value;if(yearSelected!==''&&yearSelected!==0){this._updateDate();this._showDefaultView();}}.bindObjEvent(this));Event.observe(this._options.yearField,'change',function(){this._updateDate();this._showDefaultView();}.bindObjEvent(this));}
Event.observe(document,'click',function(e){if(e.target===undefined){e.target=e.srcElement;}
if(!Element.descendantOf(this._containerObject,e.target)&&e.target!==this._dataField){if(!this._picker){this._containerObject.style.display='none';}
else if(e.target!==this._picker&&(!this._options.displayInSelect||(e.target!==this._options.dayField&&e.target!==this._options.monthField&&e.target!==this._options.yearField))){if(!this._options.dayField||(!Element.descendantOf(this._options.dayField,e.target)&&!Element.descendantOf(this._options.monthField,e.target)&&!Element.descendantOf(this._options.yearField,e.target))){this._containerObject.style.display='none';}}}}.bindObjEvent(this));this._showMonth();this._monthChanger=document.createElement('a');this._monthChanger.href='#monthchanger';this._monthChanger.className='sapo_cal_link_month';this._monthChanger.innerHTML=this._options.month[this._month+1];this._deText=document.createElement('span');this._deText.innerHTML=this._options._deText;this._yearChanger=document.createElement('a');this._yearChanger.href='#yearchanger';this._yearChanger.className='sapo_cal_link_year';this._yearChanger.innerHTML=this._year;this._monthDescContainer.innerHTML='';this._monthDescContainer.appendChild(this._monthChanger);this._monthDescContainer.appendChild(this._deText);this._monthDescContainer.appendChild(this._yearChanger);Event.observe(this._containerObject,'mouseover',function(e)
{Event.stop(e);this._hoverPicker=true;}.bindObjEvent(this));Event.observe(this._containerObject,'mouseout',function(e)
{Event.stop(e);this._hoverPicker=false;}.bindObjEvent(this));Event.observe(this._containerObject,'click',function(e){if(typeof(e.target)==='undefined'){e.target=e.srcElement;}
var className=e.target.className;var isInactive=className.indexOf('sapo_cal_off')!==-1;Event.stop(e);if(className.indexOf('sapo_cal_')===0&&!isInactive){var day=className.substr(9,2);if(Number(day)){this.setDate(this._year+'-'+(this._month+1)+'-'+day);this._containerObject.style.display='none';}else if(className==='sapo_cal_link_month'){this._monthContainer.style.display='none';this._yearSelector.style.display='none';this._monthPrev.childNodes[0].className='action_inactive';this._monthNext.childNodes[0].className='action_inactive';this._setActiveMonth();this._monthSelector.style.display='block';}else if(className==='sapo_cal_link_year'){this._monthPrev.childNodes[0].className='action_inactive';this._monthNext.childNodes[0].className='action_inactive';this._monthSelector.style.display='none';this._monthContainer.style.display='none';this._showYearSelector();this._yearSelector.style.display='block';}}else if(className.indexOf("sapo_calmonth_")===0&&!isInactive){var month=className.substr(14,2);if(Number(month)){this._month=month-1;this._monthSelector.style.display='none';this._monthPrev.childNodes[0].className='change_month_prev';this._monthNext.childNodes[0].className='change_month_next';if(this._year<this._yearMin||this._year==this._yearMin&&this._month<=this._monthMin){this._monthPrev.childNodes[0].className='action_inactive';}
else if(this._year>this._yearMax||this._year==this._yearMax&&this._month>=this._monthMax){this._monthNext.childNodes[0].className='action_inactive';}
this._updateCal();this._monthContainer.style.display='block';}}else if(className.indexOf("sapo_calyear_")===0&&!isInactive){var year=className.substr(13,4);if(Number(year)){this._year=year;this._monthPrev.childNodes[0].className='action_inactive';this._monthNext.childNodes[0].className='action_inactive';this._yearSelector.style.display='none';this._setActiveMonth();this._monthSelector.style.display='block';}}else if(className.indexOf('change_month_')===0&&!isInactive){if(className==='change_month_next'){this._updateCal(1);}else if(className==='change_month_prev'){this._updateCal(-1);}}else if(className.indexOf('change_year_')===0&&!isInactive){if(className==='change_year_next'){this._showYearSelector(1);}else if(className==='change_year_prev'){this._showYearSelector(-1);}}else if(className==='clean'){if(this._options.displayInSelect){this._options.yearField.selectedIndex=0;this._options.monthField.selectedIndex=0;this._options.dayField.selectedIndex=0;}else{this._dataField.value='';}}else if(className==='close'){this._containerObject.style.display='none';}
this._updateDescription();}.bindObjEvent(this));},_setMinMax:function(dateRange)
{if(dateRange)
{var dates=dateRange.split(':');var pattern=/^(\d{4})((\-)(\d{1,2})((\-)(\d{1,2}))?)?$/;if(dates[0])
{if(dates[0]=='NOW')
{this._yearMin=this._today.getFullYear();this._monthMin=this._today.getMonth()+1;this._dayMin=this._today.getDate();}
else if(pattern.test(dates[0]))
{var auxDate=dates[0].split('-');this._yearMin=Math.floor(auxDate[0]);this._monthMin=Math.floor(auxDate[1])||1;this._dayMin=Math.floor(auxDate[2])||1;if(1<this._monthMin&&this._monthMin>12)
{this._monthMin=1;this._dayMin=1;}
if(1<this._monthMin&&this._monthMin>this._daysInMonth(this._yearMin,this._monthMin))
{this._dayMin=1;}}
else
{this._yearMin=Number.MIN_VALUE;this._monthMin=1;this._dayMin=1;}}
if(dates[1])
{if(dates[1]=='NOW')
{this._yearMax=this._today.getFullYear();this._monthMax=this._today.getMonth()+1;this._dayMax=this._today.getDate();}
else if(pattern.test(dates[1]))
{var auxDate=dates[1].split('-');this._yearMax=Math.floor(auxDate[0]);this._monthMax=Math.floor(auxDate[1])||12;this._dayMax=Math.floor(auxDate[2])||this._daysInMonth(this._yearMax,this._monthMax);if(1<this._monthMax&&this._monthMax>12)
{this._monthMax=12;this._dayMax=31;}
var MDay=this._daysInMonth(this._yearMax,this._monthMax);if(1<this._monthMax&&this._monthMax>MDay)
{this._dayMax=MDay;}}
else
{this._yearMax=Number.MAX_VALUE;this._monthMax=12;this._dayMaXx=31;}}
if(!(this._yearMax>=this._yearMin&&this._monthMax>=this._monthMin&&this._dayMax>=this._dayMin))
{this._yearMin=Number.MIN_VALUE;this._monthMin=1;this._dayMin=1;this._yearMax=Number.MAX_VALUE;this._monthMax=12;this._dayMaXx=31;}}
else
{this._yearMin=Number.MIN_VALUE;this._monthMin=1;this._dayMin=1;this._yearMax=Number.MAX_VALUE;this._monthMax=12;this._dayMaXx=31;}},_checkDateRange:function(year,month,day)
{if(!this._isValidDate(year,month+1,day))
{year=this._today.getFullYear();month=this._today.getMonth();day=this._today.getDate();}
if(year>this._yearMax)
{year=this._yearMax;month=this._monthMax-1;day=this._dayMax;}
else if(year<this._yearMin)
{year=this._yearMin;month=this._monthMin-1;day=this._dayMin;}
if(year==this._yearMax&&month+1>this._monthMax)
{month=this._monthMax-1;day=this._dayMax;}
else if(year==this._yearMin&&month+1<this._monthMin)
{month=this._monthMin-1;day=this._dayMin;}
if(year==this._yearMax&&month+1==this._monthMax&&day>this._dayMax)day=this._dayMax;else if(year==this._yearMin&&month+1==this._monthMin&&day<this._dayMin)day=this._dayMin;else if(day>this._daysInMonth(year,month+1))day=this._daysInMonth(year,month+1);return[year,month,day];},_showDefaultView:function(){this._yearSelector.style.display='none';this._monthSelector.style.display='none';this._monthPrev.childNodes[0].className='change_month_prev';this._monthNext.childNodes[0].className='change_month_next';if(this._year<this._yearMin||this._year==this._yearMin&&this._month+1<=this._monthMin){this._monthPrev.childNodes[0].className='action_inactive';}
else if(this._year>this._yearMax||this._year==this._yearMax&&this._month+1>=this._monthMax){this._monthNext.childNodes[0].className='action_inactive';}
this._monthContainer.style.display='block';},_updateDate:function(){var dataParsed;if(!this._options.displayInSelect){if(this._dataField.value!==''){if(this._isDate(this._options.format,this._dataField.value)){dataParsed=this._getDataArrayParsed(this._dataField.value);dataParsed=this._checkDateRange(dataParsed[0],dataParsed[1]-1,dataParsed[2]);this._year=dataParsed[0];this._month=dataParsed[1];this._day=dataParsed[2];}else{this._dataField.value='';this._year=this._data.getFullYear();this._month=this._data.getMonth();this._day=this._data.getDate();}
this._data.setFullYear(this._year,this._month,this._day);this._dataField.value=this._writeDateInFormat();}}else{dataParsed=[];if(this._isValidDate(dataParsed[0]=this._options.yearField[this._options.yearField.selectedIndex].value,dataParsed[1]=this._options.monthField[this._options.monthField.selectedIndex].value,dataParsed[2]=this._options.dayField[this._options.dayField.selectedIndex].value)){dataParsed=this._checkDateRange(dataParsed[0],dataParsed[1]-1,dataParsed[2]);this._year=dataParsed[0];this._month=dataParsed[1];this._day=dataParsed[2];}else{dataParsed=this._checkDateRange(dataParsed[0],dataParsed[1]-1,1);if(this._isValidDate(dataParsed[0],dataParsed[1]+1,dataParsed[2])){this._year=dataParsed[0];this._month=dataParsed[1];this._day=this._daysInMonth(dataParsed[0],dataParsed[1]);this.setDate();}}}
this._updateDescription();this._showMonth();},_updateDescription:function(){this._monthChanger.innerHTML=this._options.month[this._month+1];this._deText.innerHTML=this._options.ofText;this._yearChanger.innerHTML=this._year;},_showYearSelector:function(){if(arguments.length){var year=+this._year+arguments[0]*10;year=year-year%10;if(year>this._yearMax||year+9<this._yearMin){return;}
this._year=+this._year+arguments[0]*10;}
var str="<li>";var ano_base=this._year-(this._year%10);for(var i=0;i<=11;i++){if(i%4===0){str+='<ul>';}
if(!i||i===11){if(i&&(ano_base+i-1)<=this._yearMax&&(ano_base+i-1)>=this._yearMin){str+='<li><a href="#year_next" class="change_year_next">'+this._options.nextLinkText+'</a></li>';}else if((ano_base+i-1)<=this._yearMax&&(ano_base+i-1)>=this._yearMin){str+='<li><a href="#year_prev" class="change_year_prev">'+this._options.prevLinkText+'</a></li>';}else{str+='<li>&nbsp;</li>';}}else{if((ano_base+i-1)<=this._yearMax&&(ano_base+i-1)>=this._yearMin){str+='<li><a href="#" class="sapo_calyear_'+(ano_base+i-1)+(((ano_base+i-1)===this._data.getFullYear())?' sapo_cal_on':'')+'">'+(ano_base+i-1)+'</a></li>';}else{str+='<li><a href="#" class="sapo_cal_off">'+(ano_base+i-1)+'</a></li>';}}
if((i+1)%4===0){str+='</ul>';}}
str+="</li>";this._yearSelector.innerHTML=str;},_getDataArrayParsed:function(dateStr){var arrData=[];var data=SAPO.Utility.Date.set(this._options.format,dateStr);if(data){arrData=[data.getFullYear(),data.getMonth()+1,data.getDate()];}
return arrData;},_isValidDate:function(year,month,day){var yearRegExp=/^\d{4}$/;var validOneOrTwo=/^\d{1,2}$/;return(yearRegExp.test(year)&&validOneOrTwo.test(month)&&validOneOrTwo.test(day)&&month>=1&&month<=12&&day>=1&&day<=this._daysInMonth(year,month));},_isDate:function(format,dateStr){try{if(typeof format==='undefined'){return false;}
var data=SAPO.Utility.Date.set(format,dateStr);if(data&&this._isValidDate(data.getFullYear(),data.getMonth()+1,data.getDate())){return true;}}catch(ex){}
return false;},_writeDateInFormat:function(){return SAPO.Utility.Date.get(this._options.format,this._data);},setDate:function(dateString)
{if(typeof dateString=='string'&&/\d{4}-\d{1,2}-\d{1,2}/.test(dateString))
{var auxDate=dateString.split('-');this._year=auxDate[0];this._month=auxDate[1]-1;this._day=auxDate[2];}
this._setDate();},_setDate:function(objClicked){if(typeof objClicked!=='undefined'&&objClicked.className&&objClicked.className.indexOf('sapo_cal_')===0)
{this._day=objClicked.className.substr(9,2);}
this._data.setFullYear.apply(this._data,this._checkDateRange(this._year,this._month,this._day));if(!this._options.displayInSelect){this._dataField.value=this._writeDateInFormat();}else{this._options.dayField.value=this._data.getDate();this._options.monthField.value=this._data.getMonth()+1;this._options.yearField.value=this._data.getFullYear();}
if(this._options.onSetDate){this._options.onSetDate(this,{date:this._data});}},_updateCal:function(inc){this._updateMonth(inc);this._showMonth();},_daysInMonth:function(_y,_m){var nDays=31;switch(_m){case 2:nDays=((_y%400===0)||(_y%4===0&&_y%100!==0))?29:28;break;case 4:case 6:case 9:case 11:nDays=30;break;}
return nDays;},_updateMonth:function(incValue){if(typeof incValue==='undefined'){incValue="0";}
var mes=this._month+1;var ano=this._year;switch(incValue){case-1:if(mes===1){if(ano===this._yearMin){return;}
mes=12;ano--;}
else{mes--;}
this._year=ano;this._month=mes-1;break;case 1:if(mes===12){if(ano===this._yearMax){return;}
mes=1;ano++;}
else{mes++;}
this._year=ano;this._month=mes-1;break;default:}},_dateParsers:{'yyyy-mm-dd':'Y-m-d','yyyy/mm/dd':'Y/m/d','yy-mm-dd':'y-m-d','yy/mm/dd':'y/m/d','dd-mm-yyyy':'d-m-Y','dd/mm/yyyy':'d/m/Y','dd-mm-yy':'d-m-y','dd/mm/yy':'d/m/y','mm/dd/yyyy':'m/d/Y','mm-dd-yyyy':'m-d-Y'},_showMonth:function(){var i,j;var mes=this._month+1;var ano=this._year;var maxDay=this._daysInMonth(ano,mes);var wDayFirst=(new Date(ano,mes-1,1)).getDay();var startWeekDay=this._options.startWeekDay||0;this._monthPrev.childNodes[0].className='change_month_prev';this._monthNext.childNodes[0].className='change_month_next';if(ano<this._yearMin||ano==this._yearMin&&mes<=this._monthMin){this._monthPrev.childNodes[0].className='action_inactive';}
else if(ano>this._yearMax||ano==this._yearMax&&mes>=this._monthMax){this._monthNext.childNodes[0].className='action_inactive';}
if(startWeekDay&&Number(startWeekDay)){if(startWeekDay>wDayFirst){wDayFirst=7+startWeekDay-wDayFirst;}else{wDayFirst+=startWeekDay;}}
var html='';html+='<ul class="sapo_cal_header">';for(i=0;i<7;i++){html+='<li>'+this._options.wDay[i+(((startWeekDay+i)>6)?startWeekDay-7:startWeekDay)].substring(0,1)+'</li>';}
html+='</ul>';var counter=0;html+='<ul>';if(wDayFirst){for(j=startWeekDay;j<wDayFirst-startWeekDay;j++){if(!counter){html+='<ul>';}
html+='<li class="sapo_cal_empty">&nbsp;</li>';counter++;}}
for(i=1;i<=maxDay;i++){if(counter===7){counter=0;html+='<ul>';}
var idx='sapo_cal_'+((String(i).length===2)?i:"0"+i);idx+=(ano==this._yearMin&&mes==this._monthMin&&i<this._dayMin||ano==this._yearMax&&mes==this._monthMax&&i>this._dayMax||ano==this._yearMin&&mes<this._monthMin||ano==this._yearMax&&mes>this._monthMax||ano<this._yearMin||ano>this._yearMax||(this._options.validDayFn&&!this._options.validDayFn.call(this,new Date(ano,mes-1,i))))?" sapo_cal_off":(this._data.getFullYear()==ano&&this._data.getMonth()==mes-1&&i==this._day)?" sapo_cal_on":"";html+='<li><a href="#" class="'+idx+'">'+i+'</a></li>';counter++;if(counter===7){html+='</ul>';}}
if(counter!==7){for(i=counter;i<7;i++){html+='<li class="sapo_cal_empty">&nbsp;</li>';}
html+='</ul>';}
html+='</ul>';this._monthContainer.innerHTML=html;},_setActiveMonth:function(parent){if(typeof parent==='undefined'){parent=this._monthSelector;}
var length=parent.childNodes.length;if(parent.className&&parent.className.match(/sapo_calmonth_/)){var year=this._year;var month=parent.className.substr(14,2);if(year==this._data.getFullYear()&&month==this._data.getMonth()+1)
{SAPO.Dom.Css.addClassName(parent,'sapo_cal_on');SAPO.Dom.Css.removeClassName(parent,'sapo_cal_off');}
else
{SAPO.Dom.Css.removeClassName(parent,'sapo_cal_on');if(year==this._yearMin&&month<this._monthMin||year==this._yearMax&&month>this._monthMax||year<this._yearMin||year>this._yearMax)
{SAPO.Dom.Css.addClassName(parent,'sapo_cal_off');}
else
{SAPO.Dom.Css.removeClassName(parent,'sapo_cal_off');}}}
else if(length!==0){for(var i=0;i<length;i++){this._setActiveMonth(parent.childNodes[i]);}}}};})();
(function(undefined){'use strict';SAPO.namespace('Ink');SAPO.Ink.FormValidator={version:'0.1',_flagMap:{'ink-fv-required':{msg:'Required field'},'ink-fv-email':{msg:'Invalid e-mail address'},'ink-fv-url':{msg:'Invalid URL'},'ink-fv-number':{msg:'Invalid number'},'ink-fv-phone_pt':{msg:'Invalid phone number'},'ink-fv-phone_cv':{msg:'Invalid phone number'},'ink-fv-phone_mz':{msg:'Invalid phone number'},'ink-fv-phone_ao':{msg:'Invalid phone number'},'ink-fv-date':{msg:'Invalid date'},'ink-fv-confirm':{msg:'Confirmation does not match'},'ink-fv-custom':{msg:''}},elements:{},confirmElms:{},hasConfirm:{},_errorClassName:'tip',_errorValidationClassName:'validaton',_errorTypeWarningClassName:'warning',_errorTypeErrorClassName:'error',validate:function(elm,options)
{this._free();options=SAPO.extendObj({onSuccess:false,onError:false,customFlag:false,confirmGroup:[]},options||{});if(typeof(elm)==='string'){elm=document.getElementById(elm);}
if(elm===null){return false;}
this.element=elm;if(typeof(this.element.id)==='undefined'||this.element.id===null||this.element.id===''){this.element.id='ink-fv_randomid_'+(Math.round(Math.random()*99999));}
this.custom=options.customFlag;this.confirmGroup=options.confirmGroup;var fail=this._validateElements();if(fail.length>0){if(options.onError){options.onError(fail);}else{this._showError(elm,fail);}
return false;}else{if(!options.onError){this._clearError(elm);}
this._clearCache();if(options.onSuccess){options.onSuccess();}
return true;}},reset:function()
{this._clearError();this._clearCache();},_free:function()
{this.element=null;this.custom=false;this.confirmGroup=false;},_clearCache:function()
{this.element=null;this.elements=[];this.custom=false;this.confirmGroup=false;},_getElements:function()
{if(typeof(this.elements[this.element.id])!=='undefined'){return;}
this.elements[this.element.id]=[];this.confirmElms[this.element.id]=[];var formElms=this.element.elements;var curElm=false;for(var i=0,totalElm=formElms.length;i<totalElm;i++){curElm=formElms[i];if(curElm.getAttribute('type')!==null&&curElm.getAttribute('type').toLowerCase()==='radio'){if(this.elements[this.element.id].length===0||(curElm.getAttribute('type')!==this.elements[this.element.id][(this.elements[this.element.id].length-1)].getAttribute('type')&&curElm.getAttribute('name')!==this.elements[this.element.id][(this.elements[this.element.id].length-1)].getAttribute('name'))){for(var flag in this._flagMap){if(SAPO.Dom.Css.hasClassName(curElm,flag)){this.elements[this.element.id].push(curElm);break;}}}}else{for(var flag2 in this._flagMap){if(SAPO.Dom.Css.hasClassName(curElm,flag2)&&flag2!=='ink-fv-confirm'){this.elements[this.element.id].push(curElm);break;}}
if(SAPO.Dom.Css.hasClassName(curElm,'ink-fv-confirm')){this.confirmElms[this.element.id].push(curElm);this.hasConfirm[this.element.id]=true;}}}},_validateElements:function()
{var oGroups;this._getElements();if(typeof(this.hasConfirm[this.element.id])!=='undefined'&&this.hasConfirm[this.element.id]===true){oGroups=this._makeConfirmGroups();}
var errors=[];var curElm=false;var customErrors=false;var inArray;for(var i=0,totalElm=this.elements[this.element.id].length;i<totalElm;i++){inArray=false;curElm=this.elements[this.element.id][i];if(!curElm.disabled){for(var flag in this._flagMap){if(SAPO.Dom.Css.hasClassName(curElm,flag)){if(flag!=='ink-fv-custom'&&flag!=='ink-fv-confirm'){if(!this._isValid(curElm,flag)){if(!inArray){errors.push({elm:curElm,errors:[flag]});inArray=true;}else{errors[(errors.length-1)].errors.push(flag);}}}else if(flag!=='ink-fv-confirm'){customErrors=this._isCustomValid(curElm);if(customErrors.length>0){errors.push({elm:curElm,errors:[flag],custom:customErrors});}}else if(flag==='ink-fv-confirm'){}}}}}
errors=this._validateConfirmGroups(oGroups,errors);return errors;},_validateConfirmGroups:function(oGroups,errors)
{var curGroup=false;for(var i in oGroups){curGroup=oGroups[i];if(curGroup.length===2){if(curGroup[0].value!==curGroup[1].value){errors.push({elm:curGroup[1],errors:['ink-fv-confirm']});}}}
return errors;},_makeConfirmGroups:function()
{var oGroups;if(this.confirmGroup&&this.confirmGroup.length>0){oGroups={};var curElm=false;var curGroup=false;for(var i=0,total=this.confirmElms[this.element.id].length;i<total;i++){curElm=this.confirmElms[this.element.id][i];for(var j=0,totalG=this.confirmGroup.length;j<totalG;j++){curGroup=this.confirmGroup[j];if(SAPO.Dom.Css.hasClassName(curElm,curGroup)){if(typeof(oGroups[curGroup])==='undefined'){oGroups[curGroup]=[curElm];}else{oGroups[curGroup].push(curElm);}}}}
return oGroups;}else{if(this.confirmElms[this.element.id].length===2){oGroups={"ink-fv-confirm":[this.confirmElms[this.element.id][0],this.confirmElms[this.element.id][1]]};}
return oGroups;}
return false;},_isCustomValid:function(elm)
{var customErrors=[];var curFlag=false;for(var i=0,tCustom=this.custom.length;i<tCustom;i++){curFlag=this.custom[i];if(SAPO.Dom.Css.hasClassName(elm,curFlag.flag)){if(!curFlag.callback(elm,curFlag.msg)){customErrors.push({flag:curFlag.flag,msg:curFlag.msg});}}}
return customErrors;},_isValid:function(elm,fieldType)
{switch(fieldType){case'ink-fv-required':if(elm.nodeName.toLowerCase()==='select'){if(elm.selectedIndex>0){return true;}else{return false;}}
if(elm.getAttribute('type')!=='checkbox'&&elm.getAttribute('type')!=='radio'){if(this._trim(elm.value)!==''){return true;}}else if(elm.getAttribute('type')==='checkbox'){if(elm.checked===true){return true;}}else if(elm.getAttribute('type')==='radio'){var aFormRadios=elm.form[elm.name];if(typeof(aFormRadios.length)==='undefined'){aFormRadios=[aFormRadios];}
var isChecked=false;for(var i=0,totalRadio=aFormRadios.length;i<totalRadio;i++){if(aFormRadios[i].checked===true){isChecked=true;}}
return isChecked;}
break;case'ink-fv-email':if(this._trim(elm.value)===''){if(SAPO.Dom.Css.hasClassName(elm,'ink-fv-required')){return false;}else{return true;}}else{if(SAPO.Utility.Validator.mail(elm.value)){return true;}}
break;case'ink-fv-url':if(this._trim(elm.value)===''){if(SAPO.Dom.Css.hasClassName(elm,'ink-fv-required')){return false;}else{return true;}}else{if(SAPO.Utility.Validator.url(elm.value)){return true;}}
break;case'ink-fv-number':if(this._trim(elm.value)===''){if(SAPO.Dom.Css.hasClassName(elm,'ink-fv-required')){return false;}else{return true;}}else{if(!isNaN(Number(elm.value))){return true;}}
break;case'ink-fv-phone_pt':if(this._trim(elm.value)===''){if(SAPO.Dom.Css.hasClassName(elm,'ink-fv-required')){return false;}else{return true;}}else{if(SAPO.Utility.Validator.isPTPhone(elm.value)){return true;}}
break;case'ink-fv-phone_cv':if(this._trim(elm.value)===''){if(SAPO.Dom.Css.hasClassName(elm,'ink-fv-required')){return false;}else{return true;}}else{if(SAPO.Utility.Validator.isCVPhone(elm.value)){return true;}}
break;case'ink-fv-phone_ao':if(this._trim(elm.value)===''){if(SAPO.Dom.Css.hasClassName(elm,'ink-fv-required')){return false;}else{return true;}}else{if(SAPO.Utility.Validator.isAOPhone(elm.value)){return true;}}
break;case'ink-fv-phone_mz':if(this._trim(elm.value)===''){if(SAPO.Dom.Css.hasClassName(elm,'ink-fv-required')){return false;}else{return true;}}else{if(SAPO.Utility.Validator.isMZPhone(elm.value)){return true;}}
break;case'ink-fv-custom':break;}
return false;},_showError:function(formElm,aFail)
{this._clearError(formElm);var curElm=false;for(var i=0,tFail=aFail.length;i<tFail;i++){curElm=aFail[i].elm;if(curElm.getAttribute('type')!=='radio'){var newLabel=document.createElement('p');newLabel.className=this._errorClassName;if(aFail[i].errors[0]!=='ink-fv-custom'){newLabel.innerHTML=this._flagMap[aFail[i].errors[0]].msg;}else{newLabel.innerHTML=aFail[i].custom[0].msg;}
if(curElm.getAttribute('type')!=='checkbox'){curElm.nextSibling.parentNode.insertBefore(newLabel,curElm.nextSibling);if(SAPO.Dom.Css.hasClassName(curElm.parentNode,'control')){SAPO.Dom.Css.addClassName(curElm.parentNode,'validation');if(aFail[i].errors[0]==='ink-fv-required'){SAPO.Dom.Css.addClassName(curElm.parentNode,'error');}else{SAPO.Dom.Css.addClassName(curElm.parentNode,'warning');}}}else{}}else{if(SAPO.Dom.Css.hasClassName(curElm.parentNode.parentNode,'control-group')){SAPO.Dom.Css.addClassName(curElm.parentNode.parentNode,'validation');SAPO.Dom.Css.addClassName(curElm.parentNode.parentNode,'error');}}}},_clearError:function(formElm)
{var aErrorLabel=formElm.getElementsByTagName('p');var curElm=false;for(var i=(aErrorLabel.length-1);i>=0;i--){curElm=aErrorLabel[i];if(SAPO.Dom.Css.hasClassName(curElm,this._errorClassName)){if(SAPO.Dom.Css.hasClassName(curElm.parentNode,'control')){SAPO.Dom.Css.removeClassName(curElm.parentNode,'validation');SAPO.Dom.Css.removeClassName(curElm.parentNode,'error');SAPO.Dom.Css.removeClassName(curElm.parentNode,'warning');}
curElm.parentNode.removeChild(curElm);}}
var aErrorLabel2=formElm.getElementsByTagName('ul');for(i=(aErrorLabel2.length-1);i>=0;i--){curElm=aErrorLabel2[i];if(SAPO.Dom.Css.hasClassName(curElm,'control-group')){SAPO.Dom.Css.removeClassName(curElm,'validation');SAPO.Dom.Css.removeClassName(curElm,'error');}}},_trim:function(str)
{if(typeof(str)==='string')
{return str.replace(/^\s+|\s+$|\n+$/g,'');}}};})();
(function(undefined){'use strict';SAPO.namespace('Ink');var Aux=SAPO.Ink.Aux,Css=SAPO.Dom.Css,Event=SAPO.Dom.Event,Selector=SAPO.Dom.Selector;var maximizeBox=function(maxSz,imageSz,forceMaximize){var w=imageSz[0];var h=imageSz[1];if(forceMaximize||(w>maxSz[0]||h>maxSz[1])){var arImg=w/h;var arMax=maxSz[0]/maxSz[1];var s=(arImg>arMax)?maxSz[0]/w:maxSz[1]/h;return[parseInt(w*s+0.5,10),parseInt(h*s+0.5,10)];}
return imageSz;};var getDimsAsync=function(o,cb){cb=cb.bindObj(o);var dims=[o.img.offsetWidth,o.img.offsetHeight];if(dims[0]&&dims[1]){cb(dims);}
o.img.onload=function(){cb([this.img.offsetWidth,this.img.offsetHeight]);}.bindObjEvent(o);};var Gallery=function(selector,options){this._options=SAPO.extendObj({fullImageMaxWidth:600,fullImageMaxHeight:400,thumbnailMaxWidth:106,layout:0,circular:false,fixImageSizes:false},options||{});this._handlers={navClick:this._onNavClick.bindObjEvent(this),paginationClick:this._onPaginationClick.bindObjEvent(this),thumbsClick:this._onThumbsClick.bindObjEvent(this),focusBlur:this._onFocusBlur.bindObjEvent(this),keyDown:this._onKeyDown.bindObjEvent(this)};this._element=Aux.elOrSelector(selector,'1st argument');this._isFocused=false;this._model=[];if(this._options.model instanceof Array){this._model=this._options.model;this._createdFrom='JSON';}
else if(this._element.nodeName.toLowerCase()==='ul'){this._createdFrom='DOM';}
else{throw new TypeError('You must pass a selector expression/DOM element as 1st option or provide a model on 2nd argument!');}
this._index=0;this._thumbIndex=0;if(this._options.layout===0){this._showThumbs=false;this._showDescription=false;this._paginationHasPrevNext=false;}
else if(this._options.layout===1||this._options.layout===2||this._options.layout===3){this._showThumbs=true;this._showDescription=true;this._paginationHasPrevNext=true;}
else{throw new TypeError('supported layouts are 0-3!');}
if(this._element.getAttribute('data-fix-image-sizes')!==null){this._options.fixImageSizes=true;}
this._init();};Gallery.prototype={_init:function(){if(this._createdFrom==='DOM'){this._extractModelFromDOM();}
var el=this._generateMarkup();var parentEl=this._element.parentNode;if(!this._notFirstInit){Aux.storeIdAndClasses(this._element,this);this._notFirstInit=true;}
parentEl.insertBefore(el,this._element);parentEl.removeChild(this._element);this._element=el;Aux.restoreIdAndClasses(this._element,this);Event.observe(this._paginationEl,'click',this._handlers.paginationClick);Event.observe(this._navEl,'click',this._handlers.navClick);if(this._showThumbs){Event.observe(this._thumbsUlEl,'click',this._handlers.thumbsClick);}
Event.observe(this._element,'mouseover',this._handlers.focusBlur);Event.observe(this._element,'mouseout',this._handlers.focusBlur);Event.observe(document,'keydown',this._handlers.keyDown);Aux.registerInstance(this,this._element,'gallery');},_extractModelFromDOM:function(){var m=[];var dims;var liEls=Selector.select('> li',this._element);liEls.forEach(function(liEl){try{var d={image_full:'',image_thumb:'',title_text:'',title_link:'',description:'',content_overlay:document.createDocumentFragment()};var enclosureAEl=Selector.select('> a[rel=enclosure]',liEl)[0];var thumbImgEl=Selector.select('> img',enclosureAEl)[0];var bookmarkAEl=Selector.select('> a[class=bookmark]',liEl)[0];var titleSpanEl=Selector.select('span[class=entry-title]',liEl)[0];var entryContentSpanEl=Selector.select('> span[class=entry-content]',liEl)[0];var contentOverlayEl=Selector.select('> .content-overlay',liEl)[0];dims=enclosureAEl.getAttribute('data-dims');if(dims!==null){dims=dims.split(',');dims[0]=parseInt(dims[0],10);dims[1]=parseInt(dims[1],10);}
if(dims&&!isNaN(dims[0])&&!isNaN(dims[1])){d.dims=dims;}
d.image_full=enclosureAEl.getAttribute('href');d.image_thumb=thumbImgEl.getAttribute('src');if(bookmarkAEl){d.title_link=bookmarkAEl.getAttribute('href');}
d.title_text=titleSpanEl.innerHTML;if(entryContentSpanEl){d.description=entryContentSpanEl.innerHTML;}
if(contentOverlayEl){d.content_overlay.appendChild(contentOverlayEl);}
m.push(d);}catch(ex){console.error('problematic element:');console.error(liEl);console.error(ex);throw new Error('Problem parsing gallery data from DOM!');}});this._model=m;},_generateMarkup:function(){var el=document.createElement('div');el.className='ink-gallery';var stageEl=document.createElement('div');stageEl.className='stage';var navEl=document.createElement('nav');navEl.innerHTML=['<ul class="unstyled">','<li><a href="#" class="next"></a></li>','<li><a href="#" class="previous"></a></li>','</ul>'].join('');this._navEl=navEl;var sliderEl=document.createElement('div');sliderEl.className='slider';var ulEl=document.createElement('ul');this._sliderUlEl=ulEl;var that=this;var W=this._options.fullImageMaxWidth;var H=this._options.fullImageMaxHeight;this._model.forEach(function(d,i){var liEl=document.createElement('li');var imgEl=document.createElement('img');imgEl.setAttribute('name','image '+(i+1));imgEl.setAttribute('src',d.image_full);imgEl.setAttribute('alt',d.title_text);liEl.appendChild(imgEl);if(d.content_overlay){if(d.content_overlay.nodeType===1||d.content_overlay.nodeType===11){d.content_overlay=liEl.appendChild(d.content_overlay);}else if(typeof d.content_overlay==='string'){var contentOverlayEl=document.createElement('div');contentOverlayEl.className='content-overlay';contentOverlayEl.innerHTML=d.content_overlay;d.content_overlay=liEl.appendChild(contentOverlayEl);}}
ulEl.appendChild(liEl);if(that._options.fixImageSizes){var dimsCb=function(dims){var imgEl=this.img;var data=this.data;if(!data.dims){data.dims=dims;}
var dims2=maximizeBox([W,H],dims);var w=dims2[0];var h=dims2[1];var dw=Math.floor((W-w)/2);var dh=Math.floor((H-h)/2);if(w!==W||h!==H){imgEl.setAttribute('width',w);imgEl.setAttribute('height',h);var s=imgEl.style;if(dw>0){s.paddingLeft=dw+'px';}
if(dh>0){s.paddingBottom=dh+'px';}}};if(d.dims){dimsCb.call({img:imgEl,data:d},d.dims);}
else{getDimsAsync({img:imgEl,data:d},dimsCb);}}});sliderEl.appendChild(ulEl);this._sliderEl=sliderEl;var articleTextDivEl;if(this._showDescription){var d=this._model[this._index];articleTextDivEl=document.createElement('div');articleTextDivEl.className=['article_text','example'+(this._options.layout===3?2:this._options.layout)].join(' ');if(d.title_link){articleTextDivEl.innerHTML=['<h1><a href="',d.title_link,'">',d.title_text,'</a></h1>',d.description].join('');}
else{articleTextDivEl.innerHTML=['<h1>',d.title_text,'</h1>',d.description].join('');}
this._articleTextDivEl=articleTextDivEl;}
var thumbsDivEl;if(this._showThumbs){thumbsDivEl=document.createElement('div');thumbsDivEl.className='thumbs';ulEl=document.createElement('ul');ulEl.className='unstyled';this._model.forEach(function(d,i){var liEl=document.createElement('li');var aEl=document.createElement('a');aEl.setAttribute('href','#');var imgEl=document.createElement('img');imgEl.setAttribute('name','thumb '+(i+1));imgEl.setAttribute('src',d.image_thumb);imgEl.setAttribute('alt',(i+1));var spanEl=document.createElement('span');spanEl.innerHTML=d.title_text;aEl.appendChild(imgEl);aEl.appendChild(spanEl);liEl.appendChild(aEl);ulEl.appendChild(liEl);});thumbsDivEl.appendChild(ulEl);this._thumbsDivEl=thumbsDivEl;this._thumbsUlEl=ulEl;}
var paginationEl=document.createElement('div');paginationEl.className='pagination';var aEl;if(this._paginationHasPrevNext){aEl=document.createElement('a');aEl.setAttribute('href','#');aEl.className='previous';paginationEl.appendChild(aEl);}
this._model.forEach(function(d,i){var aEl=document.createElement('a');aEl.setAttribute('href','#');aEl.setAttribute('data-index',i);if(i===that._index){aEl.className='active';}
paginationEl.appendChild(aEl);});if(this._paginationHasPrevNext){aEl=document.createElement('a');aEl.setAttribute('href','#');aEl.className='next';paginationEl.appendChild(aEl);}
this._paginationEl=paginationEl;if(this._options.layout===0){stageEl.appendChild(navEl);stageEl.appendChild(sliderEl);stageEl.appendChild(paginationEl);el.appendChild(stageEl);}
else if(this._options.layout===1||this._options.layout===2||this._options.layout===3){stageEl.appendChild(navEl);stageEl.appendChild(sliderEl);stageEl.appendChild(articleTextDivEl);el.appendChild(stageEl);if(this._options.layout===3){this._thumbsUlEl.className='thumbs unstyled';Css.addClassName(el,'rightNav');el.appendChild(this._thumbsUlEl);}
else{thumbsDivEl.appendChild(paginationEl);el.appendChild(thumbsDivEl);}}
this._swipeDir='x';this._swipeThumbsDir=this._options.layout===0?'':(this._options.layout===3?'y':'x');if(SAPO.Utility.Swipe._supported){new SAPO.Utility.Swipe(el,{callback:function(sw,o){var th=this._isMeOrParent(o.target,this._thumbsUlEl);var sl=th?false:this._isMeOrParent(o.target,el);if((!th&&!sl)||(th&&!this._swipeThumbsDir)){return;}
if((sl&&o.axis!==this._swipeDir)||(th&&o.axis!==this._swipeThumbsDir)){return;}
if(o.dr[0]<0){if(th){this.thumbNext();}else{this.next();}}
else{if(th){this.thumbPrevious();}else{this.previous();}}}.bindObj(this),maxDuration:0.4,minDist:50});}
return el;},_isMeOrParent:function(el,parentEl){if(!el){return;}
do{if(el===parentEl){return true;}
el=el.parentNode;}while(el);return false;},_onNavClick:function(ev){var tgtEl=Event.element(ev);var delta;if(Css.hasClassName(tgtEl,'previous')){delta=-1;}
else if(Css.hasClassName(tgtEl,'next')){delta=1;}
else{return;}
Event.stop(ev);this.goTo(delta,true);},_onPaginationClick:function(ev){var tgtEl=Event.element(ev);var i=tgtEl.getAttribute('data-index');var isRelative=false;if(Css.hasClassName(tgtEl,'previous')){i=-1;isRelative=true;}
else if(Css.hasClassName(tgtEl,'next')){i=1;isRelative=true;}
else if(i===null){return;}
else{i=parseInt(i,10);}
Event.stop(ev);if(isRelative){this.thumbGoTo(i,true);}
else{this.goTo(i);}},_onThumbsClick:function(ev){var tgtEl=Event.element(ev);if(tgtEl.nodeName.toLowerCase()==='img'){}
else if(tgtEl.nodeName.toLowerCase()==='span'){tgtEl=Selector.select('> img',tgtEl.parentNode)[0];}
else{return;}
Event.stop(ev);var i=parseInt(tgtEl.getAttribute('alt'),10)-1;this.goTo(i);},_onFocusBlur:function(ev){this._isFocused=(ev.type==='mouseover');},_onKeyDown:function(ev){if(!this._isFocused){return;}
var kc=ev.keyCode;if(kc===37){this.previous();}
else if(kc===39){this.next();}
else{return;}
Event.stop(ev);},_validateValue:function(i,isRelative,isThumb){if(!Aux.isInteger(i)){throw new TypeError('1st parameter must be an integer number!');}
if(isRelative!==undefined&&isRelative!==false&&isRelative!==true){throw new TypeError('2nd parameter must either be boolean or ommitted!');}
var val=isThumb?this._thumbIndex:this._index;if(isRelative){i+=val;}
if(this._options.circular){if(i<0){i=this._model.length-1;}
else if(i>=this._model.length){i=0;}}
else{if(i<0||i>=this._model.length||i===val){return false;}}
return i;},getIndex:function(){return this._index;},getLength:function(){return this._model.length;},goTo:function(i,isRelative){i=this._validateValue(i,isRelative,false);if(i===false){return;}
this._index=i;var paginationAEls=Selector.select('> a',this._paginationEl);var that=this;paginationAEls.forEach(function(aEl,i){Css.setClassName(aEl,'active',(i-(that._paginationHasPrevNext?1:0))===that._index);});this._sliderUlEl.style.marginLeft=['-',this._options.fullImageMaxWidth*this._index,'px'].join('');if(this._showDescription){var d=this._model[this._index];if(d.title_link){this._articleTextDivEl.innerHTML=['<h1><a href="',d.title_link,'">',d.title_text,'</a></h1>',d.description].join('');}
else{this._articleTextDivEl.innerHTML=['<h1>',d.title_text,'</h1>',d.description].join('');}}},thumbGoTo:function(i,isRelative){i=this._validateValue(i,isRelative,true);if(i===false){return;}
this._thumbIndex=i;var prop='margin'+(this._swipeThumbsDir==='x'?'Left':'Top');this._thumbsUlEl.style[prop]=['-',this._options.thumbnailMaxWidth*this._thumbIndex,'px'].join('');},previous:function(){this.goTo(-1,true);},next:function(){this.goTo(1,true);},thumbPrevious:function(){this.thumbGoTo(-1,true);},thumbNext:function(){this.thumbGoTo(1,true);},destroy:Aux.destroyComponent};SAPO.Ink.Gallery=Gallery;})();
(function(undefined){'use strict';SAPO.namespace('Ink');var Aux=SAPO.Ink.Aux,Css=SAPO.Dom.Css,Element=SAPO.Dom.Element,Event=SAPO.Dom.Event;var Modal=function(selector,options){if((typeof selector!=='string')&&(typeof selector!=='object')){throw'Invalid Modal selector';}else if(typeof selector==='string'){if(selector!==''){this._element=SAPO.Dom.Selector.select(selector);if(this._element.length===0){throw'The Modal selector has not returned any elements';}else{this._element=this._element[0];}}}else{this._element=selector;}
this._options=SAPO.extendObj({width:undefined,height:undefined,markup:((this._element)?this._element.innerHTML:undefined),onShow:undefined,onDismiss:undefined,closeOnClick:false,skipDismiss:false,resizable:true,disableScroll:false},options||{});this._handlers={click:this._onClick.bindObjEvent(this),keyDown:this._onKeyDown.bindObjEvent(this),resize:this._onResize.bindObjEvent(this)};this._wasDismissed=false;this._init();};Modal.prototype={_init:function(){this._modalShadow=document.createElement('div');this._modalDiv=document.createElement('div');this._modalShadowStyle=this._modalShadow.style;this._modalDivStyle=this._modalDiv.style;this._resizeTimeout=null;SAPO.Dom.Css.addClassName(this._modalShadow,'ink-shade');SAPO.Dom.Css.addClassName(this._modalDiv,'ink-modal');SAPO.Dom.Css.addClassName(this._modalDiv,'ink-space');this._modalShadowStyle.position='fixed';this._modalShadowStyle.top=this._modalShadowStyle.bottom=this._modalShadowStyle.left=this._modalShadowStyle.right='0px';this._modalDivStyle.position='absolute';var elem=(document.compatMode==="CSS1Compat")?document.documentElement:document.body;if(typeof this._options.addClass!=='undefined'){SAPO.Dom.Css.addClassName(this._modalDiv,this._options.addClass);}
this._contentContainer=document.createElement('div');this._contentContainer.className='ink-modal-content';this._contentContainer.innerHTML=[(this._options.skipClose?'':'<a href="#" class="ink-close">×</a>'),this._options.markup].join('');this._modalDiv.appendChild(this._contentContainer);this._modalShadow.appendChild(this._modalDiv);document.body.appendChild(this._modalShadow);if(typeof this._options.width!=='undefined'){this._modalDivStyle.maxWidth=this._modalDivStyle.width=parseInt(this._options.width,10)+'px';}else{this._modalDivStyle.maxWidth=this._modalDivStyle.width=SAPO.Dom.Element.elementWidth(this._modalDiv)+'px';}
if(parseInt(elem.clientWidth,10)<=parseInt(this._modalDivStyle.width,10)){this._modalDivStyle.width=(parseInt(elem.clientWidth,10)*0.9)+'px';}
if(typeof this._options.height!=='undefined'){this._modalDivStyle.maxHeight=this._modalDivStyle.height=parseInt(this._options.height,10)+'px';}else{this._modalDivStyle.maxHeight=this._modalDivStyle.height=SAPO.Dom.Element.elementHeight(this._modalDiv)+'px';}
if(parseInt(elem.clientHeight,10)<=parseInt(this._modalDivStyle.height,10)){this._modalDivStyle.height=(parseInt(elem.clientHeight,10)*0.9)+'px';}
this.originalStatus={viewportHeight:parseInt(elem.clientHeight,10),viewportWidth:parseInt(elem.clientWidth,10),width:parseInt(this._modalDivStyle.width,10),height:parseInt(this._modalDivStyle.height,10)};if(this._options.resizable){this._onResize.apply(this,[]);Event.observe(window,'resize',this._handlers.resize);}
this._contentElement=this._modalDiv;this._shadeElement=this._modalShadow;this._reposition.apply(this,[]);this.setContentMarkup.apply(this,[this._options.markup]);if(this._options.onShow){this._options.onShow(this);}
if(this._options.disableScroll){this._disableScroll();}
Event.observe(this._shadeElement,'click',this._handlers.click);Event.observe(document,'keydown',this._handlers.keyDown);Aux.registerInstance(this,this._shadeElement,'modal');},_reposition:function(){this._modalDivStyle.top=this._modalDivStyle.left='50%';this._modalDivStyle.marginTop='-'+(~~(SAPO.Dom.Element.elementHeight(this._modalDiv)/2))+'px';this._modalDivStyle.marginLeft='-'+(~~(SAPO.Dom.Element.elementWidth(this._modalDiv)/2))+'px';},_onResize:function(){if(!this._resizeTimeout){this._resizeTimeout=setTimeout(function(){var
elem=(document.compatMode==="CSS1Compat")?document.documentElement:document.body,currentViewportHeight=parseInt(elem.clientHeight,10),currentViewportWidth=parseInt(elem.clientWidth,10);if(currentViewportWidth>this.originalStatus.viewportWidth){this._modalDivStyle.width=((currentViewportWidth*this.originalStatus.width)/this.originalStatus.viewportWidth)+'px';}else{this._modalDivStyle.width=(currentViewportWidth*0.9)+'px';}
if(currentViewportHeight>this.originalStatus.viewportHeight){this._modalDivStyle.height=((currentViewportHeight*this.originalStatus.height)/this.originalStatus.viewportHeight)+'px';}else{this._modalDivStyle.height=(currentViewportHeight*0.9)+'px';}
this._resizeContainer.apply(this,[]);this._reposition();this._resizeTimeout=null;}.bindObj(this),500);}},_onClick:function(ev){var tgtEl=Event.element(ev);if(Css.hasClassName(tgtEl,'ink-close')||(this._options.closeOnClick&&!Element.descendantOf(this._shadeElement,tgtEl))||(tgtEl===this._shadeElement)){Event.stop(ev);this.dismiss();}},_onKeyDown:function(ev){if(ev.keyCode!==27||this._wasDismissed){return;}
this.dismiss();},_resizeContainer:function()
{this._contentContainer.style.overflow=this._contentContainer.style.overflowX=this._contentContainer.style.overflowY='visible';this._contentContainer.style.height='auto';var contentHeight=SAPO.Dom.Element.elementHeight(this._contentContainer);this._contentElement.style.overflow=this._contentElement.style.overflowX=this._contentElement.style.overflowY='visible';if(contentHeight>SAPO.Dom.Element.elementHeight(this._contentElement)){this._contentElement.style.overflow=this._contentElement.style.overflowX=this._contentElement.style.overflowY='hidden';this._contentContainer.style.height=SAPO.Dom.Element.elementHeight(this._contentElement)+'px';this._contentElement.style.overflow=this._contentElement.style.overflowX=this._contentElement.style.overflowY='visible';this._contentContainer.style.overflow=this._contentContainer.style.overflowY='auto';this._contentContainer.style.overflowX='hidden';if(!this._options.skipClose){var aClose=SAPO.Dom.Selector.select('.ink-close',this._contentElement);aClose[0].style.top='-12px';aClose[0].style.right='-14px';}}},_disableScroll:function()
{this._oldScrollPos=SAPO.Dom.Element.scroll();this._onScrollBinded=function(event){var tgtEl=SAPO.Dom.Event.element(event);if(!Element.descendantOf(this._modalShadow,tgtEl)){SAPO.Dom.Event.stop(event);window.scrollTo(this._oldScrollPos[0],this._oldScrollPos[1]);}}.bindObjEvent(this);SAPO.Dom.Event.observe(window,'scroll',this._onScrollBinded);SAPO.Dom.Event.observe(this._modalShadow,'touchmove',this._onScrollBinded);},dismiss:function(){if(this._options.onDismiss){this._options.onDismiss(this);}
if(this._options.disableScroll){SAPO.Dom.Event.stopObserving(window,'scroll',this._onScrollBinded);}
if(this._options.resizable){SAPO.Dom.Event.stopObserving(window,'resize',this._handlers.resize);}
var el=this._shadeElement;this._wasDismissed=true;el.parentNode.removeChild(el);Aux.unregisterInstance(this._instanceId);},getContentElement:function(){return this._contentContainer;},setContentMarkup:function(contentMarkup){this._modalDiv.innerHTML='';this._modalDiv.appendChild(this._contentContainer);this._contentContainer.innerHTML=[this._options.skipClose?'':'<a href="#" class="ink-close">×</a>',contentMarkup].join('');this._resizeContainer.apply(this,[]);}};Modal.destroy=Modal.dismiss;SAPO.Ink.Modal=Modal;})();
(function(window,undefined){'use strict';SAPO.namespace('Ink');var Aux=SAPO.Ink.Aux,Css=SAPO.Dom.Css,Element=SAPO.Dom.Element,Event=SAPO.Dom.Event,Selector=SAPO.Dom.Selector;var SortableList=function(selector,options){this._options=SAPO.extendObj({dragLabel:'drag here'},options||{});this._handlers={down:this._onDown.bindObjEvent(this),move:this._onMove.bindObjEvent(this),up:this._onUp.bindObjEvent(this)};this._element=Aux.elOrSelector(selector,'1st argument');this._model=[];this._index=undefined;this._isMoving=false;if(this._options.model instanceof Array){this._model=this._options.model;this._createdFrom='JSON';}
else if(this._element.nodeName.toLowerCase()==='ul'){this._createdFrom='DOM';}
else{throw new TypeError('You must pass a selector expression/DOM element as 1st option or provide a model on 2nd argument!');}
this._init();};SortableList.prototype={_init:function(){if(this._createdFrom==='DOM'){this._extractModelFromDOM();this._createdFrom='JSON';}
var el=this._generateMarkup();var parentEl=this._element.parentNode;if(!this._notFirstInit){Aux.storeIdAndClasses(this._element,this);this._notFirstInit=true;}
parentEl.insertBefore(el,this._element);parentEl.removeChild(this._element);this._element=el;Aux.restoreIdAndClasses(this._element,this);var isTouch='ontouchstart'in document.documentElement;this._down=isTouch?'touchstart':'mousedown';this._move=isTouch?'touchmove':'mousemove';this._up=isTouch?'touchend':'mouseup';var db=document.body;Event.observe(db,this._move,this._handlers.move);Event.observe(db,this._up,this._handlers.up);this._observe();Aux.registerInstance(this,this._element,'sortableList');},_observe:function(){Event.observe(this._element,this._down,this._handlers.down);},_extractModelFromDOM:function(){this._model=[];var that=this;var liEls=Selector.select('> li',this._element);liEls.forEach(function(liEl){var t=liEl.innerHTML;that._model.push(t);});},_generateMarkup:function(){var el=document.createElement('ul');el.className='unstyled ink-sortable-list';var that=this;this._model.forEach(function(label,idx){var liEl=document.createElement('li');if(idx===that._index){liEl.className='drag';}
liEl.innerHTML=['<span class="ink-label ink-info"><i class="icon-reorder"></i>',that._options.dragLabel,'</span>',label].join('');el.appendChild(liEl);});return el;},_getY:function(ev){if(ev.type.indexOf('touch')===0){return ev.changedTouches[0].pageY;}
if(typeof ev.pageY==='number'){return ev.pageY;}
return ev.clientY;},_refresh:function(skipObs){var el=this._generateMarkup();this._element.parentNode.replaceChild(el,this._element);this._element=el;Aux.restoreIdAndClasses(this._element,this);if(!skipObs){this._observe();}},_onDown:function(ev){var tgtEl=Event.element(ev);if(tgtEl.nodeName.toLowerCase()==='i'){tgtEl=tgtEl.parentNode;}
if(tgtEl.nodeName.toLowerCase()!=='span'||!Css.hasClassName(tgtEl,'ink-label')){return;}
Event.stop(ev);var liEl=tgtEl.parentNode;this._index=Aux.childIndex(liEl);this._height=liEl.offsetHeight;this._startY=this._getY(ev);this._isMoving=true;document.body.style.cursor='move';this._refresh(false);return false;},_onMove:function(ev){if(!this._isMoving){return;}
Event.stop(ev);var y=this._getY(ev);var dy=y-this._startY;var sign=dy>0?1:-1;var di=sign*Math.floor(Math.abs(dy)/this._height);if(di===0){return;}
di=di/Math.abs(di);if((di===-1&&this._index===0)||(di===1&&this._index===this._model.length-1)){return;}
var a=di>0?this._index:this._index+di;var b=di<0?this._index:this._index+di;this._model.splice(a,2,this._model[b],this._model[a]);this._index+=di;this._startY=y;this._refresh(false);},_onUp:function(ev){if(!this._isMoving){return;}
Event.stop(ev);this._index=undefined;this._isMoving=false;document.body.style.cursor='';this._refresh();},getModel:function(){return this._model.slice();},destroy:Aux.destroyComponent};SAPO.Ink.SortableList=SortableList;})(window);
(function(undefined){'use strict';SAPO.namespace('Ink');var Aux=SAPO.Ink.Aux,Event=SAPO.Dom.Event,Selector=SAPO.Dom.Selector;var TreeView=function(selector,options){this._options=SAPO.extendObj({selectable:false,startsCollapsed:false,onClick:undefined},options||{});this._handlers={click:this._onClick.bindObjEvent(this)};if(!Aux.isDOMElement(selector)){selector=Selector.select(selector);if(selector.length===0){throw new TypeError('1st argument must either be a DOM Element or a selector expression!');}
selector=selector[0];}
this._element=selector;this._model=[];this._selectedIndex=[-1];if(this._options.model instanceof Array){this._model=this._options.model;this._createdFrom='JSON';}
else if(this._element.nodeName.toLowerCase()==='ul'){this._createdFrom='DOM';}
else{throw new TypeError('You must pass a selector expression/DOM element as 1st option or provide a model on 2nd argument!');}
this._init();};TreeView.prototype={_init:function(){if(this._createdFrom==='DOM'){this._extractModelFromDOM();this._createdFrom='JSON';}
var el=this._generateMarkup();var parentEl=this._element.parentNode;if(!this._notFirstInit){Aux.storeIdAndClasses(this._element,this);this._notFirstInit=true;}
parentEl.insertBefore(el,this._element);parentEl.removeChild(this._element);this._element=el;Aux.restoreIdAndClasses(this._element,this);Event.observe(this._element,'click',this._handlers.click);Aux.registerInstance(this,this._element,'treeView');if(this._options.startsCollapsed){delete this._options.startsCollapsed;this.collapseTree();}},_extractModelFromDOM:function(){this._model=[''];this._extractLevel(this._element,this._model);if(this._model.length>1&&this._model[1]&&this._model[1].length===1){this._model=this._model[1][0];}},_extractLevel:function(ulEl,model){var liEls=Selector.select('> li',ulEl);var liEl,label,subModel,subUlEl,a,b;var children=[];for(var i=0,f=liEls.length;i<f;++i){liEl=liEls[i];label=liEl.innerHTML;a=label.indexOf('<ul>');b=label.lastIndexOf('</ul>');if(a!==-1&&b!==-1){label=label.substring(0,a)+label.substring(b+5);}
subModel=[label];subUlEl=Selector.select('> ul',liEl)[0];if(subUlEl){this._extractLevel(subUlEl,subModel);}
children.push(subModel);}
if(f>0){model[1]=children;}},_generateMarkup:function(){var el=document.createElement('ul');el.className='ink-tree-view';this._generateLevel(el,this._model,[0]);return el;},_generateLevel:function(ulEl,model,index){var liEl=document.createElement('li');liEl.setAttribute('data-index',index.join(' '));var buttonEl=document.createElement('button');var hasChildren=model.length>1&&model[1]&&model[1].length>0;var isOpened=!model[2];buttonEl.className=isOpened?'icon-caret-down':'icon-caret-right';var aEl;if(model[0]){if(hasChildren){liEl.appendChild(buttonEl);aEl=document.createElement('a');aEl.setAttribute('href','#');if(index.toString()===this._selectedIndex.toString()){aEl.className='ink-info';}
aEl.innerHTML=model[0];liEl.appendChild(aEl);}
else{liEl.innerHTML=model[0];}}
if(model.length>1){var subUlEl=document.createElement('ul');var subModel=model[1];var subIndex;for(var i=0,f=subModel.length;i<f;++i){subIndex=index.slice();subIndex.push(i);this._generateLevel(subUlEl,subModel[i],subIndex);}
liEl.appendChild(subUlEl);subUlEl.style.display=isOpened?'':'none';}
ulEl.appendChild(liEl);},_nodeFromIndex:function(index){index=index.slice();var n=this._model;index.shift();var i;while(typeof(i=index.shift())==='number'){n=n[1][i];}
return n;},_toggleNode:function(node){if(!node[0]||node.length<2){return;}
var isHidden=!!node[2];if(isHidden){node.pop();if(!node[1]){node.pop();}}
else{node[2]=1;}},_onClick:function(ev){var tgtEl=Event.element(ev);var stopEvent=!(tgtEl.nodeName.toLowerCase()==='a'&&tgtEl.getAttribute('href')!=='#');while(tgtEl.nodeName.toLowerCase()!=='li'){tgtEl=tgtEl.parentNode;}
if(stopEvent){Event.stop(ev);}
var index=tgtEl.getAttribute('data-index').split(' ');index=index.map(function(i){return parseInt(i,10);});if(this._options.selectable){this._selectedIndex=index;}
var n=this._nodeFromIndex(index);this._toggleNode(n);this._init();if(this._options.onClick){this._options.onClick({index:index.slice(),value:this.getNodeValue(index)},this);}
return false;},_setTreeCollapse:function(collapse){this._setNodeCollapse(this._model,collapse);},_setNodeCollapse:function(node,collapse){if(node.length<2){return;}
if(node[0]){if(collapse){node[2]=1;}
else if(node[2]){node.pop();if(!node[1]){node.pop();}}}
if(node[1]){for(var i=0,f=node[1].length;i<f;++i){this._setNodeCollapse(node[1][i],collapse);}}},getModel:function(){return Aux.clone(this._model);},getNodeValue:function(index){var node=this._nodeFromIndex(index);return node[0];},expandTree:function(){this._setTreeCollapse(false);this._init();},collapseTree:function(){this._setTreeCollapse(true);this._init();},toggleNode:function(index){var node=this._nodeFromIndex(index);this._toggleNode(node);},destroy:Aux.destroyComponent};SAPO.Ink.TreeView=TreeView;})();
(function(undefined){'use strict';SAPO.namespace('Ink');var Aux=SAPO.Ink.Aux,Css=SAPO.Dom.Css,Event=SAPO.Dom.Event;var genAEl=function(inner){var aEl=document.createElement('a');aEl.setAttribute('href','#');aEl.innerHTML=inner;return aEl;};var Pagination=function(selector,options){this._options=SAPO.extendObj({size:undefined,previousLabel:'Previous',nextLabel:'Next',onChange:undefined,setHash:false,hashParameter:'page'},options||{});this._handlers={click:this._onClick.bindObjEvent(this)};this._current=0;this._itemLiEls=[];this._element=Aux.elOrSelector(selector,'1st argument');if(!Aux.isInteger(this._options.size)){throw new TypeError('size option is a required integer!');}
else if(this._options.size<0){throw new RangeError('size option must be equal or more than 0!');}
if(this._options.onChange!==undefined&&typeof this._options.onChange!=='function'){throw new TypeError('onChange option must be a function!');}
this._init();};Pagination.prototype={_init:function(){this._generateMarkup(this._element);this._updateItems();this._observe();Aux.registerInstance(this,this._element,'pagination');},_observe:function(){Event.observe(this._element,'click',this._handlers.click);},_updateItems:function(){var liEls=this._itemLiEls;var isSimpleToggle=this._options.size===liEls.length;var i,f,liEl;if(isSimpleToggle){for(i=0,f=this._options.size;i<f;++i){Css.setClassName(liEls[i],'active',i===this._current);}}
else{for(i=liEls.length-1;i>=0;--i){this._ulEl.removeChild(liEls[i]);}
liEls=[];for(i=0,f=this._options.size;i<f;++i){liEl=document.createElement('li');liEl.appendChild(genAEl(i+1));Css.setClassName(liEl,'active',i===this._current);this._ulEl.insertBefore(liEl,this._nextEl);liEls.push(liEl);}
this._itemLiEls=liEls;}
Css.setClassName(this._prevEl,'disabled',!this.hasPrevious());Css.setClassName(this._nextEl,'disabled',!this.hasNext());},_generateMarkup:function(el){Css.addClassName(el,'ink-navigation');var liEl;var ulEl=document.createElement('ul');Css.addClassName(ulEl,'pagination');liEl=document.createElement('li');liEl.appendChild(genAEl(this._options.previousLabel));this._prevEl=liEl;Css.addClassName(liEl,'previous');ulEl.appendChild(liEl);liEl=document.createElement('li');liEl.appendChild(genAEl(this._options.nextLabel));this._nextEl=liEl;Css.addClassName(liEl,'next');ulEl.appendChild(liEl);el.appendChild(ulEl);this._ulEl=ulEl;},_onClick:function(ev){Event.stop(ev);var tgtEl=Event.element(ev);if(tgtEl.nodeName.toLowerCase()!=='a'){return;}
var liEl=tgtEl.parentNode;if(liEl.nodeName.toLowerCase()!=='li'){return;}
if(Css.hasClassName(liEl,'active')||Css.hasClassName(liEl,'disabled')){return;}
var isPrev=Css.hasClassName(liEl,'previous');var isNext=Css.hasClassName(liEl,'next');if(isPrev||isNext){this.setCurrent(isPrev?-1:1,true);}
else{var nr=parseInt(tgtEl.innerHTML,10)-1;this.setCurrent(nr);}},setSize:function(sz){if(!Aux.isInteger(sz)){throw new TypeError('1st argument must be an integer number!');}
this._options.size=sz;this._updateItems();this._current=0;},setCurrent:function(nr,isRelative){if(!Aux.isInteger(nr)){throw new TypeError('1st argument must be an integer number!');}
if(isRelative){nr+=this._current;}
if(nr<0){nr=0;}
else if(nr>this._options.size-1){nr=this._options.size-1;}
this._current=nr;this._updateItems();if(this._options.setHash){var o={};o[this._options.hashParameter]=nr;Aux.setHash(o);}
if(this._options.onChange){this._options.onChange(this);}},getSize:function(){return this._options.size;},getCurrent:function(){return this._current;},isFirst:function(){return this._current===0;},isLast:function(){return this._current===this._options.size-1;},hasPrevious:function(){return this._current>0;},hasNext:function(){return this._current<this._options.size-1;},destroy:Aux.destroyComponent};SAPO.Ink.Pagination=Pagination;})();
(function(undefined){'use strict';SAPO.namespace('Ink');var Aux=SAPO.Ink.Aux,Css=SAPO.Dom.Css,Element=SAPO.Dom.Element,Event=SAPO.Dom.Event,Selector=SAPO.Dom.Selector,Arr=SAPO.Utility.Array;var Table=function(selector,options){this._options=SAPO.extendObj({model:undefined,endpoint:undefined,fields:undefined,fieldNames:{},formatters:{},sortableFields:[],pageSize:undefined,pagination:undefined,onCellClick:undefined,onHeaderClick:undefined},options||{});this._handlers={headerclick:this._onHeaderClick.bindObjEvent(this),cellclick:this._onCellClick.bindObjEvent(this),updatecount:this._onUpdateCount.bindObj(this)};this._element=Aux.elOrSelector(selector,'1st argument');if(this._options.model&&this._options.endpoint){throw new TypeError('This component requires only _one_ of the following options: model, endpoint or be given a table element.');}
else if(!this._options.model&&!this._options.endpoint){if(this._element.nodeName.toLowerCase()!=='table'){throw new TypeError('This component requires one of the following options: model, endpoint or the selector pointing to a table element with datatable format.');}
else{this._extractModelFromDOM();}}
if(this._options.model){if(this._options.model instanceof Array){this._model=this._options.model;}
else{throw new TypeError('model option must be passed as an array of objects!');}}
else if(this._options.endpoint){if(typeof this._options.endpoint!=='string'){throw new TypeError('endpoint option should be a server URI!');}}
if(this._options.formatters){if(typeof this._options.formatters!=='object'){throw new TypeError('formatters option expected an object of field -> function(fieldValue, item, tdEl)!');}}
if(!(this._options.fields instanceof Array)){throw new TypeError('fields option expects an array of strings!');}
if(this._options.onCellClick&&typeof this._options.onCellClick!=='function'){throw new TypeError('onCellClick options expects a function!');}
if(this._options.onHeaderClick&&typeof this._options.onHeaderClick!=='function'){throw new TypeError('onHeaderClick options expects a function!');}
this._orderBy=undefined;this._orderDir=1;if(this._options.pageSize){if(!Aux.isInteger(this._options.pageSize)){throw new TypeError('pageSize option must be an integer number!');}
var pagEl;if(this._options.pagination){pagEl=Aux.elOrSelector(this._options.pagination,'pagination option');}
else{pagEl=document.createElement('nav');Element.insertAfter(pagEl,this._element);}
this._pagEl=pagEl;if(this._options.model){this._onUpdateCount(null,this._options.model.length);}
else{Aux.ajaxJSON(this._options.endpoint,{op:'count'},this._handlers.updatecount);}}
else{this._init();this._handlers.updatecount();}};Table.prototype={_init:function(){this._generateMarkup(this._element);this._observe();Aux.registerInstance(this,this._element,'table');},_extractModelFromDOM:function(){var thEls=Selector.select('> thead > tr > th',this._element);var trEls=Selector.select('> tbody > tr',this._element);this._options.fields=[];this._options.fieldNames={};this._options.model=[];var name,label,o,tdEls,that=this;thEls.forEach(function(thEl){try{label=thEl.innerHTML;name=thEl.getAttribute('id');if(name){if(name.indexOf('th_')===0){name=name.substring(3);}}
else{name=label.toLowerCase().replace(/ /g,'_');}
that._options.fields.push(name);that._options.fieldNames[name]=label;}catch(ex){console.error('problematic element:');console.error(thEl);throw new Error('Problem parsing table data from DOM!');}});trEls.forEach(function(trEl){tdEls=Selector.select('> td',trEl);o={};tdEls.forEach(function(tdEl,idx){try{name=tdEl.getAttribute('headers');if(name){if(name.indexOf('th_')===0){name=name.substring(3);}}
else{name=that._options.fields[idx];}
label=tdEl.innerHTML;o[name]=label;}catch(ex){console.error('problematic element:');console.error(tdEl);throw new Error('Problem parsing table data from DOM!');}});that._options.model.push(o);});},_observe:function(){Event.observe(this._theadEl,'click',this._handlers.headerclick);Event.observe(this._tbodyEl,'click',this._handlers.cellclick);},_whichRow:function(el){while(el.nodeName.toLowerCase()!=='tr'){el=el.parentNode;}
if(this._pagination){return Aux.childIndex(el);}},_whichColumn:function(el){return Aux.childIndex(el);},_remoteQuery:function(cb){Aux.ajaxJSON(this._options.endpoint,{op:'count'},function(err,data1){if(err){return cb(err);}
var count=data1.count;if(this._pagination&&count!==this._modelLength){var sz=Math.ceil(count/this._options.pageSize);if(this._pagination.getCurrent()>sz-1){console.log('setCurrent',sz-1);return this._pagination.setCurrent(sz-1);}
this._onUpdateCount(null,count);}
var params={op:'list',orderDir:this._orderDir};if(this._options.pageSize){params.pageSz=this._options.pageSize;params.pageNr=this._pagination.getCurrent();}
if(this._orderBy){params.orderBy=this._orderBy;}
Aux.ajaxJSON(this._options.endpoint,params,function(err,data2){if(err){return cb(err);}
cb(null,data2.items);}.bindObj(this));}.bindObj(this));},_localQuery:function(cb){var items=this._model.slice();var sz;var count=items.length;if(this._pagination&&count!==this._modelLength){sz=Math.ceil(count/this._options.pageSize);if(this._pagination.getCurrent()>sz-1){console.log('setCurrent',sz-1);return this._pagination.setCurrent(sz-1);}
this._onUpdateCount(null,count);}
var field=this._orderBy;if(field){var val=items[0][field];var getterFn=function(item){return item[field];};var sorterFn;if(typeof val==='number' || val.match(/^\-?\d+\.?\d*$/)){sorterFn=this._orderDir>0?function(a,b){return getterFn(a)-getterFn(b);}:function(b,a){return getterFn(a)-getterFn(b);};}
else{sorterFn=this._orderDir>0?function(a,b){var A=getterFn(a),B=getterFn(b);return(A<B?-1:(A>B?1:0));}:function(b,a){var A=getterFn(a),B=getterFn(b);return(A<B?-1:(A>B?1:0));};}
items.sort(sorterFn);}
sz=this._options.pageSize;var start;if(sz!==undefined){start=sz*this._pagination.getCurrent();items=items.slice(start,start+sz);}
cb(null,items);},_query:function(cb){this[this._model?'_localQuery':'_remoteQuery'](function(err,items){if(err){throw err;}
this._visibleItems=items;cb(null,items);}.bindObj(this));},_updateHeaders:function(theadEl){if(!theadEl){theadEl=this._theadEl;}
Aux.cleanChildren(theadEl);var i,f,field,label,tdEl,trEl=document.createElement('tr');for(i=0,f=this._options.fields.length;i<f;++i){field=this._options.fields[i];tdEl=document.createElement('th');label=this._options.fieldNames[field]||field;if(this._orderBy===field){label=[label,' <i class="icon-caret-',this._orderDir>0?'down':'up','"></i>'].join('');}
tdEl.innerHTML=label;trEl.appendChild(tdEl);}
theadEl.appendChild(trEl);},_generateMarkup:function(el){Css.addClassName(el,'ink-table');var theadEl=document.createElement('thead');this._updateHeaders(theadEl);var tbodyEl=document.createElement('tbody');Aux.cleanChildren(el);el.appendChild(theadEl);el.appendChild(tbodyEl);this._theadEl=theadEl;this._tbodyEl=tbodyEl;this.refresh();},_onPaginationChange:function(){this.refresh();},_onHeaderClick:function(ev){var el=Event.element(ev);var colNr=this._whichColumn(el);var field=this._options.fields[colNr];var orderDir;if(this._options.sortableFields==='*'||Arr.inArray(field,this._options.sortableFields)){if(this._orderBy===field){this._orderDir*=-1;}
else{this._orderBy=field;this._orderDir=1;}
orderDir=this._orderDir;this._updateHeaders();if(this._pagination){this._pagination.setCurrent(0);}
else{this.refresh();}}
if(this._options.onHeaderClick){this._options.onHeaderClick(this,{col:colNr,field:field,orderDir:orderDir});}},_onCellClick:function(ev){var el=Event.element(ev);var rowNr=this._whichRow(el);var colNr=this._whichColumn(el);var field=this._options.fields[colNr];var item=this._visibleItems[rowNr];if(this._options.onCellClick){this._options.onCellClick(this,{item:item,row:rowNr,col:colNr,field:field});}},_onUpdateCount:function(err,modelLength){if(!this._options.pageSize){return this.refresh();}
if(err){throw err;}
if(typeof modelLength==='object'){modelLength=modelLength.count;}
this._modelLength=modelLength;var sz=Math.ceil(modelLength/this._options.pageSize);if(!this._pagination){this._pagination=new SAPO.Ink.Pagination(this._pagEl,{size:sz,onChange:this._onPaginationChange.bindObj(this)});this._init();}
else{this._pagination.setSize(sz);this.refresh();}},getModel:function(){return this._model.slice();},setModel:function(mdl){if(!this._options.model){throw new Error('Component has\'t been instanced in model mode.');}
this._model=mdl;this.refresh();},getVisibleItems:function(){return this._visibleItems.slice();},refresh:function(){this._query(function(err,items){var tbodyEl=document.createElement('tbody');var i,I,j,J,trEl,tdEl,item,field,value,formatter;J=this._options.fields.length;for(i=0,I=items.length;i<I;++i){item=items[i];trEl=document.createElement('tr');for(j=0;j<J;++j){field=this._options.fields[j];value=item[field];formatter=this._options.formatters[field];tdEl=document.createElement('td');if(formatter){formatter(value,item,tdEl);}
else{tdEl.innerHTML=value||'';}
trEl.appendChild(tdEl);}
tbodyEl.appendChild(trEl);}
this._element.replaceChild(tbodyEl,this._tbodyEl);this._tbodyEl=tbodyEl;Event.observe(this._tbodyEl,'click',this._handlers.cellclick);}.bindObj(this));},destroy:Aux.destroyComponent};SAPO.Ink.Table=Table;})();
(function(undefined){'use strict';SAPO.namespace('Ink');var Aux=SAPO.Ink.Aux,Css=SAPO.Dom.Css,Event=SAPO.Dom.Event,Selector=SAPO.Dom.Selector;var Tabs=function(selector,options){this._options=SAPO.extendObj({active:undefined,disabled:[],onBeforeChange:undefined,onChange:undefined},options||{});this._handlers={tabClicked:this._onTabClicked.bindObjEvent(this),disabledTabClicked:this._onDisabledTabClicked.bindObjEvent(this),resize:this._onResize.bindObjEvent(this)};if(!SAPO.Ink.Aux.isDOMElement(selector)){selector=Selector.select(selector);if(selector.length===0){throw new TypeError('1st argument must either be a DOM Element or a selector expression!');}
this._element=selector[0];}
this._init();};Tabs.prototype={_init:function(){this._menu=Selector.select('.ink-tabs-nav',this._element)[0];this._menuTabs=this._getChildElements(this._menu);this._contentTabs=Selector.select('.ink-tabs-container',this._element);this._initializeDom();this._observe();this._setFirstActive();this._changeTab(this._activeMenuLink);this._handlers.resize();Aux.registerInstance(this,this._element,'tabs');},_initializeDom:function(){for(var i=0;i<this._contentTabs.length;i++){Css.hide(this._contentTabs[i]);}},_observe:function(){this._menuTabs.forEach(function(elem){var link=Selector.select('a',elem)[0];if(SAPO.Utility.Array.inArray(link.getAttribute('href'),this._options.disabled)){this.disable(link);}else{this.enable(link);}}.bindObj(this));Event.observe(window,'resize',this._handlers.resize);},_setFirstActive:function(){var hash=window.location.hash;this._activeContentTab=Selector.select(hash,this._element)[0]||Selector.select(this._hashify(this._options.active),this._element)[0]||Selector.select('.ink-tabs-container',this._element)[0];this._activeMenuLink=this._findLinkByHref(this._activeContentTab.getAttribute('id'));if(Css.hasClassName(this._activeMenuLink,'ink-disabled')){for(var i=0;i<this._menuTabs.length;i++){var link=Selector.select('a',this._menuTabs[i])[0];if(!Css.hasClassName(link,'ink-disabled')){this._activeMenuLink=link;break;}}}
this._activeMenuTab=this._activeMenuLink.parentNode;},_changeTab:function(link,runCallbacks){if(runCallbacks&&typeof this._options.onBeforeChange!=='undefined'){this._options.onBeforeChange(this);}
var selector=link.getAttribute('href');Css.removeClassName(this._activeMenuTab,'active');Css.removeClassName(this._activeContentTab,'active');if(this._activeContentTab){Css.hide(this._activeContentTab);}
this._activeMenuLink=link;this._activeMenuTab=this._activeMenuLink.parentNode;this._activeContentTab=Selector.select(selector,this._element)[0];Css.addClassName(this._activeMenuTab,'active');Css.addClassName(this._activeContentTab,'active');Css.show(this._activeContentTab);if(runCallbacks&&typeof(this._options.onChange)!=='undefined'){this._options.onChange(this);}},_onTabClicked:function(ev){Event.stop(ev);var target=Event.findElement(ev,'A');if(target.nodeName.toLowerCase()!=='a'){return;}
window.location.hash=target.getAttribute('href');if(target===this._activeMenuLink){return;}
this.changeTab(target);},_onDisabledTabClicked:function(ev){Event.stop(ev);},_onResize:function(){var currentLayout=SAPO.Ink.Aux.currentLayout();if(currentLayout===this._lastLayout){return;}
if(currentLayout===SAPO.Ink.Aux.Layouts.SMALL||currentLayout===SAPO.Ink.Aux.Layouts.MEDIUM){Css.removeClassName(this._menu,'menu');Css.removeClassName(this._menu,'horizontal');Css.addClassName(this._menu,'pills');}else{Css.addClassName(this._menu,'menu');Css.addClassName(this._menu,'horizontal');Css.removeClassName(this._menu,'pills');}
this._lastLayout=currentLayout;},_hashify:function(hash){if(!hash){return"";}
return hash.indexOf('#')===0?hash:'#'+hash;},_findLinkByHref:function(href){href=this._hashify(href);var ret;this._menuTabs.forEach(function(elem){var link=Selector.select('a',elem)[0];if(link.getAttribute('href')===href){ret=link;}});return ret;},_getChildElements:function(parent){var childNodes=[];var children=parent.children;for(var i=0;i<children.length;i++){if(children[i].nodeType===1){childNodes.push(children[i]);}}
return childNodes;},changeTab:function(selector){var element=(selector.nodeType===1)?selector:this._findLinkByHref(this._hashify(selector));if(!element||Css.hasClassName(element,'ink-disabled')){return;}
this._changeTab(element,true);},disable:function(selector){var element=(selector.nodeType===1)?selector:this._findLinkByHref(this._hashify(selector));if(!element){return;}
Event.stopObserving(element,'click',this._handlers.tabClicked);Event.observe(element,'click',this._handlers.disabledTabClicked);Css.addClassName(element,'ink-disabled');},enable:function(selector){var element=(selector.nodeType===1)?selector:this._findLinkByHref(this._hashify(selector));if(!element){return;}
Event.stopObserving(element,'click',this._handlers.disabledTabClicked);Event.observe(element,'click',this._handlers.tabClicked);Css.removeClassName(element,'ink-disabled');},activeTab:function(){return this._activeContentTab.getAttribute('id');},activeMenuTab:function(){return this._activeMenuTab;},activeMenuLink:function(){return this._activeMenuLink;},activeContentTab:function(){return this._activeContentTab;},destroy:Aux.destroyComponent};SAPO.Ink.Tabs=Tabs;})();
(function(undefined){'use strict';SAPO.namespace('Ink');var Css=SAPO.Dom.Css,Arr=SAPO.Utility.Array,Event=SAPO.Dom.Event;var Close=function(){Event.observe(document.body,'click',function(ev){var el=Event.element(ev);if(!Css.hasClassName(el,'ink-close')&&!Css.hasClassName(el,'ink-dismiss')){return;}
var classes;do{if(!el.className){continue;}
classes=el.className.split(' ');if(!classes){continue;}
if(Arr.inArray('ink-alert',classes)||Arr.inArray('ink-alert-block',classes)){break;}}while(el=el.parentNode);if(el){Event.stop(ev);el.parentNode.removeChild(el);}});};SAPO.Ink.Close=Close;})();
(function(window,undefined){'use strict';SAPO.namespace('Ink');var Aux=SAPO.Ink.Aux,Css=SAPO.Dom.Css,Element=SAPO.Dom.Element,Event=SAPO.Dom.Event;var Dockable=function(selector,options){this._element=Aux.elOrSelector(selector,'1st argument');this._options=SAPO.extendObj({fixedHeight:0,throttlingDeltaT:0.2},options||{});if(this._element.getAttribute('data-fixed-height')!==null){var fh=parseInt(this._element.getAttribute('data-fixed-height'),10);this._options.fixedHeight=fh;}
this._isFixed=false;this._timer=undefined;this._handlers={timer:this._onTimer.bindObj(this),change:this._onChange.bindObjEvent(this)};this._init();};Dockable.prototype={version:'0.1',_init:function(){Event.observe(window,'scroll',this._handlers.change);Event.observe(window,'resize',this._handlers.change);Aux.registerInstance(this,this._element,'dockable');},_fix:function(){if(this._isFixed){return;}
Css.addClassName(this._element,'ink-docked');this._oldTop=this._element.style.top;this._element.style.top=this._options.fixedHeight+'px';this._isFixed=true;},_reset:function(){if(!this._isFixed){return;}
Css.removeClassName(this._element,'ink-docked');this._element.style.top=this._oldTop;delete this._oldTop;this._isFixed=false;},_onTimer:function(){clearTimeout(this._timer);this._timer=undefined;var sh=Element.scroll()[1];if(!this._isFixed){var py=Element.offset2(this._element)[1];if(sh>py-this._options.fixedHeight){this._fixedAt=py;this._fix();}}
else{if(sh<this._fixedAt-this._options.fixedHeight){this._reset();}}},_onChange:function(){if(this._timer){clearTimeout(this._timer);}
this._timer=setTimeout(this._handlers.timer,this._options.throttlingDeltaT*1000);}};SAPO.Ink.Dockable=Dockable;})(window);
(function(window,undefined){'use strict';SAPO.namespace('Ink');var Aux=SAPO.Ink.Aux,Element=SAPO.Dom.Element,Event=SAPO.Dom.Event,Selector=SAPO.Dom.Selector;var Collapsible=function(selector,options){this._options=SAPO.extendObj({},options||{});this._handlers={resize:this._onResize.bindObjEvent(this),toggle:this._onToggle.bindObjEvent(this)};this._element=Aux.elOrSelector(selector,'1st argument');if(this._element.nodeName.toLowerCase()!=='ul'){this._element=Selector.select('ul',this._element)[0];}
this._init();};Collapsible.prototype={_init:function(){this._expandedMenu=Selector.select('>li',this._element);this._insertMenuElement();this._observe();Aux.registerInstance(this,this._element,'collapsible');},_insertMenuElement:function(){this._menuTitleElement=Selector.select('li:first',this._element)[0];this._toggle=Element.create('button',{className:'ink-for-s ink-for-m ink-button',innerHTML:'<i class="icon-reorder" style="height:auto"></i>'});this._expandedMenu=Element.siblings(this._menuTitleElement);this._menuTitleElement.appendChild(this._toggle);this._onResize();this._menuTitleElement.appendChild(this._toggle);this._element.insertBefore(this._menuTitleElement,this._element.firstChild);},_observe:function(){Event.observe(window,'resize',this._handlers.resize);Event.observe(this._toggle,'click',this._handlers.toggle);},_onResize:function(){var currentLayout=SAPO.Ink.Aux.currentLayout();if(currentLayout===this._lastLayout){return;}
if(currentLayout===SAPO.Ink.Aux.Layouts.SMALL||currentLayout===SAPO.Ink.Aux.Layouts.MEDIUM){this._initVertical();}else{this._initHorizontal();}
this._lastLayout=currentLayout;},_onToggle:function(ev,show){if(ev){Event.stop(ev);}
var display;if(typeof(show)!=='undefined'){display=show?'block':'none';}else{display=this._expandedMenu[0].style.display==='none'?'block':'none';}
this._expandedMenu.forEach(function(elem){elem.style.display=display;});},_initHorizontal:function(){this._toggle.style.display='none';this._expandedMenu.forEach(function(elem){elem.style.display='inline-block';});},_initVertical:function(){this._toggle.style.display='block';this._expandedMenu.forEach(function(elem){elem.style.display='none';});},toggle:function(){this._onToggle();}};SAPO.Ink.Collapsible=Collapsible;})(window);
