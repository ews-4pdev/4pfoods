function isiPhone(){
return ((navigator.platform.indexOf("iPhone")!=-1)||(navigator.platform.indexOf("iPod")!=-1));
};
function toMoney(_1){
return "$"+_1.toFixed(2);
};
function task(_2){
return _2;
};
function ttask(_3){
return _3;
};
function v(_4){
return $("#"+s_[_4]);
};
function $defined(_5){
return (typeof _5!="undefined");
};
function d(_6){
return ($defined(v(_6)));
};
function p(_7){
return "."+s_[_7];
};
function q(_8){
return $(p(_8));
};
var MyClass=Class.extend({_controller:"main",init:function(_9){
},ajax:function(_a,_b,_c){
var c=this;
_b.dfdcy_=_a;
_b.sessionID=c.sessionID;
var _d=$.ajax({url:"/ajax/"+c._controller,type:"post",data:_b});
c._3(_d,_c);
},ajaxForm:function(_e,_f,_10){
var c=this;
var _11={};
_11.dfdcy_=_e;
_11.sessionID=c.sessionID;
var _12=$("#"+_f).serialize()+"&"+$.param(_11);
var _13=$.ajax({url:"/ajax/"+c._controller,type:"post",data:_12});
c._3(_13,_10);
},_0:function(_14,_15,_16){
$(".error-on").removeClass("error-on");
var _17=$defined(_16)?_16:"";
$(_14).each(function(_18,_19){
var _1a=_19.split(":");
var _1b=_errorCodes[_1a[0]];
var el=$("#"+_15+_1a[1]+_17);
var _1c=el.parents(".form-group");
_1c.addClass("error-on");
_1c.find(".error-notify").html(_1b);
});
},_1:function(_1d){
var c=this;
var _1e=$.parseJSON(_1d);
if(_1e.success){
location.href=_1e.url;
}else{
c._0(_1e.errorCodes,_1e.request.prefix);
}
},_2:function(_1f){
var c=this;
var _20=$.parseJSON(_1f);
if(_20.success){
if($defined(_20.msg)){
alertify.alert(_20.msg);
}else{
location.href=_20.url;
}
}else{
c._4(_20.errorCodes[0]);
}
},_3:function(_21,_22){
var c=this;
if(_22){
_21.done(c._5(this,_22));
}else{
_21.done(function(_23){
var _24=$.parseJSON(_23);
if(_24.success){
window.location.href=_24.url;
}else{
alert(_24.error);
}
});
}
_21.fail(function(_25,_26,_27){
alertify.error("There was an error with the request. Please contact administrator.");
});
},_4:function(_28){
var msg=err(_28);
alertify.error(msg);
},_5:function(_29,_2a){
return function(){
return _2a.apply(_29,arguments);
};
}});
var Engine=Class.extend({sessionKey:"",globals:{_appTriggerClass:"app-trigger",_confirmLinkClass:"confirmLink",_errorBlockClass:"error-block",_autoMessageClass:"auto-message"},init:function(_2b){
var c=this;
c.sessionKey=c.globals._sessionKey=_2b;
c._6();
c._8();
c._9();
c._7();
c._a();
},_6:function(){
var c=this;
var _2c=[];
$.each(c.globals,function(key,_2d){
_2c.push(key+" = '"+_2d+"';");
});
eval(_2c.join(""));
},_7:function(){
$("."+_autoMessageClass).each(function(_2e,el){
alertify.alert($(el).text());
});
},_8:function(){
$("."+_confirmLinkClass).on("click",function(e){
var msg=$(e.target).attr("data-msg")?$(e.target).attr("data-msg"):"Are you sure?";
alertify.confirm(msg,function(e2){
if(e2){
location.href=e.target.href;
}else{
return false;
}
});
return false;
});
},_9:function(){
$("."+_errorBlockClass).each(function(_2f,el){
alertify.error($(el).text());
});
},_a:function(){
var _30=[];
$.each($("."+_appTriggerClass),function(_31,el){
_30.push("window._"+el.id.substring(1)+" = new "+el.id.substring(1)+"();\n");
});
eval(_30.join(" "));
}});
var Driver=MyClass.extend({_controller:"driver",init:function(){
var c=this;
var rel=v(0).attr("rel");
var _32=$.parseJSON(rel);
c.options=_32;
c.sessionID=_32.sessionID;
switch(_32.p){
case "login":
c._c();
break;
case "home":
c._b();
break;
}
},_b:function(){
var c=this;
v(1).on("click",function(){
var _33={iSite:v(2).val(),isSuccess:v(3).val(),iDriver:c.options.iDriver};
c.ajax("omyei",_33,c._2);
});
},_c:function(){
var c=this;
v(4).on("click",function(){
var _34={Email:v(5).val(),Password:v(6).val(),prefix:"c"};
c.ajax("zhric",_34,c._2);
return false;
});
}});

