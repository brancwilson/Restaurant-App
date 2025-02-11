const express = require('express');
const bodyParser = require('body-parser');
const path = require('path');
const session = require('express-session');

const app = express();

// Middleware
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json()); // Add this to parse JSON requests
app.use(session({ secret: 'secret_key', resave: false, saveUninitialized: true }));
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));
app.use(express.static(path.join(__dirname, 'public')));

// Menu with Prices
const menu = {
    "Lunch Sides": { "Pickled Vegetables": 5, "Rye Bread": 5, "Cheese Blintzes": 5, "Sauerkraut": 5, "Vareniki": 5 },
    "Lunch Entrees": { "Borscht": 15, "Chicken Kiev": 15, "Fish Pie": 15, "Beef Tongue": 15, "Vegetable Stew": 15 },
    "Lunch Drinks": { "Kvass": 2, "Mors": 2, "Black Tea": 2, "Sparkling Water": 2, "Homemade Lemonade": 2 },
    "Dinner Sides": { "Garlic Bread": 5, "Fried Potatoes": 5, "Roasted Beets": 5, "Cabbage Rolls": 5, "Grilled Zucchini": 5 },
    "Dinner Entrees": { "Duck a la Russe": 15, "Caviar Platter": 15, "Rabbit Stew": 15, "Lamb Shashlik": 15, "Stuffed Peppers": 15 },
    "Dinner Drinks": { "Red Wine": 2, "White Wine": 2, "Vodka Shots": 2, "Cherry Kompot": 2, "Sparkling Mineral Water": 2 }
};

// Home Page - Display Categories
app.get('/', (req, res) => {
    const selectedItems = req.session.selectedItems || {};
    let total = 0;

    for (let item in selectedItems) {
        total += selectedItems[item].price * selectedItems[item].quantity;
    }

    res.render('index', { categories: Object.keys(menu), selectedItems, total, menu });
});

// Menu Page - Display Items in a Category
app.get('/menu/:category', (req, res) => {
    const category = req.params.category.replace(/-/g, ' '); // Replace hyphens with spaces
    const items = menu[category] || {};
    const selectedItems = req.session.selectedItems || {};

    // Calculate the total
    let total = 0;
    for (let item in selectedItems) {
        total += selectedItems[item].price * selectedItems[item].quantity;
    }

    res.render('menu', { category, items, selectedItems, total, menu });
});

// Save Selected Items (AJAX Endpoint)
app.post('/add-item', (req, res) => {
    if (!req.session.selectedItems) {
        req.session.selectedItems = {};
    }

    const { item, category, price } = req.body;

    if (!req.session.selectedItems[item]) {
        req.session.selectedItems[item] = { category, price: parseFloat(price), quantity: 1 };
    } else {
        req.session.selectedItems[item].quantity += 1;
    }

    // Send a JSON response with the updated selected items
    res.json({ success: true, selectedItems: req.session.selectedItems });
});

// Fetch Selected Items (AJAX Endpoint)
app.get('/get-selected-items', (req, res) => {
    const selectedItems = req.session.selectedItems || {};
    let total = 0;

    for (let item in selectedItems) {
        total += selectedItems[item].price * selectedItems[item].quantity;
    }

    res.json({ selectedItems, total });
});

// Order Total Page
app.get('/total', (req, res) => {
    const selectedItems = req.session.selectedItems || {};
    let total = 0;

    for (let item in selectedItems) {
        total += selectedItems[item].price * selectedItems[item].quantity;
    }

    res.render('total', { selectedItems, total });
});

// Clear Cart
app.post('/clear-cart', (req, res) => {
    req.session.selectedItems = {};
    res.redirect('/');
});

app.listen(3000, () => {
    console.log('Server running on http://localhost:3000');
});