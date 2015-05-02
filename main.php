<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MJS Share Card Generator</title>
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
<body onload="canvasInit();">
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
                <!-- <a id="dl" download="mjsShareCard.png" href="#" onclick="dlCanvas();" > -->
                <button class="pure-button pure-button-primary download" onclick="dlCanvas();">Download</button>
                <!-- </a> -->
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
  <script src="canvas.js"></script>
</body>
</html>
