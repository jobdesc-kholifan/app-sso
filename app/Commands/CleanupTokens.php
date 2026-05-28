<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CleanupTokens extends BaseCommand
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
    protected $name = 'sso:cleanup-tokens';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Clean up expired and revoked OAuth access tokens, refresh tokens, and authorization codes from the database.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'sso:cleanup-tokens [options]';

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
    protected $options = [];

    /**
     * Actually run the command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        CLI::write('----------------------------------------------------', 'cyan');
        CLI::write('   SSO Gateway: Starting database token cleanup...   ', 'yellow');
        CLI::write('----------------------------------------------------', 'cyan');

        try {
            // 1. Clean up access tokens
            $db->table('oauth.access_tokens')
                ->groupStart()
                    ->where('expires_at <', $now)
                    ->orWhere('revoked', 1)
                ->groupEnd()
                ->delete();
            $deletedAccess = $db->affectedRows();
            CLI::write("✓ Cleared {$deletedAccess} expired/revoked access tokens.", 'green');

            // 2. Clean up refresh tokens
            $db->table('oauth.refresh_tokens')
                ->groupStart()
                    ->where('expires_at <', $now)
                    ->orWhere('revoked', 1)
                ->groupEnd()
                ->delete();
            $deletedRefresh = $db->affectedRows();
            CLI::write("✓ Cleared {$deletedRefresh} expired/revoked refresh tokens.", 'green');

            // 3. Clean up authorization codes
            $db->table('oauth.auth_codes')
                ->groupStart()
                    ->where('expires_at <', $now)
                    ->orWhere('revoked', 1)
                ->groupEnd()
                ->delete();
            $deletedCodes = $db->affectedRows();
            CLI::write("✓ Cleared {$deletedCodes} expired/revoked authorization codes.", 'green');

            CLI::write('----------------------------------------------------', 'cyan');
            CLI::write('✓ Success: Database token maintenance completed!', 'green');
            CLI::write('----------------------------------------------------', 'cyan');

        } catch (\Exception $e) {
            CLI::error('Error during cleanup: ' . $e->getMessage());
            log_message('critical', '[SSO CLI Token Cleanup Error]: ' . $e->getMessage() . ' Trace ' . $e->getTraceAsString());
        }
    }
}
