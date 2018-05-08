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
	<script type="text/javascript">
	function downloadCSV(event) {
	    var button = event.target;
		var p = button.parentNode;
		var container = p.previousElementSibling;
		var h2 = container.previousElementSibling;
		var type = h2.innerHTML;
		var dateobj = new Date();
		var filename = type + "_" + dateobj.toISOString() + ".csv";
		var csv = getCSVData(type, container);
		var link = document.getElementById('download_csv');
		link.href = 'data:text/csv;charset=utf-8,'+ encodeURI(csv);
		link.target = '_blank';
		console.log(link.href);
		link.download = filename;
		link.click();

		return false;
	}

	function getCSVData(type, container)
	{
		var csv = '';
		var html, text;
		var children = container.children;

		if ( type == 'Listen' ) {
			csv = '"Liste", "Empfehlungen"\n';
			var even=true;
			for ( var i=0; i < children.length; i++ ) {
				html = children[i].innerHTML;
				text = html.replace(/"/, '\\"');
				if ( children[i].tagName == 'SPAN' ) {
					if ( even ) {
						csv += '"' + text + '", ';
						even=false;
					} else {
						csv += '"' + text + '"\n';
						even=true;
					}
				}
			}
		} else if ( type == 'Thesen' ) {
			var h4, p, bar, currentSpan, nextSpan, thesis, desc, val_percent, val_count;
			var count, i;
			csv = '"These", "Beschreibung", "Zustimmung %", "Zustimmung Anzahl", "Zustimmung (doppelt) %", "Zustimmung (doppelt) Anzahl", "Neutral %", "Neutral Anzahl", ';
			csv += '"Neutral (doppelt) %", "Neutral (doppelt) Anzahl", "Ablehnung %", "Ablehnung Anzahl", "Ablehnung (doppelt) %", "Ablehnung (doppelt) Anzahl", Übersprungen"\n';
			for ( i=0; i < children.length; i++ ) {
				if ( children[i].tagName == 'DIV' ) {
					h4 = children[i].firstElementChild;
					thesis = h4.innerHTML.replace(/"/, '\\"');
					csv += '"' + thesis + '", ';
					p = h4.nextElementSibling;
					desc = p.innerHTML.replace(/"/, '\\"');
					csv += '"' + desc + '", ';
					bar = p.nextElementSibling;
					currentSpan = bar.firstElementChild;
					for ( count=0; count < 6; count++ ) {
						val_count = currentSpan.title.replace(/^.*\[/, '').replace(/\]$/, '');
						val_percent = currentSpan.title.replace(/^.*: /, '').replace(/ \[.*\]$/, '');
						csv += '"' + val_percent + '", "' + val_count + '", ';
						nextSpan = currentSpan.nextElementSibling;
						currentSpan = nextSpan;
					}
					val_count = currentSpan.title.replace(/^.*\[/, '').replace(/\]$/, '');
					val_percent = currentSpan.title.replace(/^.*: /, '').replace(/ \[.*\]$/, '');
					csv += '"' + val_percent + '", "' + val_count + '"\n';
				}
			}
		} else {
			csv = '"Empty set for type ' + type +'"\n';
		}

		alert(csv);

		return csv;
	}
	</script>
  </head>
  <body>
  
  <div class="container top-buffer">
  
	<div class="well">
	<h1>Besucher</h1>
	<p>Von wie vielen IP-Adressen wurde der AStA Wahlomat aufgerufen?</p>
  
     <table class="table table-bordered table-hover">
     <tr><th style="width: 200px;">Datum</th><th style="width:100px">Besucher</th><th style="width:640px;">&nbsp;</th></tr>
            <?php
				$total = 0;
                  foreach($visits as $day => $value){
				$dayvisitors = sizeof($value);
				$total += $dayvisitors;
                        
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
				echo "\n<tr class='total'><td><b>Gesamt</b></td><td><b>".$total."</b></td></tr>\n";
            ?>

     </table>
     </div>
     
     <div class="well">
     <h1>Durchläufe</h1>
	<p>Wie oft wurde eine Auswertung vorgenommen? (Letzte Frage oder Gewichtungsseite)</p>
     
     <table class="table table-bordered table-hover">
     <tr><th style="width: 200px;">Datum</th><th style="width:100px">Durchläufe</th><th style="width:640px;">&nbsp;</th></tr>
            <?php
				$total = 0;
                  foreach($visits as $day => $value){
				$dayvisits = 0;
				foreach($value as $key => $v){
					$dayvisits += $v;
				}
				$total += $dayvisits;
                        
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
				echo "\n<tr class='total'><td><b>Gesamt</b></td><td><b>".$total."</b></td></tr>\n";
            ?>

     </table>
     </div>
     
      <div class="well">
      <h1>Nicht gezählte Aufrufe</h1>
      <p><span class="label label-primary"><?php echo $nocount;?></span> Aufrufe wurden nicht gespeichert.</p>
      </div>
     
      <?php
		$recommends = array();
		$theses = array();
		$notcounted = 0; 
		foreach($answers as $key => $value){
			foreach($value as $k => $ans){
                while ( strlen($ans) < count($data['theses']) ) { $ans .= 'd'; }
				$sel = sort_lists_by_points($data, str_split($ans));
				$answerlist[] = $ans;
				//echo "<span style='display: inline-block; min-width: 200px'>".$ans."</span> (".trim($sel['lists'][0]['name']).")<br>\n";
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
					// transform empty to 0
					foreach ( $theses as $index => $vals ) {
						foreach ( array('consent', 'neutral', 'defeat', 'consent2x', 'neutral2x', 'defeat2x', 'skipped') as $level ) {
							if ( ! isset($theses[$index][$level]) ) $theses[$index][$level] = 0;
						}
					}
				} else {
					$notcounted++;
				}
			}
		}
      ?>

      <div class="well">
      <h1>Ergebnisse</h1>
	  <p>Es wurden <?php echo $notcounted; ?> Umfragen nicht gewertet, da mehr als 50% der Thesen übersprungen wurden.</p>
	  <h2>Listen</h2>
      <p>
	  <?php 
		arsort($recommends, SORT_NUMERIC);
		foreach ($recommends as $name => $count ) {
			echo "<span class='label' style='display: inline-block; width: 220px; color: black; text-align: left;'>".$name."</span>\n";
			echo "<span class='count' style='display: inline-block; color: white; padding-left: 2px; font-weight: bold; background-color: #666; border: 1px solid white; width: ".$count."px;'>".$count."</span><br>\n";
		}
      ?>
      </p>
	  <p><button onclick="downloadCSV(event)">CSV-Download</button></p>
	  <h2>Thesen</h2>
	  <div>
<?php
		foreach ( $data['theses'] as $index => $thesis ) {
		    echo "\t<div class='theses'>\n";
			echo "\t\t<h4>".$thesis['s']."</h4>\n";
			echo "\t\t<p>".$thesis['l']."</p>\n";
			$val = 0;
			$col = 0;
			$total = array_sum($theses[$index]);
			echo "\t\t<p class='bar'>\n";
			$consent = round(($theses[$index]['consent']/$total)*100, 2); $val += $consent;
			echo "\t\t\t<span title='Zustimmung: ".$consent."% [".$theses[$index]['consent']."]' style='background-color: #3fad46; width: ".$consent."%'></span>\n"; $col++;
			$consent2x = round(($theses[$index]['consent2x']/$total)*100, 2); $val += $consent2x;
			echo "\t\t\t<span title='Zustimmung (doppelt): ".$consent2x."% [".$theses[$index]['consent2x']."]' style='background-color: #318837; width: ".$consent2x."%'></span>\n"; $col++;
			$neutral = round(($theses[$index]['neutral']/$total)*100, 2); $val += $neutral;
			echo "\t\t\t<span title='Neutral: ".$neutral."% [".$theses[$index]['neutral']."]' style='background-color: #f0ad4e; width: ".$neutral."%'></span>\n"; $col++;
			$neutral2x = round(($theses[$index]['neutral2x']/$total)*100, 2); $val += $neutral2x;
			echo "\t\t\t<span title='Neutral (doppelt): ".$neutral2x."% [".$theses[$index]['neutral2x']."]' style='background-color: #ec971f; width: ".$neutral2x."%'></span>\n"; $col++;
			$defeat = round(($theses[$index]['defeat']/$total)*100, 2); $val += $defeat;
			echo "\t\t\t<span title='Ablehnung: ".$defeat."% [".$theses[$index]['defeat']."]' style='background-color: #d9534f; width: ".$defeat."%'></span>\n"; $col++;
			$defeat2x = round(($theses[$index]['defeat2x']/$total)*100, 2); $val += $defeat2x;
			echo "\t\t\t<span title='Ablehnung (doppelt): ".$defeat2x."% [".$theses[$index]['defeat2x']."]' style='background-color: #c9302c; width: ".$defeat2x."%'></span>\n"; $col++;
			$skipped = round((100 - $val), 2); // fix some rounding issues... (not totally exact...)
			echo "\t\t\t<span title='Übersprungen: ".$skipped."% [".$theses[$index]['skipped']."]' style='background-color: #aaa; width: ".$skipped."%'></span>\n";
			echo "\t\t</p>\n";
			echo "\t</div>\n";
		}
	  ?>
	  </div>
	  <p style='clear: both'><button onclick="downloadCSV(event)">CSV-Download</button></p>
      </div>
      <div class="well">
      <h1>Antworten</h1>
      <p>
	  <?php
		  foreach ( $answerlist as $ans ) {
			  echo $ans."<br>\n";
		  }
?>
      </p>
      </div>
  </div>
  <a style='display: none' id='download_csv'>Not visible</a>
  </body>
</html>
