document.addEventListener('DOMContentLoaded', () => {
  const b=document.getElementById('budget'),value=document.getElementById('budgetValue');
  if(b && value)b.addEventListener('input',()=>{value.textContent='RM '+Number(b.value).toLocaleString('en-MY');});
  const form=document.getElementById('assessmentForm');
  if(!form)return;
  const q=Array.from(form.querySelectorAll('.quiz-question'));
  const next=document.getElementById('quizNext'),back=document.getElementById('quizBack'),err=document.getElementById('quizError');
  const counter=document.getElementById('assessmentCounter'),stageEl=document.getElementById('assessmentStage'),title=document.getElementById('assessmentStageTitle'),progress=document.getElementById('assessmentProgress');
  let stageNames=[];try{stageNames=JSON.parse(form.dataset.stageNames);}catch{}
  const key='gearmate-v04-draft-'+form.dataset.user+'-'+(form.dataset.mode||'edit');
  function snapshot(){const d={};for(const el of form.querySelectorAll('input[name^="answers["]')){if(el.type!=='radio'||el.checked){d[el.name]=el.value;}}return d;}
  function restore(){try{const d=JSON.parse(sessionStorage.getItem(key)||'{}');for(const [name,v] of Object.entries(d)){const els=Array.from(form.elements).filter(x=>x.name===name);for(const el of els){if(el.type==='radio')el.checked=el.value===v;else el.value=v;}}}catch{}}
  restore();
  function answer(id){const chosen=form.querySelector(`[name="answers[q${id}]"]:checked`);if(chosen)return chosen.value;const el=form.querySelector(`[name="answers[q${id}]"]`);return el&&el.type!=='radio'?(el.value||''):'';}
  function eligible(el){const c=el.dataset.condition||'',level=answer(5),game=answer(4),owns=answer(26),goal=answer(25),attack=answer(23);
    if(!c)return true;
    if(c==='experienced')return level==='intermediate'||level==='advanced';
    if(c==='advanced')return level==='advanced';
    if(c==='doubles')return game==='doubles'||game==='mixed'||game==='both';
    if(c==='current_racket')return owns==='yes';
    if(c==='attack')return goal==='power'||attack==='smash';
    return false;
  }
  function pages(){const all=q.filter(eligible),chunks=[];let group=[];let s=0;
    for(const el of all){const num=Number(el.dataset.stage);if(num!==s || group.length===3){if(group.length)chunks.push(group);group=[];s=num;}group.push(el);}
    if(group.length)chunks.push(group);return chunks;
  }
  let page=Number(sessionStorage.getItem(key+'-page')||0)||0;let current=[];
  function render(){const chunks=pages();if(!chunks.length)return;page=Math.max(0,Math.min(page,chunks.length-1));current=chunks[page];
    q.forEach(el=>{const visible=current.includes(el);el.hidden=!visible;el.classList.remove('quiz-invalid');el.querySelectorAll('input').forEach(inp=>inp.disabled=!eligible(el));});
    const stage=Number(current[0].dataset.stage);counter.textContent=`${page+1} / ${chunks.length}`;stageEl.textContent=`${String(stage).padStart(2,'0')} / 06`;
    title.textContent=stageNames[stage-1]||'Player assessment';progress.style.width=(`${Math.round((page+1)/chunks.length*100)}%`);
    back.disabled=page===0;sessionStorage.setItem(key+'-page',String(page));next.textContent=(page===chunks.length-1?next.dataset.final:next.dataset.next)+' →';err.hidden=true;
  }
  function validatePage(){let valid=true;for(const el of current){const input=Array.from(el.querySelectorAll('input')).find(e=>e.type!=='radio'),radios=Array.from(el.querySelectorAll('input[type=radio]'));
    let answered;if(radios.length)answered=radios.some(e=>e.checked);else if(input){answered=input.value.trim().length>0 && input.checkValidity();}else answered=false;
    if(!answered){el.classList.add('quiz-invalid');valid=false;}else el.classList.remove('quiz-invalid');}
    if(!valid){err.textContent=form.dataset.required;err.hidden=false;current.find(x=>x.classList.contains('quiz-invalid'))?.scrollIntoView({behavior:'smooth',block:'center'});}return valid;}
  form.addEventListener('change',()=>{sessionStorage.setItem(key,JSON.stringify(snapshot()));render();});
  form.addEventListener('input',()=>{sessionStorage.setItem(key,JSON.stringify(snapshot()));});
  next.addEventListener('click',()=>{if(!validatePage())return;const chunks=pages();if(page<chunks.length-1){page++;render();window.scrollTo({top:0,behavior:'smooth'});}else{sessionStorage.removeItem(key);sessionStorage.removeItem(key+'-page');form.submit();}});
  back.addEventListener('click',()=>{if(page>0){page--;render();window.scrollTo({top:0,behavior:'smooth'});}});
  render();
});
// Estimate demo DIY accessories without a full page refresh.
document.addEventListener('DOMContentLoaded',()=>{
  const display=document.getElementById('builderTotal'),string=document.getElementById('buildString'),grip=document.getElementById('buildGrip');
  if(!display||!string||!grip)return;
  const update=()=>{
    const base=Number(display.dataset.base),a=Number(string.selectedOptions[0]?.dataset.price||0),b=Number(grip.selectedOptions[0]?.dataset.price||0);
    display.textContent='RM '+(base+a+b).toLocaleString('en-MY',{minimumFractionDigits:2,maximumFractionDigits:2});
    const stringNote=document.getElementById('stringNote'),gripNote=document.getElementById('gripNote');
    if(stringNote)stringNote.textContent=string.selectedOptions[0]?.dataset.note||'';
    if(gripNote)gripNote.textContent=grip.selectedOptions[0]?.dataset.note||'';
    for(const [kind,element] of [['string',string],['grip',grip]]){
      const opt=element.selectedOptions[0],link=document.getElementById(kind+'SourceLink'),date=document.getElementById(kind+'Checked');
      if(link){link.textContent=opt?.dataset.source||'Retail source';link.href=opt?.dataset.sourceUrl||'#';}
      if(date)date.textContent=opt?.dataset.checked||'';
    }
  };
  string.addEventListener('change',update);grip.addEventListener('change',update);
});
