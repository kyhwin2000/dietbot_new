<!DOCTYPE html>
<head>
<?php 
  $cal_rate = $_GET['cal_rate'];
  $carbo_rate = $_GET['carbo_rate'];
  $protein_rate = $_GET['protein_rate'];
  $fat_rate = $_GET['fat_rate'];
?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>오늘의 통계</title>
  <!-- // bar, pie 표시를 위한 jqplot include -->
  <script tupe="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script type="text/javascript" src="/jqplot/jquery.jqplot.js"></script>
  <script type="text/javascript" src="/jqplot/plugins/jqplot.barRenderer.js"></script>
  <script type="text/javascript" src="/jqplot/plugins/jqplot.pieRenderer.js"></script>
  <script type="text/javascript" src="/jqplot/plugins/jqplot.categoryAxisRenderer.js"></script>
  <script type="text/javascript" src="/jqplot/plugins/jqplot.pointLabels.js"></script>
  <!-- <link rel="stylesheet" type="text/css" href="/jqplot/jquery.jqplot.css" /> -->
  <link class="include" rel="stylesheet" type="text/css" href="/jqplot/jquery.jqplot.min.css" />
  
  
  <script>
  $(document).ready(function(){
      $.jqplot('graph', [[['권장 칼로리', 100], ['섭취 칼로리', <?=$cal_rate?> ]]], {
      title: '칼로리',
      series:[{renderer:$.jqplot.BarRenderer}],
      axes: {
        xaxis: {
          renderer: $.jqplot.CategoryAxisRenderer,
          // label: "X 좌표제목"
        },
        yaxis: {
          // label: "Y 좌표제목"
        }
      }
    })
    });


  $(document).ready(function(){ 
    var s1 = [['탄수화물',<?=$carbo_rate?>], ['단백질',<?=$protein_rate?>], ['지방',<?=$fat_rate?>]];
         
    var plot8 = $.jqplot('pie8', [s1], {
        grid: {
            drawBorder: false, 
            drawGridlines: false,
            background: '#ffffff',
            shadow:false
        },
        axesDefaults: {
             
        },
        seriesDefaults:{
            renderer:$.jqplot.PieRenderer,
            rendererOptions: {
                showDataLabels: true
            }
        },
        legend: {
            show: true,
            rendererOptions: {
                numberRows: 1
            },
            location: 's'
        }
    }); 
});
  </script>
</head>

<body> 
  <!-- <div id="progressbar"><div class="progress-label">Loading...</div></div> -->
  <div id="page">
    <div id="graph" style="width:70%; height:15.0em;"></div>
    <div id="pie8" style="width:300px; height:300px;"></div>
  </div>
</body>



