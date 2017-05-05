<table class="results" id="results">
<thead> 
<th class="anecdote">Anecdote</th>
<th class="book" >Auteur</th>
<th class="date" >Date</th>
<th class="content" >Contenu</th>
</thead>

<tbody>
<?php foreach($results as $result){ ?>
<tr>
<td class="div-open-document"><?php echo $result ['anecdote_title'];?><a href="<?php echo $result['book_path']; ?>#<?php echo $result['anecdote_id']; ?>" title="Afficher l'anecdote dans son contexte"> <i class="fa fa-file"></i></a> </td>
<td class="tooltip" title="<i><?php echo $result ['book_title'];?></i>"><?php echo $result ['lastname'];?></td>
<td><?php echo $result ['date'];?></td>
<td><?php echo $result ['content'];?></td>
</tr>
<?php } ?>
</tbody>


</table>