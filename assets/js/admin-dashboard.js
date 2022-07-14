function distribureData(chart, data) {
	chart.data.datasets[0].data = [];
	chart.data.datasets[1].data = [];
	$.each(data, function (index, el) {
		chart.data.datasets[0].data.push({
			t: moment(el.dt),
			y: el.sessions
		});
		chart.data.datasets[1].data.push({
			t: moment(el.dt),
			y: el.hits
		});
	});
	chart.options.scales.xAxes[0].time.max = new Date();
	chart.update();
}

jQuery(function ($) {

	var ctx = document.getElementById('liveStats').getContext('2d');
	var liveChart = new Chart(ctx, {
		type: 'bar',
		data: {
			datasets: [
				{
					label: 'Уникальных посетителей',
					backgroundColor: '#738cae'
				}, {
					label: 'Хитов',
					backgroundColor: '#d6d6d6'
				}
			]
		},
		options: {
			maintainAspectRatio: false,
			barValueSpacing: 0,
			responsive: true,
			legend: {
				display: false
			},
			title: {
				display: false
			},
			scales: {
				xAxes: [{
					stacked: true,
					type: 'time',
					time: {
						min: moment().subtract(30, 'minute'),
						max: moment(),
						tooltipFormat: 'DD MMMM, H:mm',
						unit: 'minute',
						displayFormats: {
							minute: 'H:mm',
							hour: 'H:mm:ss',
							day: 'MMM D'
						}
					},
					barThickness: 10
				}],
				yAxes: [{
					stacked: true,
					type: 'linear',
					ticks: {
						stepSize: 1,
						min: 0,
						beginAtZero: true
					}
				}]
			}
		}
	});

	function updateLiveChart(data) {
		liveChart.data.datasets[0].data = [];
		liveChart.data.datasets[1].data = [];
		$.each(data, function (index, el) {
			liveChart.data.datasets[0].data.push({
				t: moment(el.dt),
				y: el.sessions
			});
			liveChart.data.datasets[1].data.push({
				t: moment(el.dt),
				y: el.hits
			});
		});
		liveChart.options.scales.xAxes[0].time.min = moment(new Date()).subtract(30, 'minute');
		liveChart.options.scales.xAxes[0].time.max = moment(new Date());
		liveChart.update();
	}

	function requestLiveStats() {
		$.ajax({
			url: '/stats/live',
			success: function (response) {
				updateLiveChart(response);
			}
		});
	}

	function updateLiveSessions(data) {
		$('#sessoinCount').text(data);
	}

	function requestActiveSessions() {
		$.ajax({
			url: '/stats/sessions',
			success: function (response) {
				updateLiveSessions(response);
			}
		})
	}

	setInterval(requestLiveStats, 3000);
	requestLiveStats();
	setInterval(requestActiveSessions, 1000 * 60);
	requestActiveSessions();

	$('a[data-action=remove-flag]').on('click', function (e) {
		e.preventDefault();
		if (confirm('Убрать из данного списка?')) {
			var el = $(this);
			var data = el.data();
			$.post('/admin/catalog/item/' + data['id'] + '/flag/unset/' + data['flag'])
				.done(function (response) {
					console.log(response.hasOwnProperty('status') && response.status == true);
					if (response.hasOwnProperty('status') && response.status == true) {
						el.closest('tr').remove();
					}
				});
		}
	});

	var defaultStats = {
		type: 'line',
		data: {
			datasets: [
				{
					label: 'Уникальных посетителей',
					backgroundColor: '#4665ae'
				}, {
					label: 'Хитов',
					backgroundColor: '#a9cffb'
				}
			]
		},
		options: {
			maintainAspectRatio: false,
			responsive: true,
			legend: {display: false},
			title: {display: false},
			scales: {
				xAxes: [{
					type: 'time',
					time: {
						unit: 'hour',
						tooltipFormat: 'DD MMMM, H:mm',
						displayFormats: {minute: 'H:mm:ss', hour: 'H:mm', day: 'D M', week: 'D MMM', month: 'D MMM, Y'}
					}
				}],
				yAxes: [{
					type: 'linear',
					ticks: {min: 0, beginAtZero: true}
				}]
			}
		}
	};

	var statsDay = new Chart(document.getElementById('statsDay').getContext('2d'), $.extend(true, {}, defaultStats));

	$.ajax({
		url: '/stats/day',
		success: function (response) {
			statsDay.options.scales.xAxes[0].time.unit = 'hour';
			statsDay.options.scales.xAxes[0].time.min = moment(new Date()).startOf('day');
			distribureData(statsDay, response);
		}
	});

	var statsWeek = new Chart(document.getElementById('statsWeek').getContext('2d'), $.extend(true, {}, defaultStats));
	$.ajax({
		url: '/stats/week',
		success: function (response) {
			statsWeek.options.scales.xAxes[0].time.unit = 'day';
			statsWeek.options.scales.xAxes[0].time.tooltipFormat = 'DD MMMM';
			statsWeek.options.scales.xAxes[0].time.min = moment(new Date()).subtract(1, 'week').startOf('day');
			distribureData(statsWeek, response);
		}
	});

	var statsMonth = new Chart(document.getElementById('statsMonth').getContext('2d'), $.extend(true, {}, defaultStats));
	$.ajax({
		url: '/stats/month',
		success: function (response) {
			statsMonth.options.scales.xAxes[0].time.unit = 'day';
			statsMonth.options.scales.xAxes[0].time.tooltipFormat = 'DD MMMM';
			statsMonth.options.scales.xAxes[0].time.min = moment(new Date()).subtract(1, 'month').startOf('day');
			distribureData(statsMonth, response);
		}
	});
});
