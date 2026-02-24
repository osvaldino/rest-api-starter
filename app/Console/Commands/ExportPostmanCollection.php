<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;
use stdClass;

class ExportPostmanCollection extends Command
{
    private const array AUTH_ROUTES = ['register', 'login', 'logout', 'me'];

    protected $signature = 'export:postman
                            {--output=postman_collection.json : Output file path}
                            {--base-url=http://localhost : Base URL for the collection}';

    protected $description = 'Export all API routes as a Postman collection JSON file';

    /**
     * Per-route configuration: description and mock body.
     * Key = route name (e.g. "projects.store") or last URI segment for auth routes.
     */
    private array $routeConfig = [
        // Auth
        'register' => [
            'description' => "Register a new user account.\n\nReturns the created user data and an API access token.",
            'body' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ],
        ],
        'login' => [
            'description' => "Authenticate a user with email and password.\n\nReturns a Sanctum API token. Use this token as a Bearer token in subsequent requests.",
            'body' => [
                'email' => 'john@example.com',
                'password' => 'password123',
            ],
        ],
        'logout' => [
            'description' => "Revoke the current user's API token.\n\nRequires authentication. After logout, the token will no longer be valid.",
        ],
        'me' => [
            'description' => "Retrieve the authenticated user's profile data.\n\nRequires authentication.",
        ],

        // Projects
        'projects.index' => [
            'description' => "List all projects with optional filtering and pagination.\n\nQuery parameters:\n- `search` (string): Filter by name\n- `status` (string): Filter by status — `planning`, `in_progress`, `completed`, `on_hold`, `cancelled`\n- `sort` (string): Column to sort by\n- `per_page` (integer): Results per page (default: 15)",
        ],
        'projects.show' => [
            'description' => "Retrieve a single project by ID, including its related tasks.\n\nRequires authentication.",
        ],
        'projects.store' => [
            'description' => "Create a new project.\n\nRequires authentication.\n\nAllowed status values: `planning`, `in_progress`, `completed`, `on_hold`, `cancelled`.",
            'body' => [
                'name' => 'Website Redesign',
                'description' => 'Complete redesign of the company website with a new UI.',
                'status' => 'planning',
            ],
        ],
        'projects.update' => [
            'description' => "Update an existing project by ID.\n\nRequires authentication.\n\nAllowed status values: `planning`, `in_progress`, `completed`, `on_hold`, `cancelled`.",
            'body' => [
                'name' => 'Website Redesign',
                'description' => 'Complete redesign of the company website with new UI and dark mode.',
                'status' => 'in_progress',
            ],
        ],
        'projects.destroy' => [
            'description' => "Soft-delete a project by ID.\n\nRequires authentication. The project and its associated tasks are soft-deleted and can be restored.",
        ],

        // Tasks
        'tasks.index' => [
            'description' => "List all tasks with optional filtering and pagination.\n\nQuery parameters:\n- `search` (string): Filter by title\n- `project_id` (integer): Filter by project\n- `category_id` (integer): Filter by category\n- `done` (boolean): Filter by completion status\n- `sort` (string): Column to sort by\n- `per_page` (integer): Results per page (default: 15)",
        ],
        'tasks.show' => [
            'description' => "Retrieve a single task by ID, including its related project and category.\n\nRequires authentication.",
        ],
        'tasks.store' => [
            'description' => "Create a new task linked to a project and optionally a category.\n\nRequires authentication.",
            'body' => [
                'project_id' => 1,
                'category_id' => 1,
                'title' => 'Design homepage layout',
                'done' => false,
            ],
        ],
        'tasks.update' => [
            'description' => "Update an existing task by ID.\n\nRequires authentication.",
            'body' => [
                'project_id' => 1,
                'category_id' => 1,
                'title' => 'Design homepage layout',
                'done' => true,
            ],
        ],
        'tasks.destroy' => [
            'description' => "Soft-delete a task by ID.\n\nRequires authentication.",
        ],

        // Categories
        'categories.index' => [
            'description' => "List all categories with optional filtering and pagination.\n\nQuery parameters:\n- `search` (string): Filter by name\n- `sort` (string): Column to sort by\n- `per_page` (integer): Results per page (default: 15)",
        ],
        'categories.show' => [
            'description' => "Retrieve a single category by ID.\n\nRequires authentication.",
        ],
        'categories.store' => [
            'description' => "Create a new category.\n\nRequires authentication. The name must be unique.",
            'body' => [
                'name' => 'Frontend Development',
            ],
        ],
        'categories.update' => [
            'description' => "Update an existing category by ID.\n\nRequires authentication. The name must be unique (excluding the current record).",
            'body' => [
                'name' => 'Backend Development',
            ],
        ],
        'categories.destroy' => [
            'description' => "Soft-delete a category by ID.\n\nRequires authentication.",
        ],
    ];

    public function handle(): int
    {
        $baseUrl = $this->option('base-url');
        $output = $this->option('output');

        $routes = collect(RouteFacade::getRoutes()->getRoutes())
            ->filter(fn (Route $route) => $this->isApiRoute($route))
            ->values();

        $folders = [];

        foreach ($routes as $route) {
            $folder = $this->resolveFolder($route);
            $methods = $this->resolveMethods($route);

            foreach ($methods as $method) {
                $folders[$folder][] = $this->buildItem($route, $method, $baseUrl);
            }
        }

        $collection = [
            'info' => [
                'name' => config('app.name', 'Laravel').' API',
                '_postman_id' => Str::uuid()->toString(),
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ],
            'item' => collect($folders)
                ->map(fn ($items, $name) => [
                    'name' => $name,
                    'item' => $items,
                ])
                ->values()
                ->all(),
        ];

        $json = json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        file_put_contents($output, $json);

        $this->info("Postman collection exported to: $output");
        $this->line('  Folders: '.count($folders));
        $this->line('  Requests: '.collect($folders)->flatten(1)->count());

        return self::SUCCESS;
    }

    private function isApiRoute(Route $route): bool
    {
        return Str::startsWith($route->uri(), 'api/');
    }

    private function resolveFolder(Route $route): string
    {
        $segments = collect(explode('/', trim($route->uri(), '/')))
            ->reject(fn ($s) => preg_match('/^(api|v\d+)$/', $s) || Str::startsWith($s, '{'))
            ->values();

        $first = $segments->first() ?? 'general';

        if (in_array($first, self::AUTH_ROUTES)) {
            return 'Auth';
        }

        return Str::ucfirst($first);
    }

    private function resolveMethods(Route $route): array
    {
        return array_values(
            array_filter(
                $route->methods(),
                fn ($m) => $m !== 'HEAD'
            )
        );
    }

    private function buildItem(Route $route, string $method, string $baseUrl): array
    {
        $uri = $route->uri();
        $config = $this->resolveConfig($route);
        $name = $this->generateName($method, $uri);
        $rawUrl = rtrim($baseUrl, '/').'/'.ltrim($uri, '/');

        $pathSegments = collect(explode('/', trim($uri, '/')))
            ->map(fn ($s) => Str::startsWith($s, '{') ? ':'.trim($s, '{}') : $s)
            ->all();

        $item = [
            'name' => $name,
            'request' => [
                'method' => strtoupper($method),
                'description' => $config['description'] ?? '',
                'header' => [
                    ['key' => 'Accept', 'value' => 'application/json'],
                    ['key' => 'Content-Type', 'value' => 'application/json'],
                ],
                'url' => [
                    'raw' => $rawUrl,
                    'host' => [parse_url($baseUrl, PHP_URL_HOST)],
                    'path' => $pathSegments,
                ],
            ],
        ];

        if ($this->requiresAuth($route)) {
            $item['request']['auth'] = [
                'type' => 'bearer',
                'bearer' => [['key' => 'token', 'value' => '{{token}}', 'type' => 'string']],
            ];
        }

        if (in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'])) {
            $body = $config['body'] ?? new stdClass;
            $item['request']['body'] = [
                'mode' => 'raw',
                'raw' => json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'options' => ['raw' => ['language' => 'json']],
            ];
        }

        return $item;
    }

    private function resolveConfig(Route $route): array
    {
        $name = $route->getName();

        if ($name && isset($this->routeConfig[$name])) {
            return $this->routeConfig[$name];
        }

        // Fallback: match by last meaningful URI segment (e.g. "login", "me")
        $lastSegment = collect(explode('/', trim($route->uri(), '/')))
            ->reject(fn ($s) => preg_match('/^(api|v\d+)$/', $s) || Str::startsWith($s, '{'))
            ->last();

        return $this->routeConfig[$lastSegment] ?? [];
    }

    private function generateName(string $method, string $uri): string
    {
        $segments = collect(explode('/', trim($uri, '/')))
            ->reject(fn ($s) => preg_match('/^(api|v\d+)$/', $s))
            ->map(fn ($s) => Str::startsWith($s, '{') ? 'by '.trim($s, '{}') : $s)
            ->implode(' ');

        return Str::title(strtolower($method).' '.$segments);
    }

    private function requiresAuth(Route $route): bool
    {
        return collect($route->gatherMiddleware())->contains(
            fn ($m) => Str::contains($m, ['auth', 'sanctum'])
        );
    }
}
