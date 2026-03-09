from flask import Flask, render_template, session, request
from config import Config
from models import db, Category, MenuItem, Order, Reservation
from routes.menu import menu_bp
from routes.cart import cart_bp
from routes.checkout import checkout_bp
from routes.reservations import reservation_bp
from datetime import timedelta
import os

app = Flask(__name__)
app.config.from_object(Config)

# Initialize database
db.init_app(app)

# Register blueprints
app.register_blueprint(menu_bp)
app.register_blueprint(cart_bp)
app.register_blueprint(checkout_bp)
app.register_blueprint(reservation_bp)

# Session configuration
app.config['PERMANENT_SESSION_LIFETIME'] = timedelta(days=7)
app.config['SESSION_COOKIE_SECURE'] = False
app.config['SESSION_COOKIE_HTTPONLY'] = True

@app.before_request
def before_request():
    session.permanent = True
    app.permanent_session_lifetime = timedelta(days=7)

# Create instance folder if it doesn't exist
os.makedirs(os.path.join(os.path.dirname(__file__), 'instance'), exist_ok=True)

# ==================== CONTEXT PROCESSORS ====================

@app.context_processor
def inject_config():
    """Inject config variables into templates"""
    return {
        'restaurant_name': Config.RESTAURANT_NAME,
        'cart_count': len(session.get('cart', []))
    }

# ==================== ROUTES ====================

@app.route('/')
def index():
    """Home page"""
    return render_template('index.html')

@app.route('/menu')
def menu():
    """Menu page"""
    categories = Category.query.all()
    return render_template('menu.html', categories=categories)

@app.route('/cart')
def cart():
    """Shopping cart page"""
    return render_template('cart.html')

@app.route('/checkout')
def checkout():
    """Checkout page"""
    return render_template('checkout.html')

@app.route('/order/<order_number>')
def order_confirmation(order_number):
    """Order confirmation and receipt page"""
    return render_template('receipt.html', order_number=order_number)

@app.route('/reservations')
def reservations():
    """Reservations page"""
    return render_template('reservations.html')

@app.route('/track-order')
def track_order_page():
    """Track order page"""
    return render_template('track_order.html')

@app.errorhandler(404)
def not_found(error):
    """404 error handler"""
    return render_template('404.html'), 404

@app.errorhandler(500)
def server_error(error):
    """500 error handler"""
    return render_template('500.html'), 500

# ==================== DATABASE INITIALIZATION ====================

def init_db():
    """Initialize database with sample data"""
    with app.app_context():
        db.create_all()
        
        # Check if data already exists
        if Category.query.first() is None:
            # Create categories
            categories = [
                Category(name='Appetizers', description='Start your meal with these delicious appetizers'),
                Category(name='Main Courses', description='Our signature main dishes'),
                Category(name='Desserts', description='Sweet treats to end your meal'),
                Category(name='Beverages', description='Refresh yourself with our drinks')
            ]
            
            for cat in categories:
                db.session.add(cat)
            db.session.commit()
            
            # Create menu items
            appetizers = Category.query.filter_by(name='Appetizers').first()
            main_courses = Category.query.filter_by(name='Main Courses').first()
            desserts = Category.query.filter_by(name='Desserts').first()
            beverages = Category.query.filter_by(name='Beverages').first()
            
            items = [
                MenuItem(category=appetizers, name='Garlic Bread', description='Crispy bread with garlic butter', price=120),
                MenuItem(category=appetizers, name='Spring Rolls', description='Vegetable spring rolls with sweet sauce', price=150),
                MenuItem(category=appetizers, name='Calamari Rings', description='Crispy fried squid rings', price=280),
                
                MenuItem(category=main_courses, name='Beef Tenderloin', description='Grilled beef tenderloin with vegetables', price=450),
                MenuItem(category=main_courses, name='Chicken Parm', description='Breaded chicken with mozzarella', price=350),
                MenuItem(category=main_courses, name='Salmon Fillet', description='Grilled salmon with lemon butter', price=520),
                MenuItem(category=main_courses, name='Pasta Carbonara', description='Classic Italian pasta with cream and bacon', price=320),
                
                MenuItem(category=desserts, name='Tiramisu', description='Classic Italian layered dessert', price=180),
                MenuItem(category=desserts, name='Chocolate Cake', description='Rich chocolate cake with ganache', price=150),
                MenuItem(category=desserts, name='Mango Cheesecake', description='Creamy cheesecake with fresh mango', price=200),
                
                MenuItem(category=beverages, name='Iced Tea', description='Fresh brewed iced tea', price=80),
                MenuItem(category=beverages, name='Coffee', description='Hot or iced coffee', price=100),
                MenuItem(category=beverages, name='Soft Drinks', description='Assorted soft drinks', price=70),
            ]
            
            for item in items:
                db.session.add(item)
            db.session.commit()
            
            print("Database initialized with sample data!")

if __name__ == '__main__':
    init_db()
    app.run(debug=True, host='0.0.0.0', port=5000)
