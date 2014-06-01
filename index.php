<?php
/**
 * Pilote de l’application OBVIL-Molière
 * La logique applicative est libre
 * Le design est contraint par obvil.css
 */
include (dirname(__FILE__).'/../teipot/Teipot.php');
$path = Web::pathinfo(); // path demandé
$pot = false;
// aiguillage de l’adressage

// les pièces commencent par moliere…
if (strpos($path, 'moliere') === 0) {
  $pot = new Teipot(dirname(__FILE__).'/moliere.sqlite', 'fr', $path);
  $pot->file(); // fichiers statiques comme xml, epub, etc… sort tout seul en cas de fichier
  $doc=$pot->doc(); // sinon, attraper un doc
}
// ou alors on charge de la critique sous critique/…, 
else if (strpos($path, 'critique/') === 0) {
  // noter, le path relatif dans la base critique.sqlite
  $pot = new Teipot(dirname(__FILE__).'/critique-moliere.sqlite', 'fr', substr($path, 9));
  $pot->file(); // fichiers statiques, notamment images
  $doc=$pot->doc();
  // ici rajouter des metas pour les moteurs de recherche, noindex, renvoi à la version canonique du document
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

$teipot = Web::basehref().'../teipot/'; // chemin css, js ; baseHref est le nombre de '../' utile pour revenir en racine du site
$theme = Web::basehref().'../theme/'; // autres ressources spécifiques


?><!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <?php 
if(isset($doc['head'])) echo $doc['head']; 
else echo '
<title>OBVIL, corpus Critique</title>
';
    ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $teipot; ?>html.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $teipot; ?>teipot.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Web::$basehref ?>moliere.css" />
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900,700italic,600italic' rel='stylesheet' type='text/css' />
    <link rel="stylesheet" type="text/css" href="<?php echo $theme; ?>obvil.css" />
    
    <script type="text/javascript" src="<?php echo Web::$basehref ?>js/jquery-1.11.1.min.js"></script>
    
    
    <style type="text/css">
div.snip a.bookmark { display: none; }
    </style>
  </head>
  <body>
    <div id="center">
      <header id="header">
        <h1>
          <a href="<?php echo Web::$basehref; ?>">OBVIL, Molière</a>
        </h1>
        <a class="logo" href="http://obvil.paris-sorbonne.fr/"><img class="logo" src="<?php echo $theme; ?>img/logo-obvil.png" alt="OBVIL"></a>
        <?php // liens de téléchargements
          // if ($doc['downloads']) echo "\n".'<nav id="downloads"><small>Télécharger :</small> '.$doc['downloads'].'</nav>';
        ?>
      </header>
      <div id="contenu">
        <div id="main">
          <nav id="toolbar">
            <nav class="breadcrumb">
            <?php 
            echo '<a href="' . Web::$basehref . '">Molière</a> » ';
            // nous avons un livre, glisser aussi les liens de téléchargement
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
    echo $pot->concBook($doc['bookid']);
  }
}
// pas de livre demandé, montrer un rapport général
else if($pot) {
  // nombre de résultats
  echo $pot->report();
  // présentation bibliographique des résultats
  echo $pot->biblio(array('author','title','date'));
  // concordance s’il y a recherche plein texte
  echo $pot->concByBook();
}
      ?>
         </div>
      </div>
      <aside id="aside">
        <p> </p>
          <?php
// livre
if (isset($doc['bookid'])) {
  if(isset($doc['download'])) echo "\n".'<nav id="download">' . $doc['download'] . '</nav>';
  echo "\n<nav>";
  // auteur, titre, date
  if ($doc['byline']) $doc['byline']=$doc['byline'].'<br/>';
  echo "\n".'<header><a href="' . web::$basehref . $doc['bookname'].'/">'.$doc['byline'].$doc['title'].' ('.$doc['end'].')</a></header>';
  // rechercher dans ce livre
  echo '
  <form action=".#conc" name="searchbook" id="searchbook">
    <input name="q" id="q" onclick="this.select()" class="search" size="20" placeholder="Rechercher dans ce livre" title="Rechercher dans ce livre" value="'. str_replace('"', '&quot;', $pot->q) .'"/>
    <input type="image" id="go" alt="&gt;" value="&gt;" name="go" src="'. $theme . 'img/loupe.png"/>
  </form>
  ';
  // table des matières
  echo '
          <div id="toolpan" class="toc">
            <div class="download">
               '.((isset($doc['download']))?$doc['download']:'').'
            </div>
            <div class="toc">
              '.$doc['toc'].'
            </div>
          </div>
  ';
  echo "\n</nav>";
}
// accueil ? formulaire de recherche général
else {
/*
  echo'
    <form action="" style="text-align:center">
      <input name="q" placeholder="Rechercher" class="text" value="'.str_replace('"', '&quot;', $pot->q).'"/>
      <button type="submit">Rechercher</button>
    </form>
  ';
*/
}
?>
      </aside>
      <span id="ruler"></span>
    </div>
    <script type="text/javascript" src="<?php echo $teipot; ?>Tree.js">//</script>
    <script type="text/javascript" src="<?php echo $teipot; ?>Sortable.js">//</script>
    
    <!-- Pour l'alignement des vers -->
    <script type="text/javascript">
    
    function getStringWidth(theString) {
    	var ruler = document.getElementById('ruler');
    	ruler.innerHTML=theString;
    	return ruler.offsetWidth;
    }
    
    
    (function() {
    // Cool! il y a juste des IE un peu paumés, mais tant pis , c’est trop simple http://quirksmode.org/dom/core/#t11
    var theVerses = document.getElementsByClassName('part-Y');
    var tempText;
    var theGoodPrevious;
    var idVerse;
    
    $(".part-Y").each(function() {
    	var sizeOf = getStringWidth($(this).prevAll(".l:first").html());
    	//var sizeOf = getStringWidth(test.prev(".l").html());
    	var tempText = "<span class=\"space\" style=\"width:" + sizeOf + "px\"></span>" + $(this).html();
    	$(this).html(tempText); })
    	
    	//for (var i = 0; i < theVerses.length; i++) {	
    	//	var theGoodPrevious = theVerses[i].prev(".l");
    	//	var sizeOf = getStringWidth(theGoodPrevious);
    	//	var tempText = "<span class=\"space\" style=\"width:" + sizeOf + "px\"></span>" + theVerses[i].innerHTML;
    	//	theVerses[i].innerHTML=tempText;
    	//}
    })();
    
    </script>
    <!-- Fin -->
  </body>
</html>
