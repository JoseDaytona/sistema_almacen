# Proyecto API de Almacen

Este proyecto es una API RESTful construida con Laravel. Proporciona un conjunto de endpoints para gestionar recursos como usuarios, ariticulos, etc.

## Tabla de Contenidos

- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Migración de la Base de Datos](#migración-de-la-base-de-datos)
- [Ejecución de la Aplicación](#ejecución-de-la-aplicación)
- [Pruebas Unitarias](#pruebas-unitarias)

## Requisitos

Antes de instalar este proyecto de Laravel, asegúrate de tener lo siguiente instalado en tu máquina:

- PHP (>= 8.0)
- Composer
- MySQL
- Git (para clonar el repositorio)

## Instalación

Sigue estos pasos para instalar el proyecto en tu máquina local:

1. **Clona el repositorio**:

   ```bash
   git clone https://github.com/JoseDaytona/sistema_almacen.git
   cd sistema_almacen
   composer install

## Migración de la Base de Datos

Sigue estos pasos para la migración de la base de datos en tu máquina local:

   php artisan migrate        


## Ejecución de la Aplicación

Sigue estos pasos para la ejecución del proyecto en tu máquina local:

   php artisan serve


## Pruebas Unitarias

Sigue estos pasos para la ejecución de pruebas del proyecto en tu máquina local:

   1-Crear una base de datos llamada bd_almacen_test

   php artisan migrate

   php artisan test

