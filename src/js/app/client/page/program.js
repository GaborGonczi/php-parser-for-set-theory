import { CONSTANTS } from "../constants.js";
import { htmlEntityMap } from "../htmlentitytable.js";
import { getData,postData,postEncodedData, getEncodedData } from "../utils.js";
import { ExtendedSet} from "../datastructures/ExtendedSet.js";
import { Point } from "../datastructures/Point.js";
const downloadbtn=document.querySelector('#download');
const printbtn=document.querySelector('#print');
const opButtons=document.querySelectorAll('.operator');
const mode=document.querySelector('.switch input#mode');
const dfa=document.querySelector('.switch input#dfa');
const errorMessages=document.querySelector('.switch input#errorMessages');
const loadfile=document.querySelector('#load');
const variables=document.querySelector('#variables textarea');
const newbtn=document.querySelector('#new');
const outputField=document.querySelector('#output');

let logs=[];
let row=0;

function fillTemplate(data){
    return fetch(CONSTANTS.templateUrl)
    .then((response) => response.text())
    .then((template) => {
       const rendered = Mustache.render(template, data);
        document.getElementById('output').innerHTML = rendered;
        setUpTextInputEventListeners()
    })
}
function setUpTextInputEventListeners(){
    const textInput=document.querySelector('#text');
    textInput.focus();
    textInput.removeEventListener('click',log);
    textInput.removeEventListener('focus',log);
    textInput.removeEventListener('keydown',save);
    textInput.addEventListener('click',log);
    textInput.addEventListener('focus',log);
    textInput.addEventListener('keydown',save);
}
function getObjectElement(element){
    if(typeof element==="object"&& element.name=='Point'){
        return new Point(element.x,element.y);
    }
    else{
        return element;
    }
}
function setVars(data){
    let vars=data.variables;
    let varstr='';
    for (let index = 0; index < vars.length; index++) {
        const variable = vars[index];
        let name=Object.keys(variable).shift();
        let value=variable[name];
        varstr+=`${name} (${value.name}) : `;
        if(value.name==="Set"){
            let elements=value.elements;
            let set=new ExtendedSet(elements.map(element=>getObjectElement(element)));
            varstr+=set.toString();
        }
        else if(value.name==="Point"){
            let point=new Point(value.x,value.y);
            varstr+=point.toString();
        }
        varstr+='\n';
    }
    variables.value=varstr;
}
function encodeHtmlEntities(statement){
    Object.keys(htmlEntityMap).forEach((entity)=>{
        statement=statement.replaceAll(entity,htmlEntityMap[entity]);
    })
    return statement;
}
function save(e){
    if(e.code!=="Enter"&&e.code!="NumpadEnter") return;
    let element=e.target;
    let statement= element.innerText||element.value;
    if(statement.trim()==="") return;
    let startpos=0;
    let id=null;
    statement.replace(/\n/g, "");
    element.innerHTML=statement;
    element.innerText=statement;
    let statements=document.querySelectorAll('p.statement');

    for (let index = 0; index < statements.length; index++) {
        if(element.innerText!==statements[index].innerText){
           startpos+=statements[index].innerText.length;
           row++;
        }
        else {
           let idInput=document.querySelector('p[contenteditable="true"] ~ input[name="expressionId"]');
           id=idInput.value;
            break;
        }
    }
    
    let start=startpos;
    let end=start+statement.length;
    let noparse=mode.checked;
    let gdfa=dfa.checked;
    let derrorMessages=errorMessages.checked;
    statement=encodeHtmlEntities(statement);
   
    let data={id:id,statement:statement,start:start, end:end,noparse:noparse,row:row,beforelogs:logs,gdfa:gdfa,derrorMessages:derrorMessages};

    postData(data,CONSTANTS.parseUrl).then(data=>{
        fillTemplate(data);
        setVars(data);

    });
    

}
function insertspecialcharacter(e){
    //https://www.tutorialspoint.com/how-to-set-cursor-position-in-content-editable-element-using-javascript last date 2024. 03. 21.
    let element=document.querySelector('p[contenteditable="true"]');
    
    if(element!==null){
        let selection=window.getSelection();
        let range=selection.getRangeAt(0);
        let insertpos=range.startOffset;
        element.innerText=element.innerText.substring(0,insertpos) +e.target.value+element.innerText.substring(insertpos);
        range.setStart(element.childNodes[0], insertpos+1);
        range.collapse(true);
        selection.removeAllRanges();
        selection.addRange(range);
        element.focus();
    }
    else {
        let selection=window.getSelection();
        selection.removeAllRanges();
        let element=document.querySelector('#input input#text');
        let insertpos=element.selectionStart;
        element.value=element.value.substring(0,insertpos) +e.target.value+element.value.substring(insertpos);
        element.setSelectionRange(insertpos+1, insertpos+1)
        element.focus()
       
    }
}
function saveToFile(){
    getData(CONSTANTS.saveUrl).then(data=>{
        //https://bobbyhadz.com/blog/javascript-set-blob-filename last date 2024. 03. 21.
        const blob= new Blob([JSON.stringify(data)],{type:"application/json"});
        const url=URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.download="munkalap.json";
        link.click()

    })
}
function print(){
    window.print();
}
function toggleStyle(){
    const textInput=document.querySelector('#text');
    textInput.classList.toggle("math")
}
// https://stackoverflow.com/questions/1703228/how-can-i-clear-an-html-file-input-with-javascript first answer last date 2024. 03. 21.
function clearInputFile(f){
    if(f.value){
        try{
            f.value = ''; //for IE11, latest Chrome/Firefox/Opera...
        }catch(err){ }
        if(f.value){ //for IE5 ~ IE10
            var form = document.createElement('form'),
                parentNode = f.parentNode, ref = f.nextSibling;
            form.appendChild(f);
            form.reset();
            parentNode.insertBefore(f,ref);
        }
    }
}

function loadFromFile(){
    const load=loadfile.files[0];
    const formData = new FormData();
    formData.append("load", load);
    postEncodedData(formData,CONSTANTS.loadUrl).then(data=>{
        getData(CONSTANTS.parseUrl).then(data=>{
            fillTemplate(data)
            setVars(data);
            clearInputFile(loadfile)
        })
   })
   
}
function newFile(){
    getData(CONSTANTS.saveUrl).then(data=>{
        const blob= new Blob([JSON.stringify(data)],{type:"application/json"});
        const url=URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.download="munkalap.json";
        link.click()
        postData(null,CONSTANTS.newUrl).then(data=>{
            getData(CONSTANTS.parseUrl).then(data=>{
                fillTemplate(data)
                setVars(data);
                clearInputFile(loadfile)
            })
        });

    });       
    
}
function edit(e){
    let elem=e.target;
   
    if(elem.classList.contains("statement")){
        elem.removeEventListener("keydown",save);
        elem.setAttribute("contenteditable", "true");
        elem.addEventListener("keydown",save);  
    }
    else{
        let statements=document.querySelectorAll('p.statement');
        statements.forEach(element=>{
            element.removeEventListener("keydown",save);
            element.setAttribute("contenteditable", "false")
        });
    }
}
function log(e){
    let now= new Date();
    let year=now.getFullYear();
    let month=now.getMonth();
    let day=now.getDate();
    let hour=now.getHours();
    let min=now.getMinutes();
    let sec=now.getSeconds();
    let milisec=now.getMilliseconds();
    let eventData={
      type:e.type || null,
      button:e.button || null,
      ctrlKey:e.ctrlKey || null,
      key:e.key || null,
      sourceElementId:e.target.id || null,
      sourceElementValue:e.target.value || null,
      sourceElementTagname:e.target.tagName || null,
      sourceElementTitle:e.target.title || null,
      time:`${year}/${month}/${day}-${hour}:${min}:${sec}:${milisec}` || null  
    }
    logs.push(eventData);
}
function setUpLog(){
    removeAllLogListeners();
    addAllLogListeners();
}
function loadUi(){
    const container=document.querySelector(".button-grid-container")
    container.style.setProperty('--grid-column','3')
    container.style.setProperty('--grid-row',(opButtons.length/window.getComputedStyle(container).getPropertyValue('--grid-column'))+1);
    removeAllEventListeners()
    addAllEventListeners()
}
function addAllLogListeners(){
    opButtons.forEach(btn=>{
        btn.addEventListener("click",log)
    })
    mode.addEventListener("change",log);

    downloadbtn.addEventListener("click",log);
    loadfile.addEventListener("change",log);
    printbtn.addEventListener("click",log);
    newbtn.addEventListener("click",log);
    outputField.addEventListener("click",log);
}
function removeAllLogListeners(){
    opButtons.forEach(btn=>{
        btn.removeEventListener("click",log)
    })
    mode.removeEventListener("change",log);

    downloadbtn.removeEventListener("click",log);
    loadfile.removeEventListener("change",log);
    printbtn.removeEventListener("click",log);
    newbtn.removeEventListener("click",log);
    outputField.removeEventListener("click",log);
}
function addAllEventListeners(){
    opButtons.forEach(btn=>{
        btn.addEventListener("click",insertspecialcharacter)
    })
    mode.addEventListener("change",toggleStyle)
    downloadbtn.addEventListener("click",saveToFile);
    printbtn.addEventListener('click',print);
    newbtn.addEventListener("click",newFile);
    loadfile.addEventListener("change",loadFromFile);
    outputField.addEventListener("click",edit)
}
function removeAllEventListeners(){
    opButtons.forEach(btn=>{
        btn.removeEventListener("click",insertspecialcharacter)
    })
    mode.removeEventListener("change",toggleStyle)
    downloadbtn.removeEventListener("click",saveToFile);
    printbtn.removeEventListener('click',print);
    newbtn.removeEventListener("click",newFile);
    loadfile.removeEventListener("change",loadFromFile);
    outputField.removeEventListener("click",edit);
    
}
function load(e){
    log(e)
    setUpLog()
    loadUi()
    getData(CONSTANTS.parseUrl).then(data=>{
        fillTemplate(data)
        setVars(data);
        
    })
    
}
window.addEventListener("DOMContentLoaded",load);