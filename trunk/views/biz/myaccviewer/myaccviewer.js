
//js.begin.myaccviewer
	function menu_hover(elem)
	{
		document.getElementById(elem).style.backgroundColor = '#F0EDEE';
	}
	function menu_out(elem,color)
	{
		document.getElementById(elem).style.backgroundColor = color;
	}
	function isValidNumber(num){
		size=(num+"").length;
		if(!(size>0)) return false;
		for(i=0;i<size;i++)
			if( num.charAt(i)<'0' || num.charAt(i)>'9' )
				return false;
		return true;
	}
	function checkPaypal(){
		setTimeout("doCheckPaypal()",200);
	}
	function doCheckPaypal(){
		var amount=0.0;
		var handle=0.0;
		var user_amount=parseFloat(_eGetVal("paypal_user_amount"));
		if(!isNaN(user_amount) && isValidNumber(_eGetVal("paypal_user_amount"))){
			handle=(user_amount*0.039+0.3)/(1-0.039);
			amount=user_amount;
			_e("paypal_button").disabled=0;
			_eSetHTML("paypal_pay_msg","");
		}else{
			_e("paypal_button").disabled=1;
			_eSetHTML("paypal_pay_msg","Invalid");
		}
		document.forms['paypal_form'].elements['handling'].value=handle.toFixed(2);
		document.forms['paypal_form'].elements['amount'].value=amount.toFixed(2);
	}
	function checkValidWithdraw(){
		setTimeout("doCheckValidWithdraw()",200);
	}
	function doCheckValidWithdraw(){
		if(isValidNumber(_eGetVal("withdraw_amount"))){
			_e("withdraw_button").disabled=0;
			_eSetHTML("withdrawmessage","");
		}else{
			_e("withdraw_button").disabled=1;
			_eSetHTML("withdrawmessage","<font color='red'>Invalid Amount</font>");
		}
	}

//js.end.myaccviewer
