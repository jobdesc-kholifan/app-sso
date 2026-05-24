<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">SSO Active Sessions</h2>
        <p class="text-slate-500 text-sm mt-1">Monitor all users who have logged in via Single Sign-On.</p>
    </div>
    <button class="btn btn-secondary" id="btn-refresh">
        <i class="bx bx-refresh mr-1"></i> Refresh
    </button>
</div>

<!-- Stats Summary -->
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="card p-4 flex items-center gap-4">
        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
            <i class="bx bx-user-check text-emerald-600 text-xl"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500">Active Sessions</p>
            <p class="text-xl font-bold text-slate-800 dark:text-white" id="count-active">-</p>
        </div>
    </div>
    <div class="card p-4 flex items-center gap-4">
        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
            <i class="bx bx-time text-amber-600 text-xl"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500">Expired Sessions</p>
            <p class="text-xl font-bold text-slate-800 dark:text-white" id="count-expired">-</p>
        </div>
    </div>
    <div class="card p-4 flex items-center gap-4">
        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
            <i class="bx bx-block text-rose-600 text-xl"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500">Revoked Sessions</p>
            <p class="text-xl font-bold text-slate-800 dark:text-white" id="count-revoked">-</p>
        </div>
    </div>
</div>

<div class="card p-6">
    <div class="overflow-x-auto">
        <table id="sessions-table" class="w-full whitespace-nowrap stripe hover">
            <thead>
                <tr class="text-left text-xs font-semibold tracking-wide text-slate-500 uppercase border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Username</th>
                    <th class="px-4 py-3">Client App</th>
                    <th class="px-4 py-3">Scopes</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Login At</th>
                    <th class="px-4 py-3">Expires At</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-800">
                <!-- Data filled by Datatables AJAX -->
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script_foot') ?>
<script>
    let sessionsTable;

    document.addEventListener('DOMContentLoaded', function() {
        sessionsTable = $('#sessions-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: baseUrl('/oauth/sessions/datatable'),
                type: 'GET',
                dataSrc: function(json) {
                    // Count badges
                    let active = 0,
                        expired = 0,
                        revoked = 0;
                    json.data.forEach(row => {
                        if (row.status.includes('Active')) active++;
                        else if (row.status.includes('Expired')) expired++;
                        else if (row.status.includes('Revoked')) revoked++;
                    });
                    document.getElementById('count-active').textContent = active;
                    document.getElementById('count-expired').textContent = expired;
                    document.getElementById('count-revoked').textContent = revoked;
                    return json.data;
                }
            },
            columns: [{
                    data: 'full_name'
                },
                {
                    data: 'username'
                },
                {
                    data: 'client_name'
                },
                {
                    data: 'scopes'
                },
                {
                    data: 'status'
                },
                {
                    data: 'created_at'
                },
                {
                    data: 'expires_at'
                },
                {
                    data: 'action',
                    orderable: false,
                    className: 'text-right'
                }
            ],
            order: [
                [5, 'desc']
            ],
            pageLength: 25,
        });

        // Refresh button
        document.getElementById('btn-refresh').addEventListener('click', function() {
            sessionsTable.ajax.reload();
        });

        // Revoke button
        $('#sessions-table').on('click', '.revoke-btn', function() {
            const id = this.dataset.id;
            if (!confirm('Apakah Anda yakin ingin merevoke token ini? Pengguna akan dipaksa login ulang.')) return;

            fetch(baseUrl('/oauth/sessions/revoke/' + id), {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        sessionsTable.ajax.reload(null, false);
                    } else {
                        alert('Gagal: ' + data.message);
                    }
                });
        });
    });
</script>
<?= $this->endSection() ?>