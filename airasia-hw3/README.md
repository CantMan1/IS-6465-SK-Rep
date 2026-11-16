Use case - PHP page or pages - Database table(s) - Test evidence 

View a List of Gift Cards - `card-list.php` - `GIFTCARD` - Rendered the page and confirmed all 20 seeded gift cards appeared in the list. -
View Details of One Gift Card - `card-details.php` - `GIFTCARD` - Rendered `card-details.php?id=1` and confirmed the selected card's category appeared in its details. 
Add a Gift Card - `card-add.php` - `GIFTCARD` - Tested valid and invalid submissions in isolated SQLite checks; confirmed a valid card appeared in the list and invalid input was rejected. 
Update a Gift Card - `card-update.php` - `GIFTCARD` - Tested an update in isolated SQLite and confirmed the changed details appeared in the list; invalid input was rejected. 
Delete a Gift Card - `card-details.php`, `card-delete.php` - `GIFTCARD` - Tested deletion in isolated SQLite, confirmed the deleted card was removed, and checked the success confirmation path. 