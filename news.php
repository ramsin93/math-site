<?php

/*
	Gets the content of the $tag element and 
	also returns the remaining string after the element in a line.
*/
function get($tag, $line, $file) {
	$content = preg_replace('@.*?<\s*'.$tag.'\s*>(.*)?(<.*)*@', '${1}', $line);
	if (preg_match('@<\/\s*'.$tag.'\s*>@', $line)) {
		$line = preg_replace('@.*?<\/\s*'.$tag.'\s*>(.*)@', '${1}', $line);
		return array($content, $line);
	}
	
	while (($line = fgets($file)) !== false) {
		if (preg_match('@<\/\s*'.$tag.'\s*>@', $line)) {
			$content = preg_replace('@(.*)?<\/\s*'.$tag.'\s*>.*@', $content . '${1}', $line);
			$line = preg_replace('@.*?<\/\s*'.$tag.'\s*>(.*)@', '${1}', $line);
			return array($content, $line);
		}
		
		$content = $content . $line;
	}
	
	return array($content, '');
}

$feed = fopen("http://export.arxiv.org/rss/math?version=2.0", "r");
if ($feed) {
	$in_item = false;
	$line = fgets($feed);
	
    while (!empty($line)) {
		if ($in_item) {
			if (preg_match('@<\s*title@', $line)) {
				$values = get('title', $line, $feed);
				echo '<h3>' . html_entity_decode($values[0]) . '</h3>';
				if (!empty(trim($values[1])))  {
					$line = $values[1];
					continue;
				}
			} else if (preg_match('@<\s*link@', $line)) {
				$values = get('link', $line, $feed);
				echo '<p><a style="" href="' . $values[0] . '">Link to article</a></p>';
				if (!empty(trim($values[1])))  {
					$line = $values[1];
					continue;
				}
			} else if (preg_match('@<\s*description@', $line)) {
				$values = get('description', $line, $feed);
				echo html_entity_decode($values[0]);
				if (!empty(trim($values[1])))  {
					$line = $values[1];
					continue;
				}
			}
		}
		
		if (preg_match('@<\s*item@', $line)) {
			$in_item = true;
			$line = preg_replace('@.*?<\s*item\s*>(.*)@', '${1}', $line);
			
			if (!empty(trim($line))) {
				continue;
			}
		} else if ($in_item and preg_match('@<\/\s*item\s*>@', $line)) {
			$in_item = false;
			$line = preg_replace('@.*?<\/\s*item\s*>(.*)@', '${1}', $line);
			
			if (!empty(trim($line))) {
				continue;
			}
		}
		
		$line = fgets($feed);
    }

    fclose($feed);
}

?>