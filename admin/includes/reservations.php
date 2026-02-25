<div id="reservations-section" class="section-view">
    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">Table Reservations</h2>
            <button class="btn-action" style="background: var(--dark-brown); color: white;" onclick="openModal('reservationModal')">
                <i class="fas fa-plus"></i> Add Reservation
            </button>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Guest</th>
                    <th>Type</th>
                    <th>Date & Time</th>
                    <th>Guests</th>
                    <th>Occasion</th>
                    <th>Table</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="reservationsTableBody">
                <tr><td colspan="8" class="loading-spinner">Loading reservations...</td></tr>
            </tbody>
        </table>
    </div>
</div>
