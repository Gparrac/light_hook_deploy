<?php
namespace PipeLhd\Services\ScriptExecutor;

class ScriptWithVariables implements ScriptExecutorInterface
{
    private $scriptsDir = ROOT_PATH . '/scripts/';

    public function executeScript(string $script, array $deployVariables = [], string $projectDir = ROOT_PATH): array
    {
        $scriptPath = $this->scriptsDir . $script;

        // Read and replace variables in the script
        $scriptContent = file_get_contents($scriptPath);
        if ($scriptContent === false) {
            return [
                'status' => 'error',
                'message' => "Failed to read the script file at $scriptPath"
            ];
        }
        
        foreach ($deployVariables as $key => $value) {
            $scriptContent = str_replace('{{' . $key . '}}', $value, $scriptContent);
        }

        // Save to a temporary file
        $tempScriptPath = $this->scriptsDir . 'pipe_lhd_temp_script.sh';
        file_put_contents($tempScriptPath, $scriptContent);
        chmod($tempScriptPath, 0755);
        
        if (!file_exists($tempScriptPath)) {
            return [
                'status' => 'error',
                'message' => "Temporary script file not created at $tempScriptPath"
            ];
        }

        // Build and execute command
        $command = "cd $projectDir && sudo ".$this->scriptsDir."pipe_lhd_temp_script.sh";

        $output = shell_exec($command . ' 2>&1');
        
        // Remove the temporary file
        unlink($tempScriptPath);

        if ($output === null || trim($output) === '' || strpos($output, "error:") !== false) {
            return [
                'status' => 'error',
                'message' => "Script execution failed: $output"
            ];
        }

        return [
            'status' => 'success',
            'message' => "Executed: $output"
        ];
    }
}