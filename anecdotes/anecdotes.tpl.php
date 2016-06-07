<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <script type="text/javascript" src="jquery-2.2.3.min.js"></script>
        <script type="text/javascript" src="jquery.hoverIntent.js"></script>
        <script type="text/javascript" src="jquery-ui-1.11.4/jquery-ui.min.js"></script>
        <script type="text/javascript" src="tipsy/src/javascripts/jquery.tipsy.js"></script>
        <script type="text/javascript" src="fancybox/source/jquery.fancybox.js"></script>
        <script type="text/javascript" src="TableFilter/dist/tablefilter/tablefilter.js"></script>
        <script type="text/javascript" src="anecdotes.js"></script>
        <link rel="stylesheet" charset="utf-8" type="text/css" href="style.css"/>
        <link rel="stylesheet" href="fancybox/source/jquery.fancybox.css" type="text/css" />
        <link rel="stylesheet" href="tipsy/src/stylesheets/tipsy.css" type="text/css" />
        <link rel="stylesheet" href="font-awesome/css/font-awesome.css" type="text/css" />
        <title>Anecdotes</title>
    
    </head>
    <body>

	<div style="display: none">
		<div id="info" style="width:1000px;height:800px;overflow:auto;">
                    <h2>Anecdotes relatives à Molière</h2>
                    <p>Base de donnée élaborée par Élodie Bénard dans le cadre du projet <a href="http://obvil.paris-sorbonne.fr/projets/projet-moliere"><i>Molière</i></a> dirigé par Georges Forestier et Florence Naugrette (labex OBVIL)</p>
                    <p>Développement : Marc Douguet & Vincent Jolivet</p>
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
        <a id="trigger-info" href="#info" class="tooltip" title="Aide et présentation du projet"><i class="fa fa-question"></i></a>
        <a id="view-matrix" class="tooltip" title="Afficher la vue synthétique" style="display: none;"><i class="fa fa-compress"></i></a>
        <a id="view-detail" class="tooltip" title="Afficher toutes les anecdotes"><i class="fa fa-expand"></i></a>
       <table class="anecdotes matrix" id="anecdotes">
                <thead>
                        <tr class="book-author">
                            <th class="no-border th-anecdote-title">
                                <a class="tooltip" title="Classer par titre"><i class="fa fa-sort"></i></a>
                            </th>
                             <th class="no-border th-count">
                                <a class="tooltip" title="Classer par nombre d'occurrences"><i class="fa fa-sort"></i></a>
                            </th>
                             <th class="no-border th-count">
                                <a class="tooltip" title="Classer par auteur de la première occurrence"><i class="fa fa-sort"></i></a>
                            </th>
                        <th class="no-border th-count">
                                <a class="tooltip" title="Classer par date de première occurrence"><i class="fa fa-sort"></i></a>
                            </th>
                        <?php foreach($books as $bookId=>$book){?>
                                    <th id="<?php echo $bookId; ?>" class="book-author inactive book <?php echo $bookId; ?> tooltip" title="<?php echo $book["author"]; ?>, <i><?php echo $book["title"]; ?></i>">
                                        <div class="th-container">
                                            <span><?php echo $book["authorLastName"]; ?></span>
                                        </div>
                                        <div class="div-open-document"><a href="../critique/<?php echo $bookId; ?>" target="_blank" class="tooltip open-document" title="Afficher le texte complet de l'ouvrage"><i class="fa fa-file"></i></a></div>
                                    </th>
                            <?php }?>
                        </tr>

                        <tr class="book-date">
                            <th class="no-border">
                            </th>
                             <th class="no-border">
                            </th><th class="no-border">
                            </th>
                             <th class="no-border">
                            </th>
                            <?php foreach($books as $bookId=>$book){?>
                                    <th class="book-date book <?php echo $bookId; ?>">
                                        <div class="th-container">
                                            <span><?php echo $book["date"]; ?></span>

                                        </div>
                                    </td>
                            <?php }?>
                        </tr>
                        <tr class="anecdotes-count">
                            <th class="no-border">
                            </th>
                             <th class="no-border">
                            </th>
                             <th class="no-border">
                            </th>
                             <th class="no-border">
                            </th>
                            <?php foreach($books as $bookId=>$book){?>
                                    <th class="anecdotes-count book <?php echo $bookId; ?> tooltip" title="Nombre d'anecdotes">
                                       
                                            <span><?php echo $book["anecdotesCount"]; ?></span>

                                    </td>
                            <?php }?>
                        </tr>                        
                </thead>
                <tbody>
                        <?php foreach($anecdotes as $anecdoteId=>$anecdote){?>
                                <tr class="anecdote <?php echo $anecdoteId ?>" id="<?php echo $anecdoteId ?>">
                                        <td class="anecdote-title anecdote inactive <?php echo $anecdoteId ?> tooltip" title="<?php echo $anecdote["title"] ?>">
                                                <span class="short-title"><?php echo $anecdote["short-title"] ?></span>
                                                <span class="title" style="display: none;"><?php echo $anecdote["title"] ?></span>                                                
                                        </td>
                                        <td class="count <?php echo $anecdoteId ?> tooltip" title="Nombre d'occurrences">
                                            <?php echo count($anecdote["books"]); ?>
                                        </td>
                                        <td class="first-author <?php echo $anecdoteId ?> tooltip" title="Première occurrence dans <?php echo $anecdote["first"]["author"]; ?>, <i><?php echo $anecdote["first"]["title"]; ?></i>">
                                            <?php echo $anecdote["first"]["authorLastName"]; ?>
                                        </td>
                                        <td class="first-date <?php echo $anecdoteId ?> tooltip" title="Date de la première occurrence">
                                            <?php echo $anecdote["first"]["date"]; ?>
                                        </td>
        <!--				<td>
                                                <?php // echo $anecdote["dateFirstOccurrence"] ?>
                                        </td>
                                        <td>
                                                <?php // echo $anecdote["dateLastOccurrence"] ?>
                                        </td>
                                        <td>
                                                <?php // echo $anecdote["numberOccurrences"] ?>
                                        </td>-->
                                        <?php foreach($books as $bookId=>$book){
                                            $empty = ($anecdote["books"][$bookId]["content"]) ? "not-empty" : "empty";
                                            ?>
                                                
                                                <td class="<?php echo $bookId; ?> <?php echo $anecdoteId ?> anecdote-content book anecdote inactive out <?php echo $empty; ?>">
                                                        <div class="container" style="display: none;">
                                                            <div class="comment-before comment" style="display: none;"><p><?php echo $anecdote["books"][$bookId]["commentBefore"] ?></p></div>
                                                            <div class="core"><p><?php echo $anecdote["books"][$bookId]["content"] ?></p></div>
                                                            <div class="comment-after comment" style="display: none;"><p><?php echo $anecdote["books"][$bookId]["commentAfter"] ?></p></div>
                                                            <?php if($empty=="not-empty"){ ?><a href="../critique/<?php echo $bookId; ?>.xml#<?php echo $anecdoteId ?>" target="_blank" class="tooltip open-anecdote" title="Afficher l'anecdote dans son contexte"><i class="fa fa-file"></i></a><?php } ?>
                                                        </div>
                                                </td>
                                        <?php }?>
                                </tr>
                        <?php }?>
                </tbody>
        </table>        
    </body>
</html>
