let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
}


document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});

function iniciarApp() {
   // alert('App Lista');
    //console.log('Funciona');
    mostrarSeccion(); // Muestra y Oculta las secciones
    tabs(); // Cambia la seccion cuando se presiones los tabs
    botonesPaginador(); // Agrega o quita los botones del paginador
    paginaSiguiente(); 
    paginaAnterior();

    consultarAPI(); // Consulta la API en el backend de PHP

    idCliente();
    nombreCliente(); // Añade le nombre del cliente al objeto de cita
    seleccionarFecha(); // Añade la fecha del al cita en el objeto
    seleccionarHora(); // Añade la hora de la cita en el objeto

    mostrarResumen(); // Muestra el resumen de la cita
}

function mostrarSeccion(){
    //console.log('Mostrando seccion...');

     // Ocultar la sección que tenga la clase de mostrar
     const seccionAnterior = document.querySelector('.mostrar');
     if(seccionAnterior) {
         seccionAnterior.classList.remove('mostrar');
     }
    // Seleccionar la seccion con el paso...
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    seccion.classList.add('mostrar');

    // Quita la clase de actual al tab anterior
    const tabAnterior = document.querySelector('.actual');
    if(tabAnterior) {
        tabAnterior.classList.remove('actual');
    }

    // Resalta el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`); //Va entre corchetes por es es unatributo personalizado
    tab.classList.add('actual');
}

function tabs(){
    const botones = document.querySelectorAll('.tabs button');
   // console.log(botones);

   botones.forEach( boton => {
        boton.addEventListener('click', function(e) {
            //console.log('Disque Click');
            //console.log( parseInt( e.target.dataset.paso)); //Si sale negro es string y si es azul es numero
            //por eso utiliza parseInt
            paso = parseInt(e.target.dataset.paso);

            mostrarSeccion();

            botonesPaginador(); 

            
        });
   })
}

function botonesPaginador(){
    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');

    if(paso === 1) {
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    } else if (paso === 3){
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');

        mostrarResumen();
    } else {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }

    mostrarSeccion();
}

function paginaAnterior(){
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function(){
        
        if(paso <= pasoInicial) return;
        paso--;
        //console.log(paso);
        botonesPaginador();
    });
}


function paginaSiguiente() {
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function() {

        if(paso >= pasoFinal) return;
        paso++;
        
        botonesPaginador();
    })
}

async function consultarAPI() {

    try {
        const url = '/api/servicios';
        const resultado = await fetch(url);        
        const servicios = await resultado.json();
        mostrarServicios(servicios);
       // console.log(servicios);
    
    } catch (error) {
        console.log(error);
    }
}

function mostrarServicios(servicios) {
    servicios.forEach( servicio => {
        const { id, nombre, precio } = servicio; // Esta Aplicando Distruccion

       // console.log(id); // Muestra los id

        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$${precio}`;

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
       // console.log(servicioDiv);
        servicioDiv.onclick = function() {
            seleccionarServicio(servicio);
        }

        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv);

    });
}

function seleccionarServicio(servicio){
   // console.log(servicio);
    const { id } = servicio;
    const { servicios } = cita; // Estoy extrayendo los servicios del objeto cita

     // Identificar el elemento al que se le da click
     const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

     // Comprobar si un servicio ya fue agregado 
    if( servicios.some( agregado => agregado.id === id ) ) {
        console.log('Ya esta agregado');
        // Eliminarlo
        cita.servicios = servicios.filter( agregado => agregado.id !== id ); // filter array method
        divServicio.classList.remove('seleccionado');
    } else {
        console.log('Articulo nuevo, no estaba agregado');
        // Agregarlo
        cita.servicios = [...servicios, servicio];
        divServicio.classList.add('seleccionado');
    }

   // cita.servicios = [...servicios, servicio]; // Toma una copia de los servicios(...servicios) y tambien le agrega el nuevo sevicios

    console.log(cita);
}

function idCliente() {
    // console.log(cita);
    const id = document.querySelector('#id').value;
    // Le asignamos al bojeto cita, para el valor de nombre
    cita.id = id;
 
   // console.log(nombre);
 }

function nombreCliente() {
   // console.log(cita);
   const nombre = document.querySelector('#nombre').value;
   // Le asignamos al bojeto cita, para el valor de nombre
   cita.nombre = nombre;

  // console.log(nombre);
}

function seleccionarFecha(){
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e){
        //console.log('Selecionaste una Fecha ');
       // console.log(inputFecha.value);
       // console.log(e.target.value);
       // cita.fecha = inputFecha.value;

       const dia = new Date(e.target.value).getUTCDay();
       //console.log(dia);
       if( [6,0].includes(dia) ){
         e.target.value = '';
        // console.log('Sabados y Domingos no abrinos')
        mostrarAlerta('Fines de semana no permitidos', 'error', '.formulario');
       } else {
        console.log('correcto');
        cita.fecha = inputFecha.value;
       }

    });
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true){

    // Previene que se generen más de 1 alerta
    const alertaPrevia = document.querySelector('.alerta');
    
    if(alertaPrevia) {
        alertaPrevia.remove();
    }
    
    // Scripting para crear la alerta
    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);
    //console.log(alerta);
    if(desaparece){
        // Eliminar la alerta
        setTimeout( () => {
            alerta.remove();
        }, 3000);
    }

    

}

function seleccionarHora() {
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e) {


        const horaCita = e.target.value;
        const hora = horaCita.split(":")[0];
        if(hora < 10 || hora > 18) {
            e.target.value = '';
            mostrarAlerta('Hora No Válida', 'error', '.formulario');
        } else {
            cita.hora = e.target.value;

            // console.log(cita);
        }
    });
}

function mostrarResumen(){
    const resumen = document.querySelector('.contenido-resumen');
    //console.log(cita);
    //console.log(Object.values(cita));

    // Limpiar contenido de resumen
    while(resumen.firstChild) {
        resumen.removeChild(resumen.firstChild);
    }

    console.log(cita.servicios.length);
    if(Object.values(cita).includes('') || cita.servicios.length === 0){
       // console.log('Hacen falta datos o Servicios');
       mostrarAlerta('Faltan datos de Servicio, Fecha u Hora', 'error', '.contenido-resumen', false);
    
       return;
    }
    
    // Formatear el div de resumen
    const { nombre, fecha, hora, servicios } = cita;


    // Heading para Servicios en Resumen
    const headingServicios = document.createElement('H3');
    headingServicios.textContent = 'Resumen de Servicios';
    resumen.appendChild(headingServicios);

    // Iterando y mostrando los servicios
    servicios.forEach(servicio => {
        const { id, precio, nombre } = servicio;
        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    });

    // Heading para Cita en Resumen
    const headingCita = document.createElement('H3');
    headingCita.textContent = 'Resumen de Cita';
    resumen.appendChild(headingCita);

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    // Formatear la fecha en español
    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate() + 2;
    const year = fechaObj.getFullYear();

    const fechaUTC = new Date( Date.UTC(year, mes, dia));
    
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}
    const fechaFormateada = fechaUTC.toLocaleDateString('es-MX', opciones);

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`;

    // Boton para Crear una cita
    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.onclick = reservarCita;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);

    resumen.appendChild(botonReservar);
    
}

 async function reservarCita(){
    //console.log('Reservanmdo Cita.....');
    const { nombre, fecha , hora, servicios, id} = cita;

    const idServicios = servicios.map( servicio => servicio.id); // Difrencia / Foreach y map ,1ero itera y 2do busca concidencias
   // console.log(idServicios);

    const datos = new FormData();
   // datos.append('nombre', nombre);
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('usuarioid', id);
    datos.append('servicios', idServicios);


  //  console.log([...datos]); 

   // return; // Para que no me mande la peticion

   //El FormData en java script actua como el submit
   // console.log(datos); // No nos va arrojar ningun resultado
   // console.log([...datos]); // Con ... creasmo una copia del objeto

    try {
        //Peticion hacia la API
   const url = '/api/citas';
   const respuesta = await fetch(url, {
        method: 'POST',
        body: datos
   });

   //console.log(respuesta);
   const resultado = await respuesta.json();
   console.log(resultado.resultado);  

   if(resultado.resultado){
    Swal.fire({
        icon: "success",
        title: "Cita Creada...",
        text: "Tu cita fue creada correctamente!",
        //footer: '<a href="#">Why do I have this issue?</a>'
        button: 'OK'
      }).then( () => {
        setTimeout( () => {
            window.location.reload();
        }, 3000);        
      });
   }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error...",
            text: "Hubo un error al guardar la cita!",
            //footer: '<a href="#">Why do I have this issue?</a>'
          });
    }

   

}