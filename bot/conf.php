<?
$token = "5089778624:AAEgBz9MuyjQnqaGom-BCWQvn3PUpM0h7dE";
$db = new mysqli('localhost','u1591831_default','3s02Va5weQpVSNTZ','u1591831_default');







function inline(array $arrays){
    $result = [];
    foreach ($arrays as $key => $value) {
        $n = 0;
        foreach ($value as $key1 => $value2) {
            if(stripos($value2,'http') !==false){
                $type = 'url';
            }else{
                $type = 'callback_data';
            }
            $result[$key][$n] = ['text'=>$key1,$type=>$value2];
            $n++;
        }
    }
    $keyboard = [
        'inline_keyboard'=>$result
    ];
    $json = json_encode($keyboard);
    return $json;  
}

function getData($pm,$son){
    global $db;
    $button = [];
    $page = $pm;
    $offset = ($page-1) * $son;
    $result = $db->query("SELECT COUNT(*) FROM `data`");
    $total = $result->fetch_array()[0];
    $pages = ceil($total / 1);  
    $mdata = $db->query("SELECT * FROM `data` LIMIT $offset, 1");
    $markHome = ($page > 1) ? '⏪' : '◀️';
    $markNext = ($page < $pages) ? '⏩' : '▶️';
    $prevlink = ($page > 1) ? 'next_'.($page - 1) : 'home';
    $nextlink = ($page < $pages) ? 'next_'.($page + 1) : 'end';
    while($arr = $mdata->fetch_array()){
    $PhotoUrl = $arr['file_id'];
    $techno = $arr['tech'];
    $msg = $arr['msg'];}
    $but[] = [['text'=>$markHome,'callback_data'=>$prevlink],['text'=>$page."/".$pages,'callback_data'=>'null'],['text'=>$markNext,'callback_data'=>$nextlink]];
    array_push($but,$button);
    $keyboard = json_encode([
		'inline_keyboard' => $but
	]);
    $arr = ['photo' => $PhotoUrl, 'inline' => $keyboard, 'techno'=>$techno, 'caption'=>$msg];
    return $arr;
}
function step($step,$chat_id){
    global $db;
    $res = $db->query("UPDATE users SET step='$step' WHERE user_id='$chat_id'");
    return $res;
}
function getstep($chat_id){
    global $db;
    $result = $db->query("SELECT * FROM users WHERE user_id = $chat_id");
    $row = $result->fetch_assoc();
    return $row['step'];
}
function users($chat_id){
    global $db;
    $result = $db->query("SELECT * FROM users WHERE user_id = $chat_id");
    $row = $result->fetch_assoc();
    return $row;
}
function lang($text,$chat_id){
    global $db;
    $langs = $db->query("UPDATE users SET lang ='$text' WHERE user_id='$chat_id'");
    return $langs;
}
?>