from flask import Blueprint, jsonify, request, render_template, session
from datetime import datetime
from models import db, Order, OrderItem, Payment, MenuItem
import uuid

checkout_bp = Blueprint('checkout', __name__, url_prefix='/api/checkout')

@checkout_bp.route('/process', methods=['POST'])
def process_checkout():
    """Process order from cart"""
    from models import db, Order, OrderItem, Payment, MenuItem
    
    data = request.get_json()
    cart = session.get('cart', [])
    
    if not cart:
        return jsonify({'success': False, 'message': 'Cart is empty'}), 400
    
    # Validate input
    customer_name = data.get('customer_name', '').strip()
    customer_phone = data.get('customer_phone', '').strip()
    customer_email = data.get('customer_email', '').strip()
    order_type = data.get('order_type', 'dine-in')  # dine-in, takeaway, delivery
    payment_method = data.get('payment_method')  # cod, gcash
    
    if not customer_name or not customer_phone:
        return jsonify({'success': False, 'message': 'Name and phone are required'}), 400
    
    if payment_method not in ['cod', 'gcash']:
        return jsonify({'success': False, 'message': 'Invalid payment method'}), 400
    
    try:
        # Create order
        order_number = f"ORD-{datetime.now().strftime('%Y%m%d%H%M%S')}-{str(uuid.uuid4())[:8].upper()}"
        order = Order(
            order_number=order_number,
            customer_name=customer_name,
            customer_phone=customer_phone,
            customer_email=customer_email,
            order_type=order_type,
            payment_method=payment_method,
            status='confirmed'
        )
        
        total_amount = 0
        
        # Add order items
        for cart_item in cart:
            menu_item = MenuItem.query.get(cart_item['item_id'])
            if not menu_item:
                return jsonify({'success': False, 'message': f'Menu item not found'}), 400
            
            order_item = OrderItem(
                menu_item_id=cart_item['item_id'],
                quantity=cart_item['quantity'],
                unit_price=menu_item.price,
                subtotal=menu_item.price * cart_item['quantity'],
                special_instructions=cart_item.get('special_instructions', '')
            )
            order.items.append(order_item)
            total_amount += order_item.subtotal
        
        # Add delivery fee if applicable
        if order_type == 'delivery':
            delivery_fee = data.get('delivery_fee', 50)
            order.delivery_fee = delivery_fee
            order.delivery_address = data.get('delivery_address', '')
            total_amount += delivery_fee
        
        order.total_amount = total_amount
        
        # Create payment record
        payment = Payment(
            amount=total_amount,
            payment_method=payment_method,
            status='pending' if payment_method == 'cod' else 'pending'
        )
        order.payment = payment
        
        db.session.add(order)
        db.session.commit()
        
        # Clear cart
        session['cart'] = []
        session.modified = True
        
        return jsonify({
            'success': True,
            'message': 'Order created successfully',
            'order': order.to_dict(),
            'order_id': order.id,
            'order_number': order_number
        })
    
    except Exception as e:
        db.session.rollback()
        return jsonify({'success': False, 'message': f'Error processing order: {str(e)}'}), 500

@checkout_bp.route('/receipt/<int:order_id>', methods=['GET'])
def get_receipt(order_id):
    """Get order receipt"""
    order = Order.query.get_or_404(order_id)
    
    receipt = {
        'order_number': order.order_number,
        'restaurant': 'Le Maison Yelo Lane',
        'date': order.created_at.strftime('%Y-%m-%d %H:%M:%S'),
        'customer_name': order.customer_name,
        'customer_phone': order.customer_phone,
        'order_type': order.order_type,
        'items': [],
        'subtotal': 0,
        'delivery_fee': order.delivery_fee,
        'total': order.total_amount,
        'payment_method': order.payment_method,
        'payment_status': order.payment_status,
        'status': order.status
    }
    
    for item in order.items:
        receipt['items'].append({
            'name': item.menu_item.name,
            'quantity': item.quantity,
            'unit_price': item.unit_price,
            'subtotal': item.subtotal
        })
        receipt['subtotal'] += item.subtotal
    
    return jsonify({
        'success': True,
        'receipt': receipt
    })

@checkout_bp.route('/confirm-payment/<int:order_id>', methods=['POST'])
def confirm_payment(order_id):
    """Confirm payment"""
    data = request.get_json()
    order = Order.query.get_or_404(order_id)
    
    payment = order.payment
    if not payment:
        return jsonify({'success': False, 'message': 'No payment record found'}), 400
    
    # For COD, mark as unpaid initially
    if order.payment_method == 'cod':
        payment.status = 'pending'
        order.payment_status = 'pending'
        message = 'Order confirmed. Please pay upon delivery.'
    
    # For GCash, verify and mark as paid
    elif order.payment_method == 'gcash':
        reference = data.get('reference_number', '')
        payment.reference_number = reference
        payment.status = 'completed'
        order.payment_status = 'paid'
        message = 'Payment confirmed via GCash'
    
    payment.completed_at = datetime.utcnow()
    db.session.commit()
    
    return jsonify({
        'success': True,
        'message': message,
        'order': order.to_dict()
    })

@checkout_bp.route('/order/<order_number>', methods=['GET'])
def track_order(order_number):
    """Track order by order number"""
    order = Order.query.filter_by(order_number=order_number).first_or_404()
    
    return jsonify({
        'success': True,
        'order': order.to_dict()
    })
