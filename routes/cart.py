from flask import Blueprint, jsonify, request, session
from datetime import datetime

cart_bp = Blueprint('cart', __name__, url_prefix='/api/cart')

@cart_bp.route('/add', methods=['POST'])
def add_to_cart():
    """Add item to cart"""
    if 'cart' not in session:
        session['cart'] = []
    
    data = request.get_json()
    item_id = data.get('item_id')
    quantity = data.get('quantity', 1)
    special_instructions = data.get('special_instructions', '')
    
    # Check if item already in cart
    cart_item = next((item for item in session['cart'] if item['item_id'] == item_id), None)
    
    if cart_item:
        cart_item['quantity'] += quantity
    else:
        session['cart'].append({
            'item_id': item_id,
            'quantity': quantity,
            'special_instructions': special_instructions
        })
    
    session.modified = True
    
    return jsonify({
        'success': True,
        'message': 'Item added to cart',
        'cart_count': len(session['cart'])
    })

@cart_bp.route('/view', methods=['GET'])
def view_cart():
    """View cart"""
    from models import MenuItem
    
    cart = session.get('cart', [])
    cart_items = []
    total = 0
    
    for item in cart:
        menu_item = MenuItem.query.get(item['item_id'])
        if menu_item:
            item_total = menu_item.price * item['quantity']
            cart_items.append({
                'id': item['item_id'],
                'name': menu_item.name,
                'price': menu_item.price,
                'quantity': item['quantity'],
                'subtotal': item_total,
                'special_instructions': item['special_instructions']
            })
            total += item_total
    
    return jsonify({
        'success': True,
        'items': cart_items,
        'total': total,
        'item_count': len(cart_items)
    })

@cart_bp.route('/update/<int:item_id>', methods=['PUT'])
def update_cart_item(item_id):
    """Update cart item quantity"""
    data = request.get_json()
    quantity = data.get('quantity', 1)
    
    if 'cart' in session:
        cart_item = next((item for item in session['cart'] if item['item_id'] == item_id), None)
        if cart_item:
            cart_item['quantity'] = quantity
            session.modified = True
    
    return jsonify({'success': True, 'message': 'Cart updated'})

@cart_bp.route('/remove/<int:item_id>', methods=['DELETE'])
def remove_from_cart(item_id):
    """Remove item from cart"""
    if 'cart' in session:
        session['cart'] = [item for item in session['cart'] if item['item_id'] != item_id]
        session.modified = True
    
    return jsonify({'success': True, 'message': 'Item removed from cart'})

@cart_bp.route('/clear', methods=['POST'])
def clear_cart():
    """Clear entire cart"""
    session['cart'] = []
    session.modified = True
    return jsonify({'success': True, 'message': 'Cart cleared'})
