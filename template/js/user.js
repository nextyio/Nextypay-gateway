
function countInSecond(startTime,endTime) {
  var timeDiff = endTime - startTime; //in ms
  // strip the ms
  timeDiff /= 1000;

  // get seconds
  var seconds = Math.round(timeDiff);
  return seconds;
}

function call_ajax(startTime,reqId,timeout,interval){

	var seconds=countInSecond(startTime,new Date());
	console.log(seconds, reqId);
	if (seconds>timeout) {
		console.log("time out");
		return;
	}
	var paid="0";
	setTimeout(function(){
        $.post("ajax.php",
        {
            'reqId': reqId
        },
        function(data, status){
            console.log(reqId + "Data: " + data + "\nStatus: " + status);
            if ((status == 'success') && (data)) {window.location.replace(data);} else
            {
              call_ajax(startTime,reqId,timeout,interval);
            }
        });
	}, interval*1000);
}
