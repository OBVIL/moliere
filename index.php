<?php
/**
 * Pilote de l’application OBVIL-Molière
 * La logique applicative est libre
 * Le design est contraint par obvil.css
 */
include (dirname(__FILE__).'/../teipot/Teipot.php');
$path = Web::pathinfo(); // path demandé

// les pièces commencent par moliere, laissaer la main au script pieces.php
if (strpos($path, 'moliere') === 0 || strpos($path, 'theatre') === 0) {
  include (dirname(__FILE__).'/theatre.php');
  exit();
}
// devanciers et contemporains
if (strpos($path, 'contexte') === 0) {
  include (dirname(__FILE__).'/contexte.php');
  exit();
}
$pot = false;
$q = ""; // requête en cours

// ou alors on charge de la critique sous critique/…,
if (strpos($path, 'critique/') === 0) {
  // noter, le path relatif dans la base critique.sqlite
  $pot = new Teipot(dirname(__FILE__).'/critique-moliere.sqlite', 'fr', substr($path, 9));
  $pot->file(); // fichiers statiques, notamment images
  $doc=$pot->doc();
  // ici rajouter des metas pour les moteurs de recherche, noindex, renvoi à la version canonique du document
  $q = $pot->q;
  $critique = true;
}
// aller chercher un doc statique écrit en html
else if (strpos($path, 'doc/')===0) {
  $chtimel=new Chtimel(dirname(__FILE__) . '/' . $path . '.html');
  $doc['body']=$chtimel->body();
  $doc['head']=$chtimel->head();
}
else if ($path == '') {
  $chtimel=new Chtimel(dirname(__FILE__) . '/doc/home.html');
  $doc['body']=$chtimel->body();
  $doc['head']=$chtimel->head();
}
// ou alors, quoi ?
else {

}

// si une base, mais pas de doc trouvé, charger des résultats en mémoire
if ($pot && !isset($doc['body'])) {
  $timeStart=microtime(true);
  $pot->search();
}

$teinte = Web::basehref().'../Teinte/'; // chemin css, js ; basehref est le nombre de '../' utile pour revenir en racine du site
$theme = Web::basehref().'../theme/'; // autres ressources spécifiques


?><!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" charset="utf-8" type="text/css" href="<?php echo $teinte ?>tei2html.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $theme ?>obvil.css" />
        <?php
if(isset($doc['head'])) echo $doc['head'];
else echo '
<title>OBVIL, Molière</title>
';
if (isset($moliere) && !$play) {
  echo '<style>#main { width: 100%; } #aside { visibility: hidden; } </style>';
}
    ?>
  </head>
  <body>
    <div id="center">
      <header id="header">
        <h1>
          <a href="<?php

if (isset($play)) echo "moliere";
else echo Web::$basehref;
          ?>">OBVIL, Molière</a>
        </h1>
        <a class="logo" href="http://obvil.paris-sorbonne.fr/"><img class="logo" src="<?php echo $theme; ?>img/logo-obvil.png" alt="OBVIL"></a>
      </header>
      <div id="contenu">
        <div id="main">
          <nav id="toolbar">
            <nav class="breadcrumb">
            <?php
if (isset($critique)) echo '<a href="' . Web::basehref() . 'critique/' . $pot->qsa(null, null, '?') . '">Critique moliéresque</a> &gt; ';
if (isset($doc['breadcrumb'])) echo $doc['breadcrumb'];
            ?>
            </nav>
          </nav>
          <div id="article">
      <?php
if (isset($doc['body'])) {
  echo $doc['body'];
  // page d’accueil d’un livre avec recherche plein texte, afficher une concordance
  if ($pot && $pot->q && (!$doc['artname'] || $doc['artname']=='index')) {
    echo $pot->concBook($doc['bookrowid']);
  }
}
// pas de livre demandé, montrer un rapport général
else if ( $pot ) {
  // nombre de résultats
  echo $pot->report();
  // présentation bibliographique des résultats
  if (isset($critique)) echo $pot->biblio(array('byline','title','date'));
  else echo $pot->biblio(array('date', 'title'));
  // concordance s’il y a recherche plein texte
  echo $pot->concByBook();
}
      ?>
          </div>
        </div>
        <aside id="aside">
          <?php

// livre de critique
if (isset($doc['bookrowid'])) {
  if(isset($doc['download'])) echo "\n".'<nav id="download">' . $doc['download'] . '</nav>';
  echo "\n<nav>";
  // auteur, titre, date
  if ($doc['byline']) $doc['byline']=$doc['byline'].'<br/>';
  echo "\n".'<header><a href="' . $pot->basehref() . $doc['bookname'].'/">'.$doc['byline'].$doc['title'].' ('.$doc['end'].')</a></header>';
  // rechercher dans ce livre
  echo '
  <form action=".#conc" name="searchbook" id="searchbook">
    <input name="q" id="q" class="search" size="20" onclick="this.select()"  placeholder="Rechercher dans ce livre" title="Rechercher dans ce livre" value="'. str_replace('"', '&quot;', $q) .'"/>
    <input type="image" id="go" alt="&gt;" value="&gt;" name="go" src="'. $theme . 'img/loupe.png"/>
  </form>
  ';
  // table des matières
  echo '
          <div id="toolpan" class="toc">
            <div class="toc">
              '.$doc['toc'].'
            </div>
          </div>
  ';
  echo "\n</nav>";
}
// accueil ? formulaire de recherche général
else if(isset($critique)) {
  echo'
    <form action="" style="text-align:center">
      <input name="q" id="q" class="search" size="20" onclick="this.select()" placeholder="Rechercher" class="text" value="'.str_replace('"', '&quot;', $q).'"/>
      <input type="image" id="go" alt="&gt;" value="&gt;" name="go" src="'. $theme . 'img/loupe.png"/>
    </form>
  ';
}
?>
        </aside>
      </div>
    </div>
    <script type="text/javascript" src="<?php echo $teinte; ?>Tree.js">//</script>
    <script type="text/javascript" src="<?php echo $teinte; ?>Sortable.js">//</script>
  </body>
</html>
