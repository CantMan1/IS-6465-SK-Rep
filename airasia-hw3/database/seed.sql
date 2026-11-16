INSERT OR IGNORE INTO "USER" (userId, username, password, firstName, lastName, role) VALUES
    (1, 'alex.chen', 'seed-only-no-login', 'Alex', 'Chen', 'MEMBER'),
    (2, 'jamie.patel', 'seed-only-no-login', 'Jamie', 'Patel', 'MEMBER'),
    (3, 'taylor.rivera', 'seed-only-no-login', 'Taylor', 'Rivera', 'MEMBER'),
    (4, 'morgan.lee', 'seed-only-no-login', 'Morgan', 'Lee', 'MEMBER'),
    (5, 'casey.johnson', 'seed-only-no-login', 'Casey', 'Johnson', 'MEMBER');

INSERT OR IGNORE INTO "ACCOUNT" (accountId, userId, accountType, points) VALUES
    (1, 1, 'Rewards', 2500),
    (2, 2, 'Rewards', 1800),
    (3, 3, 'Rewards', 3200),
    (4, 4, 'Rewards', 950),
    (5, 5, 'Rewards', 4100);

INSERT OR IGNORE INTO "GIFTCARD" (cardId, cardName, cardType, cardValue, points, description) VALUES
    (1, 'Amazon Gift Card', 'Retail', 25.00, 1000, 'Shop books, electronics, home essentials, and more at Amazon.'),
    (2, 'Starbucks Gift Card', 'Dining', 25.00, 1000, 'Enjoy coffee, tea, and treats at participating Starbucks stores.'),
    (3, 'AirAsia Travel Gift Card', 'Travel', 50.00, 2000, 'Put your points toward your next AirAsia trip and travel adventure.'),
    (4, 'Target Gift Card', 'Retail', 25.00, 1000, 'Find everyday essentials, clothing, home goods, and more at Target.'),
    (5, 'Walmart Gift Card', 'Retail', 25.00, 1000, 'Use this card on groceries, household items, electronics, and more at Walmart.'),
    (6, 'Best Buy Gift Card', 'Retail', 50.00, 2000, 'Shop computers, appliances, entertainment, and electronics at Best Buy.'),
    (7, 'Apple Gift Card', 'Retail', 25.00, 1000, 'Use it for Apple products, accessories, apps, games, and subscriptions.'),
    (8, 'Home Depot Gift Card', 'Retail', 50.00, 2000, 'Put this card toward tools, building supplies, and home improvement projects.'),
    (9, 'Lowe''s Gift Card', 'Retail', 25.00, 1000, 'Shop for home improvement, appliances, tools, and garden supplies at Lowe''s.'),
    (10, 'Uber Gift Card', 'Travel', 25.00, 1000, 'Use your balance for eligible Uber rides and services.'),
    (11, 'Airbnb Gift Card', 'Travel', 50.00, 2000, 'Put this gift card toward eligible stays and experiences on Airbnb.'),
    (12, 'Delta Air Lines Gift Card', 'Travel', 100.00, 4000, 'Plan your next trip with a gift card for eligible Delta flight purchases.'),
    (13, 'Southwest Airlines Gift Card', 'Travel', 100.00, 4000, 'Use this card toward eligible flights on Southwest Airlines.'),
    (14, 'Netflix Gift Card', 'Entertainment', 25.00, 1000, 'Enjoy movies and series by applying this card to a Netflix account.'),
    (15, 'Spotify Gift Card', 'Entertainment', 25.00, 1000, 'Listen to music and podcasts with eligible Spotify Premium plans.'),
    (16, 'PlayStation Store Gift Card', 'Entertainment', 25.00, 1000, 'Add funds to your PlayStation account for games and digital content.'),
    (17, 'Xbox Gift Card', 'Entertainment', 25.00, 1000, 'Use your balance for games, add-ons, and entertainment on Xbox.'),
    (18, 'DoorDash Gift Card', 'Dining', 25.00, 1000, 'Order from local restaurants and stores with DoorDash.'),
    (19, 'Chipotle Gift Card', 'Dining', 25.00, 1000, 'Enjoy burritos, bowls, and other favorites at participating Chipotle locations.'),
    (20, 'Sephora Gift Card', 'Retail', 50.00, 2000, 'Shop beauty, skincare, fragrance, and more at Sephora.');
