
//js.begin.myaccviewer
	function menu_hover(elem)
	{
		document.getElementById(elem).style.backgroundColor = '#F0EDEE';
	}
	function menu_out(elem,color)
	{
		document.getElementById(elem).style.backgroundColor = color;
	}
	function checkPaypal(){
		setTimeout("doCheckPaypal()",200);
	}
	function doCheckPaypal(){
		var amount=0.0;
		var handle=0.0;
		var user_amount=parseFloat(document.getElementById("paypal_user_amount").value);
		if(!isNaN(user_amount)){
			handle=(user_amount*0.039+0.3)/(1-0.039);
			amount=user_amount;
			document.getElementById("paypal_button").disabled=0;
		}else{
			document.getElementById("paypal_button").disabled=1;
		}
		document.forms['paypal_form'].elements['handling'].value=handle.toFixed(2);
		document.forms['paypal_form'].elements['amount'].value=amount.toFixed(2);
	}

//js.end.myaccviewer
