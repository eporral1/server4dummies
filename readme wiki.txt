Silex:

php vendor/silex.phar version
Silex version 9ac208d 2012-01-21 07:30:14 +0100

Códigos de estados del protocolo HTTP
Para conocer mejor los códigos que HTTP puede retornar se puede consultar los siguientes enlaces
http://es.wikipedia.org/wiki/Anexo:C%C3%B3digos_de_estado_HTTP
http://developer.yahoo.com/social/rest_api_guide/http-response-codes.html
http://www.ietf.org/rfc/rfc2616.txt
http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
http://en.wikipedia.org/wiki/List_of_HTTP_header_fields
http://www.matlus.com/as-net-web-api-supporting-restful-crud-operations/

Tutorial para CURL
http://curl.haxx.se/docs/httpscripting.html

REST Client Plugin
Cliente de servicios REST para pruebas desde Firefox
https://addons.mozilla.org/en-US/firefox/addon/restclient/

Configuración del VirtualHost de Apache
<VirtualHost *:80>
    # Direccion de la carpeta web/ del proyecto
    DocumentRoot /home/micayael/development/phpprojects/silex-projects/ServidorRestSilex/web
    # Nombre por el que accederemos desde el navegador http://local.ServidorRestSilex
    ServerName local.ServidorRestSilex
    # Indica que dentro de la carpeta ubicada en DocumentRoot busque por defecto el archivo index.php
    DirectoryIndex index.php
    # Configuracion para crear logs independientes para los errores de apache y el registro de accesos
    ErrorLog /var/log/apache2/local.ServidorRestSilex-error_log
    CustomLog /var/log/apache2/local.ServidorRestSilex-access_log common

    <Directory "/home/micayael/development/phpprojects/silex-projects/ServidorRestSilex/web">
        AllowOverride All
        Allow from All
    </Directory>

</VirtualHost>

SQL para la base de datos
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(3) NOT NULL,
  `email` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

Probar los servicios utilizando CURL

GET: Sin enviar los parámetros de autenticación
$ curl -i -X GET -H "Accept: application/json" http://local.ServidorRestSilex/ver-comentarios.json
HTTP/1.1 401 Authorization Required
Date: Tue, 31 Jan 2012 21:48:06 GMT
Server: Apache/2.2.20 (Ubuntu)
X-Powered-By: PHP/5.3.6-13ubuntu3.3
www-authenticate: Basic realm="Autenticacion requerida"
cache-control: no-cache
Content-Length: 12
Content-Type: application/json

Unauthorized
GET: Enviando los parámetros de autenticación
$ curl -i --user "admin:123456" -X GET -H "Accept: application/json" http://local.ServidorRestSilex/ver-comentarios.json
HTTP/1.0 200 OK
Date: Tue, 31 Jan 2012 21:50:02 GMT
Server: Apache/2.2.20 (Ubuntu)
X-Powered-By: PHP/5.3.6-13ubuntu3.3
cache-control: no-cache
Content-Length: 640
Connection: close
Content-Type: application/json

[{"id":"1","author":"xxx","email":"xxx","content":"xx'xx","created_at":"2012-01-30 19:38:30","updated_at":"2012-01-30 19:38:30"},{"id":"9","author":"xxx","email":"xxx","content":"xx'xx","created_at":"2012-01-30 19:36:56","updated_at":"2012-01-30 19:36:56"},{"id":"10","author":"xxx","email":"xxx","content":"xx'xx","created_at":"2012-01-30 19:37:08","updated_at":"2012-01-30 19:37:08"},{"id":"11","author":"xxx","email":"9","content":"xx'xx","created_at":"2012-01-30 19:48:18","updated_at":"2012-01-30 19:48:18"},{"id":"12","author":"aaa","email":"9","content":"xx'xx","created_at":"2012-01-30 19:48:36","updated_at":"2012-01-30 19:48:36"}]

POST: Sin enviar los parámetros para la inserción
$ curl -i --user "admin:123456" -X POST http://local.ServidorRestSilex/crear-comentario.html
HTTP/1.0 400 Bad Request
Date: Tue, 31 Jan 2012 21:51:28 GMT
Server: Apache/2.2.20 (Ubuntu)
X-Powered-By: PHP/5.3.6-13ubuntu3.3
cache-control: no-cache
Vary: Accept-Encoding
Content-Length: 24
Connection: close
Content-Type: text/html; charset=UTF-8

Parametros insuficientes

POST: Enviando los parámetros para la inserción
$ curl -i --user "admin:123456" -d "comment[author]=John Doe&comment[email]=jdoe@gmail.com&comment[content]=this is a test" -X POST http://local.ServidorRestSilex/crear-comentario.html
HTTP/1.0 201 Created
Date: Tue, 31 Jan 2012 21:53:08 GMT
Server: Apache/2.2.20 (Ubuntu)
X-Powered-By: PHP/5.3.6-13ubuntu3.3
cache-control: no-cache
Vary: Accept-Encoding
Content-Length: 17
Connection: close
Content-Type: text/html; charset=UTF-8
X-Pad: avoid browser bug

Comentario creado

PUT: En caso de no existir un ID a actualizar
$ curl -i --user "admin:123456" -X PUT -d "comment[content]=this is a new test" http://local.ServidorRestSilex/actualizar-comentario/0.html
HTTP/1.0 404 Not Found
Date: Tue, 31 Jan 2012 21:59:06 GMT
Server: Apache/2.2.20 (Ubuntu)
X-Powered-By: PHP/5.3.6-13ubuntu3.3
cache-control: no-cache
Vary: Accept-Encoding
Content-Length: 25
Connection: close
Content-Type: text/html; charset=UTF-8
X-Pad: avoid browser bug

Comentario no encontrado
PUT: Actualizando correctamente el registro
$ curl -i --user "admin:123456" -X PUT -d "comment[content]=this is a new test" http://local.ServidorRestSilex/actualizar-comentario/1.html
HTTP/1.0 200 OK
Date: Tue, 31 Jan 2012 21:59:49 GMT
Server: Apache/2.2.20 (Ubuntu)
X-Powered-By: PHP/5.3.6-13ubuntu3.3
cache-control: no-cache
Vary: Accept-Encoding
Content-Length: 33
Connection: close
Content-Type: text/html; charset=UTF-8

Comentario con ID: 14 actualizado

DELETE: En caso de no existir el ID a eliminar
$ curl -i --user "admin:123456" -X DELETE http://local.ServidorRestSilex/eliminar-comentario/0.html
HTTP/1.0 404 Not Found
Date: Tue, 31 Jan 2012 22:00:47 GMT
Server: Apache/2.2.20 (Ubuntu)
X-Powered-By: PHP/5.3.6-13ubuntu3.3
cache-control: no-cache
Vary: Accept-Encoding
Content-Length: 25
Connection: close
Content-Type: text/html; charset=UTF-8
X-Pad: avoid browser bug

Comentario no encontrado

DELETE: En caso de existir el ID a eliminar
$ curl -i --user "admin:123456" -X DELETE http://local.ServidorRestSilex/eliminar-comentario/1.html
HTTP/1.0 200 OK
Date: Tue, 31 Jan 2012 22:01:16 GMT
Server: Apache/2.2.20 (Ubuntu)
X-Powered-By: PHP/5.3.6-13ubuntu3.3
cache-control: no-cache
Vary: Accept-Encoding
Content-Length: 31
Connection: close
Content-Type: text/html; charset=UTF-8

Comentario con ID: 14 eliminado