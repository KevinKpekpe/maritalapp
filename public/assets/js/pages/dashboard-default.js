'use strict';
document.addEventListener('DOMContentLoaded', function () {
  setTimeout(function () {
    floatchart();
  }, 500);
});

function floatchart() {
  (function () {
    // Chart for Weekly Prelevements Trend
    var options = {
      chart: {
        height: 450,
        type: 'area',
        toolbar: { show: false }
      },
      dataLabels: { enabled: false },
      colors: ['#1890ff', '#13c2c2'],
      series: [{
        name: 'Prélèvements',
        data: [31, 40, 28, 51, 42, 109, 100]
      }],
      stroke: { curve: 'smooth', width: 2 },
      xaxis: { categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] }
    };
    var chart = new ApexCharts(document.querySelector('#visitor-chart'), options);
    chart.render();

    var options1 = {
      chart: {
        height: 450,
        type: 'area',
        toolbar: { show: false }
      },
      dataLabels: { enabled: false },
      colors: ['#1890ff', '#13c2c2'],
      series: [{
        name: 'Prélèvements',
        data: [76, 85, 101, 98, 87, 105, 91, 114, 94, 86, 115, 35]
      }],
      stroke: { curve: 'smooth', width: 2 },
      xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] }
    };
    var chart1 = new ApexCharts(document.querySelector('#visitor-chart-1'), options1);
    chart1.render();
  })();
}