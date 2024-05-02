# Praying Time Generator
Praying time based on area and subscriptions

# How this is work.
### Routing
All pages should be accessing using ".html" extension.
For now there are two existing page: subscriber.html
and admin.html

URL Pattern: some-page.html/(some-sub-page)/(...params)  
To make this URL working, now you need to create the 
new "SomePageController" controller class 
(see directory structure), which have a method 
"someSubPageAction", and accept params.

### Directory structure
.  
├── cache  
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
│   │   ├── Controllers             #Where you put your controllers. 
│   │   ├── Models                  #Where you put models for data layering.  
│   │   └── Views                   #Where you put your twig files.                      
│   └── lib    
│   │   ├── Api.php                 #Class to handle API call.  
│   │   ├── DB.php                  #Class to manage DB connection.  
│   │   ├── Request.php             #Class to make a new request using CURL.    
│   │   ├── Route.php               #Class to handle routing based on given URI.    
│   │   ├── Str.php                 #Class to handle string manipulation.  
│   │   ├── User.php                #Class to store basic information for log in user.  
│   │   └── View.php                #Class to instantiate twig library.                
│   ├── bootstrap.php               #File, in here we start the session, and declare some basic variable.               
│   ├── function.php                #File, include some shortcut function to call instantiate an object.
├── .env                            #File, declare environment variable, e.g: DB auth, etc.  
└── ...


# Why my audio did not play automatically?
Most of the browser change their auto play 
policies. You need to change some configuration.
- https://developer.chrome.com/blog/autoplay
- https://webkit.org/blog/7734/auto-play-policy-changes-for-macos/
# Suggestion
- For weekly updates, it will be more clear if it's
based on current system week, so first day of the week
will be always Monday, and last date is Sunday.

-
