<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
        <script type="text/javascript" src="js/jquery.hoverIntent.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.11.4/jquery-ui.min.js"></script>
        <script type="text/javascript" src="js/tipsy/src/javascripts/jquery.tipsy.js"></script>
        <script type="text/javascript" src="js/fancybox/source/jquery.fancybox.js"></script>
        <script type="text/javascript" src="js/TableFilter/dist/tablefilter/tablefilter.js"></script>
        <script type="text/javascript" src="js/anecdotes.js"></script>
        <link rel="stylesheet" charset="utf-8" type="text/css" href="css/style.css"/>
        <link rel="stylesheet" href="js/fancybox/source/jquery.fancybox.css" type="text/css" />
        <link rel="stylesheet" href="js/tipsy/src/stylesheets/tipsy.css" type="text/css" />
        <link rel="stylesheet" href="js/font-awesome/css/font-awesome.css" type="text/css" />
        <title>Anecdotes</title>
    </head>
    <body>
        <div style="display: none">
                <div id="info" style="width:1000px;height:800px;overflow:auto;">
                    <h2>Anecdotes sur Molière</h2>
                    <p>Base de données et interface élaborées par Élodie Bénard et Marc Douguet (avec l'aide de Vincent Jolivet) dans le cadre du projet <a href="http://obvil.paris-sorbonne.fr/projets/projet-moliere"><i>Molière</i></a> dirigé par Georges Forestier et Florence Naugrette (labex OBVIL)</p>
                    <ul>
                        <li>Passer la souris sur une cellule pour afficher le texte de l'anecdote</li>
                        <li>Passer la souris sur le titre d'une anecdote pour afficher sa description complète</li>
                        <li>Passer la souris sur le nom d'un auteur pour afficher la référence de l'ouvrage</li>
                        <li>Cliquer sur le texte d'une anecdote pour afficher les commentaires</li>
                        <li>Cliquer sur le titre d'une anecdote pour afficher le texte de toutes les versions</li>
                        <li>Cliquer sur le nom d'un auteur pour afficher le texte de toutes les anecdotes présentes dans l'ouvrage</li>
                    </ul>
                </div>
        </div>
        <div class="header">
            <a href="http://obvil.paris-sorbonne.fr" class="logo"><img src="http://obvil.paris-sorbonne.fr/sites/default/files/logo.png"></a>
            <h1 class="page-title">Anecdotes sur Molière</h1>
            <a id="trigger-info" href="#info" class="tooltip" title="Aide et présentation du projet"><i class="fa fa-question"></i></a>
            <a id="view-matrix" class="tooltip" title="Afficher la vue synthétique" style="display: none;"><i class="fa fa-compress"></i></a>
            <a id="view-detail" class="tooltip" title="Afficher toutes les anecdotes"><i class="fa fa-expand"></i></a>
        </div>
        <?php
            include ("functions/functions.php");
            include ("functions/db.php");
            include ("functions/get_all.php");
            include ("functions/search.php");
            $db = connect();
            include ("tpl/form.tpl.php");
            if (isset($_POST["post"])) {
                $results = search($db);

                include("tpl/results.tpl.php");
            } else {
                $data = get_all($db);
                $books = $data[0];
                $anecdotes = $data[1];
                include ("tpl/table.tpl.php");
            }
        ?>
    </body>
</html>
