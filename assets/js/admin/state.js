export const state = {
    currentEditId: null,
    currentResEditId: null,
    currentPromoEditId: null,
    isSaving: false,

    // Charts
    revenueTrendChart: null,
    orderStatusChart: null,
    modalRevenueChart: null,
    modalOrderBreakdownChart: null,
    dailyOrdersChart: null,
    categoryRevenueChart: null,
    peakHoursChart: null,
    monthlyRevenueChart: null,
    topSellingChart: null,
    loyaltyChart: null,

    // Delivery
    activeRiderMarkers: {},
    deliveryMap: null
};

export const CHART_COLORS = {
    gold: '#D4AF37',
    goldAlpha: 'rgba(212, 175, 55, 0.15)',
    brown: '#2C1810',
    pending: '#FFEB3B',
    preparing: '#29B6F6',
    ready: '#00E676',
    delivered: '#7E57C2',
    amber: '#FFD700',
    sage: '#81C784',
    warmRed: '#E53935',
    slate: '#455A64',
    text: '#555'
};
