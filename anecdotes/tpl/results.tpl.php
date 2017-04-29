<table class="results">
<thead> 
<th class="anecdote">Titre de l'anecdote</th>
<th class="book" >Titre de l'ouvrage</th>
<th class="date" >Date</th>
<th class="content" >Contenu de l'anecdote</th>
</thead>

<tbody>
<?php foreach($results as $result){ ?>
<tr>
<td class="div-open-document"><a href="<?php echo $book["path"]; ?>#<?php echo $result["anecdote-title"]; ?>" title="Retour au texte"> <?php echo $result ['anecdote_title'];?></td>
<td><?php echo $result ['book_title'];?></td>
<td><?php echo $result ['date'];?></td>
<td><?php echo $result ['content'];?></td>
</tr>
<?php } ?>
</tbody>


</table>