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
