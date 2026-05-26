<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Master Clients</h2>
        <p class="text-slate-500 text-sm mt-1">Manage API clients and enterprise integrations.</p>
    </div>
    <button class="btn btn-primary" onclick="openClientModal()">
        <i class="bx bx-plus mr-1"></i> Add Client
    </button>
</div>

<div class="card p-6">
    <div class="overflow-x-auto">
        <table id="clients-table" class="w-full whitespace-nowrap stripe hover">
            <thead>
                <tr class="text-left text-xs font-semibold tracking-wide text-slate-500 uppercase border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Identifier</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Redirect URI</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-800">
                <!-- Data filled by Datatables AJAX -->
            </tbody>
        </table>
    </div>
</div>

<?= $this->include('master/clients/v_client_form') ?>

<?= $this->endSection() ?>

<?= $this->section('script_foot') ?>
<?= vite_asset('scripts/client_index.js', true) ?>
<?= $this->endSection() ?>