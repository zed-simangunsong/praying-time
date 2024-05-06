# About App  
System has many subscribers. Each subscriber has subscribed 
to several music boxes. Each box can have many voicesover 
(.mp3 files) and in specific prayer zone like WLY01, WLY02,… .  

We will have a simplevoice over say that “Time to pray”. We 
want to have an application to play this voiceover in prayer
time every day. Prayer time can be retrieved from API.

# How this App work.
### Routing

> [!NOTE]  
> This App implement simple MVC pattern (see the directory structure 
> below), which mean all request will be handled by specific 
> controller under `src/app/controller`.  
>
> Any pages should be accessing using `{page}.html` pattern in the 
> URL, which represented by a controller. This mean every unique 
> `{page}.html` will have a unique controller too.


> [!NOTE]  
> Example: `some-page.html/(action-segment)/(...param-segments)`  
> If we access URL like that, that's mean to make it working, we need
> to create a controller file called `SomePageController.php`, to 
> declare a class `SomePageController` (same with file name). And
> this class should have a method called `actionSegmentAction`, which
> accept as many as available `param-segments` as parameter.  
> Please note `action-segment` is optional, so if the segment did not
> given in the URL, system will use `indexAction` as default action.  

> [!TIP]  
> Naming convention: `PascalCaseController` for controller, and
> `camelCaseAction` for action.  
> `Controller` and `Action` suffix is generated automatically, so
> does need to include in the URL.

For now there are two pages by default:  
- **subscriber.html**, page for client which need to login
using their subscribe name, by default password would be 
`password` (you can change this in **migrations/seeder/script.php**)   

- **admin.html**, page for admin to see the subscriber, box, and
generated song list.  
Credential: **admin / admin**



# Libraries, and why?
* [`vlucas/phpdotenv`](https://github.com/vlucas/phpdotenv), 
easier way to separate configuration between each environment.
* [`pecee/pixie`](https://github.com/skipperbent/pecee-pixie), 
avoid using native query when not needed. It will be easier to 
maintain the query using query builder. Also this query builder 
did not have any other dependencies.
* [`twig/twig`](https://github.com/twigphp/Twig), for templating.
Why template? 
  * It will allow us to maintain our code far easier, by 
  separating logic layer from presentation layer.
  * Without reinventing the wheel, template usually offer
  cache handler (for better performance), variable sanitation
  and many others feature.
* [`phpmailer/phpmailer`](https://github.com/PHPMailer/PHPMailer),
a well known libray for sending email over SMTP, and easy to use.
Without a lot of effort, we can send our email even using HTML format.
* [`doctrine/migrations`](https://github.com/doctrine/migrations), 
maintain DB migration history, which allow us to know what changes 
in what time and we can rollback those changes in no time.

### File structure

```
.  
├── migrations                 
│   ├── seeder   
│   │   ├── color.txt               # Dummy for seeding the data  
│   │   ├── script.php              # Script for seeding the data.  
│   │   ├── word.txt                # Dummy for seeding the ddata            
│   ├── cron.php                    # Script to generate song.            
│   ├── raw-migration.sql           # MysQL script for manual migration.  
│   └── Version20240503045600.php   # Migration file using CLI-command.  
├── public                  
│   ├── css  
│   │   ├── app.css  
│   │   └── login.css  
│   ├── images  
│   ├── scss  
│   │   └── app.scss  
│   └── songs  
├── src                  
│   ├── app  
│   │   ├── Controllers             # Where you put your controllers.  
│   │   ├── Models                  # Where you put models for data layering.  
│   │   └── Views                   # Where you put your twig files.                     
│   └── lib  
│   │   ├── Api.php                 # Class to handle API call.  
│   │   ├── DB.php                  # Class to manage DB connection.  
│   │   ├── Request.php             # Class to make a new request using CURL.    
│   │   ├── Route.php               # Class to handle routing based on given URI.    
│   │   ├── Str.php                 # Class to handle string manipulation.  
│   │   ├── User.php                # Class to store basic information for log in user.  
│   │   └── View.php                # Class to instantiate twig library.                
│   ├── bootstrap.php               # File, in here we start the session, and declare some basic variable.               
│   ├── function.php                # File, include some shortcut function.
├── .env.example                    # File, declare environment variable, e.g: DB auth, etc.  
├── cli-config.php                  # File configuratin to support [`doctrine/migrations`](https://www.doctrine-project.org/projects/doctrine-migrations/en/3.7/index.html), see installation step below. 
├── composer.json         
├── composer.lock 
├── migrations.php                  # File configuration to support [`doctrine/migrations`](https://www.doctrine-project.org/projects/doctrine-migrations/en/3.7/index.html), see installation step below.          
├── package.json 
├── package-lock.json
└── ...

```

# Why my audio did not play automatically?
Most of the browser change their auto play 
policies. You need to allow your browser to play the audio.
- https://developer.chrome.com/blog/autoplay
- https://webkit.org/blog/7734/auto-play-policy-changes-for-macos/


# Installation
Please follow these setup, to install the application in your environment.  

* Clone the repo & use master branch.
* Copy `.env.example` to `.env`, and update the basic configuration.  
E.g:  _database connection_ (please note database need to be created manually), 
_smtp_, etc.
* From app root installation, run these CLI commands:  
   * `Composer install`, in production no need to install dev package, to do that you
   can use `Composer install --no-dev`
   * `NPM install` (optional, do this if you wish to edit the _CSS_ using _SASS)
   * `vendor\bin\doctrine-migrations migrate`, you can omit this if you wish
   to use provided mysql script at "./migrations/raw-migration.sql"
   instead.
   * `php ./migrations/seeder/script.php` will generate dummy data.
   * `php ./migrations/cron.php` will generate song. You can setup your
   cron to make this executed periodically. For instance 
   `0 0 * * SAT /usr/bin/php /var/www/app_root/migrations/cron.php`
   will executed every Saturday at 00:00 (assume your php.exe under _/usr/bin/php_).

> [!CAUTION]  
> On `.env` file please do not forget to update `BASE_URL` option,
> so it will match your installation, otherwise the apps will not
> working properly.
> It should be pointing to "public" directory.

# Suggestion for improvement.
* Allow admin to manage all data (Subscriber, Box, Song) from admin 
page. 
* If the system growth and become more complex, it might be need to 
use ORM instead a plain query builder for maintenance sake.
* If one box can have 2 or more zone or need a master song table,
then we need to change the DB structure a bit so we are consistent 
using DB normalization.
* Make a better login system.
