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
var Account=MyClass.extend({_controller:"account",defaultCategoryId:1,options:{},init:function(){
var c=this;
var rel=v(0).attr("rel");
var _32=$.parseJSON(rel);
c.options=_32;
c.sessionID=_32.sessionID;
switch(_32.p){
case "subscriptions":
c._b();
c._f();
c._e(c.defaultCategoryId);
break;
case "billing":
c._c();
break;
case "profile":
c._g();
break;
}
},_b:function(){
var c=this;
$(".del-tog").click(function(_33){
$(".delOptions").addClass("hide");
_33.stopPropagation();
$(this).nextAll("ul.active").removeClass("hide");
});
$(document).click(function(){
$(".delOptions").addClass("hide");
$(".msg_error").animate({height:"0"},500,function(){
});
});
$(".makeSlider").each(function(i,el){
$(el).liquidSlider({autoHeight:false,dynamicTabs:true,hoverArrows:false,dynamicTabsPosition:"bottom"});
});
$(".skipLink").on("click",function(){
var _34=$(this).parents("li.infoEl")[0];
var _35=$(_34).attr("data-isub");
var _36=$(_34).attr("data-date");
var _37={iSub:_35,date:_36,iUser:c.options.iUser,status:"skipped"};
c.ajax("svr0",_37,c._j);
});
$(".donateLink").on("click",function(){
var _38=$(this).parents("li.infoEl")[0];
var _39=$(_38).attr("data-isub");
var _3a=$(_38).attr("data-date");
var _3b={iSub:_39,date:_3a,iUser:c.options.iUser,status:"donated",};
c.ajax("pdj1",_3b,c._j);
});
$(".reactivatePopupLink").on("click",function(){
var _3c=$(this).parents("li.infoEl")[0];
var _3d=$(_3c).attr("data-isub");
var _3e=$(_3c).attr("data-date");
var _3f={iSub:_3d,date:_3e,iUser:c.options.iUser,iOrder:$(_3c).attr("data-iorder"),status:"active"};
c.ajax("spob2",_3f,c._j);
});
},_c:function(){
var c=this;
v(1).on("click",function(){
c._h();
});
},_d:function(_40,_41,_42,_43){
var el=$("#skipSlider_"+_40).find("[data-date=\""+_41+"\"]");
var _43=(_43==null)?null:_43;
$.each(["skipped","donated","active"],function(i,str){
$(el).removeClass("del-"+str);
});
$(el).addClass("del-"+_42);
switch(_42){
case "skipped":
$(el).find("h3").html("Delivery<br/>Skipped");
$(el).find(".delOptions").removeClass("active");
$(el).find(".delOptions.for-skipped").addClass("active");
$(el).attr("data-iorder",_43);
break;
case "donated":
$(el).find("h3").html("Delivery<br/>Donated");
$(el).find(".del-tog").hide();
break;
case "active":
$(el).find("h3").html("Delivery<br/>Scheduled");
$(el).find(".delOptions").removeClass("active");
$(el).find(".delOptions.for-active").addClass("active");
break;
}
},_e:function(_44){
var c=this;
c.ajax("onmd3",{iCategory:_44,iUser:c.options.iUser},function(_45){
var _46=$.parseJSON(_45);
if(!_46.success){
alertify.error(err(_46.errorCodes[0]));
}else{
v(2).loadTemplate(v(3),_46.products);
$(".productCheck").each(function(_47,el){
el=$(el);
el.attr("value",el.attr("alt"));
});
}
});
},_f:function(){
var c=this;
v(4).on("change",function(e){
c._e($(e.target).val());
});
v(5).on("click",function(){
c.ajaxForm("faitp","addSubscriptionForm",c._2);
return false;
});
$(".confirmSkip").on("click",function(e){
var id=$(e.target).attr("rel");
c.ajaxForm("mgyfq","tripForm_"+id,c._2);
return false;
});
$(".submitChange").on("click",function(e){
var id=$(e.target).attr("rel");
c.ajaxForm("epykr","changeSubscription"+id,c._2);
return false;
});
$(".stopSubscription").on("click",function(e){
var _48={iUser:$(e.target).attr("data-uid"),iSub:$(e.target).attr("data-sid")};
alertify.confirm($(e.target).attr("data-msg"),function(e){
if(e){
c.ajax("wsge4",_48,c._2);
}
});
});
$(".datepickerInput").datepicker({format:"mm/dd/yyyy"}).on("changeDate",function(){
$(this).datepicker("hide");
});
$(".reactivateLink").on("click",function(){
var _49={iOrder:$(this).attr("rel"),iUser:c.options.iUser};
c.ajax("spob2",_49,c._2);
});
},_g:function(){
var c=this;
v(6).on("click",function(){
c.ajaxForm("yxyds","fAccount",c._1);
return false;
});
v(7).on("click",function(){
c.ajaxForm("ztmut","security",c._1);
return false;
});
},_h:function(){
var c=this;
Stripe.setPublishableKey(c.options.stripeKey);
Stripe.card.createToken({number:v(8).val(),cvc:v(9).val(),exp_month:v(10).val(),exp_year:v(11).val()},c._5(c,c._k));
},_i:function(msg){
var c=this;
$(".msg_msg").html(msg);
$(".msg_error").removeClass("hide");
$(".msg_error").animate({height:"220px"},500,function(){
});
},_j:function(_4a){
var c=this;
var _4b=$.parseJSON(_4a);
if(!_4b.success){
var msg=err(_4b.errorCodes[0]);
c._i(msg);
}else{
c._d(_4b.request.iSub,_4b.request.date,_4b.request.status,(_4b.iOrder?_4b.iOrder:null));
}
},_k:function(_4c,_4d){
var c=this;
if(_4d.error){
alertify.alert(_4d.error.message);
}else{
c._stripeToken=_4d["id"];
c.ajax("jahu5",{token:_4d["id"],iUser:c.options.iUser},c._2);
}
}});

