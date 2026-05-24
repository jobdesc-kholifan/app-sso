<!-- Modal Form (Hidden by default) -->
<div id="userModal" class="vibe-modal modal-center modal-sm">
    <div class="vibe-modal-dialog">
        <div class="vibe-modal-content">
            <div class="vibe-modal-header">
                <h5 class="text-lg font-bold" id="modalTitle">Add User</h5>
                <button type="button" onclick="closeModal('userModal')" class="text-slate-400 hover:text-rose-500 transition-colors">
                    <i class="bx bx-x text-2xl"></i>
                </button>
            </div>
            
            <form id="userForm">
                <div class="vibe-modal-body space-y-4">
                    <input type="hidden" id="userId" name="id">
                    
                    <div>
                        <label class="form-label block mb-1">Full Name</label>
                        <input type="text" id="full_name" name="full_name" class="form-control w-full" required>
                    </div>
                    
                    <div>
                        <label class="form-label block mb-1">Username</label>
                        <input type="text" id="username" name="username" class="form-control w-full" required>
                    </div>
                    
                    <div>
                        <label class="form-label block mb-1">Password</label>
                        <input type="password" id="user_password" name="user_password" class="form-control w-full" placeholder="Leave blank to keep current password">
                    </div>
                    
                    <div>
                        <label class="form-label block mb-1">Role</label>
                        <select id="role" name="role" class="form-control w-full">
                            <option value="admin">Admin</option>
                            <option value="superadmin">Superadmin</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="form-label block mb-1">Status</label>
                        <select id="status" name="status" class="form-control w-full">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="vibe-modal-footer">
                    <button type="button" class="btn btn-default" onclick="closeModal('userModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>
