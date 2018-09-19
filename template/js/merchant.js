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
  //alert('email='+ email);
  
  $.post("ajaxMerchant.php",
  {
    'service' : 'addMerchant',
    'wallet': wallet,
    'merchantName': merchantName,
    'url' : url,
    'email' : email
  },
  function(data, status){
      //alert("Data: " + data + "\nStatus: " + status);
      if (data && status) {
          $QRUrl = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" + data + "&choe=UTF-8";
          //alert ($QRUrl)
          $("#infoText").text('QR Scan');
          $("#QRImg").attr("src","https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" + data + 
          "&choe=UTF-8");
          $("#QRImg").show();
          $("#androidApp").show();
          $("#iosApp").show();
          requestStatus();
      } else {
        $QRUrl = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" + data + "&choe=UTF-8";
        //alert ($QRUrl)
        $("#infoText").text('This wallet address is already registered!');
        $("#QRImg").hide();
        $("#androidApp").hide();
        $("#iosApp").hide();
      }
  })
  
}

function statusWaiting(startTime,wallet,timeout,interval){
    if (!($("#popup").data('bs.modal') || {}).isShown)   return; 
	var seconds=countInSecond(startTime,new Date());
	console.log(seconds, wallet);
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
            //alert("Data: " + data + "\nStatus: " + status);
            if ((status == 'success') && (data == 'Comfirmed')) {
                //alert(data);
                $("#infoText").text('Successful comfirmed!');
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
