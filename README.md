# RAS

# Project info
- Laravel 10.26  
- PHP 8.2


# Docker

Building image:
```shell
docker build --target prod -t ras-shoper-plugin .
```

Starting image:
```shell
docker run --rm -it --env-file .env -p 8000:80 ras-shoper-plugin
```

Starting developer environment:
```shell
./start-docker-dev
```

Setup:
* Fill environment variables (copy .env.example file to .env before):
  * Database host, port and credentials
  * Database type (mysql or pgsql)
  * Appstore credentials
  * APP_URL (api url, without trailing slash)
  * APP_KEY (two ways)
    * Generate it with command ```php artisan key:generate``` from the container
    * Generate it manually, using the command ```shuf -er -n32 {a..z} {0..9} | tr -d '\n' | base64 ``` then change the ```APP_KEY=``` line to ```APP_KEY=base64:key``` where ```key``` is the output from the above command

Project has endpoint ```/api/health``` which can be used for healthchecks
