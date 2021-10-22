## Proyecto Vacunación con PHP

**_Práctica realizada por Natalia Cristóbal Rodríguez y Sandra González García_**

En este proyecto nos hemos basado en la página oficial de la
Comunidad de Madrid en la que se ofrece una autocita para la vacunación
contra la COVID-19.

Para el diseño de la interfaz hemos usado **Bootstrap** personalizándolo 
consiguiendo un resultado similar al original.

Teniendo en cuenta que la comunidad tiene ya una
base de datos creada con toda nuestra información, hemos 
tenido en cuenta esto a la hora de crear nuestro proyecto.
Por ello, no hemos creado una pantalla de registro, hemos 
creado un login directo ya que la **base de datos** está creada 
con los valores de los usuarios.

Los datos que ya están creados en la BD son únicamente
los que se necesitan en el login para que se redirija al formulario, 
una vez ahí el usuario pueda completar el registro.

Los usuarios de prueba y sus valores son los siguientes:

```sql
INSERT INTO usuarios VALUES ('11111111A', '111111111', 'Perez', null, null, null, null, null, null, null);
INSERT INTO usuarios VALUES ('22222222A', '222222222', 'Gonzalez', null, null, null, null, null, null, null);
INSERT INTO usuarios VALUES ('33333333A', '333333333', 'Cristobal', null, null, null, null, null, null, null);
INSERT INTO usuarios VALUES ('44444444A', '444444444', 'Garcia', null, null, null, null, null, null, null);
INSERT INTO usuarios VALUES ('55555555A', '555555555', 'Martinez', '2021-01-12 11:00:00', 'Wizink Center', 'Moderna', 'izquierdo', 'No', 'No', '2021-01-19 18:00:00');
INSERT INTO usuarios VALUES ('66666666A', '666666666', 'Rodriguez', '2021-04-01 14:00:00', 'Isabel Zendal', 'Pfizer', 'izquierdo', 'Si', 'No', '2021-05-01 12:00:00');
```

Los cuatro primeros usuarios son los que contienen valores nulos 
para probar la pantalla de formulario. Los dos restantes son 
una simulación de que el usuario ya ha sido registrado con todos los datos
necesarios.

Una vez el usuario rellena el **formulario**, se actualizan sus datos en 
la BD y se le muestra la información por pantalla.

Para el almacenamiento de datos hemos usado tanto sesiones como cookies.
Las **cookies**, al ser datos más sensibles, las hemos encriptado para 
ofrecer mayor seguridad.

Con el uso de **sesiones** hemos controlado el acceso entre distintas pantallas. 
Por ejemplo: Si un usuario que no ha rellenado el formulario intenta acceder 
a la pantalla de segunda dosis, se le llevará al formulario (si la 
sesión está iniciada) o al login.

Si el usuario ha realizado el formulario previamente, cuando este inicie 
sesión no será redirigido a este, sino que se mostrará la pantalla 
de segunda dosis. En ella se imprime la información ya registrada más 
el dato de segunda dosis.

Hemos añadido la pantalla de información para hacer la simulación más 
real. En ella también hemos incluido una pantalla extra con el uso de **AJAX** 
para el cálculo de la segunda dosis.

