<!DOCTYPE html>
<head>
<?php 
  // $percent = $_GET['percent'];
  $carbo_rate = $_GET['carbo_rate'];
  $protein_rate = $_GET['protein_rate'];
  $fat_rate = $_GET['fat_rate'];
?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>오늘의 통계</title>
  <!-- // progress bar 표시를 위한 jquery-UI include -->
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/jqueryui/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <!-- // pie chart 표시를 위한 jqplot include -->
  <link class="include" rel="stylesheet" type="text/css" href="/jqplot/jquery.jqplot.min.css" />
  <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
  <script type="text/javascript" src="/jqplot/jquery.jqplot.js"></script>
  <script type="text/javascript" src="/jqplot/plugins/jqplot.pieRenderer.js"></script>
  <link rel="stylesheet" type="text/css" href="/jqplot/jquery.jqplot.css" />

  <!-- <style>
  .ui-progressbar {
    position: relative;
  }
  .progress-label {
    position: absolute;
    left: 50%;
    top: 4px;
    font-weight: bold;
    text-shadow: 1px 1px 0 #fff;
  }
  </style>
   -->
  <script>
  // // $( function() {
  //   var progressbar = $( "#progressbar" ),
  //     progressLabel = $( ".progress-label" );
 
  //   progressbar.progressbar({
  //     value: <?=$percent?>,
  //     change: function() {
  //       progressLabel.text( progressbar.progressbar( "value" ) + "%" );
  //     },
  //     complete: function() {
  //       progressLabel.text( "Complete!" );
  //     }
  //   });
 
  //   function progress() {
  //     var val = progressbar.progressbar( "value" ) || 0;
 
  //     progressbar.progressbar( "value", val + 2 );
 
  //     if ( val < 99 ) {
  //       setTimeout( progress, 80 );
  //     }
  //   }
 
    // setTimeout( progress, 2000 );
  // } );
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
  <div id="pie8" style="width:300px; height:300px;"></div>
</body>



