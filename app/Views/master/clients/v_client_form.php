<!-- Modal Form (Hidden by default) -->
<div id="clientModal" class="vibe-modal modal-center modal-md">
    <div class="vibe-modal-dialog">
        <div class="vibe-modal-content">
            <div class="vibe-modal-header">
                <h5 class="text-lg font-bold" id="modalTitle">Add Client</h5>
                <button type="button" onclick="closeModal('clientModal')" class="text-slate-400 hover:text-rose-500 transition-colors">
                    <i class="bx bx-x text-2xl"></i>
                </button>
            </div>
            
            <form id="clientForm">
                <div class="vibe-modal-body space-y-4">
                    <input type="hidden" id="clientId" name="id">
                    
                    <div>
                        <label class="form-label block mb-1">Client Identifier</label>
                        <input type="text" id="client_identifier" name="client_identifier" class="form-control w-full" placeholder="e.g. client-sso-app" required>
                    </div>
                    
                    <div>
                        <label class="form-label block mb-1">Client Secret</label>
                        <input type="password" id="client_secret" name="client_secret" class="form-control w-full" placeholder="Leave blank to keep current secret on edit">
                    </div>
                    
                    <div>
                        <label class="form-label block mb-1">Client Name</label>
                        <input type="text" id="name" name="name" class="form-control w-full" placeholder="e.g. SSO Application" required>
                    </div>
                    
                    <div>
                        <label class="form-label block mb-1">Redirect URI</label>
                        <textarea id="redirect_uri" name="redirect_uri" class="form-control w-full h-20" placeholder="e.g. http://localhost:8080/callback" required></textarea>
                        <span class="text-slate-400 text-xs mt-1 block">For multiple URIs, separate with commas or newlines.</span>
                    </div>
                    
                    <div>
                        <label class="form-label block mb-1">Confidentiality Type</label>
                        <select id="is_confidential" name="is_confidential" class="form-control w-full">
                            <option value="1">Confidential (Web / Backend apps)</option>
                            <option value="0">Public (SPA / Mobile apps)</option>
                        </select>
                    </div>
                </div>
                
                <div class="vibe-modal-footer">
                    <button type="button" class="btn btn-default" onclick="closeModal('clientModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save Client</button>
                </div>
            </form>
        </div>
    </div>
</div>
