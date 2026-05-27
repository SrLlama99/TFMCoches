Alonso
* Fix image

General
* idx {modelo marca usuario}.nombre trgm
* Add assets\images\avatar\defaultAvatar.jpg
* Regenerate secrets
* Generate folder structure

Deployment
* Install composer
* Enable php ext
  * dom
  * xml
  * ;extension=pdo_pgsql
  APP_ENV=prod
  APP_DEBUG=0
  apt install composer php8.2-{xml,dom,curl,pgsql} 
  phpenmod xml dom curl pgsql
  composer i --no-dev

