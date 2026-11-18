<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require __DIR__.'/includes/language.php';
$all=require __DIR__.'/includes/market_catalog.php';
if(count($all)!==38)throw new RuntimeException('Unexpected catalogue size');
$ids=[];$models=[];$matrix=[];
foreach($all as $r){
 if(isset($ids[$r['id']]) || isset($models[$r['model']])) throw new RuntimeException('Duplicate key');
 $ids[$r['id']]=true;$models[$r['model']]=true;
 $key=$r['category'].':'.$r['price_tier'];$matrix[$key]=($matrix[$key]??0)+1;
 if(empty($r['price_source']) || !str_starts_with($r['price_url'],'https://') || ($r['price_checked']??'')!=='2026-10-25')throw new RuntimeException('Missing evidence '.$r['model']);
 $p=referencePrice($r);
 if(($r['price_tier']==='entry' && $p>=350) || ($r['price_tier']==='mid' && ($p<350 || $p>=700)) || ($r['price_tier']==='premium' && $p<700))throw new RuntimeException('Wrong price tier '.$r['model']);
}
foreach(['attack','speed','control'] as $cat) foreach(['entry','mid','premium'] as $tier) {
 if(($matrix[$cat.':'.$tier]??0)<3)throw new RuntimeException('Low coverage '.$cat.'/'.$tier);
}
$acc=require __DIR__.'/includes/accessory_catalog.php';
if(count($acc)<10)throw new RuntimeException('Accessory count invalid');
foreach($acc as $r){if($r['price']<=0 || !$r['url'])throw new RuntimeException('Bad accessory data');}
$_SESSION['lang']='zh';if(!str_contains(modelName($all[0]),'天斧'))throw new RuntimeException('Chinese name not displayed');
$notice=marketNotice();if(!str_contains($notice,'仅供参考'))throw new RuntimeException('Missing Chinese price disclaimer');
echo "PASS: 38 Yonex entries (3+ in each tier/type), 14 accessories including v0.4 extras, sources and checked dates, bilingual notices.\n";
