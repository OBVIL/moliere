<?php
/**
 * Visualisation de théâtre
 */

include(dirname(__FILE__).'/Moliere.php');
new Moliere(dirname(__FILE__).'/'.$conf['sqlite']);

$sql = "SELECT * FROM play WHERE code = ".Moliere::$pdo->quote($conf['playcode']);
$play = Moliere::$pdo->query($sql)->fetch();
if (!$play) $conf['playcode'] = null;
?><!DOCTYPE html>
<html>
  <head>
    <?php
    if (isset($play)) echo '<title>'.$play['title'].', Molière, LABEX OBVIL</title>';
Moliere::dramahead();
if (!$conf['playcode']) {
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
        <h1><?php
          if (!$conf['playcode']) {
            echo '<a href="'.$basehref.'">Molière, accueil</a>';
          }
          else{
            echo '<a href="'.$conf['url'].'">'.$conf['title'].'</a>';
          }
        ?></h1>
        <a class="logo" href="http://obvil.paris-sorbonne.fr/projets/projet-moliere"><img class="logo" src="<?php echo $basehref; ?>../theme/img/logo-obvil.png" alt="OBVIL"></a>
      </header>
      <div id="contenu">
        <div id="main">
          <div id="article">
      <?php
if (!$conf['playcode']) {
  echo $conf['abstract'];
  if (!isset($conf['bibcols'])) $conf['bibcols']=null;
  Dramagraph_Biblio::table(Moliere::$pdo, $conf['bibcols']);
}
else {
  echo '<h1>'.$play['author']."<br/>".$play['title'].'</h1>';
  if (strpos($play['identifier'], 'bibdramatique.paris-sorbonne.fr') !== false) {
    echo '<p class="noindent">Retrouvez cette pièce sur la <a target="_blank" href="'.$play['identifier'].'">Bibliothèque dramatique</a>, avec son introduction et son apparat critique.</p>
    <p> </p>';
  }
  if (strpos($play['identifier'], 'theatre-classique.fr') !== false) {
    echo '<p class="noindent">Retrouvez cette pièce sur <a target="_blank" href="'.$play['identifier'].'">Théâtre Classique</a>, avec 800 autres textes, et leurs outils.</p>
    <p> </p>';
  }
  echo '
<details>
  <summary>Graphe d’interlocution <i>(cliquez ici pour plus d’explications)</i></summary>
  <p>Ce graphe est généré automatiquement à partir du texte balisé de la pièce de théâtre. Chaque pastille est un personnage, dont la taille est proportionnelle à la quantité de paroles qui lui sont attribuées. Les flèches indiquent à qui s’adresse ces paroles. Le placement des pastilles résulte d’un algorithme automatique cherchant à éviter les croisements entre les flèches. Jouer avec les boutons ci-dessous, notamment le mélange aléatoire (♻) et la relance de l’algorithme (►), permet de mieux saisir ce qui est arbitraire, ou déterminé par le poids des paroles, dans la disposition relative des pastilles. Les couleurs sont des convenances facilitant la lecture, elles résultent d’une combinatoire entre sexe, âge, et statut des personnages. Retrouvez <a href="#tables">ci-dessous</a> les tables de données avec lesquelles l’image est produite.</p>
</details>

  ';
  Moliere::text($conf['playcode']);
}
      ?>
          <p> </p>
         </div>
      </div>
      <aside id="aside">
          <?php
// pièce de Théâtre
if($conf['playcode']) {
  echo "\n".'<nav id="download"><small>Télécharger :</small>
  <a target="_blank" href="http://dramacode.github.io/epub/'.$conf['playcode'].'.epub" title="Livre électronique">epub</a>,
  <a target="_blank" href="http://dramacode.github.io/kindle/'.$conf['playcode'].'.mobi" title="Mobi, format propriétaire Amazon">kindle</a>,
  <a target="_blank" href="http://dramacode.github.io/markdown/'.$conf['playcode'].'.txt" title="Markdown">texte brut</a>,
  <a target="_blank" href="http://dramacode.github.io/naked/'.$conf['playcode'].'.txt" title="Texte dit sans didascalies ou titres structurants">paroles</a>,
  <a target="_blank" href="http://dramacode.github.io/iramuteq/'.$conf['playcode'].'.txt">iramuteq</a>,
  <a target="_blank" href="http://dramacode.github.io/html/'.$conf['playcode'].'.html">html</a>.
  </nav>';
  echo '<p> </p>';
  Moliere::$qobj->execute(array($conf['playcode'], 'charline'));
  echo  current(Moliere::$qobj->fetch(PDO::FETCH_ASSOC)) ;
  echo '<p> </p>';
}
        ?>
        </aside>
      </div>
    </div>
    <?php Moliere::foot();  ?>
    <a id="gotop" href="#top">▲</a>
  </body>
</html>
