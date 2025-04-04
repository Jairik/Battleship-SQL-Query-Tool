/* SQL Project - Battleship - Commands & Outputs*/

/* Q1 - Find the ships heavier than 35,000 tons*/

SELECT Ships.name, Ships.class, Ships.launched
FROM Ships, Classes 
WHERE Ships.class = Classes.class AND Classes.displacement < 35000;  

/* Output:
+-----------------+-----------+----------+
| name            | class     | launched |
+-----------------+-----------+----------+
| California      | Tennessee |     1921 |
| Haruna          | Kongo     |     1915 |
| Hiei            | Kongo     |     1914 |
| Kirishima       | Kongo     |     1915 |
| Kongo           | Kongo     |     1913 |
| Ramillies       | Revenge   |     1917 |
| Renown          | Renown    |     1916 |
| Repulse         | Renown    |     1916 |
| Resolution      | Revenge   |     1916 |
| Revenge         | Revenge   |     1916 |
| Royal Oak       | Revenge   |     1916 |
| Royal Sovereign | Revenge   |     1916 |
| Tennessee       | Tennessee |     1920 |
+-----------------+-----------+----------+
13 rows in set (0.00 sec)
*/

/* Q2 - List the name, displacement, and number of guns of the ships engaged in the battle of Guadalcanal */

SELECT Ships.name, Classes.displacement, Classes.numGuns
FROM Ships
JOIN Classes ON Ships.class = Classes.class
JOIN Outcomes ON Ships.name = Outcomes.ship
WHERE Outcomes.battle = 'Guadalcanal';

/* Output:
+------------+--------------+---------+
| name       | displacement | numGuns |
+------------+--------------+---------+
| Kirishima  |        32000 |       8 |
| Washington |        37000 |       9 |
+------------+--------------+---------+
2 rows in set (0.00 sec)
*/

/* Q3 - List all the ships mentioned in the database */

SELECT Ships.name FROM Ships
UNION
SELECT ship from Outcomes;

/* Output:
+-----------------+
| name            |
+-----------------+
| California      |
| Haruna          |
| Hiei            |
| Iowa            |
| Kirishima       |
| Kongo           |
| Missouri        |
| Musashi         |
| New Jersey      |
| North Carolina  |
| Ramillies       |
| Renown          |
| Repulse         |
| Resolution      |
| Revenge         |
| Royal Oak       |
| Royal Sovereign |
| Tennessee       |
| Washington      |
| Wisconsin       |
| Yamato          |
| Arizona         |
| Bismarck        |
| Duke of York    |
| Fuso            |
| Hood            |
| King George V   |
| Prince of Wales |
| Rodney          |
| Scharnhorst     |
| South Dakota    |
| West Virginia   |
| Yamashiro       |
+-----------------+
33 rows in set (0.00 sec)
*/

/* Q4 - Find countries that have both battleships and battlecruisiers */
SELECT Classes.country
FROM Classes
WHERE Classes.type = 'bb' AND Classes.country IN
    (SELECT Classes.country FROM Classes
    WHERE Classes.type = 'bc');

/* Output:
+-------------+
| country     |
+-------------+
| Gt. Britain |
| Japan       |
+-------------+
2 rows in set (0.00 sec)
*/

/* Q5 - Find battles with at least three ships of the same country */
SELECT battle
FROM Outcomes
JOIN Ships ON Outcomes.ship = Ships.name
JOIN Classes ON Ships.class = Classes.class
GROUP BY battle, Classes.country
HAVING COUNT(*) >= 3;

/* Output:
Empty set (0.00 sec)
*/