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
    var seed = Math.floor(Math.random() * 1000000)

    var validWallet = isAddress(wallet);
    var validInputs = validWallet;

    if (validInputs) {
        var seed = Math.floor(Math.random() * 1000000)
        var str =  (wallet + secretKey + seed).toLowerCase();
        var hash = md5(str);
        //console.log(str, hash)
        $('#hash').val(hash);
        $('#seed').val(seed);
        $('form#requestForm').submit()
        return true
    }

    $('#walletError').show();
    return false;
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

