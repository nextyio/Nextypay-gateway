
//////////////////////////////////////////
function countInSecond(startTime,endTime) {
  var timeDiff = endTime - startTime; //in ms
  // strip the ms
  timeDiff /= 1000;

  // get seconds
  var seconds = Math.round(timeDiff);
  return seconds;
}

function keyRequest(){
    $('#inputError').hide();
    $('#successNoti').hide();
  var wallet = $('#wallet').val();
  var secretKey = $('#secretKey').val();
  var sig = secretKey;

  var validWallet = isAddress(wallet);
  var validInputs = validWallet;

  validWallet ? $('#walletError').hide() : $('#walletError').show();

return validInputs;
}

function isSuccess(apiKey) {
    if (apiKey == '-1') return
    if (apiKey == '0') {
        $('#inputError').show();
        $('#successNoti').hide();
    } else {
        $('#inputError').hide();
        $('#successNoti').show();
        $('#newApiKey').text(apiKey);
    }
}
