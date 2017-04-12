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
                                        <div class="div-open-document"><a href="<?php echo $book["url"]; ?>" target="_blank" class="tooltip open-document" title="Afficher le texte complet de l'ouvrage"><i class="fa fa-file"></i></a></div>
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
                                        <?php foreach($books as $bookId=>$book){ $empty = ($anecdote["books"][$bookId]["content"]) ? "not-empty" : "empty";  ?>  
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