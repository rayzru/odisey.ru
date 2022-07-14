function Xhr(){
	try { return new XMLHttpRequest();						}catch(e){}
	try { return new ActiveXObject("Msxml3.XMLHTTP");		}catch(e){}
	try { return new ActiveXObject("Msxml2.XMLHTTP.6.0");	}catch(e){}
	try { return new ActiveXObject("Msxml2.XMLHTTP.3.0");	}catch(e){}
	try { return new ActiveXObject("Msxml2.XMLHTTP");		}catch(e){}
	try { return new ActiveXObject("Microsoft.XMLHTTP");	}catch(e){}
	return null;
}

var req = Xhr();
if (req != null) {
	var collection = {};
	collection.url = ("URL" in document) ? document.URL : null;
	collection.ref = ("referrer" in document) ? document.referrer : null;
	collection.ww = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
	collection.wh = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
	collection.sw = screen.width || screen.availWidth;
	collection.sh = screen.height || screen.availHeight;
	collection.ua = (window && window.navigator && window.navigator.userAgent) ? window.navigator.userAgent : null;
	if ("geolocation" in navigator) {
		/*navigator.geolocation.getCurrentPosition(function (position) {
			collection.lat = position.coords.latitude;
			collection.lng = position.coords.longitude;
		});*/
	}
	var postdata = 'd=' + window.btoa(JSON.stringify(collection));
	req.open("POST", '/stats', true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send(postdata);
}