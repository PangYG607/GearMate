<?php
require __DIR__.'/includes/gear_features.php';$u=currentUser();
if(!$u){header('Location: login.php?return=account.php');exit;}
if(!isAdmin($u['id'])){http_response_code(403);exit('Admin access required');}
$zh=language()==='zh';$error='';$success='';
$choices=['category'=>['attack','speed','control'],'tier'=>['entry','mid','premium'],'balance'=>['head-heavy','head-light','even'],'flex'=>['hi-flex','medium','stiff','extra-stiff'],'level'=>['beginner','intermediate','advanced']];
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!verifyCsrf((string)($_POST['csrf']??'')))$error='Invalid session / 页面已失效';
  else{
   $cmd=(string)($_POST['cmd']??'');$id=(int)($_POST['id']??0);
   try{
    $db=getDbConnection();
    if($cmd==='delete' && $id>0){
      $db->beginTransaction();$s=$db->prepare('SELECT model FROM rackets WHERE id=?');$s->execute([$id]);$old=$s->fetchColumn();
      if($old){foreach(['racket_photos','racket_cn_names','racket_market_meta'] as $table){$s=$db->prepare('DELETE FROM '.$table.' WHERE model=?');$s->execute([$old]);}$s=$db->prepare('DELETE FROM rackets WHERE id=?');$s->execute([$id]);}$db->commit();$success=$zh?'球拍条目已删除':'Racket entry deleted';
    }elseif($cmd==='save'){
      $brand=strtoupper(trim((string)($_POST['brand']??'')));
      $model=trim((string)($_POST['model']??''));$series=strtoupper(trim((string)($_POST['series']??'')));
      $weight=strtoupper(trim((string)($_POST['weight']??'')));
      $balance=(string)($_POST['balance']??'');$flex=(string)($_POST['flex']??'');$level=(string)($_POST['level']??'');$category=(string)($_POST['category']??'');$tier=(string)($_POST['tier']??'');
      $price=filter_var($_POST['price']??null,FILTER_VALIDATE_FLOAT);$retailer=trim((string)($_POST['price_source']??''));
      $retailerUrl=trim((string)($_POST['price_url']??''));$specUrl=trim((string)($_POST['source_url']??''));
      $checked=(string)($_POST['checked']??date('Y-m-d'));
      $photoUrl=trim((string)($_POST['photo_url']??''));
      $photoSource=trim((string)($_POST['photo_source_url']??''));
      $tensionMin=trim((string)($_POST['tension_min']??''));
      $tensionMax=trim((string)($_POST['tension_max']??''));
      $approveTension=isset($_POST['approve_tension']);
      if($photoUrl!=='' || $photoSource!==''){
          if(!filter_var($photoUrl,FILTER_VALIDATE_URL)||!filter_var($photoSource,FILTER_VALIDATE_URL)||!str_starts_with($photoUrl,'https://')||!str_starts_with($photoSource,'https://'))throw new RuntimeException('Photo and source must both be HTTPS URLs.');
      }
      if($tensionMin!==''||$tensionMax!==''){
        if(!ctype_digit($tensionMin)||!ctype_digit($tensionMax)||(int)$tensionMin<12||(int)$tensionMax>40||(int)$tensionMin>(int)$tensionMax||!$approveTension)
            throw new RuntimeException('Exact variant tension limits need both numbers and editor verification confirmation.');
      }
      $zhName=trim((string)($_POST['verified_zh_name']??''));
      $zhSource=trim((string)($_POST['name_source_url']??''));
      if($zhName!=='' && (unicodeLength($zhName)>180 || !filter_var($zhSource,FILTER_VALIDATE_URL) || !str_starts_with($zhSource,'https://')))throw new RuntimeException('Chinese name requires a real verified HTTPS source URL.');
      if(!$brand || strlen($brand)>60 || !$model || strlen($model)>150 || !$series || strlen($series)>60 || !preg_match('/^[3-9]U$/',$weight) ||
        !in_array($balance,$choices['balance'],true)||!in_array($flex,$choices['flex'],true)||!in_array($level,$choices['level'],true)||!in_array($category,$choices['category'],true)||!in_array($tier,$choices['tier'],true)||
        $price===false||$price<=0||$price>99999||!$retailer||strlen($retailer)>150||!filter_var($retailerUrl,FILTER_VALIDATE_URL)||!filter_var($specUrl,FILTER_VALIDATE_URL)||
        !str_starts_with($retailerUrl,'https://')||!str_starts_with($specUrl,'https://')||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$checked))throw new RuntimeException('Please complete all fields with real retailer/manufacturer source URLs.');
      $db->beginTransaction();
      if($id){
        $s=$db->prepare('SELECT model FROM rackets WHERE id=?');$s->execute([$id]);$old=$s->fetchColumn();if(!$old)throw new RuntimeException('Racket no longer exists');
        if($old!==$model)throw new RuntimeException('Keep existing model identifier unchanged; create a new item instead.');
        $s=$db->prepare('UPDATE rackets SET brand=?,series=?,balance=?,flex=?,weight=?,level=?,demo_price=?,source_url=? WHERE id=?');
        $s->execute([$brand,$series,$balance,$flex,$weight,$level,round($price),$specUrl,$id]);
      }else{
        $s=$db->prepare('INSERT INTO rackets(brand,model,series,balance,flex,weight,level,demo_price,source_url) VALUES (?,?,?,?,?,?,?,?,?)');
        $s->execute([$brand,$model,$series,$balance,$flex,$weight,$level,round($price),$specUrl]);
      }
      $s=$db->prepare("INSERT INTO racket_market_meta(model,category,price_tier,price_rm,price_source,price_url,price_checked,variant_note,spec_note) VALUES (?,?,?,?,?,?,?,'','Preliminary category/shaft suitability labels; confirm variant specifications') ON DUPLICATE KEY UPDATE category=VALUES(category),price_tier=VALUES(price_tier),price_rm=VALUES(price_rm),price_source=VALUES(price_source),price_url=VALUES(price_url),price_checked=VALUES(price_checked)");
      $s->execute([$model,$category,$tier,$price,$retailer,$retailerUrl,$checked]);
      if($zhName!==''){$s=$db->prepare('INSERT INTO racket_cn_names(model,verified_zh_name,name_source_url,checked_date) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE verified_zh_name=VALUES(verified_zh_name),name_source_url=VALUES(name_source_url),checked_date=VALUES(checked_date)');$s->execute([$model,$zhName,$zhSource,$checked]);}
      if($photoUrl!=='') {
        $photoStmt=$db->prepare('INSERT INTO racket_photos(model,photo_url,photo_source_url,checked_date) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE photo_url=VALUES(photo_url),photo_source_url=VALUES(photo_source_url),checked_date=VALUES(checked_date)');
        $photoStmt->execute([$model,$photoUrl,$photoSource,$checked]);
      }
      if($tensionMin!=='' && $tensionMax!==''){
        $specId=$id;
        if(!$specId){$idStmt=$db->prepare('SELECT id FROM rackets WHERE model=?');$idStmt->execute([$model]);$specId=(int)$idStmt->fetchColumn();}
        $limStmt=$db->prepare("INSERT INTO racket_spec_limits(racket_id,min_lbs,max_lbs,official_url,verified_date,status) VALUES (?,?,?,?,?,'official_verified') ON DUPLICATE KEY UPDATE min_lbs=VALUES(min_lbs),max_lbs=VALUES(max_lbs),official_url=VALUES(official_url),verified_date=VALUES(verified_date),status=VALUES(status)");
        $limStmt->execute([$specId,(int)$tensionMin,(int)$tensionMax,$specUrl,$checked]);
      }
      $db->commit();$success=$zh?'产品已保存':'Product saved';
    }
   }catch(Throwable $e){if(isset($db)&&$db->inTransaction())$db->rollBack();$error=$e->getMessage();}
  }
}
$all=getCatalog();$editId=(int)($_GET['edit']??0);$editing=null;foreach($all as $r)if((int)$r['id']===$editId){$editing=$r;break;}
$photoNames=[];$limitNames=[];
try {$db=getDbConnection();foreach($db->query('SELECT model,photo_url,photo_source_url FROM racket_photos')->fetchAll() as $photo)$photoNames[$photo['model']]=$photo;
 foreach($db->query('SELECT r.model,s.min_lbs,s.max_lbs FROM racket_spec_limits s JOIN rackets r ON r.id=s.racket_id')->fetchAll() as $limit)$limitNames[$limit['model']]=$limit;}
catch(Throwable $e){}
$cnNames=[];try {$db=getDbConnection();$s=$db->query('SELECT model,verified_zh_name,name_source_url FROM racket_cn_names');foreach($s->fetchAll() as $cn)$cnNames[$cn['model']]=$cn;}catch(Throwable $e){}
$def=['brand'=>'YONEX','series'=>'ASTROX','model'=>'','weight'=>'4U','balance'=>'even','flex'=>'medium','level'=>'beginner','category'=>'control','price_tier'=>'entry','price_rm'=>0,'price_source'=>'','price_url'=>'','source_url'=>'','price_checked'=>date('Y-m-d')];$f=$editing??$def;
$page='Admin';require __DIR__.'/includes/header.php';
?><main><section class="results-heading"><div class="container"><p class="eyebrow green-eyebrow">GEARMATE / ADMIN CRUD</p><h1><?= $zh?'管理球拍数据库':'MANAGE RACKET CATALOGUE' ?></h1><p><?= $zh?'请只录入真实型号、公开的马来西亚市场报价和实际规格来源。分类标签尚未等于官方验证。':'Add real models and Malaysian retailer source prices. Classification tags remain provisional until per-variant verification.' ?></p></div></section><section class="section"><div class="container"><div class="profile-card admin-layout">
<?php if($error):?><div class="form-alert"><?= e($error) ?></div><?php endif;?><?php if($success):?><div class="mode-success"><?= e($success) ?></div><?php endif;?>
<h2><?= $editing?($zh?'编辑球拍':'Edit Racket'):($zh?'添加球拍':'Add Racket') ?></h2>
<form method="post" class="admin-fields"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="cmd" value="save"><input type="hidden" name="id" value="<?= (int)($editing['id']??0) ?>">
<?php foreach(['brand'=>'Brand','model'=>'Exact model + variant','series'=>'Series','weight'=>'Weight (3U / 4U / 5U)','price_rm'=>'Retail RM price','price_source'=>'Retailer name','price_url'=>'Retailer listing HTTPS URL','source_url'=>'Manufacturer/spec HTTPS URL','price_checked'=>'Price checked YYYY-MM-DD'] as $field=>$label):?><label><?= e($label) ?><input name="<?= $field==='price_rm'?'price':($field==='price_checked'?'checked':$field) ?>" value="<?= e($f[$field]??'') ?>" <?= $editing&&$field==='model'?'readonly':'' ?> required <?= $field==='price_rm'?'type="number" min="0.01" step="0.01"':'' ?>></label><?php endforeach; ?>
<label><?= $zh?'官方／市场真实中文名称（可选）':'Verified Chinese market name (optional)' ?><input name="verified_zh_name" value="<?= e($cnNames[$f['model']]['verified_zh_name']??'') ?>"></label>
<label><?= $zh?'中文名称核实链接':'Chinese name source URL' ?><input name="name_source_url" type="url" value="<?= e($cnNames[$f['model']]['name_source_url']??'') ?>" placeholder="https://..."></label>
<label><?= $zh?'真实产品照片 HTTPS 链接（可选）':'Model-matched product photo HTTPS URL (optional)' ?><input name="photo_url" type="url" value="<?= e($photoNames[$f['model']]['photo_url']??'') ?>" placeholder="https://..."></label>
<label><?= $zh?'照片来源／版权信息网址':'Product photo source/credit URL' ?><input name="photo_source_url" type="url" value="<?= e($photoNames[$f['model']]['photo_source_url']??'') ?>" placeholder="https://..."></label>
<label><?= $zh?'已核实厂家最低磅数（可选）':'Verified official minimum lbs (optional)' ?><input name="tension_min" type="number" min="12" max="40" value="<?= e($limitNames[$f['model']]['min_lbs']??'') ?>"></label>
<label><?= $zh?'已核实厂家最高磅数（可选）':'Verified official maximum lbs (optional)' ?><input name="tension_max" type="number" min="12" max="40" value="<?= e($limitNames[$f['model']]['max_lbs']??'') ?>"></label>
<label><input type="checkbox" name="approve_tension" value="1" style="width:auto"> <?= $zh?'我已按确切型号和重量版本人工核对厂家磅数。':'I manually checked the tension range for this exact model and weight variant against its official source.' ?></label>
<?php foreach(['category'=>'Style category' ,'price_tier'=>'Price tier','balance'=>'Balance (provisional)','flex'=>'Shaft (provisional)','level'=>'Suitability tag (provisional)'] as $field=>$label):$values=$choices[$field==='price_tier'?'tier':$field];?><label><?= e($label) ?><select name="<?= $field==='price_tier'?'tier':$field ?>"><?php foreach($values as $v):?><option value="<?= e($v) ?>" <?= ($f[$field]??'')===$v?'selected':'' ?>><?= e($v) ?></option><?php endforeach;?></select></label><?php endforeach; ?>
<button class="button button-main" type="submit"><?= $zh?'保存':'Save' ?> →</button></form>
<div class="admin-rows"><h2><?= count($all) ?> <?= $zh?'条产品记录':'catalogue entries' ?></h2>
<?php foreach($all as $r):?><div class="admin-row"><div><strong><?= e($r['brand'].' '.$r['model']) ?></strong><p><?= e(formatPrice(referencePrice($r))) ?> · <?= e($r['weight']) ?></p></div><a href="admin_rackets.php?edit=<?= (int)$r['id'] ?>"><?= $zh?'编辑':'Edit' ?></a><form method="post" onsubmit="return confirm('Delete this record?')"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="cmd" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button type="submit" class="button button-outline"><?= $zh?'删除':'Delete' ?></button></form></div><?php endforeach; ?></div>
</div></div></section></main><?php require __DIR__.'/includes/footer.php'; ?>
