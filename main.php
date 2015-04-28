<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Journal Sentinel Share Card Generator</title>
  <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
  <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
  <!--[if lte IE 8]>
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-old-ie-min.css">
  <![endif]-->
  <!--[if gt IE 8]><!-->
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-min.css">
  <!--<![endif]-->
  <style>
  .left { padding:10px; }
  .download { width:100%; }
  #textoverride { min-height:125px; }
  .right { margin-top:10px; text-align: center; }
  </style>
</head>
<body>
  <div class="pure-g">
  	<div class="pure-u-md-1-4">
      <div class="left">
        <form class="pure-form">
            <fieldset>
                <legend>1) Enter JSOnline.com URL</legend>
                <input type="text" id="url">
                <button onclick="getContent(document.getElementById('url').value); return false;" class="pure-button pure-button-primary">Go</button>
            </fieldset>
        </form>
        <form class="pure-form">
            <fieldset>
                <legend>2) Edit text</legend>
                <textarea id="textoverride"></textarea>
                <button onclick="updateText(document.getElementById('textoverride').value); return false;" class="pure-button pure-button-primary">Update</button>
            </fieldset>
        </form>
        <form class="pure-form">
            <fieldset>
                <legend>3) Download image</legend>
                <a id="dl" download="Canvas.png" href="#" onclick="dlCanvas();"><button class="pure-button pure-button-primary download">Download</button></a>
            </fieldset>
        </form>
      </div>
  	</div>
    <div class="pure-u-md-3-4">
      <div class="right">
        <canvas id="canvas" width="900" height="450">
          This browser does not support canvas elements, try a recent version of Chrome.
        </canvas>
      </div>
    </div>
  </div>
  <script type="application/javascript">
    function draw(_image, _credit, _title) {
      var canvas = document.getElementById("canvas");
      if (canvas.getContext) {
        var ctx = canvas.getContext("2d");

        var bgImageObj = new Image();
        bgImageObj.src = _image;

        bgImageObj.onload = function() {
          ctx.drawImage(bgImageObj, 0, 0);

          ctx.shadowColor = '#000';
          ctx.shadowOffsetX = 1;
          ctx.shadowOffsetY = 1;
          ctx.shadowBlur = 40;
          ctx.fillStyle = '#fff';
          ctx.font = '48px Georgia';
          wrapText(ctx, _title, 100, 350, 700, 50);
		  
		  ctx.fillText('Photo by: ' + _credit, (900 - ctx.measureText('Photo by: ' + _credit) - 10), 760);
		  
        };
      }
    }
	function updateText(newText) {
	  var canvas = document.getElementById("canvas");
      if (canvas.getContext) {
        var ctx = canvas.getContext("2d");
		ctx.clearRect(0, 0, canvas.width, canvas.height);
	  }
	  draw(savedData.photo, savedData.credit, newText);
	}
    function wrapText(ctx, text, x, y, maxWidth, lineHeight) {
        var words = text.split(' ');
        var line = '';

        for(var n = 0; n < words.length; n++) {
          var testLine = line + words[n] + ' ';
          var metrics = ctx.measureText(testLine);
          var testWidth = metrics.width;
          if (testWidth > maxWidth && n > 0) {
            ctx.fillText(line, x, y);
            line = words[n] + ' ';
            y += lineHeight;
          }
          else {
            line = testLine;
          }
        }
        ctx.fillText(line, x, y);
    }
    function dlCanvas() {
      var dl = document.getElementById('dl');
      var canvas = document.getElementById("canvas");
      var dc = canvas.toDataURL('image/png');

      /* Change MIME type to trick the browser to downlaod the file instead of displaying it */
      dc = dc.replace(/^data:image\/[^;]*/, 'data:application/octet-stream');

      /* In addition to <a>'s "download" attribute, you can define HTTP-style headers */
      dc = dc.replace(/^data:application\/octet-stream/, 'data:application/octet-stream;headers=Content-Disposition%3A%20attachment%3B%20filename=Canvas.png');

      dl.href = dc;
    };
	
	// getting content
	var bootstrap = "content";
	var savedData = {};
	function getContent(url) {
	  var id = url.match(/(?!-)\d+(?=\.html)/)[0];
	  var apiUrl = 'http://m.jsonline.com/api/v1/?id=' + id + '&bootstrap=' + bootstrap;
	  bootstrapContent(apiUrl, parseContent);
	}
	function bootstrapContent(url, callback) {
	  var tag  = document.createElement('script');
	  tag.type = 'text/javascript';
	  tag.src = url;
	  tag.onreadystatechange = callback;
	  tag.onload = callback;
	  document.getElementsByTagName('head')[0].appendChild(tag);
	}
	function parseContent(){
	  var data = window[bootstrap].collection;
	  data = data[0];
	  var tempData = {};
	  tempData.title = data.title.replace(/<\/?[^>]+(>|$)/g, " ").replace('&quot;','"').replace('&amp;','&') || "";
	  tempData.photo = relayImageUrl(resizeImage(data.photoUrl));
	  tempData.credit = data.photoCredit;
	  savedData = tempData;
	  fillContent(savedData);
	}
	// helpers
	function resizeImage(imageUrl) {
      //resize image url - messy
      var original = imageUrl;
      var dims = original.match(/\/\d+\*\d+\//g);
      if (dims) {
        var dim = dims[0];
        var trimmed = dim.replace(/\//g,'');
        var parts = trimmed.split('*');
        var x = parseInt(parts[0],10);
        var y = parseInt(parts[1],10);
        var ymod = parseInt((900 * y) / x);
        var final = '/' + 900 + '*' + ymod + '/';
        return original.replace(/\/\d+\*\d+\//g, final) || false;
      } else {
        return false;
      }
      //end messy
    }
	function relayImageUrl(imageUrl) {
		var urlPieces = imageUrl.split('.com');
		var relayUrl = '/image?url=' + urlPieces[1];
		return relayUrl;
	}
	function fillContent(dataObj) {
		document.getElementById('textoverride').value = dataObj.title;
		draw(dataObj.photo, dataObj.credit, dataObj.title);
	}
</script>
</body>
</html>
