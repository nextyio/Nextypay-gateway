function countInSecond(startTime,endTime) {
  var timeDiff = endTime - startTime; //in ms
  // strip the ms
  timeDiff /= 1000;

  // get seconds
  var seconds = Math.round(timeDiff);
  return seconds;
}

function addMerchant(wallet, merchantName, url, email){
  console.log('adding new Merchant');
  var wallet = $('#wallet').val();
  var merchantName = $('#merchantName').val();
  var url = $('#url').val();
  var email = $('#email').val();
  var isMobile = ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) );
  //alert('email='+ email);
  //alert(isMobile)
  
  $.post("ajaxMerchant.php",
  {
    'service' : 'addMerchant',
    'wallet': wallet,
    'merchantName': merchantName,
    'url' : url,
    'email' : email,
    'isMobile' : isMobile
  },
  function(data, status){
      //alert("Data: " + data + "\nStatus: " + status);
      console.log("Data: " + data + "\nStatus: " + status);
      if (data && status) {
          //alert ($QRUrl)
          //console.log(data);
          $("#infoText").text('QR Scan');
          $("#QRImg").attr("src",data);
          $("#androidAppDirect").attr("href",data);
          if (!isMobile) {
                $("#QRImg").show(); 
                $("#androidAppDirect").hide();
                $("#infoText").show();
            } else {
                $("#androidAppDirect").show();
                $("#QRImg").hide();
                $("#infoText").hide();
            };
          $("#androidApp").show();
          $("#iosApp").show();
          requestStatus();
      } else {
        //alert ($QRUrl)
        $("#infoText").text('This wallet address is already registered!');
        $("#QRImg").hide();
        $("#androidAppDirect").hide();
        $("#androidApp").hide();
        $("#iosApp").hide();
      }
  })
  
}

function statusWaiting(startTime,wallet,timeout,interval){
    if (!($("#popup").data('bs.modal') || {}).isShown)   return; 
	var seconds=countInSecond(startTime,new Date());
	//console.log(seconds, wallet);
	if (seconds>timeout) {
		console.log("time out");
		return;
    }
    
	setTimeout(function(){
        $.post("ajaxMerchant.php",
        {
            'service' : 'checkStatus',
            'wallet': wallet
        },
        function(data, status){
            console.log(data);
            //alert("Data: " + data + "\nStatus: " + status);
            if ((status == 'success') && (data != 'Pending')) {
                var successMsg = 'Successful comfirmed!';
                $("#infoText").text(successMsg);
                $("#pkeyText").text('Key: ' + data);
                $("#QRImg").hide();
                $("#androidApp").hide();
                $("#iosApp").hide();
            } else
            {
                statusWaiting(startTime,wallet,timeout,interval);
            }
        });
	}, interval*1000);
}

function requestStatus() {
    var wallet = $('#wallet').val();
    statusWaiting(new Date(), wallet, 600, 3);
}
