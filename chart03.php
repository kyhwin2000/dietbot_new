<!doctype html>
<?php 
  $cal_rate = $_GET['cal_rate'];
  $carbo_rate = $_GET['carbo_rate'];
  $protein_rate = $_GET['protein_rate'];
  $fat_rate = $_GET['fat_rate'];
  $today = date("Y/m/d");
?>
<html class="no-js" lang"">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>통계</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body>
    <h1><?=$today?>의 먹은 기록</h1>
    <canvas id="CalorieBar" width="100" height="100"></canvas>
    <canvas id="NutriDonut" width="100" height="100"></canvas>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.js"></script>
    <script>
    // 칼로리 바 그리기
          var ctx = document.getElementById("CalorieBar");
          var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ["권장", "섭취"],
                datasets: [{
                    label: '칼로리',
                    data: [100, <?=$cal_rate?>],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255,99,132,1)',
                        'rgba(54, 162, 235, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }

         });
          
          // 영양 성분 도넛 그리기

          var ctx02 = document.getElementById("NutriDonut");
          var myDoughnutChart = new Chart(ctx02, {
              type: 'doughnut',
              data: {
                  datasets: [{
                      data: [<?=$carbo_rate?>,<?=$protein_rate?>,<?=$fat_rate?>],
                      backgroundColor: [
                          'rgba(75, 192, 192, 0.2)',
                          'rgba(153, 102, 255, 0.2)',
                          'rgba(255, 159, 64, 0.2)'
                      ],
                      label: '섭취 영양 비율'
                  }],
                  labels: [
                      "탄수화물",
                      "단백질",
                      "지방"
                  ]
              },
              options: {
                  responsive: true,
                  legend: {
                      position: 'top',
                  },
                  title: {
                      display: true,
                      text: '섭취 영양 비율'
                  }
              }
          });
  </script>
  </body>