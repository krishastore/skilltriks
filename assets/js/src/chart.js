import ApexCharts from 'apexcharts';

var notStarted = parseInt(StlmsChartObj.courseNotStarted);
var inProgress = parseInt(StlmsChartObj.courseInProgress);
var completed = parseInt(StlmsChartObj.courseCompleted);

var allZero = (notStarted === 0 && inProgress === 0 && completed === 0);

var options = {
	chart: {
		type: 'donut',
		height: 230
	},
	series: allZero ? [1] : [notStarted, inProgress, completed], // Show dummy data if all zero.
	labels: allZero ? ['Not Started'] : ['Not Started', 'In progress', 'Completed'],
	colors: allZero ? ['#E8EBF2'] : ['#E8EBF2', '#436CFB', '#00D000'],
	legend: {
		show: false,
		position: 'bottom',
		labels: {
			colors: '#333',
			useSeriesColors: false
		},
		formatter: function(seriesName, opts) {
			return seriesName + " - " + opts.w.globals.series[opts.seriesIndex] + " Courses";
		}
	},
	plotOptions: {
		pie: {
			startAngle: 0,
			endAngle: 360,
			donut: {
				size: '55%'
			}
		}
	},
	dataLabels: {
		enabled: false
	},
	states: {
		hover: {
			filter: {
				type: allZero ? 'none' : 'lighten',
				value: 0.15
			}
		},
		active: {
			filter: {
				type: allZero ? 'none' : 'darken',
				value: 0.35
			}
		}
	},
	tooltip: {
		enabled: !allZero,
		y: {
			formatter: function(value) {
				return value + " Courses";
			}
		}
	}
};

var chart = new ApexCharts(document.querySelector('#chart'), options);
chart.render();