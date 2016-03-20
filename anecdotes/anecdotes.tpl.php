<?php
	//lien vers le texte de l'anecdote : transformer les <milestones@xml:id/> en <???@id/> dans tei2html
foreach($anecdotes as $anecdote) {?>
	<div type="anecdote" copyOf="<?php echo $anecdote["id"]; ?>" xml:id="<?php echo $bookId; ?>_<?php echo $anecdote["id"]; ?>">
	<head>
		<ref target="../critique/<?php echo $bookId; ?>#<?php echo $anecdote["id"]; ?>"><?php echo $anecdote["id"]; ?></ref>
	</head>
	<?php foreach($comments as $comment) {	
		if($comment["id"] == $anecdote["id"] && $comment["offset"]<$anecdote["offset"]) {?>
		<p><?php echo teiClean($comment["text"]);?></p>
	<?php }} ?>
		<p rend="b"><?php echo teiClean($anecdote["text"]);?></p>
	<?php foreach($comments as $comment) {
		if($comment["id"] == $anecdote["id"] && $comment["offset"]>$anecdote["offset"]) {?>
		<p><?php echo teiClean($comment["text"]);?></p>
	<?php }}?>
	</div>
<?php } ?>