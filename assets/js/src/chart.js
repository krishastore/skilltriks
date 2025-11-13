import ApexCharts from 'apexcharts';


var options = {
	chart: {
		type: 'donut',
		height: 230
	},
	series: [3, 2, 1], // Not Started, In Progress, Completed
	labels: ['Not Started', 'In progress', 'Completed'],
	colors: ['#E8EBF2', '#436CFB', '#00D000'],
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
	}
};

var chart = new ApexCharts(document.querySelector('#chart'), options)
chart.render()