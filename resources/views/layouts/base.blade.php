<!DOCTYPE html>
<html data-theme="coollabs" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://api.fonts.coollabs.io" crossorigin>
    <link href="https://api.fonts.coollabs.io/css2?family=Inter&display=swap" rel="stylesheet">
    <title>Coolify</title>
    @env('local')
    <link rel="icon" href="{{ asset('favicon-dev.png') }}" type="image/x-icon" />
@else
    <link rel="icon" href="{{ asset('coolify-transparent.png') }}" type="image/x-icon" />
    @endenv
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @livewireStyles
    @if (config('app.name') == 'Coolify Cloud')
        <script defer data-domain="app.coolify.io" src="https://analytics.coollabs.io/js/plausible.js"></script>
    @endif
</head>
@section('body')

    <body>
        @livewireScripts
        <dialog id="help" class="modal">
            <livewire:help />
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
        <x-toaster-hub />
        <x-version class="fixed left-2 bottom-1" />
        <script>
            let checkHealthInterval = null;
            let checkIfIamDeadInterval = null;

            function changePasswordFieldType(event) {
                let element = event.target
                for (let i = 0; i < 10; i++) {
                    if (element.className === "relative") {
                        break;
                    }
                    element = element.parentElement;
                }
                element = element.children[1];
                if (element.nodeName === 'INPUT') {
                    if (element.type === 'password') {
                        element.type = 'text';
                    } else {
                        element.type = 'password';
                    }
                }
            }

            function revive() {
                if (checkHealthInterval) return true;
                console.log('Checking server\'s health...')
                checkHealthInterval = setInterval(() => {
                    fetch('/api/health')
                        .then(response => {
                            if (response.ok) {
                                Toaster.success('Coolify is back online. Reloading...')
                                if (checkHealthInterval) clearInterval(checkHealthInterval);
                                setTimeout(() => {
                                    window.location.reload();
                                }, 5000)
                            } else {
                                console.log('Waiting for server to come back from dead...');
                            }
                        })
                }, 2000);
            }

            function upgrade() {
                if (checkIfIamDeadInterval) return true;
                console.log('Update initiated.')
                checkIfIamDeadInterval = setInterval(() => {
                    fetch('/api/health')
                        .then(response => {
                            if (response.ok) {
                                console.log('It\'s alive. Waiting for server to be dead...');
                            } else {
                                Toaster.success('Update done, restarting Coolify!')
                                console.log('It\'s dead. Reviving... Standby... Bzz... Bzz...')
                                if (checkIfIamDeadInterval) clearInterval(checkIfIamDeadInterval);
                                revive();
                            }
                        })
                }, 2000);
            }
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text);
                Livewire.emit('success', 'Copied to clipboard.');
            }

            Livewire.on('reloadWindow', (timeout) => {
                if (timeout) {
                    setTimeout(() => {
                        window.location.reload();
                    }, timeout);
                    return;
                } else {
                    window.location.reload();
                }
            })
            Livewire.on('info', (message) => {
                if (message) Toaster.info(message)
            })
            Livewire.on('error', (message) => {
                if (message) Toaster.error(message)
            })
            Livewire.on('warning', (message) => {
                if (message) Toaster.warning(message)
            })
            Livewire.on('success', (message) => {
                if (message) Toaster.success(message)
            })
        </script>
    </body>
@show

</html>
