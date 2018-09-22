# VS CODE NOTAS

Insertar comentarios `shift + alt + A`

Comandos `Ctrl + Shift + P o F1`

> Con la extension Laravel Artisan se pueden lanzar comandos Artisan desde aquí

Mover linea arriba / abajo `Alt + up/down`

Seleccionar siguiente ocurrencia `Ctrl + D`

Duplicar linea `Shift + Alt + Down`


## Markdown

Archivos .md

Ver preview `Ctrl + Shift + V`



# FORGE & SERVERS

### Acceso General
Hay que tener una key ssh en el pc y la public key meterla en forge > ssh

Acceso SSH: `ssh forge@miip`
Acceso mysql: `mysql -uforge -p`
Ver databases: `show databases`
Descargar / backup database: (desde forge/server, no desde dentro de mysql) `mysqldump --user=usuarioforge --password=passfordforge nombrededb > archivo.sql`
Restaurar un bd de mysqldump: (desde forge/server, no desde dentro de mysql) `mysql -u root -p midb < D:\path\de\db.sql`

### Acceso Filezilla FTP
Seleccionar conexión FTP
Edición > Opciones > SFTP > Buscar clave privada 
Conexion con ip del servidor y cambiar modo de acceso a Normal y poner usuario de forge


