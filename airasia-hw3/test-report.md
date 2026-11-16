"Table" Layout: Test ID - Test - Test steps and input - Expected result - Actual result - Pass/Fail 

T01 - Validation test harness - Run 'php tests/validation-tests.php'. Inputs include valid name/type/value/points/ID, blank and overlong names, legacy type 'Amazon', zero/negative values, invalid points ('100.5', '1,00', '0'), and invalid IDs. - Each valid value passes; each invalid value fails; the harness prints a result for each check. - All 7 validation checks printed 'PASS'; 0 failed. - PASS

T02 - View Gift Card List - Open 'card-list.php' with the fresh isolated database and no request input. - The page displays the 20 seeded gift cards. - The page rendered 20 gift-card entries. - PASS

T03 - View Gift Card Details - Open 'card-details.php?id=1'. - The selected card's name, category, value, points, and description are displayed. - The page displayed “Amazon Gift Card” and “Card Type: Retail” with its details. - PASS

T04 - Add Gift Card - Submit 'T04 Test Gift Card', type 'Other', value '$35.00', points '1,500', and a description through 'card-add.php'. - A new 'GIFTCARD' record is inserted and appears in the list. - The record was inserted and 'T04 Test Gift Card' appeared on the list. - PASS

T05 - Update Gift Card - Submit an update for card ID '1': name 'T05 Updated Gift Card', type 'Travel', value '$42.50', points '2,500', and an updated description. - The existing 'GIFTCARD' record is updated and the details page shows the new values. - The values persisted and the details page showed the updated name and 'Travel' category. - PASS

T06 - Delete Gift Card - POST the delete action with a valid CSRF token for the newly added card, which has no redemption history. - The card is removed and a confirmation page offers a return to the list. - The record was removed; the confirmation displayed its name and the return-to-list link. - PASS 

T07 - Invalid or altered input handling - Submit an overlong name, 'Amazon' as the type, value '0', and points '1.5' on add; then submit type 'Amazon', value '-1', and points '1.5' on update. - Both forms display validation errors and do not insert or change records. - Both forms rejected the input; the database count and updated card values remained unchanged. - PASS

T08 - Invalid Gift Card identifier handling - Check identifiers 'not-an-id', '0', and '-2'; request 'card-update.php?id=not-an-id'. - Invalid identifiers are rejected and the update form is not shown. - The validator rejected all three identifiers and the invalid update request exited before rendering the form. - PASS