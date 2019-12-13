# Work Order Module for Invoice Ninja

This module adds work orders functionality to Invoice Ninja.

## Features
- Note history tracking
- Ability to create a custom intake form

## Installation

```
php artisan module:install dicarlosystems/workorder --type=github-https
```

After installing, run the migrations:

```
php artisan module:migrate WorkOrder
```