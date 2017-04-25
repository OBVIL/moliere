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
<option value=""></option>
<?php include 'anecdotes.html';?>
</select>
<p>Source</p>
<select name="book">
<option value=""></option>
<?php include 'books.html';?>
</select>
<p>Mots clés</p>
<select name="keyword">
<option value=""></option>
<?php include 'keywords.html';?>
</select>
<p>De</p>
 
<input type="text date" placeholder="AAAA" maxlength="4" value="1705" name="date"/>
<p>à</p>
<input type="text date" placeholder="AAAA" maxlength="4" value="1919" name="date"/>
</select></br>
<input type="submit" value="Valider" />
</form>
