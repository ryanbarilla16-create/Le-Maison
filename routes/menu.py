from flask import Blueprint, jsonify, session
from models import db, MenuItem, Category

menu_bp = Blueprint('menu', __name__, url_prefix='/api')

@menu_bp.route('/categories', methods=['GET'])
def get_categories():
    """Get all categories"""
    categories = Category.query.all()
    return jsonify({
        'success': True,
        'data': [cat.to_dict() for cat in categories]
    })

@menu_bp.route('/items', methods=['GET'])
def get_menu_items():
    """Get all menu items"""
    items = MenuItem.query.filter_by(available=True).all()
    return jsonify({
        'success': True,
        'data': [item.to_dict() for item in items]
    })

@menu_bp.route('/items/category/<int:category_id>', methods=['GET'])
def get_items_by_category(category_id):
    """Get menu items by category"""
    items = MenuItem.query.filter_by(category_id=category_id, available=True).all()
    return jsonify({
        'success': True,
        'data': [item.to_dict() for item in items]
    })

@menu_bp.route('/item/<int:item_id>', methods=['GET'])
def get_menu_item(item_id):
    """Get single menu item"""
    item = MenuItem.query.get_or_404(item_id)
    return jsonify({
        'success': True,
        'data': item.to_dict()
    })
