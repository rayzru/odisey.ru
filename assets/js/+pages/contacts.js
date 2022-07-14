var onSubmit = function (e) {
	e.preventDefault();
	$('#feedback').trigger('submit');
};

function initialize() {

	var rndLatLng = new google.maps.LatLng(47.238166, 39.599891);
	var stvLatLng = new google.maps.LatLng(45.053523, 42.006761);

	var map = new google.maps.Map(document.getElementById('map-canvas'), {center: rndLatLng, zoom: 15});

	var markerRND = new google.maps.Marker({map: map, position: rndLatLng});
	var markerSTV = new google.maps.Marker({map: map, position: stvLatLng});

	var infowindowRND = new google.maps.InfoWindow({content: '<div>Ростов-на-Дону</div> ул. Доватора, д.148'});
	var infowindowSTV = new google.maps.InfoWindow({content: '<div>Ставрополь</div> ул. Заводская, д. 11'});

	google.maps.event.addListener(markerSTV, 'click', function () {
		infowindowSTV.open(map, this)
	});
	google.maps.event.addListener(markerRND, 'click', function () {
		infowindowRND.open(map, this)
	});

	jQuery('.RNDLink').click(function () {
		map.setCenter(rndLatLng);
	});
	jQuery('.STVLink').click(function () {
		map.setCenter(stvLatLng);
	});
}

google.maps.event.addDomListener(window, 'load', initialize);

function disposePopover(el) {
	setTimeout(function () {
		el.popover('dispose')
	}, 10000);
}

jQuery(function ($) {
	var submit = $('#sendButton');
	$('#feedback').submit(function (event) {
		event.preventDefault();
		$('#feedback fieldset').prop('disabled', false);
		var form = this;
		$.ajax({
			type: 'POST',
			url: form.action,
			data: $(form).serialize(),
			dataType: 'json',
			complete: function(){
				grecaptcha.reset();
				$('#feedback fieldset').prop('disabled', false);
			},
			success: function (response) {
				if (response.success) {
					submit.popover({
						placement: 'left',
						container: 'body',
						content: '<h5>Сообщение отправлено</h5>Ваще сообщение успешно отправлено<br/>' +
						'и будет вскоре прочитано нашими специалистами.<br/>' +
						'Ждите скорейшего ответа.<br/>' +
						'Большое спасибо!',
						html: true
					});
					submit.popover('show');
					disposePopover(submit);
					form.reset();
				} else {
					var list = $('<ul/>');
					$.each(response.errors, function (i) {
						$('<li/>').text(response.errors[i]).appendTo(list);
					});
					$('<h5>Сообщение не отправлено</h5>Обратите внимание<br/>').prependTo(list);
					submit.popover({placement: 'left', content: list.html(), html: true});
					submit.popover('show');
					disposePopover(submit);
				}
			}
		});
	});
});
