from flask import Blueprint, jsonify, request
from datetime import datetime, timedelta
from models import db, Reservation
import uuid

reservation_bp = Blueprint('reservations', __name__, url_prefix='/api/reservations')

TOTAL_TABLES = 20
GUESTS_PER_TABLE = 4

def get_available_tables(reservation_date):
    """Get available tables for a specific date/time"""
    # Get all reservations for that time slot
    start_time = reservation_date - timedelta(hours=2)
    end_time = reservation_date + timedelta(hours=2)
    
    reservations = Reservation.query.filter(
        Reservation.reservation_date >= start_time,
        Reservation.reservation_date <= end_time,
        Reservation.status != 'cancelled'
    ).all()
    
    reserved_tables = set()
    for res in reservations:
        if res.table_ids:
            reserved_tables.update([int(t) for t in res.table_ids.split(',')])
    
    available = [t for t in range(1, TOTAL_TABLES + 1) if t not in reserved_tables]
    return available

@reservation_bp.route('/check-availability', methods=['POST'])
def check_availability():
    """Check table availability for a specific date/time"""
    data = request.get_json()
    
    try:
        reservation_date = datetime.fromisoformat(data.get('reservation_date'))
        number_of_guests = int(data.get('number_of_guests', 1))
    except:
        return jsonify({'success': False, 'message': 'Invalid date or guest count'}), 400
    
    # Calculate tables needed
    tables_needed = (number_of_guests + GUESTS_PER_TABLE - 1) // GUESTS_PER_TABLE
    
    available_tables = get_available_tables(reservation_date)
    
    if len(available_tables) >= tables_needed:
        return jsonify({
            'success': True,
            'available': True,
            'available_tables': available_tables,
            'tables_needed': tables_needed,
            'message': f'{tables_needed} table(s) available'
        })
    else:
        return jsonify({
            'success': True,
            'available': False,
            'available_tables': available_tables,
            'tables_needed': tables_needed,
            'message': f'Only {len(available_tables)} table(s) available, but {tables_needed} needed'
        })

@reservation_bp.route('/create', methods=['POST'])
def create_reservation():
    """Create a new reservation"""
    data = request.get_json()
    
    try:
        customer_name = data.get('customer_name', '').strip()
        customer_phone = data.get('customer_phone', '').strip()
        customer_email = data.get('customer_email', '').strip()
        reservation_date = datetime.fromisoformat(data.get('reservation_date'))
        number_of_guests = int(data.get('number_of_guests', 1))
        special_requests = data.get('special_requests', '').strip()
        
        if not customer_name or not customer_phone:
            return jsonify({'success': False, 'message': 'Name and phone are required'}), 400
        
        # Validate date is in future
        if reservation_date <= datetime.now():
            return jsonify({'success': False, 'message': 'Reservation date must be in the future'}), 400
        
        # Calculate tables needed
        tables_needed = (number_of_guests + GUESTS_PER_TABLE - 1) // GUESTS_PER_TABLE
        
        # Get available tables
        available_tables = get_available_tables(reservation_date)
        
        if len(available_tables) < tables_needed:
            return jsonify({
                'success': False,
                'message': f'Not enough tables available. Need {tables_needed}, have {len(available_tables)}'
            }), 400
        
        # Assign tables
        assigned_tables = available_tables[:tables_needed]
        
        reservation_number = f"RES-{datetime.now().strftime('%Y%m%d%H%M%S')}-{str(uuid.uuid4())[:8].upper()}"
        
        reservation = Reservation(
            reservation_number=reservation_number,
            customer_name=customer_name,
            customer_phone=customer_phone,
            customer_email=customer_email,
            reservation_date=reservation_date,
            number_of_guests=number_of_guests,
            number_of_tables=tables_needed,
            table_ids=','.join(map(str, assigned_tables)),
            special_requests=special_requests,
            status='confirmed'
        )
        
        db.session.add(reservation)
        db.session.commit()
        
        return jsonify({
            'success': True,
            'message': 'Reservation created successfully',
            'reservation': reservation.to_dict(),
            'reservation_id': reservation.id,
            'reservation_number': reservation_number,
            'assigned_tables': assigned_tables
        })
    
    except ValueError as e:
        return jsonify({'success': False, 'message': f'Invalid input: {str(e)}'}), 400
    except Exception as e:
        db.session.rollback()
        return jsonify({'success': False, 'message': f'Error creating reservation: {str(e)}'}), 500

@reservation_bp.route('/view/<reservation_number>', methods=['GET'])
def view_reservation(reservation_number):
    """View reservation details"""
    reservation = Reservation.query.filter_by(reservation_number=reservation_number).first_or_404()
    
    return jsonify({
        'success': True,
        'reservation': reservation.to_dict()
    })

@reservation_bp.route('/cancel/<int:reservation_id>', methods=['POST'])
def cancel_reservation(reservation_id):
    """Cancel a reservation"""
    reservation = Reservation.query.get_or_404(reservation_id)
    
    if reservation.status == 'cancelled':
        return jsonify({'success': False, 'message': 'Reservation already cancelled'}), 400
    
    reservation.status = 'cancelled'
    db.session.commit()
    
    return jsonify({
        'success': True,
        'message': 'Reservation cancelled successfully',
        'reservation': reservation.to_dict()
    })

@reservation_bp.route('/upcoming', methods=['GET'])
def get_upcoming_reservations():
    """Get upcoming reservations"""
    now = datetime.now()
    reservations = Reservation.query.filter(
        Reservation.reservation_date >= now,
        Reservation.status != 'cancelled'
    ).order_by(Reservation.reservation_date.asc()).all()
    
    return jsonify({
        'success': True,
        'count': len(reservations),
        'reservations': [res.to_dict() for res in reservations]
    })
