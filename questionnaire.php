<?php
require __DIR__.'/includes/assessment_engine.php';
$user=currentActor();if(!$user){header('Location: login.php?return=questionnaire.php');exit;}
$mode=(string)($_POST['mode']??$_GET['mode']??'edit');
if (!in_array($mode,['edit','new'],true)) $mode='edit';
$existing=$mode==='edit'?loadAssessment($user['id']):null;
$questions=questionBank();$errors=[];$previous=(array)($existing['answers']??[]);
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verifyCsrf((string)($_POST['csrf']??'')))$errors[]='csrf';
    $posted=is_array($_POST['answers']??null)?$_POST['answers']:[];
    [$submitted,$bad]=validateAnswers($posted);$previous=$submitted;
    $errors=array_merge($errors,$bad);
    if(!$errors){$profile=playerProfile($submitted);$ok=persistAssessment($user['id'],$submitted,$profile,$mode);
        if(!$ok){$errors[]='save';} else {header('Location: analysis.php');exit;}}
}
$page='Questionnaire';require __DIR__.'/includes/header.php';
$stageNames=[
 1=>['en'=>'Player background','zh'=>'玩家基本资料'],2=>['en'=>'Technical skills','zh'=>'技术能力自评'],
 3=>['en'=>'Playing style','zh'=>'打法及场上习惯'],4=>['en'=>'Racket preferences','zh'=>'球拍手感偏好'],
 5=>['en'=>'Budget & goals','zh'=>'预算及选拍目标'],6=>['en'=>'Personal follow-up','zh'=>'针对性补充问题']
];
?>
<main class="assessment-page">
<section class="assessment-head"><div class="container"><p class="eyebrow green-eyebrow">BUILT FOR YOUR PLAY STYLE / GEAR LAB</p><h1><?= e(t('hi')) ?>, <em><?= e($user['name']) ?></em>.</h1><h2><?= e(t('questionnaire')) ?></h2><p><?= e(t('questionnaire_intro')) ?> <?= $mode==='new' ? e(language()==='zh'?'这次将保存新的技术评估记录。':'A new dated assessment record will be created.') : e(language()==='zh'?'这是当前评估的修改，不会额外产生一条成长记录。':'Editing the current assessment does not create an extra progress entry.') ?></p><span class="assessment-duration">✦ <?= e(t('estimated_time')) ?></span></div></section>
<section class="assessment-main container">
 <div class="assessment-aside"><div class="assessment-side-panel"><p class="eyebrow green-eyebrow">GEARMATE ENGINE</p><h3><?= language()==='zh'?'先了解你，再为你选拍。':'First we learn your game.' ?></h3><p><?= language()==='zh'?'你能自己选择等级，系统也会根据详细自评另外做出推断，并解释两者之间的差异。':'Choose your own level. We will show a separate questionnaire-based indication and explain it.' ?></p><div class="assessment-steps">
  <?php foreach($stageNames as $idx=>$stage):?><div><span><?= sprintf('%02d',$idx) ?></span><?= e($stage[language()]) ?></div><?php endforeach; ?>
 </div></div></div>
 <div class="assessment-form-frame"><div class="assessment-status"><div><small id="assessmentStage">01 / <?= count($stageNames) ?></small><h3 id="assessmentStageTitle"><?= e($stageNames[1][language()]) ?></h3></div><span id="assessmentCounter">1 / 1</span></div><div class="assessment-progress"><span id="assessmentProgress" style="width:10%"></span></div>
 <?php if($errors):?><div class="form-alert" role="alert"><?= e(t('required')) ?></div><?php endif; ?>
 <form id="assessmentForm" method="post" action="questionnaire.php" data-user="<?= (int)$user['id'] ?>" data-mode="<?= e($mode) ?>" data-required="<?= e(t('required')) ?>" data-stage-names='<?= e(json_encode(array_map(static fn($s)=>$s[language()],$stageNames),JSON_UNESCAPED_UNICODE)) ?>'>
  <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="mode" value="<?= e($mode) ?>">
  <?php foreach($questions as $q):$id=$q['id'];$field='q'.$id;$value=$previous[$field]??($id===1?$user['name']:($id===33?450:'')); ?>
    <fieldset class="quiz-question" data-id="<?= $id ?>" data-stage="<?= (int)$q['stage'] ?>" data-condition="<?= e((string)$q['condition']) ?>">
      <legend><span class="question-num"><?= sprintf('%02d',$id) ?></span> <?= e($q[language()]) ?></legend>
      <?php $hint=$q['help'.(language()==='zh'?'Zh':'En')];if($hint):?><p class="quiz-hint"><?= e($hint) ?></p><?php endif; ?>
      <?php if($q['type']==='nickname'):?><input class="quiz-input" type="text" maxlength="40" minlength="2" name="answers[<?= $field ?>]" value="<?= e($value) ?>" autocomplete="nickname" placeholder="<?= e(t('account_name')) ?>">
      <?php elseif($q['type']==='budget'):?><div class="budget-field"><span>RM</span><input class="quiz-input" type="number" min="100" max="3000" step="10" name="answers[<?= $field ?>]" value="<?= e($value) ?>"></div>
      <?php else: ?><div class="quiz-options"><?php foreach($q['choices'] as $choiceValue=>$names):?><label class="quiz-choice"><input type="radio" name="answers[<?= $field ?>]" value="<?= e($choiceValue) ?>" <?= (string)$value===(string)$choiceValue?'checked':'' ?>><span><?= e($names[language()]) ?><span class="choice-dot" aria-hidden="true"></span></span></label><?php endforeach;?></div>
      <?php endif; ?>
    </fieldset>
  <?php endforeach; ?>
  <div id="quizError" class="quiz-error" role="alert" hidden></div>
  <div class="quiz-bottom"><button type="button" class="button button-outline" id="quizBack">← <?= e(t('back')) ?></button><span class="quiz-saved"><?= e(t('saved_auto')) ?></span><button type="button" class="button button-main" id="quizNext" data-next="<?= e(t('next')) ?>" data-final="<?= e(t('submit_assessment')) ?>"><?= e(t('next')) ?> →</button></div>
 </form></div>
</section></main>
<?php require __DIR__.'/includes/footer.php'; ?>
