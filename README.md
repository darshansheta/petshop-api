# Petshop API Setup

1. Clone this repository
2. Run ```composer install```
3. Run ```cp .env.example .env```
4. Setup your MYJWT_PUBLIC_KEY and MYJWT_PRIVATE_KEY value in .env file and generate app key
5. Run ```php artisan migrate:fresh --seed```
6. Run ```php artisan serve```
7. php artisan test
8. change .env file App_env = local && composer dump-autoload && php artisan config:cache
9. Run ```php artisan l5-swagger:generate``` to generate swagger documentation api 
10. To run testcase adjust value of file ```.env.testing```
11. Run ```php artisan test```

#ENV to adjust
MYJWT_PUBLIC_KEY <br>
MYJWT_PRIVATE_KEY <br>


# L3 Challenge Package
I have build currency exchange package as local package in same repository

[darshan/daily-exchanger](https://github.com/darshansheta/packages/darshan/daily-exchanger)

# Challenge Code
- I have used Guzzle client to call external url even we can do with file_get_contents function
- Created simple DailyExchanger simple class for all my logical functions
- Created api route and registered above class and route with controller to expose api ```daily-exchanger/currency-to```

### To run testcase
- enter to package folder ```cd packages/darshan/daily-exchanger```
- Run command ```../../../vendor/bin/phpunit```


# PHP insights score <br>
<img width="550" alt="image" src=""> <br>

# JWT
- As primary requirement I have implemented JWT auth using [lcobucci/jwt] library
- Created custom auth guard named `jwt` so I was to able to use default Auth facade to do authentication
- Implementation is almost like library to doing some couple of change we can use it as package
- Yes, as requested implemented an asymmetric ( public and private keys )
- Also added custom claim such uuid of user in uid key of claim header
- refer files inside `app/Services/Auth` for implementation
- I added some Feature test that do validate token 

