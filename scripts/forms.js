window.addEventListener("load", function(){
    let forms = document.querySelectorAll("form[ajax]");
    for(let i = 0; i < forms.length; i++){
        forms[i].addEventListener("submit", ajax); //
    }
});
function getInputs(form){
    let inputs = form.querySelectorAll('input:not([type=submit],[type=reset]), textarea, select');
    return inputs;
}
function getInputForms(form){
    let inputForms = form.querySelectorAll("div.form-floating");
    return inputForms;
}
function getData(inputs){
    let data = {};
    for(let i = 0; i < inputs.length; i++){
        if(inputs[i].type === "checkbox") {
            if (inputs[i].checked) {
                data[inputs[i].name] = inputs[i].value;
            }else{
                data[inputs[i].name] = "";
            }
        }else{
            data[inputs[i].name] = inputs[i].value;
        }
    }
    return data;
}
function setAlert(form, errorStr){
    let alertDiv = form.querySelector("div.alert");
    try {
        alertDiv.classList.remove("d-none");
        alertDiv.classList.add("d-block");
        alertDiv.classList.remove("alert-success");
        alertDiv.classList.add("alert-danger");
        alertDiv.innerHTML = errorStr;
    }catch (e) {
        console.log(e);
    }
}
function clearAlert(form) {
    try {
        let alertDiv = form.querySelector("div.alert");
        alertDiv.innerHTML = "";
        alertDiv.classList.remove("d-block");
        alertDiv.classList.add("d-none");
    }catch (e) {
        console.log(e);
    }
}
function clearErrors(form, floatingForms){
    let inputs = getInputs(form);
    let labels = [];
    for(let i = 0; i < floatingForms.length; i++){
        labels[floatingForms[i].firstElementChild.name] = floatingForms[i].querySelector("label");
    }
    for(let i = 0; i < labels.length; i++){
        labels[i].innerHTML = "";
    }
    for(let i = 0; i < inputs.length; i++){
        inputs[i].classList.remove("is-invalid");
    }
}
function success(form, successStr){
    let inputs = getInputs(form);
    for(let i = 0; i < inputs.length; i++){
        inputs[i].classList.add("is-valid");
    }
    try{
    let alertDiv = form.querySelector("div.alert");
    alertDiv.style.display = "block";
    alertDiv.classList.remove("alert-danger");
    alertDiv.classList.add("alert-success");
    alertDiv.innerHTML = successStr;
    }catch (e) {
        console.log(e);
    }
}

function ajax(e){
    e.preventDefault();
    let form = e.target;
    let inputs = getInputs(form);

    clearAlert(form);
    clearErrors(form, getInputForms(form));

    let data = getData(inputs);
    if(e.submitter.getAttribute("mod")){
        data.mod = e.submitter.getAttribute("mod");
    }
    console.log("data:");
    console.log(data);

    let target = form.action;
    postData(target, data).then(response => {
        console.log("response:");
        console.log(response);
        if(response.type === "error"){
            if(response.options.errors) {
                Object.keys(response.options.errors).forEach(function (key){
                    setError(form,key,response.options.errors[key]);
                    //console.log(response.options.errors[key]);
                })
            }
            if(response.message) {
                setAlert(form, response.message);
            }
        }
        if(response.type === "ok"){
            if(response.message){
                success(form, response.message);
            }
            if(response.options.redirect){
                window.location.href = response.options.redirect;
            }
        }
    });
}
function setError(form, inputId, message){
    try {
        let input = form.querySelector("#" + inputId);
        let inputDiv = input.parentElement;
        let label = inputDiv.querySelector("label");
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
        label.innerHTML = message;
    }catch (e) {
        console.log(e);
    }
}
function trainerInput(checkElement, form){
    let cvFormGroup = form.querySelector("#cvInput");
    let cvInput = cvFormGroup.querySelector("textarea");
    if(checkElement.checked){
        cvFormGroup.style.display = "block";
        cvInput.toggleAttribute("disabled");
    }else{
        cvFormGroup.style.display = "none";
        cvInput.toggleAttribute("disabled");
    }
}