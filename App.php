<?php


class App
{
    public $gitRoot;

    public function init()
    {
        echo Git::init($this->gitRoot . '/' . $_POST['repo_name']);
    }

    public function getInfoRefs()
    {
        $service = trim($_GET['service'], 'git-');
        $repo_path = $this->gitRoot . explode('.git', $_SERVER['REQUEST_URI'])[0] . '.git';

        echo Git::getInfoRefs($service, $repo_path);
    }

    public function command()
    {
        $input = file_get_contents('php://input');

        $pattern = '~/git-([a-z]+)-pack~ims';
        if (preg_match($pattern, $_SERVER['PHP_SELF'], $match)) {
            $repo_path = $this->gitRoot . explode('.git', $_SERVER['REQUEST_URI'])[0] . '.git';
            $command = sprintf('%s-pack', $match[1]);

            header("Content-Type: application/x-git-$command-result");
            header("HTTP/1.1 200 OK");

            file_put_contents('repo.data', $repo_path);

            echo Git::command($command, $input, $repo_path);

        } else {
            header("HTTP/1.1 404 NOT Found");
        }
    }
}

