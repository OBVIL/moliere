<table class="results">
<thead> 
<th class="anecdote">Titre de l'anecdote</th>
<th class="book" >Titre de l'ouvrage</th>
<th class="date" >Date</th>
<th class="content" >Contenu de l'anecdote</th>
</thead>

<tbody>
<tr class="result">

<td class="anecdote <?php foreach($results as $result){
	echo "<br/>";
	
	print_r($result ['anecdote_id']);
	}?>">
</td>
<td class="book <?php foreach($results as $result){
	echo "<br/>";

	print_r($result ['book']);
	}?>">
</td>
<td class="date <?php foreach($results as $result){
	echo "<br/>";

	print_r($result['date']);
	}?>">
</td>
<td class="content <?php foreach($results as $result){
	echo "<br/>";

	print_r($result ['content']);
	}?>">
</td>
</tr>
</tbody>
</table>