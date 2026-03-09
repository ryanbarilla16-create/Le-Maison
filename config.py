import os
from datetime import timedelta

class Config:
    """Base configuration"""
    basedir = os.path.abspath(os.path.dirname(__file__))
    
    # Database
    SQLALCHEMY_DATABASE_URI = 'sqlite:///' + os.path.join(basedir, 'instance', 'lemaisonyelolane.db')
    SQLALCHEMY_TRACK_MODIFICATIONS = False
    
    # Session
    PERMANENT_SESSION_LIFETIME = timedelta(days=7)
    SESSION_COOKIE_SECURE = False
    SESSION_COOKIE_HTTPONLY = True
    SESSION_COOKIE_SAMESITE = 'Lax'
    
    # App settings
    RESTAURANT_NAME = "Le Maison Yelo Lane"
    TIMEZONE = "Asia/Manila"
    RESTAURANT_HOURS = {
        "monday": {"open": "10:00", "close": "22:00"},
        "tuesday": {"open": "10:00", "close": "22:00"},
        "wednesday": {"open": "10:00", "close": "22:00"},
        "thursday": {"open": "10:00", "close": "22:00"},
        "friday": {"open": "10:00", "close": "23:00"},
        "saturday": {"open": "11:00", "close": "23:00"},
        "sunday": {"open": "11:00", "close": "22:00"},
    }
    TOTAL_TABLES = 20
    RESERVATION_ADVANCE_DAYS = 30
