from flask_sqlalchemy import SQLAlchemy
from datetime import datetime
from flask_login import UserMixin
from werkzeug.security import generate_password_hash, check_password_hash
from utils import get_ph_time

db = SQLAlchemy()

class User(db.Model, UserMixin):
    id = db.Column(db.Integer, primary_key=True)
    first_name = db.Column(db.String(50), nullable=False)
    middle_name = db.Column(db.String(50), nullable=True)
    phone_number = db.Column(db.String(15), nullable=True)
    last_name = db.Column(db.String(50), nullable=False)
    username = db.Column(db.String(20), unique=True, nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=False)
    birthday = db.Column(db.Date, nullable=True)
    password_hash = db.Column(db.String(256), nullable=False)
    profile_picture_url = db.Column(db.String(500), nullable=True)
    status = db.Column(db.String(20), default='PENDING')
    role = db.Column(db.String(20), default='USER')
    is_verified = db.Column(db.Boolean, default=False)
    otp_code = db.Column(db.String(6), nullable=True)
    otp_created_at = db.Column(db.DateTime, nullable=True)

    def set_password(self, password):
        self.password_hash = generate_password_hash(password, method='pbkdf2:sha256')

    def check_password(self, password):
        return check_password_hash(self.password_hash, password)

# ==================== MENU MODELS ====================

class Category(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(100), nullable=False, unique=True)
    description = db.Column(db.Text)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    
    items = db.relationship('MenuItem', back_populates='category', cascade='all, delete-orphan')
    
    def to_dict(self):
        return {
            'id': self.id,
            'name': self.name,
            'description': self.description
        }

class MenuItem(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    category_id = db.Column(db.Integer, db.ForeignKey('category.id'), nullable=False)
    name = db.Column(db.String(150), nullable=False)
    description = db.Column(db.Text)
    price = db.Column(db.Float, nullable=False)
    image_url = db.Column(db.String(255))
    available = db.Column(db.Boolean, default=True)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    
    category = db.relationship('Category', back_populates='items')
    order_items = db.relationship('OrderItem', back_populates='menu_item')
    
    def to_dict(self):
        return {
            'id': self.id,
            'category_id': self.category_id,
            'category_name': self.category.name,
            'name': self.name,
            'description': self.description,
            'price': self.price,
            'image_url': self.image_url,
            'available': self.available
        }

# ==================== ORDER MODELS ====================

class Order(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    order_number = db.Column(db.String(50), unique=True, nullable=False)
    customer_name = db.Column(db.String(150), nullable=False)
    customer_phone = db.Column(db.String(20), nullable=False)
    customer_email = db.Column(db.String(150))
    
    # Order details
    total_amount = db.Column(db.Float, default=0)
    status = db.Column(db.String(50), default='pending')  # pending, confirmed, preparing, ready, completed, cancelled
    payment_method = db.Column(db.String(50))  # cod, gcash
    payment_status = db.Column(db.String(50), default='unpaid')  # unpaid, paid, void
    
    # Delivery/Dining
    order_type = db.Column(db.String(50))  # dine-in, takeaway, delivery
    delivery_address = db.Column(db.Text)
    delivery_fee = db.Column(db.Float, default=0)
    
    # Timestamps
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    completed_at = db.Column(db.DateTime)
    
    items = db.relationship('OrderItem', back_populates='order', cascade='all, delete-orphan')
    payment = db.relationship('Payment', back_populates='order', uselist=False, cascade='all, delete-orphan')
    
    def to_dict(self):
        return {
            'id': self.id,
            'order_number': self.order_number,
            'customer_name': self.customer_name,
            'customer_phone': self.customer_phone,
            'customer_email': self.customer_email,
            'total_amount': self.total_amount,
            'status': self.status,
            'payment_method': self.payment_method,
            'payment_status': self.payment_status,
            'order_type': self.order_type,
            'items': [item.to_dict() for item in self.items],
            'created_at': self.created_at.strftime('%Y-%m-%d %H:%M:%S')
        }

class OrderItem(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    order_id = db.Column(db.Integer, db.ForeignKey('order.id'), nullable=False)
    menu_item_id = db.Column(db.Integer, db.ForeignKey('menu_item.id'), nullable=False)
    quantity = db.Column(db.Integer, nullable=False)
    unit_price = db.Column(db.Float, nullable=True)
    price_at_time = db.Column(db.Float, nullable=True)
    subtotal = db.Column(db.Float, nullable=False)
    special_instructions = db.Column(db.Text)
    
    order = db.relationship('Order', back_populates='items')
    menu_item = db.relationship('MenuItem', back_populates='order_items')
    
    def to_dict(self):
        return {
            'id': self.id,
            'menu_item': self.menu_item.to_dict() if self.menu_item else None,
            'quantity': self.quantity,
            'unit_price': self.unit_price if self.unit_price is not None else self.price_at_time,
            'price_at_time': self.price_at_time,
            'subtotal': self.subtotal,
            'special_instructions': self.special_instructions
        }

class Payment(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    order_id = db.Column(db.Integer, db.ForeignKey('order.id'), nullable=False)
    amount = db.Column(db.Float, nullable=False)
    payment_method = db.Column(db.String(50), nullable=False)  # cod, gcash
    reference_number = db.Column(db.String(100))  # For GCash reference
    status = db.Column(db.String(50), default='pending')  # pending, completed, failed, refunded
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    completed_at = db.Column(db.DateTime)
    notes = db.Column(db.Text)
    
    order = db.relationship('Order', back_populates='payment')
    
    def to_dict(self):
        return {
            'id': self.id,
            'amount': self.amount,
            'payment_method': self.payment_method,
            'reference_number': self.reference_number,
            'status': self.status,
            'created_at': self.created_at.strftime('%Y-%m-%d %H:%M:%S')
        }

# ==================== RESERVATION MODELS ====================

class Reservation(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    reservation_number = db.Column(db.String(50), unique=True, nullable=False)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    customer_name = db.Column(db.String(150), nullable=False)
    customer_phone = db.Column(db.String(20), nullable=False)
    customer_email = db.Column(db.String(150))

    # Legacy/per-site reservation model
    reservation_date = db.Column(db.DateTime, nullable=True)
    number_of_guests = db.Column(db.Integer, nullable=True)
    number_of_tables = db.Column(db.Integer, nullable=True)
    table_ids = db.Column(db.String(255))  # Comma-separated table numbers

    # API reservation version expected by routes/reservations
    date = db.Column(db.Date, nullable=True)
    time = db.Column(db.Time, nullable=True)
    guest_count = db.Column(db.Integer, nullable=True)
    occasion = db.Column(db.String(50), nullable=True)
    booking_type = db.Column(db.String(20), nullable=True)

    special_requests = db.Column(db.Text)
    status = db.Column(db.String(50), default='confirmed')  # confirmed, cancelled, completed
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

    user = db.relationship('User', backref=db.backref('reservations', lazy=True))

    def to_dict(self):
        return {
            'id': self.id,
            'reservation_number': self.reservation_number,
            'user_id': self.user_id,
            'customer_name': self.customer_name,
            'customer_phone': self.customer_phone,
            'customer_email': self.customer_email,
            'reservation_date': (self.reservation_date.strftime('%Y-%m-%d %H:%M:%S') if self.reservation_date else None),
            'date': (self.date.strftime('%Y-%m-%d') if self.date else None),
            'time': (self.time.strftime('%H:%M:%S') if self.time else None),
            'guest_count': self.guest_count or self.number_of_guests,
            'number_of_guests': self.number_of_guests,
            'number_of_tables': self.number_of_tables,
            'table_ids': self.table_ids,
            'occasion': self.occasion,
            'booking_type': self.booking_type,
            'special_requests': self.special_requests,
            'status': self.status,
            'created_at': self.created_at.strftime('%Y-%m-%d %H:%M:%S')
        }


class Review(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    order_id = db.Column(db.Integer, db.ForeignKey('order.id'), nullable=True)
    rating = db.Column(db.Integer, nullable=False)
    comment = db.Column(db.Text, nullable=True)
    status = db.Column(db.String(20), default='PENDING')
    created_at = db.Column(db.DateTime, default=get_ph_time)

    user = db.relationship('User', backref=db.backref('reviews', lazy=True))

    def to_dict(self):
        return {
            'id': self.id,
            'user_id': self.user_id,
            'order_id': self.order_id,
            'rating': self.rating,
            'comment': self.comment,
            'status': self.status,
            'created_at': self.created_at.strftime('%Y-%m-%d %H:%M:%S') if self.created_at else None
        }


class Notification(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    title = db.Column(db.String(200), nullable=False)
    message = db.Column(db.Text, nullable=False)
    type = db.Column(db.String(30), default='SYSTEM')
    is_read = db.Column(db.Boolean, default=False)
    created_at = db.Column(db.DateTime, default=get_ph_time)

    user = db.relationship('User', backref=db.backref('notifications', lazy=True, order_by='Notification.created_at.desc()'))

    def to_dict(self):
        return {
            'id': self.id,
            'user_id': self.user_id,
            'title': self.title,
            'message': self.message,
            'type': self.type,
            'is_read': self.is_read,
            'created_at': self.created_at.strftime('%Y-%m-%d %H:%M:%S') if self.created_at else None
        }
