Validador de Cédula y RUC de Ecuador
=============================
<p align="center"><img src="http://res.cloudinary.com/edwin/image/upload/v1496095463/cedulaLogo_lmct8r.png"/></p>

<p align="center">

[![Packagist](https://img.shields.io/badge/Packagist-v1.0.0-orange.svg?style=flat-square)](https://packagist.org/packages/tavo1987/mini-framework)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square)](https://packagist.org/packages/tavo1987/mini-framework)

</p>

Este pequeño paquete ha sido desarrollado para validar fácilmente:

- Cédula
- RUC de persona natural
- RUC de sociedad privada
- RUC de sociedad pública

Introducción
-------------

Para el desarrollo de este paquete se ha tomado como base el siguiente repositorio [validacion-cedula-ruc-ecuador](https://github.com/diaspar/validacion-cedula-ruc-ecuador) creado por [diaspar](https://github.com/diaspar),
el cual ha sido modificado, para que sea mucho más fácil de instalar y usar en  cualquier proyecto PHP mediante composer.

Si quieres saber más sobre la lógica utilizada a este paquete puedes visitar el siguiente artículo [Cómo validar cédula y RUC en Ecuador](https://medium.com/@bryansuarez/c%C3%B3mo-validar-c%C3%A9dula-y-ruc-en-ecuador-b62c5666186f), donde se detalla el proceso manual.
 
Instalación
----
    composer require tavo1987/ec-validador-cedula-ruc

Uso
----

- Instanciar la clase y llamar al metodo para validar la identificación

```
// Crear nuevo objecto
$validador = new Tavo\ValidadorEc;

// validar CI
if ($validador->validarCedula('0926687856')) {
    echo 'Cédula válida';
} else {
    echo 'Cédula incorrecta: '.$validador->getMessage();
}

// validar RUC persona natural
if ($validador->validarRucPersonaNatural('0926687856001')) {
    echo 'RUC válido';
} else {
    echo 'RUC incorrecto: '.$validador->getMessage();
}

// validar RUC sociedad privada
if ($validador->validarRucSociedadPrivada('0992397535001')) {
    echo 'RUC válido';
} else {
    echo 'RUC incorrecto: '.$validador->getMessage();
}

// validar RUC sociedad pública
if ($validador->validarRucSociedadPublica('1760001550001')) {
    echo 'RUC válido';
} else {
    echo 'RUC incorrecto: '.$validador->getMessage();
}
```


Tests
-------

El paquete se encuentra con sus respectivos suite de tests (phpunit) los cuales puedes encontrarlos 
en el siguiente directorio `tests`

Cómo contribuir
------------

Si encuentras algún error o quieres agregar más funcionalidad, por favor siéntete libré de abrir un issue o enviar un pull request, que
lo analizaremos y agregaremos al nuestro repositorio lo mas pronto posible simpre y cuando cumpla con las siguientes reglas

- Todos los Test debe estar en verde  es decir funcionando
- Si esbríbes una nueva funcionalidad este debe tener su propio test, para probar la misma

Contactos
------------
Edwin Ramírez 
- Twitter: [@edwin_tavo](https://twitter.com/edwin_tavo)

Bryan Suárez 
- Twitter: [@BryanSC_7](https://twitter.com/BryanSC_7)