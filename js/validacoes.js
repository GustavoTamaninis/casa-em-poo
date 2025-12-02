document.addEventListener("DOMContentLoaded", function(){
    const form = document.getElementById("menuForm");

    if(form){
        form.addEventListener("submit", function(e){
            const button = e.submitter || document.activeElement;
            const acao = button ? button.value : "desconhecida";

            const confirmar = window.confirm("Confirma a ação: " + acao + "?");

            if(!confirmar){
                e.preventDefault();
                console.log("Envio cancelado.");
            }else{
                console.log("Envio confirmado para o PHP");
            }
        })
    }
})