<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>laravel-dock status</title>
    </head>
    <body>
        <code>
            <table>
                <tbody>
                    <tr>
                        <td>Time</td>
                        <td>{{ Carbon\carbon::now()->format('c') }}</td>
                    </tr>
                    <tr>
                        <td>Client</td>
                        <td>{{ request()->ip() }}</td>
                    </tr>
                    <tr>
                        <td>Server</td>
                        <td>{{ request()->server('SERVER_ADDR') }}:{{ request()->server('SERVER_PORT') }} ({{ request()->server('SERVER_NAME') }})</td>
                    </tr>
                    <tr>
                        <td>Database Driver</td>
                        <td>{{ config('database.connections.'.config('database.default').'.driver') }}</td>
                    </tr>
                    <tr>
                        <td>Database Server</td>
                        <td>{{ config('database.connections.'.config('database.default').'.host') }}:{{ config('database.connections.'.config('database.default').'.port') }}</td>
                    </tr>
                    <tr>
                        <td>Database Migrations</td>
                        <td>
                            <ol>
                                @foreach($migrations as $migration)
                                    <li>{{ $migration }}</li>
                                @endforeach
                            </ol>
                        </td>
                    </tr>
                    <tr>
                        <td>Cache Driver</td>
                        <td>{{ config('cache.stores.'.config('cache.default').'.driver') }}</td>
                    </tr>
                    <tr>
                        <td>Queue Driver</td>
                        <td>{{ config('queue.connections.'.config('queue.default').'.driver') }}</td>
                    </tr>
                    <tr>
                        <td>Cache Test</td>
                        <td>{{ Cache::remember('cache-test', 1, function () { return Carbon\carbon::now()->format('H:i:s'); }) }} (TTL: 1 minute)</td>
                    </tr>
                    <tr>
                        <td>Scheduler Test</td>
                        <td>{{ Cache::get('increment-requests', 0) }} jobs dispatched</td>
                    </tr>
                    <tr>
                        <td>Queue Test</td>
                        <td>{{ Cache::get('counter', 0) }} jobs handled</td>
                    </tr>
                </tbody>
            </table>
        </code>
    </body>
</html>
