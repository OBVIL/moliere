<?php
ini_set('display_errors', '1');
error_reporting(-1);
include(dirname(dirname(__FILE__))."/Teinte/Web.php");
include(dirname(dirname(__FILE__))."/Teinte/Base.php");
$basehref = Teinte_Web::basehref(); //
$teinte = $basehref."../Teinte/";

$path = Teinte_Web::pathinfo(); // document demandé
// chercher le doc dans la base
$branches = explode('/', $path);
$docid = end($branches);


// pièces de moliere
if (strpos($path, 'moliere') === 0 || strpos($path, 'theatre') === 0) {
  $conf = array(
    "playcode" => $docid, // relatif à la politique d’URL décidée ici
    "url" => Teinte_Web::basehref()."moliere/",
    "title" => "Molière",
    "sqlite" => "moliere.sqlite", // nom de la base sqlite
    "bibcols" => array('created', 'issued', 'title'),
    "abstract" => '<h1>Théâtre de Molière</h1>',
  );
  include (dirname(__FILE__).'/theatre.php');
  exit();
}
// pièces de devanciers et contemporains
if (strpos($path, 'contexte') === 0) {
  $conf = array(
    "playcode" => $docid, // relatif à la politique d’URL décidée ici
    "url" => Teinte_Web::basehref()."contexte/",
    "title" => "Devanciers et contemporains",
    "sqlite"=> "contexte.sqlite", // nom de la base sqlite
    "bibcols" => array('author', 'created', 'issued', 'title', 'publisher'),
    "abstract" => '
<div style="padding: 1em">
<h1>Devanciers et contemporains de Molière</h1>
<p class="noindent">Ce dossier présente des pièces de théâtre qui ne sont pas de Molière mais qui lui sont antérieures ou contemporaines, afin de les comparer avec les mêmes statistiques et interfaces. Ce ne sont pas des éditions du LABEX OBVIL, mais des textes prêtés par la <a href="http://bibdramatique.paris-sorbonne.fr/">Bibliothèque dramatique</a> (CELLF, Université Paris-Sorbonne) et le site <a href="http://theatre-classique.fr/pages/programmes/PageEdition.php">Théâtre Classique</a> (Paul Fièvre). L’OBVIL tient à remercier ces partenaires, et à leur laisser toute la paternité (et la responsabilité) de leurs éditions. Si un texte vous intéresse plus particulièrement, il vaut mieux le consulter sur son site d’origine. Sur la <a href="http://bibdramatique.paris-sorbonne.fr/">Bibliothèque dramatique</a>, vous trouverez une introduction critique, des notes, et différents formats pour la lecture (epub, mobi, pdf). Sur <a href="http://theatre-classique.fr/pages/programmes/PageEdition.php">Théâtre Classique</a>, vous trouverez bien d’autres pièces (plus de 800 en 2016), d’autres statistiques et formats. Cette compilation a été rendue possible par un effort de convergence sur le format des textes (XML/TEI) afin que les logiciels de chacun y trouvent leurs repères et leurs balises.</p>
</div>'."\n",
  );
  include (dirname(__FILE__).'/theatre.php');
  exit();
}
// pièces avec molière comme personnage
if (strpos($path, 'personnage') === 0) {
  $conf = array(
    "playcode" => $docid, // relatif à la politique d’URL décidée ici
    "url" => Teinte_Web::basehref()."personnage/",
    "title" => "Molière, personnage de théâtre",
    "sqlite"=> "moliere-personnage.sqlite", // nom de la base sqlite
    "bibcols" => array('author', 'created', 'issued', 'title', 'publisher'),
    "abstract" => '
<div style="padding: 1em">
<h1>Molière, personnage de théâtre</h1>
</div>
'."\n",
  );
  include (dirname(__FILE__).'/theatre.php');
  exit();
}



header('content-type: text/html; charset=utf-8');
if (!file_exists($f=dirname(__FILE__)."/conf.php")) {
  echo '<h1>Problème de configuration, fichier conf.php introuvable.</h1>';
}
else {
  $conf = include($f);
}

if (!file_exists($conf['sqlite'])) {
  echo '<h1>Première installation ? Allez voir la page <a href="pull.php">pull.php</a> pour transformer vos fichiers XML.</h1>';
  exit();
}
$base = new Teinte_Base($conf['sqlite']);
$query = $base->pdo->prepare("SELECT * FROM doc WHERE code = ?; ");
$query->execute(array($docid));
$doc = $query->fetch();

$q = null;
if (isset($_REQUEST['q'])) $q=$_REQUEST['q'];

?><!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title><?php
if($doc) echo $doc['title'].' — ';
echo $conf['title'];
    ?></title>
    <link rel="stylesheet" type="text/css" href="<?= $teinte ?>tei2html.css" />
    <link rel="stylesheet" type="text/css" href="<?= $basehref ?>../theme/obvil.css"/>
    <link rel="stylesheet" type="text/css" href="<?= $basehref ?>gongora.css" />
  </head>
  <body id="top">
    <div id="center">
      <header id="header">
        <h1><?php
          if (!$path && $base->search) {
            echo '<a href="?">'.$conf['title'].'</a>';
          }
          else if (!$path) {
            echo '<a class="home" href="'.$basehref.'?">Molière</a>';
          }
          else if (trim($path, "/") == "critique") {
            echo '<a href="'.$basehref.'">Molière, accueil</a>';
          }
          else {
            echo '<a href="'.$basehref.'critique/?'.$_COOKIE['lastsearch'].'">Critique moliéresque</a>';
          }
        ?></h1>
        <a class="logo" href="http://obvil.paris-sorbonne.fr/projets/projet-moliere"><img class="logo" src="<?php echo $basehref; ?>../theme/img/logo-obvil.png" alt="OBVIL"></a>
      </header>
      <div id="contenu">
        <aside id="aside">
          <?php
if ($doc) {
  echo '
<nav id="download"><small>Télécharger :</small>
    <a target="_blank" href="http://obvil.github.io/moliere/critique/'.$doc['code'].'.xml" title="Livre électronique">tei</a>,
    <a target="_blank" href="'.$basehref.'epub/'.$doc['code'].'.epub" title="Livre électronique">epub</a>,
    <a target="_blank" href="'.$basehref.'kindle/'.$doc['code'].'.mobi" title="Mobi, format propriétaire Amazon">kindle</a>,
    <a target="_blank" href="'.$basehref.'markdown/'.$doc['code'].'.md" title="Markdown">texte brut</a>,
    <a target="_blank" href="'.$basehref.'iramuteq/'.$doc['code'].'.txt" title="Markdown">iramuteq</a>,
    <a target="_blank" href="'.$basehref.'html/'.$doc['code'].'.html">html</a>.
</nav>';
// auteur, titre, date
  echo '
<header>
  <a class="title" href="' . $basehref . $doc['code'] . '">'.$doc['title'].'</a>
</header>
<form action="#mark1">
  <a title="Retour aux résultats" href="'.$basehref.'critique/?'.$_COOKIE['lastsearch'].'"><img src="'.$basehref.'../theme/img/fleche-retour-corpus.png" alt="←"/></a>
  <input name="q" value="'.str_replace('"', '&quot;', $base->p['q']).'"/><button type="submit">🔎</button>
</form>
';

  // table des matières, quand il y en a une
   if (file_exists($f="toc/".$doc['code']."_toc.html")) readfile($f);
}
// accueil ? formulaire de recherche général
else {
}
          ?>
        </aside>
        <div id="main">
          <nav id="toolbar">
            <?php
            ?>
          </nav>
          <div id="article" class="<?php echo $doc['class']; ?>">
            <?php
// page d’accueil
if (!$path) {
  echo '
  <h1>OBVIL - corpus Molière</h1>
  <div class="clear">
    <a href="./moliere" class="square couleur1">Théâtre</a>
    <a href="./critique/" class="square couleur2">Critique</a>
    <a href="./contexte/" class="square couleur3">Devanciers et contemporains</a>
    <a href="./personnage/" class="square couleur4">Molière personnage</a>
    <a href="http://obvil-dev.paris-sorbonne.fr/corpus/moliere/anecdotes/index.php" class="square couleur2">Anecdotes</a>
  </div>
  ';
}
else if ($doc) {
  $html = file_get_contents("article/".$doc['code']."_art.html");
  if ($q) echo $base->hilite($doc['id'], $q, $html);
  else echo $html;
}
else if ($base->search) {
  $base->biblio(array("no", "date", "author", "title", "occs"), "SEARCH");
}
// pas de livre demandé, page de couverture
else {
  $base->biblio(array("date", "author", "title"));
}
            ?>
            <a id="gotop" href="#top">▲</a>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript" src="<?= $teinte ?>Teinte.js">//</script>
    <script type="text/javascript" src="<?= $teinte ?>Tree.js">//</script>
    <script type="text/javascript" src="<?= $teinte ?>Sortable.js">//</script>
  </body>
</html>
