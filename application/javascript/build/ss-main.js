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
var SignupForm=MyClass.extend({_controller:"signup",_stripeKey:"",_iProduct:"",discount:0,init:function(){
var c=this;
var rel=v(0).attr("rel");
var _32=$.parseJSON(rel);
c._stripeKey=_32.stripeKey;
c._iProduct=_32.iProduct;
c.sessionID=_32.sessionID;
switch(_32.p){
case "signup":
c._d();
c._i();
c._c();
break;
}
},_b:function(){
v(1).val("Tom");
v(2).val("Exampleton");
v(3).val("1231231234");
v(4).val("tom.exampleton@4pfoods.com");
v(5).val("123");
v(6).val("123");
v(7).val("4242424242424242");
v(8).val(11);
v(9).val(123);
v(10).val(1234);
},_c:function(){
var c=this;
v(4).on("blur",function(){
var _33={Email:v(4).val(),FirstName:v(1).val(),LastName:v(2).val()};
c.ajax("fink",_33,function(){
});
});
},_d:function(){
var c=this;
c._e();
c._g();
c._f();
$(".productCheck").on("click",function(e){
var el=$(e.target);
var txt="<p class=\"productTitle\">"+el.attr("rel")+"</p>";
var _34=parseInt(el.attr("alt"));
v(11).html(_34);
v(12).html("");
v(12).append(txt);
});
$("#p"+c._iProduct).attr("checked","checked");
$(".productCheck").on("click",function(){
c._i();
});
},_e:function(){
var c=this;
$(".collapseButton").on("click",function(e){
var _35=$("#"+$(e.target).attr("data-this"));
var _36=$("#"+$(e.target).attr("data-next"));
_35.collapse("hide");
_36.collapse("show");
});
v(13).on("click",function(){
$(this).button("loading");
c._h();
});
},_f:function(){
var c=this;
v(14).on("click",function(){
var _37={Code:v(15).val()};
c.ajax("gbvil",_37,c._l);
});
},_g:function(){
var c=this;
$(".chooseSite").on("click",function(){
v(10).val($(this).attr("data-code"));
v(10).trigger("keyup");
});
v(16).on("click",function(){
v(17).collapse("show");
$("#customAddress input, #customAddress select, #customAddress textarea").attr("disabled",false).val("");
v(18).removeClass("highlightMe");
alertify.set({labels:{ok:"Continue Creating Account",cancel:"Just Notify Me"}});
alertify.confirm(v(19).html(),function(e){
if(!e){
var _38={email:v(4).val(),firstname:v(1).val(),lastname:v(2).val()};
c.ajax("wqdym",_38,c._2);
}
});
alertify.set({labels:{ok:"OK",cancel:"Cancel"}});
});
v(10).on("click",function(){
$(this).select();
});
v(10).on("keyup",function(){
if(v(10).val().length==4){
c._k(v(10).val());
}
});
},_h:function(){
var c=this;
Stripe.setPublishableKey(c._stripeKey);
Stripe.card.createToken({number:v(7).val(),cvc:v(9).val(),exp_month:v(8).val(),exp_year:v(20).val()},c._5(c,c._n));
},_i:function(){
var c=this;
var _39=[];
var _3a=0;
v(12).html("");
$(".productCheck:checked").each(function(_3b,el){
var _3c=$("#pi_"+$(el).val());
var _3d=parseFloat(_3c.attr("rel"));
_3a+=_3d;
v(12).append("<div>"+_3c.html()+"</div>");
});
_3a-=parseFloat(c.discount);
v(11).html(toMoney(_3a));
},_j:function(){
var c=this;
var _3e=[];
$(".productCheck").each(function(_3f,el){
if(el.checked){
_3e.push(el.value);
}
});
var _40={FirstName:v(1).val(),LastName:v(2).val(),Address1:v(21).val(),Address2:v(22).val(),City:v(23).val(),StateId:v(24).val(),Zip:v(25).val(),Phone:v(3).val(),Email:v(4).val(),Password:v(5).val(),ConfirmPassword:v(6).val(),DeliverySiteNotes:v(26).val(),DietaryRestrictions:v(27).val(),DiscountCode:v(15).val(),Token:c._stripeToken,Products:_3e,AccessCode:v(10).val()};
c.ajax("prbln",_40,c._m);
},_k:function(_41){
var c=this;
var _42={code:_41};
c.ajax("qwjho",_42,c._o);
},_l:function(_43){
var c=this;
var _44=$.parseJSON(_43);
if(_44.success){
c.discount=parseFloat(_44.amount);
v(28).removeClass("hide");
v(29).html(_44.amount);
v(30).html(_44.nOrders);
}else{
c.discount=0;
c._4(_44.errorCodes[0]);
v(28).addClass("hide");
}
c._i();
},_m:function(_45){
var c=this;
var _46=$.parseJSON(_45);
if(_46.success){
location.href=_46.url;
}else{
v(13).button("reset");
var _47=[];
$(".error-on").removeClass("error-on");
$.each(_46.errorCodes,function(_48,_49){
var _4a=_49.split(":");
var _4b=_4a[0];
var _4c=_4a[1];
if(_4c=="NoField"){
_47.push(_errorCodes[_4b]);
}else{
var el=$("#c"+_4c).parents(".form-group");
el.find(".error-notify").html(_errorCodes[_4b]);
el.addClass("error-on");
el.parents(".panel-collapse").collapse("show");
}
});
if(_47.length>0){
var msg=_errorCodes["SignupGeneral"]+_47.join("<br/>");
alertify.alert(msg);
}else{
alertify.alert(err("SignupMain"));
}
}
},_n:function(_4d,_4e){
var c=this;
if(_4e.error){
v(13).button("reset");
alertify.alert(_4e.error.message);
}else{
c._stripeToken=_4e["id"];
c._j();
}
},_o:function(_4f){
var c=this;
var _50=$.parseJSON(_4f);
if(_50.success){
v(17).collapse("show");
v(18).removeClass("error-on");
v(18).addClass("highlightMe");
$.each(_50.site,function(key,_51){
$("#c"+key).val(_51);
$("#c"+key).attr("disabled","disabled");
});
}else{
v(18).addClass("error-on");
v(18).removeClass("highlightMe");
}
}});
var Gateway=MyClass.extend({_controller:"auth",init:function(){
var c=this;
var rel=v(31).attr("rel");
var _52=$.parseJSON(rel);
c.options=_52;
c.sessionID=_52.sessionID;
switch(_52.p){
case "login":
c._q();
break;
case "confirm":
c._p();
break;
}
},_p:function(){
var c=this;
v(32).on("click",function(){
c.ajax("uzrsj",{hash:c.options.hash},c._r);
});
},_q:function(){
var c=this;
v(33).on("click",function(){
c.ajaxForm("zhric","formLogin",c._2);
return false;
});
v(34).on("click",function(){
c.ajaxForm("bgiz","nPassword",c._2);
return false;
});
v(35).on("click",function(){
location.href="/gateway/signup";
});
},_r:function(_53){
var c=this;
var _54=$.parseJSON(_53);
if(_54.success){
alertify.success("Email has been resent.");
}else{
alertify.error(err(_54.errorCodes[0]));
}
}});

