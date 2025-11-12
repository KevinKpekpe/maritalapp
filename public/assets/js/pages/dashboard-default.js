'use strict';
document.addEventListener('DOMContentLoaded', function () {
  setTimeout(function () {
    floatchart();
  }, 500);
});

function floatchart() {
  (function () {
    // Données par défaut si window.chartData n'existe pas
    var weeklyData = window.chartData && window.chartData.weekly ? window.chartData.weekly.data : [0, 0, 0, 0, 0, 0, 0];
    var weeklyLabels = window.chartData && window.chartData.weekly ? window.chartData.weekly.labels : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    var monthlyData = window.chartData && window.chartData.monthly ? window.chartData.monthly.data : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    var monthlyLabels = window.chartData && window.chartData.monthly ? window.chartData.monthly.labels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    // Chart for Weekly RSVP Confirmations Trend
    var options = {
      chart: {
        height: 450,
        type: 'area',
        toolbar: { show: false }
      },
      dataLabels: { enabled: false },
      colors: ['#1890ff', '#13c2c2'],
      series: [{
        name: 'Confirmations',
        data: weeklyData
      }],
      stroke: { curve: 'smooth', width: 2 },
      xaxis: { categories: weeklyLabels }
    };
    var chart = new ApexCharts(document.querySelector('#visitor-chart'), options);
    chart.render();

    // Chart for Monthly RSVP Confirmations Trend
    var options1 = {
      chart: {
        height: 450,
        type: 'area',
        toolbar: { show: false }
      },
      dataLabels: { enabled: false },
      colors: ['#1890ff', '#13c2c2'],
      series: [{
        name: 'Confirmations',
        data: monthlyData
      }],
      stroke: { curve: 'smooth', width: 2 },
      xaxis: { categories: monthlyLabels }
    };
    var chart1 = new ApexCharts(document.querySelector('#visitor-chart-1'), options1);
    chart1.render();
  })();
}
