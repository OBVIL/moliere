<?php
/**
 * Pilote de l’application OBVIL-Molière
 * La logique applicative est libre
 * Le design est contraint par obvil.css
 */
include (dirname(__FILE__).'/../teipot/Teipot.php');
$path = Web::pathinfo(); // path demandé
$pot = false;
$q = ""; // requête en cours

// les pièces commencent par moliere…
if (strpos($path, 'moliere') === 0) {
  list($playcode) = explode('/', $path);
  $pdomol = new PDO('sqlite:'.dirname(__FILE__).'/moliere.sqlite');
  $pdomol->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
  $qobj = $pdomol->prepare("SELECT * FROM object WHERE playcode = ? AND type = ?");
  $play = $pdomol->query("SELECT * FROM play WHERE code = ".$pdomol->quote($playcode))->fetch();
  $moliere = true;
}
// ou alors on charge de la critique sous critique/…, 
else if (strpos($path, 'critique/') === 0) {
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

$teinte = Web::basehref().'../Teinte/'; // chemin css, js ; basehref est le nombre de '../' utile pour revenir en racine du site
$theme = Web::basehref().'../theme/'; // autres ressources spécifiques


?><!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" charset="utf-8" type="text/css" href="<?php echo $teinte; ?>tei2html.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $theme; ?>obvil.css" />
    <style>
/* couleur des rôles par genre */
.charline .role { background-color: rgba(192, 192, 192, 0.7); color: rgba(0, 0, 0, 0.5); }
.charline .female { background-color: rgba(255, 0, 0, 0.5); color: rgba(255, 255, 255, 1);}
.charline .female.junior { background-color: rgba(255, 64, 128, 0.3); color: rgba(0, 0, 0, 0.7);}
.charline .female.inferior { background-color: rgba(192, 96, 96, 0.4); color: rgba(255, 255, 255, 1);}
.charline .female.veteran { background-color: rgba(128, 0, 0, 0.4); color: rgba(255, 255, 255, 1);}
.charline .male { background-color: rgba(0, 0, 255, 0.4); color:  rgba(255, 255, 255, 1);}
.charline .male.junior { background-color: rgba(0, 192, 255, 0.2); color: rgba(0, 0, 0, 0.7);}
.charline .male.inferior { background-color: rgba(96, 96, 192, 0.3); color: rgba(255, 255, 255, 1);}
.charline .male.veteran { background-color: rgba(0, 0, 128, 0.4); color:  rgba(255, 255, 255, 1);}
.charline .male.superior { background-color: rgba(0, 0, 255, 0.6); color:  rgba(255, 255, 255, 1);}

    </style>
    <?php 
if (isset($moliere)) {
  echo '
    <style>
#article { padding: 0; }
#aside { padding-top: 3px; } 
    </style>
    <script src="sigma/sigma.min.js">//</script>
    <script src="sigma/sigma.layout.forceAtlas2.min.js">//</script>
    <script src="sigma/sigma.plugins.dragNodes.min.js">//</script>
    <script src="Rolenet.js">//</script>
  ';
}
if(isset($doc['head'])) echo $doc['head']; 
else echo '
<title>OBVIL, Molière</title>
';
    ?>
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
        if (isset($moliere)) {

              echo ' 
<form name="net" style="position: fixed; z-index: 3; top: 2px; left: 492px; " action="#">
<select name="play" onchange="this.form.action = this.options[this.selectedIndex].value+\'#graph\'; this.form.submit()">'."\n";
echo "  <option>  </option>\n";
foreach ($pdomol->query("SELECT * FROM play ORDER BY author, year") as $row) {
  if ($row['code'] == $playcode) $selected=' selected="selected"';
  else $selected = "";
  echo '<option value="'.$row['code'].'"'.$selected.'>'.bibl($row)."</option>\n";
}
echo '</select>
<a href="#" class="but">▲</a>
</form>';

            }
        ?>
      </header>
      <div id="contenu">
        <div id="main">
          <nav id="toolbar">
            <nav class="breadcrumb">
            <?php 

            if (isset($critique)) echo '<a href="' . Web::basehref() . 'critique/' . $pot->qsa(null, null, '?') . '">Critique moliéresque</a> &gt; ';
            if (isset($doc['breadcrumb'])) echo $doc['breadcrumb']; 
            ?>
            </nav>
          </nav>
          <div id="article">
      <?php
if (isset($moliere) && !$play) {
  echo '
<table class="sortable">
  <tr><th>Date</th><th>Titre</th></tr>
';
  foreach ($pdomol->query("SELECT * FROM play ORDER BY author, year") as $play) {
    echo '<tr><td>'.$play['year'].'</td><td><a href="'.$play['code'].'">'.$play['title']."</a></td></tr>\n";
  }
  echo "\n</table>\n";
}
else if (isset($moliere)) {
  echo '
        <div id="graph" style="height: 500px; position: relative; background: #FFFFFF; ">

          <div style="position: absolute; bottom: 0; right: 0; z-index: 2; ">
            <button class="mix but" type="button" title="Mélanger le graphe">♻</button>
            <button class="grav but" type="button" title="Démarrer ou arrêter la gravité">►</button>
          </div>
        </div>
      <script>
var data =';  
$qobj->execute(array($playcode, 'sigma'));
$res = $qobj->fetch(PDO::FETCH_ASSOC);
echo $res['cont'];
echo ' var graph1 = new Rolenet("graph", data, "sigma/worker.js"); // 
      </script>';
  $qobj->execute(array($playcode, 'article'));
  $res = $qobj->fetch(PDO::FETCH_ASSOC);
  echo '<div style="padding: 0 20px 50px 45px; ">'.$res['cont'].'</div>';
}
else if (isset($doc['body'])) {
  echo $doc['body'];
  // page d’accueil d’un livre avec recherche plein texte, afficher une concordance
  if ($pot && $pot->q && (!$doc['artname'] || $doc['artname']=='index')) {
    echo $pot->concBook($doc['bookrowid']);
  }
}
// pas de livre demandé, montrer un rapport général
else if($pot) {
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
// 
if(isset($moliere)) {
  $qobj->execute(array($playcode, 'charline'));
  $res = $qobj->fetch(PDO::FETCH_ASSOC);
  echo $res['cont'];
  echo '<p> </p>';
}
// livre
else if (isset($doc['bookrowid'])) {
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
else if(isset($moliere) || isset($critique)) {
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
    <!--
    <script type="text/javascript" src="<?php echo $teinte; ?>Tree.js">//</script>
    <script type="text/javascript" src="<?php echo $teinte; ?>Sortable.js">//</script>
-->
  </body>
</html>
<?php
/**
 * Ligne bibliographique pour une pièce
 */
function bibl($play) {
  if (is_string($play)) {
    $playcode = $this->pdo->quote($playcode);
    $play = $this->pdo->query("SELECT * FROM play WHERE code = $playcode")->fetch();
  }
  $bibl = $play['author'].', '.$play['title'];
  $meta = array();
  if ($play['year']) $meta[] = $play['year'];
  if ($play['genre'] == 'tragedy') $meta[] = 'tragédie';
  else if ($play['genre'] == 'comedy') $meta[] = 'comédie';
  if ($play['acts']) $meta[] = $play['acts'].(($play['acts']>2)?" actes":" acte");
  $meta[] = (($play['verse'])?"vers":"prose");
  if (count($meta)) $bibl .= " (".implode(", ", $meta).")";
  return $bibl;
}
  
$pdomol = null;
?>