<?php
/**
 * Visualisation du dossier des précurseurs de Molière
 */
include( dirname(__FILE__).'/Moliere.php' );
new Moliere(dirname(__FILE__).'/moliere.sqlite');
$playcode = null;
if ( Moliere::$pathinfo != 'moliere' && Moliere::$pathinfo != 'moliere/' ) {
  $playcode = current( explode( '/', Moliere::$pathinfo ));
  $sql = "SELECT * FROM play WHERE code = ".Moliere::$pdo->quote($playcode);
  $play = Moliere::$pdo->query( $sql )->fetch();
  if (!$play) {
    $playcode;
    echo  Moliere::$basehref.'moliere/';
    // header( 'Status: 301 Moved Permanently', false, 301 );
    // header( 'Location: '.Moliere::$basehref.'moliere/' );
    exit();
  }
}
?><!DOCTYPE html>
<html>
  <head>
    <?php
    if (isset($play)) echo '<title>'.$play['title'].', Molière, LABEX OBVIL</title>';
Moliere::head();
if ( !$playcode ) {
  echo '<style>
  #main { width: 100%; float: none !important; }
  #aside { visibility: hidden; }
  #contenu { padding: 0 51px 0 51px; height: 100%; clear: both; };
   </style>';
}
    ?>
  </head>
  <body>
    <div id="center">
      <header id="header">
        <h1>
          <a href=".">OBVIL, Molière</a>
        </h1>
        <a class="logo" href="http://obvil.paris-sorbonne.fr/"><img class="logo" src="<?php echo Moliere::$basehref ?>'../theme/img/logo-obvil.png" alt="OBVIL"></a>
      </header>
      <div id="contenu">
        <div id="main">
          <nav id="toolbar">
            <nav class="breadcrumb">
            Molière, <a href="<?php echo Moliere::$basehref ?>moliere/">Théâtre complet</a>
            <?php if ( $playcode ) { Moliere::select( $playcode ); } ?>
            </nav>
          </nav>
          <div id="article">
      <?php
if ( !$playcode ) {
  Dramagraph_Biblio::table( Moliere::$pdo );
}
else {
  Moliere::text($playcode);
}
      ?>
          <p> </p>
         </div>
      </div>
      <aside id="aside">
          <?php
// pièce de Théâtre
if( $playcode ) {
  echo "\n".'<nav id="download"><small>Télécharger :</small>
  <a href="http://dramacode.github.io/epub/'.$playcode.'.epub">epub</a>,
  <a href="http://dramacode.github.io/kindle/'.$playcode.'.mobi">kindle</a>,
  <a href="http://dramacode.github.io/iramuteq/'.$playcode.'.txt">iramuteq</a>,
  <a href="http://dramacode.github.io/html/'.$playcode.'.html">html</a>.
  </nav>';
  Moliere::$qobj->execute( array( $playcode, 'charline' ) );
  echo  current( Moliere::$qobj->fetch(PDO::FETCH_ASSOC)) ;
  echo '<p> </p>';
}
        ?>
        </aside>
      </div>
    </div>
    <?php Moliere::foot();  ?>
  </body>
</html>
