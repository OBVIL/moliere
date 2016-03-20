<?php
	//auteur, titre, date
foreach($bookIds as $bookId){
	$bookPath = '../critique/'.$bookId.'.xml';
	if(!is_file($bookPath)){continue;}
	$book = new DOMDocument();
	$book->load($bookPath);
	$bookTitle = $book->getElementsByTagName("title");
	$bookTitle = $bookTitle->length>0 ? $bookTitle->item(0)->textContent : '';
	
	$bookAuthor = $book->getElementsByTagName("author");
	$bookAuthor = $bookAuthor->length>0 ? $bookAuthor->item(0)->textContent : '';
	
	$bookDate = $book->getElementsByTagName("creation");
	$bookDate = $bookDate->length>0 ? $bookDate->item(0)->getElementsByTagName("date") : false;
	$bookDate = $bookDate->length>0 ? $bookDate->item(0)->getAttribute("when") : '';
	
	$book = file_get_contents($bookPath);
	$anecdotePattern = '|<milestone type="anecdoteStart" xml:id="([^"]+)"/>(.*?)<milestone type="anecdoteEnd"/>|';
	$commentPattern = '|<milestone type="commentStart" corresp="#([^"]+)"/>(.*?)<milestone type="commentEnd"/>|';
	$anecdotes = array();
	$comments = array();
	if(!preg_match_all($anecdotePattern, $book, $anecdotes, PREG_OFFSET_CAPTURE)){continue;}
// 	preg_match_all($anecdotePattern, $book, $anecdotes, PREG_OFFSET_CAPTURE);
	preg_match_all($commentPattern, $book, $comments, PREG_OFFSET_CAPTURE);
	
	$anecdotes = combine($anecdotes);
	$comments = combine($comments);
?>
	<div type="book" xml:id="<?php echo $bookId; ?>">
		<head><?php echo $bookAuthor; ?>, <ref target="../critique/<?php echo $bookId; ?>"><?php echo $bookTitle; ?></ref>, <?php echo $bookDate; ?></head>
		<?php include("anecdotes.tpl.php");?>
	</div>
<?php } ?>