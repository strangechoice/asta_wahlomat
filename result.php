<?php    
    include 'includes/functions.php';
    include 'includes/elements.php';
    include 'includes/file.php';
    
    $data_content = file_get_contents("config/data.json");
    if(!$data_content){
	echo "ERROR READING CONFIG";
    } else {
    $data = json_decode($data_content, true);
    
	$css = Array();
	$css[0] = "bootstrap.min.css";
	$css[1] = "cerulean.min.css";
	$css[2] = "cosmo.min.css";
	$css[3] = "cyborg.min.css";
	$css[4] = "darkly.min.css";
	$css[5] = "flatly.min.css";
	$css[6] = "journal.min.css";
	$css[7] = "lumen.min.css";
	$css[8] = "paper.min.css";
	$css[9] = "readable.min.css";
	$css[10] = "sandstone.min.css";
	$css[11] = "simplex.min.css";
	$css[12] = "slate.min.css";
	$css[13] = "spacelab.min.css";
	$css[14] = "superhero.min.css";
	$css[15] = "united.min.css";
	$css[16] = "yeti.min.css";
	$css_id = 9;
	if(isset($_GET['css'])){
		$css_id = intval($_GET['css']);
		if($css_id < 0 || $css_id > 16){
			$css_id = 0;
		}
	}

    $theses = $data['theses'];

    $theses_count = sizeof($theses);
    
    $answers = Array();
    $answerstring = '';
    $warning = true;
    $count = 'undefined';
    $sharelink = '';
    $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
    $baseurl = "http://" . $_SERVER['SERVER_NAME'] . $uri_parts[0];
    $share_via_id = false;
    $bars_only = false;
    
    if(isset($_POST['count'])){
		$count = $_POST['count'];
    }
    
    if(isset($_GET['id'])){
	$warning = false;
	$share_id = $_GET['id'];
	$sharelink = '?id='.$share_id;
	$share_via_id = true;
	$bars_only = true;
	if(substr_count($share_id , '-') == 1){
		$items = explode('-' , $share_id);
		$index = $items[0];
		$subindex = intval($items[1]);
		$answerstring = get_answer_string('./data/visits.sav', $index, $subindex);
		$answers = str_split($answerstring);
	}
    } 
    if(isset($_POST['ans']) and $_POST['ans'] != ''){
		$warning = false;
		$answerstring = $_POST['ans'];
		$answers = str_split($answerstring);
		$bars_only = false;
    } elseif(isset($_GET['ans']) and $_GET['ans'] != ''){
		$warning = false;
		$answerstring = $_GET['ans'];
		$answers = str_split($answerstring);
		$bars_only = false;
    } 
    
    if($warning) {
      for($i = 0; $i < $theses_count; $i++){
          $answers[$i] = 'd';
      }
    }
    
    
    if($count === 'true' and $sharelink === ''){
		$share_id = get_share_id($_SERVER['REMOTE_ADDR'], './data/salt.sav', './data/visits.sav');
		$sharelink = '?id='.$share_id;
		$share_via_id = true;
	}
    if($count === 'false' and $sharelink === ''){
		$sharelink = '?ans='.$answerstring;
	}
// 	
    $data = sort_lists_by_points($data, $answers);
    
    if($bars_only){
	$answerstring = '';
    }
    }
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Mahlowat - Ergebnis</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta content="Mahlowat">
    
    <meta name="image_src" content="img/mahlowat_logo.png"/>
    <meta name="description" content="Mein Mahlowat-Ergebnis"/>
    
    <meta property="og:title" content="Mahlowat"/>
    <meta property="og:type"  content="website"/>
    <meta property="og:image" content="img/mahlowat_logo.png"/>
    <meta property="og:url"   content=""/>
    <meta property="og:site-name" content="akut-bonn.de"/>
    <meta property="og:description" content="Mein Mahlowat-Ergebnis"/>
    
    
    <!--<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">-->
    <link href="css/<?php echo $css[$css_id];?>" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    
    <link rel="stylesheet" type="text/css" href="css/style.css">
    
    <script src="js/jquery-2.0.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/mahlowat.js"></script>
    
    <link href="shariff/shariff.min.css" rel="stylesheet">
  </head>
  <body>

  <div class="container" style="margin-top: 20px;">
      <img src="img/mahlowat_logo.png" title="Mahlowat Logo" class="pull-right" onclick="changeText()"/>
	<p id="spruch" class="pull-right"></p>
	
      <div class="bottom-buffer top-buffer">
    <?php 
    if($bars_only){
	echo "<h1>Ergebnis</h1>";
    } else {
	echo "<h1>Ergebnisse</h1>";
    }
    ?>
    
        <ul class="pagination">
            <li id="navi_overview" class="active"><a href="#overview" onclick="showOverview()">Übersicht</a></li>
            <?php if(!$bars_only){?>
            <li id="navi_detail" class=""><a href="#detail" onclick="showDetail()">Detailansicht</a></li>
            <?php } ?>
        </ul>
    
    
      <?php if($warning && !isset($_GET['ans'])){ ?>
      <div id="warning" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Hoppla...</h4>
				</div>
				<div class="modal-body">
					<p><strong>Anscheinend hast du keine Fragen beantwortet.</strong><br />
					Entweder hast du diese Seite direkt aufgerufen, oder du hast die Thesen wirklich noch nicht bearbeitet.</p> 
					<p>Falls letzteres zutrifft, möchten wir dir empfehlen, dies nun zu tun.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">Schließen</button>
					<a href="mahlowat.php" class="btn btn-primary">Thesen bearbeiten</a>
				</div>
			</div>
		</div>
	</div>
      
      <script type="text/javascript">
      $(document).ready(function() {
            setTimeout(function(){
                  $('#warning').modal('show');
            }, 1000);
      });
      </script>
     <?php } ?>
     
     <?php if(!$bars_only){?>
     <p><small>Nicht zufrieden mit dem Ergebnis? Vielleicht willst du die Thesen <a href="multiplier.php" onclick="callPage(event, 'multiplier.php', <?php echo "'$answerstring', '$count'";?>)" title="Gewichtung ändern">anders gewichten</a>.</small></p>
     <?php } ?>
     
     <div id="result-bars">
     <table class="table table-bordered table-hover">
     <tr><th style="width: 200px;">Liste</th><th style="width:100px">Punkte</th><th style="width:640px;">Punkte</th></tr>
            <?php
                  $top = calculate_points($data['answers'][0], $answers);
                  for($i = 0; $i < sizeof($data['answers']); $i++){
                        (calculate_points($data['answers'][$i], $answers) == $top) ? $class = "success" : $class = "";
                        print_list_result_bar($data, $i, $answers, $class);
                        echo "\n";
                  }
            ?>

     </table>
    </div>
    
    <?php if(!$bars_only){?>
    <div id="result-table">
    <div class="panel panel-default">
	<div class="panel-body">
		Listen ein-/ausblenden:
		<?php 
			for($i = 0; $i < sizeof($data['lists']); $i = $i + 1){
				$classname = string_to_css_classname($data['lists'][$i]['name']);
				echo "<button class='btn btn-default btn-primary listbtn-$classname' onclick='toggleColumn(\"$classname\")'>{$data['lists'][$i]['name_x']} </button> ";   
			}
		?>
	</div>
    </div>
    
    <p><small>Thesen mit <span class="glyphicon glyphicon-star" title="Sternchen"></span> fandest du besonders wichtig.<br> Wenn du auf den Button mit dem Namen der These klickst, bekommst du die Statements der Listen in einer Übersicht angezeigt.</small></p>
    
    <table class="table table-bordered" id="resulttable">
      <tr id="tableheader"><th> </th><th>Deine Wahl</th>
      <?php 
      
		print_result_detail_table($answers, $data);
      
      ?>
     </table>
     </div>
     
     
    <?php } ?>
    
    <hr />
    
	<div class="control-group alert alert-info">
		<p><strong>Ergebnis teilen:</strong></p>
		<div class="controls sharecontrols">
			<input type="text" class="col-md-5 form-control" id="resultlink" placeholder="" value="<?php echo $sharelink; ?>">
		</div>
		<p><?php if($count === 'false'){ ?><strong>Achtung!</strong> Aus diesem Link kann man ablesen, welche Antworten du ausgewählt und wie du die Thesen gewichtet hast!<?php } ?>&nbsp;</p>
	</div>
    
    
    <div class="shariff" data-url="<?php echo $baseurl; ?>" data-referrer-track="<?php echo $sharelink; ?>"></div>
    <div class="text-right">
      <small>Du kannst die Befragung 
      <a href="index.php" title="Von vorn beginnen">neu starten</a><?php if($bars_only){echo '.';} else {?>,
      deine 
      <a href="mahlowat.php" onclick="callPage(event, 'mahlowat.php', <?php echo "'$answerstring', '$count'";?>)" title="Antworten ändern">Antworten ändern</a>
      oder die 
      <a href="multiplier.php" onclick="callPage(event, 'multiplier.php', <?php echo "'$answerstring', '$count'";?>)" title="Gewichtung ändern">Gewichtung anpassen</a>.<?php } ?><br />
      Außerdem haben wir auch eine <a href="faq.php?from=result.php<?php if($share_via_id){echo $sharelink;}?>" onclick="callPage(event, 'faq.php?from=result.php<?php if($share_via_id){echo $sharelink;} echo "', '$answerstring', '$count'";?>)" title="FAQ">FAQ-Seite</a>.
      </small>
    </div>
    </div>
  </div>
  
  <script type="text/javascript">
	// page-specific
	$('#resultlink').click(function() {
		var $this = $(this);
		$this.select();
	});
	$('#resultlink').val(location.protocol + '//' + location.host + location.pathname + "<?php echo $sharelink; ?>");

	
	<?php if(!$bars_only){?>
	$('#result-table').hide();
	
      $('.listanswer').tooltip();
      $('.multheseslong').hide();
      $('.tt').tooltip();
      
      if(window.location.hash == '#overview'){
		showOverview();
      } else if(window.location.hash == '#detail'){
		showDetail();
      }
      
      function toggleNext(caller){
          $(caller).parent().parent().next().toggle();
      }
      
      function toggleColumn(listname){
		$('.list-'+listname).toggle(200);
		$('.listbtn-'+listname).toggleClass('btn-primary');
      }
      
      function showOverview(){
		$('#result-bars').show();
		$('#result-table').hide();
		$('#navi_overview').addClass('active');
		$('#navi_detail').removeClass('active');
      }
      
      function showDetail(){
		$('#result-bars').hide();
		$('#result-table').show();
		$('#navi_overview').removeClass('active');
		$('#navi_detail').addClass('active');
      }
     <?php } ?>
  </script>
  
    <script src="shariff/shariff.min.js"></script>
  </body>
</html>