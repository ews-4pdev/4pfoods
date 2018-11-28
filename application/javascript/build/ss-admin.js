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
var Admin=MyClass.extend({_controller:"admin",_options:{},init:function(){
var c=this;
var rel=v(0).attr("rel");
var _32=$.parseJSON(rel);
c.options=_32;
c.sessionID=_32.sessionID;
c._controller="admin";
c.options=_32;
switch(_32.p){
case "login":
c._h();
break;
case "deliveries":
c._l();
break;
case "products":
c._i();
break;
case "viewproduct":
c._k();
break;
case "viewcustomer":
c._d();
break;
case "approval":
c._j();
break;
case "discounts":
c._f();
break;
case "vieworder":
c._g();
break;
case "viewpayment":
c._e();
break;
case "payments":
c._c();
break;
case "customers":
c._b();
break;
}
},_b:function(){
var c=this;
$(".archiveButton").on("click",function(){
var _33={iUser:$(this).attr("data-id")};
c.ajax("esn6",_33,c._2);
});
},_c:function(){
var c=this;
$(".refundLink").on("click",function(){
var el=$(this);
var _34=$.parseJSON(el.attr("data-info"));
v(1).attr("data-info",el.attr("data-info"));
v(2).html(_34.maxAmount);
});
v(1).on("click",function(){
var _35=$.parseJSON($(this).attr("data-info"));
var _36={iPayment:_35.iPayment,Amount:v(3).val()};
c.ajax("tjdt7",_36,c._2);
return false;
});
},_d:function(){
var c=this;
v(4).on("click",function(){
c.ajax("bykq8",{iUser:c.options.iUser},c._2);
return false;
});
$(".refundLink").on("click",function(){
var el=$(this);
var _37=$.parseJSON(el.attr("data-info"));
v(1).attr("data-info",el.attr("data-info"));
v(2).html(_37.maxAmount);
});
v(1).on("click",function(){
var _38=$.parseJSON($(this).attr("data-info"));
var _39={iPayment:_38.iPayment,Amount:v(3).val()};
c.ajax("tjdt7",_39,c._2);
return false;
});
v(5).on("click",function(){
var _3a={iUser:c.options.iUser,iSite:v(6).val()};
c.ajax("buqu9",_3a,c._2);
return false;
});
$(".pay-on-demand").on("click",function(){
var _3b={iOrder:$(this).attr("data-id")};
c.ajax("jevxa",_3b,c._2);
});
},_e:function(){
var c=this;
v(1).on("click",function(){
var _3c={iPayment:c.options.iPayment,Amount:v(7).val()};
c.ajax("tjdt7",_3c,c._2);
return false;
});
},_f:function(){
var c=this;
$(".unpublishButton").on("click",function(e){
c.ajax("rhamb",{iDiscount:$(e.target).attr("rel")},c._2);
});
v(8).on("click",function(){
c.ajaxForm("jpsyu","addDiscount",c._1);
return false;
});
},_g:function(){
var c=this;
v(9).on("click",function(){
});
},_h:function(){
var c=this;
v(10).on("click",function(){
var _3d={Email:v(11).val(),Password:v(12).val(),prefix:"c"};
c.ajax("zhric",_3d,c._2);
return false;
});
},_i:function(){
var c=this;
v(13).on("click",function(){
c.ajaxForm("gujwv","newProducts",c._1);
return false;
});
},_j:function(){
var c=this;
$(".submitApproval").each(function(_3e,el){
el=$(el);
el.on("click",function(){
var id=el.attr("rel");
c.ajaxForm("qsdww","approvalForm"+id,c._n);
return false;
});
});
$(".assignSelect").on("change",function(e){
var el=$(e.target);
var _3f={iUser:el.attr("rel"),iSite:el.val()};
c.ajax("iychd",_3f,c._2);
});
},_k:function(){
var c=this;
v(14).on("click",function(){
c.ajaxForm("gujwv","editProductForm",c._o);
return false;
});
},_l:function(){
var c=this;
$(".hideSiteLink").on("click",function(){
var el=$(this);
alertify.confirm("This will hide the site permanently from this page. Are you sure?",function(e){
if(e){
c.ajax("buvve",{iSite:el.attr("data-id")},c._2);
}
});
});
$(".editSiteLink").on("click",function(){
var el=$(this);
var _40=$.parseJSON($(this).parents("tr").attr("data-info"));
$.each(_40,function(key,_41){
$("#update"+key).val(_41);
});
});
v(15).on("click",function(){
c.ajaxForm("smpdx","updateSiteForm",c._1);
return false;
});
v(16).on("click",function(){
c.ajaxForm("qsdww","site",c._p);
return false;
});
v(17).on("click",function(e){
c.ajaxForm("tnjyy","addDriverForm",c._m);
return false;
});
$(".disableSite").on("click",function(e){
c.ajax("czodf",{iSite:$(e.target).attr("rel")},c._2);
return false;
});
$(".changeDeliveryDayLink").on("click",function(){
v(18).val($(this).attr("data-id"));
});
v(19).on("click",function(){
var _42={iSite:v(18).val(),DefaultDeliveryDay:v(20).val()};
c.ajax("xzuyg",_42,c._2);
});
$(".removeDriver").each(function(_43,el){
el=$(el);
el.on("click",function(){
var _44={iUser:el.attr("rel")};
if(confirm("Are you sure you want to delete this driver?")){
c.ajax("skrah",_44,function(){
location.href="/admin/deliveries";
});
}
});
});
},_m:function(_45){
var c=this;
var _46=$.parseJSON(_45);
if(_46.success){
location.href=_46.url;
}else{
c._0(_46.errorCodes,"driver");
}
},_n:function(_47){
var c=this;
var _48=$.parseJSON(_47);
if(_48.success){
location.href=_48.url;
}else{
c._0(_48.errorCodes,"formSite",_48.request.iUser);
}
},_o:function(_49){
var c=this;
var _4a=$.parseJSON(_49);
if(_4a.success){
location.href=_4a.url;
}else{
c._0(_4a.errorCodes,"p");
}
},_p:function(_4b){
var c=this;
var _4c=$.parseJSON(_4b);
if(_4c.success){
location.href=_4c.url;
}else{
c._0(_4c.errorCodes,"site");
}
}});

