<form method="post" >

<p>Rechercher</p>
<select name="content type">
<option value="anecdote">Dans l'anecdote</option>
<option value="commentaire">Dans le commentaire</option>
<option value="les-deux">Dans les deux</option>
</select>
<input type="text" name="content"/>
<p>Anecdote</p>
<select name="anecdote">
<?php include 'anecdotes.html';?>
</select></br>
<p>Source</p>
<select name="book">
<?php include 'books.html';?>
</select></br>
<p>Mots clés</p>
<select name="keyword">
<?php include 'keywords.html';?>
</select></br>
<p>De</p>
<input type="text date" placeholder="AAAA" maxlength="4" value="1705"/>
<p>à</p>
<input type="text date" placeholder="AAAA" maxlength="4" value="1919"/>
</select></br>
<input type="submit" value="Valider" />
</form>
