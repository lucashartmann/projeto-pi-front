# PHP Unit (já incluído via composer neste projeto)
composer install

# Requirements PHP
composer require phpmailer/phpmailer
composer require google/apiclient

# TESTES COM JS

## - Instale Node.js.

## Inicializar npm e instalar dependências de teste
npm init -y
npm install --save-dev jest@29 jest-environment-jsdom @testing-library/dom

## Rodar testes JS
npm run test:js

# Ou rodar todos os testes (PHPUnit + JS)
vendor\bin\phpunit.bat tests