<?php
/** Answer IDs and enumerations are stable across both languages; never translate values. */
function questionBank(): array {
    $q=[];
    $add=function(int $id,int $stage,string $en,string $zh,array $choices=[],string $type='select',?string $condition=null,string $helpEn='',string $helpZh='') use (&$q):void {
        $q[$id]=compact('id','stage','en','zh','choices','type','condition','helpEn','helpZh');
    };
    $opt=function(array $pairs):array {return array_map(static fn($en,$zh)=>['en'=>$en,'zh'=>$zh],array_column($pairs,0),array_column($pairs,1));};
    $level=[['beginner','Beginner · learning the fundamentals','新手 · 正在学习基本动作'],['developing','Developing · can maintain short rallies','初中级 · 可以进行短回合对打'],['intermediate','Intermediate · stable rallies and basic tactics','中级 · 回合较稳定，具备基础战术'],['advanced','Advanced · consistent under pressure','高级 · 在对抗压力下仍有稳定表现']];
    $lev=[];foreach($level as $r)$lev[$r[0]]=['en'=>$r[1],'zh'=>$r[2]];
    $freq=['less1'=>['en'=>'Occasionally / less than once a week','zh'=>'偶尔打／每周少于一次'],'once'=>['en'=>'Once a week','zh'=>'每周一次'],'twothree'=>['en'=>'2–3 times a week','zh'=>'每周 2–3 次'],'four'=>['en'=>'4+ times a week','zh'=>'每周 4 次以上']];
    $uncertain=['unsure'=>['en'=>'Not sure / not yet practised','zh'=>'不确定／还没练习过']];
    $ability=['1'=>['en'=>'Not yet / rarely','zh'=>'还做不到／很少成功'],'2'=>['en'=>'Sometimes, but inconsistent','zh'=>'偶尔可以，但不太稳定'],'3'=>['en'=>'Usually consistent in casual play','zh'=>'一般对打时多数能够做到'],'4'=>['en'=>'Consistent even under pressure','zh'=>'受到对抗压力时仍比较稳定']] + $uncertain;
    $add(1,1,'What should we call you?','我们应该怎么称呼你？',[],'nickname',null,'A nickname is fine; this does not change your account name.','可以填写昵称，不会改变账号的显示名称。');
    $add(2,1,'How long have you played badminton?','你接触羽毛球多久了？',['under3'=>['en'=>'Less than 3 months','zh'=>'少于 3 个月'],'under12'=>['en'=>'3–12 months','zh'=>'3–12 个月'],'one3'=>['en'=>'1–3 years','zh'=>'1–3 年'],'over3'=>['en'=>'Over 3 years','zh'=>'3 年以上']]);
    $add(3,1,'How often do you play?','你平均多久打一次球？',$freq);
    $add(4,1,'Which format do you usually play?','你平常主要打哪一种？',['singles'=>['en'=>'Singles','zh'=>'单打'],'doubles'=>['en'=>'Doubles','zh'=>'双打'],'mixed'=>['en'=>'Mixed doubles','zh'=>'混双'],'both'=>['en'=>'A mix of formats','zh'=>'各种形式都会打']]);
    $add(5,1,'What level would you choose for yourself?','你认为自己目前处于什么水平？',$lev,'select',null,'Your choice stays visible beside the system indication.','你选择的等级会和系统推断的等级并排显示。');
    $add(6,1,'Have you received badminton coaching?','你有接受过羽毛球训练吗？',['none'=>['en'=>'No formal coaching','zh'=>'没有正式训练'],'occasional'=>['en'=>'Occasional lessons','zh'=>'偶尔上课'],'regular'=>['en'=>'Regular coaching','zh'=>'定期训练'],'competition'=>['en'=>'Trained / have competition experience','zh'=>'接受过训练／有比赛经验']]);
    $add(7,2,'Can you hit high clears from your backcourt to the opponent’s backcourt?','你能把后场高远球稳定打到对方后场吗？',$ability,'select',null,'Choose how often you can achieve sufficient depth, not how hard you swing.','以球是否能够稳定到达后场来判断，而不是凭挥拍力量猜测。');
    $add(8,2,'How regularly do you strike the shuttle near the sweet spot?','你击球时，能稳定打到球拍的甜区附近吗？',$ability);
    $add(9,2,'Can your smash put the opponent under pressure?','你的杀球通常能给对手带来多大压力？',$ability);
    $add(10,2,'Can you place smashes away from the opponent reliably?','你能有意识地控制杀球的落点吗？',$ability,'select','experienced');
    $add(11,2,'Can you land drop shots close to the front of the opponent’s court?','你的吊球能比较稳定地落在对方前场吗？',$ability);
    $add(12,2,'Can you play controlled, tight net shots?','你能打出贴近球网且容易控制的网前球吗？',$ability);
    $add(13,2,'Can you lift a net shot deep to the other backcourt?','遇到网前球时，你能稳定把球挑到后场吗？',$ability);
    $add(14,2,'Can you maintain the pace of fast flat drive exchanges?','快速平抽挡对抗时，你能连续跟上节奏吗？',$ability,'select','experienced');
    $add(15,2,'Can you return smashes into a playable position?','面对对手杀球，你能稳定把球接回有效位置吗？',$ability);
    $add(16,2,'Can you reach the four corners of the court in time?','球被打向球场四角时，你能及时移动到位吗？',$ability);
    $add(17,2,'Do you recover to a ready position after hitting?','每次击球之后，你能及时回到合理的准备位置吗？',$ability);
    $add(18,2,'Can you maintain stable rallies against players of similar level?','与水平相近的对手交手时，你能保持稳定的多拍回合吗？',$ability,'select','experienced');
    $add(19,3,'When playing doubles, where do you feel more comfortable?','打双打时，你更习惯哪个位置？',['front'=>['en'=>'Front court','zh'=>'前场'],'rear'=>['en'=>'Rear court','zh'=>'后场'],'rotate'=>['en'=>'Rotate between both','zh'=>'前后场轮转'],'unsure'=>['en'=>'Not sure','zh'=>'不确定']],'select','doubles');
    $add(20,3,'What rally rhythm do you enjoy?','你比较喜欢哪一种回合节奏？',['fast'=>['en'=>'Fast exchanges','zh'=>'快速对攻'],'long'=>['en'=>'Long, steady rallies','zh'=>'稳定多拍'],'tactical'=>['en'=>'Control the pace and placement','zh'=>'控制节奏和落点'],'unsure'=>['en'=>'Not sure','zh'=>'不确定']]);
    $add(21,3,'Which stroke do you trust the most?','你最有信心的技术是哪一项？',['smash'=>['en'=>'Smash','zh'=>'杀球'],'net'=>['en'=>'Net shots','zh'=>'网前球'],'drives'=>['en'=>'Drives','zh'=>'平抽挡'],'defence'=>['en'=>'Defence','zh'=>'防守'],'clear'=>['en'=>'High clear','zh'=>'高远球'],'unsure'=>['en'=>'Not sure yet','zh'=>'目前不确定']]);
    $add(22,3,'What would you most like to improve?','你最想提升哪一种能力？',['power'=>['en'=>'Smash power','zh'=>'杀球力量'],'defence'=>['en'=>'Defence','zh'=>'防守'],'control'=>['en'=>'Shot placement','zh'=>'落点控制'],'speed'=>['en'=>'Reaction speed','zh'=>'反应速度'],'footwork'=>['en'=>'Footwork','zh'=>'步伐'],'consistency'=>['en'=>'Consistency','zh'=>'稳定性']]);
    $add(23,3,'When you have an attacking chance, what do you usually do?','获得主动进攻机会时，你通常会怎么打？',['smash'=>['en'=>'Smash directly','zh'=>'直接杀球'],'drop'=>['en'=>'Drop and move the opponent','zh'=>'吊球调动对手'],'drive'=>['en'=>'Flat drive and pressure','zh'=>'平抽压迫'],'safe'=>['en'=>'Play a stable setup shot','zh'=>'先打稳定的过渡球']]);
    $add(24,3,'When defending repeated attacks, what happens most often?','面对对手连续进攻时，你通常怎样应对？',['block'=>['en'=>'Block to the net','zh'=>'挡网'],'lift'=>['en'=>'Lift high to reset','zh'=>'挑高球争取回位'],'drive'=>['en'=>'Counter with drives','zh'=>'反抽回击'],'late'=>['en'=>'Often late to react','zh'=>'经常反应不及']],'select','experienced');
    $add(25,3,'What is your biggest goal for a new racket?','换一支球拍后，你最想改善哪方面？',['power'=>['en'=>'More attacking power','zh'=>'进攻更有力量'],'speed'=>['en'=>'Faster handling','zh'=>'挥拍更灵活'],'control'=>['en'=>'More reliable control','zh'=>'控球更稳定'],'ease'=>['en'=>'Easier, more forgiving feel','zh'=>'更容易上手、容错更好']]);
    $add(26,4,'Do you currently use your own racket?','你目前有固定使用的球拍吗？',['no'=>['en'=>'No / borrowed racket','zh'=>'没有／借用球拍'],'yes'=>['en'=>'Yes','zh'=>'有']]);
    $add(27,4,'What bothers you most about your current racket?','你觉得目前的球拍最大的不足是什么？',['heavy'=>['en'=>'Too heavy or slow','zh'=>'太重／挥拍慢'],'weak'=>['en'=>'Hard to generate power','zh'=>'不容易打出力量'],'control'=>['en'=>'Hard to control shots','zh'=>'落点不容易控制'],'fine'=>['en'=>'No major issues','zh'=>'没有明显问题'],'unsure'=>['en'=>'Not sure','zh'=>'不确定']],'select','current_racket');
    $add(28,4,'Which racket weight do you prefer?','你喜欢哪种重量级别？',['3u'=>['en'=>'3U · heavier','zh'=>'3U · 较重'],'4u'=>['en'=>'4U · medium-light','zh'=>'4U · 较轻'],'5u'=>['en'=>'5U or lighter','zh'=>'5U 或更轻'],'unsure'=>['en'=>'Not sure','zh'=>'不确定']]);
    $add(29,4,'What balance feels best to you?','你偏好哪种拍头平衡？',['head-heavy'=>['en'=>'Head-heavy · more momentum','zh'=>'头重型 · 借助拍头惯性'],'even'=>['en'=>'Even-balanced · versatile','zh'=>'平衡型 · 兼顾多种打法'],'head-light'=>['en'=>'Head-light · quick handling','zh'=>'头轻型 · 更易快速挥动'],'unsure'=>['en'=>'Not sure','zh'=>'不确定']]);
    $add(30,4,'Which shaft feel would you choose?','你喜欢怎样的中杆手感？',['hi-flex'=>['en'=>'Flexible','zh'=>'偏软'],'medium'=>['en'=>'Medium','zh'=>'适中'],'stiff'=>['en'=>'Stiff','zh'=>'偏硬'],'unsure'=>['en'=>'Not sure','zh'=>'不确定']]);
    $add(31,4,'After longer sessions, how does your racket feel to swing?','打球时间较长时，通常觉得挥拍怎样？',['easy'=>['en'=>'Still easy to swing','zh'=>'挥拍依然轻松'],'tired'=>['en'=>'It starts to feel heavy','zh'=>'开始觉得球拍沉、难挥动'],'varies'=>['en'=>'Depends on the session','zh'=>'看当天情况'],'unsure'=>['en'=>'Not sure','zh'=>'不确定']]);
    $add(32,4,'Do you have a preferred brand?','你有特别偏好的球拍品牌吗？',['none'=>['en'=>'No brand preference','zh'=>'没有品牌偏好'],'YONEX'=>['en'=>'Yonex','zh'=>'尤尼克斯 Yonex'],'LI-NING'=>['en'=>'Li-Ning','zh'=>'李宁 Li-Ning'],'VICTOR'=>['en'=>'Victor','zh'=>'胜利 Victor'],'OTHER'=>['en'=>'Other','zh'=>'其他']]);
    $add(33,5,'What is your maximum budget in MYR?','你这次最多打算花多少马币？',[],'budget',null,'RM prices are source-linked retailer references, not live quotations.','这里的 RM 价格来自商家公开标价，仅供参考，并非实时售价。');
    $add(34,5,'Should this budget cover accessories too?','预算是否包括球线和手胶？',['racket'=>['en'=>'Racket only','zh'=>'只计算球拍'],'complete'=>['en'=>'Complete setup','zh'=>'整套装备都算在内']]);
    $add(35,5,'If no strong match fits, could you stretch the budget?','如果预算内没有适合的球拍，你愿意考虑增加预算吗？',['none'=>['en'=>'No · show alternatives anyway','zh'=>'不愿意 · 仍可查看替代方案'],'50'=>['en'=>'Up to RM50','zh'=>'最多加 RM50'],'100'=>['en'=>'Up to RM100','zh'=>'最多加 RM100'],'all'=>['en'=>'Show suitable models at other prices','zh'=>'可以看看其他价位']]);
    $add(36,5,'Which buying priority matters most?','购买球拍时，你最在意什么？',['value'=>['en'=>'Value for money','zh'=>'性价比'],'durability'=>['en'=>'Durability','zh'=>'耐用性'],'easy'=>['en'=>'Easy to use','zh'=>'容易上手'],'performance'=>['en'=>'Performance','zh'=>'性能表现']]);
    $add(37,6,'In a pressured backhand rear-court situation, how consistent are your clears?','在被动的反手后场位置，你的高远球有多稳定？',$ability,'select','advanced');
    $add(38,6,'In doubles, what limits you most during quick defence-to-attack transitions?','双打时，从防守转进攻最困扰你的是什么？',['reaction'=>['en'=>'Reaction time','zh'=>'反应速度'],'drive'=>['en'=>'Drive control','zh'=>'平抽挡控制'],'rotation'=>['en'=>'Court rotation','zh'=>'前后场轮转'],'none'=>['en'=>'No clear limitation','zh'=>'暂时没有明显问题']],'select','doubles');
    $add(39,6,'What most limits your attack?','你觉得进攻时最受限制的是？',['power'=>['en'=>'Not enough power','zh'=>'力量不足'],'placement'=>['en'=>'Placement / accuracy','zh'=>'落点不准确'],'repeat'=>['en'=>'Hard to keep attacking','zh'=>'难以连续进攻'],'unsure'=>['en'=>'Not sure','zh'=>'不确定']],'select','attack');
    $add(40,6,'What change would you want compared with your existing racket?','如果换新球拍，你最希望和目前这支有何不同？',['lighter'=>['en'=>'Lighter and faster','zh'=>'更轻、更灵活'],'stronger'=>['en'=>'More attack support','zh'=>'更有利于进攻'],'control'=>['en'=>'More precise control','zh'=>'更好控球'],'similar'=>['en'=>'Similar familiar feel','zh'=>'保留熟悉的手感']],'select','current_racket');
    return $q;
}
