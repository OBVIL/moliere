<?php
/**
 * Différents affichages pour l‘application Molière de l’OBVIL
 */
include(dirname(dirname(__FILE__)).'/Dramagraph/Biblio.php');
include(dirname(dirname(__FILE__)).'/Dramagraph/Net.php');
include(dirname(dirname(__FILE__)).'/Dramagraph/Table.php');
class Moliere
{
  public static $pdo;
  public static $qobj;
  public static $pathinfo;
  public static $basehref;

  /**
   * Constructeur
   */
  public function __construct($sqlitefile, $basepre='')
  {
    self::$pdo = new PDO('sqlite:'.$sqlitefile);
    self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    self::$qobj = self::$pdo->prepare("SELECT cont FROM object WHERE playcode = ? AND type = ?");
    if (isset($_SERVER['PATH_INFO'])) self::$pathinfo = $_SERVER['PATH_INFO'];
    //trim first slash of pathinfo
    self::$pathinfo = ltrim(self::$pathinfo, '/');
    self::$basehref = $basepre.str_repeat("../", substr_count(self::$pathinfo, '/'));
  }

  /**
   * Entête html pour piece
   * $basehref URL relative au contexte de l’application
   */
  static function dramahead()
  {
    $dramagraph = self::$basehref.'../Dramagraph/';
    echo '
      <meta charset="UTF-8" />
      <link rel="stylesheet" charset="utf-8" type="text/css" href="'.self::$basehref.'../Teinte/tei2html.css"/>
      <link rel="stylesheet" type="text/css" href="'.self::$basehref.'../theme/obvil.css" />
      <link rel="stylesheet" charset="utf-8" type="text/css" href="'.$dramagraph.'dramagraph.css"/>
      <script src="'.$dramagraph.'sigma/sigma.min.js">//</script>
      <script src="'.$dramagraph.'sigma/sigma.layout.forceAtlas2.min.js">//</script>
      <script src="'.$dramagraph.'sigma/sigma.plugins.dragNodes.min.js">//</script>
      <script src="'.$dramagraph.'sigma/sigma.exporters.image.min.js">//</script>
      <script src="'.$dramagraph.'Rolenet.js">//</script>
      <style>
  #article { padding: 0; }
  #graph { background: #F5F5F5; }
      </style>'."\n";
  }

  /**
   * Navigation par selecteur
   */
   static function select($playcode)
   {
     echo '
     <div style="position: fixed; margin-left: 45%; bottom: 10px;  ">
       <a href="#" class="but" title="Tête de page">▲</a>
     </div>';
     echo '<form action="#" onsubmit="var option = this.play.options[this.play.selectedIndex]; if (!option.value) this.action = \'.\'; else this.action = option.value; this.play.disabled = true; console.log(this.action); this.submit() ">'."\n";
     echo Dramagraph_Biblio::select(Moliere::$pdo, $playcode);
     echo '</form>'."\n";
   }
  /**
   * Foot scripts
   */
  static function foot()
  {
    echo '
    <script type="text/javascript" src="'.self::$basehref.'../Teinte/Tree.js">//</script>
    <script type="text/javascript" src="'.self::$basehref.'../Teinte/Sortable.js">//</script>'."\n";
  }

  /**
   * Texte de la pièce
   */
  static function text($playcode)
  {
    self::$qobj->execute(array($playcode, 'graph'));
    echo current(self::$qobj->fetch(PDO::FETCH_NUM));
    self::$qobj->execute(array($playcode, 'article'));
    $res = self::$qobj->fetch(PDO::FETCH_NUM);
    echo '<div style="padding: 0 20px 50px 45px; ">'.$res[0].'</div>';
    self::$qobj->execute(array($playcode, 'roles'));
    echo current(self::$qobj->fetch(PDO::FETCH_NUM));
    self::$qobj->execute(array($playcode, 'relations'));
    echo current(self::$qobj->fetch(PDO::FETCH_NUM));
  }

}

?>
