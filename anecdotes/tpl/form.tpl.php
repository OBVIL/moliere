<form method="post" >

<h1>Rechercher</h1>
<select name="content_type">
<option value="anecdote">Dans l'anecdote</option>
<option value="commentaire">Dans le commentaire</option>
<option value="les-deux">Dans les deux</option>
</select>
<input type="text" name="content"/>

<h2>Titre de l'anecdote</h2>
<select name="anecdotes[]" multiple>
<option value=""></option>
<?php include 'anecdotes.html';?>
</select>

<h3>Titre de l'ouvrage</h3>
<select name="books[]" multiple>
<option value=""></option>
<?php include 'books.html';?>
</select>

<h4>Mots clés</h4>
<select name="keywords[]" multiple>
<option value=""></option>
<?php include 'keywords.html';?>
</select>

<h5>De</h5>
<input type="text date" placeholder="AAAA" maxlength="4" value="1705" name="after"/>
<p>à</p>
<input type="text date" placeholder="AAAA" maxlength="4" value="1919" name="before"/>
</select></br>
<input type="hidden" name="post"/>
<input type="submit" value="Valider" />
</form>
