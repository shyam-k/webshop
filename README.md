# webshop# webshop

## Quickstart

***Startup***

The ddev environment can be created very easily:

```
ddev start
```

After the ddev startup you can setup laravel site by running following commands :

```
ddev composer install
```


```
ddev php artisan migrate
```


Once the laravel setup you can access the site with the following url:


```
https://loop.ddev.site/
```




## Import Masterdata


### Customer CSV

To Import the customer CSV file to database , run following command

```
ddev php artisan import:csv:customer https://backend-developer.view.agentur-loop.com/customers.csv  --auth=loop,backend_dev
```

### Products CSV

To Import the product CSV file to database , run following command

```
ddev php artisan import:csv:customer https://backend-developer.view.agentur-loop.com/products.csv  --auth=loop,backend_dev
```





## API Testing

To test the API's, import the file ```'Loop.postman_collection.json'``` to postman and test



## Links

* [Install Docker](https://docs.docker.com/#docker-products)
* [Install DDEV Local](https://ddev.readthedocs.io/en/stable/)