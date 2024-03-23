# api-cli
Command line interface for APIs and WebApps.

## Usage

### Execution from the command line

If you want to execute CLI commands from the command line, use the '-x' argument followed by the command youw ant to execute.

Example:
Suppose you want to create a cron job that executes `database cleanup tokens`, which will run once per minute and remove access tokens that have expired. You sould setup a cron job like this:

````
# m h  dom mon dow   command
  * *   *   *   *    /home/user/php/app/cli.php -x "database cleanup tokens"
````

*Note on paths*: the command above will **not** work out of the box. You must add the following to your cli.php file in order to change the working directory to that directory:
````
chdir(__DIR__);
````

This command will tell the system executing the scripts to change the working directory to the same directory where the cli.php directory is location. Otherwise, the cronjob will attempt to execute from the user's home directory, and it will not be able to run properly.