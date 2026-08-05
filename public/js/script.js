document.addEventListener("DOMContentLoaded", function () {
    const selectRol = document.getElementById("rol");
    const cedulaContainer = document.querySelector(".cedula-container");
    const apellidoContainer = document.querySelector(".apellido-container");

    const nombreLabel = document.querySelector("label[for='nombre']");
    const cedulaInput = document.getElementById("cedulaJuridica");

    console.log(
        "Rol es: " + selectRol +
        "Cedula: " + cedulaContainer +
        "Apellido: "+ apellidoContainer +

        "Nombre: " + nombreLabel +
        "Cedula Input: "+ cedulaInput

    );

    if (!selectRol) {
        return;
    }

    function actualizarFormulario() {

        if (selectRol.value === "Empresa") {

            cedulaContainer.style.display = "block";
            apellidoContainer.style.display = "none";

            cedulaInput.required = true;

            nombreLabel.textContent = "Nombre de la Empresa";

        } else {

            cedulaContainer.style.display = "none";
            apellidoContainer.style.display = "block";

            cedulaInput.required = false;
            cedulaInput.value = "";

            nombreLabel.textContent = "Nombre de usuario";
        }
    }

    actualizarFormulario();

    selectRol.addEventListener("change", actualizarFormulario);

});

