<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Master Users</h2>
        <p class="text-slate-500 text-sm mt-1">Manage system administrators and enterprise users.</p>
    </div>
    <button class="btn btn-primary" onclick="openUserModal()">
        <i class="bx bx-plus mr-1"></i> Add User
    </button>
</div>

<div class="card p-6">
    <div class="overflow-x-auto">
        <!-- Datatables Tailwind Theme Requires specific classes -->
        <table id="users-table" class="w-full whitespace-nowrap stripe hover">
            <thead>
                <tr class="text-left text-xs font-semibold tracking-wide text-slate-500 uppercase border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Full Name</th>
                    <th class="px-4 py-3">Username</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Last Login</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-800">
                <!-- Data filled by Datatables AJAX -->
            </tbody>
        </table>
    </div>
</div>

<?= $this->include('master/users/v_user_form') ?>

<?= $this->endSection() ?>

<?= $this->section('script_foot') ?>

<?= vite_asset('app/Views/master/users/scripts/user_index.js', false) ?>
<?= $this->endSection() ?>