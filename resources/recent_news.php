<?php
$news = json_decode(file_get_contents('resources/news.json'), true);
foreach($news as $row) {
    echo "
    <div class='news-post-block'>
        <div class='news-post-title-block'>
            " . $row["title"] . " <span style='float:right;'>".$row["date"]."</span>
        </div>
        <div class='news-paragraph'>
            <p>".$row["body"]."</p>
            <div class='news-post-title-block'></div>
            <span style='float:right;'>Posted by: <a style='color:#e4bf6f;'>".$row["poster"]."</a></span>
		</div>
	</div>";
}