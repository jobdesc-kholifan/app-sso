<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class GenerateKeys extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'SSO';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'sso:generate-keys';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Generate secure OAuth 2.0 private and public keys using native PHP OpenSSL.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'sso:generate-keys [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '-f' => 'Force overwrite existing keys without prompt.',
    ];

    /**
     * Actually run the command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $keysDir = WRITEPATH . 'keys/';
        $privateKeyPath = $keysDir . 'oauth-private.key';
        $publicKeyPath = $keysDir . 'oauth-public.key';

        CLI::write('----------------------------------------------------', 'cyan');
        CLI::write('   SSO Gateway: Generating OAuth Cryptographic Keys  ', 'yellow');
        CLI::write('----------------------------------------------------', 'cyan');

        // Check if openssl extension is loaded
        if (!extension_loaded('openssl')) {
            CLI::error('Error: PHP OpenSSL extension is not enabled.');
            return;
        }

        // Ensure directories exist
        if (!is_dir($keysDir)) {
            if (!mkdir($keysDir, 0755, true) && !is_dir($keysDir)) {
                CLI::error("Error: Failed to create directory: {$keysDir}");
                return;
            }
        }

        // Force option
        $force = array_key_exists('f', $params) || CLI::getOption('f');

        // Overwrite protection
        if (!$force && (is_file($privateKeyPath) || is_file($publicKeyPath))) {
            $overwrite = CLI::prompt('OAuth keys already exist in writable/keys/. Overwrite?', ['y', 'n'], 'n');
            if (strtolower($overwrite) !== 'y') {
                CLI::write('Generation cancelled.', 'yellow');
                return;
            }
        }

        try {
            CLI::write('Generating 2048-bit RSA keys...', 'white');

            $config = [
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ];

            // Create private key resource
            $res = openssl_pkey_new($config);
            if ($res === false) {
                throw new \Exception('Failed to create private key: ' . openssl_error_string());
            }

            // Export private key
            $privateKey = null;
            if (!openssl_pkey_export($res, $privateKey)) {
                throw new \Exception('Failed to export private key: ' . openssl_error_string());
            }

            // Extract public key details
            $pubKeyDetails = openssl_pkey_get_details($res);
            if ($pubKeyDetails === false || !isset($pubKeyDetails['key'])) {
                throw new \Exception('Failed to extract public key: ' . openssl_error_string());
            }
            $publicKey = $pubKeyDetails['key'];

            // Write to files
            if (file_put_contents($privateKeyPath, $privateKey) === false) {
                throw new \Exception("Failed to write to {$privateKeyPath}");
            }
            if (file_put_contents($publicKeyPath, $publicKey) === false) {
                throw new \Exception("Failed to write to {$publicKeyPath}");
            }

            // Set secure permissions (chmod 600) on Unix-like operating systems
            if (DIRECTORY_SEPARATOR !== '\\') {
                @chmod($privateKeyPath, 0600);
                @chmod($publicKeyPath, 0600);
            }

            CLI::write('✓ Generated: ' . basename($privateKeyPath), 'green');
            CLI::write('✓ Generated: ' . basename($publicKeyPath), 'green');
            CLI::write('----------------------------------------------------', 'cyan');
            CLI::write('✓ Success: OAuth private and public keys generated!', 'green');
            CLI::write('----------------------------------------------------', 'cyan');

        } catch (\Exception $e) {
            CLI::error('Error generating keys: ' . $e->getMessage());
        }
    }
}
