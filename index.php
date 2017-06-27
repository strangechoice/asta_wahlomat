<?php
$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
$baseurl = "http://" . $_SERVER['SERVER_NAME'] . $uri_parts[0];
require_once('config/config.php');
?>
<!DOCTYPE html>
<html>
  <head>
  <title><?php echo $config['name']; ?></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta content="<?php echo $config['name']; ?>">
    
    <meta name="image_src" content="<?php echo $config['logo']; ?>"/>
    <meta name="description" content="Der <?php echo $config['name']; ?> ist ein Angebot von XYZ und wurde auf Beschluss des XXXV. Studierendenparlaments der Uni Bonn entwickelt. Er ermöglicht es, zu ausgewählten Themen die eigenen Standpunkte mit denen der Listen abzugleichen, die zur Wahl antreten."/>
    
    <meta property="og:title" content="<?php echo $config['name']; ?>"/>
    <meta property="og:type"  content="website"/>
    <meta property="og:image" content="<?php echo $config['logo']; ?>"/>
    <meta property="og:url"   content=""/>
    <meta property="og:site-name" content="asta.hhu.de"/>
    <meta property="og:description" content="Der <?php $config['name']; ?> ist ein Angebot von XYZ und wurde auf Beschluss des XXXV. Studierendenparlaments der Uni Bonn entwickelt. Er ermöglicht es, zu ausgewählten Themen die eigenen Standpunkte mit denen der Listen abzugleichen, die zur Wahl antreten."/>
    
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    
    <link rel="stylesheet" type="text/css" href="css/style.css">
    
    <link href="shariff/shariff.min.css" rel="stylesheet">
    
    <script src="js/jquery-2.0.2.min.js"></script>
    
  </head>
  <body>

  <div class="container mow-container" style="margin-top:20px;">

  <div class='coop'>
    <a class='imglink' href='https://asta.hhu.de/'><img id='asta' src='img/asta_344x75.png' alt='AStA'/></a>
    <a class='imglink' href='http://hochschulradio.de/'><img id='hochschulradio' src='img/hochschulradio_96x96.png' alt='Hochschulradio'/></a>
  </div>
  
  <div class="text-center">
  
  <img src="<?php echo $config['logo']; ?>" title="<?php echo $config['name']; ?> Logo"/>
    
  <h1><small>Der</small> <?php echo $config['name']; ?></h1>
  </div>
    <p>Der <?php echo $config['name']; ?> ist ein technisches Hilfsmittel, das es erm&ouml;glicht, zu ausgew&auml;hlten Themen die eigenen Standpunkte mit denen der Listen abzugleichen, die zur Wahl antreten.</p>
    
    <p>Er ist selbstverständlich nur als Automat ohne Hirn zu verstehen und spricht keine Wahlempfehlungen aus.</p>
    
    <p>Für ihre Stellungnahmen zu den Thesen sind die Listen selbst verantwortlich.</p>
    
    <p class="text-center"><a class="btn btn-large btn-primary" href="sp-check.php" title="<?php echo $config['name']; ?> starten">Mit der Befragung beginnen!</a></p>
    
    <p class="text-center"><a href="faq.php" title="Fragen und Antworten"><small>FAQ</small></a></p>
    
    
    
    <div class="shariff" data-url="<?php echo $baseurl; ?>" data-referrer-track=null></div>
    <script src="shariff/shariff.min.js"></script>
  </div>
  
  </body>
</html>
