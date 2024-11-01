<?

function getLatestThreadsFromExternal($url,$limit, $from_charset, $to_charset){
	$xml = @simplexml_load_file($url."external.php", 'SimpleXMLElement');
	if(!$xml){
		echo "Impossible to reach the file at the address ".$url."external.php";
		$array=array();
		return $array;
	}
	
	$array=array();
	$i=0;
	foreach($xml->channel->item as $item){
	$temp=array();
	$text="".$item->title;
	$temp["title"]=iconv($from_charset, $to_charset, $text);
	$temp["link"]="".$item->link;
	$temp["pubDate"]="".$item->pubDate;
	$dc = $item->children("http://purl.org/dc/elements/1.1/"); 
	$temp["creator"]="".$dc->creator;
	$array[]=$temp;
	$i++;
	if($i==$limit) break;
	}
	return $array;
}

function getAvatarUrl($creator, $url){
$creator=strtolower($creator);
list($width, $height, $type, $attr) = getimagesize($url."avatars/".$creator.".gif");
if($width>5) return $url."avatars/".$creator.".gif";
else return get_bloginfo('wpurl')."/wp-content/plugins/vbulletin-latest-threads/images/avatar_grey.png";
}

?>
