PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS "USER" (
    userId INTEGER PRIMARY KEY,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    firstName TEXT NOT NULL,
    lastName TEXT NOT NULL,
    role TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS "ACCOUNT" (
    accountId INTEGER PRIMARY KEY,
    userId INTEGER NOT NULL,
    accountType TEXT NOT NULL,
    points INTEGER NOT NULL DEFAULT 0 CHECK (points >= 0),
    FOREIGN KEY (userId) REFERENCES "USER"(userId)
);

CREATE TABLE IF NOT EXISTS "GIFTCARD" (
    cardId INTEGER PRIMARY KEY,
    cardName TEXT NOT NULL,
    cardType TEXT NOT NULL,
    cardValue REAL NOT NULL CHECK (cardValue >= 0),
    points INTEGER NOT NULL CHECK (points >= 0),
    description TEXT NOT NULL DEFAULT ''
);

CREATE TABLE IF NOT EXISTS "REDEMPTION" (
    redeemId INTEGER PRIMARY KEY,
    date TEXT NOT NULL,
    pointsRedeemed INTEGER NOT NULL CHECK (pointsRedeemed > 0),
    accountId INTEGER NOT NULL,
    cardId INTEGER NOT NULL,
    FOREIGN KEY (accountId) REFERENCES "ACCOUNT"(accountId),
    FOREIGN KEY (cardId) REFERENCES "GIFTCARD"(cardId)
);

CREATE INDEX IF NOT EXISTS idx_account_userId ON "ACCOUNT"(userId);
CREATE INDEX IF NOT EXISTS idx_redemption_accountId ON "REDEMPTION"(accountId);
CREATE INDEX IF NOT EXISTS idx_redemption_cardId ON "REDEMPTION"(cardId);
