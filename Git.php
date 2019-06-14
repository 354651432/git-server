<?php


class Git
{

    public static function init($name)
    {
        $cmd = "git init --bare {$name}.git";

        return self::procExec($cmd);

    }

    public static function getInfoRefs($service, $repo_path)
    {
        if (in_array($service, ['upload-pack', 'receive-pack'])) {
            self::updateServerInfo($repo_path);
        }

        header("Content-Type: application/x-git-$service-advertisement");
        header("HTTP/1.1 200 OK");
        $cmd = "git $service --stateless-rpc --advertise-refs $repo_path";
        $out = shell_exec($cmd);
        $res = self::packetWrite("# service=git-$service");
        $res .= "0000";
        $res .= $out;

        return $res;
    }

    public static function updateServerInfo($repo_path)
    {
        $cmd = "git --git-dir $repo_path update-server-info";
        file_put_contents('updateServerInfo', $cmd);
        $out = shell_exec($cmd);
    }

    public static function command($command, $input, $repo_path)
    {
        $cmd = "git $command --stateless-rpc $repo_path";

        $res = self::procExec($cmd, $input);

        if ('receive-pack' == $command) {
            self::updateServerInfo($repo_path);
        }

        return $res;
    }

    public static function packetWrite($str)
    {
        $len = dechex(strlen($str) + 4);
        return "00$len$str";
    }

    public static function procExec($cmd, $input = null)
    {
        $proc = proc_open($cmd, [['pipe', 'r'], ['pipe', 'w']], $pipes);
        if (is_resource($proc)) {
            $input && fwrite($pipes[0], $input);
            fclose($pipes[0]);
            $res = '';
            while (!feof($pipes[1])) {
                $res .= fread($pipes[1], 8192);
            }
            fclose($pipes[1]);
            proc_close($proc);

            return $res;
        } else {
            return false;
        }
    }
}

