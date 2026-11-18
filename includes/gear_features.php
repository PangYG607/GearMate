<?php
require_once __DIR__.'/assessment_engine.php';
function racketById(int $id): ?array {
    foreach(getCatalog() as $r) if((int)$r['id']===$id) return $r;
    return null;
}
function verifiedTension(int $racketId): ?array {
    if($racketId<=0)return null;
    try {
        $db=getDbConnection();$s=$db->prepare('SELECT min_lbs,max_lbs,official_url,verified_date FROM racket_spec_limits WHERE racket_id=? AND status=? LIMIT 1');
        $s->execute([$racketId,'official_verified']);return $s->fetch()?:null;
    }catch(Throwable $e){return null;}
}
function setupDetails(array $r,array $string,array $grip,int $tension,array $profile=[]):array {
    $limit=verifiedTension((int)$r['id']);
    $valid=$limit!==null && $tension>=(int)$limit['min_lbs'] && $tension<=(int)$limit['max_lbs'];
    $status=$limit===null?'unverified':($valid?'verified_in_range':'out_of_range');
    $price=(float)referencePrice($r);
    return [
        'racket_id'=>(int)$r['id'],'racket_model'=>$r['model'],'racket_brand'=>$r['brand'],'racket_weight'=>$r['weight'],
        'racket_source'=>$r['source_url'],'racket_price'=>$price,
        'racket_price_source'=>$r['price_source']??'','racket_price_url'=>$r['price_url']??'',
        'price_checked'=>$r['price_checked']??'2026-10-25',
        'string_code'=>$string['code'],'string_name'=>$string['name'],'string_price'=>(float)$string['price'],
        'string_source'=>$string['source'],'string_url'=>$string['url'],'string_note_en'=>$string['note_en'],'string_note_zh'=>$string['note_zh'],
        'grip_code'=>$grip['code'],'grip_name'=>$grip['name'],'grip_price'=>(float)$grip['price'],
        'grip_source'=>$grip['source'],'grip_url'=>$grip['url'],'grip_note_en'=>$grip['note_en'],'grip_note_zh'=>$grip['note_zh'],
        'tension_lbs'=>$tension,'tension_status'=>$status,'official_tension'=>$limit,
        'total'=>$price+(float)$string['price']+(float)$grip['price'],
        'nickname'=>$profile['nickname']??'', 'play_style'=>$profile['style']??'',
        'assessment_level'=>$profile['assessed_level']??'', 'created_at'=>date('Y-m-d H:i:s'),
        'price_disclaimer_en'=>'Reference retailer listings only. Confirm actual prices, availability, variant and stringing charges with your shop.',
        'price_disclaimer_zh'=>'以上为商家公开标价参考，实际售价、库存、具体版本及穿线人工费请向商家确认。'
    ];
}
function saveGearCard(int $userId,array $details): ?int {
    if($userId<=0){$_SESSION['guest_setup']=$details;return 0;}
    try {
        $db=getDbConnection();$s=$db->prepare('INSERT INTO gear_setup_cards (user_id,racket_id,setup_json,created_at) VALUES (?,?,?,NOW())');
        $s->execute([$userId,$details['racket_id'],json_encode($details,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)]);
        return (int)$db->lastInsertId();
    }catch(Throwable $e){return null;}
}
function loadGearCard(int $userId,?int $id=null):?array {
    if($userId===0)return isset($_SESSION['guest_setup'])?['id'=>0,'details'=>$_SESSION['guest_setup']]:null;
    try {
        $db=getDbConnection();
        if($id!==null){$s=$db->prepare('SELECT id,setup_json FROM gear_setup_cards WHERE id=? AND user_id=?');$s->execute([$id,$userId]);}
        else {$s=$db->prepare('SELECT id,setup_json FROM gear_setup_cards WHERE user_id=? ORDER BY id DESC LIMIT 1');$s->execute([$userId]);}
        $row=$s->fetch();
        if($row)return ['id'=>(int)$row['id'],'details'=>json_decode($row['setup_json'],true,512,JSON_THROW_ON_ERROR)];
    }catch(Throwable $e){}
    return null;
}
function savedGearCards(int $userId):array {
    if($userId<=0)return [];
    try {$db=getDbConnection();$s=$db->prepare('SELECT id,setup_json,created_at FROM gear_setup_cards WHERE user_id=? ORDER BY id DESC LIMIT 30');$s->execute([$userId]);return $s->fetchAll();}
    catch(Throwable $e){return [];}
}
function isAdmin(int $userId):bool {
    if($userId<=0)return false;
    try{$db=getDbConnection();$s=$db->prepare('SELECT user_id FROM gear_admin_users WHERE user_id=?');$s->execute([$userId]);return (bool)$s->fetchColumn();}
    catch(Throwable $e){return false;}
}
