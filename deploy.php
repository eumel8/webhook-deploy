<?php

// 1. generate ssh-key for httpd user
// 2. put the public key as deploy key on github
// 3. setup json webhook 
// 4. set up local ssh config (i.e. /var/lib/wwwrun/.ssh/config)
// Host *
//         StrictHostKeyChecking no
//                 PasswordAuthentication no
//


// Set Variables
$LOCAL_ROOT         = "/data/www/";
$LOCAL_REPO_NAME    = "www.example.de";
$LOCAL_REPO         = "{$LOCAL_ROOT}/{$LOCAL_REPO_NAME}";
$REMOTE_REPO        = "git@github.com:xxxxx/xxxxx.git";
$BRANCH             = "master";
$SECRET_KEY	    = "XXXXXXXXXXXXXXXX";

// End of configuration settings
$payload_github = file_get_contents('php://input');
$data = json_decode($payload_github,true);

if ($SECRET_KEY !== NULL) {
	list($algo, $hash) = explode('=', $_SERVER['HTTP_X_HUB_SIGNATURE'], 2) + array('', '');
	if ($hash !== hash_hmac($algo, $payload_github, $SECRET_KEY)) {
       		echo "No maching secret key ";
      		exit;
	}
};

$git_url = $data['repository']['ssh_url'];

if ( $git_url === "{$REMOTE_REPO}" ) {

  // Only respond to POST requests from Github
  echo "Payload received from GitHub".PHP_EOL;

  if( file_exists($LOCAL_REPO) ) 
  {

    $sshagent = shell_exec("cd {$LOCAL_REPO} && ssh-agent bash -c 'ssh-add; git checkout {$BRANCH}; git pull;'");
    echo "sshagent: $sshagent".PHP_EOL;

    die("Sucess deployed! " . mktime());    
  } 
  else 
  {

    // If the repo does not exist, then clone it into the parent directory
    shell_exec("cd {$LOCAL_ROOT} && ssh-agent bash -c 'ssh-add; git clone -b {$BRANCH} {$REMOTE_REPO} {$LOCAL_REPO_NAME}'");
    echo "git clone: repo cloned successfully!".PHP_EOL;


    die("Sucessfuly cloned! " . mktime());
  }
} 
else 
{
    echo "Payload is not from GitHub. Nothing to see here!".PHP_EOL;
}
