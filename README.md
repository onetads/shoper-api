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
docker run --rm -it -p 8000:80 ras-shoper-plugin
```

Starting developer environment:
```shell
./start-docker-dev
```

Project has endpoint `/api/health`
