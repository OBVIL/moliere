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
<td> <?php echo $result ['anecdote_id'];?></td>
<td><?php echo $result ['book_id'];?></td>
<td><?php echo $result ['date'];?></td>
<td><?php echo $result ['content'];?></td>
</tr>
<?php } ?>
</tbody>


</table>