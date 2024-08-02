# verifarma
Challenge Farmacias

<h4>Instrucciones</h4>
<p>
Descargar el repositorio y:<br>
<ol>
<li>Teniendo iniciado Docker ejecutar el comando <b>docker-compose up --build -d</b> en la raiz del proyecto para iniciar el contenedor</li>
<li>En el navegador ingresar a PHPMyAdmin ingresando la url <b>localhost:8081</b> con el usuario root y contraseña rootpassword</li>
<li>Estando en la interfaz de PHPMyAdmin ir a la solapa de Importar y realizar la importacion con el archivo db.sql</li>
<li>Una vez cargada la tabla de farmacias en <b>localhost:8080/api/</b> tenemos el inicio y en localhost:8080/api/docs la documentación</li>
<li>Para acceder a las acciones de la API la url es <b>localhost:8080/api/farmacias</b> (GET/POST)</li>
<li>El usuario y contraseña para la api son user y pass respectivamente</li>
<li>Los test unitarios se ejecutan con el comando <b>php artisan test</b> estando situado en src/api</li>
</ol>
</p>
