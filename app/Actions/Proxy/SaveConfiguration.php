<?php

namespace App\Actions\Proxy;

use App\Models\Server;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class SaveConfiguration
{
    use AsAction;

    public function handle(Server $server)
    {
        $proxy_settings = CheckConfiguration::run($server, true);
        $proxy_path = get_proxy_path();
        $docker_compose_yml_base64 = base64_encode($proxy_settings);

        $server->proxy->last_saved_settings = Str::of($docker_compose_yml_base64)->pipe('md5')->value;
        $server->save();

        return instant_remote_process([
            "mkdir -p $proxy_path",
            "echo '$docker_compose_yml_base64' | base64 -d > $proxy_path/docker-compose.yml",
        ], $server);
    }
}
