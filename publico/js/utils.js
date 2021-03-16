function stopIt(e) {
    e.preventDefault();
    e.stopPropagation();
}

function limpiador(clase) {
    if (document.getElementsByClassName(clase)) {
        for (let i = 0; i < document.getElementsByClassName(clase).length; i++) {
            document.getElementsByClassName(clase)[i].remove();
        }
    }
}

export {
    stopIt,
    limpiador
}