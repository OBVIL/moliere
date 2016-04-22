<table>
	<thead>
		<th>
		</th>
		<?php foreach($books as $bookId=>$book){?>
			<th>
				<?php echo $book["author"] ?>
				<?php echo $book["title"] ?>
				<?php echo $book["date"] ?>
			</th>
		<?php }?>
	</thead>
	<tbody>
		<?php foreach($anecdotes as $anecdoteId=>$anecdote){?>
			<tr>
				<td>
					<?php echo $anecdote["description"] ?>
				</td>
				<td>
					<?php echo $anecdote["dateFirstOccurrence"] ?>
				</td>
				<td>
					<?php echo $anecdote["dateLastOccurrence"] ?>
				</td>
				<td>
					<?php echo $anecdote["numberOccurrences"] ?>
				</td>
				<?php foreach($books as $bookId=>$book){?>
					<td>
						<?php echo $anecdote["books"][$bookId]["commentBefore"] ?>
						<?php echo $anecdote["books"][$bookId]["content"] ?>
						<?php echo $anecdote["books"][$bookId]["commentAfter"] ?>
					</td>
				<?php }?>
			</tr>
		<?php }?>
	</tbody>
</table>