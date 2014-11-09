var http = require('http');
//http://www.google.com/trends/fetchComponent?q=apple%20iphone,%20samsung%20galaxy&cid=TIMESERIES_GRAPH_0&export=3


var options = {
    host: "www.google.com",
    path: "/trends/fetchComponent?q=apple%20iphone,%20samsung%20galaxy&cid=TIMESERIES_GRAPH_0&export=3"
}


callback = function(response) {
  var str = '';
  console.log("Http code: " + response.statusCode);
  response.on('data', function (chunk) {
    str += chunk;
  });


  response.on('end', function () {
    console.log(str);
  });

}

http.get(options, callback)
    .on('error', function (e){
        console.log("Got error: " + e.message);
    })
    .end();
