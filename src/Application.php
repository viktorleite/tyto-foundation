<?php

declare(strict_types=1);

namespace Tyto\Foundation;

use ErrorException;

class Application
{
    private static ?self $instance = null;
    protected array $config = [];
    protected bool $booted = false;
    protected bool $debug = false;
    protected ?float $bootStartedAt = null;

    protected function __construct(private readonly string $rootPath)
    {
        if (!defined('APP_ROOT')) {
            define('APP_ROOT', $this->rootPath . DIRECTORY_SEPARATOR);
        }
    }

    public static function init(string $rootPath): static
    {
        if (static::$instance === null) {
            static::$instance = new static($rootPath);
        }

        return static::$instance;
    }

    public function withDebug(bool $enable = true): static
    {
        $this->debug = $enable;
        return $this;
    }

    public function withConfig(array $config): static
    {
        $this->config = $config;
        
        if (isset($config['debug'])) {
            $this->withDebug((bool) $config['debug']);
        }

        $this->boot();
        
        return $this;
    }

    public function withCustomErrorHandler(callable $errorHandler, callable $exceptionHandler): static
    {
        set_error_handler(function (int $severity, string $message, string $file, int $line) use ($errorHandler) {
            if (!(error_reporting() & $severity)) {
                return;
            }
            $errorHandler($severity, $message, $file, $line);
        });

        set_exception_handler($exceptionHandler);
        

        return $this;
    }

    protected function boot(): void
    {
        if ($this->booted) {
            return;
        }

        if ($this->debug) {
            $this->bootStartedAt = microtime(true);
        }

        $this->checkEnvironment();
        $this->configureTimezone();
        $this->configureLocale();
        $this->configureErrorHandling();
        $this->configureLogging();

        $this->booted = true;
    }

     protected function checkEnvironment(): void
    {
        $minVersionId = $this->config['min_php_version_id'] ?? 80200;
        
        if (PHP_VERSION_ID < $minVersionId) {
            exit("Erro de Ambiente: É necessário o PHP 8.2.0 ou superior. Versão atual: " . PHP_VERSION);
        }

        $requiredExtensions = $this->config['extensions'] ?? ['mbstring', 'pdo', 'json'];
        $missing = [];
        $installed = [];

        foreach ($requiredExtensions as $ext) {
            if (extension_loaded($ext)) {
                $installed[] = $ext;
            } else {
                $missing[] = $ext;
            }
        }

        if (!empty($missing)) {
            $output = "=== Relatório de Extensões PHP ===\n";
            $output .= "Instaladas:\n" . implode("\n", array_map(fn($e) => "  ✓ {$e}", $installed)) . "\n";
            $output .= "Ausentes (Necessárias):\n" . implode("\n", array_map(fn($e) => "  ✗ {$e}", $missing)) . "\n";
            
            exit("<pre>" . htmlspecialchars($output) . "</pre>");
        }
    }

    protected function configureTimezone(): void
    {
        date_default_timezone_set($this->config['timezone'] ?? 'UTC');
    }

    protected function configureLocale(): void
    {
        if (isset($this->config['locale'])) {
            setlocale(LC_ALL, $this->config['locale']);
        }
    }

    protected function configureErrorHandling(): void
    {
        if ($this->debug) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(-1);
            ini_set('display_errors', '0');
        }
    }

    protected function configureLogging(): void
    {
        ini_set('log_errors', '1');
        $storageLogs = ($this->config['paths']['storage'] ?? (APP_ROOT . 'storage')) . '/logs';
        
        if (!is_dir($storageLogs) && !@mkdir($storageLogs, 0755, true)) {
            error_log("Aviso do Kernel: Não foi possível criar o diretório de logs em: {$storageLogs}");
        }

        ini_set('error_log', $storageLogs . DIRECTORY_SEPARATOR . 'php.log');
    }

    public function run(?callable $callback = null): mixed
    {
        if ($callback !== null) {
            return $callback($this);
        }
        return true;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function getConfig(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->config;
        }
        return $this->config[$key] ?? $default;
    }
}