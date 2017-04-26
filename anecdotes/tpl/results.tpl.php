<table class="results">
<thead> 
<th class="anecdote">Titre de l'anecdote</th>
<th class="book" >Titre de l'ouvrage</th>
<th class="date" >Date</th>
<th class="content" >Contenu de l'anecdote</th>
</thead>

<tbody>
<tr class="result" >
<?php foreach($results as $result){?>
<td class="anecdote <?php 
	echo "<br/>";
	
	print_r($result ['anecdote_id']);
	}?>"
	>
</td>
<?php foreach($results as $result){?>
<td class="book <?php 
	echo "<br/>";

	print_r($result ['book_id']);
	}?>">
</td>
<?php foreach($results as $result){?>
<td class="date <?php 
	echo "<br/>";

	print_r($result['date']);
	}?>">
</td>
<?php foreach($results as $result){?>
<td class="content <?php 
	echo "<br/>";

	print_r($result ['content']);
	}?>">
</td>
</tr>
</tbody>
</table>