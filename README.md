Installing docker
------------------------

Visit https://docs.docker.com/ and choose your OS, follow instructions

Runing on docker
------------------------

Open project folder in a console and run:

$ docker-compose up

or to run in background:

$ docker-compose up -d

Create the database
------------------------

If you are running with docker you can access phpmyadmin at http://localhost:82/
Create your database with collation utf8_general_ci

Update username and password values at config/database.php file.

Open project folder in a console and run:

$ ./yii migrate

Confirm to create database tables

Acessing the API
-----------------------

You now can access the API at http://localhost:8000/