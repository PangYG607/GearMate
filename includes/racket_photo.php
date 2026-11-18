<?php
require_once __DIR__.'/functions.php';
function racketPhoto(string $model):?array {
    static $photos=null;
    if($photos===null) {
        $photos=require __DIR__.'/racket_images.php';
        try {
            $db=getDbConnection();
            foreach($db->query('SELECT model,photo_url,photo_source_url FROM racket_photos')->fetchAll() as $row){
                if($row['photo_url']&&$row['photo_source_url'])$photos[$row['model']]=['url'=>$row['photo_url'],'credit'=>$row['photo_source_url']];
            }
        }catch(Throwable $e){}
    }
    $base=preg_replace('/ \((?:3U|4U|LIGHT BEIGE)\)$/','',$model);
    return $photos[$model]??$photos[$base]??null;
}
