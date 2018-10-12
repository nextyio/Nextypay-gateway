
function validatePhone(txtPhone) {
    var filter = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
    if (filter.test(txtPhone)) {
        return true;
    }
    else {
        return false;
    }
}

function isUrlValid(url) {
    return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
}

///////////////////////////////////////////////////////////////////////

/**
 * Checks if the given string is an address
 *
 * @method isAddress
 * @param {String} address the given HEX adress
 * @return {Boolean}
*/
function isAddress(address) {
    if (!/^(0x)?[0-9a-f]{40}$/i.test(address)) {
        // check if it has the basic requirements of an address
        return false;
    } else if (/^(0x)?[0-9a-f]{40}$/.test(address) || /^(0x)?[0-9A-F]{40}$/.test(address)) {
        // If it's all small caps or all all caps, return true
        return true;
    } else {
        // Otherwise check each case
        return isChecksumAddress(address);
    }
};

/**
 * Checks if the given string is a checksummed address
 *
 * @method isChecksumAddress
 * @param {String} address the given HEX adress
 * @return {Boolean}
*/
var isChecksumAddress = function (address) {
    // Check each case
    address = address.replace('0x','');
    var addressHash = sha3(address.toLowerCase());
    for (var i = 0; i < 40; i++ ) {
        // the nth letter should be uppercase if the nth digit of casemap is 1
        if ((parseInt(addressHash[i], 16) > 7 && address[i].toUpperCase() !== address[i]) || (parseInt(addressHash[i], 16) <= 7 && address[i].toLowerCase() !== address[i])) {
            return false;
        }
    }
    return true;
};
//////////////////////////////////////////
function countInSecond(startTime,endTime) {
  var timeDiff = endTime - startTime; //in ms
  // strip the ms
  timeDiff /= 1000;

  // get seconds
  var seconds = Math.round(timeDiff);
  return seconds;
}

function validateEmail(email) {
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email))
    {
      return (true)
    }
      //alert("You have entered an invalid email address!")
      return (false)
}

function addMerchant(wallet, merchantName, url, email){
  console.log('adding new Merchant');
  var wallet = $('#wallet').val();
  var merchantName = $('#merchantName').val();
  var url = $('#url').val();
  var email = $('#email').val();
  var phone = $('#phone').val();

  var validEmail = validateEmail(email);
  var validWallet = isAddress(wallet);
  var validUrl = isUrlValid(url);
  var validPhone = validatePhone(phone);

  var validInputs = ((validEmail) && (validWallet) && (validUrl) && (validPhone))
  var isMobile = ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) );

  validEmail ? $('#emailError').hide() : $('#emailError').show();
  validWallet ? $('#walletError').hide() : $('#walletError').show();
  validUrl ? $('#urlError').hide() : $('#urlError').show();
  validPhone ? $('#phoneError').hide() : $('#phoneError').show();

  if (validInputs)
  {
    $('#popup').modal('toggle')
    $("#request").html('http://' + window.location.host + '/nextypay-gateway/request.php');
    $("#posRequest").html('http://' + window.location.host + '/nextypay-gateway/posRequest.php (POS)');
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
                $("#pkeyText").text(data);
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
