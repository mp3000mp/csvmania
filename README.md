# CSV MANIA

Projet datant de 2018 afin de facilité la saisie en masse d'émotion ressentie à partir de commentaires contenus dans des fichiers csv pour alimenter un réseau de neurones (IA deep learning)

[![In Progress](https://img.shields.io/badge/in%20progress-yes-red)](https://img.shields.io/badge/in%20progress-yes-red)
[![License](https://img.shields.io/badge/License-Apache%202.0-blue.svg)](https://opensource.org/licenses/Apache-2.0)

## Run

```sh
npm i
npm run build

# update settings in env.local file (copied from .env)
composer install

php bin/console doctrine:database:create
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load
```

## Démo

Todo

