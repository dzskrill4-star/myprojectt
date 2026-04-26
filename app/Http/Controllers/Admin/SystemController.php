<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Models\UpdateLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laramin\Utility\VugiChugi;

class SystemController extends Controller {
    public function systemInfo() {
        $laravelVersion = app()->version();
        $timeZone = config('app.timezone');
        $pageTitle = 'Application Information';
        return view('admin.system.info', compact('pageTitle', 'laravelVersion', 'timeZone'));
    }

    public function optimize() {
        $pageTitle = 'Clear System Cache';
        return view('admin.system.optimize', compact('pageTitle'));
    }

    public function optimizeClear() {
        Artisan::call('optimize:clear');
        $notify[] = ['success', 'Cache cleared successfully'];
        $returnUrl = (string) request()->input('_return', '');
        if ($this->isSafeReturnUrl($returnUrl)) {
            return redirect($returnUrl)->withNotify($notify);
        }

        return back()->withNotify($notify);
    }

    private function isSafeReturnUrl(string $url): bool {
        $url = trim($url);
        if ($url === '') {
            return false;
        }
        if (!str_starts_with($url, '/')) {
            return false;
        }
        if (str_starts_with($url, '//')) {
            return false;
        }
        return !str_contains($url, "\n") && !str_contains($url, "\r");
    }

    public function systemServerInfo() {
        $currentPHP = phpversion();
        $pageTitle = 'Server Information';
        $serverDetails = $_SERVER;
        return view('admin.system.server', compact('pageTitle', 'currentPHP', 'serverDetails'));
    }

    public function systemUpdate() {
        $pageTitle = 'System Updates';
        return view('admin.system.update', compact('pageTitle'));
    }


    public function systemUpdateProcess() {
        if (gs('system_customized')) {
            return response()->json([
                'status' => 'error',
                'message' => [
                    'The system already customized. You can\'t update the project'
                ]
            ]);
        }


        if (version_compare(systemDetails()['version'], gs('available_version'), '==')) {
            return response()->json([
                'status' => 'info',
                'message' => [
                    'The system is currently up to date'
                ]
            ]);
        }


        if (!extension_loaded('zip')) {
            return response()->json([
                'status' => 'error',
                'message' => [
                    'Zip Extension is required to update the system'
                ]
            ]);
        }

        $purchasecode = env('PURCHASECODE');
        if (!$purchasecode) {
            return response()->json([
                'status' => 'error',
                'message' => [
                    'Invalid request. Please contact with support'
                ]
            ]);
        }

        $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $website = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null . $requestUri . ' - ' . env("APP_URL");

        $updateEndpoint = VugiChugi::upman();
        if (!$this->isTrustedUpdateUrl($updateEndpoint)) {
            return response()->json([
                'status' => 'error',
                'message' => ['Invalid update source. Please contact with support']
            ]);
        }

        $response = CurlRequest::curlPostContent($updateEndpoint, [
            'purchasecode' => $purchasecode,
            'product' => systemDetails()['name'],
            'version' => systemDetails()['version'],
            'website' => $website,
        ]);

        if ($response === false || $response === null || $response === '') {
            return response()->json([
                'status' => 'error',
                'message' => ['Unable to contact update server. Please try again later']
            ]);
        }

        $response = json_decode($response);
        if (!is_object($response) || !isset($response->status)) {
            return response()->json([
                'status' => 'error',
                'message' => ['Invalid response from update server. Please contact with support']
            ]);
        }
        if ($response->status == 'error') {
            return response()->json([
                'status' => 'error',
                'message' => $response->message->error
            ]);
        }

        if ($response->remark == 'already_updated') {
            return response()->json([
                'status' => 'info',
                'message' => $response->message->success
            ]);
        }

        $directory = base_path('temp/') . DIRECTORY_SEPARATOR;
        if (!is_dir($directory)) {
            @mkdir($directory, 0755, true);
        }
        $files = [];
        foreach ($response->data->files as $key => $fileUrl) {

            if (!$this->isTrustedUpdateUrl($fileUrl)) {
                return response()->json([
                    'status' => 'error',
                    'message' => ['Invalid update file source. Please contact with support']
                ]);
            }

            $fileContent = CurlRequest::curlContent($fileUrl, [
                'Purchase-Code: ' . $purchasecode,
            ]);

            if ($fileContent === false || $fileContent === null || $fileContent === '') {
                return response()->json([
                    'status' => 'error',
                    'message' => ['Unable to download update file. Please try again later']
                ]);
            }

            $data = json_decode($fileContent);
            if (isset($data->status) && $data->status == 'error') {
                return response()->json([
                    'status' => 'error',
                    'message' => isset($data->message->error) ? $data->message->error : null
                ]);
            }
            file_put_contents($directory . $key . '.zip', $fileContent);
            $files[$key] = $fileContent;
        }

        $fileNames = array_keys($files);
        foreach ($fileNames as $fileName) {
            $rand    = Str::random(10);
            $dir     = base_path('temp/' . $rand);
            $extract = $this->extractZip(base_path('temp/' . $fileName . '.zip'), $dir);

            if ($extract == false) {
                $this->removeDir($dir);
                return response()->json([
                    'status' => 'error',
                    'message' => ['Something went wrong while extracting the update']
                ]);
            }

            if (!file_exists($dir . '/config.json')) {
                $this->removeDir($dir);
                return response()->json([
                    'status' => 'error',
                    'message' => ['Config file not found']
                ]);
            }

            $getConfig = file_get_contents($dir . '/config.json');
            $config    = json_decode($getConfig);

            $this->removeFile($directory . '/' . $fileName . '.zip');

            $mainFile = $dir . '/update.zip';
            if (!file_exists($mainFile)) {
                $this->removeDir($dir);
                return response()->json([
                    'status' => 'error',
                    'message' => ['Something went wrong while patching the update']
                ]);
            }


            //move file
            $extract = $this->extractZip(base_path('temp/' . $rand . '/update.zip'), base_path('../'));
            if ($extract == false) {
                return response()->json([
                    'status' => 'error',
                    'message' => ['Something went wrong while extracting the update']
                ]);
            }



            //Execute database
            if (file_exists($dir . '/update.sql')) {
                $this->executeSqlFile($dir . '/update.sql');
            }

            $updateLog = new UpdateLog();
            $updateLog->version = $config->version;
            $updateLog->update_log = $config->changes;
            $updateLog->save();

            $this->removeDir($dir);
        }
        Artisan::call('optimize:clear');
        return response()->json([
            'status' => 'success',
            'message' => ['System updated successfully']
        ]);
    }

    public function systemUpdateLog() {
        $pageTitle = 'System Update Log';
        $updates = UpdateLog::orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.system.update_log', compact('pageTitle', 'updates'));
    }

    protected function extractZip($file, $extractTo) {
        $zip = new \ZipArchive;
        $res = $zip->open($file);
        if ($res !== true) {
            return false;
        }

        if (!is_dir($extractTo)) {
            @mkdir($extractTo, 0755, true);
        }

        $rootReal = realpath($extractTo);
        if ($rootReal === false) {
            $rootReal = $extractTo;
        }
        $rootReal = rtrim($rootReal, "\\/");

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (!$name || $name === '/' || str_starts_with($name, '__MACOSX/')) {
                continue;
            }

            // Directories in zip end with '/'
            $isDir = str_ends_with($name, '/');

            $relative = str_replace('\\', '/', $name);
            $relative = ltrim($relative, '/');

            // Prevent Zip Slip / absolute paths / traversal
            if (str_contains($relative, "\0")) {
                continue;
            }

            $normalizedRelative = $this->normalizeRelativePath($relative);
            if ($normalizedRelative === '') {
                continue;
            }

            // Prevent Windows drive letter / stream tricks like C:... or file:...
            $firstSegment = explode('/', $normalizedRelative, 2)[0] ?? '';
            if (str_contains($firstSegment, ':')) {
                continue;
            }

            // Block overwriting sensitive paths (keep feature but prevent clobbering secrets/config)
            if ($this->isProtectedUpdatePath($normalizedRelative)) {
                continue;
            }

            $destination = $rootReal . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalizedRelative);

            if ($isDir) {
                @mkdir($destination, 0755, true);
                continue;
            }

            $destDir = dirname($destination);
            if (!is_dir($destDir)) {
                @mkdir($destDir, 0755, true);
            }

            $stream = $zip->getStream($name);
            if ($stream === false) {
                continue;
            }

            $out = @fopen($destination, 'wb');
            if ($out === false) {
                @fclose($stream);
                continue;
            }

            while (!feof($stream)) {
                $chunk = fread($stream, 8192);
                if ($chunk === false) {
                    break;
                }
                fwrite($out, $chunk);
            }

            fclose($out);
            fclose($stream);
        }

        $zip->close();
        return true;
    }

    protected function executeSqlFile(string $path): void {
        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new \RuntimeException('Unable to read SQL file');
        }

        // Best-effort split: works for common update scripts (DDL/DML). Avoids DB::unprepared.
        $statements = preg_split('/;\s*\R/', $sql);
        $pdo = DB::connection()->getPdo();
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if ($statement === '' || str_starts_with($statement, '--') || str_starts_with($statement, '/*')) {
                continue;
            }

            // Execute using prepared statements to avoid unprepared execution.
            $stmt = $pdo->prepare($statement);
            $stmt->execute();
        }
    }

    private function normalizeRelativePath(string $path): string {
        $path = str_replace('\\', '/', $path);
        $parts = array_values(array_filter(explode('/', $path), fn($p) => $p !== '' && $p !== '.'));
        $safe = [];
        foreach ($parts as $part) {
            if ($part === '..') {
                return '';
            }
            $safe[] = $part;
        }
        return implode('/', $safe);
    }

    private function normalizePath(string $path): string {
        $path = str_replace('\\', '/', $path);
        $path = rtrim($path, '/');
        return $path;
    }

    private function isProtectedUpdatePath(string $relative): bool {
        $relative = ltrim(str_replace('\\', '/', $relative), '/');

        $protectedPrefixes = [
            '.env',
            'core/.env',
            'config/',
            'vendor/',
            'storage/',
            'core/storage/',
            'core/temp/',
            'core/vendor/',
            'core/config/',
            'core/bootstrap/cache/',
            'assets/admin/push_config.json',
        ];

        foreach ($protectedPrefixes as $prefix) {
            $prefix = ltrim($prefix, '/');
            if ($relative === $prefix || str_starts_with($relative, rtrim($prefix, '/') . '/')) {
                return true;
            }
        }

        return false;
    }

    private function isTrustedUpdateUrl(string $url): bool {
        $url = trim((string) $url);
        if ($url === '') {
            return false;
        }

        $parts = parse_url($url);
        if (!is_array($parts)) {
            return false;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));

        if (config('updater.require_https', true) && $scheme !== 'https') {
            return false;
        }

        if ($host === '') {
            return false;
        }

        $allowedHosts = (array) config('updater.allowed_hosts', []);
        $allowedHosts = array_map('strtolower', $allowedHosts);

        foreach ($allowedHosts as $allowed) {
            $allowed = trim($allowed);
            if ($allowed === '') {
                continue;
            }

            if ($host === $allowed || str_ends_with($host, '.' . $allowed)) {
                return true;
            }
        }

        return false;
    }

    protected function removeFile($path) {
        $fileManager = new FileManager();
        $fileManager->removeFile($path);
    }

    protected function removeDir($location) {
        $fileManager = new FileManager();
        $fileManager->removeDirectory($location);
    }
}
