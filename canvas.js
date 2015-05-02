// default image settings
// align bottom of image to bottom of canvas
// align middle of image to middle of canvas
// align top of image to top of canvas
// add semi-transparent overlay
var defaultOptions = { 
    bottom: false,
    middle: false, 
    top: true, 
    fade: false 
};
// getContent helper for cross-domain
var bootstrap = "content";
// global content store
var savedData = {};
// image width always = 900px, store computed height for calcs
var imageY = 0;
// prefill canvas with "getting started" message
function canvasInit() {
	var canvas = document.getElementById("canvas");
	if (canvas.getContext) {
		var ctx = canvas.getContext("2d");
		ctx.fillStyle = '#444';
		ctx.font = '28px Arial';
		wrapText(ctx, "← Start by entering a jsonline.com url on the left.", 100, 25, 700, 50);
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
	draw(savedData.photo, savedData.credit, savedData.title, savedData.imageY, defaultOptions);
}
// redraw modified title
function updateText(newText) {
	clearCanvas();
	draw(savedData.photo, savedData.credit, newText, savedData.imageY);
}
// wrap text to fit inside bounds
function wrapText(ctx, text, x, y, maxWidth, lineHeight) {
	var words = text.split(' ');
	var line = '';

	for (var n = 0; n < words.length; n++) {
		var testLine = line + words[n] + ' ';
		var metrics = ctx.measureText(testLine);
		var testWidth = metrics.width;
		if (testWidth > maxWidth && n > 0) {
			ctx.fillText(line, x, y);
			line = words[n] + ' ';
			y += lineHeight;
		} else {
			line = testLine;
		}
	}
	ctx.fillText(line, x, y);
}
// cross-browser method to force download of of canvas as image
function dlCanvas() {
    var dl = document.getElementById('dl');
    var canvas = document.getElementById("canvas");
    var dc = canvas.toDataURL('image/png');
    /* Change MIME type to trick the browser to downlaod the file instead of displaying it */
    dc = dc.replace(/^data:image\/[^;]*/, 'data:application/octet-stream');
    /* In addition to <a>'s "download" attribute, you can define HTTP-style headers */
    dc = dc.replace(/^data:application\/octet-stream/, 'data:application/octet-stream;headers=Content-Disposition%3A%20attachment%3B%20filename=mjsShareCard.png');
    window.open(dc);
    //dl.href = dc;
};
// get content from mjs api
function getContent(url) {
	if (url.indexOf('.jsonline.com') > -1) {
		var id = url.match(/(?!-)\d+(?=\.html)/)[0];
		var apiUrl = 'http://m.jsonline.com/api/v1/?id=' + id + '&bootstrap=' + bootstrap;
		bootstrapContent(apiUrl, parseContent);
	} else {
		var newUrl = prompt('Something about that URL is incorrect. It must be a valid JSOnline.com link to an article or blog post. Double check and try again:');
		getContent(newUrl);
		document.getElementById('url').value = newUrl;
	}
}
// using bootstrap method, includes file with global variable: bootstrap
function bootstrapContent(url, callback) {
	var tag = document.createElement('script');
	tag.type = 'text/javascript';
	tag.src = url;
	tag.onreadystatechange = callback;
	tag.onload = callback;
	document.getElementsByTagName('head')[0].appendChild(tag);
}
// get content from new global object aka api
function parseContent() {
	var data = window[bootstrap].collection;
	data = data[0];
	var tempData = {};
	tempData.title = data.title.replace(/<\/?[^>]+(>|$)/g, " ").replace('&quot;', '"').replace('&amp;', '&') || "";
	tempData.photo = relayImageUrl(resizeImage(data.photoUrl));
	tempData.credit = data.photoCredit;
	tempData.imageY = imageY;
	savedData = tempData;
	fillContent(savedData);
}
// use clickability to get a correctly sized and scaled image
function resizeImage(imageUrl) {
	//resize image url - messy
	var original = imageUrl;
	var dims = original.match(/\/\d+\*\d+\//g); // get dimensions out of url
	if (dims) {
		var dim = dims[0];
		var trimmed = dim.replace(/\//g, ''); // strip slashes 
		var parts = trimmed.split('*');
		var x = parseInt(parts[0], 10); // width
		var y = parseInt(parts[1], 10); // height
		var ymod = parseInt((900 * y) / x); // scale height to match width of 900
		imageY = ymod;
		var final = '/' + 900 + '*' + ymod + '/'; // create new dims portion of url
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