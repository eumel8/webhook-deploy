# Webhook Deploy
## A git based continuous integration tool for web sites

### Usecase
install and update a web site as virtual host under the help of git 

### Prerequisites
deployment webserver on the same environment where the deploy.php is hosted

### Installation
* copy deploy.php on webserver reachable from github.com (recommended with https)
* generate ssh-key for httpd-user and put the key in the home dir (i.e. /var/lib/wwwrun/.ssh)
* setup a json webhook in the git repo with the content of your web site you want to deploy
  use as URL the https:<deployment server>/deploy.php
* adjust repo URL, branch, web site, location and secret
* generate optionally /var/lib/wwwrun/.ssh/config

```
     Host *
           StrictHostKeyChecking no
           PasswordAuthentication no
```

### Operation
Commit to the web site repo and check if the webhook is working. After the first git clone the directory with
the web content should be there and you can configure the new vHost in the webserver configuration.

Put a .htaccess file in the web root to protect the git internal information

```
    RedirectMatch 404 /\.git
```
