<?php
    include '../config/config.php';
    include '../includes/file.php';
	include '../includes/functions.php';
    
    $visits = get_visits('', '../data/visits.sav');
    
    $data_content = file_get_contents("../config/data.json");
    if(!$data_content){
	    echo "ERROR READING CONFIG";
    } else {
        $data = json_decode($data_content, true);
    }
    
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
    <title><?php echo $config['name']; ?> - Statistik</title>
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
	<p>Von wie vielen IP-Adressen wurde der AStA Wahlomat aufgerufen?</p>
  
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
		$recommends = array();
		$theses = array();
		$notcounted = 0; 
		foreach($answers as $key => $value){
			foreach($value as $k => $ans){
                while ( strlen($ans) < count($data['theses']) ) { $ans .= 'd'; }
				$sel = sort_lists_by_points($data, str_split($ans));
				echo "<span style='display: inline-block; min-width: 200px'>".$ans."</span> (".trim($sel['lists'][0]['name']).")<br>\n";
				if ( strlen(str_replace('d', '', $ans)) > (count($data['theses'])/2) ) {
					$rec = trim($sel['lists'][0]['name']);
					if ( !isset($recommends[$rec]) ) $recommends[$rec] = 0;
					$recommends[$rec]++;
					foreach ( str_split($ans) as $index => $answer ) {
						switch ($answer) {
							case 'a': $theses[$index]['consent']++; break;
							case 'b': $theses[$index]['neutral']++; break;
							case 'c': $theses[$index]['defeat']++; break;
							case 'e': $theses[$index]['consent2x']++; break;
							case 'f': $theses[$index]['neutral2x']++; break;
							case 'g': $theses[$index]['defeat2x']++; break;
							case 'd': 
							case 'h': 
							default:  $theses[$index]['skipped']++;
						}
					}
				} else {
					$notcounted++;
				}
			}
		}
      ?>
      </p>
      </div>

      <div class="well">
      <h1>Ergebnisse</h1>
	  <p>Es wurden <?php echo $notcounted; ?> Umfragen nicht gewertet, da mehr als 50% der Thesen übersprungen wurden.</p>
	  <h2>Listen</h2>
      <p>
	  <?php 
		arsort($recommends, SORT_NUMERIC);
		foreach ($recommends as $name => $count ) {
			echo "<span class='label' style='display: inline-block; width: 220px; color: black; text-align: left;'>".$name."</span>";
			echo "<span class='count' style='display: inline-block; color: white; font-weight: bold; background-color: #666; border: 1px solid white; width: ".$count."px;'>".$count."</span><br>";
		}
      ?>
      </p>
	  <h2>Thesen</h2>
	  <p>
	  <?php
		foreach ( $data['theses'] as $index => $thesis ) {
		    echo "<div class='theses'>\n";
			echo "<h4>".$thesis['s']."</h4>\n";
			echo "<p>".$thesis['l']."</p>\n";
			$val = 0;
			$col = 0;
			$total = array_sum($theses[$index]);
			echo "<p class='bar'>\n";
			$consent = round(($theses[$index]['consent']/$total)*100, 2); $val += $consent;
			if ( $consent ) { echo "<span title='Zustimmung: ".$consent."%' style='background-color: #3fad46; width: ".$consent."%'></span>\n"; $col++; }
			$consent2x = round(($theses[$index]['consent2x']/$total)*100, 2); $val += $consent2x;
			if ( $consent2x ) { echo "<span title='Zustimmung (doppelt): ".$consent2x."%' style='background-color: #318837; width: ".$consent2x."%'></span>\n"; $col++; }
			$neutral = round(($theses[$index]['neutral']/$total)*100, 2); $val += $neutral;
			if ( $neutral ) { echo "<span title='Neutral: ".$neutral."%' style='background-color: #f0ad4e; width: ".$neutral."%'></span>\n"; $col++; }
			$neutral2x = round(($theses[$index]['neutral2x']/$total)*100, 2); $val += $neutral2x;
			if ( $neutral2x ) { echo "<span title='Neutral (doppelt): ".$neutral2x."%' style='background-color: #ec971f; width: ".$neutral2x."%'></span>\n"; $col++; }
			$defeat = round(($theses[$index]['defeat']/$total)*100, 2); $val += $defeat;
			if ( $defeat ) { echo "<span title='Ablehnung: ".$defeat."%' style='background-color: #d9534f; width: ".$defeat."%'></span>\n"; $col++; }
			$defeat2x = round(($theses[$index]['defeat2x']/$total)*100, 2); $val += $defeat2x;
			if ( $defeat2x ) { echo "<span title='Ablehnung (doppelt): ".$defeat2x."%' style='background-color: #c9302c; width: ".$defeat2x."%'></span>\n"; $col++; }
			
			$skipped = 100 - $val; // Design fix...
			$real_skip = round(($theses[$index]['skipped']/$total)*100, 2);
			if ( $skipped ) echo "<span title='Übersprungen: ".$real_skip."%' style='background-color: #aaa; width: ".$skipped."%'></span>\n";
			echo "</p>\n";
			echo "</div>\n";
		}
	  ?>
	  </p>
      </div>
  </div>
  
  </body>
</html>
