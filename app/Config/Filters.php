<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;
use CodeIgniter\Shield\Filters\ForcePasswordResetFilter;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     */
    public array $aliases = [
        'auth'          => \App\Filters\AuthFilter::class,
        'forcehttps'    => \CodeIgniter\Filters\ForceHTTPS::class,
        'pagecache'     => \CodeIgniter\Filters\PageCache::class,
        'performance'   => \CodeIgniter\Filters\PerformanceMetrics::class,
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'forcePasswordReset' => ForcePasswordResetFilter::class,
        'logFilter'     => \App\Filters\LogFilter::class,
    ];

    public array $required = [
        'before' => [
            'forcehttps', // Force Global Secure Requests
            'pagecache',  // Web Page Caching
        ],
        'after' => [
            'pagecache',   // Web Page Caching
            'performance', // Performance Metrics
            'toolbar',     // Debug Toolbar
        ],
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     */
    public array $globals = [
        'before' => [
            'auth' => ['except' => [
                'login', 
                'login/*', 
                'register', 
                'register/*', 
                'password-reset', 
                'password-reset/*',
                '*api/*',
                '*slack*',
                '*jira*',
                '*interlock*',
                '*resta*'
            ]], // 로그인 및 회원가입 관련 URI 예외 처리
            // 'honeypot',
            // 'csrf',
            // 'invalidchars',
            'session' => ['except' => ['login*', 'register', 'auth/a/*', 'slack/*', 'jira/*', 'interlock', 'resta/*']],
            'forcePasswordReset' => ['except' => ['login*', 'set-password*', 'logout']],
            'logFilter'
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'post' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don’t expect could bypass the filter.
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     */
    public array $filters = [
        'auth-rates' => [
            'before' => [
                'login*', 'register', 'auth/*'
            ]
        ]
    ];
}
