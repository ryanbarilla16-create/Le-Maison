import { initializeApp, getApps, getApp } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-app.js";
import { getFirestore, collection } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";
import { getAuth } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-auth.js";
import { firebaseConfig } from "../firebase-config.js";

const app = getApps().length ? getApp() : initializeApp(firebaseConfig);
export const db = getFirestore(app);
export const auth = getAuth(app);

export const ORDERS_COL = collection(db, 'orders');
export const MENU_COL = collection(db, 'menu_items');
export const USERS_COL = collection(db, 'users');
export const RESERVATIONS_COL = collection(db, 'reservations');
export const DELIVERIES_COL = collection(db, 'deliveries');
export const RIDERS_COL = collection(db, 'riders');
export const REVIEWS_COL = collection(db, 'reviews');
export const PROMOTIONS_COL = collection(db, 'promotions');
export const INVENTORY_COL = collection(db, 'inventory');
