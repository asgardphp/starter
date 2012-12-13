gmapscripts = [];

function initialize() {
	geocoder = new google.maps.Geocoder();
	
	for(i in gmapscripts) {
		gmapscripts[i]();
	}
}

function loadScript() {
	var key = "AIzaSyCrPR4l6noIDvLvN__u-euZCAzBYIR423E";
	var script = document.createElement("script");
	script.type = "text/javascript";
	script.src = "http://maps.googleapis.com/maps/api/js?key="+key+"&sensor=false&callback=initialize";
	document.body.appendChild(script);
}
window.onload = loadScript;

function addMap(id) {
	var options = {
		zoom: 1,
		//~ zoom: 10,
		center: new google.maps.LatLng(2.21, -48.51),
		//~ center: results[0].geometry.location,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	
	return new google.maps.Map(document.getElementById(id), options);
}

function addMarker(address, title, position) {
	//~ geocoder.geocode( { 'address': address}, function(results, status) {
		//~ if (status == google.maps.GeocoderStatus.OK) {
			var marker = new google.maps.Marker({
				//~ var map = addMap(id);
				
				//~ position: results[0].geometry.location,
				position: position,
				map: map,
				title:title,
			});
			
			google.maps.event.addListener(marker, 'click', function() {
				popin(id);
			});
		//~ }
	//~ });
}