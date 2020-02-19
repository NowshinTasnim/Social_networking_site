<?php
	
	
	
	echo "
		<div class='postarea fix'>
			<div class='posthead fix'>
				CREATE POST
			</div>
			<form name='postform' method='POST' enctype='multipart/form-data'>
				<textarea type='text' id='posttext' name='posttext' rows='5' cols='88' class='post_text fix' placeholder='What is on your mind?'></textarea>
				<div class='postfooter fix'>
					<input class = 'filestyle photo_up fix' data-classIcon = 'icon-plus' data-buttonText = 'Photos' type = 'file' name = 'photo' />
					<input class='btn_post fix' type='submit' name='post' value='Post' />
				</div>
			</form>
		</div>
	";

?>