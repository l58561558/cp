<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:53:"D:\phpStudy\WWW\cp/application/home\view\pay\pay.html";i:1545183331;}*/ ?>
<html>
<head>
<form style="display: none" id='formpay' name='formpay' method='post' action='http://pay.liweiqiguan.com/Pay_Index.html'>
	<input name='pay_amount' id='pay_amount' type='text' value="" />
	<input name='pay_applydate' id='pay_applydate' type='text' value="" />
	<input name='pay_attach' id='pay_attach' type='text' value=""/>
	<input name='pay_bankcode' id='pay_bankcode' type='text' value=""/>
	<input name='pay_callbackurl' id='pay_callbackurl' type='text' value=""/>
	<input name='pay_md5sign' id='pay_md5sign' type='text' value=""/>
	<input name='pay_memberid' id='pay_memberid' type='text' value=""/>
	<input name='pay_notifyurl' id='pay_notifyurl' type='text' value=""/>
	<input name='pay_orderid' id='pay_orderid' type='text' value=""/>
	<input name='pay_productname' id='pay_productname' type='text' value=""/>
	<input name='user_name' id='user_name' type='text' value=""/>
	<input type='submit' id='submitdemo1'>
</form>
<script src="__PLUGINS__/jquery/js/jquery-2.2.3.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
	pay();
})
function pay()
{
	var data = GetRequest();
	var user_id = data.user_id;
	var price = data.price;
	$.ajax({
		url:"<?php echo url('pay/set_pay'); ?>",
		data:{user_id:user_id,price:price},
		type:'post',
		success:function(res){
			var dataa = JSON.parse(res);
			$.each(dataa.data,function(i, item){
			    $("#pay_amount").val(dataa.data.pay_amount);
			    $("#pay_applydate").val(dataa.data.pay_applydate);
			    $("#pay_attach").val(dataa.data.pay_attach);
			    $("#pay_bankcode").val(dataa.data.pay_bankcode);
			    $("#pay_callbackurl").val(dataa.data.pay_callbackurl);
			    $("#pay_md5sign").val(dataa.data.pay_md5sign);
			    $("#pay_memberid").val(dataa.data.pay_memberid);
			    $("#pay_notifyurl").val(dataa.data.pay_notifyurl);
			    $("#pay_orderid").val(dataa.data.pay_orderid);
			    $("#pay_productname").val(dataa.data.pay_productname);
			    $("#user_name").val(dataa.data.user_name);
			});
			$("#formpay").submit();
		}
	})
}

function GetRequest() {  
   	var url = location.search; //获取url中"?"符后的字串  
   	var theRequest = new Object();  
   	if (url.indexOf("?") != -1) {  
      	var str = url.substr(1);  
      	strs = str.split("&");  
      	for(var i = 0; i < strs.length; i ++) {  
         	theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);  
      	}  
   	}  
   	return theRequest;  
}  
</script>
</head>
<body>
