<?php
	function custom_echo($x, $length)
	{
	  if(strlen($x)<=$length)
	  {
		echo $x;
	  }
	  else
	  {
		$y=substr($x,0,$length) . '...';
		echo $y;
	  }
	}
		$conn = new mysqli('68.178.222.132', 'dawn_reader', '+y6$h81T5R[[', 'dawn_website'); //The Blank string is the password
		
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		$query = "SELECT * FROM forums_topics INNER JOIN forums_posts ON forums_topics.tid = forums_posts.topic_id WHERE forum_id='2' AND author_name='Primal' OR forum_id='2' AND author_name='Sly' OR forum_id='2' AND author_name='Stars' ORDER BY tid DESC LIMIT 4"; //You don't need a ; like you do in SQL
		$result = $conn->query($query);

		if ($result->num_rows > 0) {
	  // output data of each row
			while($row = $result->fetch_assoc()) {
				echo "<div class='news-post-block'><div class='news-post-title-block'><a href='https://primal.ps/forum/index.php?app=forums&module=forums&controller=topic&id=" . $row["tid"] . "' class='thread-title'>" . $row["title"] . "</a><a href='https://primal.ps/forum/index.php?app=forums&module=forums&controller=topic&id=" . $row["tid"] . "' class='read-button w-button'>read post</a></div><div class='news-paragraph'><p>";
				custom_echo($row["post"], 500);
				echo "</p></div></div>";
			}
		} else {
			echo "<div class='news-post-block'>
                <div class='news-post-title-block'><a href='#' class='thread-title'>Thread Post Title Here</a><a href='#' class='read-button w-button'>read post</a></div>
                <div class='news-paragraph'>
                  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare, eros dolor interdum nulla, ut commodo diam libero vitae erat. Aenean faucibus nibh et justo cursus id rutrum lorem imperdiet. Nunc ut sem vitae risus tristique posuere.</p>
                  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare, eros dolor interdum nulla, ut commodo diam libero vitae erat. Aenean faucibus nibh et justo cursus id rutrum lorem imperdiet. Nunc ut sem vitae risus tristique posuere.</p>
                  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare, eros dolor interdum nulla, ut commodo diam libero vitae erat. Aenean faucibus nibh et justo cursus id rutrum lorem imperdiet. Nunc ut sem vitae risus tristique posuere.</p>
                </div>
              </div>";
		}
		$conn->close();
?>
