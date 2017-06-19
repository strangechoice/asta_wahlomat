<?php
    include '../includes/file.php';
    
    $visits = get_visits('', '../data/visits.sav');
    
    $nocount = $visits['nocount'];
    $answers = $visits['ans'];
    
    unset($visits['nocount']);
    unset($visits['ans']);
    
    $max_visitors = 0;
    foreach($visits as $value){
        if(sizeof($value) > $max_visitors){
            $max_visitors = sizeof($value);
        } 
    }
    
    $max_visits = 0;
    foreach($visits as $value){
		$count = 0;
		foreach($value as $key => $v){
			$count += $v;
		}
		if($count > $max_visits){
            $max_visits = $count;
        } 
    }
?>
<!DOCTYPE HTML>
<html>
  <head>
    <title>Mahlowat - Statistik</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta content="">
    <link href="../css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="../css/bootstrap-responsive.min.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="../css/style.css">
  </head>
  <body>
  
  <div class="container top-buffer">
  
	<div class="well">
	<h1>Besucher</h1>
	<p>Von wie vielen IP-Adressen wurde der Mahlowat aufgerufen?</p>
  
     <table class="table table-bordered table-hover">
     <tr><th style="width: 200px;">Datum</th><th style="width:100px">Besucher</th><th style="width:640px;">&nbsp;</th></tr>
            <?php
                  foreach($visits as $day => $value){
				$dayvisitors = sizeof($value);
                        
                        if($max_visitors != 0){
					$day_percentage = intval( 100 *  $dayvisitors / $max_visitors);
				} else {
					$day_percentage = 0;
				}
				
				$dayvisitors == $max_visitors ? $class = "success" : $class = "";
      
				echo "<tr class='$class'>
				<td><b>$day</b></td><td>$dayvisitors</td>
				<td><div class='progress'>
					<div class='progress-bar' role='progressbar' aria-valuenow='$dayvisitors' aria-valuemin='0' aria-valuemax='$max_visitors' style='width: $day_percentage%;'>
						$dayvisitors
					</div>
				</div>
				</td>
				</tr>";
                  }
            ?>

     </table>
     </div>
     
     <div class="well">
     <h1>Durchläufe</h1>
	<p>Wie oft wurde eine Auswertung vorgenommen? (Letzte Frage oder Gewichtungsseite)</p>
     
     <table class="table table-bordered table-hover">
     <tr><th style="width: 200px;">Datum</th><th style="width:100px">Durchläufe</th><th style="width:640px;">&nbsp;</th></tr>
            <?php
                  foreach($visits as $day => $value){
				$dayvisits = 0;
				foreach($value as $key => $v){
					$dayvisits += $v;
				}
                        
                        if($max_visits != 0){
					$day_percentage2 = intval( 100 *  $dayvisits / $max_visits);
				} else {
					$day_percentage2 = 0;
				}
				
				$dayvisits == $max_visits ? $class = "success" : $class = "";
      
				echo "<tr class='$class'>
				<td><b>$day</b></td><td>$dayvisits</td>
				<td><div class='progress'>
					<div class='progress-bar' role='progressbar' aria-valuenow='$dayvisits' aria-valuemin='0' aria-valuemax='$max_visits' style='width: $day_percentage2%;'>
						$dayvisits
					</div>
				</div>
				</td>
				</tr>";
                  }
            ?>

     </table>
     </div>
     
      <div class="well">
      <h1>Nicht gezählte Aufrufe</h1>
      <p><span class="label label-primary"><?php echo $nocount;?></span> Aufrufe wurden nicht gespeichert.</p>
      </div>
     
      <div class="well">
      <h1>Antworten</h1>
      <p>
      <?php 
		foreach($answers as $key => $value){
			foreach($value as $k => $ans){
				echo "$ans<br>\n";
			}
		}
      ?>
      </p>
      </div>
     
      
  </div>
  
  </body>
</html>