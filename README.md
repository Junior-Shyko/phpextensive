# PHPEXTENSIVE

### Instalação

    composer require juniorshyko/phpextensive


### 💡 - Instalação

```php
    require  __DIR__.'/vendor/autoload.php';
    
    use JuniorShyko\Phpextensive\Extensive;
```

### 📢  - Como usar
```php
    $e = new  Extensive();
    echo  $e->extensive( 1001 ); // mil e um reais
    echo  $e->extensive( 1001, Extensive::COIN ); // mil e um reais
    echo  $e->extensive( 54001.99, Extensive::MALE_NUMBER ); // cinquenta e quatro mil e um e noventa e nove centésimos
    echo  $e->extensive( 185001.084 ); // cento e oitenta e cinco mil e um reais e oitenta e quatro milésimos
    echo  $e->extensive( 4001.17, Extensive::MALE_NUMBER ); // quatro mil e um e dezessete centésimos
```

## 👤 Autor
Junior Oliveira : https://github.com/Junior-Shyko (Github:  @Junior-Shyko)

## 👤 Contribuição

 - Contribuição sempre é bem vinda.
 - Tarefa principal é a tradução baseada em *transaction laravel*


#### Baseado em um projeto existente
Link: [@thiagodp](https://github.com/thiagodp/extenso)