<?php

namespace Database\Seeders;

use App\Data\ServerMetadata;
use App\Enums\ProxyStatus;
use App\Enums\ProxyTypes;
use App\Models\GithubApp;
use App\Models\GitlabApp;
use App\Models\InstanceSettings;
use App\Models\PrivateKey;
use App\Models\Server;
use App\Models\StandaloneDocker;
use App\Models\Team;
use App\Models\User;
use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        // Fix for 4.0.0-beta.37
        if (User::find(0) !== null && Team::find(0) !== null) {
            if (DB::table('team_user')->where('user_id', 0)->first() === null) {
                DB::table('team_user')->insert([
                    'user_id' => 0,
                    'team_id' => 0,
                    'role' => 'owner',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        if (InstanceSettings::find(0) == null) {
            InstanceSettings::create([
                'id' => 0
            ]);
        }
        if (GithubApp::find(0) == null) {
            GithubApp::create([
                'id' => 0,
                'name' => 'Public GitHub',
                'api_url' => 'https://api.github.com',
                'html_url' => 'https://github.com',
                'is_public' => true,
                'team_id' => 0,
            ]);
        }
        if (GitlabApp::find(0) == null) {
            GitlabApp::create([
                'id' => 0,
                'name' => 'Public GitLab',
                'api_url' => 'https://gitlab.com/api/v4',
                'html_url' => 'https://gitlab.com',
                'is_public' => true,
                'team_id' => 0,
            ]);
        }

        if (config('app.name') !== 'Coolify Cloud') {
            // Save SSH Keys for the Coolify Host
            $coolify_key_name = "id.root@host.docker.internal";
            $coolify_key = Storage::disk('ssh-keys')->get("{$coolify_key_name}");

            if ($coolify_key) {
                PrivateKey::updateOrCreate(
                    [
                        'id' => 0,
                        'name' => 'localhost\'s key',
                        'description' => 'The private key for the Coolify host machine (localhost).',
                        'team_id' => 0,
                    ],
                    ['private_key' => $coolify_key]
                );
            } else {
                echo "No SSH key found for the Coolify host machine (localhost).\n";
                echo "Please generate one and save it in /data/coolify/ssh/keys/{$coolify_key_name}\n";
                echo "Then try to install again.\n";
                exit(1);
            }
            // Add Coolify host (localhost) as Server if it doesn't exist
            if (Server::find(0) == null) {
                $server_details = [
                    'id' => 0,
                    'name' => "localhost",
                    'description' => "This is the server where Coolify is running on. Don't delete this!",
                    'user' => 'root',
                    'ip' => "host.docker.internal",
                    'team_id' => 0,
                    'private_key_id' => 0
                ];
                $server_details['proxy'] = ServerMetadata::from([
                    'type' => ProxyTypes::TRAEFIK_V2->value,
                    'status' => ProxyStatus::EXITED->value
                ]);
                $server = Server::create($server_details);
                $server->settings->is_reachable = true;
                $server->settings->is_usable = true;
                $server->settings->save();
            } else {
                $server = Server::find(0);
                $server->settings->is_reachable = true;
                $server->settings->is_usable = true;
                $server->settings->save();
            }
            if (StandaloneDocker::find(0) == null) {
                StandaloneDocker::create([
                    'id' => 0,
                    'name' => 'localhost-coolify',
                    'network' => 'coolify',
                    'server_id' => 0,
                ]);
            }
        }

        try {
            $settings = InstanceSettings::get();
            if (is_null($settings->public_ipv4)) {
                $ipv4 = Process::run('curl -4s https://ifconfig.io')->output();
                if ($ipv4) {
                    $ipv4 = trim($ipv4);
                    $ipv4 = filter_var($ipv4, FILTER_VALIDATE_IP);
                    $settings->update(['public_ipv4' => $ipv4]);
                }
            }
            if (is_null($settings->public_ipv6)) {
                $ipv6 = Process::run('curl -6s https://ifconfig.io')->output();
                if ($ipv6) {
                    $ipv6 = trim($ipv6);
                    $ipv6 = filter_var($ipv6, FILTER_VALIDATE_IP);
                    $settings->update(['public_ipv6' => $ipv6]);
                }
            }
        } catch (\Throwable $e) {
            echo "Error: {$e->getMessage()}\n";
        }
    }
}
