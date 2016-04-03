<?php
/**
 * Visualisation du dossier des précurseurs de Molière
 */
include( dirname(__FILE__).'/Moliere.php' );
new Moliere(dirname(__FILE__).'/contexte.sqlite');
$playcode = null;
if ( Moliere::$pathinfo != 'contexte/' ) {
  $playcode = end ( explode( '/', Moliere::$pathinfo ) );
  $play = Moliere::$pdo->query("SELECT * FROM play WHERE code = ".Moliere::$pdo->quote($playcode))->fetch();
  if (!$play) {
    echo  Moliere::$basehref.'contexte/';
    header( 'Status: 301 Moved Permanently', false, 301 );
    header( 'Location: '.Moliere::$basehref.'contexte/' );
    exit();
  }
}
?><!DOCTYPE html>
<html>
  <head>
    <?php
Moliere::head();
echo '<title>OBVIL, dossier Molière</title>';
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
          <a href="<?php echo Moliere::$basehref; ?>">OBVIL, Molière</a>
        </h1>
        <a class="logo" href="http://obvil.paris-sorbonne.fr/"><img class="logo" src="<?php echo Moliere::$basehref.'../theme/'?>img/logo-obvil.png" alt="OBVIL"></a>
      </header>
      <div id="contenu">
        <div id="main">
          <nav id="toolbar">
            <nav class="breadcrumb">
              Molière, <a href="<?php echo Moliere::$basehref ?>contexte/">Devanciers et contemporains</a>
            <?php if ( $playcode ) { Moliere::select( $playcode ); } ?>
            </nav>
          </nav>
          <div id="article">
      <?php
if ( !$playcode ) {

  echo '
<div style="padding: 1em">
<h1>Devanciers et contemporains de Molière</h1>
<p class="noindent">Ce dossier présente des pièces de théâtre qui ne sont pas de Molière mais qui lui sont antérieures ou contemporaines, afin de les comparer avec les mêmes statistiques et interfaces. Ce ne sont pas des éditions du LABEX OBVIL, mais des textes prêtés par la <a href="http://bibdramatique.paris-sorbonne.fr/">Bibliothèque dramatique</a> (CELLF, Université Paris-Sorbonne) et le site <a href="http://theatre-classique.fr/pages/programmes/PageEdition.php">Théâtre Classique</a> (Paul Fièvre). L’OBVIL tient à remercier ces partenaires, et à leur laisser toute la paternité (et la responsabilité) de leurs éditions. Si un texte vous intéresse plus particulièrement, il vaut mieux le consulter sur son site d’origine. Sur la <a href="http://bibdramatique.paris-sorbonne.fr/">Bibliothèque dramatique</a>, vous trouverez une introduction critique, des notes, et différents formats pour la lecture (epub, mobi, pdf). Sur <a href="http://theatre-classique.fr/pages/programmes/PageEdition.php">Théâtre Classique</a>, vous trouverez bien d’autres pièces (plus de 800 en 2016), d’autres statistiques et formats. Cette compilation a été rendue possible par un effort de convergence sur le format des textes (XML/TEI) afin que les logiciels de chacun y trouvent leurs repères et leurs balises.</p>
</div>'."\n";
  Dramagraph_Biblio::table( Moliere::$pdo );
}
else {
  if (strpos( $play['identifier'], 'bibdramatique.paris-sorbonne.fr') !== false) {
    echo '<p class="noindent">Retrouvez cette pièce sur la <a target="_blank" href="'.$play['identifier'].'">Bibliothèque dramatique</a>, avec son introduction et son apparat critique.</p>
    <p> </p>';
  }
  if (strpos( $play['identifier'], 'theatre-classique.fr') !== false) {
    echo '<p class="noindent">Retrouvez cette pièce sur <a target="_blank" href="'.$play['identifier'].'">Théâtre Classique</a>, avec 800 autres textes, et leurs outils.</p>
    <p> </p>';

  }
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
  Moliere::$qobj->execute( array( $playcode, 'charline' ) );
  echo  current( Moliere::$qobj->fetch(PDO::FETCH_ASSOC)) ;
  echo '<p> </p>';
}
        ?>
      </aside>
    </div>
    <?php Moliere::foot();  ?>
  </body>
</html>
