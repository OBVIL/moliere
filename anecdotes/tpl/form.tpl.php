<form method="post" >

<div class="field">
<p>Rechercher</p>
<select name="content_type">
<option value="anecdote">Dans l'anecdote</option>
<option value="commentaire">Dans le commentaire</option>
<option value="les-deux">Dans les deux</option>
</select>
<input type="text" name="content"/>
</div>

<div class="field">
<p class="form-title">Titre de l'anecdote</p>
<select name="anecdotes[]" multiple>
<option value=""></option>
<?php include 'anecdotes.html';?>
</select>
</div>

<div class="field">
<p>Titre de l'ouvrage</p>
<select name="books[]" multiple>
<option value=""></option>
<?php include 'books.html';?>
</select>
</div>

<div class="field">
<p>Mots clés</p>
<select name="keywords[]" multiple>
<option value=""></option>
<?php include 'keywords.html';?>
</select>
</div>

<div class="field">
<p>De</p>
<input type="text date" placeholder="AAAA" maxlength="4" value="1705" name="after"/>
<p>à</p>
<input type="text date" placeholder="AAAA" maxlength="4" value="1919" name="before"/>
</select></br>
<input type="hidden" name="post"/>
<input type="submit" value="Valider" />
</div>

</form>
