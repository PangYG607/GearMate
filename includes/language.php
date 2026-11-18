<?php
require_once __DIR__.'/auth.php';
bootAuth();
function language(): string {
    bootAuth();
    return ($_SESSION['lang'] ?? $_COOKIE['gm_lang'] ?? 'en') === 'zh' ? 'zh' : 'en';
}
function t(string $key): string {
    static $cache=[];
    $lang=language();
    if (!isset($cache[$lang])) $cache[$lang]=require __DIR__.'/lang/'.$lang.'.php';
    return $cache[$lang][$key] ?? $key;
}
function localisedLabel(array $item, string $key='label'): string {
    return (string)($item[$key.'_'.language()] ?? $item[$key.'_en'] ?? '');
}
function modelName(array $racket): string {
    $code=$racket['model'] ?? '';
    if (language()==='en') return $code;
    // Prioritise editor-verified real Chinese market names; do not auto-translate unverified brands.
    static $verifiedNames=null;
    if($verifiedNames===null){
        $verifiedNames=[];
        try {$pdo=getDbConnection();$rows=$pdo->query('SELECT model,verified_zh_name FROM racket_cn_names')->fetchAll();
            foreach($rows as $row)$verifiedNames[$row['model']]=$row['verified_zh_name'];
        }catch(Throwable $ex){}
    }
    if(isset($verifiedNames[$code]))return $verifiedNames[$code].' ('.$code.')';
    // Known official Yonex Chinese SERIES names; retain the unchanged model code and variant. 
    if (($racket['brand']??'') === 'YONEX') {
        foreach (['ASTROX'=>'天斧','NANOFLARE'=>'疾光','ARCSABER'=>'弓箭'] as $prefix=>$zh) {
            if (str_starts_with($code,$prefix.' ')) return '尤尼克斯 '.$zh.substr($code,strlen($prefix)+1).' ('.$code.')';
        }
    }
    return $code; // No invented model translations.
}
function gameLabel(string $key): string {
    $values = [
    'beginner'=>['en'=>'Beginner','zh'=>'新手'], 'intermediate'=>['en'=>'Intermediate','zh'=>'中级'], 'advanced'=>['en'=>'Advanced','zh'=>'高级'],
    'developing'=>['en'=>'Developing','zh'=>'正在进步'],
    'power'=>['en'=>'Attack-oriented','zh'=>'偏进攻'], 'speed'=>['en'=>'Fast-paced','zh'=>'偏速度'], 'control'=>['en'=>'Control-oriented','zh'=>'偏控球'], 'allround'=>['en'=>'All-round','zh'=>'全面型'],
    'head-heavy'=>['en'=>'Head-heavy','zh'=>'头重型'], 'head-light'=>['en'=>'Head-light','zh'=>'头轻型'], 'even'=>['en'=>'Even-balanced','zh'=>'平衡型'],
    'hi-flex'=>['en'=>'Flexible shaft','zh'=>'偏软中杆'], 'medium'=>['en'=>'Medium shaft','zh'=>'中等硬度中杆'], 'stiff'=>['en'=>'Stiff shaft','zh'=>'硬中杆'], 'extra-stiff'=>['en'=>'Extra-stiff shaft','zh'=>'超硬中杆'],
    'not_sure'=>['en'=>'Not sure','zh'=>'不确定'],
    ];
    return $values[$key][language()]??$key;
}
