<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MJS Share Card Generator</title>
  <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
  <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
  <!--[if lte IE 8]>
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-old-ie-min.css">
  <![endif]-->
  <!--[if gt IE 8]><!-->
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-min.css">
  <!--<![endif]-->
  <style>
  .left { padding: 10px; }
  .generate {margin-bottom:10px; width:100%;}
  .download { width: 100%; background: rgb(28, 184, 65); color:rgba(255,255,255,1); }
  .download.disabled { background-image: none; opacity: .4; cursor: not-allowed; box-shadow: none; }
  .pure-button-hover, .pure-button:hover, .pure-button:focus {background-image:none; color:rgba(255,255,255,1);}
  #textoverride { min-height: 125px; }
  .right { margin-top: 10px; text-align: center; }
  a, a:visited, a:hover { color: #fff; text-decoration: none; }
  </style>
</head>
<body onload="canvasInit();">
  <div class="pure-g">
  	<div class="pure-u-md-1-4">
      <div class="left">
        <form class="pure-form">
            <fieldset>
                <legend>1) Enter JSOnline.com URL</legend>
                <input type="text" id="url">
                <button onclick="getContent(document.getElementById('url').value); return false;" class="pure-button pure-button-primary">Get story data</button>
            </fieldset>
        </form>
        <form class="pure-form">
            <fieldset>
                <legend>2) Edit</legend>
                <textarea id="textoverride"></textarea>
                <button onclick="updateText(document.getElementById('textoverride').value); return false;" class="pure-button pure-button-primary">Update</button>
                <label for="checkbox-one" class="pure-checkbox">
                    <input id="checkbox-one" type="checkbox" value="" onchange="if (this.checked) { updateImage('fadeOn'); } else { updateImage('fadeOff'); };">
                    Darken Image
                </label>
                <label for="radio-one" class="pure-radio">
                    <input id="radio-one" type="radio" name="optionsRadios" value="top" checked onchange="updateImage('top');">
                    Top align image
                </label>
                <label for="radio-two" class="pure-radio">
                    <input id="radio-two" type="radio" name="optionsRadios" value="middle" onchange="updateImage('middle')">
                    Middle align image
                </label>
                <label for="radio-three" class="pure-radio">
                    <input id="radio-three" type="radio" name="optionsRadios" value="bottom" onchange="updateImage('bottom')">
                    Bottom align image
                </label>
            </fieldset>
        </form>
        <form class="pure-form">
            <fieldset>
                <legend>3) Download image</legend>
                <button class="pure-button pure-button-primary generate" onclick="prep(); return false;">Generate</button>
                <a id="dl" download="MJS-share-card.png" href="" class="pure-button download disabled">Download</a>
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
    var defaultOptions = {		// default image settings
		bottom: false,          // align bottom of image to bottom of canvas
		middle: false,          // align middle of image to middle of canvas
		top: true,              // align top of image to top of canvas
		fade: false             // add semi-transparent overlay
		};
	var bootstrap = "content";  // getContent helper for cross-domain
	var savedData = {};			// global content store
	var imageY = 0;				// image width always = 900px, store computed height for calcs
    var step = -1;
	//prefill canvas with "getting started" message
	function canvasInit() {
	  var canvas = document.getElementById("canvas");
      if (canvas.getContext) {
        var ctx = canvas.getContext("2d");
		ctx.fillStyle = '#444';
        ctx.font = '28px Arial';
        wrapText(ctx, "← Start by entering a jsonline.com url on the left.", 100, 25, 700, 50);
        step = 0;
	  }
	}
	// main drawing method
	// @params image url, photo credit text, headline text, global image height, options
	function draw(_image, _credit, _title, _imageY, _opts) {
	  // make sure there's a clean canvas
	  clearCanvas();
	  var canvas = document.getElementById("canvas");
      if (canvas.getContext) {
        var ctx = canvas.getContext("2d");

		if (_opts === undefined) {
			var _opts = defaultOptions;
		}

        var bgImageObj = new Image();
        bgImageObj.src = _image;
		// load image
        bgImageObj.onload = function() {
          if (_opts.bottom) {
			// align image: bottom
		    ctx.drawImage(bgImageObj, 0, (0 - (_imageY - 450)));
		  } else if (_opts.middle) {
			// align image: middle
		    ctx.drawImage(bgImageObj, 0, (0 - ((_imageY - 450) / 2)));
		  } else {
			// align image: top (default)
		    ctx.drawImage(bgImageObj, 0, 0);
		  }

		  if (_opts.fade) {
			// darken image
			ctx.fillStyle = "rgba(0, 0, 0, 0.2)";
		    ctx.fillRect(0, 0, canvas.width, canvas.height);
		  } else {
			// text shadow
            ctx.shadowColor = '#000';
            ctx.shadowOffsetX = 1;
            ctx.shadowOffsetY = 1;
            ctx.shadowBlur = 40;
		  }
		  ctx.fillStyle = '#fff';
          ctx.font = '48px Georgia';

		  // title
          wrapText(ctx, _title, 100, 310, 700, 50);

		  // photo credit
		  ctx.font = '14px Arial';
		  ctx.fillText('Photo by: ' + _credit, 8, 442);

		  //js logo
		  var jsImageObj = new Image();
		  jsImageObj.src = 'js_logo.png';
			jsImageObj.onload = function() {
			  ctx.drawImage(jsImageObj, 676, 424);
			};
		};
        step = 2;
      }
    }
	// clear canvas by drawing a white rectangle over entire canvas
	function clearCanvas() {
	    var canvas = document.getElementById("canvas");
        if (canvas.getContext) {
            var ctx = canvas.getContext("2d");
		    ctx.clearRect(0, 0, canvas.width, canvas.height);
	    }
	}
	// modify defaults and redraw canvas
	// toggle image position and fade overlay
	function updateImage(update) {
		if (update === "top") {
			defaultOptions.top = true;
			defaultOptions.middle = false;
			defaultOptions.bottom = false;
		} else if (update === "middle") {
			defaultOptions.top = false;
			defaultOptions.middle = true;
			defaultOptions.bottom = false;
		} else if (update === "bottom") {
			defaultOptions.top = false;
			defaultOptions.middle = false;
			defaultOptions.bottom = true;
		} else if (update === "fadeOn") {
			defaultOptions.fade = true;
		} else if (update === "fadeOff") {
			defaultOptions.fade = false;
		}
        document.getElementById('dl').className = "pure-button download disabled";
		draw(savedData.photo, savedData.credit, savedData.title, savedData.imageY, defaultOptions);
	}
	// redraw modified title
	function updateText(newText) {
	    clearCanvas();
        savedData.title = newText;
        document.getElementById('dl').className = "pure-button download disabled";
	    draw(savedData.photo, savedData.credit, savedData.title, savedData.imageY);
	}
	// wrap text to fit inside bounds
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
	// cross-browser method to force download of of canvas as image
    function prep() {
      if(step > 1) {
        var dl = document.getElementById('dl');
        var canvas = document.getElementById("canvas");
        var dc = canvas.toDataURL('image/png');
        /* Change MIME type to trick the browser to downlaod the file instead of displaying it */
        dc = dc.replace(/^data:image\/[^;]*/, 'data:application/octet-stream');
        /* In addition to <a>'s "download" attribute, you can define HTTP-style headers */
        dc = dc.replace(/^data:application\/octet-stream/, 'data:application/octet-stream;headers=Content-Disposition%3A%20attachment%3B%20filename=MJS-share-card.png');
	    dl.href = dc;
        dl.className = "pure-button download";
      } else {
        alert('Please start by entering a jsonline.com url, clicking "Get story data", and then you can click "Generate".');
      }
    };
	// get content from mjs api
	function getContent(url) {
	  if (url.indexOf('.jsonline.com') > -1) {
	      var id = url.match(/(?!-)\d+(?=\.html)/)[0];
	      var apiUrl = 'http://m.jsonline.com/api/v1/?id=' + id + '&bootstrap=' + bootstrap;
	      bootstrapContent(apiUrl, parseContent);
          step = 1;
	  } else {
		  var newUrl = prompt('Something about that URL is incorrect. It must be a valid JSOnline.com link to an article or blog post. Double check and try again:');
		  getContent(newUrl);
		  document.getElementById('url').value = newUrl;
	  }
	}
	// using bootstrap method, includes file with global variable: bootstrap
	function bootstrapContent(url, callback) {
	  var tag  = document.createElement('script');
	  tag.type = 'text/javascript';
	  tag.src = url;
	  tag.onreadystatechange = callback;
	  tag.onload = callback;
	  document.getElementsByTagName('head')[0].appendChild(tag);
	}
	// get content from new global object aka api
	function parseContent(){
	  var data = window[bootstrap].collection;
	  data = data[0];
	  var tempData = {};
	  if (data.photoUrl && data.photoUrl !== "") {
        tempData.title = data.title.replace(/<\/?[^>]+(>|$)/g, " ").replace('&quot;','"').replace('&amp;','&') || "";
        tempData.photo = relayImageUrl(resizeImage(data.photoUrl));
	    tempData.credit = data.photoCredit;
	    tempData.imageY = imageY;
	    savedData = tempData;
	    fillContent(savedData);
      } else {
          alert('The selected story does not have a lead story image. Please select a story that does have a lead image.');
    }

	}
	// use clickability to get a correctly sized and scaled image
	function resizeImage(imageUrl) {
      //resize image url - messy
      var original = imageUrl;
      var dims = original.match(/\/\d+\*\d+\//g);  // get dimensions out of url
      if (dims) {
        var dim = dims[0];
        var trimmed = dim.replace(/\//g,'');       // strip slashes
        var parts = trimmed.split('*');
        var x = parseInt(parts[0],10);			   // width
        var y = parseInt(parts[1],10);			   // height
        var ymod = parseInt((900 * y) / x);		   // scale height to match width of 900
		imageY = ymod;
        var final = '/' + 900 + '*' + ymod + '/';  // create new dims portion of url
        return original.replace(/\/\d+\*\d+\//g, final) || false;
      } else {
        return false;
      }
      //end messy
    }
	// in order to export canvas, it can't be tainted, so we need the images on the same domain
	// bouncing the image through the same server solves this problem
	function relayImageUrl(imageUrl) {
		var urlPieces = imageUrl.split('.com');
		var relayUrl = '/image?url=' + urlPieces[1];
		return relayUrl;
	}
	// got the content, fill the screen
	function fillContent(dataObj) {
		document.getElementById('textoverride').value = dataObj.title;
		draw(dataObj.photo, dataObj.credit, dataObj.title, imageY);
	}
</script>
</body>
</html>
