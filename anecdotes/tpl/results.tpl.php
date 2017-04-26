<table class="results">
<thead> 
<th class="anecdote">Titre de l'anecdote</th>
<th class="book" >Titre de l'ouvrage</th>
<th class="date" >Date</th>
<th class="content" >Contenu de l'anecdote</th>
</thead>

<tbody>

<tr <?php foreach($results as $result){?>>
<td
<?php foreach($results as $result){
	echo "<br/>";

	print_r($result ['anecdote_id']);
	}
?>
</td>
</tr>
</tbody>
</table>