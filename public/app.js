const api = '/api/tasks';
const list = document.getElementById('list');
const form = document.getElementById('form');
async function load(){
  const r = await fetch(api); const tasks = await r.json();
  list.innerHTML = tasks.map(t => `<li><div><strong>${t.title}</strong><br/><small>${t.description||''}</small></div><div>${t.status} <button data-id="${t.id}" class="del">x</button></div></li>`).join('');
  document.querySelectorAll('.del').forEach(b=>b.onclick=async (e)=>{ const id=b.dataset.id; await fetch(api+'/'+id,{method:'DELETE'}); load(); });
}
form.onsubmit = async (e)=>{ e.preventDefault(); const title=document.getElementById('title').value; const desc=document.getElementById('description').value; await fetch(api,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({title,description:desc})}); form.reset(); load(); };
load();
