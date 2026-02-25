<div id="settings-section" class="section-view">
    <div class="settings-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- Restaurant Info -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Restaurant Information</h2>
            </div>
            <form id="restaurantInfoForm" style="padding: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Restaurant Name</label>
                    <input type="text" id="settingRestName" class="form-control" value="Le Maison de Yelo Lane">
                </div>
                <div class="form-group">
                    <label class="form-label">Contact Email</label>
                    <input type="email" id="settingEmail" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" id="settingPhone" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea id="settingAddress" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn-submit">Save Restaurant Info</button>
            </form>
        </div>

        <!-- Social Media Links -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Social Media Links</h2>
            </div>
            <form id="socialMediaForm" style="padding: 1.5rem;">
                <div class="form-group">
                    <label class="form-label"><i class="fab fa-facebook" style="color:#1877F2;"></i> Facebook URL</label>
                    <input type="url" id="settingFacebook" class="form-control" placeholder="https://facebook.com/...">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fab fa-instagram" style="color:#E4405F;"></i> Instagram URL</label>
                    <input type="url" id="settingInstagram" class="form-control" placeholder="https://instagram.com/...">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fab fa-tiktok" style="color:#000000;"></i> TikTok URL</label>
                    <input type="url" id="settingTikTok" class="form-control" placeholder="https://tiktok.com/@...">
                </div>
                <button type="submit" class="btn-submit">Save Social Links</button>
            </form>
        </div>

        <!-- Operating Hours -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Operating Hours</h2>
            </div>
            <form id="operatingHoursForm" style="padding: 1.5rem;">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label">Opening Time</label>
                        <input type="time" id="settingOpenTime" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Closing Time</label>
                        <input type="time" id="settingCloseTime" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Days Open</label>
                    <input type="text" id="settingDaysOpen" class="form-control" placeholder="e.g. Mon - Sun">
                </div>
                <button type="submit" class="btn-submit">Save Hours</button>
            </form>
        </div>

        <!-- Audit Logs / Activity -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">System Activity</h2>
            </div>
            <div style="padding: 1rem; max-height: 380px; overflow-y: auto;" id="auditLogList">
                <p style="text-align:center; color:#999; padding:2rem;">No recent activity</p>
            </div>
        </div>
    </div>
</div>
