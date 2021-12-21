# X1

A very simple web based note solution that's designed to serve as my second brain.

![X1 Screenshot](media/x1-screenshot.png)

## Starting Server

To start using this tool simply clone the repo and then run PHP in server mode (for development and testing only) from the repo's root directory.

`php -S 0:8001`

Then open your web browser and point it to `https://localhost:8001`.

## More Information

The back-end of this is currently a very poorly written PHP application. I wrote part of the application before I figured out how I wanted to organize it. Still, it's a really simple application and I plan to clean it up later. In the long term I'm thinking I may re-write it in GO once I get a feel for how it works for me. PHP is the language I use professionally and have for many years, so it currently serves as my hammer.

The front-end uses htmx (but there's also a little bit of old JS code in there too). Just like the back-end, I added a bit of code before I settled on htmx. This is also my first time using htmx.

## X as in Experiments

Naming software experiments is a burden to entry for me. Typically, I need to come up with a name before I can even create a project. Using numbers removes that burden and lets me start with a readme or a bit of code. This is the first of those numbered experiments.

Because their numbered, I can more easily decide on ports and domain names to run them on. For example, for development work, I'll run experiments on port 8000-8999. Since this is experiment number one, I'll run it on port 8001.

## Alternate Database Filename

By default the database filename is `pages.sqlite`. If you want to use a different filename you can set the environment variable `X1_FILE` to the name of the DB to use.

`SET X1_FILE=example.sqlite`

Because PHP's built-in development server doesn't read environment variables by default, you need to pass a new `variablers_order` value to PHP. Here's the launch command to use if you're not using the default filename.

`php -d variables_order=EGPCS -S 0:8001`
